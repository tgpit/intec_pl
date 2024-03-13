(function () {
    return {
        'computed': {
            'isPresetsFiltered': function () {
                return this.presetsFilter !== null && this.presetsFilter.length > 0;
            },
            'presetsGroups': function () {
                var result = [];

                api.each(this.presets, function (index, preset) {
                    if (preset.hasGroup() && result.indexOf(preset.group) === -1)
                        result.push(preset.group);
                });

                return result;
            },
            'sortedPresets': function () {
                var result = api.array.rebuild(this.presets);

                result.sort(function (left, right) {
                    if (!api.isDeclared(left.sort) && !api.isDeclared(right.sort)) {
                        return 0;
                    } else if (!api.isDeclared(left.sort)) {
                        return 1;
                    } else if (!api.isDeclared(right.sort)) {
                        return -1;
                    }

                    return left.sort - right.sort;
                });

                return result;
            },
            'sortedPresetsGroups': function () {
                var result = api.array.rebuild(this.presetsGroups);

                result.sort(function (left, right) {
                    if (!api.isDeclared(left.sort) && !api.isDeclared(right.sort)) {
                        return 0;
                    } else if (!api.isDeclared(left.sort)) {
                        return 1;
                    } else if (!api.isDeclared(right.sort)) {
                        return -1;
                    }

                    return left.sort - right.sort;
                });

                return result;
            }
        },
        'data': {
            'presets': [],
            'presetsGroup': null,
            'presetsFilter': null
        },
        'methods': {
            'isPresetDisplay': function (preset) {
                if (!this.isPresetsFiltered)
                    return true;

                return preset.name && preset.name.toLowerCase().indexOf(this.presetsFilter.toLowerCase()) >= 0;
            },
            'isPresetsGroupSelected': function (group) {
                return this.presetsGroup === group;
            },
            'isPresetsGroupDisplay': function (group) {
                var self = this;
                var result = !this.isPresetsFiltered;

                if (!result)
                    api.each(group.presets, function (index, preset) {
                        if (self.isPresetDisplay(preset)) {
                            result = true;
                            return false;
                        }
                    });

                return result;
            },
            'refreshPresets': function () {
                var self = this;

                return this.requestPresets().then(function (response) {
                    self.presets = response;

                    return response;
                });
            },
            'requestPresets': function () {
                var self = this;

                return this.request('preset.getList').then(function (response) {
                    var groups = [];
                    var presets = [];
                    var handlers = {
                        'block': function (configuration, resolve, reject) {
                            self.requestBlockCreating(configuration.code).then(resolve, reject);
                        },
                        'component': function (model, configuration, resolve) {
                            self.openElementSettings(model);
                            resolve();
                        }
                    };

                    groups.push(new models.preset.Group({
                        'code': 'system',
                        'name': self.$localization.getMessage('widgets.presets.group.system'),
                        'sort': 0
                    }));

                    groups.push(new models.preset.Group({
                        'code': 'widgets',
                        'name': self.$localization.getMessage('widgets.presets.group.widgets'),
                        'sort': 1
                    }));

                    groups.push(new models.preset.Group({
                        'code': 'other',
                        'name': self.$localization.getMessage('widgets.presets.group.other')
                    }));

                    presets.push(new models.Preset({
                        'type': 'container',
                        'name': self.$localization.getMessage('widgets.container.name'),
                        'configuration': {
                            'type': 'normal',
                            'display': true
                        }
                    }, groups[0]));

                    presets.push(new models.Preset({
                        'type': 'area',
                        'name': self.$localization.getMessage('widgets.area.name'),
                        'handler': function (configuration, resolve) {
                            self.interface.dialogs.areaSelect.open(function (area) {
                                resolve(area);
                            });
                        }
                    }, groups[0]));

                    presets.push(new models.Preset({
                        'type': 'variator',
                        'name': self.$localization.getMessage('widgets.variator.name')
                    }, groups[0]));

                    presets.push(new models.Preset({
                        'type': 'component',
                        'name': self.$localization.getMessage('widgets.component.name'),
                        'handler': function (model, configuration, resolve) {
                            self.interface.dialogs.componentList.open(function (item) {
                                model.code = item.code;
                                self.openElementSettings(model);
                                resolve();
                            });
                        }
                    }, groups[0]));

                    presets.push(new models.Preset({
                        'type': 'custom',
                        'name': self.$localization.getMessage('widgets.code.name'),
                        'handler': function (configuration, resolve) {
                            self.interface.dialogs.containerPaste.open(function (container) {
                                resolve(container);
                            });
                        }
                    }, groups[0]));

                    api.each(response.data, function (index, data) {
                        var group = null;

                        if (data.type === 'widget') {
                            group = groups[1];
                        } else if (api.isDeclared(data.group) && api.isDeclared(data.group.code)) {
                            group = api.array.find(data.group, groups, function (index, group, object) {
                                return group.code === object.code;
                            });

                            if (!group) {
                                group = new models.preset.Group(data.group);
                                groups.push(group);
                            }
                        } else {
                            group = groups[2];
                        }

                        presets.push(new models.Preset(data, group));
                    });

                    api.each(presets, function (index, preset) {
                        if (preset.hasHandler())
                            return;

                        var handler = handlers[preset.getType()];

                        if (api.isDeclared(handler))
                            preset.handler = handler;
                    });

                    return presets;
                });
            }
        }
    }
})();
