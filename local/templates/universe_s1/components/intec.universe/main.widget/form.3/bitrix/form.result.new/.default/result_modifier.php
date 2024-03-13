<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'WEB_FORM_ID' => null,
    'SETTINGS_USE' => 'N',
    'WEB_FORM_CONSENT_SHOW' => 'N',
    'WEB_FORM_CONSENT_LINK' => null,
    'WEB_FORM_TITLE_SHOW' => 'N',
    'WEB_FORM_TITLE_POSITION' => 'center',
    'WEB_FORM_DESCRIPTION_SHOW' => 'N',
    'WEB_FORM_DESCRIPTION_POSITION' => 'center',
    'WEB_FORM_THEME' => 'dark',
    'WEB_FORM_BACKGROUND_USE' => 'N',
    'WEB_FORM_BACKGROUND_COLOR' => 'theme',
    'WEB_FORM_BACKGROUND_COLOR_CUSTOM' => null,
    'WEB_FORM_BACKGROUND_OPACITY' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'TITLE' => [
        'SHOW' => $arParams['WEB_FORM_TITLE_SHOW'] === 'Y' && !empty($arResult['FORM_TITLE']),
        'POSITION' => ArrayHelper::fromRange(['center', 'left', 'right'], $arParams['WEB_FORM_TITLE_POSITION'])
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['WEB_FORM_DESCRIPTION_SHOW'] === 'Y' && !empty($arResult['FORM_DESCRIPTION']),
        'POSITION' => ArrayHelper::fromRange(['center', 'left', 'right'], $arParams['WEB_FORM_DESCRIPTION_POSITION'])
    ],
    'THEME' => ArrayHelper::fromRange(['dark', 'light'], $arParams['WEB_FORM_THEME']),
    'BUTTON' => [
        'POSITION' => ArrayHelper::fromRange(['center', 'left', 'right'], $arParams['WEB_FORM_BUTTON_POSITION'])
    ],
    'BACKGROUND' => [
        'USE' => $arParams['WEB_FORM_BACKGROUND_USE'] === 'Y',
        'COLOR' => [
            'VALUE' => ArrayHelper::fromRange(['theme', 'custom'], $arParams['WEB_FORM_BACKGROUND_COLOR']),
            'CUSTOM' => null
        ],
        'OPACITY' => 1
    ],
    'CONSENT' => [
        'SHOW' => $arParams['WEB_FORM_CONSENT_SHOW'],
        'LINK' => $arParams['WEB_FORM_CONSENT_LINK'],
        'CHECKED' => !empty($_POST['licenses_popup'])
    ]
];

if ($arVisual['BACKGROUND']['USE']) {
    if ($arVisual['BACKGROUND']['COLOR']['VALUE'] == 'custom') {
        if (!empty($arParams['WEB_FORM_BACKGROUND_COLOR_CUSTOM']))
            $arVisual['BACKGROUND']['COLOR']['CUSTOM'] = $arParams['WEB_FORM_BACKGROUND_COLOR_CUSTOM'];
    }

    if (!empty($arParams['WEB_FORM_BACKGROUND_OPACITY'])) {
        $opacity = StringHelper::replace($arParams['WEB_FORM_BACKGROUND_OPACITY'], ['%' => '']);

        if (Type::isNumeric($opacity)) {
            if ($opacity >= 0 && $opacity <= 100)
                $opacity = 1 - $opacity / 100;
            $arVisual['BACKGROUND']['OPACITY'] = $opacity;
        }

        unset($opacity);
    }
}

if ($arVisual['CONSENT']['SHOW']) {
    if (!empty($arVisual['CONSENT']['LINK']))
        $arVisual['CONSENT']['LINK'] = StringHelper::replaceMacros($arVisual['CONSENT']['LINK'], [
            'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH,
            'TEMPLATE_PATH' => $this->GetFolder(),
            'SITE_DIR' => SITE_DIR
        ]);
    else
        $arVisual['CONSENT']['SHOW'] = false;
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);