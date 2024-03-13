(function () {
    return {
        'computed': {
            'site': function () {
                return this.getSite(this.siteId);
            }
        },
        'data': {
            'siteId': null,
            'sites': []
        },
        'methods': {
            'getSite': function (id) {
                return api.array.find(id, this.sites, function (index, item, id) {
                    return item.id === id;
                });
            },
            'refreshSites': function () {
                var self = this;

                return this.requestSites().then(function (response) {
                    self.sites = response;

                    if (self.site === null && response.length > 0)
                        self.siteId = response[0].id;

                    return response;
                });
            },
            'requestSites': function () {
                return this.request('site.getList').then(function (response) {
                    return api.array.rebuild(response.data, function (index, data) {
                        return new models.Site(data);
                    });
                });
            }
        }
    }
})();
