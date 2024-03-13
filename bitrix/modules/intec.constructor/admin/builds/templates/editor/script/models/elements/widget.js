(function () {
    var prototype;
    var model;
    var extend = models.Element;

    models.elements.Widget = function (data, parent) {
        var self = this;

        self.code = null;
        self.template = null;
        self.properties = null;

        self.component = null;
        extend.call(self, data, parent);

        api.object.configure(self, data, function (event) {
            if (['parent', 'component'].indexOf(event.path[0]) >= 0)
                event.cancel();
        });
    };

    model = models.elements.Widget;
    model.prototype = Object.create(extend.prototype);
    prototype = model.prototype;
    prototype.constructor = model;

    prototype.getModel = function () {
        return null;
    };

    prototype.save = function (identified) {
        var self = this;
        var prototype = Object.getPrototypeOf(Object.getPrototypeOf(self));
        var result = prototype.save.call(self, identified);

        result.code = self.code;
        result.template = self.template;
        result.properties = api.extend({}, self.properties);

        return result;
    }
})();