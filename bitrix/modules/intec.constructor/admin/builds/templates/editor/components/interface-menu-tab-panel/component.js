(function () {
    return {
        'updated': function () {
            if (this.$refs.scrollbar)
                this.$refs.scrollbar.scrollTo({
                    'y': this.scrollbarPosition
                }, 0);
        },
        'data': function () {
            return {
                'scrollbarSettings': {
                    'vuescroll': {
                        'mode': 'native',
                        'sizeStrategy': 'percent',
                        'detectResize': true
                    },
                    'scrollPanel': {
                        'initialScrollY': false,
                        'initialScrollX': false,
                        'scrollingX': true,
                        'scrollingY': true,
                        'speed': 300
                    },
                    'bar': {
                        'onlyShowBarOnScroll': false,
                        'background': '#8F8F8F'
                    }
                },
                'scrollbarPosition': 0
            };
        },
        'computed': {
            'isActive': function () {
                return this.active;
            }
        },
        'props': {
            'active': {
                'type': Boolean,
                'required': true,
                'default': false
            }
        },
        'methods': {
            'handleScroll': function (vertical, horizontal, event) {
                this.scrollbarPosition = vertical.scrollTop;
            }
        }
    }
})();
