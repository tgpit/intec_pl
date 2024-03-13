(function () {
    return {
        'computed': {
            'code': function () {
                return this.model.code;
            },
            'template': function () {
                return this.model.template;
            },
            'component': function () {
                return this.model.component;
            },
            'isBusy': function () {
                return !this.isInitialized || this.isRefreshing || !this.isResourcesLoaded;
            },
            'isInitialized': function () {
                return this.component !== null;
            },
            'isLoaded': function () {
                return this.isInitialized && this.isResourcesLoaded;
            },
            'isResourcesLoaded': function () {
                return this.$root.isWidgetResourcesLoaded(this.code, this.template);
            },
            'widget': function () {
                return this.$root.useWidget(this.code, this.template);
            }
        },
        'mounted': function () {
            var self = this;

            if (!self.isInitialized)
                self.refresh();

            if (self.isBusy)
                self.emitBeginBusy();
        },
        'data': function () {
            return {
                'isRefreshing': false
            }
        },
        'methods': {
            'emitBeginBusy': function () {
                this.$emit('begin-busy')
            },
            'emitEndBusy': function () {
                this.$emit('end-busy');
            },
            'initialize': function () {

            },
            'refresh': function () {
                this.isRefreshing = true;

                if (this.widget)
                    this.model.component = this.widget.compileViewComponent(this.model);

                this.isRefreshing = false;

                return Promise.resolve();
            },
            'saveProperties': function (properties) {
                this.model.properties = properties;
            }
        },
        'props': {
            'model': {
                'type': models.elements.Widget,
                'required': true
            }
        },
        'watch': {
            'code': function () {
                this.refresh();
            },
            'isBusy': function (value) {
                if (value) {
                    this.emitBeginBusy();
                } else {
                    this.emitEndBusy();
                }
            },
            'template': function () {
                this.refresh();
            },
            'widget': function () {
                this.refresh();
            }
        }
    }
})();
