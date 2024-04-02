<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\Json;
use intec\core\helpers\FileHelper;

/**
 * @var array $arResult
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 */

$this->setFrameMode(true);

if (!Loader::includeModule('intec.core'))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

if (empty($arResult['ITEMS']) || empty($arResult['CATEGORIES']))
    return;

$arSvg = [
    'PRICE_DIFFERENCE' => FileHelper::getFileData(__DIR__.'/svg/price.difference.svg')
];

$dData = include(__DIR__.'/parts/data.php');
$vButtons = include(__DIR__.'/parts/buttons.php');
$vCounter = include(__DIR__.'/parts/counter.php');
$vImage = include(__DIR__.'/parts/image.php');
$vPrice = include(__DIR__.'/parts/price.php');
$vPurchase = include(__DIR__.'/parts/purchase.php');
$vQuantity = include(__DIR__.'/parts/quantity.php');
$vSku = include(__DIR__.'/parts/sku.php');

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-widget',
        'c-widget-products-3'
    ],
    'data' => [
        'properties' => !empty($arResult['SKU_PROPS']) ? Json::encode($arResult['SKU_PROPS'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true) : '',
        'columns-mobile' => $arVisual['COLUMNS']['MOBILE']
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
                    <?= Html::beginTag('ul', [
                        'class' => Html::cssClassFromArray([
                            'widget-tabs' => true,
                            'widget-tabs-margin-' . $arVisual['TABS']['ALIGN'] => $arBlocks['FOOTER']['SHOW'] && $arBlocks['FOOTER']['BUTTON']['SHOW'],
                            'intec-ui' => [
                                '' => true,
                                'control-tabs' => true,
                                'mod-block' => true,
                                'mod-position-'.$arVisual['TABS']['ALIGN'] => true,
                                'scheme-current' => true,
                                'view-1' => true
                            ]
                        ], true),
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
                                <?php $arItems = &$arCategory['ITEMS'] ?>
                                <?php include(__DIR__.'/parts/items.php') ?>
                            <?= Html::endTag('div') ?>
                            <?php $iCounter++ ?>
                        <?php } ?>
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
                                'href' => $arBlocks['FOOTER']['BUTTON']['URL']
                            ]) ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?php } else if ($arResult['MODE'] === 'category') { ?>
                    <?php $arCategory = null; ?>
                    <?php $arItems = &$arResult['ITEMS'] ?>
                    <?php include(__DIR__.'/parts/items.php') ?>
                <?php } ?>
                <?php if (!defined('EDITOR'))
                    include(__DIR__.'/parts/script.php');
                ?>
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
<?= Html::endTag('div') ?>