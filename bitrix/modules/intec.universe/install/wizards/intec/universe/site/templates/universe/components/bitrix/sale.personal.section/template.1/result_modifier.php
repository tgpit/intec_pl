<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\template\Properties;
use intec\core\collections\Arrays;

Loader::includeModule('iblock');

global $USER;
global $USER_FIELD_MANAGER;

$rsUser = CUser::GetByID($USER->GetID());
$arUser = $rsUser->Fetch();
$arUserFields = $USER_FIELD_MANAGER->GetUserFields('USER');
$arVisual = [
    'SHOW_ICON' => $arParams['SHOW_ICON'] === 'Y',
    'MANAGER_BLOCK_SHOW' => !empty($arParams['PROPERTY_MANAGER']) && !empty($arParams['MANAGER_IBLOCK_TYPE']) && !empty($arParams['MANAGER_IBLOCK_ID']),
    'CLAIMS_BLOCK_SHOW' => false
];
$arManager = [];
$iManagerId = null;

if ($arVisual['MANAGER_BLOCK_SHOW']) {
    if ($arParams['MANAGER_DEFAULT_USE'] === 'Y')
        $iManagerId = $arUserFields[$arParams['PROPERTY_MANAGER']]['SETTINGS']['DEFAULT_VALUE'];

    if (!empty($arUser[$arParams['PROPERTY_MANAGER']]))
        $iManagerId = $arUser[$arParams['PROPERTY_MANAGER']];

    $arVisual['MANAGER_BLOCK_SHOW'] = !empty($iManagerId);

    if (!empty($iManagerId)) {
        $arFilter = ['ID' => $iManagerId, 'IBLOCK_ID' => $arParams['MANAGER_IBLOCK_ID'], 'ACTIVE' => 'Y'];
        $arKeyField = ['POSITION', 'PHONE', 'EMAIL', 'SOCIAL_VK', 'SOCIAL_FB', 'SOCIAL_INST', 'SOCIAL_TW', 'SOCIAL_SKYPE'];
        $arManagerProperties = [
            'POSITION' => null,
            'PHONE' => null,
            'EMAIL' => null,
            'SOCIAL_VK' => null,
            'SOCIAL_FB' => null,
            'SOCIAL_INST' => null,
            'SOCIAL_TW' => null,
            'SOCIAL_SKYPE' => null
        ];
        $arSelect = ['*'];

        foreach ($arKeyField as $keyField) {
            if (!empty($arParams['MANAGER_PROPERTY_' . $keyField]))
                $arSelect[] = 'PROPERTY_' . $arParams['MANAGER_PROPERTY_' . $keyField];
        }

        unset($keyField);

        $arManager = Arrays::fromDBResult(CIBlockElement::GetList([], $arFilter, false, [], $arSelect))->asArray();

        if (empty($arManager)) {
            $arVisual['MANAGER_BLOCK_SHOW'] = false;
        } else {
            foreach ($arKeyField as $keyField) {
                if (!empty($arParams['MANAGER_PROPERTY_' . $keyField]))
                    $arManagerProperties[$keyField] = $arManager[0]['PROPERTY_' . $arParams['MANAGER_PROPERTY_' . $keyField] . '_VALUE'];
            }

            $arManager = $arManager[0];
            $arManager['MANAGER_PROPERTY'] = $arManagerProperties;
            $sImg = !empty($arManager['PREVIEW_PICTURE']) ? $arManager['PREVIEW_PICTURE'] : $arManager['DETAIL_PICTURE'];
            $arManager['PICTURE'] = !empty($sImg) ? CFile::GetPath($sImg) : SITE_TEMPLATE_PATH . '/images/picture.missing.png';

            unset($arFilter, $arKeyField, $arManagerProperties, $arSelect, $sImg);
        }
    }
}

$arRolesUser = $APPLICATION->GetUserRoles('support');
$arAdmissibleRoles = ['R', 'T', 'V', 'W'];
$arResult['TICKETS'] = [];
$arResult['ADD_TICKET_URL'] = '';
$arResult['ITEMS'] = [];
$sPageUrl = $APPLICATION->GetCurPage($arParams['SEF_MODE'] === 'Y' ? false : true);
$oRequest = Core::$app->request;
$sPageSection = $oRequest->get('SECTION');
$sHistoryPage = $oRequest->get('filter_history');
$arVisual['CLAIMS_BLOCK_SHOW'] = Loader::includeModule('support') &&
    $USER->IsAuthorized() &&
    !empty(array_intersect($arRolesUser, $arAdmissibleRoles)) &&
    $arParams['CLAIMS_USE'] === 'Y';

if ($arParams['SETTINGS_USE'] === 'Y') {
    include(__DIR__.'/modifiers/settings.php');

    if (!Properties::get('basket-use')) {
        $arParams['SHOW_BASKET_PAGE'] = 'N';
    }
}

if (!empty($arResult['SEF_FOLDER'])) {
    $arResult['ITEMS'][] = [
        'PATH' => $arResult['SEF_FOLDER'],
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_MENU_ITEM_ROOT'),
        'ICON' => '<i class="fa fa-home"></i>',
        'ACTIVE' => $sPageUrl == $arResult['SEF_FOLDER'] && empty($sPageSection)
    ];
}

if ($arParams['SHOW_ORDER_PAGE'] === 'Y') {
    $arResult['ITEMS'][] = [
        'PATH' => $arResult['PATH_TO_ORDERS'],
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_MENU_ITEM_ORDER'),
        'ICON' => '<i class="fa fa-calculator"></i>',
        'ACTIVE' => empty($sPageSection) ? $sPageUrl == $arResult['PATH_TO_ORDERS'] && empty($sHistoryPage) : $sPageSection == 'orders' && empty($sHistoryPage)
    ];
}

if ($arParams['SHOW_ACCOUNT_PAGE'] === 'Y') {
    $arResult['ITEMS'][] = [
        'PATH' => $arResult['PATH_TO_ACCOUNT'],
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_MENU_ITEM_ACCOUNT'),
        'ICON' => '<i class="fa fa-credit-card"></i>',
        'ACTIVE' => empty($sPageSection) ? $sPageUrl == $arResult['PATH_TO_ACCOUNT'] : $sPageSection == 'account'
    ];
}

if ($arParams['SHOW_PRIVATE_PAGE'] === 'Y') {
    $arResult['ITEMS'][] = [
        'PATH' => $arResult['PATH_TO_PRIVATE'],
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_MENU_ITEM_PERSONAL'),
        'ICON' => '<i class="fa fa-user-secret"></i>',
        'ACTIVE' => empty($sPageSection) ? $sPageUrl == $arResult['PATH_TO_PRIVATE'] : $sPageSection == 'private'
    ];
}

if ($arParams['SHOW_ORDER_PAGE'] === 'Y') {
    $delimeter = ($arParams['SEF_MODE'] === 'Y') ? '?' : '&';
    $arResult['ITEMS'][] = [
        'PATH' => $arResult['PATH_TO_ORDERS'].$delimeter.'filter_history=Y',
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_MENU_ITEM_ORDER_HISTORY'),
        'ICON' => '<i class="fa fa-list-alt"></i>',
        'ACTIVE' => empty($sPageSection) ? $sPageUrl == $arResult['PATH_TO_ORDERS'] && !empty($sHistoryPage) : $sPageSection == 'orders' && !empty($sHistoryPage)
    ];
}

if ($arParams['SHOW_PROFILE_PAGE'] === 'Y') {
    $arResult['ITEMS'][] = [
        'PATH' => $arResult['PATH_TO_PROFILE'],
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_MENU_ITEM_PROFILE'),
        'ICON' => '<i class="fa fa-list-ol"></i>',
        'ACTIVE' => empty($sPageSection) ? $sPageUrl == $arResult['PATH_TO_PROFILE'] : $sPageSection == 'profile'
    ];
}

if ($arParams['SHOW_BASKET_PAGE'] === 'Y') {
    $arResult['ITEMS'][] = [
        'PATH' => $arParams['PATH_TO_BASKET'],
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_MENU_ITEM_BASKET'),
        'ICON' => '<i class="fa fa-shopping-cart"></i>',
        'ACTIVE' => ''
    ];
}

if ($arParams['SHOW_SUBSCRIBE_PAGE'] === 'Y') {
    $arResult['ITEMS'][] = [
        'PATH' => $arResult['PATH_TO_SUBSCRIBE'],
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_MENU_ITEM_SUBSCRIBE'),
        'ICON' => '<i class="fas fa-star"></i>',
        'ACTIVE' => empty($sPageSection) ? $sPageUrl == $arResult['PATH_TO_SUBSCRIBE'] : $sPageSection == 'subscribe'
    ];
}

if ($arParams['MAILING_SHOW'] === 'Y') {
    $arResult['ITEMS'][] = [
        'PATH' => $arParams['MAILING_PATH'],
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_MENU_ITEM_MAILING'),
        'ICON' => '<i class="fa fa-envelope"></i>',
        'ACTIVE' => ''
    ];
}

if ($arParams['SHOW_CONTACT_PAGE'] === 'Y') {
    $arResult['ITEMS'][] = [
        'PATH' => $arParams['PATH_TO_CONTACT'],
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_MENU_ITEM_CONTACT'),
        'ICON' => '<i class="fa fa-info-circle"></i>',
        'ACTIVE' => ''
    ];
}

if ($arParams['CRM_SHOW_PAGE'] === 'Y') {
    $arResult['ITEMS']['CRM'] = [
        'PATH' => isset($arResult['PATH_TO_CRM']) ? $arResult['PATH_TO_CRM'] : $arResult['SEF_FOLDER'].'?SECTION=crm',
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_MENU_ITEM_CRM'),
        'ICON' => '<i class="fa fa-business-time"></i>',
        'ACTIVE' => empty($sPageSection) ? $sPageUrl == $arResult['PATH_TO_CRM'] : $sPageSection == 'crm'
    ];
}

if ($arVisual['CLAIMS_BLOCK_SHOW']) {
    $arResult['ITEMS']['CLAIMS'] = [
        'PATH' => isset($arResult['PATH_TO_CLAIMS']) ? $arResult['PATH_TO_CLAIMS'] : $arResult['SEF_FOLDER'].'?SECTION=claims',
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_MENU_ITEM_CLAIMS'),
        'ICON' => '<i class="fa fa-question"></i>',
        'ACTIVE' => empty($sPageSection) ? $sPageUrl == $arResult['PATH_TO_CLAIMS'] : $sPageSection == 'claims'
    ];
}

if ($arParams['PRODUCT_VIEWED_SHOW_PAGE'] === 'Y') {
    $arResult['ITEMS']['PRODUCT_VIEWED'] = [
        'PATH' => isset($arResult['PATH_TO_VIEWED']) ? $arResult['PATH_TO_VIEWED'] : $arResult['SEF_FOLDER'].'?SECTION=viewed',
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_MENU_ITEM_PRODUCT_VIEWED'),
        'ICON' => '<i class="fa fa-history"></i>',
        'ACTIVE' => empty($sPageSection) ? $sPageUrl == $arResult['PATH_TO_VIEWED'] : $sPageSection == 'viewed'
    ];
}

if ($arParams['PROFILE_ADD_USE'] === 'Y') {
    $arResult['ITEMS']['PROFILE_ADD'] = [
        'PATH' => isset($arResult['PATH_TO_PROFILE_ADD']) ? $arResult['PATH_TO_PROFILE_ADD'] : $arResult['SEF_FOLDER'].'?SECTION=profile_add',
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_MENU_ITEM_PROFILE_ADD'),
        'ICON' => '<i class="fa fa-user-shield"></i>',
        'ACTIVE' => empty($sPageSection) ? $sPageUrl == $arResult['PATH_TO_PROFILE_ADD'] : $sPageSection == 'profile_add'
    ];
}

$customPagesList = CUtil::JsObjectToPhp($arParams['~CUSTOM_PAGES']);

if ($customPagesList) {
    foreach ($customPagesList as $page) {
        $arResult['ITEMS'][] = [
            'PATH' => $page[0],
            'NAME' => $page[1],
            'ICON' => (strlen($page[2])) ? '<i class="fa ' . htmlspecialcharsbx($page[2]) . '"></i>' : '',
            'ACTIVE' => ''
        ];
    }
}

if ($arVisual['CLAIMS_BLOCK_SHOW']) {
    $sSortBy = 's_id';
    $sSortOrder = 'desc';
    $rsTickets = CTicket::GetList(
        $sSortBy,
        $sSortOrder,
        [
            'OWNER_USER_ID' => $arUser['ID'],
            'CLOSE' => 'N'
        ],
        $isFiltered,
        $check_rights = 'Y',
        $get_user_name = 'N',
        $get_dictionary_name = 'N',
        false,
        ['SELECT' => ['UF_*']]
    );

    $sClaimsUrl = '';
    $arResult['ALL_TICKET_URL'] = isset($arParams['SEF_URL_TEMPLATES']) && empty($arParams['SEF_URL_TEMPLATES']['claims']) ? '' : $arResult['ITEMS']['CLAIMS']['PATH'];

    if (!empty($arResult['ALL_TICKET_URL'])) {
        $arResult['ADD_TICKET_URL'] = $arResult['ALL_TICKET_URL'] . ($arParams['SEF_MODE'] === 'Y' ? '?ID=0&edit=1' : '&ID=0&edit=1');
        $sClaimsUrl = $arResult['ALL_TICKET_URL'] . ($arParams['SEF_MODE'] === 'Y' ? '?ID=#ID#&edit=1' : '&ID=#ID#&edit=1');
    }

    while ($arTicket = $rsTickets->GetNext()) {
        $arResult['TICKETS'][$arTicket['ID']] = $arTicket;
        $arResult['TICKETS'][$arTicket['ID']]['TICKET_EDIT_URL'] = '';

        if (!empty($sClaimsUrl))
            $arResult['TICKETS'][$arTicket['ID']]['TICKET_EDIT_URL'] = CComponentEngine::MakePathFromTemplate($sClaimsUrl, ['ID' => $arTicket['ID']]);
    }

    unset($sClaimsUrl, $arTicket, $rsTickets, $sSortBy, $sSortOrder);
}

$arResult['MANAGER'] = $arManager;
$arResult['VISUAL'] = $arVisual;

unset($arManager, $iManagerId, $arUserFields, $arVisual, $sPageUrl, $oRequest, $sPage, $sHistoryPage);