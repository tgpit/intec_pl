(function () {
    var prototype;
    var constructor;

    classes.Gallery = function (application) {
        var self = this;

        Object.defineProperty(self, 'application', {
            'configurable': false,
            'enumerable': true,
            'get': function () {
                return application;
            }
        });
    };

    constructor = classes.Gallery;
    prototype = constructor.prototype;

    prototype.requestItems = function () {
        var self = this;

        return self.application.request('gallery.getItems').then(function (response) {
            return api.array.rebuild(response.data, function (index, data) {
                return new classes.gallery.Item(data);
            });
        });
    };

    prototype.sendFile = function (file) {
        var self = this;

        return new Promise(function (resolve, reject) {
            var data = new FormData();

            if (!File || !(file instanceof File)) {
                reject();
                return;
            }

            data.append('file', file);

            self.application.request('gallery.uploadFile', data).then(function (response) {
                response = new classes.gallery.Item(response.data);

                resolve(response);
            }, reject);
        });
    };

    prototype.sendFiles = function (files) {
        var self = this;

        return new Promise(function (resolve, reject) {
            var promises = [];
            var index;

            if (!FileList || !(files instanceof FileList) || files.length === 0) {
                reject();
                return;
            }

            for (index = 0; index < files.length; index++)
                promises.push(self.sendFile(files[index]));

            Promise.all(promises).then(resolve, reject);
        });
    };

    prototype.sendFileByLink = function (link) {
        var self = this;

        return new Promise(function (resolve, reject) {
            if (!api.isString(link) || link.length === 0)
                reject();

            self.application.request('gallery.uploadFileByLink', {
                'link': link
            }).then(function (response) {
                response = new classes.gallery.Item(response.data);

                resolve(response);
            }, reject);
        });
    };

    prototype.sendFilesByLinks = function (links) {
        var self = this;

        return new Promise(function (resolve, reject) {
            var promises = [];

            api.each(links, function (index, link) {
                promises.push(self.sendFileByLink(link));
            });

            Promise.all(promises).then(resolve, reject);
        });
    };

    prototype.deleteItem = function (item) {
        var self = this;

        return new Promise(function (resolve, reject) {
            if (!(item instanceof classes.gallery.Item))
                reject();

            self.application.request('gallery.deleteFile', {
                'name': item.name
            }).then(function (response) {
                resolve();
            }, reject);
        });
    }
})();