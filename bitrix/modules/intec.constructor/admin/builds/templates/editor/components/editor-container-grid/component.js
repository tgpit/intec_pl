(function () {
    return {
        'computed': {
            'normalizedWidth': function () {
                return this.validateNumber(this.width);
            },
            'widthLines': function () {
                var lines;

                if (this.type !== 'fixed') {
                    lines = this.countAdaptive(this.normalizedWidth);
                } else {
                    lines = this.countFixed(this.normalizedWidth, this.componentWidth);
                }

                return lines;
            },
            'widthStep': function () {
                if (this.type !== 'fixed') {
                    if (this.normalizedWidth < 2)
                        return 100;

                    return 100 / (this.normalizedWidth);
                } else {
                    return this.normalizedWidth;
                }
            },
            'normalizedHeight': function () {
                return this.validateNumber(this.height);
            },
            'heightLines': function () {
                var lines;

                if (this.type !== 'fixed') {
                    lines = this.countAdaptive(this.normalizedHeight);
                } else {
                    lines = this.countFixed(this.normalizedHeight, this.componentHeight);
                }

                return lines;
            },
            'heightStep': function () {
                if (this.type !== 'fixed') {
                    if (this.normalizedHeight < 2)
                        return 100;

                    return 100 / (this.normalizedHeight);
                } else {
                    return this.normalizedHeight;
                }
            },
            'measure': function () {
                return this.type === 'fixed' ? 'px' : '%';
            }
        },
        'props': {
            'width': {
                'required': false,
                'default': 2
            },
            'height': {
                'required': false,
                'default': 2
            },
            'type': {
                'required': false,
                'default': 'adaptive',
                'validator': function (value) {
                    return ['none', 'adaptive', 'fixed'].indexOf(value) !== -1;
                }
            }
        },
        'data': function () {
            return {
                'componentWidth': 0,
                'componentHeight': 0
            };
        },
        'methods': {
            'validateNumber': function (number) {
                number = api.toInteger(number);

                if (api.isNaN(number))
                    number = 0;

                return number;
            },
            'countAdaptive': function (quantity) {
                if (quantity < 1)
                    return [];

                var increment = 1;
                var counted = [];

                while (increment <= quantity - 1) {
                    counted.push(0);
                    increment++;
                }
                
                return counted;
            },
            'countFixed': function (step, value) {
                var quantity;
                var quantityInteger;
                var counted = [];
                var increment = 1;

                if (step < 1 || step >= value)
                    return counted;

                quantity = value / step;
                quantityInteger = api.toInteger(quantity);

                if (quantity > quantityInteger)
                    quantity = quantityInteger;
                else
                    quantity--;

                while (increment <= quantity) {
                    counted.push(0);
                    increment++;
                }

                return counted;
            },
            'getComponentSize': function () {
                this.componentWidth = this.$el.clientWidth;
                this.componentHeight = this.$el.clientHeight;
            }
        },
        'mounted': function () {
            this.getComponentSize();
        },
        'updated': function () {
            this.getComponentSize();
        }
    };
})();