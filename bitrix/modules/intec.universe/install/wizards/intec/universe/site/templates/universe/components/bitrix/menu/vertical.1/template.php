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
        'TEXT' => Loc::getMessage('C_MENU_VERTICAL_1_STUB_ITEM_1'),
        'LINK' => null,
        'ITEMS' => []
    ], [
        'SELECTED' => false,
        'ACTIVE' => false,
        'TEXT' => Loc::getMessage('C_MENU_VERTICAL_1_STUB_ITEM_2'),
        'LINK' => null,
        'ITEMS' => []
    ], [
        'SELECTED' => false,
        'ACTIVE' => false,
        'TEXT' => Loc::getMessage('C_MENU_VERTICAL_1_STUB_ITEM_3'),
        'LINK' => null,
        'ITEMS' => []
    ], [
        'SELECTED' => false,
        'ACTIVE' => false,
        'TEXT' => Loc::getMessage('C_MENU_VERTICAL_1_STUB_ITEM_4'),
        'LINK' => null,
        'ITEMS' => []
    ]];

if (empty($arResult['ELEMENTS']))
    return;

$sView = ArrayHelper::getValue($arParams, 'VIEW');
$fView = null;

include(__DIR__.'/parts/views.php');

$sView = ArrayHelper::fromRange(ArrayHelper::getKeys($arViews), $sView);
$fView = $arViews[$sView];
$iLevel = 0;

$sMainView = ArrayHelper::fromRange(['simple', 'pictures'], $arParams['MAIN_VIEW']);

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
        'c-menu-vertical-1' => true
    ], true),
    'data' => [
        'role' => 'menu'
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
                <div class="menu-item-catalog-arrow" data-role="menu.arrow" data-active="<?= $arResult['MENU']['SHOW'] ? 'true' : 'false' ?>">
                    <i class="fal fa-angle-right"></i>
                </div>
            </div>
        <?php } ?>
        <?= Html::beginTag('div', [
            'class' => 'menu-items',
            'data' => [
                'role' => 'menu.container',
                'expanded' => !$arVisual['MAIN_MENU']['SHOW'] || $arResult['MENU']['SHOW'] ? 'true' : 'false'
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
                    <?= Html::beginTag($sTag, [
                        'class' => 'menu-item-text',
                        'href' => !$bActive ? $arItem['LINK'] : null
                    ]) ?>
                        <div class="intec-grid intec-grid-a-v-center">
                            <?php if ($sMainView === 'pictures') { ?>
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
                        <?php if (!empty($arItem['ITEMS'])) { ?>
                            <div class="menu-item-arrow">
                                <i class="fal fa-angle-right"></i>
                            </div>
                        <?php } ?>
                    <?= Html::endTag($sTag) ?>
                    <?php if (!empty($arItem['ITEMS'])) { ?>
                        <?= Html::beginTag('div', [
                            'class' => 'menu-item-submenu',
                            'data' => [
                                'expanded' => 'false',
                                'role' => 'menu',
                                'view' => $sView
                            ]
                        ]) ?>
                            <?php $fView($arItem['ITEMS'], $iLevel + 1) ?>
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

                var menu = item.find('[data-role=menu]').first();

                if (menu.length > 0) {
                    item.on('mouseover', function () {
                        menu.show().stop().animate({
                            'opacity': 1
                        }, 300);
                    }).on('mouseout', function () {
                        menu.stop().animate({
                            'opacity': 0
                        }, 300, function () {
                            menu.hide();
                        });
                    });
                }
            });

            <?php if ($arVisual['MAIN_MENU']['SHOW']) { ?>
            var catalog = $('[data-role="menu.catalog"]', root);
            var container = $('[data-role="menu.container"]', root);
            var arrow = $('[data-role="menu.arrow"]', catalog);
            var active = container.attr('data-expanded') === 'true';

            if (!active)
                container.hide();

            arrow.on('click', function () {
                var active;
                var item = $(this);

                if (container.length > 0) {
                    active = container.attr('data-expanded') === 'true';

                    if (!active) {
                        item.attr('data-active', 'true');

                        container.attr('data-expanded', 'true');
                        container.stop().slideDown(300);
                    } else {
                        item.attr('data-active', 'false');

                        container.stop().slideUp(300, function () {
                            container.attr('data-expanded', 'false');
                        });
                    }
                }
            });
            <?php } ?>
        }, {
            'name': '[Component] bitrix:menu (vertical.1)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?= Html::endTag('div') ?>