(function () {
    var model;
    var prototype;
    var extend = models.container.Condition;

    models.container.conditions.Condition = function (data, parent) {
        var self = this;

        if (!api.isObject(data))
            data = {};

        self.type = null;
        self.result = true;
        self.value = null;

        if (data.type === 'match') {
            self.match = self.getMatch();
        } else if (
            data.type === 'parameter.get' ||
            data.type === 'parameter.page' ||
            data.type === 'parameter.template'
        ) {
            self.key = null;

            if (data.type === 'parameter.page' || data.type === 'parameter.template')
                self.logic = self.getLogic();
        }

        extend.call(self, data, parent);

        api.object.configure(self, data, function (event) {
            if (event.path[0] === 'parent')
                event.cancel();
        });
    };

    model = models.container.conditions.Condition;
    model.prototype = Object.create(extend.prototype);
    prototype = model.prototype;
    prototype.constructor = model;

    /** Сохраняет условие */
    prototype.save = function () {
        var self = this;
        var result = {};

        result.type = self.getType();
        result.result = self.result;
        result.value = self.value;

        if (self.type === 'match') {
            result.match = self.getMatch();
        } else if (
            self.type === 'parameter.get' ||
            self.type === 'parameter.page' ||
            self.type === 'parameter.template'
        ) {
            result.key = self.key;

            if (self.type === 'parameter.page' || self.type === 'parameter.template')
                result.logic = self.getLogic();
        }

        return result;
    };

    prototype.getTypes = function () {
        return [
            'path',
            'match',
            'parameter.get',
            'parameter.page',
            'parameter.template',
            'expression',
            'site'
        ];
    };

    prototype.getType = function () {
        var types = this.getTypes();

        if (types.indexOf(this.type) === -1)
            return types[0];

        return this.type;
    };

    prototype.getMatches = function () {
        return [
            'url',
            'scheme',
            'host',
            'path',
            'query'
        ];
    };

    prototype.getMatch = function () {
        var matches = this.getMatches();

        if (matches.indexOf(this.match) === -1)
            return matches[0];

        return this.match;
    };

    prototype.getLogics = function () {
        return [
            '=',
            '!',
            '>',
            '>=',
            '<',
            '<='
        ];
    };

    prototype.getLogic = function () {
        var logics = this.getLogics();

        if (logics.indexOf(this.logic) === -1)
            return logics[0];

        return this.logic;
    };
})();