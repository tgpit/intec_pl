<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\StringHelper;
use intec\template\Properties;
use intec\core\helpers\FileHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

$arParams = ArrayHelper::merge([
    'HEADER_SHOW' => 'N',
    'HEADER_TEXT' => null,
    'HEADER_POSITION' => 'center',
    'DESCRIPTION_SHOW' => 'N',
    'DESCRIPTION_TEXT' => null,
    'DESCRIPTION_POSITION' => 'center',
    'LINK_BLANK' => 'Y',
    'FOOTER_SHOW' => 'N',
    'FOOTER_TEXT' => null,
    'FOOTER_POSITION' => 'center',
    'FOOTER_LINK' => null,
    'FOOTER_ON_HEADER' => 'Y',
    'ITEM_DESCRIPTION_SHOW' => 'N',
    'ITEM_PADDING_USE' => 'N',
    'ITEM_WIDE' => 'N',
    'COLUMN' => 5,
    'MOBILE_COLUMN' => 2,
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'HEADER' => [
        'SHOW' => $arParams['HEADER_SHOW'] === 'Y' && !empty($arParams['HEADER_TEXT']),
        'TEXT' => $arParams['HEADER_TEXT'],
        'POSITION' => $arParams['HEADER_POSITION']
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y' && !empty($arParams['DESCRIPTION_TEXT']),
        'TEXT' => $arParams['DESCRIPTION_TEXT'],
        'POSITION' => $arParams['DESCRIPTION_POSITION']
    ],
    'FOOTER' => [
        'SHOW' => $arParams['FOOTER_SHOW'] === 'Y' && !empty($arParams['FOOTER_LINK']),
        'TEXT' => !empty($arParams['FOOTER_TEXT']) ? $arParams['FOOTER_TEXT'] : Loc::getMessage('MAIN_INSTAGRAM_TEMP1_FOOTER_DEFAULT'),
        'POSITION' => $arParams['FOOTER_POSITION'],
        'ON_HEADER' => $arParams['FOOTER_ON_HEADER'] === 'Y' && $arParams['HEADER_SHOW'] === 'Y',
        'LINK' => $arParams['FOOTER_LINK']
    ],
    'ITEMS' => [
        'DESCRIPTION' => $arParams['ITEM_DESCRIPTION_SHOW'] === 'Y',
        'PADDING' => $arParams['ITEM_PADDING_USE'] === 'Y',
        'WIDE' => $arParams['ITEM_WIDE'] === 'Y',
        'COUNT' => [
            'DESKTOP' => $arParams['COLUMN'],
            'MOBILE' => $arParams['MOBILE_COLUMN']
        ],
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'SVG' => [
        'INSTAGRAM_ICON' => FileHelper::getFileData(__DIR__.'/svg/instagram_icon.svg')
    ]
];

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/',
    'TEMPLATE_PATH' => $this->GetFolder().'/'
];

if ($arVisual['FOOTER']['SHOW'])
    $arVisual['FOOTER']['LINK'] = StringHelper::replaceMacros($arParams['FOOTER_LINK'], $arMacros);

$arResult['VISUAL'] = ArrayHelper::merge($arResult['VISUAL'], $arVisual);