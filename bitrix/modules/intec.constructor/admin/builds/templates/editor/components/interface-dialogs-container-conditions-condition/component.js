(function () {
    return {
        'computed': {
            'root': function () {
                var parent = this.$parent;

                while (parent !== null) {
                    if (parent instanceof Vue && parent.$options.name === 'v-interface-dialogs-container-conditions')
                        return parent;

                    parent = parent.$parent;
                }
            },
            'operators': function () {
                var self = this;
                var operators = [];
                var list = models.container.conditions.Group.prototype.getOperators();

                api.each(list, function (index, item) {
                    operators.push({
                        'text': self.$root.$localization.getMessage('dialogs.container.conditions.result.' + item),
                        'value': item
                    })
                });

                return operators;
            },
            'sites': function () {
                var sites = [];

                api.each(this.$root.sites, function (index, site) {
                    sites.push({
                        'value': site.id,
                        'text': '(' + site.id + ') ' + site.name
                    });
                });

                return sites;
            },
            'matches': function () {
                var self = this;
                var list = models.container.conditions.Condition.prototype.getMatches();
                var matches = [];

                api.each(list, function (index, item) {
                    matches.push({
                        'text': self.$root.$localization.getMessage('dialogs.container.conditions.groups.match.' + item),
                        'value': item
                    });
                });

                return matches;
            },
            'logics': function () {
                var self = this;
                var list = models.container.conditions.Condition.prototype.getLogics();
                var logics = [];

                api.each(list, function (index, item) {
                    logics.push({
                        'text': self.$root.$localization.getMessage('dialogs.container.conditions.groups.parameter.logic.' + item),
                        'value': item
                    });
                });

                return logics;
            },
            'level': function () {
                return this.item.getLevel();
            }
        },
        'data': function () {
            return {};
        },
        'props': ['item'],
        'methods': {
            'isGroup': function () {
                return this.item instanceof models.container.conditions.Group;
            },
            'isCondition': function () {
                return this.item instanceof models.container.conditions.Condition;
            },
            'remove': function () {
                if (this.root.selectedGroup === this.item)
                    this.root.selectedGroup = this.root.condition;

                this.item.parent = null;
            }
        }
    }
})();
