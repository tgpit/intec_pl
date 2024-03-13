(function () {
    var model;
    var prototype;

    models.container.Condition = function (data, parent) {
        var self = this;

        self.uid = uid++;

        Object.defineProperty(self, 'parent', {
            'configurable': true,
            'enumerable': true,
            'get': function () {
                return parent;
            },
            'set': function (value) {
                if (!(value instanceof models.container.conditions.Group))
                    value = null;

                var index;

                if (parent instanceof models.container.conditions.Group) {
                    index = parent.conditions.indexOf(self);

                    if (index >= 0)
                        parent.conditions.splice(index, 1);
                }

                if (value instanceof models.container.conditions.Group)
                    value.conditions.push(self);

                parent = value;
            }
        });

        self.parent = parent;
    };

    model = models.container.Condition;
    prototype = model.prototype;

    /** Возвращает всех родителей */
    prototype.getParents = function () {
        var result = [];
        var parent = this.parent;

        while (parent !== null) {
            result.push(parent);
            parent = parent.parent;
        }

        return result;
    };

    /** Возвращает привязанность к родителю */
    prototype.hasParent = function (value) {
        var parent = this.parent;

        while (parent !== null) {
            if (parent === value)
                return true;

            parent = parent.parent;
        }

        return false;
    };

    /** Возвращает уровень */
    prototype.getLevel = function () {
        return this.getParents().length;
    };
})();