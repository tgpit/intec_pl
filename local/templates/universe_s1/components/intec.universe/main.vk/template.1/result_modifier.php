<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'ACCESS_TOKEN' => null,
    'USER_ID' => null,
    'DOMAIN' => null,
    'ITEMS_OFFSET' => null,
    'ITEMS_COUNT' => null,
    'FILTER' => null,
    'DATE_FORMAT' => 'd.m.Y',
    'CACHE_TYPE' => null,
    'CACHE_TIME' => null,
    'LAZYLOAD_USE' => 'N',
    'HEADER_SHOW' => 'N',
    'HEADER_TITLE' => null,
    'HEADER_DESCRIPTION' => null,
    'MAIN_URL_LIST_SHOW' => 'N',
    'MAIN_URL_LIST_URL' => null,
    'MAIN_URL_LIST_BLANK' => 'N',
    'MAIN_URL_LIST_TEXT' => null,
    'ITEMS_COLUMNS' => 4,
    'ITEM_DESCRIPTION_TRUNCATE_USE' => 'N',
    'ITEM_DESCRIPTION_TRUNCATE_LIMIT' => 20,
    'ITEM_URL_USE' => 'N',
    'ITEM_URL_BLANK' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'HEADER' => [
        'SHOW' => $arParams['HEADER_SHOW'] === 'Y',
        'TITLE' => [
            'SHOW' => !empty($arParams['HEADER_TITLE']),
            'VALUE' => $arParams['HEADER_TITLE']
        ],
        'DESCRIPTION' => [
            'SHOW' => !empty($arParams['HEADER_DESCRIPTION']),
            'VALUE' => $arParams['HEADER_DESCRIPTION']
        ]
    ],
    'ITEMS' => [
        'COLUMNS' => ArrayHelper::fromRange([4, 1, 2, 3, 5], $arParams['ITEMS_COLUMNS'])
    ],
    'ITEM' => [
        'URL' => [
            'USE' => $arParams['ITEM_URL_USE'] === 'Y',
            'BLANK' => $arParams['ITEM_URL_USE'] === 'Y' && $arParams['ITEM_URL_BLANK'] === 'Y',
        ],
        'DESCRIPTION' => [
            'TRUNCATE' => [
                'USE' => $arParams['ITEM_DESCRIPTION_TRUNCATE_USE'] === 'Y' && Type::toInteger($arParams['ITEM_DESCRIPTION_TRUNCATE_LIMIT']) > 0,
                'LIMIT' => Type::toInteger($arParams['ITEM_DESCRIPTION_TRUNCATE_LIMIT'])
            ]
        ]
    ],
    'URL' => [
        'LIST' => [
            'SHOW' => $arParams['MAIN_URL_LIST_SHOW'] === 'Y',
            'TEXT' => $arParams['MAIN_URL_LIST_TEXT'],
            'VALUE' => $arParams['MAIN_URL_LIST_URL'],
            'BLANK' => $arParams['MAIN_URL_LIST_BLANK'] === 'Y'
        ]
    ]
];

if (
    $arVisual['HEADER']['SHOW'] &&
    !$arVisual['HEADER']['TITLE']['SHOW'] &&
    !$arVisual['HEADER']['DESCRIPTION']['SHOW']
)
    $arVisual['HEADER']['SHOW'] = false;

if ($arVisual['HEADER']['SHOW'] && $arVisual['URL']['LIST']['SHOW'])
    if (empty($arVisual['URL']['LIST']['VALUE']))
        $arVisual['URL']['LIST']['SHOW'] = false;

foreach ($arResult['ITEMS'] as &$item) {
    if (!empty($item['TEXT'])) {
        if ($arVisual['ITEM']['DESCRIPTION']['TRUNCATE']['USE'])
            $item['TEXT'] = Html::stripTags(
                StringHelper::truncateWords($item['TEXT'], $arVisual['ITEM']['DESCRIPTION']['TRUNCATE']['LIMIT'])
            );
    }
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual, $item);