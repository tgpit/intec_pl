(function () {
    return {
        'computed': {
            'hasHovering': function () {
                return this.hovering instanceof models.Container;
            },
            'hasSelection': function () {
                return this.selection instanceof models.Container;
            }
        },
        'data': {
            'hovering': null,
            'selection': null
        },
        'methods': {
            'addElement': function (parent, order) {
                var self = this;
                var tab = self.interface.menu.getTab('widgets');
                var handler;

                if (!(parent instanceof models.Container))
                    return Promise.reject();

                return new Promise(function (resolve, reject) {
                    if (tab) {
                        handler = function (preset) {
                            tab.close();

                            if (preset instanceof models.Preset) {
                                preset.createElement().then(function (model) {
                                    var container;

                                    if (!(model instanceof models.Container)) {
                                        container = new models.Container();
                                        model.parent = container;
                                    } else {
                                        container = model;
                                    }

                                    if (!parent.pasteContainer(container, order))
                                        reject();

                                    resolve(container);
                                }, reject);
                            } else {
                                reject();
                            }
                        };

                        tab.$once('selected', handler);
                        tab.$once('close', function () {
                            self.presetsGroup = null;

                            tab.$off('selected', handler);
                        });

                        tab.open();
                    }
                });
            },
            'canOpenElementSettings': function (element) {
                return element instanceof models.Container ||
                    element instanceof models.elements.Block ||
                    element instanceof models.elements.Component ||
                    element instanceof models.elements.Variator ||
                    element instanceof models.elements.Widget;
            },
            'hoverContainer': function (container, toggle) {
                if (toggle && this.hovering === container) {
                    this.removeHovering();
                    return;
                }

                if (container instanceof models.Container) {
                    this.hovering = container;
                } else {
                    this.hovering = null;
                }
            },
            'isContainerHovered': function (container) {
                return this.hovering === container;
            },
            'isContainerSelected': function (container) {
                return this.selection === container;
            },
            'openElementSettings': function (element) {
                if (this.interface.menu && this.canOpenElementSettings(element)) {
                    if (element instanceof models.Container) {
                        this.selectContainer(element);
                        this.interface.menu.openTab('container');
                    } else if (element instanceof models.elements.Block) {
                        this.selectContainer(element.parent);
                        this.interface.menu.openTab('block');
                    } else if (element instanceof models.elements.Component) {
                        this.interface.dialogs.componentSettings.open(element);
                    } else if (element instanceof models.elements.Variator) {
                        this.selectContainer(element.parent);
                        this.interface.menu.openTab('variator');
                    } else if (element instanceof models.elements.Widget) {
                        this.selectContainer(element.parent);
                        this.interface.menu.openTab('widget');
                    }
                }
            },
            'orderDownContainer': function (container) {
                return container.orderDown();
            },
            'orderDownSelectedContainer': function () {
                if (this.hasSelection)
                    return this.orderDownContainer(this.selection);

                return false;
            },
            'orderUpContainer': function (container) {
                return container.orderUp();
            },
            'orderUpSelectedContainer': function () {
                if (this.hasSelection)
                    return this.orderUpContainer(this.selection);

                return false;
            },
            'pasteContainerFromBuffer': function (parent, order) {
                var container;
                var clear;

                if (!(parent instanceof models.Container))
                    return false;

                container = this.restoreFromBuffer();
                clear = container.parent !== null;

                if (parent.pasteContainer(container, order)) {
                    if (clear)
                        this.clearBuffer();

                    return true;
                }

                return false;
            },
            'pasteContainerToSelectionFromBuffer': function (order) {
                if (!api.isDeclared(order))
                    order = -0.5;

                if (this.hasSelection)
                    return this.pasteContainerFromBuffer(this.selection, order);

                return false;
            },
            'removeContainer': function (container) {
                if (container.isInContainer()) {
                    container.parent = null;

                    if (this.isContainerSelected(container))
                        this.removeSelection();

                    return true;
                }

                return false;
            },
            'removeHovering': function () {
                this.hovering = null;
            },
            'removeSelectedContainer': function () {
                if (this.hasSelection && this.removeContainer(this.selection))
                    return true;

                return false;
            },
            'removeSelection': function () {
                if (this.hasSelection) {
                    this.selection = null;
                    return true;
                }

                return false;
            },
            'selectContainer': function (container, toggle) {
                if (toggle && this.isContainerSelected(container)) {
                    this.removeSelection();
                    return;
                }

                if (container instanceof models.Container) {
                    this.selection = container;
                } else {
                    this.removeSelection();
                }
            },
            'selectNextContainer': function () {
                var containers;
                var index;

                if (this.hasSelection && !this.selection.isRoot()) {
                    containers = this.selection.parent.getSortedContainers();
                    index = containers.indexOf(this.selection);

                    if (index >= 0 && index < containers.length - 1) {
                        this.selection = containers[++index];
                        return true;
                    }
                }

                return false;
            },
            'selectNextLevelContainer': function () {
                var containers;

                if (this.hasSelection) {
                    containers = this.selection.getChildContainers();

                    if (containers.length > 0) {
                        this.selectContainer(containers[0]);
                        return true;
                    }
                }

                return false;
            },
            'selectPreviousContainer': function () {
                var containers;
                var index;

                if (this.hasSelection && !this.selection.isRoot()) {
                    containers = this.selection.parent.getSortedContainers();
                    index = containers.indexOf(this.selection);

                    if (index > 0) {
                        this.selection = containers[--index];
                        return true;
                    }
                }

                return false;
            },
            'selectPreviousLevelContainer': function () {
                if (this.hasSelection && this.selection.isInContainer()) {
                    if (this.selection.parent.isInElement()) {
                        this.selectContainer(this.selection.parent.getParentContainer());
                    } else {
                        this.selectContainer(this.selection.parent);
                    }

                    return true;
                }

                return false;
            },
            'storeContainerInBuffer': function (container, cut) {
                if (cut) {
                    if (container.isInContainer()) {
                        this.storeInBuffer(container);
                        return true;
                    }
                } else {
                    if (container.isRoot() || container.isInContainer()) {
                        this.storeInBuffer(container.clone());
                        return true;
                    }
                }

                return false;
            },
            'storeSelectedContainerInBuffer': function (cut) {
                if (this.hasSelection) {
                    this.storeContainerInBuffer(this.selection, cut);
                    return true;
                }

                return false;
            }
        }
    }
})();
