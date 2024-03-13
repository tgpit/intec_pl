(function () {
    return {
        'data': {
            'containers': []
        },
        'methods': {
            'getContainer': function (zone) {
                return api.array.find(zone, this.containers, function (index, item, zone) {
                    return item.zone === zone;
                });
            },
            'refreshContainers': function () {
                var self = this;

                return this.requestContainers().then(function (response) {
                    self.containers = response;

                    return response;
                });
            },
            'requestContainers': function () {
                return this.request('container.getList').then(function (response) {
                    return api.array.rebuild(response.data, function (index, data) {
                        return new models.Container(data);
                    });
                });
            }
        }
    }
})();
