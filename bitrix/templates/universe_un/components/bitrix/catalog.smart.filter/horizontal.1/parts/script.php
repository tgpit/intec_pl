<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arVisual
 */

$arResult['JS_FILTER_PARAMS']['variable'] = 'smartFilter';

$arResult['JS_FILTER_PARAMS']['id'] = [
    'setFilter' => 'set_filter',
    'delFilter' => 'del_filter'
];

?>
<script>
    var <?= $arResult['JS_FILTER_PARAMS']['variable'] ?> = new JCSmartFilter(
        <?= JavaScript::toObject($arResult['FORM_ACTION']) ?>,
        <?= JavaScript::toObject($arVisual['VIEW']) ?>,
        <?= JavaScript::toObject($arResult['JS_FILTER_PARAMS'])?>
    );

    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var filter = {
            'container': $('[data-role="filter.container"]', root),
            'button': $('[data-role="filter.toggle"]', root),
            'body': $('[data-role="filter"]', root),
            'state': null,
            'toggle': null
        };

        var state = filter.container.data('expanded');
        var scrollbars = $('[data-role="scrollbar"]', root);
        var itemsHint = $('[data-role="hint"]', root);

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

                $('[data-role="hint.content"]', root).each(function () {
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

        scrollbars.each(function () {
            $(this).scrollbar();
        });

        filter.state = state;

        filter.toggle = function () {
            var title = $('span', filter.button);
            var height = {
                'current': null,
                'full': null
            };

            if (filter.state == true) {
                filter.state = false;

                filter.body.stop().animate({'height': '40px'}, 500);
                title.stop().animate({'opacity': '0'}, 100, function () {
                    title.html("<?= Loc::getMessage('FILTER_TEMP_HORIZONTAL_TOGGLE_DOWN') ?>");
                    title.animate({'opacity': '1'}, 100);
                });

                filter.container.attr('data-expanded', filter.state);
            } else if (filter.state === false) {
                filter.state = true;

                height.current = filter.body.height();

                filter.body.css('height', '');
                height.full = filter.body.height();
                filter.body.css('height', height.current).animate({'height': height.full}, 500, function () {
                    filter.body.css('height', '');
                });

                title.stop().animate({'opacity': '0'}, 100, function () {
                    title.html("<?= Loc::getMessage('FILTER_TEMP_HORIZONTAL_TOGGLE_UP') ?>");
                    title.animate({'opacity': '1'}, 100);
                });

                filter.container.attr('data-expanded', filter.state);
            }
        };

        filter.button.on('click', function () {
            filter.toggle();
        });
    }, {
        'name': '[Component] bitrix:catalog.smart.filter (horizontal.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>