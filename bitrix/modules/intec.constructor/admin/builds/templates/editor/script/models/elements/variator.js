(function () {
    var prototype;
    var model;
    var extend = models.Element;

    models.elements.Variator = function (data, parent) {
        var self = this;

        self.variant = null;
        self.variants = [];
        extend.call(self, data, parent);

        api.object.configure(self, data, function (event) {
            if (event.path[0] === 'parent') {
                event.cancel();
            } else if (event.path[0] === 'variants') {
                event.cancel();

                api.each(event.value, function (index, data) {
                    new models.elements.Variant(data, self);
                });
            }
        });
    };

    model = models.elements.Variator;
    model.prototype = Object.create(extend.prototype);
    prototype = model.prototype;
    prototype.constructor = model;

    prototype.save = function (identified) {
        var self = this;
        var prototype = Object.getPrototypeOf(Object.getPrototypeOf(self));
        var result = prototype.save.call(self, identified);

        result.variant = self.variant;
        result.variants = [];

        api.each(self.variants, function (index, variant) {
            result.variants.push(variant.save(identified));
        });

        return result;
    };

    prototype.updateVariantsOrder = function () {
        api.each(this.getSortedVariants(), function (index, variant) {
            variant.order = index;
        });
    };

    prototype.getSortedVariants = function () {
        var result = [];

        api.each(this.variants, function (index, container) {
            result.push(container);
        });

        result.sort(function (left, right) {
            if (!api.isDeclared(left.order) && !api.isDeclared(right.order)) {
                return 0;
            } else if (!api.isDeclared(left.order)) {
                return 1;
            } else if (!api.isDeclared(right.order)) {
                return -1;
            }

            return left.order - right.order;
        });

        return result;
    };

    prototype.createVariant = function () {
        var self = this;
        var variant = new models.elements.Variant();

        variant.parent = self;

        return variant;
    };

    prototype.getVariant = function () {
        var self = this;

        if (self.variant < 0 || self.variants.length <= self.variant)
            return null;

        return self.variants[self.variant];
    };

    prototype.setVariant = function (variant) {
        var self = this;
        var index = self.variants.indexOf(variant);

        if (index >= 0) {
            self.variant = index;
        } else {
            self.variant = -1;
        }
    };

    prototype.hasVariant = function () {
        return this.getVariant() instanceof models.elements.Variant;
    }
})();