<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

Loc::loadMessages(__FILE__);

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    $bLite = true;
}

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];
$arBlocks = $arResult['BLOCKS'];

$bActionButtonsShow = $arResult['DELAY']['USE'] ||
    $arResult['COMPARE']['USE'] ||
    $arResult['DELAY']['SHOW_INACTIVE'] ||
    $arResult['COMPARE']['SHOW_INACTIVE'];

$dData = include(__DIR__.'/parts/data.php');
$vButtons = include(__DIR__.'/parts/action.buttons.php');
$vMeasure = include(__DIR__.'/parts/measure.php');
$vOrder = include(__DIR__.'/parts/order.buttons.php');
$vPrice = include(__DIR__.'/parts/price.php');
$vPriceTotal = include(__DIR__.'/parts/price.total.php');
$vPurchase = include(__DIR__.'/parts/purchase.php');
$vQuantity = include(__DIR__.'/parts/quantity.php');
$vSku = include(__DIR__.'/parts/sku.php');

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-widget',
        'c-widget-products-5'
    ],
    'data' => [
        'properties' => !empty($arResult['SKU_PROPS']) ? Json::encode($arResult['SKU_PROPS'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true) : ''
    ]
]) ?>
    <div class="widget-wrapper intec-content intec-content-visible">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                        <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                            <div class="widget-title-container intec-grid-item">
                                <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                    'class' => [
                                        'widget-title',
                                        'align-'.$arBlocks['HEADER']['ALIGN'],
                                        $arBlocks['FOOTER']['BUTTON']['SHOW'] ? 'widget-title-margin' : null
                                    ]
                                ]) ?>
                            </div>
                            <?php if ($arBlocks['FOOTER']['BUTTON']['SHOW']) { ?>
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'widget-all-container' => true,
                                        'mobile' => $arBlocks['HEADER']['SHOW'],
                                        'intec-grid-item' => [
                                            'auto' => $arBlocks['HEADER']['SHOW'],
                                            '1' => !$arBlocks['HEADER']['SHOW']
                                        ]
                                    ], true)
                                ]) ?>
                                    <?= Html::beginTag('a', [
                                        'class' => [
                                            'widget-all-button',
                                            'intec-cl-text-light-hover',
                                        ],
                                        'href' => $arBlocks['FOOTER']['BUTTON']['URL']
                                    ])?>
                                        <i class="fal fa-angle-right"></i>
                                    <?= Html::endTag('a')?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                            <div class="intec-grid-item-1">
                                <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['ALIGN'] ?>">
                                    <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <div class="widget-content">
                <?php if ($arResult['MODE'] === 'all' || $arResult['MODE'] === 'categories') { ?>
                    <?php if ($arVisual['VIEW'] === 'tabs') { ?>
                        <div class="widget-tabs-wrap" data-ui-control="tabs">
                            <?= Html::beginTag('ul', [
                                'class' => [
                                    'widget-tabs',
                                    'intec-ui' => [
                                        '',
                                        'control-tabs',
                                        'mod-block',
                                        'mod-position-'.$arVisual['TABS']['ALIGN'],
                                        'scheme-current',
                                        'view-1'
                                    ]
                                ],
                                'data' => [
                                    'ui-control' => 'tabs'
                                ]
                            ]) ?>
                                <?php $iCounter = 0 ?>
                                <?php foreach ($arResult['CATEGORIES'] as $arCategory) { ?>
                                    <?= Html::beginTag('li', [
                                        'class' => 'intec-ui-part-tab',
                                        'data' => [
                                            'active' => $iCounter === 0 ? 'true' : 'false'
                                        ]
                                    ]) ?>
                                        <a href="<?= '#'.$sTemplateId.'-tab-'.$iCounter ?>" data-type="tab">
                                            <?= $arCategory['NAME'] ?>
                                        </a>
                                    <?= Html::endTag('li') ?>
                                    <?php $iCounter++ ?>
                                <?php } ?>
                            <?= Html::endTag('ul') ?>
                            <div class="widget-tabs-content intec-ui intec-ui-control-tabs-content">
                                <?php $iCounter = 0 ?>
                                <?php foreach ($arResult['CATEGORIES'] as $arCategory) { ?>
                                    <?= Html::beginTag('div', [
                                        'id' => $sTemplateId.'-tab-'.$iCounter,
                                        'class' => 'intec-ui-part-tab',
                                        'data' => [
                                            'active' => $iCounter === 0 ? 'true' : 'false'
                                        ]
                                    ]) ?>
                                        <?php $arProperties = &$arCategory['PROPERTIES'] ?>
                                        <?php $arItems = &$arCategory['ITEMS'] ?>
                                        <?php include(__DIR__.'/parts/items.php') ?>
                                    <?= Html::endTag('div') ?>
                                    <?php $iCounter++ ?>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if ($arBlocks['FOOTER']['SHOW'] && $arBlocks['FOOTER']['BUTTON']['SHOW']) { ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'widget-footer' => true,
                                    'mobile' => $arBlocks['HEADER']['SHOW'] && $arBlocks['FOOTER']['BUTTON']['SHOW']
                                ], true),
                                'data' => [
                                    'type' => 'tabs'
                                ]
                            ]) ?>
                                <?= Html::tag('a', $arBlocks['FOOTER']['BUTTON']['TEXT'], [
                                    'class' => [
                                        'widget-footer-button',
                                        'intec-cl-text-hover'
                                    ],
                                    'href' => $arBlocks['FOOTER']['BUTTON']['URL'],
                                ]) ?>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="widget-sections">
                            <?php foreach ($arResult['CATEGORIES'] as $arCategory) { ?>
                                <div class="widget-section">
                                    <?php if ($arVisual['SECTIONS']['TITLE']['SHOW']) { ?>
                                        <?= Html::tag('div', $arCategory['NAME'], [
                                            'class' => [
                                                'widget-section-name',
                                                'intec-template-part',
                                                'intec-template-part-title'
                                            ],
                                            'data' => [
                                                'align' => $arVisual['SECTIONS']['TITLE']['ALIGN']
                                            ]
                                        ]) ?>
                                    <?php } ?>
                                    <div class="widget-section-content">
                                        <?php $arProperties = &$arCategory['PROPERTIES'] ?>
                                        <?php $arItems = &$arCategory['ITEMS'] ?>
                                        <?php include(__DIR__.'/parts/items.php') ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } else if ($arResult['MODE'] === 'category') { ?>
                    <?php $arCategory = null ?>
                    <?php $arProperties = &$arResult['PROPERTIES'] ?>
                    <?php $arItems = &$arResult['ITEMS'] ?>
                    <?php include(__DIR__.'/parts/items.php') ?>
                <?php } ?>

                <?php if (!defined('EDITOR')) include(__DIR__.'/parts/script.php') ?>
            </div>
            <?php if ($arBlocks['FOOTER']['SHOW'] && $arBlocks['FOOTER']['BUTTON']['SHOW'] && $arVisual['VIEW'] !== 'tabs') { ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-footer' => true,
                        'align-' . $arBlocks['FOOTER']['ALIGN'] => true,
                        'mobile' => $arBlocks['HEADER']['SHOW'] && $arBlocks['FOOTER']['BUTTON']['SHOW']
                    ], true),
                    'data' => [
                        'type' => 'default'
                    ]
                ]) ?>
                    <?= Html::tag('a', $arBlocks['FOOTER']['BUTTON']['TEXT'], [
                        'class' => [
                            'widget-footer-button',
                            'intec-ui' => [
                                '',
                                'control-button',
                                'size-5',
                                'scheme-current',
                                'mod-transparent',
                                'mod-round-half'
                            ]
                        ],
                        'href' => $arBlocks['FOOTER']['BUTTON']['URL']
                    ]) ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
        </div>
    </div>
<?= Html::endTag('div');?>
