(function () {
    return {
        'computed': {
            'script': {
                'get': function () {
                    if (this.container !== null)
                        return this.container.script;

                    return null;
                },
                'set': function (value) {
                    if (this.container !== null)
                        this.container.script = value;
                }
            }
        },
        'data': function () {
            return {
                'display': false,
                'container': null
            }
        },
        'methods': {
            'close': function () {
                this.display = false;
                this.container = null;
            },
            'open': function (container) {
                if (this.display || !(container instanceof models.Container))
                    return;

                this.container = container;
                this.display = true;
            }
        }
    }
})();
