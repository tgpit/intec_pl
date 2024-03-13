(function () {
    var prototype;
    var model;

    models.preset.Group = function (data) {
        var self = this;

        self.uid = uid++;
        self.code = null;
        self.name = null;
        self.sort = null;
        self.presets = [];

        api.object.configure(self, data);
    };

    model = models.preset.Group;
    prototype = model.prototype;
})();