<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

if (defined('EDITOR'))
    $arResult['ELEMENTS'] = [[
        'SELECTED' => false,
        'ACTIVE' => false,
        'TEXT' => Loc::getMessage('C_MENU_VERTICAL_3_STUB_ITEM_1'),
        'LINK' => null,
        'ITEMS' => []
    ], [
        'SELECTED' => false,
        'ACTIVE' => false,
        'TEXT' => Loc::getMessage('C_MENU_VERTICAL_3_STUB_ITEM_2'),
        'LINK' => null,
        'ITEMS' => []
    ], [
        'SELECTED' => false,
        'ACTIVE' => false,
        'TEXT' => Loc::getMessage('C_MENU_VERTICAL_3_STUB_ITEM_3'),
        'LINK' => null,
        'ITEMS' => []
    ], [
        'SELECTED' => false,
        'ACTIVE' => false,
        'TEXT' => Loc::getMessage('C_MENU_VERTICAL_3_STUB_ITEM_4'),
        'LINK' => null,
        'ITEMS' => []
    ]];

if (empty($arResult['ELEMENTS']))
    return;

$sView = ArrayHelper::fromRange(['simple', 'pictures'], $arParams['MAIN_VIEW']);
$iLevel = 0;

$arSvg = [
    'CATALOG' => FileHelper::getFileData(__DIR__.'/svg/catalog.svg'),
];

$arVisual = $arResult['VISUAL'];
?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => Html::cssClassFromArray([
        'ns-bitrix' => true,
        'c-menu' => true,
        'c-menu-vertical-3' => true
    ], true),
    'data' => [
        'role' => 'menu',
        'view' => $sView
    ]
]) ?>
    <div class="menu-wrapper">
        <?php if ($arVisual['MAIN_MENU']['SHOW']) {
            $sTag = !empty($arVisual['MAIN_MENU']['LINK']) ? 'a' : 'div';
        ?>
            <div class="menu-item-catalog" data-role="menu.catalog">
                <?= Html::tag('span', $arSvg['CATALOG'], [
                    'class' => 'menu-item-catalog-icon'
                ]) ?>
                <?= Html::tag($sTag, $arVisual['MAIN_MENU']['TEXT'], [
                    'class' => 'menu-item-catalog-text',
                    'href' => $sTag === 'a' ? $arVisual['MAIN_MENU']['LINK'] : null
                ]) ?>
                <div class="menu-item-catalog-arrow" data-role="menu.arrow" data-active="<?= $arResult['MENU_SHOW'] ? 'true' : 'false' ?>">
                    <i class="fal fa-angle-right"></i>
                </div>
            </div>
        <?php } ?>
        <?= Html::beginTag('div', [
            'class' => 'menu-items',
            'data' => [
                'role' => 'menu.container',
                'expanded' => $arResult['MENU_SHOW'] ? 'true' : 'false'
            ]
        ]) ?>
            <?php foreach ($arResult['ELEMENTS'] as $arItem) { ?>
            <?php
                $bSelected = ArrayHelper::getValue($arItem, 'SELECTED');
                $bSelected = Type::toBoolean($bSelected);
                $bActive = ArrayHelper::getValue($arItem, 'ACTIVE');
                $sTag = $bActive ? 'div' : 'a';

                $arImage = [
                    'TYPE' => 'picture',
                    'SOURCE' => null
                ];

                if (!empty($arItem['IMAGE'])) {
                    if ($arItem['IMAGE']['CONTENT_TYPE'] === 'image/svg+xml') {
                        $arImage['TYPE'] = 'svg';
                        $arImage['SOURCE'] = $arItem['IMAGE']['SRC'];
                    } else {
                        $arImage['SOURCE'] = CFile::ResizeImageGet($arItem['IMAGE'], array(
                            'width' => 90,
                            'height' => 90
                        ), BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                        if (!empty($arImage['SOURCE'])) {
                            $arImage['SOURCE'] = $arImage['SOURCE']['src'];
                        } else {
                            $arImage['SOURCE'] = null;
                        }
                    }
                }

                if (empty($arImage['SOURCE'])) {
                    $arImage['TYPE'] = 'picture';
                    $arImage['SOURCE'] = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                }
            ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'menu-item' => true
                    ], true),
                    'data' => [
                        'active' => $bActive ? 'true' : 'false',
                        'selected' => $bSelected ? 'true' : 'false',
                        'role' => 'item',
                        'level' => $iLevel
                    ]
                ]) ?>
                    <div class="menu-item-text">
                        <?= Html::beginTag($sTag, [
                            'class' => '',
                            'href' => !$bActive ? $arItem['LINK'] : null
                        ]) ?>
                            <div class="intec-grid intec-grid-a-v-center">
                                <?php if ($sView === 'pictures') { ?>
                                <div class="intec-grid-item-auto">
                                    <?php if ($arImage['TYPE'] === 'svg') { ?>
                                        <?= Html::tag('div', FileHelper::getFileData('@root/'.$arImage['SOURCE']), [
                                            'class' => [
                                                'menu-item-picture',
                                                'intec-cl-svg',
                                                'intec-image-effect'
                                            ]
                                        ]) ?>
                                    <?php } else { ?>
                                        <?= Html::tag('div', null, [
                                            'class' => [
                                                'menu-item-picture',
                                                'intec-image-effect'
                                            ],
                                            'data' => [
                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $arImage['SOURCE'] : null
                                            ],
                                            'style' => [
                                                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arImage['SOURCE'].'\')' : null
                                            ]
                                        ]) ?>
                                    <?php } ?>
                                </div>
                                <?php } ?>
                                <div class="intec-grid-item">
                                    <?= $arItem['TEXT'] ?>
                                </div>
                            </div>
                        <?= Html::endTag($sTag) ?>
                        <?php if (!empty($arItem['ITEMS'])) { ?>
                            <div class="menu-item-arrow" data-active="<?= $bSelected ? 'true' : 'false' ?>" data-role="item.arrow">
                                <i class="fal fa-angle-right"></i>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if (!empty($arItem['ITEMS'])) { ?>
                        <?= Html::beginTag('div', [
                            'class' => 'menu-item-submenu',
                            'data' => [
                                'expanded' => $bSelected ? 'true' : 'false',
                                'role' => 'menu'
                            ]
                        ]) ?>
                            <?php foreach ($arItem['ITEMS'] as $arItem) { ?>
                                <?php
                                $bSelected = ArrayHelper::getValue($arItem, 'SELECTED');
                                $bSelected = Type::toBoolean($bSelected);
                                $bActive = ArrayHelper::getValue($arItem, 'ACTIVE');
                                $sTag = $bActive ? 'div' : 'a';
                                ?>
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'menu-item-submenu-item' => true
                                    ], true),
                                    'data' => [
                                        'active' => $bActive ? 'true' : 'false',
                                        'selected' => $bSelected ? 'true' : 'false',
                                        'role' => 'item'
                                    ]
                                ]) ?>
                                    <div class="menu-item-submenu-item-text">
                                        <?= Html::tag($sTag, $arItem['TEXT'], [
                                            'class' => 'intec-cl-text-hover',
                                            'href' => !$bActive ? $arItem['LINK'] : null
                                        ]) ?>
                                    </div>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
        <?= Html::endTag('div') ?>
    </div>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');

            var root = data.nodes;
            var items = root.find('[data-role=item]');

            items.each(function () {
                var item = $(this);
                var arrow = item.find('[data-role="item.arrow"]');

                var menu = item.find('[data-role=menu]').first();

                if (menu.length > 0) {
                    arrow.on('click', function () {
                        var item = $(this);
                        var expanded = menu.attr('data-expanded') === 'true';

                        if (!expanded) {
                            menu.css({
                                'display': '',
                                'height': 'auto'
                            });

                            heightContainer = menu.outerHeight();
                            menu.css({
                                'height': 0
                            });

                            menu.show().stop().animate({
                                'height': heightContainer
                            }, 300, function () {
                                menu.css('height', '');
                                menu.attr('data-expanded', 'true');
                            });

                            item.attr('data-active', 'true');
                        } else {
                            menu.animate({
                                'height': 0
                            }, 300, function () {
                                menu.hide();
                                menu.css('height', '');
                                menu.attr('data-expanded', 'false');
                            });
                            item.attr('data-active', 'false');
                        }
                    });
                }
            });

            <?php if ($arVisual['MAIN_MENU']['SHOW']) { ?>
                var catalog = $('[data-role="menu.catalog"]', root);
                var container = $('[data-role="menu.container"]', root);
                var arrow = $('[data-role="menu.arrow"]', catalog);

                arrow.on('click', function () {
                    var item = $(this);

                    if (container.length > 0) {
                        var active = container.attr('data-expanded') === 'true';

                        if (!active) {
                            container.css({
                                'height': 'auto'
                            });

                            heightContainer = container.outerHeight();
                            container.css('height', 0);

                            container.animate({
                                'height': heightContainer
                            }, 300, function () {
                                container.css('height', '');
                                container.attr('data-expanded', 'true');
                            });
                            item.attr('data-active', 'true');
                        } else {
                            container.animate({
                                'height': 0
                            }, 300, function () {
                                container.css('height', '');
                                container.attr('data-expanded', 'false');
                            });
                            item.attr('data-active', 'false');
                        }
                    }
                });
            <?php } ?>

        }, {
            'name': '[Component] bitrix:menu (vertical.3)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?= Html::endTag('div') ?>