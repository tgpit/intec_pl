<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array arFields
 */

$arSections = [
    'DESCRIPTION' => [
        'ID' => 'description',
        'SHOW' => false,
        'TYPE' => 'print',
        'NAME' => $arVisual['DESCRIPTION']['DETAIL']['NAME'],
        'VALUE' => null,
        'OFFERS' => []
    ],
    'PROPERTIES' => [
        'ID' => 'properties',
        'SHOW' => $arVisual['PROPERTIES']['DETAIL']['SHOW'] || $arVisual['OFFERS']['PROPERTIES']['SHOW'],
        'TYPE' => 'file',
        'NAME' => $arVisual['PROPERTIES']['DETAIL']['NAME'],
        'VALUE' => __DIR__.'/sections/properties.php'
    ],
    'ACCESSORIES' => [
        'ID' => 'accessories',
        'SHOW' => $arVisual['ACCESSORIES']['SHOW'] && $arResult['FIELDS']['ACCESSORIES']['SHOW'],
        'TYPE' => 'file',
        'NAME' => $arVisual['ACCESSORIES']['NAME'],
        'VALUE' => __DIR__.'/sections/accessories.php',
        'VIEW' => $arVisual['ACCESSORIES']['VIEW'],
        'LINK' => null
    ],
    'STORES' => [
        'ID' => 'stores',
        'SHOW' => $arVisual['STORES']['USE'] && $arVisual['STORES']['POSITION'] === 'content' && ($arResult['SKU']['VIEW'] === 'dynamic' || !$bOffers && $arResult['SKU']['VIEW'] === 'list'),
        'TYPE' => 'file',
        'NAME' => $arVisual['STORES']['NAME'],
        'VALUE' => __DIR__.'/sections/stores.php'
    ],
    'DOCUMENTS' => [
        'ID' => 'documents',
        'SHOW' => $arFields['DOCUMENTS']['SHOW'] && $arVisual['DOCUMENTS']['POSITION'] === 'content',
        'TYPE' => 'file',
        'NAME' => $arVisual['DOCUMENTS']['NAME'],
        'VALUE' => __DIR__.'/sections/documents.php'
    ],
    'VIDEO' => [
        'ID' => 'video',
        'SHOW' => $arFields['VIDEO']['SHOW'],
        'TYPE' => 'file',
        'NAME' => $arVisual['VIDEO']['NAME'],
        'VALUE' => __DIR__.'/sections/video.php'
    ],
    'ARTICLES' => [
        'ID' => 'articles',
        'SHOW' => $arFields['ARTICLES']['SHOW'],
        'TYPE' => 'file',
        'NAME' => $arVisual['ARTICLES']['NAME'],
        'VALUE' => __DIR__.'/sections/articles.php'
    ],
    'REVIEWS' => [
        'ID' => 'reviews',
        'SHOW' => $arResult['REVIEWS']['SHOW'],
        'TYPE' => 'file',
        'NAME' => $arResult['REVIEWS']['NAME'],
        'VALUE' => __DIR__.'/sections/reviews.php'
    ],
    'BUY' => [
        'ID' => 'buy',
        'SHOW' => $arVisual['INFORMATION']['BUY']['SHOW'],
        'TYPE' => 'file',
        'NAME' => $arVisual['INFORMATION']['BUY']['NAME'],
        'VALUE' => __DIR__.'/sections/information.buy.php'
    ],
    'PAYMENT' => [
        'ID' => 'payment',
        'SHOW' => $arVisual['INFORMATION']['PAYMENT']['SHOW'],
        'TYPE' => 'file',
        'NAME' => $arVisual['INFORMATION']['PAYMENT']['NAME'],
        'VALUE' => __DIR__.'/sections/information.payment.php'
    ],
    'SHIPMENT' => [
        'ID' => 'shipment',
        'SHOW' => $arVisual['INFORMATION']['SHIPMENT']['SHOW'],
        'TYPE' => 'file',
        'NAME' => $arVisual['INFORMATION']['SHIPMENT']['NAME'],
        'VALUE' => __DIR__.'/sections/information.shipment.php'
    ],
    'ADDITIONAL_1' => [
        'ID' => 'additional_1',
        'SHOW' => $arVisual['INFORMATION']['ADDITIONAL_1']['SHOW'],
        'TYPE' => 'file',
        'NAME' => $arVisual['INFORMATION']['ADDITIONAL_1']['NAME'],
        'VALUE' => __DIR__.'/sections/information.additional.1.php'
    ],
    'ADDITIONAL_2' => [
        'ID' => 'additional_2',
        'SHOW' => $arVisual['INFORMATION']['ADDITIONAL_2']['SHOW'],
        'TYPE' => 'file',
        'NAME' => $arVisual['INFORMATION']['ADDITIONAL_2']['NAME'],
        'VALUE' => __DIR__.'/sections/information.additional.2.php'
    ]
];

if ($arVisual['DESCRIPTION']['DETAIL']['SHOW']) {
    if (empty($arSections['DESCRIPTION']['NAME']))
        $arSections['DESCRIPTION']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_ADDITIONAL_DESCRIPTION');

    if (!empty($arResult['DETAIL_TEXT']))
        $arSections['DESCRIPTION']['VALUE'] = &$arResult['DETAIL_TEXT'];
    else if ($arVisual['DESCRIPTION']['DETAIL']['FROM_PREVIEW'] && !empty($arResult['PREVIEW_TEXT']))
        $arSections['DESCRIPTION']['VALUE'] = &$arResult['PREVIEW_TEXT'];

    if ($arVisual['OFFERS']['DESCRIPTION']['SHOW']) {
        foreach ($arResult['OFFERS'] as $arOffer) {
            $sDescription = $arOffer['DETAIL_TEXT'];

            if (empty($sDescription))
                $sDescription = $arSections['DESCRIPTION']['VALUE'];

            if (!empty($sDescription)) {
                $arSections['DESCRIPTION']['OFFERS'][$arOffer['ID']] = $sDescription;

                if (!$arSections['DESCRIPTION']['SHOW'])
                    $arSections['DESCRIPTION']['SHOW'] = true;
            }
        }

        unset($sDescription);
    }

    if (!empty($arSections['DESCRIPTION']['VALUE']))
        $arSections['DESCRIPTION']['SHOW'] = true;
}

if ($arSections['PROPERTIES']['SHOW']) {
    if (empty($arSections['PROPERTIES']['NAME']))
        $arSections['PROPERTIES']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_PROPERTIES_DETAIL_NAME_DEFAULT');
}

if ($arSections['ACCESSORIES']['SHOW']) {
    if (empty($arSections['ACCESSORIES']['NAME']))
        $arSections['ACCESSORIES']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_PRODUCTS_ACCESSORIES_NAME_DEFAULT');

    if ($arSections['ACCESSORIES']['VIEW'] === 'link') {
        if (!empty($arVisual['ACCESSORIES']['LINK']))
            $arSections['ACCESSORIES']['LINK'] = $arVisual['ACCESSORIES']['LINK'];
        else
            $arSections['ACCESSORIES']['SHOW'] = false;
    }
}

if ($arSections['STORES']['SHOW']) {
    if (empty($arSections['STORES']['NAME']))
        $arSections['STORES']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_STORES_NAME_DEFAULT');
}

if ($arSections['DOCUMENTS']['SHOW']) {
    if (empty($arSections['DOCUMENTS']['NAME']))
        $arSections['DOCUMENTS']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_DOCUMENTS_NAME_DEFAULT');
}

if ($arSections['VIDEO']['SHOW']) {
    if (empty($arSections['VIDEO']['NAME']))
        $arSections['VIDEO']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_VIDEO_NAME_DEFAULT');
}

if ($arSections['ARTICLES']['SHOW']) {
    if (empty($arSections['ARTICLES']['NAME']))
        $arSections['ARTICLES']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_ARTICLES_NAME_DEFAULT');
}

if ($arSections['REVIEWS']['SHOW']) {
    if (empty($arSections['REVIEWS']['NAME']))
        $arSections['REVIEWS']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_REVIEWS_NAME_DEFAULT');
}

if ($arSections['BUY']['SHOW']) {
    if (empty($arSections['BUY']['NAME']))
        $arSections['BUY']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_ADDITIONAL_BUY');
}

if ($arSections['PAYMENT']['SHOW']) {
    if (empty($arSections['PAYMENT']['NAME']))
        $arSections['PAYMENT']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_ADDITIONAL_PAYMENT');
}

if ($arSections['SHIPMENT']['SHOW']) {
    if (empty($arSections['SHIPMENT']['NAME']))
        $arSections['SHIPMENT']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_ADDITIONAL_SHIPMENT');
}

if ($arSections['ADDITIONAL_1']['SHOW']) {
    if (empty($arSections['ADDITIONAL_1']['NAME']))
        $arSections['ADDITIONAL_1']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_ADDITIONAL_ADDITIONAL_1');
}

if ($arSections['ADDITIONAL_2']['SHOW']) {
    if (empty($arSections['ADDITIONAL_2']['NAME']))
        $arSections['ADDITIONAL_2']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_ADDITIONAL_ADDITIONAL_2');
}

?>
<div class="catalog-element-sections-container catalog-element-additional-block" data-role="section">
    <div class="catalog-element-sections">
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-element-sections-tabs'
            ],
            'data' => [
                'role' => 'section.tabs',
                'sticky' => 'nulled'
            ]
        ]) ?>
            <div class="owl-carousel" data-role="scroll" data-navigation="false">
                <?php $bFirst = true ?>
                <?php foreach ($arSections as $arSection) {

                    if (!$arSection['SHOW'])
                        continue;

                ?>
                    <?php if ($arSection['VIEW'] === 'link') { ?>
                        <?= Html::tag('a', $arSection['NAME'], [
                            'class' => Html::cssClassFromArray([
                                'catalog-element-sections-tab' => true,
                            ], true),
                            'data' => [
                                'id' => $arSection['ID'],
                                'active' => 'false'
                            ],
                            'href' => $arSection['LINK'],
                            'target' => '_blank'
                        ]) ?>
                    <?php } else { ?>
                        <?= Html::tag('div', $arSection['NAME'], [
                            'class' => Html::cssClassFromArray([
                                'catalog-element-sections-tab' => true,
                                'intec-cl' => [
                                    'background' => $bFirst,
                                    'background-light-hover' => $bFirst,
                                    'border' => $bFirst,
                                    'border-light-hover' => $bFirst
                                ],
                            ], true),
                            'data' => [
                                'role' => 'section.tabs.item',
                                'id' => $arSection['ID'],
                                'active' => $bFirst ? 'true' : 'false'
                            ]
                        ]) ?>
                        <?php if ($bFirst) $bFirst = false ?>
                    <?php } ?>
                <?php } ?>
            </div>
        <?= Html::endTag('div') ?>
        <div class="catalog-element-sections-content" data-role="section.content">
            <?php $bFirst = true ?>
            <?php foreach ($arSections as $arSection) {

                if (!$arSection['SHOW'] || $arSection['VIEW'] === 'link')
                    continue;

            ?>
                <?= Html::beginTag('div', [
                    'class' => 'catalog-element-sections-content-item',
                    'data' => [
                        'role' => 'section.content.item',
                        'id' => $arSection['ID'],
                        'active' => $bFirst ? 'true' : 'false'
                    ]
                ]) ?>
                    <?php if (!empty($arSections['DESCRIPTION']['OFFERS']) && $arSection['TYPE'] === 'print') { ?>
                        <?php foreach ($arSections['DESCRIPTION']['OFFERS'] as $sKey => $sDescription) { ?>
                        <div class="catalog-element-sections-content-text" data-offer="<?= $sKey ?>">
                                <?= $sDescription ?>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <?php if ($arSection['TYPE'] === 'print') { ?>
                            <div class="catalog-element-sections-content-text">
                                <?= $arSection['VALUE'] ?>
                            </div>
                        <?php } else if ($arSection['TYPE'] === 'file')
                            include($arSection['VALUE']);
                        ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
                <?php if ($bFirst) $bFirst = false ?>
            <?php } ?>
        </div>
    </div>
</div>