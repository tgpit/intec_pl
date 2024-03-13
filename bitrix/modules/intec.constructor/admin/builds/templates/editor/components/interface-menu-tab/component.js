(function () {
    return {
        'computed': {
            'isActive': function () {
                return this.isCurrent && this.active;
            },
            'isCurrent': function () {
                return this.$parent.tab === this;
            },
            'icon': function () {
                return this.$slots.icon;
            }
        },
        'updated': function () {
            var self = this;
            var popupsRemoving = [];

            api.each(self.popups, function (index, component) {
                if (self.$children.indexOf(component) < 0)
                    popupsRemoving.push(index);
            });

            if (popupsRemoving.length > 0) {
                popupsRemoving.reverse();

                api.each(popupsRemoving, function (index, popupIndex) {
                    self.popups.splice(popupIndex, 1);
                });
            }

            api.each(self.$children, function (index, component) {
                if (component.$options.name === 'v-interface-menu-tab-popup' && self.popups.indexOf(component) < 0)
                    self.popups.push(component);
            });
        },
        'data': function () {
            return {
                'scrollbarSettings': {
                    'vuescroll': {
                        'mode': 'native',
                        'sizeStrategy': 'percent',
                        'detectResize': true
                    },
                    'scrollPanel': {
                        'initialScrollY': false,
                        'initialScrollX': false,
                        'scrollingX': false,
                        'scrollingY': true,
                        'speed': 300
                    },
                    'bar': {
                        'onlyShowBarOnScroll': false,
                        'background': '#808080'
                    }
                },
                'popups': []
            };
        },
        'props': {
            'active': {
                'type': Boolean,
                'required': false,
                'default': true
            },
            'code': {
                'type': String,
                'required': true
            },
            'name': {
                'type': String,
                'required': true
            },
            'flat': {
                'type': Boolean,
                'required': false,
                'default': false
            }
        },
        'methods': {
            'open': function () {
                this.$parent.openTab(this);
            },
            'toggle': function () {
                this.$parent.toggleTab(this);
            },
            'close': function () {
                this.$parent.closeTab();
            },
            'getPopup': function (code) {
                var result = null;

                api.each(this.popups, function (index, component) {
                    if (component.code === code) {
                        result = component;
                        return false;
                    }
                });

                return result;
            },
            'openPopup': function (code, node) {
                var popup = this.getPopup(code);

                api.each(this.popups, function (index, component) {
                    if (component !== popup)
                        component.close();
                });

                if (popup) {
                    popup.open();
                    popup.adjust(node);
                }
            },
            'togglePopup': function (code, node) {
                var popup = this.getPopup(code);

                api.each(this.popups, function (index, component) {
                    if (component !== popup)
                        component.close();
                });

                if (popup) {
                    popup.toggle();
                    popup.adjust(node);
                }
            },
            'closePopup': function (code) {
                var popup = this.getPopup(code);

                if (popup)
                    popup.close();
            }
        },
        'watch': {
            'isActive': function (value) {
                if (value) {
                    this.$emit('open', this);
                } else {
                    this.$emit('close', this);
                }
            }
        }
    }
})();
