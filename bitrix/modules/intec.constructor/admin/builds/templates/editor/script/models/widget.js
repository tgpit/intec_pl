(function () {
    var prototype;
    var model;
    var build;
    var compile;

    build = function (script, arguments) {
        if (!api.isArray(arguments))
            arguments = [];

        if (api.isString(script) && script.length > 0)
            script = 'return ' + script;

        return eval('(function (' + arguments.join(', ') + ') {' + script + '})');
    };

    compile = function (widget, type) {
        var self = this;
        var mixins = [];
        var builder;
        var object;
        var template;
        var properties;

        if (!(widget instanceof models.elements.Widget))
            return null;

        template = self.getTemplate(widget.template);

        if (!template)
            return null;

        mixins.push({
            'created': function () {
                this.loadProperties();
                this.emitInitializeProperties();

                this.$nextTick(function () {
                    this.isInitialized = true;
                });
            }
        });

        properties = function () {
            return api.extend({}, properties.component(), properties.template());
        };

        builder = build(this.script, ['meta', 'template']);
        object = builder.call(self, {
            'type': type,
            'messages': api.extend({}, self.messages)
        }, template);

        if (api.isObject(object))
            mixins.push(object);

        object = builder.call(self, {
            'type': 'properties',
            'messages': api.extend({}, self.messages)
        }, template);

        if (api.isFunction(object)) {
            properties.component = object;
        } else {
            properties.component = function () {};
        }

        builder = build(template.script, ['meta']);
        object = builder.call(template, {
            'type': type,
            'messages': api.extend({}, template.messages)
        });

        if (api.isObject(object))
            mixins.push(object);

        object = builder.call(template, {
            'type': 'properties',
            'messages': api.extend({}, template.messages)
        });

        if (api.isFunction(object)) {
            properties.template = object;
        } else {
            properties.template = function () {};
        }

        mixins.push({
            'data': function () {
                return {
                    'data': null,
                    'isDataRefreshing': false,
                    'isInitialized': false,
                    'isPropertiesUpdating': false,
                    'properties': properties()
                };
            },
            'props': {
                'model': {
                    'type': models.elements.Widget,
                    'required': true
                }
            },
            'methods': {
                'emitInitializeProperties': function () {
                    this.$emit('initialize-properties', api.extend({}, this.properties));
                },
                'emitSaveProperties': function () {
                    this.$emit('save-properties', api.extend({}, this.properties));
                },
                'loadProperties': function () {
                    api.object.configure(
                        this.properties,
                        this.model.properties
                    );

                    this.properties = api.extend({}, this.properties);
                },
                'request': function (parameters, templated) {
                    return this.$root.request('widget.request', {
                        'code': self.getCode(),
                        'template': templated ? template.code : null,
                        'parameters': parameters,
                        'properties': this.properties
                    }, {
                        'environment': true
                    }).then(function (response) {
                        return response.data;
                    });
                },
                'requestData': function () {
                    return this.$root.request('widget.getData', {
                        'code': self.getCode(),
                        'template':template.code,
                        'properties': this.properties
                    }, {
                        'environment': true
                    }).then(function (response) {
                        return response.data;
                    });
                },
                'refreshData': function () {
                    var self = this;

                    self.isDataRefreshing = true;

                    return this.requestData().then(function (data) {
                        self.data = data;
                        self.isDataRefreshing = false;

                        return data;
                    }, function (reason) {
                        self.isDataRefreshing = false;

                        return reason;
                    });
                }
            },
            'watch': {
                'model.properties': {
                    'deep': true,
                    'handler': function () {
                        this.isPropertiesUpdating = true;
                        this.loadProperties();

                        this.$nextTick(function () {
                            this.isPropertiesUpdating = false;
                        });
                    }
                },
                'properties': {
                    'deep': true,
                    'handler': function () {
                        if (this.isInitialized && !this.isPropertiesUpdating)
                            this.emitSaveProperties();
                    }
                }
            }
        });

        mixins.push(Vue.compile(template[type]));

        return Vue.extend({
            'mixins': mixins
        });
    };

    models.Widget = function (data) {
        var self = this;

        self.namespace = null;
        self.id = null;
        self.uid = uid++;
        self.icon = null;
        self.name = null;
        self.script = null;
        self.messages = null;
        self.templates = [];

        api.object.configure(self, data, function (event) {
            if (event.path[0] === 'templates')
                event.cancel();
        });

        api.each(data.templates, function (index, data) {
            new models.widget.Template(data, self);
        });
    };

    model = models.Widget;
    prototype = model.prototype;

    prototype.getCode = function () {
        if (!this.namespace || !this.id)
            return null;

        return this.namespace + ':' + this.id;
    };

    prototype.getTemplate = function (code) {
        return api.array.find(code, this.templates, function (index, template) {
            return template.code === code;
        });
    };

    prototype.compileViewComponent = function (widget) {
        return compile.call(this, widget, 'view');
    };

    prototype.compileSettingsComponent = function (widget) {
        return compile.call(this, widget, 'settings');
    };
})();