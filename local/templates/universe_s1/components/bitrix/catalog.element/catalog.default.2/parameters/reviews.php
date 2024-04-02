<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 * @var string $siteTemplate
 */

$sComponent = 'intec.universe:reviews';
$sTemplate = 'template.1';
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

if (ArrayHelper::isIn($sTemplate, $arTemplates)) {
    $arReviewsPropertiesCommon = [
        'IBLOCK_ID',
        'IBLOCK_TYPE',
        'MODE',
        'PROPERTY_ID',
        'ID',
        'SETTINGS_USE',
        'LAZYLOAD_USE',
        'CAPTCHA_USE',
        'CACHE_TYPE',
        'CACHE_TIME',
        'CACHE_NOTES'
    ];

    $arCurrentValues['DETAIL_REVIEWS_IBLOCK_TYPE'] = $arParams['REVIEWS_IBLOCK_TYPE'];
    $arCurrentValues['DETAIL_REVIEWS_IBLOCK_ID'] = $arParams['REVIEWS_IBLOCK_ID'];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        $sComponent,
        $sTemplate,
        $siteTemplate,
        $arCurrentValues,
        $sPrefix,
        function ($key, &$arParameter) use ($arReviewsPropertiesCommon) {
            if (ArrayHelper::isIn($key, $arReviewsPropertiesCommon))
                return false;

            $arParameter['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_REVIEWS').' '.$arParameter['NAME'];
            $arParameter['PARENT'] = 'DETAIL_SETTINGS';

            return true;
        },
        Component::PARAMETERS_MODE_BOTH
    ));

    unset($arReviewsPropertiesCommon);
}

unset($sComponent, $sTemplate, $sPrefix, $arTemplates);
