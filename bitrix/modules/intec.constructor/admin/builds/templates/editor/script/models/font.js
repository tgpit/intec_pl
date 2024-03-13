(function () {
    var prototype;
    var model;

    models.Font = function (data) {
        var self = this;

        self.uid = uid++;
        self.code = null;
        self.name = null;
        self.sort = null;
        self.family = null;
        self.style = null;

        api.object.configure(self, data);
    };

    model = models.Font;
    prototype = model.prototype;
})();