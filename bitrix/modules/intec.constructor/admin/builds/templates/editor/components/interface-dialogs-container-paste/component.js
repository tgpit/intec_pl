(function () {
    return {
        'data': function () {
            return {
                'callback': null,
                'display': false,
                'script': null
            }
        },
        'methods': {
            'apply': function () {
                try {
                    if (api.isFunction(this.callback))
                        this.callback(new models.Container(JSON.parse(this.script)));

                    this.close();
                } catch (error) {}
            },
            'close': function () {
                this.display = false;
                this.callback = null;
                this.script = null;
            },
            'open': function (callback) {
                if (this.display)
                    return;

                this.callback = callback;
                this.script = null;
                this.display = true;
            }
        }
    }
})();
