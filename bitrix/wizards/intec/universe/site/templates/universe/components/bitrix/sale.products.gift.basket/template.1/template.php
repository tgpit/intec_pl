<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use Bitrix\Main\Security\Sign\Signer;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CAllMain $APPLICATION
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (!Loader::includeModule('intec.core'))
    return;

$templateLibrary = [
    'popup',
    'fx'
];
$currencyList = '';
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$bSlider = $arParams['SHOW_SLIDER'] === 'Y';

$sColumns = ArrayHelper::fromRange(['1', '2', '3', '4'], $arParams['COLUMNS']);


if (!empty($arResult['CURRENCIES'])) {
    $templateLibrary[] = 'currency';
    $currencyList = CUtil::PhpToJSObject(
        $arResult['CURRENCIES'],
        false,
        true,
        true
    );
}

$templateData = [
    'TEMPLATE_LIBRARY' => $templateLibrary,
    'CURRENCIES' => $currencyList
];

unset($currencyList, $templateLibrary);

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = [
    'CONFIRM' => Loc::getMessage('C_SALE_PRODUCTS_GIFT_BASKET_TEMPLATE_1_TEMPLATE_SYSTEM_DELETE_CONFIRM')
];

$arGeneralParameters = [
    'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
    'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
    'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
    'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
    'ADD_PROPERTIES_TO_BASKET' => $arParams['ADD_PROPERTIES_TO_BASKET'],
    'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
    'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'],
    'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
    'PRODUCT_BLOCKS_ORDER' => $arParams['PRODUCT_BLOCKS_ORDER'],
    '~ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
    '~BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE'],
];

$sContainerName = 'sale-products-gift-container';

?>
<?= Html::beginTag('div', [
    'class' => [
        'ns-bitrix',
        'c-sale-products-gift-basket',
        'c-sale-products-gift-basket-template-1'
    ],
    'data-entity' => $sContainerName,
    'data-view' => 'template.1',
    'id' => $sTemplateId
]) ?>
    <?php if ($arParams['HEADER_SHOW'] === 'Y') { ?>
        <div class="sale-products-gift-basket-header" data-entity="header" data-showed="false">
            <div class="intec-grid intec-grid-a-v-center">
                <div class="intec-grid-item">
                    <?= Html::tag('div', Loc::getMessage('C_SALE_PRODUCTS_GIFT_BASKET_TEMPLATE_1_TEMPLATE_TITLE'), [
                        'class' => 'sale-products-gift-basket-title'
                    ]) ?>
                </div>
                <?php if ($bSlider) { ?>
                    <div class="intec-grid-item-auto">
                        <div class="" data-role="gift.navigation"></div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
    <?php if (!empty($arResult['ITEMS']) && !empty($arResult['ITEM_ROWS'])) {
        $arAreaIds = [];

        foreach ($arResult['ITEMS'] as &$arItem) {
            $uniqueId = $arItem['ID'].'_'.md5($this->randString().$component->getAction());
            $arAreaIds[$arItem['ID']] = $this->GetEditAreaId($uniqueId);
            $this->AddEditAction($uniqueId, $arItem['EDIT_LINK'], $elementEdit);
            $this->AddDeleteAction(
                $uniqueId,
                $arItem['DELETE_LINK'],
                $elementDelete,
                $elementDeleteParams
            );

            if (!empty($arParams['TEXT_LABEL_GIFT']))
                $arItem['LABEL_VALUE'] = $arParams['TEXT_LABEL_GIFT'];
            else
                $arItem['LABEL_VALUE'] = Loc::getMessage('C_SALE_PRODUCTS_GIFT_BASKET_TEMPLATE_1_TEMPLATE_LABEL_GIFT_DEFAULT');

            $arItem['LABEL'] = !empty($arItem['LABEL_VALUE']);
            $arItem['LABEL_ARRAY_VALUE'] = [
                'gift' => $arItem['LABEL_VALUE']
            ];
        }

        unset($arItem);

    ?>
        <!-- items-container -->
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'sale-products-gift-basket-items' => true,
                'intec-grid' => [
                    '' => true,
                    'wrap' => true,
                    'a-v-stretch' => true
                ],
                'owl-carousel' => $bSlider
            ], true),
            'data-entity' => 'items-row',
            'data-role' => $bSlider ? 'slider' : null
        ]) ?>
            <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'sale-products-gift-basket-item' => true,
                        'intec-grid-item' => [
                            $arParams['COLUMNS'] => true,
                            '1200-4' => $arParams['COLUMNS'] >= 5,
                            '1024-3' => $arParams['COLUMNS'] >= 4,
                            '768-2' => true,
                            '500-1' => true
                        ]
                    ], true)
                ]) ?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.item',
                        'template.1', [
                            'RESULT' => [
                                'ITEM' => $arItem,
                                'AREA_ID' => $arAreaIds[$arItem['ID']]
                            ],
                            'PARAMS' => ArrayHelper::merge($arGeneralParameters, [
                                'SKU_PROPS' => $arResult['SKU_PROPS'][$arItem['IBLOCK_ID']]
                            ])
                        ],
                        $component,
                        ['HIDE_ICONS' => 'Y']
                    ) ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
            <?php unset($arItem) ?>
        <?= Html::endTag('div') ?>
        <!-- items-container -->
        <?php unset($arAreaIds) ?>
    <?php } else { ?>
        <?php $APPLICATION->IncludeComponent(
            'bitrix:catalog.item',
            'template.1',
            [],
            $component,
            ['HIDE_ICONS' => 'Y']
        ) ?>
    <?php } ?>
    <?php unset($arGeneralParameters) ?>
<?= Html::endTag('div') ?>
<?php

$signer = new Signer;
$signedTemplate = $signer->sign($templateName, 'sale.products.gift.basket');
$signedParams = $signer->sign(
    base64_encode(serialize($arResult['ORIGINAL_PARAMETERS'])),
    'sale.products.gift.basket'
);

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));

?>
<script type="text/javascript">
    template.load(function (data) {

        var $ = this.getLibrary('$');

        var <?= $obName ?> = new JCSaleProductsGiftBasketComponent({
            'siteId': <?= JavaScript::toObject($component->getSiteId()) ?>,
            'componentPath': <?= JavaScript::toObject($componentPath) ?>,
            'deferredLoad': true,
            'initiallyShowHeader': <?= JavaScript::toObject(!empty($arResult['ITEM_ROWS'])) ?>,
            'currentProductId': <?= JavaScript::toObject((int)$arResult['POTENTIAL_PRODUCT_TO_BUY']['ID']) ?>,
            'template': <?= JavaScript::toObject($signedTemplate) ?>,
            'parameters': <?= JavaScript::toObject($signedParams) ?>,
            'container': <?= JavaScript::toObject($sContainerName) ?>
        }, function() {

            var root = data.nodes;
            <?php if ($bSlider) {
                $arSlider = [
                    'items' => $sColumns,
                    'nav' => true,
                    'dots' => false,
                    'navText' => [
                        FileHelper::getFileData(__DIR__.'/svg/navigation.left.svg'),
                        FileHelper::getFileData(__DIR__.'/svg/navigation.right.svg')
                    ]
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
                    'autoHeight': true,
                    'smartSpeed': 600,
                    'navText': parameters.navText,
                    'dots': parameters.dots,
                    'navContainer': giftNavigation,
                    'navClass': ['gift-navigation-left intec-cl-background-hover intec-cl-border-hover', 'gift-navigation-right intec-cl-background-hover intec-cl-border-hover'],
                    'responsive': responsive
                });
            <?php }?>
        });
    }, {
        'name': '[Component] bitrix:sale.products.gift.basket (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>
    });
</script>