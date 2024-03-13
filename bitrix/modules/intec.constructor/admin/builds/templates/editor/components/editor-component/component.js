(function () {
    return {
        'computed': {
            'id': function () {
                return this.model.id;
            },
            'code': function () {
                return this.model.code;
            },
            'name': function () {
                return this.model.data ? this.model.data.name : null;
            },
            'template': {
                'get': function () {
                    return this.model.template;
                },
                'set': function (value) {
                    this.model.template = value;
                }
            },
            'isBusy': function () {
                return !this.isInitialized || this.isRefreshing;
            },
            'isInitialized': function () {
                return this.content !== null;
            },
            'properties': {
                'get': function () {
                    return this.model.properties;
                },
                'set': function (value) {
                    this.model.properties = value;
                }
            },
            'content': function () {
                return this.model.content;
            }
        },
        'mounted': function () {
            var self = this;

            if (!self.isInitialized)
                self.refresh();

            if (self.isBusy)
                self.emitBeginBusy();

            self.isScriptsEvaluated = true;
            self.evaluateScripts();
        },
        'updated': function () {
            var self = this;

            if (!self.isScriptsEvaluated) {
                self.isScriptsEvaluated = true;
                self.evaluateScripts();
            }
        },
        'data': function () {
            return {
                'isRefreshing': false,
                'isScriptsEvaluated': false
            }
        },
        'methods': {
            'emitBeginBusy': function () {
                this.$emit('begin-busy')
            },
            'emitEndBusy': function () {
                this.$emit('end-busy');
            },
            'evaluateScripts': function () {
                var self = this;
                var container = self.$refs.content;

                if (container)
                    helpers.DOM.evaluateScripts(container.querySelectorAll('script'));
            },
            'requestData': function () {
                var self = this;

                if (!api.isDeclared(self.model.code))
                    return Promise.reject();

                return self.$root.request('component.getData', {
                    'code': self.model.code,
                    'template': self.model.template
                });
            },
            'refreshData': function () {
                var self = this;

                return self.requestData().then(function (response) {
                    api.object.configure(self.data, response.data);

                    return self.data;
                });
            },
            'requestContent': function () {
                var self = this;

                if (!api.isDeclared(self.model.code))
                    return Promise.reject();

                return self.$root.request('component.getContent', {
                    'code': self.model.code,
                    'template': self.model.template,
                    'parameters': self.model.properties
                }, {
                    'environment': true,
                    'responseType': 'text'
                });
            },
            'refreshContent': function () {
                var self = this;

                return self.requestContent().then(function (response) {
                    self.model.content = response;

                    return response;
                });
            },
            'refreshRecursive': function () {
                var self = this;
            },
            'refresh': function () {
                var self = this;

                if (self.isRefreshing)
                    return;

                self.isRefreshing = true;

                return Promise.all([
                    self.refreshData(),
                    self.refreshContent()
                ]).then(function (responses) {
                    self.isRefreshing = false;

                    return responses;
                }, function (reason) {
                    self.isRefreshing = false;

                    return reason;
                });
            }
        },
        'props': {
            'model': {
                'type': models.elements.Component,
                'required': true
            }
        },
        'watch': {
            'code': function () {
                if (this.isInitialized)
                    this.refresh();
            },
            'content': function () {
                this.isScriptsEvaluated = false;
            },
            'isBusy': function (value) {
                if (value) {
                    this.emitBeginBusy();
                } else {
                    this.emitEndBusy();
                }
            },
            'properties': {
                'deep': true,
                'handler': function () {
                    if (this.isInitialized)
                        this.refresh();
                }
            },
            'template': function () {
                if (this.isInitialized)
                    this.refresh();
            }
        }
    }
})();
