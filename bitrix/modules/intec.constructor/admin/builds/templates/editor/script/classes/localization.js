(function () {
    var prototype;
    var constructor;

    classes.Localization = function (messages) {
        var self = this;
        var items = {};

        self.getMessage = function (key, macros) {
            var message = null;

            if (items.hasOwnProperty(key)) {
                message = items[key];

                api.each(macros, function (key, macro) {
                    message = message.split('#' + key + '#').join(macro);
                });
            }

            return message;
        };

        self.setMessages = function (messages) {
            api.each(messages, function (key, message) {
                if (api.isDeclared(message)) {
                    items[key] = api.toString(message);
                } else {
                    items[key] = undefined;
                }
            });
        };

        self.setMessages(messages);
    };

    constructor = classes.Localization;
    prototype = constructor.prototype;

    prototype.setMessage = function (key, message) {
        var self = this;

        self.setMessages({
            key: message
        });
    };
})();