(function () {
    var prototype;
    var constructor;

    classes.Links = function (templates) {
        var self = this;
        var items = {};

        self.get = function (key, macros) {
            var message = null;

            if (items.hasOwnProperty(key)) {
                message = items[key];

                api.each(macros, function (key, macro) {
                    message = message.split('#' + key + '#').join(macro);
                });
            }

            return message;
        };

        api.each(templates, function (key, value) {
            items[key] = value;
        });
    };

    constructor = classes.Links;
    prototype = constructor.prototype;
})();