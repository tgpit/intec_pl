<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['HEADER_SHOW'] = array(
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_HEADER_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
);

if ($arCurrentValues['HEADER_SHOW'] == 'Y') {
    $arTemplateParameters['HEADER_POSITION'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_HEADER_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => array(
            'left' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_POSITION_RIGHT')
        ),
        'DEFAULT' => 'center'
    );
    $arTemplateParameters['HEADER_TEXT'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_HEADER_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_HEADER_TEXT_DEFAULT')
    );
}

$arTemplateParameters['DESCRIPTION_SHOW'] = array(
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_DESCRIPTION_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
);

$arTemplateParameters['ITEM_WIDE'] = array(
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_ITEM_WIDE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
);

if ($arCurrentValues['DESCRIPTION_SHOW'] === 'Y') {
    $arTemplateParameters['DESCRIPTION_POSITION'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_DESCRIPTION_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => array(
            'left' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_POSITION_RIGHT')
        ),
        'DEFAULT' => 'center'
    );
    $arTemplateParameters['DESCRIPTION_TEXT'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_DESCRIPTION_TEXT'),
        'TYPE' => 'STRING'
    );
}

if (!empty($arCurrentValues['ACCESS_TOKEN'])) {
    $arTemplateParameters['ITEM_DATE_SHOW'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_ITEM_DATE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'REFRESH' => 'Y'
    );

    $arTemplateParameters['ITEM_DATE_FORMAT'] = CIBlockParameters::GetDateFormat(
        Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_ITEM_DATE_FORMAT'),
        'VISUAL'
    );

    $arTemplateParameters['ITEM_DESCRIPTION_SHOW'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_ITEM_DESCRIPTION_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    );

    $arTemplateParameters['ITEM_FIRST_BIG'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_ITEM_FIRST_BIG'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'REFRESH' => 'Y'
    );

    if ($arCurrentValues['ITEM_FIRST_BIG'] === 'Y') {
        $arTemplateParameters['ITEM_SHOW_MORE'] = array(
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_ITEM_SHOW_MORE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        );
        if ($arCurrentValues['ITEM_SHOW_MORE'] === 'Y') {
            $arTemplateParameters['ITEM_SHOW_MORE_IN'] = array(
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_ITEM_SHOW_MORE_IN'),
                'TYPE' => 'LIST',
                'VALUES' => array(
                    'mobile' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_ITEM_SHOW_MORE_ONLY_MOBILE'),
                    'desktop' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_ITEM_SHOW_MORE_ONLY_DESKTOP'),
                    'both' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_ITEM_SHOW_MORE_ALL')
                ),
                'DEFAULT' => 'mobile'
            );
        }
    }

    $arTemplateParameters['ITEM_FILL_BLOCKS'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_ITEM_FILL_BLOCKS'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    );

    $arTemplateParameters['LINK_BLANK'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_LINK_BLANK'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y'
    );

    $arTemplateParameters['CACHE_PATH'] = array(
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_CACHE_PATH'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'upload/intec.universe/instagram/cache/#SITE_DIR#'
    );
}

$arTemplateParameters['FOOTER_SHOW'] = array(
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_FOOTER_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
);

if ($arCurrentValues['FOOTER_SHOW'] === 'Y') {
    if ($arCurrentValues['HEADER_SHOW'] === 'Y') {
        $arTemplateParameters['FOOTER_ON_HEADER'] = array(
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_FOOTER_ON_HEADER'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
            'REFRESH' => 'Y'
        );
    }
    if ($arCurrentValues['FOOTER_ON_HEADER'] === 'N' || $arCurrentValues['HEADER_SHOW'] === 'N') {
        $arTemplateParameters['FOOTER_POSITION'] = array(
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_FOOTER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => array(
                'left' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_POSITION_LEFT'),
                'center' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_POSITION_CENTER'),
                'right' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_POSITION_RIGHT')
            ),
            'DEFAULT' => 'center'
        );
    }
    $arTemplateParameters['FOOTER_LINK'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_FOOTER_LINK'),
        'TYPE' => 'STRING'
    );
    $arTemplateParameters['FOOTER_TEXT'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_FOOTER_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_FOOTER_DEFAULT')
    );
}