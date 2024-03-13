(function () {
    return {
        'data': function () {
            return {
                'area': null,
                'callback': null,
                'display': false,
                'isSelecting': false
            };
        },
        'props': {
            'areas': {
                'required': true
            }
        },
        'methods': {
            'close': function () {
                this.display = false;
                this.callback = null;
            },
            'open': function (callback) {
                this.callback = callback;

                if (this.areas.length > 0)
                    this.area = this.areas[0];

                this.display = true;
            },
            'requestStructure': function (code) {
                return this.$root.request('area.getStructure', {
                    'code': code
                }).then(function (response) {
                    return new models.elements.Area(response.data);
                });
            },
            'selectArea': function () {
                var self = this;
                var area = self.area;

                if (!(area instanceof models.Area))
                    return;

                self.isSelecting = true;
                self.requestStructure(area.code).then(function (area) {
                    if (api.isFunction(self.callback))
                        self.callback(area);

                    self.isSelecting = false;
                    self.close();
                }, function () {
                    self.isSelecting = false;
                });
            }
        }
    }
})();
