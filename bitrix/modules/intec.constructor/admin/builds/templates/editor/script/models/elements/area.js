(function () {
    var prototype;
    var model;
    var extend = models.Element;

    models.elements.Area = function (data, parent) {
        var self = this;

        self.name = null;
        self.container = null;
        extend.call(self, data, parent);

        api.object.configure(self, data, function (event) {
            if (event.path[0] === 'parent') {
                event.cancel();
            } else if (event.path[0] === 'container') {
                event.cancel();
                new models.Container(event.value, self);
            }
        });
    };

    model = models.elements.Area;
    model.prototype = Object.create(extend.prototype);
    prototype = model.prototype;
    prototype.constructor = model;

    prototype.save = function (identified) {
        var self = this;
        var prototype = Object.getPrototypeOf(Object.getPrototypeOf(self));

        identified = true;

        var result = prototype.save.call(self, identified);

        result.name = self.name;
        result.container = null;

        if (self.hasContainer())
            result.container = self.container.save(identified);

        return result;
    };

    prototype.hasContainer = function () {
        return this.container instanceof models.Container;
    };

    prototype.canStoreContainer = function () {
        return true;
    }
})();