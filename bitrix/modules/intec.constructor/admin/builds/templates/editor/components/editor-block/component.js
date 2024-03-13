(function () {
    return {
        'computed': {
            'id': function () {
                return this.model.id;
            },
            'content': function () {
                return this.model.content;
            },
            'isBusy': function () {
                return !this.isInitialized || this.isRefreshing;
            },
            'isInitialized': function () {
                return this.content !== null;
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
            'requestContent': function () {
                var self = this;

                if (!api.isDeclared(self.model.id))
                    return Promise.reject();

                return self.$root.requestBlockContent(self.model.id);
            },
            'refreshContent': function () {
                var self = this;

                return self.requestContent().then(function (response) {
                    self.model.content = response;

                    return response;
                });
            },
            'refresh': function () {
                var self = this;

                if (self.isRefreshing)
                    return;

                self.isRefreshing = true;

                return Promise.all([
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
                'type': models.elements.Block,
                'required': true
            }
        },
        'watch': {
            'id': function () {
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
            }
        }
    };
})();
