(function () {
    return {
        'computed': {
            'isActive': function () {
                return this.active;
            },
            'isCloseable': function () {
                return this.closeable;
            }
        },
        'data': function () {
            return {
                'active': false
            };
        },
        'props': {
            'code': {
                'type': String,
                'required': true
            },
            'closeable': {
                'type': Boolean,
                'required': false,
                'default': true
            }
        },
        'methods': {
            'adjust': function (node) {
                var self = this;

                if (self.isActive && node) {
                    self.$nextTick(function () {
                        self.$el.style.top = null;

                        var selfBounds = self.$el.getBoundingClientRect();
                        var nodeBounds = node.getBoundingClientRect();
                        var difference = nodeBounds.top - selfBounds.top;

                        if (difference > 0) {
                            if (window.innerHeight < difference + selfBounds.bottom) {
                                if (window.innerHeight > selfBounds.bottom + 4)
                                    self.$el.style.top = (window.innerHeight - selfBounds.bottom - 4) + 'px';
                            } else {
                                self.$el.style.top = difference + 'px';
                            }
                        }
                    });
                }
            },
            'open': function () {
                this.active = true;
            },
            'toggle': function () {
                this.active = !this.active;
            },
            'close': function () {
                this.active = false;
            }
        }
    }
})();
