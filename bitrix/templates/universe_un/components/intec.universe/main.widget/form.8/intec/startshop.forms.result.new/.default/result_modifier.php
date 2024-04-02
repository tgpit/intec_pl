<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'FORM_ID' => null,
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'CONSENT_SHOW' => 'N',
    'CONSENT_URL' => null,
    'FORM_TITLE_SHOW' => 'N',
    'FORM_DESCRIPTION_SHOW' => 'N',
    'FORM_POSITION' => 'left',
    'FORM_ADDITIONAL_PICTURE_SHOW' => 'N',
    'FORM_ADDITIONAL_PICTURE_PATH' => null,
    'FORM_ADDITIONAL_PICTURE_VERTICAL' => 'center',
    'FORM_ADDITIONAL_PICTURE_SIZE' => 'contain',
    'FORM_BACKGROUND_PATH' => null,
    'FORM_BACKGROUND_PARALLAX_USE' => 'N',
    'FORM_BACKGROUND_PARALLAX_RATIO' => 10
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arMacros = [
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH,
    'TEMPLATE_PATH' => $this->GetFolder(),
    'SITE_DIR' => SITE_DIR
];

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') ? Properties::get('template-images-lazyload-stub') : null
    ],
    'CONSENT' => [
        'SHOW' => $arParams['CONSENT_SHOW'] === 'Y' && !empty($arParams['CONSENT_URL']),
        'URL' => StringHelper::replaceMacros($arParams['CONSENT_URL'], $arMacros),
        'CHECKED' => !empty($_POST['licenses_popup'])
    ],
    'FORM' => [
        'POSITION' => $arParams['FORM_POSITION'],
        'TITLE' => [
            'SHOW' => $arParams['FORM_TITLE_SHOW'] === 'Y',
            'VALUE' => $arResult['LANG'][LANGUAGE_ID]['NAME']
        ],
        'DESCRIPTION' => [
            'SHOW' => false,
            'VALUE' => ''
        ]
    ],
    'BACKGROUND' => [
        'PATH' => StringHelper::replaceMacros($arParams['FORM_BACKGROUND_PATH'], $arMacros),
        'PARALLAX' => [
            'USE' => $arParams['FORM_BACKGROUND_PARALLAX_USE'] === 'Y',
            'RATIO' => Type::toInteger($arParams['FORM_BACKGROUND_PARALLAX_RATIO'])
        ],
    ],
    'ADDITIONAL_PICTURE' => [
        'SHOW' => $arParams['FORM_ADDITIONAL_PICTURE_SHOW'] === 'Y' && !empty($arParams['FORM_ADDITIONAL_PICTURE_PATH']) && $arParams['FORM_POSITION'] != 'center',
        'PATH' => StringHelper::replaceMacros($arParams['FORM_ADDITIONAL_PICTURE_PATH'], $arMacros),
        'VERTICAL_ALIGN' => $arParams['FORM_ADDITIONAL_PICTURE_VERTICAL'],
        'SIZE' => $arParams['FORM_ADDITIONAL_PICTURE_SIZE']
    ],
    'CAPTCHA' => [
        'USE' => $arResult['USE_CAPTCHA'] == 'Y'
    ]
];

if ($arVisual['BACKGROUND']['PARALLAX']['USE']) {
    if ($arVisual['BACKGROUND']['PARALLAX']['RATIO'] < 0)
        $arVisual['BACKGROUND']['PARALLAX']['RATIO'] = 0;
    else if ($arVisual['BACKGROUND']['PARALLAX']['RATIO'] > 100)
        $arVisual['BACKGROUND']['PARALLAX']['RATIO'] = 100;

    $arVisual['BACKGROUND']['PARALLAX']['RATIO'] = (100 - $arVisual ['BACKGROUND']['PARALLAX']['RATIO']) / 100;
}

$arResult['VARIABLES'] = [
    'REQUEST_VARIABLE_ACTION' => Html::encode(ArrayHelper::getValue($arParams, 'REQUEST_VARIABLE_ACTION')),
    'FORM_VARIABLE_CAPTCHA_SID' => Html::encode(ArrayHelper::getValue($arParams, 'FORM_VARIABLE_CAPTCHA_SID')),
    'FORM_VARIABLE_CAPTCHA_CODE' => Html::encode(ArrayHelper::getValue($arParams, 'FORM_VARIABLE_CAPTCHA_CODE'))
];

$arResult['VISUAL'] = $arVisual;

unset($arMacros, $arVisual);