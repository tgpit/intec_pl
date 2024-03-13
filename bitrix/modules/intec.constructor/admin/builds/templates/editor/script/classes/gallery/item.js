(function () {
    var prototype;
    var constructor;

    classes.gallery.Item = function (data) {
        var self = this;

        Object.defineProperty(self, 'name', {
            'configurable': false,
            'enumerable': true,
            'get': function () {
                return data.name;
            }
        });

        Object.defineProperty(self, 'path', {
            'configurable': false,
            'enumerable': true,
            'get': function () {
                return data.path;
            }
        });

        Object.defineProperty(self, 'value', {
            'configurable': false,
            'enumerable': true,
            'get': function () {
                return data.value;
            }
        })
    };

    constructor = classes.gallery.Item;
    prototype = constructor.prototype;
})();