(function () {
    var prototype;
    var model;

    models.Site = function (data) {
        var self = this;

        self.id = null;
        self.uid = uid++;
        self.active = null;
        self.name = null;
        self.directory = null;
        self.sort = null;

        api.object.configure(self, data);
    };

    model = models.Site;
    prototype = model.prototype;
})();