(function () {
    return {
        'data': {
            'widgetsResources': {},
            'widgets': []
        },
        'methods': {
            'getWidget': function (code) {
                return api.array.find(code, this.widgets, function (index, item, code) {
                    return item.getCode() === code;
                });
            },
            'isWidgetRegistered': function (widget, template) {
                widget = this.getWidget(widget);

                if (widget === null)
                    return false;

                if (api.isDeclared(template)) {
                    template = widget.getTemplate(template);

                    if (template === null)
                        return false;

                    return api.isDeclared(this.widgetsResources[widget.getCode()]) && api.isDeclared(this.widgetsResources[widget.getCode() + ':' + template.code]);
                } else {
                    return api.isDeclared(this.widgetsResources[widget.getCode()]);
                }
            },
            'isWidgetResourcesLoaded': function (widget, template) {
                widget = this.getWidget(widget);

                if (widget === null)
                    return false;

                if (api.isDeclared(template)) {
                    template = widget.getTemplate(template);

                    if (template === null)
                        return false;

                    if (!this.isWidgetRegistered(widget, template))
                        return false;

                    return api.isArray(this.widgetsResources[widget.getCode()]) && api.isArray(this.widgetsResources[widget.getCode() + ':' + template.code]);
                } else {
                    if (!this.isWidgetRegistered(widget, template))
                        return false;

                    return api.isArray(this.widgetsResources[widget.getCode()]);
                }
            },
            'refreshWidgets': function () {
                var self = this;

                return this.requestWidgets().then(function (response) {
                    self.widgets = response;

                    return response;
                });
            },
            'registerWidget': function (widget, template) {
                var self = this;

                widget = self.getWidget(widget);

                if (widget === null)
                    return;

                if (!self.isWidgetRegistered(widget)) {
                    self.$set(self.widgetsResources, widget.getCode(), true);

                    self.requestWidgetHeaders(widget.getCode()).then(function (response) {
                        self.$resources.loadFromText(response).then(function (nodes) {
                            self.widgetsResources[widget.getCode()] = nodes;
                        });
                    });
                }

                if (api.isDeclared(template)) {
                    template = widget.getTemplate(template);

                    if (template === null)
                        return;

                    if (!self.isWidgetRegistered(widget, template)) {
                        self.$set(self.widgetsResources, widget.getCode() + ':' + template.code, true);

                        self.requestWidgetHeaders(widget.getCode(), template.code).then(function (response) {
                            self.$resources.loadFromText(response).then(function (nodes) {
                                self.widgetsResources[widget.getCode() + ':' + template.code] = nodes;
                            });
                        });
                    }
                }
            },
            'requestWidgetHeaders': function (code, template) {
                return this.request('widget.getHeaders', {
                    'code': code,
                    'template': template
                }, {
                    'environment': true,
                    'responseType': 'text'
                });
            },
            'requestWidgets': function () {
                return this.request('widget.getList').then(function (response) {
                    return api.array.rebuild(response.data, function (index, data) {
                        return new models.Widget(data);
                    });
                });
            },
            'useWidget': function (widget, template) {
                widget = this.getWidget(widget);

                if (widget !== null) {
                    if (api.isDeclared(template)) {
                        template = widget.getTemplate(template);

                        if (template === null)
                            return null;
                    }

                    if (!this.isWidgetRegistered(widget, template))
                        this.registerWidget(widget, template);

                    return widget;
                }

                return null;
            }
        }
    }
})();
