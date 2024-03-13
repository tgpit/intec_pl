(function () {
    var prototype;
    var model;
    var extend = models.Element;

    models.elements.Component = function (data, parent) {
        var self = this;

        self.code = null;
        self.template = null;
        self.properties = null;
        self.data = {
            'name': null,
            'sort': null
        };

        self.content = null;
        extend.call(self, data, parent);

        api.object.configure(self, data, function (event) {
            if (['parent', 'data', 'content'].indexOf(event.path[0]) >= 0) {
                event.cancel();
            } else if (event.path[0] === 'properties') {
                if (!api.isObject(event.value))
                    event.value = {};
            }
        });
    };

    model = models.elements.Component;
    model.prototype = Object.create(extend.prototype);
    prototype = model.prototype;
    prototype.constructor = model;

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