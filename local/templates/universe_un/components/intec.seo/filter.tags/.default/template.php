<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

if (empty($arResult['ITEMS']))
    return;

$arVisual = $arResult['DATA']['VISUAL'];
$arNavigation = $arResult['DATA']['NAVIGATION'];
$sSectionUrl = $arResult['DATA']['SECTION']['URL'];

$iCounter = 0;
$sStatus = 'auto';

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-intec-seo',
        'c-filter-tags',
        'c-filter-tags-default',
        $arVisual['MOBILE']['HIDE'] ? 'c-filter-tags-mobile-hide' : null
    ],
    'data' => [
        'initialized' => 'false',
        'expandable' => 'false',
        'expanded' => 'false'
    ]
]) ?>
    <?php if ($arVisual['SLIDER']['USE'] && $arVisual['SLIDER']['ARROW']) { ?>
        <?= Html::tag('div', null, [
            'class' => 'filter-tags-navigation',
            'data' => [
                'role' => 'navigation',
            ]
        ]) ?>
    <?php } ?>
    <div class="filter-tags-items owl-carousel" data-role="items">
        <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
            <?php
                $iCounter++;

                if ($arVisual['BUTTON']['AUTO'] && $iCounter <=1)
                    $sStatus = 'before';
                else
                    $sStatus = 'auto';

                if (!$arVisual['BUTTON']['AUTO']) {
                    $sStatus = 'after';

                    if ($iCounter <= $arVisual['BUTTON']['NUMBER'])
                        $sStatus = 'before';
                }
            ?>
            <?= Html::beginTag('div', [
                'id' => $arItem['ACTIVE'] ? 'del_filter_seo' : null,
                'class' => 'filter-tags-item',
                'data' => [
                    'active' => $arItem['ACTIVE'] ? 'true' : 'false',
                    'role' => 'item',
                    'status' => $sStatus
                ]
            ]) ?>
                <?= Html::beginTag($arItem['ACTIVE'] ? 'div' : 'a',[
                    'href' => !$arItem['ACTIVE'] ? Html::encode($arItem['TARGET'] ? $arItem['URL']['TARGET'] : $arItem['URL']['SOURCE']) : null,
                    'class' => [
                        'filter-tags-item-name',
                        'intec-grid' => [
                            '',
                            'between',
                            'a-v-center'
                        ],
                        'intec-cl' => [
                            $arItem['ACTIVE'] ? 'background' : null,
                            $arItem['ACTIVE'] ? 'border' : 'border-light-hover',
                            $arItem['ACTIVE'] ? 'background-light-hover' : 'background-light-40-hover',
                        ]
                    ]
                ]) ?>
                    <span class="filter-tags-item-name-value">
                        <?= Html::encode($arItem['NAME']) ?>
                    </span>
                    <?php if ($arVisual['COUNT']['SHOW']) { ?>
                        <span class="filter-tags-item-name-count <?= !$arItem['ACTIVE'] ? 'intec-cl-text' : null?>">
                            <?= Html::encode($arItem['COUNT']) ?>
                        </span>
                    <?php } ?>
                    <?php if ($arItem['ACTIVE']) { ?>
                        <i class="close fas fa-times"></i>
                    <?php } ?>
                <?= Html::endTag($arItem['ACTIVE'] ? 'div' : 'a') ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
        <?php if (!$arVisual['SLIDER']['USE']) { ?>
            <a class="filter-tags-button intec-grid intec-grid-a-h-center intec-grid-a-v-center intec-cl intec-cl-border-light-hover intec-cl-background-light-hover" data-role="button" data-action="collapse">
                <i class="fas fa-chevron-up"></i>
            </a>
        <?php } ?>
    </div>
    <?php if (!$arVisual['SLIDER']['USE']) { ?>
        <div class="filter-tags-buttons" data-role="expand-button-root">
            <a class="filter-tags-button intec-grid intec-grid-a-h-center intec-grid-a-v-center intec-cl intec-cl-border-light-hover intec-cl-background-light-hover" data-role="button" data-action="expand">
                <i class="fas fa-chevron-down"></i>
            </a>
        </div>
    <?php } ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var root = data.nodes;

            var container = $('[data-role="items"]', root).first();
            var items = {
                'all': $('[data-role="item"]', container),
                'before': $('[data-role="item"][data-status="before"]', container),
                'lastBefore': $('[data-role="item"][data-status="before"]', container).last()
            };
            var buttons = $('[data-role="button"]', root);
            var button = {
                'items': {
                    'expand': $('[data-role="button"][data-action="expand"]', root),
                    'collapse': $('[data-role="button"][data-action="collapse"]', root),
                    'root' : $('[data-role="expand-button-root"]', root)
                },
                'position': 'underList', /** inList, underList */
                'settings': {
                    'auto': <?= JavaScript::toObject($arVisual['BUTTON']['AUTO']) ?>,
                    'number': <?= JavaScript::toObject($arVisual['BUTTON']['NUMBER']) ?>,
                    'mod': <?= JavaScript::toObject($arVisual['BUTTON']['MOD']) ?>
                }
            };

            var state = false;
            var link = <?= JavaScript::toObject($sSectionUrl) ?>;
            var sliderUse = <?= JavaScript::toObject($arVisual['SLIDER']['USE']) ?>;
            var navigation = <?= JavaScript::toObject($arNavigation) ?>;

            $('#del_filter_seo').on('click', function(){
                location.href = link;
            });

            if (!sliderUse) {
                var setItemStatus = function () {
                    var containerWidth = container.outerWidth(true);
                    var width = button.items.expand.outerWidth(true);
                    var counter = 0;

                    items.all.each( function () {
                        width = width + $(this).outerWidth(true);
                        counter++;

                        if (counter > 1) {
                            if (containerWidth <= 270)
                                width = width - button.items.expand.outerWidth(true);

                            if (width >= containerWidth) {
                                $(this).attr('data-status', 'after');
                                $(this).data('status', 'after');
                            } else {
                                $(this).attr('data-status', 'before');
                                $(this).data('status', 'before');
                            }
                        }
                    });

                    expandable(width - button.items.expand.outerWidth(true));
                };

                var moveExpandButton = function (after = null, before = null) {
                    if (!!after)
                        button.items.expand.insertAfter($(after));

                    if (!!before)
                        button.items.expand.insertBefore($(before));
                };

                var getExpandedHeight = function () {
                    var value = container.css('height');
                    var result;

                    container.css('height', '');
                    result = container.height();
                    container.css('height', value);

                    return result;
                };

                var getCollapsedHeight = function (mobile = false) {
                    var containerWidth = container.outerWidth(true);
                    var height = mobile ? 2 : button.items.expand.outerHeight(true);
                    var previousHeight = 0;
                    var width = 0;

                    if (button.settings.auto) {
                        if (height < items.before.first().outerHeight(true))
                            height = items.before.first().outerHeight(true)
                    } else {
                        items.before.each(function(){
                            width = width + $(this).outerWidth(true);

                            if ($(this).outerWidth(true) >= containerWidth) {
                                width = 0;
                                height = height + previousHeight + $(this).outerHeight(true);
                            } else if (width > containerWidth) {
                                height = height + $(this).outerHeight(true);
                                width = $(this).outerWidth(true);
                            } else {
                                previousHeight = $(this).outerHeight(true);
                            }
                        });

                        if (width + button.items.expand.outerWidth(true) > containerWidth)
                            height = height + button.items.expand.outerHeight(true);
                    }

                    return height;
                };

                var hasHiddenItems = function () {
                    var result = false;

                    if (button.settings.auto) {
                        items.all.each(function () {
                            var item = $(this);
                            var height = getCollapsedHeight();

                            if (item.offset().top - container.offset().top >= height) {
                                result = true;
                                return false;
                            }
                        });
                    } else {
                        if (items.all.length > items.before.length) {
                            result = true;
                        }
                    }

                    return result;
                };

                var expand = function () {
                    if (state)
                        return;

                    state = true;
                    container.stop().animate({
                        'height': getExpandedHeight()
                    }, 250, function () {
                        container.css('height', '');
                        root.attr('data-expanded', 'true');
                        setTimeout(function () {
                            button.items.collapse.css('display', 'flex');
                        }, 150);
                    });

                };

                var collapse = function () {
                    if (!state)
                        return;

                    state = false;
                    container.stop().animate({
                        'height': getCollapsedHeight()
                    }, 250, function () {
                        root.attr('data-expanded', 'false');
                        button.items.collapse.css('display', 'none');
                    });

                };

                var toggle = function () {
                    if (hasHiddenItems())
                        state ? collapse() : expand();
                };

                var moveTo = function (under = false) {
                    if (under) {
                        if (button.position !== 'underList') {
                            button.items.expand.appendTo(button.items.root);
                            button.position = 'underList';
                        }
                    } else {
                        if (button.position !== 'inList') {
                            moveExpandButton(items.lastBefore);
                            button.position = 'inList';
                        }
                    }
                };

                var expandable = function (width = 0) {
                    if (items.all.length > items.before.length && !button.settings.auto) {
                        root.attr('data-expandable', 'true');
                    } else if (button.settings.auto) {
                        if (width >= container.outerWidth(true))
                            root.attr('data-expandable', 'true');
                        else
                            root.attr('data-expandable', 'false');
                    }
                };

                $(window).on('resize', function () {

                    if (button.settings.auto)
                        setItemStatus();
                    else
                        expandable();

                    if (root.attr('data-expanded') === 'false') {
                        if (container.outerWidth(true) <= 270) {
                            container.css('height', getCollapsedHeight(true) + 'px');
                            moveTo(true);
                        } else {
                            container.css('height', getCollapsedHeight() + 'px');
                            moveTo();
                        }
                    }
                });

                if (button.settings.auto) {
                    setItemStatus();
                    items.before = $('[data-role="item"][data-status="before"]', container);
                    items.lastBefore = $('[data-role="item"][data-status="before"]', container).last();
                } else {
                    expandable();
                }

                if (container.outerWidth(true) > 270) {
                    moveExpandButton(items.lastBefore);
                    button.position = 'inList';
                }

                container.css('height', getCollapsedHeight() + 'px');
                buttons.on('click', toggle);
                root.attr('data-initialized', 'true');

            } else {
                var index = $('[data-role="item"][data-active="true"]', container).index();

                var owl = container.owlCarousel({
                    'margin': 10,
                    'nav': sliderUse,
                    'navContainer': $(navigation.container, root),
                    'navClass': navigation.class,
                    'navText': navigation.text,
                    'dots': false,
                    'autoWidth': true,
                    'responsive': {
                        0:{
                            'items': 1
                        },
                        600:{
                            'items': 3
                        },
                        1000:{
                            'items': 5
                        }
                    }
                });

                setTimeout(function(){
                    owl.trigger('refresh.owl.carousel');
                    if (index >= 0)
                        owl.trigger('to.owl.carousel', [index, 300]);
                }, 1000);
            }
        },{
            'name': '[Component] intec.seo:filter.tags (.default)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?= Html::endTag('div') ?>