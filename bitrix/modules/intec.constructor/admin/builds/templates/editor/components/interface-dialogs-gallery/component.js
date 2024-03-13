(function () {
    return {
        'name': 'v-interface-dialogs-gallery',
        'updated': function () {
            if (this.$refs.scrollbar)
                this.$refs.scrollbar.scrollTo({
                    'y': this.scrollbarPosition
                }, 0);
        },
        'computed': {
            'filteredItems': function () {
                var self = this;
                var handler;

                handler = function (items) {
                    var result = [];

                    if (!self.display)
                        return result;

                    if (self.isFiltered) {
                        api.each(items, function (index, item) {
                            if (item.name !== null && item.name.indexOf(self.filter) >= 0)
                                result.push(item);
                        });

                        return result;
                    } else {
                        api.each(items, function (index, item) {
                            result.push(item);
                        });
                    }

                    return result;
                };

                return handler(this.items);
            },
            'isFiltered': function () {
                return this.filter !== null && this.filter.length > 0;
            }
        },
        'data': function () {
            return {
                'callback': null,
                'display': false,
                'filter': null,
                'items': [],
                'isUploading': false,
                'isRefreshing': false,
                'uploadLinks': [],
                'selectedFiles': [],
                'uploadedFiles': [],
                'limitFiles': false,
                'scrollbarSettings': {
                    'vuescroll': {
                        'mode': 'native',
                        'sizeStrategy': 'percent',
                        'detectResize': true
                    },
                    'scrollPanel': {
                        'initialScrollY': false,
                        'initialScrollX': false,
                        'scrollingX': true,
                        'scrollingY': true,
                        'speed': 300
                    },
                    'bar': {
                        'onlyShowBarOnScroll': false,
                        'background': '#cfd3de'
                    }
                },
                'scrollbarPosition': 0,
                'tab': null
            }
        },
        'methods': {
            'close': function () {
                this.display = false;
                this.callback = null;
                this.filter = null;
                this.uploadLinks = [];
                this.selectedFiles = [];
                this.uploadedFiles = [];
                this.limitFiles = false;
            },
            'handleScroll': function (vertical, horizontal, event) {
                this.scrollbarPosition = vertical.scrollTop;
            },
            'open': function (callback) {
                this.callback = callback;
                this.tab = null;
                this.display = true;

                this.resetUploadLinks();
                this.refresh();
            },
            'refresh': function () {
                var self = this;
                var gallery = self.$root.$gallery;

                self.isRefreshing = true;

                if (self.items.length > 0)
                    self.items.splice(0, self.items.length);

                gallery.requestItems().then(function (items) {
                    api.each(items, function (index, item) {
                        item.isDeleting = false;
                        self.items.push(item);
                    });

                    self.isRefreshing = false;
                }, function (reason) {
                    self.isRefreshing = false;
                });
            },
            'cutToLimitFiles': function (fileList) {
                if (!fileList.length)
                    return;

                var remaining = 30 - this.selectedFiles.length;
                var files = [];

                if (remaining > 0) {
                    this.limitFiles = fileList.length >= remaining;

                    api.each(fileList, function (index, file) {
                        files.push(file);
                    });

                    if (files.length > remaining) {
                        files.splice(remaining, files.length);
                    }
                }

                return files;
            },
            'dragEnter': function (event) {
                event.stopPropagation();
                event.preventDefault();
            },
            'dragOver': function (event) {
                event.stopPropagation();
                event.preventDefault();
            },
            'drop': function (event) {
                event.stopPropagation();
                event.preventDefault();

                if (this.isUploading || this.limitFiles)
                    return;

                var files;

                files = this.cutToLimitFiles(event.dataTransfer.files);

                if (files.length)
                    this.selectFiles(files);
            },
            'inputChange': function (event) {
                if (this.isUploading || this.limitFiles)
                    return;

                if (!api.isEmpty(event) && !api.isEmpty(event.srcElement) && !api.isEmpty(event.srcElement.files)) {
                    var files = this.cutToLimitFiles(event.srcElement.files);

                    this.selectFiles(files);

                    event.target.value = '';
                    event.target.files = null;
                }
            },
            'selectFiles': function (files) {
                if (!files.length)
                    return;

                var self = this;

                api.each(files, function (index, file) {
                    self.readSelectedFile(file).then(function (result) {
                        file.image = result;
                        self.selectedFiles.push(file);
                    });
                });
            },
            'readSelectedFile': function (file) {
                return new Promise(function (resolve, reject) {
                    var reader = new FileReader();

                    reader.onload = function () {
                        resolve(reader.result);
                    };

                    reader.onerror = function () {
                        reject();
                    };

                    reader.readAsDataURL(file);
                });
            },
            'removeSelectedFile': function (index) {
                this.limitFiles = false;
                this.selectedFiles.splice(index, 1);
            },
            'removeSelectedFiles': function () {
                this.limitFiles = false;
                this.selectedFiles = [];
            },
            'uploadFiles': function () {
                var self = this;

                if (!self.selectedFiles.length)
                    return;

                var gallery = self.$root.$gallery;
                var promises = [];

                self.isUploading = true;

                api.each(self.selectedFiles, function (index, file) {
                    promises.push(gallery.sendFile(file));
                });

                Promise.all(promises).then(function (responses) {
                    api.each(responses, function (index, response) {
                        self.uploadedFiles.push(response.name);
                    });

                    self.isUploading = false;
                    self.removeSelectedFiles();
                    self.refresh();
                    self.selectTab(null);
                }, function (reason) {
                    self.isUploading = false;
                });
            },
            'isUploadedFile': function (name) {
                return this.uploadedFiles.indexOf(name) !== -1;
            },
            'uploadFilesByLinks': function () {
                var self = this;
                var gallery = self.$root.$gallery;
                var links = [];

                api.each(self.uploadLinks, function (index, link) {
                    if (!api.isEmpty(link.value))
                        links.push(link.value);
                });

                if (self.isUploading || links.length === 0)
                    return;

                self.isUploading = true;

                gallery.sendFilesByLinks(links).then(function (response) {
                    api.each(response, function (index, file) {
                        self.uploadedFiles.push(file.name);
                    });

                    self.isUploading = false;
                    self.resetUploadLinks();
                    self.refresh();
                    self.selectTab(null);
                }, function (reason) {
                    self.isUploading = false;
                });
            },
            'resetUploadLinks': function () {
                this.uploadLinks = [{
                    'value': null
                }];
            },
            'addUploadLink': function () {
                this.uploadLinks.push({
                    'value': null
                });
            },
            'deleteItem': function (item) {
                var self = this;
                var gallery = self.$root.$gallery;

                if (!(item instanceof classes.gallery.Item))
                    return;

                item.isDeleting = true;

                gallery.deleteItem(item).then(function () {
                    var index = self.items.indexOf(item);
                    var indexUploaded = self.uploadedFiles.indexOf(item.name);

                    if (index >= 0)
                        self.items.splice(index, 1);

                    if (indexUploaded >= 0)
                        self.uploadedFiles.splice(indexUploaded, 1);

                    item.isDeleting = false;
                }, function () {
                    item.isDeleting = false;
                });
            },
            'selectItem': function (item) {
                if (!(item instanceof classes.gallery.Item))
                    return;

                if (api.isFunction(this.callback))
                    this.callback(item);

                this.close();
            },
            'selectTab': function (name) {
                if (!api.isString(name) || name.length === 0)
                    name = null;

                this.tab = name;
            }
        }
    }
})();
