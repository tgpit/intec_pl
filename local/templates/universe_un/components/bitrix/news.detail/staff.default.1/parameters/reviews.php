<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

$sComponent = 'intec.universe:main.reviews';
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

$sPrefix = 'REVIEWS_';
$sTemplate = ArrayHelper::getValue($arCurrentValues, $sPrefix . 'TEMPLATE');
$sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

if (!empty($sTemplate))
    $sTemplate = 'template.' . $sTemplate;

$arTemplateParameters[$sPrefix . 'HEADER_TEXT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_' . $sPrefix . 'HEADER_TEXT'),
    'TYPE' => 'STRING'
];

$arTemplateParameters[$sPrefix.'TEMPLATE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_' . $sPrefix . 'TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($sTemplate)) {
    $arExcluded = [
        'SETTINGS_USE',
        'LAZYLOAD_USE'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($key, &$value) use ($arExcluded) {
            if (ArrayHelper::isIn($key, $arExcluded))
                return false;

            $value['NAME'] = Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_REVIEWS') . ' ' . $value['NAME'];
            $value['PARENT'] = 'DETAIL_SETTINGS';

            return true;
        },
        Component::PARAMETERS_MODE_TEMPLATE
    ));

    unset($arExcluded);
}

unset($sComponent, $sTemplate, $sPrefix, $arTemplates);
