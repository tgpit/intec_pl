<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

if (!empty($arResult['JS_FILTER_PARAMS']['id'])) {
    foreach ($arResult['JS_FILTER_PARAMS']['id'] as $sKey => $sValue)
        $arResult['JS_FILTER_PARAMS']['id'][$sKey] = $sValue . '_mobile';
}

?>
<script type="text/javascript">
    var mobileFilter = new JCSmartFilterMobile1(
        <?= JavaScript::toObject($arResult['FORM_ACTION']) ?>,
        <?= JavaScript::toObject($arResult['JS_FILTER_PARAMS']) ?>
    );

    template.load(function (data) {
        var $ = this.getLibrary('$');
        var properties = $('[data-role="property"]', data.nodes);
        var inputs = {
            'checkbox': $('[data-role="property.checkbox"]', data.nodes),
            'select': $('[data-role="property.select"]', data.nodes)
        };
        var inputsUpdater = {
            'checkbox': function (input) {
                var checkbox = $('input[type="checkbox"]', input);
                var indicator = $('[data-role="property.checkbox.indicator"]', input);

                if (checkbox.is(':checked'))
                    indicator.addClass('intec-cl-border');
                else
                    indicator.removeClass('intec-cl-border');
            },
            'select': function (select, value) {
                $('option:selected', select).each(function () {
                    var option = $(this);

                    value.html(option.text());
                });
            }
        };
        var itemsHint = $('[data-role="hint"]', data.nodes);

        itemsHint.each(function () {
            var self = $(this);
            var icon = $('[data-role="hint.icon"]', self);
            var content = $('[data-role="hint.content"]', self);

            content.on('click', function (event) {
                event.stopPropagation();
            });

            icon.on('click', function (event) {
                event.stopPropagation();
                var state = content[0].dataset.state;

                $('[data-role="hint.content"]', data.nodes).each(function () {
                    $(this)[0].dataset.state = 'hidden';
                });

                if (state === 'visible') {
                    content[0].dataset.state = 'hidden';
                } else {
                    content[0].dataset.state = 'visible';
                }
            });

            $(document).on('click', function (event) {
                if (!$(event.target).closest(content).length) {
                    content[0].dataset.state = 'hidden';
                }
            });
        });

        properties.each(function () {
            var property = $(this);

            property.header = $('[data-role="property.header"]', property);
            property.container = $('[data-role="property.container"]', property);

            property.header.on('click', function () {
                var expanded = property.attr('data-expanded') === 'true';

                property.css('pointer-events', 'none');

                if (expanded) {
                    property.container.animate({'height': 0}, 400, function () {
                        property.container.css({
                            'height': '',
                            'display': 'none'
                        });

                        property.css('pointer-events', '');
                    });
                } else {
                    var height = null;

                    property.container.css('display', '');

                    height = property.container.height();

                    property.container.css('height', 0)
                        .animate({'height': height}, 400, function () {
                            property.container.css('height', '');
                            property.css('pointer-events', '');
                        });
                }

                property.attr('data-expanded', !expanded);
            });
        });

        inputs.checkbox.each(function () {
            var self = $(this);

            self.on('click', function () {
                inputsUpdater.checkbox(self);
            });
        });

        inputs.select.each(function () {
            var self = $(this);
            var select = $('select', self);
            var value = $('[data-role="property.select.value"]', self);

            select.on('change', function () {
                inputsUpdater.select(select, value);
            });
        });
    }, {
        'name': '[Component] bitrix:catalog.smart.filter (mobile.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
