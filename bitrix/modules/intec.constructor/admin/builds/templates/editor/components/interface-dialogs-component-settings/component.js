(function () {
    return {
        'updated': function () {
            var self = this;
            var loaders = [];

            if (!self.isRefreshing && !self.isScriptsEvaluated) {
                self.isScriptsEvaluated = true;

                api.each(self.parameters, function (index, parameter) {
                    if (parameter.type !== 'CUSTOM')
                        return;

                    loaders.push(VueScript2.load(parameter.javascript.file).then(function () {
                        var event = window[parameter.javascript.event];
                        var input = self.getParameterInput(parameter);
                        var arguments = {
                            'data': parameter.javascript.data,
                            'fChange': function () {
                                parameter.value = input.value;
                            },
                            'getElements': self.getParametersInputs,
                            'oCont': self.getParameterCell(parameter),
                            'oInput': input,
                            'propertyID': parameter.code,
                            'propertyParams': api.extend({}, parameter.raw)
                        };

                        if (input)
                            input.onchange = function () {
                                parameter.value = input.value;
                            };

                        if (api.isFunction(event))
                            event(arguments);
                    }));
                });
            }

            Promise.all(loaders).then(function () {
                if (self.$refs.panelScrollbar)
                    self.$refs.panelScrollbar.scrollTo({
                        'y': self.panelScrollbarPosition
                    }, 0);

                if (self.$refs.bodyScrollbar)
                    self.$refs.bodyScrollbar.scrollTo({
                        'y': self.bodyScrollbarPosition
                    }, 0);

                self.updateGroupActivityThrottle();
            });
        },
        'computed': {
            'code': function () {
                return this.component ? this.component.code : null;
            },
            'isFiltered': function () {
                return this.filter !== null && this.filter.length > 0;
            },
            'parameters': function () {
                var self = this;
                var result = [];

                api.each(self.groups, function (index, group) {
                    api.each(group.parameters, function (index, parameter) {
                        result.push(parameter);
                    });
                });

                return result;
            },
            'updateGroupActivityThrottle': function () {
                return api.throttle(this.updateGroupActivity, 250, true);
            }
        },
        'data': function () {
            return {
                'bodyScrollbarPosition': 0,
                'description': null,
                'display': false,
                'component': null,
                'filter': null,
                'groups': [],
                'name': null,
                'panelScrollbarPosition': 0,
                'values': {},
                'isRefreshing': false,
                'isScriptsEvaluated': false,
                'scripts': [],
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
                'templates': [],
                'template': null
            };
        },
        'methods': {
            'apply': function () {
                this.component.template = this.template;
                this.component.properties = this.values;
                this.close();
            },
            'applyFilter': function () {
                var self = this;
                var filter;

                if (self.isFiltered) {
                    filter = self.filter.toLowerCase();

                    api.each(self.groups, function (index, group) {
                        var display = false;

                        api.each(group.parameters, function (index, parameter) {
                            parameter.display = !parameter.hidden && (
                                (parameter.code && parameter.code.toLowerCase().indexOf(filter) >= 0) ||
                                (parameter.name && parameter.name.toLowerCase().indexOf(filter) >= 0)
                            );

                            if (parameter.display)
                                display = true;
                        });

                        group.display = display || group.code === 'COMPONENT_TEMPLATE';
                    });
                } else {
                    api.each(self.groups, function (index, group) {
                        group.display = true;

                        api.each(group.parameters, function (index, parameter) {
                            parameter.display = true;
                        });
                    });
                }
            },
            'close': function () {
                this.display = false;
                this.component = null;
                this.template = null;
                this.values = {};
            },
            'getParameterCell': function (parameter) {
                var self = this;
                var parameterCell = self.$refs['parameterCell' + parameter.code];

                if (api.isArray(parameterCell) && parameterCell.length > 0)
                    return parameterCell[0];

                return null;
            },
            'getGroupsPanelItem': function (group) {
                if (this.$refs['groupItem' + group.code])
                    return this.$refs['groupItem' + group.code][0];

                return null;
            },
            'getGroupsPanelItems': function () {
                var self = this;
                var result = [];

                api.each(this.groups, function (index, group) {
                    var node = self.getGroupsPanelItem(group);

                    if (node)
                        result.push(node);
                });

                return result;
            },
            'getGroupRow': function (group) {
                var self = this;
                var node = self.$refs['groupRow' + group.code];

                if (api.isArray(node) && node.length > 0)
                    return node[0];

                return null;
            },
            'getParameterInput': function (parameter) {
                var self = this;
                var parameterInputs = self.$refs['parameterInput' + parameter.code];
                var parameterInput;

                if (api.isArray(parameterInputs) && parameterInputs.length > 0) {
                    parameterInput = parameterInputs[0];

                    if (parameterInput instanceof Vue)
                        parameterInput = parameterInput.$refs.input;

                    return parameterInput;
                }

                return null;
            },
            'getParametersInputs': function () {
                var self = this;
                var inputs = {};

                api.each(self.parameters, function (index, parameter) {
                    var parameterInputs = self.$refs['parameterInput' + parameter.code];
                    var parameterInput;

                    if (api.isArray(parameterInputs) && parameterInputs.length > 0) {
                        parameterInput = parameterInputs[0];

                        if (parameterInput instanceof Vue)
                            parameterInput = parameterInput.$refs.input;

                        inputs[parameter.code] = parameterInput;
                    }
                });

                return inputs;
            },
            'getParametersValues': function () {
                var self = this;
                var values = {};

                api.each(self.parameters, function (index, parameter) {
                    var value;

                    if (!parameter.multiple) {
                        value = parameter.value;
                    } else {
                        value = [];

                        api.each(parameter.value, function (index, parameterValue) {
                            if (parameterValue.value !== null && (api.isNumber(parameterValue.value) || parameterValue.value.length > 0))
                                value.push(parameterValue.value);
                        });
                    }

                    if (parameter.type === 'LIST' && parameter.extended) {
                        if (!parameter.multiple) {
                            if (value === null)
                                value = parameter.customValue;
                        } else {
                            api.each(parameter.customValue, function (index, parameterValue) {
                                if (parameterValue.value !== null && (api.isNumber(parameterValue.value) || parameterValue.value.length > 0))
                                    value.push(parameterValue.value);
                            });
                        }
                    }

                    values[parameter.code] = value;
                });

                return values;
            },
            'handleBodyScroll': function (vertical, horizontal, event) {
                this.bodyScrollbarPosition = vertical.scrollTop;
            },
            'handlePanelScroll': function (vertical, horizontal, event) {
                this.panelScrollbarPosition = vertical.scrollTop;
            },
            'open': function (component) {
                if (this.display || !(component instanceof models.elements.Component))
                    return;

                this.component = component;
                this.template = component.template;
                this.values = api.extend({}, component.properties);
                this.filter = null;
                this.bodyScrollbarPosition = 0;
                this.panelScrollbarPosition = 0;
                this.display = true;
                this.refresh();
            },
            'requestData': function (clear) {
                var values = {};

                if (!this.component)
                    return new Promise(function (resolve, reject) {
                        reject();
                    });

                api.each(this.values, function (key, value) {
                    if (value === null || (api.isArray(value) && value.length === 0))
                        value = '';

                    values[key] = value;
                });

                return this.$root.request('component.getParameters', {
                    'component': this.component.code,
                    'clear': clear ? 1 : 0,
                    'src_site': this.$root.environment.site,
                    'siteTemplateId': this.$root.environment.template,
                    'template': this.template,
                    'values': values
                }).then(function (response) {
                    return response.data;
                });
            },
            'refresh': function (clear) {
                var self = this;

                if (self.isRefreshing)
                    return;

                self.isRefreshing = true;
                self.isScriptsEvaluated = false;
                self.name = null;
                self.description = null;

                if (self.groups.length > 0)
                    self.groups.splice(0, self.groups.length);

                if (self.parameters.length > 0)
                    self.parameters.splice(0, self.parameters.length);

                if (self.templates.length > 0)
                    self.templates.splice(0, self.templates.length);

                return self.requestData(clear).then(function (data) {
                    var templateExists = false;

                    self.name = data.name;
                    self.description = data.description;
                    self.template = data.template;

                    api.each(data.groups, function (index, group) {
                        var hidden = true;

                        group.display = true;

                        api.each(group.parameters, function (index, parameter) {
                            var setted = false;

                            parameter.display = true;

                            if (!parameter.hidden)
                                hidden = false;

                            if (parameter.multiple) {
                                parameter.value = api.array.rebuild(parameter.value, function (index, value) {
                                    return {
                                        'value': value
                                    };
                                });
                            }

                            if (parameter.type === "STRING") {
                                if (parameter.multiple) {
                                    if (parameter.value.length === 0)
                                        parameter.value.push({
                                            'value': null
                                        });
                                }
                            }

                            if (parameter.type === "LIST") {
                                if (!parameter.multiple) {
                                    if (parameter.extended)
                                        parameter.customValue = null;

                                    api.each(parameter.values, function (index, value) {
                                        if (value.value == parameter.value) {
                                            setted = true;
                                            return false;
                                        }
                                    });

                                    if (!setted) {
                                        if (parameter.extended) {
                                            parameter.customValue = parameter.value;
                                            parameter.value = null;
                                        } else if (parameter.values.length > 0) {
                                            parameter.value = parameter.values[0].value;
                                        }
                                    }

                                    if (parameter.extended)
                                        parameter.values.unshift({
                                            'name': self.$root.$localization.getMessage('dialogs.componentSettings.values.other'),
                                            'value': null
                                        });
                                } else {
                                    var values = [];

                                    parameter.values.unshift({
                                        'name': self.$root.$localization.getMessage('dialogs.componentSettings.values.unset'),
                                        'value': null
                                    });

                                    if (parameter.extended)
                                        parameter.customValue = [];

                                    api.each(parameter.value, function (index, propertyValue) {
                                        var setted = false;

                                        api.each(parameter.values, function (index, value) {
                                            setted = propertyValue.value == value.value;

                                            if (setted) {
                                                values.push(value);
                                                return false;
                                            }
                                        });

                                        if (parameter.extended && !setted)
                                            parameter.customValue.push(propertyValue);
                                    });

                                    if (parameter.extended)
                                        if (parameter.customValue.length === 0)
                                            parameter.customValue.push({
                                                'value': null
                                            });

                                    parameter.value = values;
                                }
                            }
                        });

                        if (group.code === 'COMPONENT_TEMPLATE')
                            hidden = false;

                        group.hidden = hidden;

                        self.groups.push(group);
                    });

                    api.each(data.templates, function (index, template) {
                        if (template.code === self.template)
                            templateExists = true;

                        self.templates.push(template);
                    });

                    if (!templateExists)
                        self.template = '.default';

                    self.applyFilter();
                    self.isRefreshing = false;

                    self.$nextTick(function () {
                        self.updateGroupActivity();
                    });
                }, function (reason) {
                    self.isRefreshing = false;
                });
            },
            'scrollBodyScrollToGroup': function (group) {
                var self = this;
                var scrollbar = self.$refs.bodyScrollbar;

                if (!scrollbar)
                    return;

                var node = self.getGroupRow(group);
                var container = scrollbar.$refs.scrollContent;

                if (node) {
                    scrollbar.scrollTo({
                        'y': node.getBoundingClientRect().top - container.getBoundingClientRect().top
                    }, 500);
                }
            },
            'updateGroupActivity': function () {
                var self = this;
                var scrollbar;

                if (self.groups.length > 0) {
                    scrollbar  = self.$refs.bodyScrollbar;

                    if (!scrollbar)
                        return;

                    var container = scrollbar.$refs.scrollContent;
                    var current = null;
                    var node;
                    var nodes;

                    api.each(self.groups, function (index, group) {
                        var node = self.getGroupRow(group);
                        var active;

                        if (group.hidden || !group.display || !node)
                            return;

                        active = node.getBoundingClientRect().top - container.getBoundingClientRect().top;
                        active = active <= self.bodyScrollbarPosition;

                        if (active)
                            current = group;
                    });

                    if (current === null)
                        current = self.groups[0];

                    nodes = self.getGroupsPanelItems();

                    api.each(nodes, function (index, node) {
                        node.setAttribute('data-active', 'false');
                    });

                    node = self.getGroupsPanelItem(current);

                    if (node)
                        node.setAttribute('data-active', 'true');
                }
            }
        },
        'watch': {
            'bodyScrollbarPosition': function () {
                this.updateGroupActivityThrottle();
            },
            'filter': function () {
                var self = this;

                self.applyFilter();
                self.$nextTick(function () {
                    self.updateGroupActivityThrottle();
                });
            },
            'parameters': {
                'deep': true,
                'handler': function () {
                    var self = this;
                    var values = self.getParametersValues();
                    var changes = [];

                    api.each(self.parameters, function (index, parameter) {
                        var changed = false;
                        var oldValue = self.values[parameter.code];
                        var newValue = values[parameter.code];

                        if (!parameter.multiple) {
                            changed = oldValue !== newValue;
                        } else {
                            if (!api.isArray(oldValue))
                                oldValue = [];

                            if (!api.isArray(newValue))
                                newValue = [];

                            changed = oldValue.length !== newValue.length;

                            if (!changed) {
                                var stack = api.array.rebuild(newValue);

                                api.each(oldValue, function (index, value) {
                                    index = stack.indexOf(value);

                                    if (index >= 0) {
                                        stack.splice(index, 1);
                                    } else {
                                        changed = true;
                                        return false;
                                    }
                                });
                            }
                        }

                        if (changed)
                            changes.push(parameter);
                    });

                    if (changes.length > 0) {
                        self.values = values;

                        api.each(changes, function (index, parameter) {
                            if (parameter.refresh) {
                                self.refresh();
                                return false;
                            }
                        });
                    }
                }
            },
            'template': function () {
                this.refresh();
            }
        }
    }
})();
