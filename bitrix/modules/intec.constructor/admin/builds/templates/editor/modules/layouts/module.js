(function () {
    return {
        'computed': {
            'hasLayouts': function () {
                return this.layouts.length > 0;
            }
        },
        'data': {
            'layouts': []
        },
        'methods': {
            'refreshLayouts': function () {
                var self = this;

                return this.requestLayouts().then(function (response) {
                    self.layouts = response;

                    return response;
                });
            },
            'requestLayouts': function () {
                return this.request('layout.getList').then(function (response) {
                    return api.array.rebuild(response.data, function (index, data) {
                        return new models.Layout(data);
                    });
                });
            },
            'isCurrentLayout': function (layout) {
                if (this.template.layout === null || this.template.layout.code === null)
                    return false;

                return layout.code === this.template.layout.code;
            },
            'setLayout': function (code) {
                return this.request('layout.set', {
                    'code': code
                });
            }
        }
    }
})();
