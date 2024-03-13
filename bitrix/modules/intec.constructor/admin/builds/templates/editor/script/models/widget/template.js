(function () {
    var prototype;
    var model;

    models.widget.Template = function (data, widget) {
        var self = this;

        self.code = null;
        self.script = null;
        self.view = null;
        self.settings = null;
        self.messages = null;

        Object.defineProperty(self, 'widget', {
            'configurable': true,
            'enumerable': true,
            'get': function () {
                return widget;
            },
            'set': function (value) {
                if (!(value instanceof models.Widget))
                    value = null;

                var index;

                if (widget instanceof models.Widget) {
                    index = widget.templates.indexOf(self);

                    if (index >= 0)
                        widget.templates.splice(index, 1);
                }

                if (value instanceof models.Widget)
                    value.templates.push(self);

                widget = value;
            }
        });

        api.object.configure(self, data, function (event) {
            if (event.path[0] === 'widget')
                event.cancel();
        });

        self.widget = widget;
    };

    model = models.widget.Template;
    prototype = model.prototype;
})();