(function () {
    return {
        'computed': {
            'code': function () {
                if (this.container !== null)
                    return JSON.stringify(this.container.save(false), null, '\t');

                return null;
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
