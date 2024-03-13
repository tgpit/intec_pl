(function () {
    return {
        'computed': {
            'id': function () {
                return this.model.id;
            },
            'uid': function () {
                return this.model.uid;
            },
            'classes': function () {
                return this.model.properties.class;
            },
            'element': function () {
                return this.model.element;
            },
            'elementType': function () {
                if (!this.hasElement)
                    return null;

                if (this.element instanceof models.elements.Area) {
                    return 'area';
                } else if (this.element instanceof models.elements.Block) {
                    return 'block';
                } else if (this.element instanceof models.elements.Component) {
                    return 'component';
                } else if (this.element instanceof models.elements.Variator) {
                    return 'variator';
                }  else if (this.element instanceof models.elements.Widget) {
                    return 'widget';
                }

                return null;
            },
            'elementComponent': function () {
                if (this.elementType)
                    return meta.data.components[this.elementType];

                return null;
            },
            'containers': function () {
                return this.model.containers;
            },
            'sortedContainers': function () {
                return this.model.getSortedContainers();
            },
            'parentContainer': function () {
                return this.model.getParentContainer();
            },
            'hasElement': function () {
                return this.model.hasElement();
            },
            'hasArea': function () {
                return this.model.hasArea();
            },
            'hasBlock': function () {
                return this.model.hasBlock();
            },
            'hasVariator': function () {
                return this.model.hasVariator();
            },
            'hasContainers': function () {
                return this.model.hasContainers();
            },
            'hasParentContainer': function () {
                return this.model.hasParentContainer();
            },
            'hasHovered': function () {
                if (this.$root.hasHovering)
                    return this.$root.hovering.hasParent(this.model);

                return false;
            },
            'isDisplaying': function () {
                return this.$root.template.settings.containersHiddenShow || this.model.display || this.isSelected || this.isInternal;
            },
            'isBuffered': function () {
                return this.$root.buffer === this.model;
            },
            'isSelected': function () {
                return this.$root.isContainerSelected(this.model);
            },
            'isHovered': function () {
                return this.$root.isContainerHovered(this.model);
            },
            'isRoot': function () {
                return this.model.isRoot();
            },
            'isInRoot': function () {
                return this.model.isInRoot();
            },
            'isInContainer': function () {
                return this.model.isInContainer();
            },
            'isInElement': function () {
                return this.model.isInElement();
            },
            'isInArea': function () {
                return this.model.isInArea();
            },
            'isInVariatorVariant': function () {
                return this.model.isInVariatorVariant();
            },
            'isInternal': function () {
                return this.$parent && !this.isInContainer && !this.isRoot;
            },
            'isGrid': function () {
                if (this.model.type === 'absolute' && this.model.properties.grid.show) {
                    return true;
                } else {
                    if (this.model.type !== 'absolute')
                        this.model.properties.grid.show = false;

                    return false;
                }
            },
            'isInGrid': function () {
                return this.isInContainer && this.$parent.model.properties.grid.show;
            },
            'gridType': function () {
                return this.model.properties.grid.type;
            },
            'gridWidth': function () {
                return this.validateGridNumber(this.model.properties.grid.width);
            },
            'gridHeight': function () {
                return this.validateGridNumber(this.model.properties.grid.height);
            },
            'gridStepWidth': function () {
                return this.calculateGridStep(this.gridWidth);
            },
            'gridStepHeight': function () {
                return this.calculateGridStep(this.gridHeight);
            },
            'canAddElementInside': function () {
                return !this.hasElement || this.hasArea || this.hasVariator && this.element.getVariant() !== null;
            },
            'canAddElementOutside': function () {
                return this.isInContainer && this.$parent.type !== 'absolute';
            },
            'canBeCopy': function () {
                return this.isInContainer;
            },
            'canBeCut': function () {
                return this.isInContainer;
            },
            'canBeRefreshed': function () {
                return true;
            },
            'canBeRemoved': function () {
                return this.isInContainer;
            },
            'canBeSelected': function () {
                return true;
            },
            'canChangeOrder': function () {
                return this.isInContainer;
            },
            'canPasteContainerFromBuffer': function () {
                return this.$root.isBufferFilled;
            },
            'canPasteContainerInsideFromBuffer': function () {
                if (!(this.canAddElementInside && this.canPasteContainerFromBuffer))
                    return false;

                return this.model.canPasteContainer(this.$root.buffer);
            },
            'canPasteContainerOutsideFromBuffer': function () {
                if (!(this.canAddElementOutside && this.canPasteContainerFromBuffer))
                    return false;

                return this.$parent.model.canPasteContainer(this.$root.buffer);
            },
            'canOpenElementSettings': function () {
                return this.hasElement && this.$root.canOpenElementSettings(this.element);
            },
            'canToggleDisplay': function () {
                return !this.isRoot;
            },
            'level': function () {
                if (this.hasParentContainer) {
                    return this.parentContainer.level + 1;
                } else {
                    return 0;
                }
            },
            'order': {
                'get': function () {
                    return this.model.order;
                },
                'set': function (value) {
                    this.model.order = value;
                }
            },
            'showPanels': function () {
                return this.isSelected;
            },
            'style': function () {
                var self = this;
                var model = self.model;
                var properties = model.properties;
                var font;
                var values = {};
                var result = {};
                var value;
                var base;

                if (model.isInContainer() && model.parent.getType() === 'absolute')
                    values['position'] = 'absolute';

                api.each(model.getStyleProperties(), function (code, property) {
                    if (
                        property.type === 'float' ||
                        property.type === 'opacity'
                    ) {
                        values[code] = properties[code];
                    } else if (property.type === 'background') {
                        values[code + '-color'] = properties[code].color;
                        values[code + '-repeat'] = properties[code].repeat;

                        if (api.isDeclared(properties[code].image.url))
                            values[code + '-image'] = 'url(\'' + self.$root.replacePathMacros(properties[code].image.url) + '\')';

                        if (properties[code].size.type === 'cover') {
                            values[code + '-size'] = 'cover';
                        } else if (properties[code].size.type === 'contain') {
                            values[code + '-size'] = 'contain';
                        } else if (properties[code].size.type === 'custom' && (
                                api.isDeclared(properties[code].size.width.value) ||
                                api.isDeclared(properties[code].size.height.value)
                            )) {
                            values[code + '-size'] = (api.isDeclared(properties[code].size.width.value) ? self.getStylePropertyMeasuredValue(
                                code,
                                properties[code].size.width.value,
                                properties[code].size.width.measure
                            ) : 'auto') + ' ' + (api.isDeclared(properties[code].size.height.value) ? self.getStylePropertyMeasuredValue(
                                code,
                                properties[code].size.height.value,
                                properties[code].size.height.measure
                            ) : 'auto');
                        }

                        if (
                            api.isDeclared(properties[code].position.top.value) ||
                            api.isDeclared(properties[code].position.left.value)
                        ) {
                            values[code + '-position'] = (api.isDeclared(properties[code].position.left.value) ? self.getStylePropertyMeasuredValue(
                                code,
                                properties[code].position.left.value,
                                properties[code].position.left.measure
                            ) : 0) + ' ' + (api.isDeclared(properties[code].position.top.value) ? self.getStylePropertyMeasuredValue(
                                code,
                                properties[code].position.top.value,
                                properties[code].position.top.measure
                            ) : 0);
                        }
                    } else if (
                        property.type === 'side' ||
                        property.type === 'size'
                    ) {
                        if (property.type === 'side' && self.isInContainer  && self.$parent.type !== 'absolute')
                            return;

                        values[code] = self.getStylePropertyMeasuredValue(
                            code,
                            properties[code].value,
                            properties[code].measure
                        );

                        if (property.type === 'size')
                            api.each(['min', 'max'], function (index, part) {
                                values[part + '-' + code] = self.getStylePropertyMeasuredValue(
                                    code,
                                    properties[code][part].value,
                                    properties[code][part].measure
                                );
                            });
                    } else if (property.type === 'indent') {
                        if (code === 'margin' && properties[code].isAuto) {
                            base = 'auto';
                        } else {
                            base = self.getStylePropertyMeasuredValue(
                                code,
                                properties[code].value,
                                properties[code].measure
                            );
                        }

                        api.each(['top', 'right', 'bottom', 'left'], function (index, part) {
                            if (code === 'margin' && properties[code][part].isAuto) {
                                values[code + '-' + part] = 'auto';
                            } else {
                                values[code + '-' + part] = self.getStylePropertyMeasuredValue(
                                    code,
                                    properties[code][part].value,
                                    properties[code][part].measure
                                );
                            }

                            if (values[code + '-' + part] === null)
                                values[code + '-' + part] = base;
                        });
                    } else if (property.type === 'border') {
                        base = {
                            'width': self.getStylePropertyMeasuredValue(
                                ['border', 'width'],
                                properties[code]['width']['value'],
                                properties[code]['width']['measure']
                            ),
                            'style': properties[code]['style'],
                            'color': properties[code]['color']
                        };

                        api.each(['top', 'right', 'bottom', 'left'], function (index, part) {
                            var value = [];

                            value.push(self.getStylePropertyMeasuredValue(
                                ['border', 'width'],
                                properties[code][part]['width']['value'],
                                properties[code][part]['width']['measure']
                            ));

                            if (value[0] === null)
                                value[0] = base.width;

                            if (value[0] === null)
                                value.shift();

                            if (properties[code][part]['style']['value'] !== null) {
                                value.push(properties[code][part]['style']['value']);
                            } else if (base.style !== null) {
                                value.push(base.style);
                            }

                            if (properties[code][part]['color']['value'] !== null) {
                                value.push(properties[code][part]['color']['value']);
                            } else if (base.color !== null) {
                                value.push(base.color);
                            }

                            if (value.length > 0)
                                values[code + '-' + part] = value.join(' ');
                        });

                        base = null;

                        if (properties[code]['radius']['value'] !== 0)
                            base = self.getStylePropertyMeasuredValue(
                                ['border', 'radius'],
                                properties[code]['radius']['value'],
                                properties[code]['radius']['measure']
                            );

                        value = {};

                        api.each(['top', 'right', 'bottom', 'left'], function (index, part) {
                            value[part] = null;

                            if (properties[code][part]['radius']['value'] !== 0)
                                value[part] = self.getStylePropertyMeasuredValue(
                                    ['border', 'radius'],
                                    properties[code][part]['radius']['value'],
                                    properties[code][part]['radius']['measure']
                                );

                            if (value[part] === null)
                                value[part] = base;
                        });

                        if (value.top !== null || value.right !== null || value.bottom !== null || value.left !== null) {
                            api.each(value, function (index, part) {
                                if (part === null)
                                    value[index] = 0
                            });

                            values[code + '-radius'] = value.top + ' ' + value.right + ' ' + value.bottom + ' ' + value.left;
                        }
                    } else if (property.type === 'text') {
                        if (properties[code]['font'] !== null) {
                            font = self.$root.useFont(properties[code]['font']);

                            if (font !== null)
                                values['font-family'] = '"' + font.family + '", sans-serif';
                        }

                        values['font-size'] = self.getStylePropertyMeasuredValue(
                            ['text', 'size'],
                            properties[code]['size']['value'],
                            properties[code]['size']['measure']
                        );

                        values['color'] = properties[code]['color'];
                        values['text-transform'] = properties[code]['uppercase'] ? 'uppercase' : null;
                        values['letter-spacing'] = self.getStylePropertyMeasuredValue(
                            ['text', 'letterSpacing'],
                            properties[code]['letterSpacing']['value'],
                            properties[code]['letterSpacing']['measure']
                        );

                        values['line-height'] = self.getStylePropertyMeasuredValue(
                            ['text', 'lineHeight'],
                            properties[code]['lineHeight']['value'],
                            properties[code]['lineHeight']['measure']
                        );
                    }
                });

                api.each(values, function (key, value) {
                    if (value !== null)
                        result[key] = value;
                });

                return result;
            },
            'styleBackgroundColor': {
                'get': function () {
                    if (!api.isDeclared(this.model.properties.background.color))
                        return '#000000';

                    return this.model.properties.background.color;
                },
                'set': function (value) {
                    if (api.isEmpty(value))
                        return null;

                    this.model.properties.background.color = value;
                }
            },
            'type': function () {
                return this.model.getType();
            }
        },
        'created': function () {
            window.addEventListener('resize', this.resize);
        },
        'mounted': function () {
            var self = this;

            if (self.model.properties.text.font !== null)
                self.$root.registerFont(self.model.properties.text.font);

            if (!self.isInternal) {
                var interaction = interact(self.$el);

                interaction.on('mouseover', function (event) {
                    self.$root.hoverContainer(self.model);

                    event.preventDefault();
                    event.stopPropagation();
                });

                interaction.on('mouseleave', function (event) {
                    if (self.isRoot)
                        self.$root.removeHovering();

                    event.preventDefault();
                    event.stopPropagation();
                });

                interaction.on('click', function (event) {
                    var parent = event.target;

                    while (parent !== self.$el && parent !== null) {
                        if (parent === self.$refs.border || parent === self.$refs.content || parent === self.$refs.overlay) {
                            self.select();
                            break;
                        }

                        parent = parent.parentNode;
                    }

                    event.stopPropagation();
                });
            }

            self.node.width = self.$el.clientWidth;
        },
        'updated': function () {
            this.node.width = this.$el.clientWidth;
        },
        'destroyed': function () {
            window.removeEventListener('resize', this.resize);
        },
        'data': function () {
            return {
                'isBusy': false,
                'modelPropertiesHandling': false,
                'node': {
                    'width': null
                }
            };
        },
        'methods': {
            'beginBusy': function () {
                this.isBusy = true;
            },
            'endBusy': function () {
                this.isBusy = false;
            },
            'getStyleProperties': function () {
                return this.model.getStyleProperties();
            },
            'getStyleProperty': function (name) {
                return this.model.getStyleProperty(name);
            },
            'getStylePropertyMeasuredValue': function (name, value, measure) {
                if (!api.isDeclared(value))
                    return null;

                var property;
                var meta;

                if (api.isArray(name)) {
                    name = api.array.rebuild(name);
                    property = this.getStyleProperty(name.shift());

                    if (name.length > 0)
                        meta = api.object.getValue(property, name);
                } else {
                    property = this.getStyleProperty(name);
                    meta = property;
                }

                if (!api.isObject(meta) && !api.isArray(meta.measures))
                    return null;

                if (meta.measures.indexOf(measure) < 0)
                    measure = meta.measures[0];

                return api.toString(value) + api.toString(measure);
            },
            'addElementBefore': function () {
                if (this.canAddElementOutside)
                    return this.$root.addElement(this.$parent.model, this.order - 0.5);

                return Promise.reject();
            },
            'addElementInside': function () {
                if (this.canAddElementInside)
                    return this.$root.addElement(this.model, -0.5);

                return Promise.reject();
            },
            'addElementAfter': function () {
                if (this.canAddElementOutside)
                    return this.$root.addElement(this.$parent.model, this.order + 0.5);

                return Promise.reject();
            },
            'copy': function () {
                if (this.canBeCopy)
                    this.$root.storeContainerInBuffer(this.model, false);
            },
            'cut': function () {
                if (this.canBeCut)
                    this.$root.storeContainerInBuffer(this.model, true);
            },
            'pasteContainerBeforeFromBuffer': function () {
                if (this.canPasteContainerOutsideFromBuffer)
                    return this.$root.pasteContainerFromBuffer(this.$parent.model, this.order - 0.5);

                return false;
            },
            'pasteContainerInsideFromBuffer': function () {
                if (this.canPasteContainerInsideFromBuffer)
                    return this.$root.pasteContainerFromBuffer(this.model, -0.5);

                return false;
            },
            'pasteContainerAfterFromBuffer': function () {
                if (this.canPasteContainerOutsideFromBuffer)
                    return this.$root.pasteContainerFromBuffer(this.$parent.model, this.order + 0.5);

                return false;
            },
            'refresh': function () {
                var handler;

                if (this.canBeRefreshed) {
                    handler = function (components) {
                        api.each(components, function (index, component) {
                            if (
                                component.$options.name === 'v-editor-component' ||
                                component.$options.name === 'v-editor-block' ||
                                component.$options.name === 'v-editor-widget'
                            ) component.refresh();

                            handler(component.$children);
                        });
                    };

                    handler(this.$children);
                }
            },
            'remove': function () {
                if (this.canBeRemoved)
                    this.$root.removeContainer(this.model);
            },
            'orderUp': function () {
                if (this.canChangeOrder) {
                    this.order = this.order - 1.5;
                    this.model.parent.updateContainersOrder();
                }
            },
            'orderDown': function () {
                if (this.canChangeOrder) {
                    this.order = this.order + 1.5;
                    this.model.parent.updateContainersOrder();
                }
            },
            'openSettings': function () {
                this.$root.openElementSettings(this.model);
            },
            'openElementSettings': function () {
                if (this.canOpenElementSettings)
                    this.$root.openElementSettings(this.model.element);
            },
            'toggleDisplay': function () {
                if (this.canToggleDisplay)
                    this.model.display = !this.model.display;
            },
            'select': function () {
                if (this.canBeSelected)
                    this.$root.selectContainer(this.model);
            },
            'validateGridNumber': function (number) {
                number = api.toInteger(number);

                if (api.isNaN(number))
                    number = 0;

                return number;
            },
            'calculateGridStep': function (value) {
                if (this.gridType !== 'fixed') {
                    if (value < 2)
                        return 100;

                    return 100 / (value);
                } else {
                    return value;
                }
            },
            'roundGridCoordinate': function (coordinate, step) {
                if (!api.isNumber(step))
                    return coordinate;

                coordinate = api.toFloat(coordinate);

                if (api.isNaN(coordinate) || !api.isNumber(coordinate))
                    return null;

                var correction;

                correction = coordinate % step;

                if (correction !== 0) {
                    if (correction < step / 2) {
                        coordinate = coordinate - correction;
                    } else {
                        coordinate = coordinate + (step - correction);
                    }
                }

                return coordinate;
            },
            'resize': function () {
                this.node.width = this.$el.clientWidth;
            }
        },
        'props': {
            'model': {
                'type': models.Container,
                'required': true
            }
        },
        'watch': {
            'model.properties': {
                'handler': function (value) {
                    if (this.modelPropertiesHandling)
                        return;

                    this.modelPropertiesHandling = true;

                    api.object.scan(value, function (event) {
                        if (api.isString(event.value) && event.value.length === 0)
                            event.value = null;
                    });

                    this.modelPropertiesHandling = false;
                },
                'deep': true
            }/*,
            'model.properties.top.value': function (value) {
                if (
                    value !== null &&
                    value.length > 0 &&
                    this.model.properties.bottom.value !== null
                ) {
                    this.model.properties.height.value = null;
                } else {
                    if (this.isInGrid) {
                        if (this.$parent.gridType !== 'fixed') {
                            this.model.properties.top.measure = '%';
                        } else {
                            this.model.properties.top.measure = 'px';
                        }

                        this.model.properties.top.value = this.roundGridCoordinate(this.model.properties.top.value, this.$parent.gridStepHeight);
                    }
                }
            },
            'model.properties.right.value': function (value) {
                if (
                    value !== null &&
                    value.length > 0 &&
                    this.model.properties.left.value !== null
                ) {
                    this.model.properties.width.value = null;
                } else {
                    if (this.isInGrid) {
                        if (this.$parent.gridType !== 'fixed') {
                            this.model.properties.right.measure = '%';
                        } else {
                            this.model.properties.right.measure = 'px';
                        }

                        this.model.properties.right.value = this.roundGridCoordinate(this.model.properties.right.value, this.$parent.gridStepWidth);
                    }
                }
            },
            'model.properties.bottom.value': function (value) {
                if (
                    value !== null &&
                    value.length > 0 &&
                    this.model.properties.top.value !== null
                ) {
                    this.model.properties.height.value = null;
                } else {
                    if (this.isInGrid) {
                        if (this.$parent.gridType !== 'fixed') {
                            this.model.properties.bottom.measure = '%';
                        } else {
                            this.model.properties.bottom.measure = 'px';
                        }

                        this.model.properties.bottom.value = this.roundGridCoordinate(this.model.properties.bottom.value, this.$parent.gridStepHeight);
                    }
                }
            },
            'model.properties.left.value': function (value) {
                if (
                    value !== null &&
                    value.length > 0 &&
                    this.model.properties.right.value !== null
                ) {
                    this.model.properties.width.value = null;
                } else {
                    if (this.isInGrid) {
                        if (this.$parent.gridType !== 'fixed') {
                            this.model.properties.left.measure = '%';
                        } else {
                            this.model.properties.left.measure = 'px';
                        }

                        this.model.properties.left.value = this.roundGridCoordinate(this.model.properties.left.value, this.$parent.gridStepWidth);
                    }
                }
            },
            'model.properties.width.value': function (value) {
                if (
                    value != null &&
                    value.length > 0 &&
                    this.model.properties.left.value !== null &&
                    this.model.properties.right.value !== null
                ) {
                    this.model.properties.right.value = null;
                } else {
                    if (this.isInGrid) {
                        if (this.$parent.gridType !== 'fixed') {
                            this.model.properties.width.measure = '%';
                        } else {
                            this.model.properties.width.measure = 'px';
                        }

                        this.model.properties.width.value = this.roundGridCoordinate(this.model.properties.width.value, this.$parent.gridStepWidth);
                    }
                }
            },
            'model.properties.height.value': function (value) {
                if (
                    value !== null &&
                    value.length > 0 &&
                    this.model.properties.top.value !== null &&
                    this.model.properties.bottom.value !== null
                ) {
                    this.model.properties.bottom.value = null;
                } else {
                    if (this.isInGrid) {
                        if (this.$parent.gridType !== 'fixed') {
                            this.model.properties.height.measure = '%';
                        } else {
                            this.model.properties.height.measure = 'px';
                        }

                        this.model.properties.height.value = this.roundGridCoordinate(this.model.properties.height.value, this.$parent.gridStepHeight);
                    }
                }
            }*/
        }
    }
})();
