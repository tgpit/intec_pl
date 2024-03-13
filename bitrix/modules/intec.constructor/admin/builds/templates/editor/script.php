<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\JavaScript;
use intec\core\web\assets\vue\Application;

/**
 * @var Application $application
 * @var array $data
 */

$application->useModule('actions');
$application->useModule('areas');
$application->useModule('blocks');
$application->useModule('buffer');
$application->useModule('containers');
$application->useModule('control');
$application->useModule('fonts');
$application->useModule('interaction');
$application->useModule('layouts');
$application->useModule('presets');
$application->useModule('sites');
$application->useModule('widgets');

?>
<script type="text/javascript">
    (function (api) {
        VueScript2.install(Vue);

        var application;
        var models = {
            'container': {
                'conditions': {}
            },
            'elements': {},
            'layout': {},
            'preset': {},
            'widget': {}
        };

        var classes = {
            'gallery': {}
        };

        var components = {};
        var helpers;
        var data = <?= JavaScript::toObject($data) ?>;

        <?php include(__DIR__.'/script/helpers.js') ?>
        <?php include(__DIR__.'/script/classes/gallery.js') ?>
        <?php include(__DIR__.'/script/classes/gallery/item.js') ?>
        <?php include(__DIR__.'/script/classes/localization.js') ?>
        <?php include(__DIR__.'/script/classes/links.js') ?>
        <?php include(__DIR__.'/script/classes/resources.js') ?>

        (function () {
            var uid = 0;

            <?php include(__DIR__.'/script/models/area.js') ?>
            <?php include(__DIR__.'/script/models/font.js') ?>
            <?php include(__DIR__.'/script/models/layout.js') ?>
            <?php include(__DIR__.'/script/models/layout/zone.js') ?>
            <?php include(__DIR__.'/script/models/site.js') ?>
            <?php include(__DIR__.'/script/models/template.js') ?>
            <?php include(__DIR__.'/script/models/preset.js') ?>
            <?php include(__DIR__.'/script/models/preset/group.js') ?>
            <?php include(__DIR__.'/script/models/widget.js') ?>
            <?php include(__DIR__.'/script/models/widget/template.js') ?>
            <?php include(__DIR__.'/script/models/container.js') ?>
            <?php include(__DIR__.'/script/models/container/condition.js') ?>
            <?php include(__DIR__.'/script/models/container/conditions/condition.js') ?>
            <?php include(__DIR__.'/script/models/container/conditions/group.js') ?>
            <?php include(__DIR__.'/script/models/element.js') ?>
            <?php include(__DIR__.'/script/models/elements/area.js') ?>
            <?php include(__DIR__.'/script/models/elements/block.js') ?>
            <?php include(__DIR__.'/script/models/elements/component.js') ?>
            <?php include(__DIR__.'/script/models/elements/variator.js') ?>
            <?php include(__DIR__.'/script/models/elements/variant.js') ?>
            <?php include(__DIR__.'/script/models/elements/widget.js') ?>

            models.elements.Block.prototype.clone = function () {
                var self = this;
                var id = self.id;

                self.id = null;
                self.name = null;
                self.content = null;

                application.requestBlockCloning(id).then(function (response) {
                    self.id = response.id;
                    self.name = response.name;
                }, function () {
                    self.parent = null;
                });
            };

            models.Container.prototype.clone = (function (method) {
                return function () {
                    var result = method.apply(this, arguments);

                    if (result.hasBlock() && !result.isInAreaRoot()) {
                        result.element.clone();
                    } else {
                        result.eachContainer(function (index, container) {
                            if (container.hasBlock() && !container.isInAreaRoot())
                                container.element.clone();
                        });
                    }

                    if (result.hasArea())
                        result.convertToSimple();

                    result.eachContainer(function (index, container) {
                        if (container.hasArea())
                            container.convertToSimple();
                    });

                    return result;
                }
            })(models.Container.prototype.clone);
        })();

        application = <?= $application->build() ?>;
        application.extend({
            'el': '#editor',
            'components': components,
            'computed': {
                'pathMacros': function () {
                    return {
                        'TEMPLATE': this.path
                    };
                },
                'backgroundImageUrl': function () {
                    return this.replacePathMacros(this.selection.properties.background.image.url);
                },
                'hasSelectionWidget': function () {
                    return this.selectionWidget !== null;
                },
                'isBusy': function () {
                    return !this.isLoaded || this.isRefreshing || this.isLayoutChanging;
                },
                'isSelectionWidgetResourceLoaded': function () {
                    if (this.hasSelectionWidget)
                        return this.isWidgetResourcesLoaded(this.selection.element.code, this.selection.element.template);

                    return false;
                },
                'selectionWidget': function () {
                    if (this.hasSelection && this.selection.hasWidget())
                        return this.useWidget(this.selection.element.code, this.selection.element.template);

                    return null;
                }
            },
            'created': function () {
                var self = this;

                self.siteId = self.template.settings.siteId;
                self.$api = api;
                self.$localization = this.$options.localization;
                self.$links = this.$options.links;
                self.$gallery = new classes.Gallery(this);
                self.$resources = new classes.Resources(document);
                self.refresh().then(function () {
                    self.isLoaded = true;
                });
            },
            'mounted': function () {
                var self = this;

                self.interface.menu = self.$refs.menu;
                self.interface.dialogs.areaSelect = self.$refs.dialogsAreaSelect;
                self.interface.dialogs.blockConvert = self.$refs.dialogsBlockConvert;
                self.interface.dialogs.componentList = self.$refs.dialogsComponentList;
                self.interface.dialogs.componentSettings = self.$refs.dialogsComponentSettings;
                self.interface.dialogs.gallery = self.$refs.dialogsGallery;
                self.interface.dialogs.conditions = self.$refs.dialogsContainerConditions;
                self.interface.dialogs.containerPaste = self.$refs.dialogsContainerPaste;
                self.interface.dialogs.containerStructure = self.$refs.dialogsContainerStructure;
                self.interface.dialogs.containerScript = self.$refs.dialogsContainerScript;
            },
            'data': {
                'id': data.id,
                'code': data.code,
                'path': data.path,
                'name': data.name,
                'interface': {
                    'menu': null,
                    'dialogs': {
                        'areaSelect': null,
                        'blockConvert': null,
                        'componentList': null,
                        'componentSettings': null,
                        'gallery': null,
                        'conditions': null,
                        'containerPaste': null,
                        'containerScript': null,
                        'containerStructure': null
                    }
                },
                'settings': data.settings,
                'template': new models.Template(data.template),
                'isLayoutChanging': false,
                'isLoaded': false,
                'isRefreshing': false,
                'isSaving': false
            },
            'methods': {
                'refresh': function () {
                    var self = this;
                    var responses = [];

                    if (self.isRefreshing)
                        return;

                    self.isRefreshing = true;

                    return self.refreshSites().then(function (response) {
                        responses.push(response);

                        return Promise.all([
                            self.refreshAreas(),
                            self.refreshContainers(),
                            self.refreshFonts(),
                            self.refreshPresets(),
                            self.refreshLayouts(),
                            self.refreshWidgets()
                        ]).then(function (response) {
                            api.each(response, function (index, response) {
                                responses.push(response);
                            });

                            self.isRefreshing = false;

                            return responses;
                        }, function (reason) {
                            self.isRefreshing = false;

                            console.error('Error occurred during refresh');
                            console.log(reason);

                            return reason;
                        });
                    }, function (reason) {
                        self.isRefreshing = false;

                        console.error('Error occurred during refresh');
                        console.log(reason);

                        return reason;
                    });
                },
                'refreshElements': function () {
                    if (this.$refs.layout) {
                        api.each(this.$refs.layout.$children, function (index, component) {
                            if (component.$options.name === 'v-editor-layout-zone' && component.$refs.container)
                                component.$refs.container.refresh();
                        });
                    }
                },
                'replaceMacros': function (value, macros) {
                    var result = value;

                    api.each(macros, function (key, value) {
                        key = '#' + key + '#';

                        while (result.indexOf(key) !== -1)
                            result = result.replace(key, value);
                    });

                    return result;
                },
                'replacePathMacros': function (value) {
                    return this.replaceMacros(value, this.pathMacros);
                },
                'save': function () {
                    var self = this;
                    var data = {};

                    self.isSaving = true;
                    data.containers = [];
                    data.settings = api.extend({}, this.template.settings);

                    api.each(this.containers, function (index, container) {
                        data.containers.push(container.save(true));
                    });

                    return this.request('application.save', {
                        'data': JSON.stringify(data)
                    }).then(function (response) {
                        self.isSaving = false;

                        return response;
                    }, function (reason) {
                        self.isSaving = false;

                        return reason;
                    });
                },
                'gallerySelectItemCallback': function (item) {
                    this.selection.properties.background.image.url = item.value;
                },
                'propertiesClearBackgroundImage': function () {
                    this.selection.properties.background.image.url = null;
                }
            },
            'watch': {
                'site': function (site) {
                    var self = this;

                    if (site) {
                        self.template.settings.siteId = site.id;
                        self.refreshElements();
                    } else {
                        self.template.settings.siteId = null;
                    }
                }
            },
            'localization': new classes.Localization(data.localization),
            'links': new classes.Links(data.links),
            'vuetify': new Vuetify()
        });

        (function () {
            var variant;

            application.extend({
                'methods': {
                    'interfaceMenuTabsBlockConvert': function () {
                        this.interface.dialogs.blockConvert.open(this.selection.element);
                    },
                    'interfaceMenuTabsSettingsLayoutSet': function (layout) {
                        var self = this;

                        self.isLayoutChanging = true;
                        self.interface.menu.closeTab();
                        self.setLayout(layout.code).then(function () {
                            document.location.reload();
                        }, function () {
                            self.isLayoutChanging = false;
                        });
                    },
                    'interfaceMenuTabsVariatorVariantsAdd': function () {
                        var container = new models.Container({
                            'display': true
                        });

                        var variant = new models.elements.Variant();

                        container.parent = variant;
                        variant.name = this.$localization.getMessage('menu.items.variator.groups.settings.field.variants.new');
                        variant.parent = this.selection.element;
                    },
                    'interfaceMenuTabsVariatorVariantsRemove': function (variant) {
                        if (this.selection.element.getVariant() === variant)
                            this.selection.element.setVariant(null);

                        variant.parent = null;
                    },
                    'interfaceMenuTabsVariatorVariantsDragStart': function () {
                        variant = this.selection.element.getVariant();
                    },
                    'interfaceMenuTabsVariatorVariantsDragEnd': function () {
                        this.selection.element.setVariant(variant);
                        variant = undefined;
                    },
                    'interfaceMenuTabsWidgetsGroupToggle': function (group) {
                        if (this.presetsGroup === group)
                            this.presetsGroup = null;
                        else
                            this.presetsGroup = group;
                    }
                }
            });
        })();

        window.addEventListener('DOMContentLoaded', function () {
            application = application.compose();
            application = new Vue(application);

            window.application = application;
        });
    })(intec)
</script>