(function () {
    var prototype;
    var model;

    models.Element = function (data, parent) {
        var self = this;

        self.id = null;
        self.uid = uid++;

        if (!self.hasOwnProperty('parent')) {
            Object.defineProperty(self, 'parent', {
                'configurable': true,
                'enumerable': true,
                'get': function () {
                    return parent;
                },
                'set': function (value) {
                    if (!(value instanceof models.Container))
                        value = null;

                    if (parent)
                        parent.element = null;

                    if (value) {
                        if (value.hasElement())
                            value.element.parent = null;

                        value.element = self;
                    }

                    parent = value;
                }
            });
        }

        self.parent = parent;
    };

    model = models.Element;
    prototype = model.prototype;

    prototype.save = function (identified) {
        var self = this;
        var result = {};

        result.id = null;

        if (identified)
            result.id = self.id;

        return result;
    };

    prototype.canStoreContainer = function () {
        return false;
    };
})();