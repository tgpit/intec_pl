(function () {
    return {
        'computed': {
            'isBufferFilled': function () {
                return this.buffer instanceof models.Container;
            }
        },
        'data': {
            'buffer': null
        },
        'methods': {
            'isStoredInBuffer': function (container) {
                return this.buffer === container;
            },
            'storeInBuffer': function (container) {
                if (container instanceof models.Container) {
                    this.buffer = container;
                } else {
                    this.buffer = null;
                }
            },
            'restoreFromBuffer': function (forget) {
                var buffer = this.buffer;

                if (this.isBufferFilled)
                    if (forget) {
                        this.buffer = null;
                    } else {
                        this.buffer = buffer.clone();
                    }

                return buffer;
            },
            'clearBuffer': function () {
                this.buffer = null;
            }
        }
    }
})();
