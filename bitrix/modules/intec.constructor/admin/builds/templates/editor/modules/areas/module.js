(function () {
    return {
        'computed': {
            'availableAreas': function () {
                var self = this;
                var result = [];

                api.each(self.areas, function (index, area) {
                    if (self.isAreaAvailable(area))
                        result.push(area);
                });

                return result;
            }
        },
        'data': {
            'areas': []
        },
        'methods': {
            'isAreaAvailable': function (area) {
                var self = this;
                var found = false;

                if (!(area instanceof models.Area) && !(area instanceof models.elements.Area))
                    return false;

                api.each(self.containers, function (index, container) {
                    container.eachContainer(function (index, container, parent) {
                        if (container.hasArea()) {
                            found = container.element.id === area.id;

                            if (found)
                                return false;
                        }
                    });

                    if (found)
                        return false;
                });

                return !found;
            },
            'refreshAreas': function () {
                var self = this;

                return this.requestAreas().then(function (response) {
                    self.areas = response;
                });
            },
            'requestAreas': function () {
                return this.request('area.getList').then(function (response) {
                    return api.array.rebuild(response.data, function (index, data) {
                        return new models.Area(data);
                    });
                });
            }
        }
    }
})();
