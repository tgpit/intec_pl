(function () {
    var prototype;
    var model;
    var extend = models.Element;

    models.elements.Variant = function (data, parent) {
        var self = this;

        Object.defineProperty(self, 'parent', {
            'configurable': true,
            'enumerable': true,
            'get': function () {
                return parent;
            },
            'set': function (value) {
                if (!(value instanceof models.elements.Variator))
                    value = null;

                var index;

                if (parent instanceof models.elements.Variator) {
                    index = parent.variants.indexOf(self);

                    if (index >= 0)
                        parent.variants.splice(index, 1);
                }

                if (value)
                    value.variants.push(self);

                parent = value;
            }
        });

        self.code = null;
        self.name = null;
        self.order = null;
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

    model = models.elements.Variant;
    model.prototype = Object.create(extend.prototype);
    prototype = model.prototype;
    prototype.constructor = model;

    prototype.save = function (identified) {
        var self = this;
        var prototype = Object.getPrototypeOf(Object.getPrototypeOf(self));
        var result = prototype.save.call(self, identified);

        result.code = null;

        if (identified)
            result.code = self.code;

        result.name = self.name;
        result.order = self.order;
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