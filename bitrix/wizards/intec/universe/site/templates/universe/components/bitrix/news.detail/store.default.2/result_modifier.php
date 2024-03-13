<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\RegExp;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$arParams = ArrayHelper::merge([
    'MAP_VENDOR' => 'yandex',
    'DESCRIPTION_SHOW' => 'N',
    'PICTURE_SHOW' => 'N',
    'PICTURE_SOURCE' => 'preview',
    'PROPERTY_PICTURES' => null,
    'FORM_ID' => null,
    'FORM_TEMPLATE' => '.default',
    'FORM_FIELD' => null,
    'FORM_TITLE' => null,
    'CONSENT' => null,
    'PROPERTY_PHONE' => null,
    'PROPERTY_ADDRESS' => null,
    'PROPERTY_EMAIL' => null,
    'PROPERTY_SCHEDULE' => null,
    'FORM_SHOW' => 'N'

], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') ? Properties::get('template-images-lazyload-stub') : null
    ],
    'PHONES' => [
        'SHOW' => $arParams['PHONE_SHOW'] === 'Y'
    ],
    'ADDRESS' => [
        'SHOW' => $arParams['ADDRESS_SHOW'] === 'Y'
    ],
    'EMAIL' => [
        'SHOW' => $arParams['EMAIL_SHOW'] === 'Y'
    ],
    'SCHEDULE' => [
        'SHOW' => $arParams['SCHEDULE_SHOW'] === 'Y'
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y'
    ],
    'SOCIAL_SERVICES' => [
        'SHOW' => $arParams['SOCIAL_SERVICES_SHOW'] === 'Y'
    ],
    'PICTURE' => [
        'SHOW' => $arParams['PICTURE_SHOW'] === 'Y',
        'SOURCE' => ArrayHelper::fromRange(['preview', 'detail', 'property'], $arParams['PICTURE_SOURCE'])
    ]
];

if ($arParams['SETTINGS_USE'] == 'Y')
    include(__DIR__.'/parts/settings.php');

$arResult['TITLE'] = null;
$arParams['MAP_VENDOR'] = ArrayHelper::fromRange([
    'google',
    'yandex'
], $arParams['MAP_VENDOR']);

$mapId = $arParams['MAP_ID'];
$mapIdLength = StringHelper::length($mapId);
$mapIdExpression = new RegExp('^[A-Za-z_][A-Za-z01-9_]*$');

if ($mapIdLength <= 0 || $mapIdExpression->isMatch($mapId))
    $arParams['MAP_ID'] = 'MAP_'.RandString();


$arResult['PHONES'] = [];
$arPhones = $arResult['PROPERTIES'][$arParams['PROPERTY_PHONE']]['VALUE'];

if (!empty($arPhones)) {
    if (Type::isArray($arPhones)){
        foreach ($arPhones as $sPhone) {
            $arResult['PHONES'][] = [
                'DISPLAY' => $sPhone,
                'VALUE' => StringHelper::replace(
                    $sPhone, [
                        '-' => '',
                        ' ' => '',
                        '(' => '',
                        ')' => ''
                    ]
                )
            ];
        }
    } else {
        $arResult['PHONES'][] = [
            'DISPLAY' => $arPhones,
            'VALUE' => StringHelper::replace(
                $arPhones, [
                    '-' => '',
                    ' ' => '',
                    '(' => '',
                    ')' => ''
                ]
            )
        ];
    }
}

if (empty($arResult['PHONES']))
    $arVisual['PHONES']['SHOW'] = false;

$arResult['ADDRESS'] = $arResult['PROPERTIES'][$arParams['PROPERTY_ADDRESS']]['VALUE'];

if (empty($arResult['ADDRESS']))
    $arVisual['ADDRESS']['SHOW'] = false;

$arResult['EMAIL'] = $arResult['PROPERTIES'][$arParams['PROPERTY_EMAIL']]['VALUE'];

if (empty($arResult['EMAIL']))
    $arVisual['EMAIL']['SHOW'] = false;

$arSchedule = $arResult['PROPERTIES'][$arParams['PROPERTY_SCHEDULE']];
$arResult['SCHEDULE'] = [];

if (!empty($arSchedule['VALUE'])) {
    if (Type::isArray($arSchedule['VALUE'])) {
        foreach ($arSchedule['VALUE'] as $key => $sSchedule) {
            $arResult['SCHEDULE'][] = $arSchedule['DESCRIPTION'][$key].' '.$sSchedule;
        }
    } else {
        $arResult['SCHEDULE'][] = $sSchedule;
    }

}

if (empty($arResult['SCHEDULE']))
    $arVisual['SCHEDULE']['SHOW'] = false;

$arResult['DESCRIPTION'] = null;
if (!empty($arResult['DETAIL_TEXT'])) {
    $arResult['DESCRIPTION'] = $arResult['DETAIL_TEXT'];
} else if (!empty($arResult['PREVIEW_TEXT'])) {
    $arResult['DESCRIPTION'] = $arResult['PREVIEW_TEXT'];
}

$arResult['SOCIAL_SERVICES'] = [
    'VK' => [
        'LINK' => $arParams['SOCIAL_SERVICES_VK'],
        'ICON' => '<i class="glyph-icon-vk"></i>'
    ],
    'FACEBOOK' => [
        'LINK' => $arParams['SOCIAL_SERVICES_FACEBOOK'],
        'ICON' => '<i class="glyph-icon-facebook"></i>'
    ],
    'INSTAGRAM' => [
        'LINK' => $arParams['SOCIAL_SERVICES_INSTAGRAM'],
        'ICON' => '<i class="glyph-icon-instagram"></i>'
    ],
    'TWITTER' => [
        'LINK' => $arParams['SOCIAL_SERVICES_TWITTER'],
        'ICON' => '<i class="glyph-icon-twitter"></i>'
    ],
    'SKYPE' => [
        'LINK' => $arParams['SOCIAL_SERVICES_SKYPE'],
        'ICON' => '<i class="fab fa-skype"></i>'
    ],
    'YOUTUBE' => [
        'LINK' => $arParams['SOCIAL_SERVICES_YOUTUBE'],
        'ICON' => '<i class="fab fa-youtube"></i>'
    ],
    'OK' => [
        'LINK' => $arParams['SOCIAL_SERVICES_OK'],
        'ICON' => '<i class="fab fa-odnoklassniki"></i>'
    ]
];

$arForm = [
    'SHOW' => false,
    'ID' => $arParams['FORM_ID'],
    'TEMPLATE' => $arParams['FORM_TEMPLATE'],
    'FIELD' => $arParams['FORM_FIELD'],
    'TITLE' => $arParams['FORM_TITLE'],
    'CONSENT' => $arParams['CONSENT']
];

if (!empty($arForm['ID']) && !empty($arForm['TEMPLATE']) && $arParams['FORM_SHOW'] === 'Y')
    $arForm['SHOW'] = true;

$arResult['FORM'] = $arForm;

unset($arForm);

include(__DIR__.'/modifiers/pictures.php');

$arResult['VISUAL'] = $arVisual;
?>