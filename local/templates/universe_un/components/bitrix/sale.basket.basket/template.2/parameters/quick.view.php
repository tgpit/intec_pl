<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Catalog;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use Bitrix\Main\Loader;

/**
 * @var array $arCurrentValues
 * @var string $componentName
 * @var string $componentTemplate
 * @var string $siteTemplate
 */

$sPrefix = 'QUICK_VIEW_';

$sComponent = 'bitrix:catalog.element';
$sTemplate = 'quick.view.';

$arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
    $sComponent,
    $siteTemplate
))->asArray(function ($iIndex, $arTemplate) use (&$sTemplate) {
    if (!StringHelper::startsWith($arTemplate['NAME'], $sTemplate))
        return ['skip' => true];

    $sName = StringHelper::cut(
        $arTemplate['NAME'],
        StringHelper::length($sTemplate)
    );

    return [
        'key' => $sName,
        'value' => $sName
    ];
});

$sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
$sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

if (!empty($sTemplate))
    $sTemplate = 'quick.view.'.$sTemplate;

$arTemplateParameters[$sPrefix.'TEMPLATE'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_QUICK_VIEW_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'REFRESH' => 'Y'
];

$arTemplateParameters[$sPrefix.'IBLOCK_TYPE'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_QUICK_VIEW_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'MULTIPLE' => 'N',
    'VALUES' => CIBlockParameters::GetIBlockTypes(),
    'REFRESH' => 'Y'
];

if ($arCurrentValues[$sPrefix.'IBLOCK_TYPE'] !== '') {

    $offersIblock = [];
    if (Loader::includeModule('catalog')) {
        $iterator = Catalog\CatalogIblockTable::getList([
            'select' => ['IBLOCK_ID'],
            'filter' => ['!=PRODUCT_IBLOCK_ID' => 0]
        ]);
        while ($row = $iterator->fetch())
            $offersIblock[$row['IBLOCK_ID']] = true;
        unset($row, $iterator);
    }

    $arIBlock = [];

    $iblockFilter = (
    !empty($arCurrentValues[$sPrefix.'IBLOCK_TYPE'])
        ? ['TYPE' => $arCurrentValues[$sPrefix.'IBLOCK_TYPE'], 'ACTIVE' => 'Y']
        : ['ACTIVE' => 'Y']
    );

    $rsIBlock = CIBlock::GetList(['SORT' => 'ASC'], $iblockFilter);

    while ($arr = $rsIBlock->Fetch())
    {
        $id = (int)$arr['ID'];
        if (isset($offersIblock[$id]))
            continue;
        $arIBlock[$id] = '['.$id.'] '.$arr['NAME'];
    }

    unset($id, $arr, $rsIBlock, $iblockFilter, $offersIblock);

    $arTemplateParameters[$sPrefix.'IBLOCK_ID'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_QUICK_VIEW_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'N',
        'DEFAULT' => 'N',
        'VALUES' => $arIBlock,
        'REFRESH' => 'Y'
    ];
}

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['sort' => 'asc'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]));

    $arTemplateParameters[$sPrefix . 'PROPERTY_CODE'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_QUICK_VIEW_PROPERTY_CODE'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'Y',
        'ADDITIONAL_VALUES' => 'Y',
        'VALUES' => $arProperties->asArray(function ($iIndex, $arProperty) {
            $sCode = $arProperty['CODE'];

            if (empty($sCode))
                $sCode = $arProperty['ID'];

            return [
                'key' => $sCode,
                'value' => '['.$sCode.'] '.$arProperty['NAME']
            ];
        })
    ];
}

if (!empty($sTemplate)) {
    $arQuickViewParametersUsed = [
        'PROPERTY_CODE',
        'OFFERS_FIELD_CODE',
        'OFFERS_PROPERTY_CODE',
        'OFFERS_SORT_FIELD',
        'OFFERS_SORT_ORDER',
        'OFFERS_SORT_FIELD2',
        'OFFERS_SORT_ORDER2',
        'OFFERS_LIMIT',
        'HIDE_NOT_AVAILABLE_OFFERS',
        'PRICE_CODE',
        'USE_PRICE_COUNT',
        'SHOW_PRICE_COUNT',
        'PRICE_VAT_INCLUDE',
        'PRICE_VAT_SHOW_VALUE',
        'CONVERT_CURRENCY',
        'CURRENCY_ID',
        'PROPERTY_MARKS_HIT',
        'PROPERTY_MARKS_NEW',
        'PROPERTY_MARKS_RECOMMEND',
        'PROPERTY_MARKS_SHARE',
        'PROPERTY_PICTURES',
        'MARKS_SHOW',
        'QUANTITY_SHOW',
        'QUANTITY_MODE',
        'QUANTITY_BOUNDS_FEW',
        'QUANTITY_BOUNDS_MANY',
        'DESCRIPTION_SHOW',
        'DESCRIPTION_MODE',
        'PROPERTY_TEXT',
        'TEXT_SHOW',
        'GALLERY_PANEL',
        'GALLERY_PREVIEW',
        'INFORMATION_PAYMENT',
        'PAYMENT_URL',
        'INFORMATION_SHIPMENT',
        'SHIPMENT_URL'
    ];
    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($sKey, &$arParameter) use (&$arQuickViewParametersUsed) {
            if (ArrayHelper::isIn($sKey, $arQuickViewParametersUsed)) {
                $arParameter['PARENT'] = 'ADDITIONAL_SETTINGS';
                $arParameter['NAME'] = Loc::getMessage('C_BASKET_TEMPLATE_DEFAULT_2_QUICK_VIEW') . ' ' . $arParameter['NAME'];

                return true;
            }
        },
        Component::PARAMETERS_MODE_BOTH
    ));
}
