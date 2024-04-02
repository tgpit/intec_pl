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
    'POSITION_SHOW' => 'N',
    'PROPERTY_POSITION' => null,
    'LINK_USE' => 'N',
    'COLUMNS' => '3',
    'PREVIEW_SHOW' => 'N',
    'PICTURE_SIZE' => 'middle',
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'COLUMNS' => ArrayHelper::fromRange(['2', '3'], $arParams['COLUMNS']),
    'POSITION' => [
        'SHOW' => $arParams['POSITION_SHOW'] === 'Y'
    ],
    'PREVIEW' => [
        'SHOW' => $arParams['PREVIEW_SHOW'] === 'Y'
    ],
    'PICTURE' => [
        'SIZE' => ArrayHelper::fromRange(['middle', 'big'], $arParams['PICTURE_SIZE'])
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
}