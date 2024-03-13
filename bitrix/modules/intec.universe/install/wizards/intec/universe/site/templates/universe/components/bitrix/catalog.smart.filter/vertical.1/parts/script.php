<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arVisual
 */

$arResult['JS_FILTER_PARAMS']['id'] = [
    'setFilter' => 'set_filter',
    'delFilter' => 'del_filter'
];

$arResult['JS_FILTER_PARAMS']['variable'] = 'smartFilter'.($arVisual['MOBILE'] ? 'Mobile' : null);

if ($arVisual['MOBILE'])
    foreach ($arResult['JS_FILTER_PARAMS']['id'] as $sKey => $sValue)
        $arResult['JS_FILTER_PARAMS']['id'][$sKey] = $sValue.'_mobile';

?>
<script>

    var <?= $arResult['JS_FILTER_PARAMS']['variable'] ?> = new JCSmartFilterVertical1(
        <?= JavaScript::toObject($arResult['FORM_ACTION']) ?>,
        <?= JavaScript::toObject($arVisual['VIEW']) ?>,
        <?= JavaScript::toObject($arResult['JS_FILTER_PARAMS']) ?>
    );

    template.load(function (data) {

        var $ = this.getLibrary('$');
        var root = data.nodes;
        var smartFilterSearch = $('[data-role="smart.filter.search"]');
        var scrollBlocks = $('[data-role="scrollbar"]', root);
        var itemsRoot;
        var items = [];
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

        scrollBlocks.each(function () {
            $(this).scrollbar();
        });

        smartFilterSearch.on('focus', function () {
            itemsRoot = $(this).closest('[data-role="bx_filter_block"]').parent();
            items = [];

            $('[data-role="scrollbar.item"]', itemsRoot).each(function () {
                var item = {
                    'item': $(this),
                    'value': $(this).attr('data-search-value')
                };
                items.push(item);
            });
        });

        smartFilterSearch.on('input', function () {
            var searchButton = $('[data-role="search.button"]', itemsRoot);
            var searchCancel = $('[data-role="search.cancel"]', itemsRoot);

            var regular = new RegExp(this.value.toLowerCase());
            var foundSomething = false;
            var lastItem;

            $(items).each(function () {
                if (!!this.value) {
                    var found = regular.test(this.value.toLowerCase());

                    if (found) {
                        this.item.attr('data-active', 'true');
                        lastItem = this.item;
                        if (!foundSomething)
                            foundSomething = true;

                    } else {
                        this.item.attr('data-active', 'false');
                    }
                }
            });

            var notingFoundText = $('[data-role="nothing.found"]', itemsRoot);

            if (!foundSomething && notingFoundText.attr('data-status', 'disabled')) {
                notingFoundText.attr('data-status', 'enabled');
            } else if (foundSomething && notingFoundText.attr('data-status', 'enabled')) {
                notingFoundText.attr('data-status', 'disabled');
            }

            if (this.value !== '') {
                if (searchButton.attr('data-status') === 'enabled') {
                    searchButton.attr('data-status', 'disabled');
                    searchCancel.attr('data-status', 'enabled');
                }
            } else {
                if (searchButton.attr('data-status') === 'disabled') {
                    searchButton.attr('data-status', 'enabled');
                    searchCancel.attr('data-status', 'disabled');
                }

                $(items).each(function () {
                    $(this.item).removeAttr('data-active');
                    lastItem = this.item;
                });
            }
        });

        $('[data-role="search.cancel"]', root).on('click', function () {
            var parent = $(this).closest('[data-role="bx_filter_block"]').parent();
            var items = {
                'searchField' : $('[data-role="smart.filter.search"]', parent),
                'searchButton' : $('[data-role="search.button"]', parent),
                'canceledButton' : $('[data-role="search.cancel"]', parent),
                'scrollbarItems' : $('[data-role="scrollbar.item"]', parent),
                'notingFoundText' : $('[data-role="nothing.found"]', parent),
                'lastItem' : null
            };

            items.searchField.val('');
            items.scrollbarItems.each(function () {
                $(this).removeAttr('data-active');
            });

            if (!!items.scrollbarItems)
                items.lastItem = $(items.scrollbarItems[items.scrollbarItems.length - 1]);

            items.searchButton.attr('data-status', 'enabled');
            items.canceledButton.attr('data-status', 'disabled');
            items.notingFoundText.attr('data-status', 'disabled');
        });

        var filter = {
            'button': $('[data-role="filter.toggle"]', root),
            'body': $('[data-role="filter"]', root),
            'state': $('[data-role="filter"]', root).data('expanded'),
            'toggle': null
        };
        var popup = {
            'button': $('[data-role="popup.close"]', root),
            'close': null
        };

        popup.close = function () {
            var container = popup.button.closest('[data-role="popup"]');

            container.animate({'opacity': '0'}, 200, function () {
                container.css({'opacity': '', 'display': 'none'});
            });
        };

        filter.toggle = function () {
            var title = $('span', filter.button);
            var height = {
                'current': null,
                'full': null
            };

            if (filter.state === true) {
                filter.state = false;
                filter.body.attr('data-expanded', filter.state);
                filter.button.attr('data-expanded', filter.state);

                filter.body.stop().animate({'height': '0'}, 300, function () {
                    filter.body.css('display', 'none');
                });
                title.stop().animate({'opacity': '0'}, 100, function () {
                    title.html("<?= Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_1_TOGGLE_DOWN') ?>");
                    title.animate({'opacity': '1'}, 100);
                });
            } else if (filter.state === false) {
                filter.state = true;
                filter.body.attr('data-expanded', filter.state);
                filter.button.attr('data-expanded', filter.state);

                filter.body.stop().css('display', '');
                height.current = filter.body.height();

                filter.body.css('height', '');
                height.full = filter.body.height();

                filter.body.css('height', height.current).animate({'height': height.full}, 300, function () {
                    filter.body.css('height', '');
                });
                title.stop().animate({'opacity': '0'}, 100, function () {
                    title.html("<?= Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_1_TOGGLE_UP') ?>");
                    title.animate({'opacity': '1'}, 100);
                });
            }
        };

        filter.button.on('click', function () {
            filter.toggle();
        });

        popup.button.on('click', function () {
            popup.close()
        });
    }, {
        'name': '[Component] bitrix:catalog.smart.filter (vertical.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
