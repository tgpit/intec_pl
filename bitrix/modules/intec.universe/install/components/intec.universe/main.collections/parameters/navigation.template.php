<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\StringHelper;

/**
 * @var array $arCurrentValues
 */

$arNavigationTemplates = Arrays::from(CComponentUtil::GetTemplatesList('bitrix:main.pagenavigation'));

if ($arCurrentValues['NAVIGATION_MODE'] === 'standard')
    $arNavigationTemplates = $arNavigationTemplates->asArray(function ($key, $arValue) {
        if (StringHelper::startsWith($arValue['NAME'], 'lazy.'))
            return ['skip' => true];

        $sName = $arValue['NAME'];

        if (!empty($arValue['TEMPLATE']))
            $sName = $sName.' ('.$arValue['TEMPLATE'].')';

        return [
            'key' => $arValue['NAME'],
            'value' => $sName
        ];
    });
else
    $arNavigationTemplates = $arNavigationTemplates->asArray(function ($key, $arValue) {
        if (StringHelper::startsWith($arValue['NAME'], 'lazy.')) {
            $sName = $arValue['NAME'];

            if (!empty($arValue['TEMPLATE']))
                $sName = $sName.' ('.$arValue['TEMPLATE'].')';

            return [
                'key' => $arValue['NAME'],
                'value' => $sName
            ];
        }

        return ['skip' => true];
    });

$arParameters['NAVIGATION_TEMPLATE'] = [
    'PARENT' => 'NAVIGATION',
    'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_NAVIGATION_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arNavigationTemplates,
    'DEFAULT' => 'lazy.2'
];