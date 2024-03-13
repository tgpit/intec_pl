<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

$arPrefixes = [
    'QUICK_VIEW_',
    'LIST_TEXT_SECTION_',
    'LIST_LIST_SECTION_',
    'LIST_TILE_SECTION_',
    'DETAIL_'
];

$bTimerShow = false;

foreach ($arPrefixes as $sPrefix) {
    if ($arCurrentValues[$sPrefix.'TIMER_SHOW'] === 'Y') {
        $bTimerShow = true;
        break;
    }
}

if ($bTimerShow) {
    $sPrefix = 'CATALOG_TIMER_';
    $sComponent = 'intec.universe:product.timer';
    $sTemplate = 'template.1';

    $arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
        $sComponent,
        $siteTemplate
    ))->asArray(function ($key, $arTemplate) {
        return [
            'key' => $arTemplate['NAME'],
            'value' => $arTemplate['NAME']
        ];
    });

    if (ArrayHelper::isIn($sTemplate, $arTemplates)) {
        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            $sComponent,
            $sTemplate,
            $siteTemplate,
            $arCurrentValues,
            $sPrefix,
            function ($key, &$arParameter) {
                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_CATALOG_1_CATALOG_TIMER').' '.$arParameter['NAME'];
                $arParameter['PARENT'] = 'ADDITIONAL_SETTINGS';

                return true;
            },
            Component::PARAMETERS_MODE_BOTH
        ));
    }

    unset($sComponent, $sTemplate, $sPrefix, $arTemplates);
}