<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Json;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

Loc::loadMessages(__FILE__);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];

if ($arResult['TAB']['USE'] && !empty($arResult['TAB']['VALUE'])) {
    if (
        !isset($arResult['SECTIONS'][$arResult['TAB']['VALUE']]) ||
        $arVisual['VIEW']['VALUE'] !== 'tabs'
    ) return;

    foreach ($arResult['SECTIONS'] as &$arSection)
        $arSection['ACTIVE'] = false;

    unset($arSection);

    $arResult['SECTIONS'][$arResult['TAB']['VALUE']]['ACTIVE'] = true;
}

include(__DIR__.'/parts/data.php');
include(__DIR__.'/parts/quantity.php');
include(__DIR__.'/parts/price.range.php');

$arPrice = null;
$bOffers = !empty($arResult['OFFERS']);

if (!empty($arResult['ITEM_PRICES']))
    $arPrice = ArrayHelper::getFirstValue($arResult['ITEM_PRICES']);

$arSvg = [
    'NAVIGATION' => [
        'LEFT' => FileHelper::getFileData(__DIR__.'/svg/navigation.left.svg'),
        'RIGHT' => FileHelper::getFileData(__DIR__.'/svg/navigation.right.svg')
    ],
    'PLAY' => FileHelper::getFileData(__DIR__.'/svg/play.svg'),
    'GIF' => FileHelper::getFileData(__DIR__.'/svg/gif.svg'),
    'MEASURES' => [
        'ARROW' => FileHelper::getFileData(__DIR__.'/svg/measures.select.arrow.svg')
    ]
];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-element',
        'c-catalog-element-catalog-default-2'
    ],
    'data' => [
        'data' => Json::encode($arData, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true),
        'properties' => Json::encode($arResult['SKU_PROPS'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true),
        'available' => $arData['available'] ? 'true' : 'false',
        'subscribe' => $arData['subscribe'] ? 'true' : 'false',
        'wide' => $arVisual['WIDE'] ? 'true' : 'false',
        'panel-desktop' => $arVisual['PANEL']['DESKTOP']['SHOW'] ? 'true' : 'false',
    ]
]) ?>
    <?php if ($arVisual['WIDE']) { ?>
        <div class="catalog-element-wrapper intec-content intec-content-visible">
            <div class="catalog-element-wrapper-2 intec-content-wrapper">
    <?php } ?>
    <div class="catalog-element-information-wrap" data-role="dynamic">
        <?php if ($arVisual['PANEL']['DESKTOP']['SHOW']) { ?>
            <!--noindex-->
            <? include(__DIR__.'/parts/panel.php') ?>
            <!--/noindex-->
        <?php } ?>
        <?php if ($arVisual['PANEL']['MOBILE']['SHOW']) { ?>
            <!--noindex-->
            <? include(__DIR__.'/parts/panel.mobile.php') ?>
            <!--/noindex-->
        <?php } ?>
        <?=Html::beginTag('div', [
           'class' => [
                'catalog-element-information',
                'intec-grid' => [
                    '',
                    'a-v-start',
                    'wrap',
                    'i-20'
                ]
           ]
        ])?>
            <?php if ($arVisual['GALLERY']['SHOW']) { ?>
                <div class="catalog-element-information-left intec-grid-item intec-grid-item-768-1">
                    <?php if ($arVisual['MARKS']['SHOW']) { ?>
                        <?php include(__DIR__.'/parts/marks.php') ?>
                    <?php } ?>
                    <?php if (empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'dynamic') { ?>
                        <?php include(__DIR__.'/parts/buttons.php') ?>
                    <?php } ?>
                    <?php include(__DIR__.'/parts/gallery.php') ?>
                </div>
                <div class="catalog-element-information-right intec-grid-item intec-grid-item-768-1">
            <?php } else { ?>
                <div class="catalog-element-information-right intec-grid-item-1">
                    <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-element-information-part',
                                'intec-grid' => [
                                    '',
                                    'a-v-center',
                                    'i-10'
                                ],
                                'intec-ui-m-b-15'
                            ]
                    ]) ?>
                        <?php if ($arVisual['MARKS']['SHOW']) { ?>
                            <div class="intec-grid-item-auto">
                                <?php include(__DIR__.'/parts/marks.php') ?>
                            </div>
                        <?php } ?>
                        <?php if (empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'dynamic') { ?>
                            <div class="intec-grid-item-auto"">
                                 <?php include(__DIR__.'/parts/buttons.php') ?>
                            </div>
                        <?php } ?>
                     <?= Html::endTag('div');?>
            <?php } ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-information' => [
                            'brand',
                            'part'
                        ],
                        'intec-grid' => [
                            '',
                            'a-v-center',
                            'i-h-10'
                        ]
                    ]
                ])?>
                    <?php if ($arVisual['ARTICLE']['SHOW']) { ?>
                        <div class="intec-grid-item">
                            <?php include(__DIR__.'/parts/article.php') ?>
                        </div>
                    <?php } ?>
                    <?php if ($arVisual['BRAND']['SHOW']) { ?>
                        <div class="intec-grid-item-auto">
                            <?php include(__DIR__.'/parts/brand.php') ?>
                        </div>
                    <?php } ?>
                    <?php if ($arVisual['PRINT']['SHOW']) { ?>
                        <div class="intec-grid-item-auto">
                            <?php include(__DIR__.'/parts/print.php') ?>
                        </div>
                    <?php } ?>
                    <?php if ($arResult['SHARES']['SHOW']) { ?>
                        <div class="intec-grid-item-auto">
                            <?php include(__DIR__.'/parts/shares.php') ?>
                        </div>
                    <?php } ?>
                <?= Html::endTag('div');?>
                <?php if ($arVisual['VOTE']['SHOW'] || $arVisual['QUANTITY']['SHOW']) { ?>
                    <div class="catalog-element-information-part">
                        <div class="catalog-element-information-part-wrapper intec-grid intec-grid-i-h-10 intec-grid-a-v-center">
                            <?php if ($arVisual['VOTE']['SHOW']) { ?>
                                <div class="intec-grid-item-auto">
                                    <?php include(__DIR__.'/parts/vote.php') ?>
                                </div>
                            <?php } ?>
                            <?php if ($arVisual['QUANTITY']['SHOW'] && (empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'dynamic')) { ?>
                                <div class="intec-grid-item-auto">
                                    <!--noindex-->
                                    <?php $vQuantity($arResult);

                                    if (!empty($arResult['OFFERS']))
                                        foreach ($arResult['OFFERS'] as &$arOffer) {
                                            $vQuantity($arOffer, true);

                                            unset($arOffer);
                                        }
                                    ?>
                                    <!--/noindex-->
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if (empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'dynamic') { ?>
                    <?php if ($arVisual['TIMER']['SHOW']) { ?>
                        <div class="catalog-element-timer">
                            <?php include(__DIR__ . '/parts/timer.php') ?>
                        </div>
                    <?php } ?>
                    <?php if ($arVisual['PRICE']['SHOW']) { ?>
                        <div class="catalog-element-information-part">
                            <?php include(__DIR__.'/parts/price.php') ?>
                        </div>
                        <?php if ($arVisual['PRICE']['RANGE']) { ?>
                            <div class="catalog-element-information-part">
                                <?php $vPriceRange($arResult);

                                if (!empty($arResult['OFFERS']))
                                    foreach ($arResult['OFFERS'] as &$arOffer) {
                                        $vPriceRange($arOffer, true);

                                        unset($arOffer);
                                    }
                                ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php } else if (!empty($arResult['OFFERS']) && $arResult['SKU_VIEW'] == 'list') { ?>
                    <div class="catalog-element-information-part">
                        <div class="catalog-element-information-part-wrapper">
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'intec-grid' => [
                                        '',
                                        'wrap',
                                        'i-5',
                                        'a-h-start',
                                        'a-v-center'
                                    ]
                                ]
                            ]) ?>
                                <?php if ($arVisual['PRICE']['SHOW'] && !empty($arPrice)) { ?>
                                    <div class="intec-grid-item">
                                        <div class="catalog-element-price-discount intec-grid-item-auto" data-role="price.discount">
                                            <?= Loc::GetMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_PRICE_FROM') ?>
                                            <?= $arPrice['PRINT_PRICE'] ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                    <?= Html::button(Loc::GetMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_SKU_MORE'), [
                                        'class' => [
                                            'intec-ui' => [
                                                '',
                                                'control-button',
                                                'scheme-current',
                                                'size-5',
                                                'mod-round-half'
                                            ]
                                        ],
                                        'data' => [
                                            'role' => 'sku.more',
                                            'print' => 'false'
                                        ]
                                    ]);?>
                                </div>
                            <?= Html::endTag('div') ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($arVisual['MEASURES']['USE']) { ?>
                    <div class="catalog-element-information-part">
                        <?php include(__DIR__ . '/parts/measures.php');?>
                    </div>
                <?php } ?>
                <?php if (
                        $arResult['FORM']['CHEAPER']['SHOW'] ||
                        $arResult['FORM']['MARKDOWN']['SHOW'] || (
                            $arResult['DELIVERY_CALCULATION']['USE'] && (
                                empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'dynamic'
                            )
                        ) || (
                            $arVisual['CREDIT']['SHOW'] && (
                                empty($arResult['OFFERS']) ||
                                $arResult['SKU_VIEW'] == 'dynamic'
                            )
                        )
                ) { ?>
                    <div class="catalog-element-information-part">
                        <?php if (
                            $arVisual['CREDIT']['SHOW'] && (
                                empty($arResult['OFFERS']) ||
                                $arResult['SKU_VIEW'] == 'dynamic'
                            )
                        )
                            include(__DIR__.'/parts/credit.php');
                        ?>
                        <?php if (
                            $arResult['FORM']['CHEAPER']['SHOW'] ||
                            $arResult['FORM']['MARKDOWN']['SHOW'] || (
                                $arResult['DELIVERY_CALCULATION']['USE'] && (
                                    empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'dynamic'
                                )
                            )
                        ) { ?>
                            <div class="intec-ui-m-b-25">
                                <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-h-18 intec-grid-i-v-5">
                                    <?php if ($arResult['FORM']['CHEAPER']['SHOW']) { ?>
                                        <div class="intec-grid-item-auto">
                                            <?php include(__DIR__.'/parts/cheaper.php'); ?>
                                        </div>
                                    <?php } ?>
                                    <?php if ($arResult['FORM']['MARKDOWN']['SHOW']) { ?>
                                        <div class="intec-grid-item-auto">
                                            <?php include(__DIR__.'/parts/markdown.php'); ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (
                                        $arResult['DELIVERY_CALCULATION']['USE'] && (
                                            empty($arResult['OFFERS']) ||
                                            $arResult['SKU_VIEW'] == 'dynamic'
                                        )
                                    ) { ?>
                                        <div class="intec-grid-item-auto">
                                            <?php include(__DIR__.'/parts/delivery.calculation.php'); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if (!empty($arResult['SKU_PROPS']) && !empty($arResult['OFFERS']) && $arResult['SKU_VIEW'] == 'dynamic') { ?>
                    <div class="catalog-element-information-part">
                        <?php include(__DIR__.'/parts/sku.php') ?>
                    </div>
                <?php } ?>
                <?php if ($arVisual['SIZES']['SHOW']) { ?>
                    <div class="catalog-element-information-part">
                        <?php include(__DIR__.'/parts/sizes.php') ?>
                    </div>
                <?php } ?>
                <?php if ($arVisual['ADDITIONAL']['SHOW']) { ?>
                    <div class="catalog-element-information-part catalog-element-additional-products">
                        <?php include(__DIR__.'/parts/additional.php') ?>
                    </div>
                <?php } ?>
                <?php if (empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'dynamic') { ?>
                    <?php if ($arResult['ACTION'] !== 'none') { ?>
                        <?php include(__DIR__.'/parts/purchase.php') ?>
                    <?php } ?>
                <?php } ?>
                <?php if ($arVisual['DESCRIPTION']['PREVIEW']['SHOW']) { ?>
                    <div class="catalog-element-information-part">
                        <div class="catalog-element-description catalog-element-description-preview intec-ui-markup-text">
                            <?= $arResult['PREVIEW_TEXT'] ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($arVisual['PROPERTIES']['PREVIEW']['SHOW'] || $arVisual['OFFERS']['PROPERTIES']['SHOW']) { ?>
                    <div class="catalog-element-information-part">
                        <!--noindex-->
                            <?php include(__DIR__.'/parts/properties.php') ?>
                        <!--/noindex-->
                    </div>
                <?php } ?>
                <?php if ($arVisual['INFORMATION']['PAYMENT']['SHOW'] || $arVisual['INFORMATION']['SHIPMENT']['SHOW']) { ?>
                    <div class="catalog-element-information-part">
                        <div class="catalog-element-other-information">
                            <?php include(__DIR__.'/parts/information.php') ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($arVisual['VIEW']['VALUE'] === 'narrow') { ?>
                    <div class="catalog-element-information-part">
                        <?php include(__DIR__.'/parts/advantages.php'); ?>
                    </div>
                <?php } ?>
                <?php if (!empty($arResult['SECTIONS']) && $arVisual['VIEW']['VALUE'] === 'narrow') { ?>
                    <div class="catalog-element-information-part">
                        <?php include(__DIR__.'/parts/sections.narrow.php') ?>
                    </div>
                <?php } ?>
            </div>
            <!--catalog-element-information-right-->
        <?= Html::endTag('div') ?>
        <!--catalog-element-information-->
        <?php if (!empty($arResult['OFFERS']) && $arResult['SKU_VIEW'] == 'list')
            include(__DIR__.'/parts/sku.list.php');
        ?>
    </div>
    <?php if ($arVisual['VIEW']['VALUE'] !== 'narrow') {
        include(__DIR__.'/parts/advantages.php');
    } ?>
    <?php if ($arVisual['VIEW']['VALUE'] !== 'tabs') {
        include(__DIR__.'/parts/sets.php');
    } ?>
    <?php if (!empty($arResult['SECTIONS'])) {
        if ($arVisual['VIEW']['VALUE'] === 'wide') {
            include(__DIR__.'/parts/sections.wide.php');
        } else if (
            $arVisual['VIEW']['VALUE'] === 'tabs' &&
            $arVisual['VIEW']['POSITION'] === 'top'
        ) {
            include(__DIR__.'/parts/sections.tabs.php');
        }
    } ?>
    <?php if ($arVisual['VIEW']['VALUE'] === 'tabs') {
        include(__DIR__.'/parts/sets.php');
    } ?>
    <?php if ($arVisual['VIEW']['VALUE'] === 'narrow') { ?>
        <?php if ($arVisual['STORES']['SHOW']) { ?>
            <div class="catalog-element-sections catalog-element-sections-wide">
                <div class="catalog-element-section">
                    <div class="catalog-element-section-name">
                        <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_SECTIONS_STORES') ?>
                    </div>
                    <div class="catalog-element-section-content">
                        <?php include(__DIR__.'/parts/sections/stores.php'); ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
    <?php if ($arVisual['ACCESSORIES']['SHOW']) { ?>
        <?php $bIsLink = $arVisual['ACCESSORIES']['VIEW'] === 'link' ?>
        <?= Html::beginTag($bIsLink ? 'a' : 'div',[
            'class' => [
                'catalog-element-sections',
                'catalog-element-sections-wide',
                $bIsLink ? 'catalog-element-sections-accessories' : null,
                $bIsLink ? 'intec-cl-text-hover' : null
            ],
            'href' => $bIsLink ? $arVisual['ACCESSORIES']['LINK'] : null,
            'target' => $bIsLink ? '_blank' : null,
            'data-print' => "false"
        ]) ?>
            <div class="catalog-element-section">
                <div class="catalog-element-section-name">
                    <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_ACCESSORIES_NAME_DEFAULT') ?>
                </div>
                <?php if (!$bIsLink) { ?>
                    <div class="catalog-element-section-content">
                        <?php include(__DIR__.'/parts/accessories.php') ?>
                    </div>
                <?php } ?>
            </div>
        <?= Html::endTag($bIsLink ? 'a' : 'div') ?>
    <?php } ?>
    <?php $arCodes = ['ASSOCIATED', 'RECOMMENDED', 'SERVICES'];
    foreach ($arCodes as $sCode) {
        $sLowerCode = \intec\core\helpers\StringHelper::toLowerCase($sCode);
        if ($arVisual[$sCode]["SHOW"]) {?>
            <div class="catalog-element-sections catalog-element-sections-wide" data-print="false">
                <div class="catalog-element-section">
                    <div class="catalog-element-section-name">
                        <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_SECTIONS_'.$sCode) ?>
                    </div>
                    <div class="catalog-element-section-content">
                        <?php include(__DIR__.'/parts/'.$sLowerCode.'.php') ?>
                    </div>
                </div>
            </div>
        <?php }?>
    <?php } ?>
    <?php unset($arCodes) ?>
    <?php if (!empty($arResult['SECTIONS'])) {
        if (
            $arVisual['VIEW']['VALUE'] === 'tabs' &&
            $arVisual['VIEW']['POSITION'] === 'bottom'
        ) include(__DIR__.'/parts/sections.tabs.php');
    } ?>
    <?php include(__DIR__.'/parts/script.php') ?>
    <?php if ($arVisual['WIDE']) { ?>
            </div>
        </div>
    <?php } ?>
    <?php include(__DIR__.'/parts/microdata.php') ?>
<?= Html::endTag('div') ?>