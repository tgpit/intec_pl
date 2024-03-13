<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$sSite = $_REQUEST['site'];

if (empty($sSite) && !empty($_REQUEST['src_site'])) {
    $sSite = $_REQUEST['src_site'];
}

$arIBlocksTypes = CIBlockParameters::GetIBlockTypes();
$arIBlocks = [
    'ACTIVE' => 'Y',
    'SITE_ID' => $sSite
];

if (!empty($arCurrentValues['IBLOCK_TYPE']))
    $arIBlocks['TYPE'] = $arCurrentValues['IBLOCK_TYPE'];

$arIBlocks = Arrays::fromDBResult(CIBlock::GetList([
    'SORT' => 'ASC'
], $arIBlocks))->indexBy('ID');

$arIBlock = null;

if (!empty($arCurrentValues['IBLOCK_ID']))
    $arIBlock = $arIBlocks->get($arCurrentValues['IBLOCK_ID']);

$arParameters = [];
$arParameters['IBLOCK_TYPE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_INTEC_SEO_TAGS_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocksTypes,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

$arParameters['IBLOCK_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_INTEC_SEO_TAGS_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) {
        return [
            'key' => $arIBlock['ID'],
            'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
        ];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arIBlock)) {
    $arSections = Arrays::fromDBResult(CIBlockSection::GetList([
        'LEFT_MARGIN' => 'ASC'
    ], [
        'IBLOCK_ID' => $arIBlock['ID']
    ]));

    $arParameters['SECTION_ID'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_TAGS_SECTION_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $arSections->asArray(function ($iId, $arSection) {
            return [
                'key' => $arSection['ID'],
                'value' => '['.$arSection['ID'].'] '.$arSection['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

$arParameters['CACHE_TIME'] = [
    'DEFAULT' => 3600000
];

$arComponentParameters = [
    'GROUPS' => [],
    'PARAMETERS' => $arParameters
];