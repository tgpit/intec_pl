<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;

/** @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'UPPERCASE' => 'N',
    'TRANSPARENT' => 'N',
    'DELIMITERS' => 'N',
    'SECTION_VIEW' => 'default',
    'SECTION_COLUMNS_COUNT' => 3,
    'SECTION_ITEMS_COUNT' => 3,
    'OVERLAY_USE' => 'N',
    'SECTION_BANNER_SHOW' => 'Y',
    'SECTION_BANNER_MENU_SYNCHRONIZE' => 'Y',
    'SECTION_BANNER_HEADER_SHOW' => 'N',
    'SECTION_BANNER_DESCRIPTION_SHOW' => 'N',
    'SECTION_BANNER_DOTS_SHOW' => 'Y',
    'SECTION_BANNER_LOOP_USE' => 'N',
    'SECTION_BANNER_DESCRIPTION_LIMIT' => 'Y'
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y'
    ],
    'UPPERCASE' => $arParams['UPPERCASE'] === 'Y',
    'TRANSPARENT' => $arParams['TRANSPARENT'] === 'Y',
    'DELIMITERS' => $arParams['DELIMITERS'] === 'Y',
    'SECTION' => [
        'VIEW' => ArrayHelper::fromRange([
            'default',
            'images',
            'banner'
        ], $arParams['SECTION_VIEW']),
        'COLUMNS' => ArrayHelper::fromRange([
            2, 3, 4
        ], $arParams['SECTION_COLUMNS_COUNT']),
        'ITEMS' => $arParams['SECTION_ITEMS_COUNT']
    ],
    'OVERLAY' => [
        'USE' => $arParams['OVERLAY_USE'] === 'Y'
    ],
    'BANNER' => [
        'SHOW' => $arParams['SECTION_BANNER_SHOW'] === 'Y',
        'SYNCHRONIZE' => $arParams['SECTION_BANNER_MENU_SYNCHRONIZE'],
        'HEADER' => $arParams['SECTION_BANNER_HEADER_SHOW'] === 'Y',
        'DESCRIPTION' => [
            'SHOW' => $arParams['SECTION_BANNER_DESCRIPTION_SHOW'] === 'Y',
            'LIMIT' => $arParams['SECTION_BANNER_DESCRIPTION_SHOW'] === 'Y' && $arParams['SECTION_BANNER_DESCRIPTION_LIMIT'] === 'Y'
        ],
        'DOTS' => $arParams['SECTION_BANNER_DOTS_SHOW'],
        'LOOP' => $arParams['SECTION_BANNER_LOOP_USE']
    ]
];

if (defined('EDITOR'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['TRANSPARENT'])
    $arVisual['DELIMITERS'] = false;

$iCount = 0;

$sLongMenu = $arVisual['BANNER']['HEADER'] && $arVisual['BANNER']['DESCRIPTION']['LIMIT'] ? 'long' : null;
?>
<?php $fDraw = function ($arItem, $iLevel, $bIsIBlock = false, $bIsSection = false) use (&$fDraw, &$arParams, &$arResult, &$arVisual, &$iCount, &$sLongMenu) { ?>
<?php
    $arItems = $arItem['ITEMS'];

    if (!$bIsIBlock)
        $bIsSection = false;

    if ($bIsSection) {
        if ($arVisual['SECTION']['VIEW'] == 'banner') {
            include('parts/banner.section.php');
            $iCount++;
        } else {
            include('parts/section.php');
        }
    } else {
        include('parts/default.php');
    }
?>
<?php } ?>
<?php if (!empty($arResult)) { ?>
    <?= Html::beginTag('div', [
        'id' => $sTemplateId,
        'class' => Html::cssClassFromArray([
            'ns-bitrix' => true,
            'c-menu' => true,
            'c-menu-horizontal-1' => true
        ], true),
        'data' => [
            'role' => 'menu',
            'uppercase' => $arVisual['UPPERCASE'] ? 'true' : 'false',
            'transparent' => $arVisual['TRANSPARENT'] ? 'true' : 'false',
            'section-view' => $arParams['SECTION_VIEW'],
            'submenu-view' => $arParams['SUBMENU_VIEW']
        ]
    ]) ?>
        <?php if (empty($arParams['~OVERLAY_SELECTOR'])) { ?>
            <div class="menu-overlay" data-role="overlay"></div>
        <?php } ?>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'menu-wrapper' => true,
                'menu-transparent' => $arVisual['TRANSPARENT'],
                'intec-cl-background' => !$arVisual['TRANSPARENT'],
            ], true)
        ]) ?>
            <div class="menu-wrapper-2 intec-content">
                <div class="menu-wrapper-3 intec-content-wrapper">
                    <div class="menu-wrapper-4 intec-grid intec-grid-nowrap intec-grid-a-h-start intec-grid-a-v-stretch" data-role="items">
                        <?php foreach ($arResult as $arItem) { ?>
                        <?php
                            $bIsIBlock = false;

                            if (!empty($arItem['ITEMS'])) {
                                $bIsIBlock = ArrayHelper::getFirstValue($arItem['ITEMS']);
                                $bIsIBlock = ArrayHelper::getValue($bIsIBlock, ['PARAMS', 'FROM_IBLOCK']);
                                $bIsIBlock = Type::toBoolean($bIsIBlock);
                            }

                            $bIsSection = $bIsIBlock && $arParams['SECTION_VIEW'] !== 'information';

                            $bActive = $arItem['ACTIVE'];
                            $bCatalog = $arItem['IS_CATALOG'] == 'Y';
                            $bSelected = ArrayHelper::getValue($arItem, 'SELECTED');
                            $bSelected = Type::toBoolean($bSelected);

                            $sUrl = $bActive ? null : $arItem['LINK'];
                            $sTag = $bActive ? 'div' : 'a';
                        ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'intec-grid-item-auto' => true,
                                    'menu-for-selector' => $arVisual['SECTION']['VIEW'] === 'banner',
                                    'menu-item' => [
                                        '' => true,
                                        'default' => !$bIsSection,
                                        'section' => $bIsSection,
                                        'active' => $bSelected,
                                        'border' => $arVisual['DELIMITERS']
                                    ],
                                    'intec-cl' => [
                                        'text' => $bSelected,
                                        'background-light' => $bSelected,
                                        'background-light-hover' => !$arVisual['TRANSPARENT'],
                                        'border-light' => $arVisual['DELIMITERS']
                                    ]

                                ], true),
                                'data' => [
                                    'role' => 'item',
                                    'level' => 0
                                ]
                            ]) ?>
                                <?= Html::beginTag($sTag, [
                                    'class' => Html::cssClassFromArray([
                                        'menu-item-text' => true,
                                        'intec-grid' => true,
                                        'intec-grid-a-v-center' => true,
                                        'intec-grid-a-h-center' => true,
                                        'menu-item-catalog-text' => $bCatalog && !$arVisual['TRANSPARENT']
                                    ], true),
                                    'href' => $sUrl
                                ]) ?>
                                    <?php if ($bCatalog && !$arVisual['TRANSPARENT']) {?>
                                        <div class="menu-item-text-icon menu-item-text-icon-catalog<?= $arVisual['TRANSPARENT'] ? ' intec-cl-text' : null ?>">
                                            <i class="far fa-bars"></i>
                                        </div>
                                    <? } ?>
                                    <?= Html::beginTag('div', [
                                        'class' => Html::cssClassFromArray([
                                            'menu-item-text-wrapper' => true,
                                            'intec-grid-item-auto' => true,
                                            'intec-cl-text' => $arVisual['TRANSPARENT']
                                        ], true)
                                    ]) ?>
                                        <?= Html::encode($arItem['TEXT']) ?>
                                    <?= Html::endTag('div') ?>
                                    <?php if ($bCatalog && !$arVisual['TRANSPARENT']) {?>
                                        <div class="menu-item-text-icon menu-item-text-icon-arrow<?= $arVisual['TRANSPARENT'] ? ' intec-cl-text' : null ?>">
                                            <i class="far fa-angle-down"></i>
                                        </div>
                                    <?php } ?>
                                <?= Html::endTag($sTag) ?>
                                <?php if (!empty($arItem['ITEMS'])) {
                                    $fDraw($arItem, 1, $bIsIBlock, $bIsSection);
                                } ?>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'menu-item' => [
                                    '' => true,
                                    'default' => true,
                                    'more' => true,
                                    'border' => $arVisual['DELIMITERS'],
                                ],
                                'intec-cl' => [
                                    'background-light-hover' => !$arVisual['TRANSPARENT'],
                                    'border-light' => $arVisual['DELIMITERS']
                                ]
                            ], true),
                            'data' => [
                                'role' => 'more'
                            ]
                        ]) ?>
                            <a class="intec-grid intec-grid-a-h-center intec-grid-a-v-center menu-item-text">
                                <?= Html::tag('div', '...', [
                                    'class' => Html::cssClassFromArray([
                                        'intec-grid-item-auto' => true,
                                        'menu-item-text-wrapper' => true,
                                        'intec-cl-text' => $arVisual['TRANSPARENT']
                                    ], true)
                                ]) ?>
                            </a>
                            <?php $fDraw(array(
                                'TEXT' => '...',
                                'LINK' => null,
                                'ITEMS' => $arResult
                            ), 1, false) ?>
                        <?= Html::endTag('div') ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        <?= Html::endTag('div') ?>
        <script type="text/javascript">
            template.load(function (data) {
                var $ = this.getLibrary('$');
                
                var root = data.nodes;
                var selectors = {
                    'menu': '[data-role=menu]',
                    'item': '[data-role=item]',
                    'items': '[data-role=items]',
                    'more': '[data-role=more]',
                    'cards': '[data-role=cards]',
                    'card': '[data-role=card]',
                    'overlay': '[data-role=overlay]',
                    'scrollbar': '[data-role=scrollbar]'
                };
                var classes = {
                    'adapted': 'menu-adapted',
                    'initialized': 'menu-initialized',
                    'visible': 'menu-submenu-visible',
                    'right': 'menu-submenu-right'
                };
                var menu;
                var overlay;
                var adapt;

                if (root.is(selectors.menu)) {
                    menu = root;
                } else {
                    menu = root.find(selectors.menu).eq(0);
                }

                <?php if (!empty($arParams['~OVERLAY_SELECTOR'])) { ?>
                    overlay = $('<?= $arParams['~OVERLAY_SELECTOR'] ?>');
                <?php } else {  ?>
                    overlay = root.find(selectors.overlay);
                <?php } ?>

                /**
                 * Возвращает элемент, содержащий все пункты указанного меню.
                 * Значение параметра submenu:
                 * - селектор или jQuery - возвращать элемент указанного меню.
                 * - false - возвращать элементы всех меню.
                 */
                menu.getItemsWrappers = function (submenu) {
                    if (!submenu) {
                        return menu
                            .find(selectors.items);
                    }

                    if (menu.get(0) === submenu.get(0)) {
                        submenu = menu;
                    } else {
                        submenu = menu
                            .find(submenu);
                    }

                    return submenu
                        .find(selectors.items)
                        .eq(0);
                };

                /**
                 * Возвращает элементы меню.
                 * Значение параметра submenu:
                 * - селектор или jQuery - возвращать элементы определенного меню.
                 * - false - возвращать все элементы.
                 */
                menu.getItems = function (submenu) {
                    if (!submenu) {
                        return menu
                            .find(selectors.item);
                    }

                    return menu
                        .getItemsWrappers(submenu)
                        .children(selectors.item);
                };

                /**
                 * Возвращает меню.
                 * Значение параметра item:
                 * - селектор или объект jQuery - возвращает меню элемента.
                 * - false - возвращать все меню.
                 */
                menu.getMenu = function (item) {
                    if (item)
                        return menu
                            .find(item)
                            .find(selectors.menu)
                            .eq(0);

                    return menu
                        .find(selectors.menu);
                };

                /** Управление содержимым "Еще" */
                menu.more = {};
                /** Возвращает элемент меню "Еще" */
                menu.more.getItem = function () {
                    return menu
                        .find(selectors.more);
                };
                /** Возвращает меню элемента "Еще" */
                menu.more.getMenu = function () {
                    return menu.getMenu(menu.more.getItem());
                };
                /** Добавляет элементы (jQuery коллекция) в меню "Еще" */
                menu.more.add = function (add) {
                    var items;

                    add = $(add);
                    items = menu.getItems(menu.more.getMenu());
                    add.each(function () {
                        var self = $(this);
                        var item = items.eq(self.index());

                        self.hide();
                        item.show();
                    });
                };
                /** Удаляет элементы (jQuery коллекция) из меню "Еще" */
                menu.more.remove = function (remove) {
                    var items;

                    remove = $(remove);
                    items = menu.getItems(menu.more.getMenu());
                    remove.each(function () {
                        var self = $(this);
                        var item = items.eq(self.index());

                        self.show();
                        item.hide();
                    });
                };

                /** Правила адаптивности */
                adapt = {};
                /** Адаптация положения подменю */
                adapt.menu = function () {
                    var submenu = menu.getMenu().filter('[data-visible=true]');
                    var wrapper = menu.getItemsWrappers(menu);
                    var width = wrapper.width();
                    var right = false;

                    submenu.each(function () {
                        var self = $(this);
                        var offset = {};

                        self.removeClass(classes.right);

                        offset.start = function () { return self.offset().left - wrapper.offset().left };
                        offset.end = function () { return offset.start() + self.width(); };

                        if (offset.end() > width)
                            right = true;

                        if (right) {
                            self.addClass(classes.right);

                            if (offset.start() < 0) {
                                self.removeClass(classes.right);
                                right = false;
                            }
                        }
                    });
                };
                /** Адаптация элементов корневого меню */
                adapt.items = function () {
                    var items = {};
                    var width = {};
                    var wrapper = menu.getItemsWrappers(menu);

                    menu.removeClass(classes.adapted);
                    items.all = menu.getItems(menu);
                    items.visible = $([]);
                    items.hidden = $([]);

                    items.all.hide();
                    width.available = wrapper.width() - menu.more.getItem().show().width();
                    items.all.show();
                    width.total = 0;

                    menu.more.remove(items.all);
                    items.all.each(function () {
                        var item = $(this);

                        item.css({'width': 'auto'});
                        width.total += item.width();

                        if (width.total < width.available) {
                            items.visible = items.visible.add(item);
                        } else {
                            items.hidden = items.hidden.add(item);
                        }
                    });

                    if (items.hidden.length > 0) {
                        menu.more.add(items.hidden);
                    } else {
                        menu.more.getItem().hide();
                        width.available = wrapper.width();
                    }

                    menu.addClass(classes.adapted);

                    var last = null;

                    width.total = {
                        'original': 0,
                        'rounded': 0
                    };

                    items.visible.each(function () {
                        width.total.original += $(this).width();
                    }).each(function () {
                        var item = $(this);
                        var size = Math.floor((width.available / 100) * (item.width() / width.total.original) * 100);

                        width.total.rounded += size;
                        item.css('width', size + 'px');
                        last = item;
                    });

                    if (last != null)
                        last.css('width', last.width() + (width.available - width.total.rounded) + 'px');
                };

                /** События наведения мыши на пунктах меню */
                menu.getItems().add(menu.more.getItem()).on('mouseenter', function (event) {
                    var item = $(this);
                    var submenu;
                    var level = item.attr('data-level');

                    submenu = menu.getMenu(item);
                    <?php if ($arVisual['OVERLAY']['USE']) { ?>
                        if ((level === '0' || item.attr('data-role') === 'more') && submenu.length === 1) {
                            overlay.show().stop().animate({
                                'opacity': 1
                            }, 300);
                        }
                    <?php } ?>
                    submenu.show().addClass(classes.visible).stop().animate({
                        'opacity': 1
                    }, 300);
                    submenu.attr('data-visible', 'true');
                    adapt.menu();

                    event.preventDefault();
                }).on('mouseleave', function (event) {
                    var item = $(this);
                    var submenu;
                    var level = item.attr('data-level');

                    <?php if ($arVisual['OVERLAY']['USE']) { ?>
                        if (level === '0' || item.attr('data-role') === 'more') {
                            overlay.stop().animate({
                                'opacity': 0
                            }, 300, function () {
                                overlay.hide();
                            });
                        }
                    <?php } ?>
                    submenu = menu.getMenu(item);
                    submenu.stop().removeClass(classes.visible).animate({
                        'opacity': 0
                    }, 50, function () {
                        adapt.menu();
                        submenu.removeAttr('data-visible');
                        submenu.hide();
                    });

                    event.preventDefault();
                });

                /** Скроллбар в меню */
                root.find(selectors.scrollbar).scrollbar();

                /** Анимация карт */
                menu.getItems(menu).each(function () {
                    var item = $(this);
                    var submenu = menu.getMenu(item);
                    var items = menu.getItems(submenu);

                    if (submenu.length !== 1)
                        return;

                    var cardsWrapper = submenu.children(selectors.cards);

                    if (cardsWrapper.length !== 1)
                        return;

                    var cards = cardsWrapper.children(selectors.card);
                    var opening = false;

                    cardsWrapper.css({
                        'width': 0
                    });

                    items.on('mouseenter', function () {
                        var item = $(this);
                        var card = cards.eq(item.index());
                        var width;

                        cards.attr('data-expanded', 'false');
                        card.attr('data-expanded', 'true');

                        if (!opening) {
                            width = {
                                'current': cardsWrapper.width(),
                                'new': cardsWrapper.css({
                                    'width': 'auto'
                                }).width()
                            };

                            if (width.current !== width.new) {
                                opening = true;

                                cardsWrapper.stop().css({
                                    'width': width.current
                                }).animate({
                                    'width': width.new
                                }, 500, function () {
                                    opening = false;
                                });
                            }
                        }
                    });

                    submenu.on('mouseleave', function () {
                        var width = cardsWrapper.width();

                        opening = false;
                        cardsWrapper.stop().css({
                            'width': width
                        }).animate({
                            'width': 0
                        }, 500, function () {
                            cards.attr('data-expanded', 'false');
                        });
                    });
                });

                root.on('update', function () {
                    adapt.menu();
                    adapt.items();
                    menu.addClass(classes.initialized);
                });

                $(window).on('resize', function () {
                    root.trigger('update');
                });

                setTimeout(function () {
                    root.trigger('update');
                }, 1000);
            }, {
                'name': '[Component] bitrix:menu (horizontal.1)',
                'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
                'loader': {
                    'name': 'lazy'
                }
            });
        </script>
    <?= Html::endTag('div') ?>
<?php } ?>
<?php if ($arVisual['SECTION']['VIEW'] === 'banner') { ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');

            var root = data.nodes;

            var allroots = $('[data-section-view="banner"]');
            var menu = $('[data-role="menu"]', root);
            var menuitem = $('[data-role="main-item"]', menu);
            var menuitemcontent = $('[data-role="menu-item-content"]', menu);
            var menuitemactive = $('[data-role="main-item"].active', menu);
            var scrollbar = $('[data-role="scrollbar"]', menu);
            var slider = $('[data-role="slider"]', menu);

            var count = <?= $iCount?>;
            var menuid = '';
            var smenuid = '';
            var thisroot = '';
            var enotherroot = '';
            var itemelements = '';
            var synchronize = "<?= $arVisual['BANNER']['SYNCHRONIZE'] ?>" === 'Y';
            var dots = "<?= $arVisual['BANNER']['DOTS'] ?>" === 'Y';
            var loop = "<?= $arVisual['BANNER']['LOOP'] ?>" === 'Y';

            scrollbar.scrollbar();

            slider.owlCarousel({
                dots: dots,
                loop: loop,
                margin: 10,
                items: 1
            });

            menuitemactive.each(function(){
                var activeindex = menuitem.index($(this));
                menuitemcontent.eq(activeindex).addClass('active');
            });

            for (var i = 0; i <= count; i++) {
                var id = $('[data-menu="menu' + i + '"]', root);
                if(!id.find('[data-role="main-item"].active').length > 0) {
                    $('[data-role="main-item"]', id).first().addClass('active');
                    $('[data-role="main-item"]', id).first().children().addClass('intec-cl-text');
                    $('[data-role="menu-item-content"]', id).first().addClass('active');
                }
            }

            allroots.on('mouseenter', function(){
                thisroot = '#' + $(this).attr('id');
                allroots.each(function(){
                    if (('#' + $(this).attr('id')) != thisroot) {
                        enotherroot = '#' + $(this).attr('id');
                    }
                });
            });

            $('.menu-for-selector').on('mouseenter', function(){
                smenuid = '[data-menu="' + $(this).children('.menu-submenu-banner-section').data('menu') + '"]';
                menuid = $(smenuid, root);
                itemelements = $('[data-role="main-item"]', menuid);
            });

            menuitem.on('mouseenter', function(){
                activateMenuItem(thisroot, smenuid, itemelements.index($(this)));
                if (synchronize) {
                    activateMenuItem(enotherroot, smenuid, itemelements.index($(this)));
                }
            });

            function activateMenuItem(rootid,menuid,elementid) {
                var fmenuid = $(menuid, rootid);
                var items = $('[data-role="main-item"]',fmenuid);
                var content = $('[data-role="menu-item-content"]', fmenuid);

                items.children().removeClass('intec-cl-text');
                items.eq(elementid).children().first().addClass('intec-cl-text');
                items.removeClass('active');
                items.eq(elementid).addClass('active');
                content.removeClass('active');
                content.eq(elementid).addClass('active');
            }
        }, {
            'name': '[Component] bitrix:menu (horizontal.1)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>