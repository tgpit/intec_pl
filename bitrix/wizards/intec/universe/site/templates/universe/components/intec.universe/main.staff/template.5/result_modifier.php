<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'SLIDER_NAV' => 'Y',
    'SLIDER_LOOP' => 'N',
    'SLIDER_CENTER' => 'N',
    'SLIDER_AUTO_USE' => 'N',
    'SLIDER_AUTO_TIME' => '5000',
    'SLIDER_AUTO_HOVER' => 'N',
    'LINK_USE' => 'N',
    'POSITION_SHOW' => 'N',
    'PROPERTY_POSITION' => null,
    'PROPERTY_PHONE' => null,
    'PHONE_SHOW' => 'N',
    'PROPERTY_EMAIL' => null,
    'EMAIL_SHOW' => 'N',
    'FORM_SHOW' => 'N',
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'WIDE' => $arParams['WIDE'] === 'Y',
    'SLIDER' => [
        'NAV' => $arParams['SLIDER_NAV'] === 'Y'
    ],
    'POSITION' => [
        'SHOW' => $arParams['POSITION_SHOW'] === 'Y'
    ],
    'PHONE' => [
        'SHOW' => $arParams['PHONE_SHOW'] === 'Y'
    ],
    'EMAIL' => [
        'SHOW' => $arParams['EMAIL_SHOW'] === 'Y'
    ],
    'LINK' => [
        'USE' => ArrayHelper::getValue($arParams, 'LINK_USE') == 'Y'
    ]
];

if (defined('EDITOR'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

$arResult['VISUAL'] = ArrayHelper::merge($arVisual, $arResult['VISUAL']);

unset($arVisual);

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [];

    if (!empty($arParams['PROPERTY_POSITION'])) {
        $arItem['DATA']['POSITION'] = [
            'VALUE' => '',
            'SHOW' => false
        ];

        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_POSITION'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            $arItem['DATA']['POSITION']['VALUE'] = $arProperty;
        }

        if (!empty($arItem['DATA']['POSITION']['VALUE']))
            $arItem['DATA']['POSITION']['SHOW'] = $arVisual['POSITION']['SHOW'];
    }

    $arProperties = [
        'PHONE',
        'EMAIL'
    ];

    foreach ($arProperties as $sProperty) {
        if (!empty($arParams['PROPERTY_'.$sProperty])) {
            $arItem['DATA'][$sProperty] = [
                'VALUE' => '',
                'SHOW' => false
            ];

            $arProperty = ArrayHelper::getValue($arItem, [
                'PROPERTIES',
                $arParams['PROPERTY_'.$sProperty],
                'VALUE'
            ]);

            if (!empty($arProperty)) {
                if (Type::isArray($arProperty)) {
                    $arItem['DATA'][$sProperty]['VALUE'] = $arProperty;
                } else {
                    $arItem['DATA'][$sProperty]['VALUE'] = [
                        '0' => $arProperty
                    ];
                }
            }

            if (!empty($arItem['DATA'][$sProperty]['VALUE']))
                $arItem['DATA'][$sProperty]['SHOW'] = $arVisual[$sProperty]['SHOW'];
        }
    }
}

$arForm = [
    'SHOW' => $arParams['FORM_SHOW'] === 'Y',
    'ID' => $arParams['FORM_ID'],
    'TEMPLATE' => $arParams['FORM_TEMPLATE'],
    'FIELD' => $arParams['FORM_PROPERTY_FIELD'],
    'TITLE' => $arParams['FORM_TITLE'],
    'CONSENT' => $arParams['FORM_CONSENT'],
    'BUTTON' => $arParams['FORM_BUTTON']
];

if ($arForm['SHOW'])
    if (empty($arForm['ID']) || empty($arForm['TEMPLATE']) || empty($arForm['FIELD']))
        $arForm['SHOW'] = false;

$arResult['FORM'] = $arForm;

unset($arForm);