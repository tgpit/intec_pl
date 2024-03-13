(function () {
    return {
        'updated': function () {
            if (this.$refs.scrollbar)
                this.$refs.scrollbar.scrollTo({
                    'y': this.scrollbarPosition
                }, 0);
        },
        'computed': {
            'types': function () {
                var self = this;
                var list = ['group'].concat(models.container.conditions.Condition.prototype.getTypes());
                var types = [];

                api.each(list, function (index, item) {
                    types.push({
                        'text': self.getTypeName(item),
                        'value': item
                    });
                });

                return types;
            },
            'operators': function () {
                var self = this;
                var list = models.container.conditions.Group.prototype.getOperators();
                var operators = [];

                api.each(list, function (index, item) {
                    operators.push({
                        'text': self.getOperatorName(item),
                        'value': item
                    });
                });

                return operators;
            },
            'groups': function () {
                var self = this;
                var result = [];

                result.push(self.condition);

                self.condition.eachCondition(function (index, condition, parent) {
                    if (condition instanceof models.container.conditions.Group)
                        result.push(condition);
                });

                return result;
            }
        },
        'data': function () {
            return {
                'display': false,
                'condition': null,
                'selectedGroup': null,
                'selectedType': null,
                'scrollbarSettings': {
                    'vuescroll': {
                        'mode': 'native',
                        'sizeStrategy': 'percent',
                        'detectResize': true
                    },
                    'scrollPanel': {
                        'initialScrollY': false,
                        'initialScrollX': false,
                        'scrollingX': true,
                        'scrollingY': true,
                        'speed': 300
                    },
                    'bar': {
                        'onlyShowBarOnScroll': false,
                        'background': '#cfd3de'
                    }
                },
                'scrollbarPosition': 0
            };
        },
        'props': {},
        'methods': {
            'handleScroll': function (vertical, horizontal, event) {
                this.scrollbarPosition = vertical.scrollTop;
            },
            'open': function (condition) {
                if (!(condition instanceof models.container.Condition))
                    return;

                this.condition = condition;
                this.selectedType = 'group';
                this.selectedGroup = this.condition;
                this.display = true;
            },
            'close': function () {
                this.selectedType = null;
                this.selectedGroup = null;
                this.display = false;
            },
            'getGroupName': function (group) {
                var self = this;
                var text;

                if (group.parent === null) {
                    text = self.$root.$localization.getMessage('dialogs.container.conditions.property.group.text.root');
                } else {
                    text = '';

                    api.each(group.getParents(), function (index, parent) {
                        var order = parent.getOrder();

                        if (order !== null)
                            text = (order + 1) + '-' + text;
                    });

                    text = self.$root.$localization.getMessage('dialogs.container.conditions.property.group.text.inner') + ' ' + text + (group.getOrder() + 1);
                }

                return text;
            },
            'getTypeName': function (type) {
                return this.$root.$localization.getMessage('dialogs.container.conditions.groups.' + type);
            },
            'getOperatorName': function (operator) {
                return this.$root.$localization.getMessage('dialogs.container.conditions.result.' + operator);
            },
            'add': function () {
                if (this.selectedType === 'group')
                    new models.container.conditions.Group({}, this.selectedGroup);
                else
                    new models.container.conditions.Condition({
                        'type': this.selectedType
                    }, this.selectedGroup);
            }
        }
    }
})();
