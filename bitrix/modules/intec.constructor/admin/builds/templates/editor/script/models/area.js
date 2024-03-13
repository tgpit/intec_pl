(function () {
    var prototype;
    var model;

    models.Area = function (data) {
        var self = this;

        self.uid = uid++;
        self.id = null;
        self.code = null;
        self.name = null;
        self.sort = null;

        api.object.configure(self, data);
    };

    model = models.Area;
    prototype = model.prototype;
})();