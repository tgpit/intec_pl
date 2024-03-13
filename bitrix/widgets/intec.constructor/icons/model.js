(function () {
    /**
     * @var Object meta
     */

    if (meta.type === 'properties') {
        return function () {
            return {
                'header': {
                    'show': false,
                    'value': null
                },
                'caption': {
                    'style': {
                        'bold': false,
                        'italic': false,
                        'underline': false
                    },
                    'text': {
                        'align': 'left',
                        'size': {
                            'value': 14,
                            'measure': 'px'
                        },
                        'color': null,
                        'opacity': 0
                    }
                },
                'description': {
                    'style': {
                        'bold': false,
                        'italic': false,
                        'underline': false
                    },
                    'text': {
                        'align': 'left',
                        'size': {
                            'value': 14,
                            'measure': 'px'
                        },
                        'color': null,
                        'opacity': 0
                    }
                },
                'background': {
                    'show': false,
                    'color': null,
                    'rounding': {
                        'shared': false,
                        'value': null,
                        'measure': 'px',
                        'top': {
                            'value': null,
                            'measure': 'px'
                        },
                        'left': {
                            'value': null,
                            'measure': 'px'
                        },
                        'right': {
                            'value': null,
                            'measure': 'px'
                        },
                        'bottom': {
                            'value': null,
                            'measure': 'px'
                        }
                    },
                    'opacity': 0
                },
                'count': 4,
                'items': []
            }
        };
    } else if (meta.type === 'view') {
        return {
            'computed': {
                'width': function () {
                    return 100 / this.properties.count + '%';
                },
                'captionOpacity': function () {
                    return 1 - this.properties.caption.text.opacity / 100
                },
                'descriptionOpacity': function () {
                    return 1 - this.properties.description.text.opacity / 100
                },
                'backgroundRounding': function () {
                    if (!this.properties.background.show)
                        return null;

                    var backgroundRounding;

                    if (this.properties.background.rounding.shared)
                        backgroundRounding =
                            this.properties.background.rounding.value +
                            this.properties.background.rounding.measure;
                    else
                        backgroundRounding =
                            this.properties.background.rounding.top.value +
                            this.properties.background.rounding.top.measure + ' ' +
                            this.properties.background.rounding.left.value +
                            this.properties.background.rounding.left.measure + ' ' +
                            this.properties.background.rounding.right.value +
                            this.properties.background.rounding.right.measure + ' ' +
                            this.properties.background.rounding.bottom.value +
                            this.properties.background.rounding.bottom.measure;

                    return backgroundRounding;
                },
                'backgroundOpacity': function () {
                    return 1 - this.properties.background.opacity / 100;
                }
            },
            'methods': {
                'replacePathMacros': function (value) {
                    return this.$root.replacePathMacros(value);
                }
            }
        };
    } else if (meta.type === 'settings') {
        return {
            'computed': {
                'iconsCount': {
                    'get': function () {
                        if (api.isEmpty(this.properties.count))
                            return 4;

                        return this.properties.count;
                    },
                    'set': function (value) {
                        if (api.isEmpty(value))
                            this.properties.count = 4;
                        else if (value < 1)
                            this.properties.count = 1;
                        else if (value > 5)
                            this.properties.count = 5;
                        else
                            this.properties.count = value;
                    }
                },
                'captionColor': {
                    'get': function () {
                        if (api.isEmpty(this.properties.caption.text.color))
                            return '#000000';

                        return this.properties.caption.text.color;
                    },
                    'set': function (value) {
                        if (this.menu.caption && (!api.isEmpty(value) || api.isString(value)))
                            this.properties.caption.text.color = value;
                    }
                },
                'descriptionColor': {
                    'get': function () {
                        if (api.isEmpty(this.properties.description.text.color))
                            return '#000000';

                        return this.properties.description.text.color;
                    },
                    'set': function (value) {
                        if (this.menu.description && (!api.isEmpty(value) || api.isString(value)))
                            this.properties.description.text.color = value;
                    }
                },
                'backgroundColor': {
                    'get': function () {
                        if (api.isEmpty(this.properties.background.color))
                            return '#000000';

                        return this.properties.background.color;
                    },
                    'set': function (value) {
                        if (this.menu.background && (!api.isEmpty(value) || api.isString(value)))
                            this.properties.background.color = value;
                    }
                }
            },
            'data': function () {
                return {
                    'menu': {
                        'caption': false,
                        'description': false,
                        'background': false
                    }
                }
            },
            'methods': {
                'replacePathMacros': function (value) {
                    return this.$root.replacePathMacros(value);
                },
                'iconAdd': function (item) {
                    this.properties.items.push({
                        'name': null,
                        'description': null,
                        'image': item.value
                    });
                },
                'iconReplace': function (item, icon) {
                    item.image = icon.value;
                },
                'iconRemove': function (item) {
                    var index;

                    index = this.properties.items.indexOf(item);

                    if (index >= 0)
                        this.properties.items.splice(index, 1);
                }
            }
        };
    }
})();