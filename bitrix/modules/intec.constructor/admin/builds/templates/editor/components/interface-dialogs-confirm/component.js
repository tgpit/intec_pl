(function () {
    return {
        'computed': {},
        'data': function () {
            return {
                'display': false,
                'data': null
            };
        },
        'props': {
            'maxWidth': {
                'default': 400
            }
        },
        'methods': {
            'emitConfirm': function () {
                this.$emit('confirm', this.data);
            },
            'emitReject': function () {
                this.$emit('reject', this.data);
            },
            'open': function (data) {
                if (api.isUndefined(data))
                    data = null;

                this.display = true;
                this.data = data;
            },
            'confirm': function () {
                this.emitConfirm();
                this.close();
            },
            'reject': function () {
                this.emitReject();
                this.close();
            },
            'close': function () {
                this.display = false;
            }
        }
    }
})();
