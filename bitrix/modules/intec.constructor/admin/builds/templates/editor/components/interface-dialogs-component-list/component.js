(function () {
    return {
        'computed': {
            'filteredItems': function () {
                var self = this;
                var handler;
                var filter;

                if (self.isFiltered)
                    filter = self.filter.toLowerCase();

                handler = function (items) {
                    var result = [];

                    api.each(items, function (index, item) {
                        var copy = {};

                        api.each(item, function (key, value) {
                            if (key === 'children') {
                                copy[key] = [];
                            } else {
                                copy[key] = value;
                            }
                        });

                        if (item.type === 'section') {
                            copy.children = handler(item.children);

                            if (copy.children.length > 0)
                                result.push(copy);
                        } else if (
                            !self.isFiltered ||
                            (copy.code && copy.code.toLowerCase().indexOf(filter) >= 0) ||
                            (copy.name && copy.name.toLowerCase().indexOf(filter) >= 0)
                        ) result.push(copy);
                    });

                    return result;
                };

                return handler(self.items);
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
                'isRefreshing': false,
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
                }
            };
        },
        'methods': {
            'close': function () {
                this.display = false;
                this.callback = null;
            },
            'open': function (callback) {
                this.callback = callback;
                this.display = true;
                this.filter = null;
                this.refresh();
            },
            'requestData': function () {
                return this.$root.request('component.getList').then(function (response) {
                    return response.data;
                });
            },
            'refresh': function () {
                var self = this;

                self.isRefreshing = true;

                if (self.items.length > 0)
                    self.items.splice(0, self.items.length);

                return self.requestData().then(function (data) {
                    api.each(data, function (index, item) {
                        self.items.push(item);
                    });

                    self.isRefreshing = false;
                }, function (reason) {
                    self.isRefreshing = false;
                });
            },
            'selectItem': function (item) {
                if (!api.isObject(item) || item.type !== 'component')
                    return;

                if (api.isFunction(this.callback))
                    this.callback(item);

                this.close();
            }
        }
    }
})();
