(function () {
    var prototype;
    var model;

    models.Preset = function (data, group) {
        var self = this;

        self.uid = uid++;
        self.type = null;
        self.name = null;
        self.picture = null;
        self.sort = null;
        self.handler = null;
        self.configuration = null;

        Object.defineProperty(self, 'group', {
            'configurable': true,
            'enumerable': true,
            'get': function () {
                return group;
            },
            'set': function (value) {
                if (!(value instanceof models.preset.Group))
                    value = null;

                var index;

                if (group instanceof models.preset.Group) {
                    index = group.presets.indexOf(self);

                    if (index >= 0)
                        group.presets.splice(index, 1);
                }

                if (value instanceof models.preset.Group)
                    value.presets.push(self);

                group = value;
            }
        });

        api.object.configure(self, data, function (event) {
            if (event.path[0] === 'group')
                event.cancel();
        });

        self.group = group;
    };

    model = models.Preset;
    prototype = model.prototype;

    prototype.getConstructors = function () {
        return {
            'custom': null,
            'container': models.Container,
            'variator': models.elements.Variator,
            'component': models.elements.Component,
            'block': null,
            'widget': models.elements.Widget
        };
    };

    prototype.getConstructor = function () {
        var constructors = this.getConstructors();
        var constructor = constructors[this.getType()];

        if (api.isDeclared(constructor))
            return constructor;

        return null;
    };

    prototype.getTypes = function () {
        var types = [];

        api.each(this.getConstructors(), function (type, constructor) {
            types.push(type);
        });

        return types;
    };
    
    prototype.getType = function () {
        var types = this.getTypes();

        if (types.indexOf(this.type) === -1)
            return types[0];

        return this.type;
    };

    prototype.hasGroup = function () {
        return this.group !== null;
    };

    prototype.hasHandler = function () {
        return api.isFunction(this.handler);
    };

    prototype.handle = function (context, arguments) {
        if (this.hasHandler())
            this.handler.apply(context, arguments);
    };

    prototype.createElement = function (configuration) {
        var type = this.getType();
        var constructor;
        var model = null;
        var promiseResolve;
        var promiseReject;
        var promise = new Promise(function (resolve, reject) {
            promiseResolve = function () { resolve(model, configuration); };
            promiseReject = reject;
        });

        configuration = api.extend({}, this.configuration, configuration);

        if (type !== 'custom') {
            constructor = this.getConstructor();

            if (constructor) {
                model = new constructor(configuration);

                if (this.hasHandler()) {
                    this.handle(this, [
                        model,
                        configuration,
                        promiseResolve,
                        promiseReject
                    ]);
                } else {
                    promiseResolve();
                }
            } else if (this.hasHandler()) {
                this.handle(this, [
                    configuration,
                    function (result) { model = result; promiseResolve(); },
                    promiseReject
                ]);
            } else {
                promiseReject();
            }
        } else if (this.hasHandler()) {
            this.handle(this, [
                configuration,
                function (result) { model = result; promiseResolve(); },
                promiseReject
            ]);
        } else {
            promiseReject();
        }

        return promise;
    };
})();