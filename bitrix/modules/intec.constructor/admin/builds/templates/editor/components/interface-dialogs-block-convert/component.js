(function () {
    return {
        'computed': {
            'hasError': function () {
                return this.error !== null;
            }
        },
        'data': function () {
            return {
                'block': null,
                'code': null,
                'display': false,
                'error': null,
                'isApplying': false,
                'name': null
            };
        },
        'methods': {
            'apply': function () {
                var self = this;

                if (self.isApplying)
                    return;

                self.isApplying = true;
                self.error = null;

                return self.$root.requestBlockConverting(self.block.id, self.code, self.name).then(function () {
                    self.isApplying = false;
                    self.close();
                }, function (error) {
                    self.isApplying = false;
                    self.error = error.message;
                });
            },
            'open': function (block) {
                if (this.display || !(block instanceof models.elements.Block))
                    return;

                this.block = block;
                this.code = null;
                this.name = block.name;
                this.error = null;
                this.display = true;
            },
            'close': function () {
                this.display = false;
                this.block = null;
            }
        }
    }
})();
