<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 * @var array $arCurrentValues
 */

$sComponent = 'intec.universe:reviews';
$sTemplate = 'template.3';
$sPrefix = 'REVIEWS_';

$arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
    $sComponent,
    $siteTemplate
))->asArray(function ($key, $arTemplate) {
    return [
        'key' => $key,
        'value' => $arTemplate['NAME']
    ];
});

$arReviewsPropertiesCommon = [
    'ID',
    'DATE_FORMAT',
    'SETTINGS_USE',
    'LAZYLOAD_USE',
    'CACHE_TYPE',
    'CACHE_TIME',
    'CACHE_NOTES'
];

if (ArrayHelper::isIn($sTemplate, $arTemplates)) {
    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($key, &$arParameter) use ($arReviewsPropertiesCommon) {
            if (ArrayHelper::isIn($key, $arReviewsPropertiesCommon))
                return false;

            $arParameter['NAME'] = Loc::getMessage('N_PROJECTS_PARAMETERS_REVIEWS').' '.$arParameter['NAME'];
            $arParameter['PARENT'] = 'DETAIL_SETTINGS';

            return true;
        },
        Component::PARAMETERS_MODE_BOTH
    ));

    unset($arReviewsPropertiesCommon);
}

unset($sComponent, $sTemplate, $sPrefix, $arTemplates);

$arTemplateParameters['REVIEWS_ALLOW_LINK'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'TYPE' => 'CHECKBOX',
    'NAME' => Loc::getMessage('N_PROJECTS_PARAMETERS_REVIEWS_ALLOW_LINK')
];
