<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use \Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 */



$this->setFrameMode(true);
$this->addExternalCss('/bitrix/css/main/bootstrap.css');

$templateLibrary = ['popup'];
$currencyList = '';
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

if (!empty($arResult['CURRENCIES']))
{
    $templateLibrary[] = 'currency';
    $currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}

$templateData = [
    'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
    'TEMPLATE_LIBRARY' => $templateLibrary,
    'CURRENCIES' => $currencyList
];

unset($currencyList, $templateLibrary);

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = [
    'CONFIRM' => GetMessage('CT_SPG_TPL_ELEMENT_DELETE_CONFIRM')
];
$positionClassMap = [
    'left' => 'product-item-label-left',
    'center' => 'product-item-label-center',
    'right' => 'product-item-label-right',
    'bottom' => 'product-item-label-bottom',
    'middle' => 'product-item-label-middle',
    'top' => 'product-item-label-top'
];
$discountPositionClass = '';

if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($arParams['DISCOUNT_PERCENT_POSITION'])) {
    foreach (explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) as $pos) {
        $discountPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
    }
}

$labelPositionClass = '';

if (!empty($arParams['LABEL_PROP_POSITION'])) {
    foreach (explode('-', $arParams['LABEL_PROP_POSITION']) as $pos) {
        $labelPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
    }
}

$arParams['~MESS_BTN_BUY'] = $arParams['~MESS_BTN_BUY'] ?: Loc::getMessage('CT_SPG_TPL_MESS_BTN_CHOOSE');
$arParams['~MESS_BTN_DETAIL'] = $arParams['~MESS_BTN_DETAIL'] ?: Loc::getMessage('CT_SPG_TPL_MESS_BTN_DETAIL');
$arParams['~MESS_BTN_COMPARE'] = $arParams['~MESS_BTN_COMPARE'] ?: Loc::getMessage('CT_SPG_TPL_MESS_BTN_COMPARE');
$arParams['~MESS_BTN_SUBSCRIBE'] = $arParams['~MESS_BTN_SUBSCRIBE'] ?: Loc::getMessage('CT_SPG_TPL_MESS_BTN_SUBSCRIBE');
$arParams['~MESS_BTN_ADD_TO_BASKET'] = $arParams['~MESS_BTN_ADD_TO_BASKET'] ?: Loc::getMessage('CT_SPG_TPL_MESS_BTN_CHOOSE');
$arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE'] ?: Loc::getMessage('CT_SPG_TPL_MESS_PRODUCT_NOT_AVAILABLE');
$arParams['~MESS_SHOW_MAX_QUANTITY'] = $arParams['~MESS_SHOW_MAX_QUANTITY'] ?: Loc::getMessage('CT_SPG_CATALOG_SHOW_MAX_QUANTITY');
$arParams['~MESS_RELATIVE_QUANTITY_MANY'] = $arParams['~MESS_RELATIVE_QUANTITY_MANY'] ?: Loc::getMessage('CT_SPG_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['~MESS_RELATIVE_QUANTITY_FEW'] = $arParams['~MESS_RELATIVE_QUANTITY_FEW'] ?: Loc::getMessage('CT_SPG_CATALOG_RELATIVE_QUANTITY_FEW');

$arGeneralParameters = [
    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
    'VOTE_PREFIX_ID' => $arParams['VOTE_PREFIX_ID'],
    'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
    'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
    'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
    'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
    'MESS_SHOW_MAX_QUANTITY' => $arParams['~MESS_SHOW_MAX_QUANTITY'],
    'MESS_RELATIVE_QUANTITY_MANY' => $arParams['~MESS_RELATIVE_QUANTITY_MANY'],
    'MESS_RELATIVE_QUANTITY_FEW' => $arParams['~MESS_RELATIVE_QUANTITY_FEW'],
    'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
    'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
    'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
    'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
    'ADD_PROPERTIES_TO_BASKET' => $arParams['ADD_PROPERTIES_TO_BASKET'],
    'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
    'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'],
    'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
    'COMPARE_PATH' => $arParams['COMPARE_PATH'],
    'COMPARE_NAME' => $arParams['COMPARE_NAME'],
    'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
    'PRODUCT_BLOCKS_ORDER' => $arParams['PRODUCT_BLOCKS_ORDER'],
    'LABEL_POSITION_CLASS' => $labelPositionClass,
    'DISCOUNT_POSITION_CLASS' => $discountPositionClass,
    'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
    'SLIDER_PROGRESS' => $arParams['SLIDER_PROGRESS'],
    '~ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
    '~BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE'],
    '~COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
    '~COMPARE_DELETE_URL_TEMPLATE' => $arResult['~COMPARE_DELETE_URL_TEMPLATE'],
    'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
    'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
    'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
    'MESS_BTN_BUY' => $arParams['~MESS_BTN_BUY'],
    'MESS_BTN_DETAIL' => $arParams['~MESS_BTN_DETAIL'],
    'MESS_BTN_COMPARE' => $arParams['~MESS_BTN_COMPARE'],
    'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
    'MESS_BTN_ADD_TO_BASKET' => $arParams['~MESS_BTN_ADD_TO_BASKET'],
    'MESS_NOT_AVAILABLE' => $arParams['~MESS_NOT_AVAILABLE'],
    'PROPERTY_ARTICLE' => $arParams['PROPERTY_ARTICLE'],
    'ARTICLE_SHOW' => $arParams['ARTICLE_SHOW'],
    'PROPERTY_PICTURES' => $arParams['PROPERTY_PICTURES'],
    'VOTE_SHOW' => $arParams['VOTE_SHOW'],
    'VOTE_MODE' => $arParams['VOTE_MODE'],
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],

    'QUANTITY_SHOW' => $arParams['QUANTITY_SHOW'],
    'QUANTITY_BOUNDS_FEW' => $arParams['QUANTITY_BOUNDS_FEW'],
    'QUANTITY_BOUNDS_MANY' => $arParams['QUANTITY_BOUNDS_MANY'],
];

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
$sContainerName = 'sale-products-gift-container';
$sParentName = 'parent-container';

$sColumns = ArrayHelper::fromRange(['1', '2', '3', '4'], $arParams['COLUMNS']);
$bSlider = $arParams['SHOW_SLIDER'] === 'Y';
$sView = ArrayHelper::fromRange(['1', '2', '3', '4', '5'], $arParams['VIEW']);
$sButtonPosition = ArrayHelper::fromRange(['top', 'bottom'], $arParams['NAVIGATION_BUTTON_POSITION']);
$sContentPosition = ArrayHelper::fromRange(['middle', 'top', 'bottom'], $arParams['GIFTS_POSITION_IN_LIST']);

$sHeaderText = !empty($arParams['HEADER_TEXT']) ? $arParams['HEADER_TEXT'] :  Loc::getMessage('CT_SPG_TPL_TEXT_HEADER');

if ($sView == '1')
    $sTemplate = 'template.3';
elseif ($sView == '2')
    $sTemplate = 'template.2';
elseif ($sView == '3')
    $sTemplate = 'template.4';
elseif ($sView == '4')
    $sTemplate = 'template.4';
elseif ($sView == '5')
    $sTemplate = 'template.4';

?>

<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-intec-universe',
        'c-sale-products-gift-section',
        'c-sale-products-gift-section-template-1'
    ],
    'data' => [
        'entity' => $sParentName,
        'view' => $sView,
        'position' => 'none'
    ]
]) ?>
    <?php if ($arParams['HEADER_SHOW'] === 'Y') { ?>
        <div class="sale-products-gift-header" data-entity="header" data-showed="false" style="display: none; opacity: 0;">
            <div class="intec-grid intec-grid-a-v-center">
                <div class="intec-grid-item">
                    <?= Html::tag('div', $sHeaderText, [
                        'class' => Html::cssClassFromArray([
                            'catalog-element-additional-block-name' => $sView != 4,
                            'catalog-element-additional-block-name-small' => $sView == 4,
                        ], true)
                    ]) ?>
                </div>
                <?php if ($bSlider && $sView != '3' && $sButtonPosition == 'top') { ?>
                    <div class="intec-grid-item-auto">
                        <div class="" data-role="gift.navigation"></div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
    <div class="c-sale-products-gift-items-wrap" data-entity="<?= $sContainerName ?>">
        <?php if ($bSlider && $sView == '3') { ?>
            <div class="sale-products-gift-navigation" data-role="gift.navigation"></div>
        <?php } ?>
        <?php if (!empty($arResult['ITEMS']) && !empty($arResult['ITEM_ROWS'])) {
            $arAreaIds = [];

            foreach ($arResult['ITEMS'] as &$arItem) {
                $uniqueId = $arItem['ID'].'_'.md5($this->randString().$component->getAction());
                $arAreaIds[$arItem['ID']] = $this->GetEditAreaId($uniqueId);
                $this->AddEditAction($uniqueId, $arItem['EDIT_LINK'], $elementEdit);
                $this->AddDeleteAction($uniqueId, $arItem['DELETE_LINK'], $elementDelete, $elementDeleteParams);

                $arItem['LABEL_VALUE'] = $arParams['TEXT_LABEL_GIFT'] ?: Loc::getMessage('CT_SPG_TPL_TEXT_LABEL_GIFT_DEFAULT');
                $arItem['LABEL_ARRAY_VALUE'] = ['gift' => $arItem['LABEL_VALUE']];
                $arItem['LABEL_PROP_MOBILE'] = array('gift' => true);
                $arItem['LABEL'] = !empty($arItem['LABEL_VALUE']);
            }

            unset($arItem);
        ?>
            <!-- items-container -->
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'sale-products-gift-items' => true,
                    'intec-grid' => [
                        '' => true,
                        'wrap' => true,
                        'a-v-stretch' => true,
                        'a-h-start' => true
                    ],
                    'owl-carousel' => $bSlider
                ], true),
                'data-entity' => 'items-row',
                'data-role' => $bSlider ? 'slider' : null
            ]) ?>
                <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'sale-products-gift-item' => true,
                            'intec-grid-item' => [
                                $sColumns => true,
                                '1200-4' => $sColumns >= 5,
                                '1024-3' => $sColumns >= 4,
                                '768-2' => true,
                                '500-1' => true
                            ]
                        ], true),
                        'data-role' => 'item'
                    ]) ?>
                        <?php $APPLICATION->IncludeComponent(
                            'bitrix:catalog.item',
                            $sTemplate,
                            [
                                'RESULT' => [
                                    'ITEM' => $arItem,
                                    'AREA_ID' => $arAreaIds[$arItem['ID']],
                                    'BIG_LABEL' => 'N',
                                    'BIG_DISCOUNT_PERCENT' => 'N',
                                    'BIG_BUTTONS' => 'N',
                                    'SCALABLE' => 'N'
                                ],
                                'PARAMS' => ArrayHelper::merge($arGeneralParameters, [
                                    'SKU_PROPS' => $arResult['SKU_PROPS'][$arItem['IBLOCK_ID']],
                                    'WIDE' => $sView == 5 ? 'Y' : 'N'
                                ])
                            ],
                            $component,
                            ['HIDE_ICONS' => 'Y']
                        ); ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            <?= Html::endTag('div') ?>
            <!-- items-container -->
        <?php }	else { ?>
            <?$APPLICATION->IncludeComponent(
                'bitrix:catalog.item',
                $sTemplate,
                [],
                $component,
                ['HIDE_ICONS' => 'Y']
            );?>
        <?php } ?>
        <?php
        $signer = new \Bitrix\Main\Security\Sign\Signer;
        $signedTemplate = $signer->sign($templateName, 'sale.products.gift.section');
        $signedParams = $signer->sign(base64_encode(serialize($arResult['ORIGINAL_PARAMETERS'])), 'sale.products.gift.section');
        ?>
    </div>
    <?php if ($bSlider && $sView != '3' && $sButtonPosition == 'bottom') { ?>
        <div class="sale-products-gift-navigation-bottom">
            <div class="" data-role="gift.navigation"></div>
        </div>
    <?php } ?>
<?= Html::endTag('div') ?>

<script>
    BX.message({
        BTN_MESSAGE_BASKET_REDIRECT: '<?=GetMessageJS('CT_SPG_CATALOG_BTN_MESSAGE_BASKET_REDIRECT')?>',
        BASKET_URL: '<?=$arParams['BASKET_URL']?>',
        ADD_TO_BASKET_OK: '<?=GetMessageJS('ADD_TO_BASKET_OK')?>',
        TITLE_ERROR: '<?=GetMessageJS('CT_SPG_CATALOG_TITLE_ERROR')?>',
        TITLE_BASKET_PROPS: '<?=GetMessageJS('CT_SPG_CATALOG_TITLE_BASKET_PROPS')?>',
        TITLE_SUCCESSFUL: '<?=GetMessageJS('ADD_TO_BASKET_OK')?>',
        BASKET_UNKNOWN_ERROR: '<?=GetMessageJS('CT_SPG_CATALOG_BASKET_UNKNOWN_ERROR')?>',
        BTN_MESSAGE_SEND_PROPS: '<?=GetMessageJS('CT_SPG_CATALOG_BTN_MESSAGE_SEND_PROPS')?>',
        BTN_MESSAGE_CLOSE: '<?=GetMessageJS('CT_SPG_CATALOG_BTN_MESSAGE_CLOSE')?>',
        BTN_MESSAGE_CLOSE_POPUP: '<?=GetMessageJS('CT_SPG_CATALOG_BTN_MESSAGE_CLOSE_POPUP')?>',
        COMPARE_MESSAGE_OK: '<?=GetMessageJS('CT_SPG_CATALOG_MESS_COMPARE_OK')?>',
        COMPARE_UNKNOWN_ERROR: '<?=GetMessageJS('CT_SPG_CATALOG_MESS_COMPARE_UNKNOWN_ERROR')?>',
        COMPARE_TITLE: '<?=GetMessageJS('CT_SPG_CATALOG_MESS_COMPARE_TITLE')?>',
        PRICE_TOTAL_PREFIX: '<?=GetMessageJS('CT_SPG_CATALOG_PRICE_TOTAL_PREFIX')?>',
        RELATIVE_QUANTITY_MANY: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_MANY'])?>',
        RELATIVE_QUANTITY_FEW: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_FEW'])?>',
        BTN_MESSAGE_COMPARE_REDIRECT: '<?=GetMessageJS('CT_SPG_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT')?>',
        SITE_ID: '<?= CUtil::JSEscape($component->getSiteId()) ?>'
    });

    template.load(function (data) {
        var $ = this.getLibrary('$');

        var <?= $obName ?> = new JCSaleProductsGiftComponent({
            'siteId': <?= JavaScript::toObject($component->getSiteId()) ?>,
            'componentPath': <?= JavaScript::toObject($componentPath) ?>,
            'deferredLoad': true,
            'initiallyShowHeader': <?= JavaScript::toObject(!empty($arResult['ITEM_ROWS'])) ?>,
            'currentProductId': <?= JavaScript::toObject((int)$arResult['POTENTIAL_PRODUCT_TO_BUY']['ID']) ?>,
            'template': <?= JavaScript::toObject($signedTemplate) ?>,
            'parameters': <?= JavaScript::toObject($signedParams) ?>,
            'container': <?= JavaScript::toObject($sContainerName) ?>,
            'parentContainer': <?= JavaScript::toObject($sParentName) ?>,
            'contentPosition': <?= JavaScript::toObject($sContentPosition) ?>
        }, function() {

            var root = data.nodes;

            <?php if ($bSlider) { ?>
            <?php
            $arSlider = [
                'items' => $sColumns,
                'nav' => true,
                'dots' => false
            ];
            ?>
            var slider = $('[data-role="slider"]', root);
            var parameters = <?= JavaScript::toObject($arSlider) ?>;
            var giftNavigation = $('[data-role="gift.navigation"]');

            var responsive = {
                0: {
                    'items': 1
                }
            };

            if (parameters.items > 2)
                responsive[500] = {
                    'items': 2
                };

            if (parameters.items > 3)
                responsive[820] = {
                    'items': 3
                };

            responsive[1200] = {'items': parameters.items};

            slider.owlCarousel({
                'items': parameters.items,
                'nav': parameters.nav,
                'autoHeight': false,
                'smartSpeed': 600,
                'navText': [
                    '<i class="far fa-chevron-left"></i>',
                    '<i class="far fa-chevron-right"></i>'
                ],
                'dots': parameters.dots,
                'navContainer': giftNavigation,
                'navClass': ['gift-navigation-left intec-cl-background-hover intec-cl-border-hover', 'gift-navigation-right intec-cl-background-hover intec-cl-border-hover'],
                'responsive': responsive
            });
            <?php } ?>
        });
    }, {
        'name': '[Component] intec.universe:sale.products.gift (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>
    });
</script>