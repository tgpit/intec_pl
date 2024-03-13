<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 * @var string $siteTemplate
 */

$sComponent = 'bitrix:support.wizard';
$sTemplate = 'template.';

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

$sPrefix = 'CLAIMS_';
$sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix.'TEMPLATE');
$sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

if (!empty($sTemplate))
    $sTemplate = 'template.'.$sTemplate;

$arTemplateParameters[$sPrefix.'TEMPLATE'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_'.$sPrefix.'TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($sTemplate)) {
    $arExceptionParameters = [
        'IBLOCK_TYPE',
        'IBLOCK_ID',
        'PROPERTY_FIELD_TYPE',
        'PROPERTY_FIELD_VALUES',
        'INCLUDE_IBLOCK_INTO_CHAIN',
        'SET_PAGE_TITLE',
        'TEMPLATE_TYPE',
        'SHOW_RESULT',
        'SHOW_COUPON_FIELD',
        'SECTIONS_TO_CATEGORIES',
        'TICKET_EDIT_TEMPLATE',
        'TICKET_LIST_URL'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($sKey, &$arParameter) use (&$arExceptionParameters) {
            if (ArrayHelper::isIn($sKey, $arExceptionParameters))
                return false;

            $arParameter['PARENT'] = 'ADDITIONAL_SETTINGS';
            $arParameter['NAME'] = Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_CLAIMS').' '.$arParameter['NAME'];

            return true;
        },
        Component::PARAMETERS_MODE_BOTH
    ));
}

unset($sComponent, $sTemplate, $sPrefix, $arTemplates);