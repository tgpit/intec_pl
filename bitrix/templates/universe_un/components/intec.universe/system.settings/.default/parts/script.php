<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\FileHelper;
use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var _ = this.getLibrary('_');
        var $ = this.getLibrary('$');

        var root = data.nodes;
        var blind = $('[data-role="blind"]', root);
        var overlay = $('[data-role="overlay"]', root);
        var close = $('[data-role="close"]', root);
        var tabs = $('[data-role="tabs"]', root);
        var containers = $('[data-role="containers"]', root);
        var properties = $('[data-role="properties"]', root);
        var inputs = $('[data-role="property.input"]', properties);
        var scrollbar = $('[data-role="scrollbar"]', root);
        var engine = {};
        var views = {};

        engine.initialized = false;
        engine.sending = false;
        engine.tab = root.find('input[name="section"]').val();

        if (engine.tab.length === 0)
            engine.tab = null;

        engine.settings = {
            'activeAttribute': 'data-active',
            'codeAttribute': 'data-code',
            'expandedAttribute': 'data-expanded',
            'animationTime': 500,
            'overlayOpacity': 0.5
        };

        engine.initialize = function () {
            if (engine.initialized)
                return;

            engine.initialized = true;

            root.attr('data-initialized', 'true');
            root.attr('data-expanded', 'false');
            root.css('display', 'block');
            blind.css('left', '-' + blind.width() + 'px');
            root.css('display', '');
            overlay.css('visibility', 'hidden');

            if (engine.tab !== null) {
                var tab = engine.tab;

                engine.tab = null;
                engine.open(tab, false);
            }

            <?php if (FileHelper::isFile(__DIR__.'/script.custom.php')) include(__DIR__.'/script.custom.php') ?>
        };

        engine.getPanelItems = function () {
            return $('[data-role="panel"]', root)
                .find('[data-role="panel.items"] [data-role="panel.item"]')
                .filter(function (index, element) {
                    return element.getAttribute(engine.settings.codeAttribute) != null;
                });
        };

        engine.getTabs = function () {
            return tabs
                .find('[data-role="tab"], [data-role="tab"] [data-role="tab.categories"] [data-role="tab.category"]')
                .filter(function (index, element) {
                    return element.getAttribute(engine.settings.codeAttribute) != null;
                });
        };

        engine.getTab = function (name) {
            var result = null;
            var parts = name.split('.');

            engine.getTabs().each(function () {
                var code = this.getAttribute(engine.settings.codeAttribute);

                if (parts.length > 1) {
                    if (code === name) {
                        result = this;
                        return false;
                    }
                } else {
                    if (code === name) {
                        result = this;
                    } else if (code.split('.')[0] === name) {
                        result = this;
                        return false;
                    }
                }
            });

            return result;
        };

        engine.getContainersGroups = function () {
            return containers
                .children('[data-role="containers.group"]')
                .filter(function (index, element) {
                    return element.getAttribute(engine.settings.codeAttribute) != null;
                });
        };

        engine.getContainers = function () {
            return engine
                .getContainersGroups()
                .children('[data-role="container"]')
                .filter(function (index, element) {
                    return element.getAttribute(engine.settings.codeAttribute) != null;
                });
        };

        engine.getContainer = function (name) {
            var result = null;

            engine.getContainers().each(function () {
                var code = this.getAttribute(engine.settings.codeAttribute);

                if (code === name) {
                    result = this;
                    return false;
                }
            });

            return result;
        };

        engine.getCategories = function () {
            var result = [];

            engine.getContainers().each(function () {
                var code = this.getAttribute(engine.settings.codeAttribute);

                if (code != null)
                    result.push(code);
            });

            return result;
        };

        engine.getProperties = function () {
            return properties
                .find('[data-role="property"]');
        };

        engine.open = function (name, animate) {
            if (_.isNil(animate))
                animate = true;

            var isOpened = engine.isOpened();
            var recent = root.find('input[name="section"]');
            var tab;

            if (!isOpened && recent.val() !== '')
                tab = engine.getTab(recent.val());
            else
                tab = engine.getTab(name);

            if (tab === null)
                return;

            var code = tab.getAttribute(engine.settings.codeAttribute);
            var container = engine.getContainer(code);
            var parent = $(tab).closest('[data-role="tab"]', root);

            if (container === null)
                return;

            var group = $(container).closest('[data-role="containers.group"]', root);

            engine.tab = code;
            engine.getContainersGroups().attr(engine.settings.activeAttribute, 'false');
            engine.getContainers().attr(engine.settings.activeAttribute, 'false');
            engine.getTabs().attr(engine.settings.activeAttribute, 'false');
            recent.val(code);

            tab.setAttribute(engine.settings.activeAttribute, 'true');
            container.setAttribute(engine.settings.activeAttribute, 'true');

            if (parent.length !== 0)
                parent.attr(engine.settings.activeAttribute, 'true');

            if (group.length !== 0)
                group.attr(engine.settings.activeAttribute, 'true');

            if (!isOpened) {
                overlay.css('visibility', 'visible');

                if (animate) {
                    blind.stop().animate({
                        'left': 0
                    }, engine.settings.animationTime);

                    overlay.stop().animate({
                        'opacity': engine.settings.overlayOpacity
                    }, engine.settings.animationTime);
                } else {
                    blind.css('left', 0);
                    overlay.css('opacity', engine.settings.overlayOpacity);
                }

                root.attr(engine.settings.expandedAttribute, 'true')
            }
        };

        engine.isOpened = function () {
            return engine.tab != null;
        };

        engine.close = function (animate) {
            if (!engine.isOpened())
                return;

            if (_.isNil(animate))
                animate = true;

            engine.tab = null;

            if (animate) {
                blind.stop().animate({
                    'left': '-' + blind.width() + 'px'
                }, engine.settings.animationTime, function () {
                    engine.getContainers().attr(engine.settings.activeAttribute, 'false');
                    engine.getContainers().attr(engine.settings.activeAttribute, 'false');
                    engine.getTabs().attr(engine.settings.activeAttribute, 'false');
                    root.attr(engine.settings.expandedAttribute, 'false');
                });

                overlay.stop().animate({
                    'opacity': 0
                }, engine.settings.animationTime, function () {
                    overlay.css('visibility', 'hidden');
                });
            } else {
                blind.css('left', '-' + blind.width() + 'px');
                engine.getContainers().attr(engine.settings.activeAttribute, 'false');
                engine.getContainers().attr(engine.settings.activeAttribute, 'false');
                engine.getTabs().attr(engine.settings.activeAttribute, 'false');
                root.attr(engine.settings.expandedAttribute, 'false');
            }
        };

        (function () {
            var properties = engine.getProperties();

            views.blocks = properties.filter('[data-view="blocks"]');
            views.boolean = properties.filter('[data-view="boolean"]');
            views.color = properties.filter('[data-view="color"]');
        })();

        views.blocks.each(function () {
            var view = $(this);
            var blocks = $('[data-role="property.blocks"] [data-role="property.block"]', view);

            blocks.each(function () {
                var block = $(this);
                var inputs = $('[data-role="property.input"]', block);
                var templates = $('[data-role="property.block.templates"] [data-role="property.block.template"]', block);

                inputs.active = inputs.filter('[data-type="active"]');
                inputs.template = inputs.filter('[data-type="template"]');
                templates.container = $('[data-role="property.block.templates"]', block);

                if (!inputs.active.prop('checked'))
                    templates.container.hide();

                inputs.active.on('change', function () {
                    templates.container.stop().slideToggle(600);
                });
            })
        });

        views.color.each(function () {
            var view = $(this);
            var input = $('[data-role="property.input"]', view);
            var values = $('[data-role="property.values"] [data-role="property.value"]', view);

            values.custom = values.filter('[data-value="custom"]');
            values.custom.background = values.custom.find('[data-role="property.value.background"]');
            values.set = function (node, value) {
                values.custom.background.css('background', value);
                values.attr('data-active', 'false');
                node.attr('data-active', 'true');
                input.val(value).trigger('change');
            };

            values.on('click', function () {
                var node = $(this);
                var value = node.data('value');

                if (value != null && value !== 'custom')
                    values.set(node, value);
            });

            values.custom.ColorPicker({
                'color': input.val(),
                'onSubmit': function (hsb, hex, rgb) {
                    var node = values.custom;
                    var value = '#' + hex;

                    values.set(node, value);
                }
            });
        });

        engine.initialize();
        scrollbar.scrollbar();

        $(window).on('click', function (event) {
            var switches = engine.getPanelItems().add(engine.getTabs());
            var target = $(event.target);
            var triggered = false;

            switches.each(function () {
                if (target.closest(this).length > 0) {
                    engine.open(this.getAttribute(engine.settings.codeAttribute));
                    triggered = true;
                }
            });

            if (!triggered && (
                target.closest(overlay, root).length !== 0 ||
                target.closest(close, root).length !== 0)
            ) {
                engine.close();
                triggered = true;
            }

            if (triggered) {
                event.preventDefault();
                event.stopPropagation();
            }
        });

        $(window).resize(function() {
            if (!engine.isOpened())
                blind.css('left', '-' + blind.width() + 'px');
        });

        root.on('submit', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (engine.sending)
                return;

            engine.sending = true;

            var button = $('[data-action="apply"]', root)[0];
            var data = new FormData(event.target);

            data.append(button.name, button.value);

            $.ajax({
                'url' : '',
                'data': data,
                'processData': false,
                'contentType': false,
                'type': 'POST',
                'beforeSend': function() {
                    $(button).attr('data-state', 'processing');
                },
                'success': function () {
                    window.location.reload();
                },
                'complete': function () {
                    engine.sending = false;
                }
            });
        });

        inputs.on('change', function () {
            root.attr('data-changed', 'true');
        });
    }, <?= JavaScript::toObject([
        'name' => '[Component] intec.universe:system.settings (.default)',
        'nodes' => '#' . $sTemplateId,
        'loader' => [
            'options' => [
                'async' => [
                    'timeout' => $arResult['SECTION'] ? 0 : 2000
                ]
            ]
        ]
    ]) ?>);
</script>
