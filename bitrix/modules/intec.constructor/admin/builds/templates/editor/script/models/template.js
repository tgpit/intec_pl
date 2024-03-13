(function () {
    var prototype;
    var model;

    models.Template = function (data) {
        var self = this;

        self.id = null;
        self.uid = uid++;
        self.name = null;
        self.settings = {
            'containersHiddenShow': false,
            'containersStructureShow': true,
            'developmentMode': false,
            'siteId': null
        };

        self.layout = null;

        api.object.configure(self, data, function (event) {
            var path = event.pathString;

            if (path === 'layout') {
                if (api.isDeclared(event.value))
                    event.value = new models.Layout(event.value);
            }
        });
    };

    model = models.Template;
    prototype = model.prototype;
})();