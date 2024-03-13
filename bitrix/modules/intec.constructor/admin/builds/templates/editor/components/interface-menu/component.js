(function () {
    return {
        'mounted': function () {
            var self = this;

            api.each(this.$children, function (index, child) {
                if (child.$options.name === meta.data.components.tab)
                    self.tabs.push(child);
            });
        },
        'beforeDestroy': function () {
            var self = this;

            if (self.tabs.length > 0)
                self.tabs.splice(0, self.tabs.length);
        },
        'data': function () {
            return {
                'tab': null,
                'tabs': []
            };
        },
        'methods': {
            'getTab': function (code) {
                return api.array.find(code, this.tabs, function (index, item, code) {
                    return item.code === code;
                });
            },
            'openTab': function (code) {
                var self = this;
                var tab = this.getTab(code);

                if (tab !== null && tab !== this.tab) {
                    var old = this.tab;

                    self.tab = tab;
                    self.$emit('tab-change', [tab, old]);
                }
            },
            'closeTab': function () {
                var self = this;
                var tab = self.tab;

                if (tab !== null) {
                    var old = tab;

                    self.tab = null;
                    self.$emit('tab-change', [this.tab, old]);
                }
            },
            'toggleTab': function (code) {
                var self = this;
                var tab = this.getTab(code);

                if (tab !== null) {
                    if (tab === self.tab) {
                        self.closeTab();
                    } else {
                        self.openTab(tab);
                    }
                } else {
                    self.closeTab();
                }
            }
        }
    }
})();
