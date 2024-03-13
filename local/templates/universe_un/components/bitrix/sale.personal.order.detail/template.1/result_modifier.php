<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Type;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\net\Url;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

if (empty($arParams['HIDE_USER_INFO']))
    $arParams['HIDE_USER_INFO'] = [];

if (!Type::isArray($arParams['HIDE_USER_INFO']))
    $arParams['HIDE_USER_INFO'] = [];

$arVisual = [
    'CLAIMS_BLOCK_SHOW' => false
];

$arDefaultColors = ['gray', 'green', 'yellow', 'red'];

$arOrdersStatuses = Arrays::fromDBResult(CSaleStatus::GetList(['SORT' => 'ASC'], [
    'LID' => LANGUAGE_ID
]))->indexBy('ID');

if (!empty($arResult['STATUS'])) {
    if (!ArrayHelper::keyExists('COLOR', $arResult['STATUS']))
        $arResult['STATUS']['COLOR'] = $arResult['CANCELED'] === 'Y' ? $arParams['STATUS_COLOR_PSEUDO_CANCELLED'] : $arParams['STATUS_COLOR_' . $arResult['STATUS']['ID']];
}

if (isset($arResult['STATUS']['COLOR']) && $arResult['STATUS']['COLOR'] === 'current')
    $arResult['STATUS']['COLOR'] = $arOrdersStatuses[$arResult['STATUS']['ID']]['COLOR'];

$arChangeHistory = Arrays::fromDBResult(CSaleOrderChange::GetList(
    ['ID' => 'DESC'],
    [
        'ORDER_ID' => $arResult['ID'],
        '@TYPE' => \Bitrix\Sale\OrderHistory::getManagerLogItems()
    ]
))->indexBy('ID');

$arResult['CHANGE_HISTORY'] = [];
$arResult['TICKETS'] = [];
$arResult['INFO_BLOCKS'] = [];

$arOperation = '';

foreach ($arChangeHistory as &$arChangeHistoryItem) {
    $arOperation = CSaleOrderChange::GetRecordDescription($arChangeHistoryItem["TYPE"], $arChangeHistoryItem["DATA"]);
    $arOperation['DATE'] = CIBlockFormatProperties::DateFormat($arParams['ACTIVE_DATE_FORMAT'].' H:i:s', MakeTimeStamp($arChangeHistoryItem['DATE_MODIFY'], 'DD.MM.YYYY HH:MI:SS'));
    $arResult['CHANGE_HISTORY'][] = $arOperation;
}

unset($arChangeHistoryItem, $arOperation);

$arRolesUser = $APPLICATION->GetUserRoles('support');
$arAdmissibleRoles = ['R', 'T', 'V', 'W'];
$oClaimsUrl = new Url($arParams['PATH_TO_CLAIMS']);
$arParamClaimsUrl = $oClaimsUrl->getQuery()->asArray();
$sClaimsUrl = '';
$arResult['PATH_TO_NEW_CLAIM'] = '';
$arResult['PATH_TO_CLAIMS'] = $arParams['PATH_TO_CLAIMS'];
$arVisual['CLAIMS_BLOCK_SHOW'] = Loader::includeModule('support') && $USER->IsAuthorized() && !empty(array_intersect($arRolesUser, $arAdmissibleRoles)) && !empty($arParams['PROPERTY_CLAIMS']);

if (!empty($arResult['PATH_TO_CLAIMS'])) {
    $arResult['PATH_TO_NEW_CLAIM'] = $arResult['PATH_TO_CLAIMS'] . (empty($arParamClaimsUrl) ? '?ID=0&edit=1' : '&ID=0&edit=1');
    $sClaimsUrl = $arResult['PATH_TO_CLAIMS'] . (empty($arParamClaimsUrl) ? '?ID=#ID#&edit=1' : '&ID=#ID#&edit=1');
}

if (!empty($arParams['PROPERTY_CLAIMS']) && !empty($arResult['PATH_TO_NEW_CLAIM'])) {
    $arResult['PATH_TO_NEW_CLAIM'] .= '&'.$arParams['PROPERTY_CLAIMS'].'='.$arResult['ID'];
}

if ($arVisual['CLAIMS_BLOCK_SHOW']) {
    $sSortBy = 's_id';
    $sSortOrder = 'desc';
    $rsTickets = CTicket::GetList(
        $sSortBy,
        $sSortOrder,
        ['OWNER_USER_ID' => $arResult['USER_ID']],
        $isFiltered,
        $check_rights = 'Y',
        $get_user_name = 'N',
        $get_dictionary_name = 'N',
        false,
        ['SELECT' => ['UF_*']]
    );

    while ($arTicket = $rsTickets->GetNext()) {
        if (ArrayHelper::keyExists($arParams['PROPERTY_CLAIMS'], $arTicket) && $arTicket[$arParams['PROPERTY_CLAIMS']] == $arResult['ID']) {
            $arResult['TICKETS'][$arTicket['ID']] = $arTicket;
            $arResult['TICKETS'][$arTicket['ID']]['TICKET_EDIT_URL'] = '';

            if (!empty($sClaimsUrl))
                $arResult['TICKETS'][$arTicket['ID']]['TICKET_EDIT_URL'] = CComponentEngine::MakePathFromTemplate($sClaimsUrl, ['ID' => $arTicket['ID']]);
        }
    }
}

$groups = [];
$props = [];
$arFilterProps = [];
$groups = Arrays::fromDBResult(CSaleOrderPropsGroup::GetList(
    ['ID' => 'ASC'],
    ['PERSON_TYPE_ID' => $arResult['PERSON_TYPE']['ID']],
    false,
    false,
    []
))->asArray();

$arFilterProps = $arParams['PROP_'.$arResult['PERSON_TYPE']['ID']];

if (!empty($groups) && !empty($arFilterProps)) {
    foreach ($groups as &$arGroup) {
        $arResult['INFO_BLOCKS'][$arGroup['ID']]['NAME'] = $arGroup['NAME'];
        $arResult['INFO_BLOCKS'][$arGroup['ID']]['PROPS'] = [];

        foreach ($arResult['ORDER_PROPS'] as &$arProp) {
            if ($arProp['PROPS_GROUP_ID'] == $arGroup['ID'] && !empty($arProp['VALUE']) && !ArrayHelper::isIn($arProp['ID'], $arFilterProps)) {
                if ($arProp['PROP_TYPE'] == 'LOCATION') {
                    $arLocs = CSaleLocation::GetByID($arProp['VALUE'], LANGUAGE_ID);
                    $arProp['VALUE'] = $arLocs['COUNTRY_NAME'].', '.$arLocs['REGION_NAME'].', '.$arLocs['CITY_NAME'];
                    unset($arLocs);
                }

                $arResult['INFO_BLOCKS'][$arGroup['ID']]['PROPS'][] = $arProp;
            }
        }

        unset($arProp);
    }

    unset($arGroup);
} else {
    $arResult['INFO_BLOCKS'][0]['NAME'] = Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_INFORMATION_TITLE');
    $arResult['INFO_BLOCKS'][0]['PROPS'] = $arResult['ORDER_PROPS'];
}

$arResult['VISUAL'] = $arVisual;

unset($groups, $props, $arVisual);