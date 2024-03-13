(function () {
    var prototype;
    var model;
    var properties = {};

    properties.color = function (self, name, property) {
        Object.defineProperty(self, name, {
            'configurable': true,
            'enumerable': true,
            'get': function () {
                var value = api.object.getValue(self, property);

                if (!api.isDeclared(value))
                    return '#000000';

                return value;
            },
            'set': function (value) {
                if (api.isEmpty(value) || !api.isString(value))
                    api.object.setValue(this, property, null);

                api.object.setValue(this, property, value);
            }
        });
    };

    properties.number = function (self, name, property) {
        Object.defineProperty(self, name, {
            'configurable': true,
            'enumerable': true,
            'get': function () {
                var value = api.object.getValue(self, property);

                if (!api.isDeclared(value))
                    return 0;

                return value;
            },
            'set': function (value) {
                if (!api.isNumber(value))
                    api.object.setValue(this, property, null);

                api.object.setValue(this, property, value);
            }
        });
    };

    models.Container = function (data, parent) {
        var self = this;

        if (!api.isObject(data))
            data = {};

        self.id = null;
        self.uid = uid++;
        self.code = null;
        self.type = 'normal';
        self.display = true;
        self.order = null;
        self.script = null;
        self.properties = {};
        self.element = null;
        self.containers = [];
        self.condition = null;
        self.zone = null;

        Object.defineProperty(self, 'parent', {
            'configurable': true,
            'enumerable': true,
            'get': function () {
                return parent;
            },
            'set': function (value) {
                if (
                    !(value instanceof models.Container) &&
                    !(value instanceof models.Element)
                ) value = null;

                var index;

                if (parent instanceof models.Container) {
                    index = parent.containers.indexOf(self);

                    if (index >= 0)
                        parent.containers.splice(index, 1);
                } else if (parent instanceof models.Element) {
                    if (parent.canStoreContainer())
                        parent.container = null;
                }

                if (value instanceof models.Container) {
                    value.containers.push(self);
                } else if (value instanceof models.Element) {
                    if (value.canStoreContainer()) {
                        value.container = self;
                    } else {
                        value = null;
                    }
                }

                parent = value;
            }
        });

        self.parent = parent;
        self.properties.id = null;
        self.properties.class = null;
        self.properties.grid = {
            'show': false,
            'type': 'none',
            'width': 0,
            'height': 0
        };

        api.each(self.getStyleProperties(), function (code, property) {
            if (
                property.type === 'float' ||
                property.type === 'opacity'
            ) {
                self.properties[code] = null;
            } else if (property.type === 'background') {
                self.properties[code] = {
                    'color': null,
                    'image': {
                        'url': null
                    },
                    'repeat': null,
                    'size': {
                        'type': null,
                        'width': {
                            'value': null,
                            'measure': property.measures[0]
                        },
                        'height': {
                            'value': null,
                            'measure': property.measures[0]
                        }
                    },
                    'position': {
                        'top': {
                            'value': null,
                            'measure': property.measures[0]
                        },
                        'left': {
                            'value': null,
                            'measure': property.measures[0]
                        }
                    }
                };
            } else if (
                property.type === 'side' ||
                property.type === 'size'
            ) {
                self.properties[code] = {
                    'value': null,
                    'measure': property.measures[0]
                };

                if (property.type === 'size')
                    api.each(['min', 'max'], function (index, part) {
                        self.properties[code][part] = {
                            'value': null,
                            'measure': property.measures[0]
                        };
                    });
            } else if (property.type === 'indent') {
                self.properties[code] = {
                    'value': null,
                    'measure': property.measures[0]
                };

                if (code === 'margin')
                    self.properties[code].isAuto = false;

                api.each(['top', 'right', 'bottom', 'left'], function (index, part) {
                    self.properties[code][part] = {
                        'value': null,
                        'measure': property.measures[0]
                    };

                    if (code === 'margin')
                        self.properties[code][part].isAuto = false;
                });
            } else if (property.type === 'border') {
                self.properties[code] = {
                    'width': {
                        'value': null,
                        'measure': property.width.measures[0]
                    },
                    'color': null,
                    'style': null,
                    'radius': {
                        'value': null,
                        'measure': property.radius.measures[0]
                    }
                };

                api.each(['top', 'right', 'bottom', 'left'], function (index, part) {
                    self.properties[code][part] = {
                        'width': {
                            'value': null,
                            'measure': property.width.measures[0]
                        },
                        'color': {
                            'value': null
                        },
                        'style': {
                            'value': null
                        },
                        'radius': {
                            'value': null,
                            'measure': property.radius.measures[0]
                        }
                    };
                });
            } else if (property.type === 'overflow') {
                self.properties[code] = {
                    'value': null,
                    'x': {
                        'value': null
                    },
                    'y': {
                        'value': null
                    }
                }
            } else if (property.type === 'text') {
                self.properties[code] = {
                    'font': null,
                    'size': {
                        'value': null,
                        'measure': property.size.measures[0]
                    },
                    'color': null,
                    'letterSpacing': {
                        'value': null,
                        'measure': property.letterSpacing.measures[0]
                    },
                    'lineHeight': {
                        'value': null,
                        'measure': property.lineHeight.measures[0]
                    },
                    'uppercase': false
                };
            }
        });

        api.object.configure(self, data, function (event) {
            if (['element', 'containers', 'parent'].indexOf(event.path[0]) >= 0) {
                event.cancel();
            } else if (event.path[0] === 'condition') {
                event.value = new models.container.conditions.Group(event.value);
            } else if (event.path[0] === 'properties') {
                if (
                    api.isObject(event.value) ||
                    api.isString(event.value) && event.value.length === 0
                ) event.cancel();
            }
        });

        Object.defineProperty(self, 'propertyOpacity', {
            'configurable': true,
            'enumerable': true,
            'get': function () {
                var value = self.properties.opacity;

                if (!api.isDeclared(value))
                    return 0;

                return api.toInteger((100 - (value * 100)).toFixed(0));
            },
            'set': function (value) {
                if (api.isDeclared(value) && value !== 0) {
                    if (value < 1)
                        value = 1;

                    if (value > 100)
                        value = 100;

                    self.properties.opacity = api.toFloat(((100 - value) / 100).toFixed(2));
                } else {
                    self.properties.opacity = null;
                }
            }
        });

        properties.color(self, 'propertyBackgroundColor', ['properties', 'background', 'color']);
        properties.color(self, 'propertyBorderColor', ['properties', 'border', 'color']);
        properties.color(self, 'propertyTextColor', ['properties', 'text', 'color']);
        properties.number(self, 'propertyBorderRadiusValue', ['properties', 'border', 'radius', 'value']);
        properties.number(self, 'propertyBorderTopRadiusValue', ['properties', 'border', 'top', 'radius', 'value']);
        properties.number(self, 'propertyBorderRightRadiusValue', ['properties', 'border', 'right', 'radius', 'value']);
        properties.number(self, 'propertyBorderBottomRadiusValue', ['properties', 'border', 'bottom', 'radius', 'value']);
        properties.number(self, 'propertyBorderLeftRadiusValue', ['properties', 'border', 'left', 'radius', 'value']);

        if (api.isObject(data.area)) {
            new models.elements.Area(data.area, self);
        } else if (api.isObject(data.block)) {
            new models.elements.Block(data.block, self);
        } else if (api.isObject(data.component)) {
            new models.elements.Component(data.component, self);
        } else if (api.isObject(data.variator)) {
            new models.elements.Variator(data.variator, self);
        } else if (api.isObject(data.widget)) {
            new models.elements.Widget(data.widget, self);
        } else {
            api.each(data.containers, function (index, data) {
                new model(data, self);
            });
        }
    };

    model = models.Container;
    prototype = model.prototype;

    /** Сохраняет контейнер */
    prototype.save = function (identified) {
        var self = this;
        var element;
        var result = {};

        result.id = null;
        result.code = null;

        if (identified) {
            result.id = self.id;
            result.code = self.code;
        }

        result.type = self.type;
        result.display = self.display;
        result.order = self.order;
        result.condition = null;
        result.script = self.script;
        result.properties = self.getNormalizedProperties();
        result.area = null;
        result.component = null;
        result.widget = null;
        result.block = null;
        result.variator = null;
        result.containers = [];
        result.zone = self.zone;

        if (self.condition instanceof models.container.conditions.Group)
            result.condition = self.condition.save();

        if (self.hasElement()) {
            element = self.element.save(identified);

            if (self.hasArea()) {
                result.area = element;
            } else if (self.hasBlock()) {
                result.block = element;
            } else if (self.hasComponent()) {
                result.component = element;
            } else if (self.hasVariator()) {
                result.variator = element;
            } else if (self.hasWidget()) {
                result.widget = element;
            }
        } else {
            api.each(self.containers, function (index, container) {
                result.containers.push(container.save(identified));
            });
        }

        return result;
    };

    /** Клонирует контейнер */
    prototype.clone = function () {
        return new model(this.save(false));
    };

    /** Проверяет возможность вставки контейнера */
    prototype.canPasteContainer = function (container) {
        var self = this;
        var result = false;

        if (!(container instanceof model))
            return result;

        result = self.hasArea() || self.hasVariator() || !self.hasElement();

        if (result)
            result = self !== container;

        if (result)
            this.eachParent(function (index, parent) {
                result = parent !== container;

                if (!result)
                    return false;
            });

        if (result && (self.hasArea() || self.isInAreaRoot())) {
            result = !container.hasArea();

            if (result)
                container.eachContainer(function (index, container) {
                    result = !container.hasArea();

                    if (!result)
                        return false;
                });
        }

        return result;
    };

    /** Осущевствляет вставку контейнера */
    prototype.pasteContainer = function (container, order) {
        var parent;

        if (!this.canPasteContainer(container))
            return false;

        if (this.hasArea()) {
            if (!this.element.hasContainer())
                return false;

            parent = this.element.container;
        } else if (this.hasVariator()) {
            if (!this.element.hasVariant() || !this.element.getVariant().hasContainer())
                return false;

            parent = this.element.getVariant().container;
        } else if (this.hasElement()) {
            return false;
        } else {
            parent = this;
        }

        container.parent = parent;
        container.order = order;
        container.parent.updateContainersOrder();

        return true;
    };

    /** Идет по всем контейнерам рекурсивно */
    (function () {
        var handler;

        handler = function (parent, containers, callback) {
            var result;

            api.each(containers, function (index, container) {
                var containers = null;

                result = callback(index, container, parent);

                if (result === false)
                    return false;

                if (container.hasElement()) {
                    if (container.hasArea()) {
                        if (container.element.hasContainer())
                            containers = [container.element.container];
                    } else if (container.hasVariator()) {
                        containers = [];

                        api.each(container.element.getSortedVariants(), function (index, variant) {
                            if (variant.hasContainer())
                                containers.push(variant.container);
                        });

                        if (containers.left === 0)
                            containers = null;
                    }
                } else if (container.hasContainers()) {
                    containers = container.getSortedContainers();
                }

                if (containers !== null)
                    result = handler(container, containers, callback);

                return result;
            });

            return result;
        };

        prototype.eachContainer = function (callback) {
            if (api.isFunction(callback))
                handler(this, this.getChildContainers(), callback)
        };
    })();

    /** Делает контейнер выше по порядку */
    prototype.orderUp = function () {
        if (this.isInContainer()) {
            this.order = this.order - 1.5;
            this.parent.updateContainersOrder();
            return true;
        }

        return false;
    };

    /** Делает контейнер ниже по порядку */
    prototype.orderDown = function () {
        if (this.isInContainer()) {
            this.order = this.order + 1.5;
            this.parent.updateContainersOrder();
            return true;
        }

        return false;
    };

    /** Обновляет порядок следования контейнеров */
    prototype.updateContainersOrder = function () {
        if (this.hasContainers()) {
            api.each(this.getSortedContainers(), function (index, container) {
                container.order = index;
            });
        }
    };

    /** Возвращает типы контейнеров */
    prototype.getTypes = function () {
        return [
            'normal',
            'absolute'
        ];
    };

    /** Возвращает тип контейнера */
    prototype.getType = function () {
        var types = this.getTypes();

        if (types.indexOf(this.type) === -1)
            return types[0];

        return this.type;
    };

    /** Возвращает нормализованные свойства */
    prototype.getNormalizedProperties = function () {
        var result = api.extend({}, this.properties);

        api.object.scan(result, function (event) {
            if (api.isString(event.value) && event.value.length === 0)
                event.value = null;
        });

        return result;
    };

    /** Возвращает список контейнеров в порядке сортировки */
    prototype.getSortedContainers = function () {
        var result = [];

        api.each(this.containers, function (index, container) {
            result.push(container);
        });

        result.sort(function (left, right) {
            if (!api.isDeclared(left.order) && !api.isDeclared(right.order)) {
                return 0;
            } else if (!api.isDeclared(left.order)) {
                return 1;
            } else if (!api.isDeclared(right.order)) {
                return -1;
            }

            return left.order - right.order;
        });

        return result;
    };

    /** Возвращает мета-информацию о свойствах стиля */
    prototype.getStyleProperties = function () {
        return {
            'float': {
                'type': 'float'
            },
            'opacity': {
                'type': 'opacity'
            },
            'background': {
                'type': 'background',
                'measures': ['px', 'cm', 'em', '%']
            },
            'text': {
                'type': 'text',
                'size': {
                    'measures': ['px', 'pt', '%', 'em']
                },
                'letterSpacing': {
                    'measures': ['px', 'pt', '%', 'em']
                },
                'lineHeight': {
                    'measures': ['px', 'pt', '%', 'em']
                }
            },
            'top': {
                'type': 'side',
                'measures': ['px', '%']
            },
            'right': {
                'type': 'side',
                'measures': ['px', '%']
            },
            'bottom': {
                'type': 'side',
                'measures': ['px', '%']
            },
            'left': {
                'type': 'side',
                'measures': ['px', '%']
            },
            'width': {
                'type': 'size',
                'measures': ['px', '%']
            },
            'height': {
                'type': 'size',
                'measures': ['px', '%']
            },
            'margin': {
                'type': 'indent',
                'measures': ['px', '%']
            },
            'padding': {
                'type': 'indent',
                'measures': ['px', '%']
            },
            'border': {
                'type': 'border',
                'width': {
                    'measures': ['px', 'em', 'cm']
                },
                'radius': {
                    'measures': ['px', '%']
                }
            },
            'overflow': {
                'type': 'overflow'
            }
        };
    };

    /** Возвращает мета-информацию о свойстве стиля */
    prototype.getStyleProperty = function (name) {
        return this.getStyleProperties()[name];
    };

    /** Возвращает список групп условий отображения контейнера */
    prototype.getConditionTypes = function () {
        return [
            'group',
            'path',
            'match',
            'expression',
            'parameter.get',
            'parameter.page',
            'parameter.template',
            'site'
        ];
    };

    /** Возможность конвертации контейнера */
    prototype.isConvertable = function () {
        if (!this.hasElement())
            return true;

        return this.hasArea() || this.hasVariator();
    };

    /** Преобразует контейнер в зону синхронизации */
    prototype.convertToArea = function (area) {
        var self = this;

        if (!(area instanceof models.elements.Area))
            return false;

        if (self.hasElement() && !self.convertToSimple())
            return false;

        api.each(self.getSortedContainers(), function (index, container) {
            container.parent = null;
        });

        area.parent = self;

        return true;
    };

    /** Преобразует контейнер в вариативный контейнер */
    prototype.convertToVariator = function () {
        var self = this;
        var element;

        if (self.hasElement() && !self.convertToSimple())
            return false;

        element = new models.elements.Variator(null, self);

        api.each(self.getSortedContainers(), function (index, container) {
            var variant = new models.elements.Variant(null, element);
            var parent = new models.Container();

            variant.order = index;
            parent.parent = variant;
            container.parent = parent;
        });

        element.updateVariantsOrder();

        if (element.variants.length > 0)
            element.setVariant(element.variants[0]);

        element.parent = self;

        return true;
    };

    /** Преобразует контейнер в обычный */
    prototype.convertToSimple = function () {
        var self = this;
        var element;
        var order;

        if (!self.hasElement())
            return true;

        if (self.hasArea()) {
            element = self.element;
            element.parent = null;

            if (element.hasContainer()) {
                api.each(element.container.containers, function (index, container) {
                    container = container.clone();
                    container.order = index;
                    container.parent = self;
                });
            }

            self.updateContainersOrder();

            return true;
        } else if (self.hasVariator()) {
            element = self.element;
            element.parent = null;
            order = 0;

            api.each(element.getSortedVariants(), function (index, variant) {
                if (variant.hasContainer()) {
                    api.each(variant.container.containers, function (index, container) {
                        container.order = order;
                        container.parent = self;
                        order++;
                    });
                }
            });

            self.updateContainersOrder();

            return true;
        }

        return false;
    };

    /** Идет по всем родителям рекурсивно */
    prototype.eachParent = function (callback) {
        var parent = this.parent;
        var index = 0;

        if (!api.isFunction(callback))
            return;

        while (parent !== null) {
            if (callback.call(this, index, parent) === false)
                break;

            index++;
            parent = parent.parent;
        }
    };

    /** Возвращает всех родителей */
    prototype.getParents = function () {
        var result = [];

        this.eachParent(function (index, parent) {
            result.push(parent);
        });

        return result;
    };

    /** Возвращает привязанность к родителю */
    prototype.hasParent = function (value) {
        var result = false;

        this.eachParent(function (index, parent) {
            result = parent === value;

            if (result)
                return false;
        });

        return result;
    };

    /** Возвращает родительский контейнер */
    prototype.getParentContainer = function () {
        var parent = this.parent;

        while (parent !== null) {
            if (parent instanceof model)
                return parent;

            parent = parent.parent;
        }

        return null;
    };

    /** Возвращает наличие родительского контейнера */
    prototype.hasParentContainer = function () {
        return this.getParentContainer() !== null;
    };

    prototype.getChildContainers = function () {
        var result = [];

        if (this.hasElement()) {
            if (this.element instanceof models.elements.Area && this.element.hasContainer())
                result = this.element.container.getSortedContainers();
            else if (this.element instanceof models.elements.Variator && this.element.hasVariant() && this.element.getVariant().hasContainer())
                result = this.element.getVariant().container.getSortedContainers();
        } else {
            result = this.getSortedContainers();
        }

        return result;
    };

    /** Возвращает наличие элемента в контейнере */
    prototype.hasElement = function () {
        return this.element instanceof models.Element;
    };

    /** Возвращает наличие зоны синхронизации в контейнере */
    prototype.hasArea = function () {
        return this.element instanceof models.elements.Area;
    };

    /** Возвращает наличие блока в контейнере */
    prototype.hasBlock = function () {
        return this.element instanceof models.elements.Block;
    };

    /** Возвращает наличие компонента в контейнере */
    prototype.hasComponent = function () {
        return this.element instanceof models.elements.Component;
    };

    /** Возвращает наличие вариатора в контейнере */
    prototype.hasVariator = function () {
        return this.element instanceof models.elements.Variator;
    };

    /** Возвращает наличие виджета в контейнере */
    prototype.hasWidget = function () {
        return this.element instanceof models.elements.Widget;
    };

    /** Возвращает наличие контейнеров в контейнере  */
    prototype.hasContainers = function () {
        return this.containers.length > 0;
    };

    /** Контейнер является корневым */
    prototype.isRoot = function () {
        return this.parent === null;
    };

    /** Контейнер находится в корневом контейнере */
    prototype.isInRoot = function () {
        return this.isInContainer() && this.parent.isRoot();
    };

    /** Контейнер находится в контейнере */
    prototype.isInContainer = function () {
        return this.parent !== null && this.parent instanceof models.Container;
    };

    /** Контейнер находится в элементе */
    prototype.isInElement = function () {
        return this.parent !== null && this.parent instanceof models.Element;
    };

    /** Контейнер находится в зоне синхронизации */
    prototype.isInArea = function () {
        return this.parent !== null && this.parent instanceof models.elements.Area;
    };

    /** Возвращает зону синхронизации в которую вложен контейнер */
    prototype.getAreaRoot = function () {
        var result = null;

        this.eachParent(function (index, parent) {
            if (parent instanceof models.elements.Area) {
                result = parent;
                return false;
            }
        });

        return result;
    };

    /** Контейнер вложен в зону синхронизации */
    prototype.isInAreaRoot = function () {
        return this.getAreaRoot() !== null;
    };

    /** Контейнер находится в варианте вариатора */
    prototype.isInVariatorVariant = function () {
        return this.parent !== null && this.parent instanceof models.elements.Variant;
    }
})();