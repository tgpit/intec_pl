(function () {
    var prototype;
    var model;

    models.layout.Zone = function (data) {
        var self = this;

        self.uid = uid++;
        self.code = null;
        self.name = null;

        api.object.configure(self, data);
    };

    model = models.layout.Zone;
    prototype = model.prototype;
})();