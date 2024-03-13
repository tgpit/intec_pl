<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arParams
 * @var array $arResult
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
    'PHONE_SHOW' => null,
    'ADDRESS_SHOW' => null,
    'EMAIL_SHOW' => null,
    'SCHEDULE_SHOW' => null,

], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') ? Properties::get('template-images-lazyload-stub') : null
    ],
    'PHONE' => [
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
    ]
];

$arResult['PICTURE'] = null;
$arResult['EMAIL'] = null;

if (!empty($arResult['IMAGE_ID']))
    $arResult['PICTURE'] = CFile::GetFileArray($arResult['IMAGE_ID']);

unset($arResult['IMAGE_ID']);

$arStore = CCatalogStore::GetList(
    ['ID' => 'ASC'],
    ['ID' => $arParams['STORE'], 'ACTIVE' => 'Y'],
    false,
    false,
    ['EMAIL']
);

$arStore = $arStore->GetNext();

if (!empty($arStore))
    $arResult = ArrayHelper::merge($arResult, $arStore);

if (!empty($arResult['PHONE'])) {
    $arResult['PHONE'] = [
        'VALUE' => StringHelper::replace($arResult['PHONE'], [
            '(' => '',
            ')' => '',
            ' ' => '',
            '-' => ''
        ]),
        'DISPLAY' => $arResult['PHONE']
    ];
} else {
    $arResult['PHONE'] = null;
}

$arResult['MAP'] = [
    'SHOW' =>
        (!empty($arResult['GPS_N']) || Type::isNumeric($arResult['GPS_N'])) &&
        (!empty($arResult['GPS_S']) || Type::isNumeric($arResult['GPS_S'])),
    'VENDOR' => $arResult['MAP'],
    'GPS' => [
        'N' => 0,
        'S' => 0
    ]
];

if ($arResult['MAP']['SHOW']) {
    $arResult['MAP']['GPS']['N'] = Type::toFloat(substr($arResult['GPS_N'],0,15));
    $arResult['MAP']['GPS']['S'] = Type::toFloat(substr($arResult['GPS_S'],0,15));
}

unset($arResult['GPS_N']);
unset($arResult['GPS_S']);

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

$arResult['VISUAL'] = $arVisual;