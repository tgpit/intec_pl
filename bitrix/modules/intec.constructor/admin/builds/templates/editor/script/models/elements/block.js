(function () {
    var prototype;
    var model;
    var extend = models.Element;

    models.elements.Block = function (data, parent) {
        var self = this;

        self.id = null;
        self.name = null;

        self.content = null;
        extend.call(self, data, parent);

        api.object.configure(self, data, function (event) {
            if (['parent', 'content'].indexOf(event.path[0]) >= 0)
                event.cancel();
        });
    };

    model = models.elements.Block;
    model.prototype = Object.create(extend.prototype);
    prototype = model.prototype;
    prototype.constructor = model;

    prototype.save = function (identified) {
        var self = this;
        var prototype = Object.getPrototypeOf(Object.getPrototypeOf(self));
        var result = prototype.save.call(self, true);

        result.name = self.name;

        return result;
    }
})();