<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arBlock
 */

$GLOBALS['arServicesServicesFilter'] = [
    'ID' => $arBlock['IBLOCK']['ELEMENTS']
];

?>
<div class="catalog-element-services widget">
    <div class="catalog-element-services-wrapper intec-content intec-content-visible">
        <div class="catalog-element-services-wrapper-2 intec-content-wrapper">
            <?php if (!empty($arBlock['HEADER']['VALUE'])) { ?>
                <div class="catalog-element-services-header widget-header">
                    <?= Html::tag('div', $arBlock['HEADER']['VALUE'], [
                        'class' => [
                            'widget-title',
                            'align-'.$arBlock['HEADER']['POSITION']
                        ]
                    ]) ?>
                </div>
            <?php } ?><? ?>
            <div class="catalog-element-services-content widget-content">
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:catalog.section',
                    'services.small.1',
                    array(
                        'IBLOCK_TYPE' => $arBlock['IBLOCK']['TYPE'],
                        'IBLOCK_ID' => $arBlock['IBLOCK']['ID'],
                        'SECTION_USER_FIELDS' => array(),
                        'SHOW_ALL_WO_SECTION' => 'Y',
                        'FILTER_NAME' => 'arServicesServicesFilter',
                        'PROPERTY_PRICE' => $arBlock['PROPERTIES']['PRICE']['BASE']['VALUE'],
                        'PROPERTY_PRICE_OLD' => $arBlock['PROPERTIES']['PRICE']['OLD']['VALUE'],
                        'PRICE_OLD_SHOW' => $arBlock['PROPERTIES']['PRICE']['OLD']['SHOW'],
                        'CURRENCY' => $arBlock['PRICE']['CURRENCY'],
                        'PROPERTY_CURRENCY' => $arBlock['PROPERTIES']['PRICE']['CURRENCY'],
                        'PRICE_FORMAT' => $arBlock['PRICE']['FORMAT'],
                        'PROPERTY_PRICE_FORMAT' => $arBlock['PROPERTIES']['PRICE']['PRICE_FORMAT'],
                        'PROPERTY_CODE' => [
                            0 => $arBlock['PROPERTIES']['PRICE']['BASE']['VALUE'],
                            1 => $arBlock['PROPERTIES']['PRICE']['OLD']['VALUE']
                        ],
                        'PROPERTY_CODE_MOBILE' => [
                            0 => $arBlock['PROPERTIES']['PRICE']['BASE']['VALUE'],
                            1 => $arBlock['PROPERTIES']['PRICE']['OLD']['VALUE']
                        ],
                        'COLUMNS' => 4,
                        'BORDERS' => 'Y',
                        'POSITION' => 'left',
                        'SIZE' => 'small',
                        'SLIDER_USE' => 'N',
                        'WIDE' => 'N',
                        'SETTINGS_USE' => 'N',
                        'LAZYLOAD_USE' => $arResult['LAZYLOAD']['USE'] ? 'Y' : 'N'
                    ),
                    $component
                ) ?>
            </div>
        </div>
    </div>
</div>