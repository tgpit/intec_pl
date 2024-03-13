(function () {
    var model;
    var prototype;
    var extend = models.container.Condition;

    models.container.conditions.Group = function (data, parent) {
        var self = this;

        if (!api.isObject(data))
            data = {};

        self.operator = 'and';
        self.result = true;
        self.conditions = [];
        extend.call(self, data, parent);

        api.object.configure(self, data, function (event) {
            if (event.path[0] === 'parent' || event.path[0] === 'conditions')
                event.cancel();
        });

        api.each(data.conditions, function (index, data) {
            if (data.type === 'group') {
                new model(data, self);
            } else {
                new models.container.conditions.Condition(data, self);
            }
        });
    };

    model = models.container.conditions.Group;
    model.prototype = Object.create(extend.prototype);
    prototype = model.prototype;
    prototype.constructor = model;

    /** Сохраняет группу условий */
    prototype.save = function () {
        var self = this;
        var result = {};

        result.type = 'group';
        result.operator = self.getOperator();
        result.result = self.result;
        result.conditions = [];

        api.each(self.conditions, function (index, condition) {
            result.conditions.push(condition.save());
        });

        return result;
    };

    /** Группа условий является корневой */
    prototype.isRoot = function () {
        return this.parent === null;
    };

    /** Идет по всем контейнерам рекурсивно */
    (function () {
        var handler;

        handler = function (parent, callback) {
            var result;

            api.each(parent.conditions, function (index, condition) {
                result = callback(index, condition, parent);

                if (result === false)
                    return false;

                if (condition instanceof model)
                    result = handler(condition, callback);

                return result;
            });

            return result;
        };

        prototype.eachCondition = function (callback) {
            if (api.isFunction(callback))
                handler(this, callback)
        };
    })();

    /** Возвращает порядковый номер группы в родителе */
    prototype.getOrder = function () {
        var self = this;
        var order = 0;

        if (self.parent !== null) {
            api.each(self.parent.conditions, function (index, condition) {
                if (condition instanceof model) {
                    if (condition === self)
                        return false;

                    order++;
                }
            });

            return order;
        }

        return null;
    };

    /** Возвращает список операторов */
    prototype.getOperators = function () {
        return [
            'and',
            'or'
        ];
    };

    /** Проверяет валидность оператора */
    prototype.getOperator = function () {
        var operators = this.getOperators();

        if (operators.indexOf(this.operator))
            return operators[0];

        return this.operator;
    }
})();