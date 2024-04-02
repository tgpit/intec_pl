<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\bitrix\Component;
use intec\core\helpers\JavaScript;
use intec\core\helpers\StringHelper;

if (!Loader::includeModule('intec.core'))
    return;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 */

$sTitle = StringHelper::replaceMacros(Loc::getMessage('C_WIDGET_ACCESSORIES_TEMPLATE_TITLE'), [
    'PRODUCT_NAME' => $arResult['ITEM']['NAME']
]);

$APPLICATION->SetTitle($sTitle);

if (empty($arResult['ITEM']) || empty($arResult['DATA']['ITEMS'])) {
    if ($arVisual['ERROR']['SHOW'])
        include(__DIR__.'/parts/error.php');

    return;
}

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$bIsBase = Loader::includeModule('catalog') && Loader::includeModule('sale');
$arVisual = $arResult['VISUAL'];
$sLink = $arResult['ITEM']['DETAIL_PAGE_URL'];

include(__DIR__.'/parts/sort.php');

?>
<div class="widget-accessories" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <div class="widget-accessories-adaptation intec-grid">
                <div class="widget-left intec-grid-item-4 intec-grid-item-900-1">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'widget-sections-list-items',
                            'intec-grid' => [
                                '',
                                'o-vertical'
                            ]
                        ],
                        'data' => [
                            'role' => 'section.list',
                            'status' => $arVisual['LIST']['OPEN'] ? 'open' : 'close',
                            'active' => $arVisual['LIST']['ACTIVE']
                        ]
                    ])?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'widget-sections-list-item',
                                'widget-main-product',
                                'intec-grid'
                            ],
                            'data' => [
                                'role' => 'section.list.item'
                            ]
                        ])?>
                            <a class="widget-main-product-picture" style="background-image: url(<?= $arResult['ITEM']['DATA']['PICTURE'] ?>)" href="<?= $sLink ?>">
                            </a>
                            <div class="widget-main-product-name-wrapper">
                                <a class="widget-main-product-name intec-cl-text-hover" href="<?= $sLink ?>">
                                    <?= $arResult['ITEM']['NAME'] ?>
                                </a>
                                <a class="widget-main-product-button intec-cl-border intec-cl-background-light-hover intec-cl-border-light-hover" href="<?= $sLink ?>">
                                    <?= Loc::getMessage('C_WIDGET_ACCESSORIES_TEMPLATE_PRODUCT_DETAIL') ?>
                                </a>
                            </div>
                        <?= Html::endTag('div') ?>
                        <?php if ($arVisual['LIST']['SHOW'] && $arResult['DATA']['LIST']) { ?>
                            <?php foreach ($arResult['DATA']['LIST'] as $key => $arItem) { ?>
                                <?= Html::beginTag($arItem['LINK_ACTIVE'] ? 'div' : 'a', [
                                    'class' => [
                                        'widget-sections-list-item',
                                        $arItem['LINK_ACTIVE'] ? 'active' : null,
                                        'intec' => [
                                            'grid',
                                            'grid-a-h-between',
                                            $arItem['LINK_ACTIVE'] ? 'cl-text' : 'cl-text-hover',
                                        ],
                                    ],
                                    'data' => [
                                        'role' => 'section.list.item'
                                    ],
                                    'href' => $arItem['LINK_ACTIVE'] ? null : $arItem['LINK']
                                ])?>
                                    <span class="widget-sections-list-item-name">
                                        <?= $arItem['NAME'] ?>
                                    </span>
                                    <span class="widget-sections-list-quantity">
                                        <?php if (!empty($arResult['DATA']['ITEMS'][$arItem['ID']])) { ?>
                                            <?= count($arResult['DATA']['ITEMS'][$arItem['ID']]) ?>
                                        <?php } else { ?>
                                            0
                                        <?php } ?>
                                    </span>
                                    <?php if ($arItem['LINK_ACTIVE']) { ?>
                                        <i class="widget-sections-list-mobile-control far fa-angle-down"></i>
                                    <?php } ?>
                                <?= Html::endTag($arItem['LINK_ACTIVE'] ? 'div' : 'a')?>
                            <?php } ?>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                    <?php if (count($arResult['DATA']['LIST']) > 7) { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'widget-sections-list-items-control',
                                'intec-cl-text'
                            ],
                            'data' => [
                                'role' => 'section.list.control',
                                'status' => $arVisual['LIST']['OPEN'] ? 'open' : 'close'
                            ]
                        ]) ?>
                            <span class="widget-sections-list-items-control-name" data-role="section.list.control.text">
                                <?= Loc::getMessage('C_WIDGET_ACCESSORIES_TEMPLATE_LIST_SHOW_ALL') ?>
                            </span>
                            <i class="far fa-angle-down"></i>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                    <?php if ($arVisual['FILTER']['SHOW']) { ?>
                        <div class="widget-filter">
                            <?php include(__DIR__.'/parts/filter.php') ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="widget-right intec-grid-item intec-grid-item-900-1">
                    <?php include(__DIR__.'/parts/panel.php') ?>
                    <?php include(__DIR__.'/parts/section.php') ?>
                </div>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>