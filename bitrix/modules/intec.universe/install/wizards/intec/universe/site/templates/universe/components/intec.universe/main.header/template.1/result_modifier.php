<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\component\InnerTemplates;
use intec\core\bitrix\component\InnerTemplate;
use intec\core\bitrix\FilesQuery;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Type;
use intec\regionality\models\Region;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @global $APPLICATION
 */

if (!Loader::includeModule('iblock'))
    return;

Loc::loadMessages(__FILE__);

$arParams = ArrayHelper::merge([
    'DESKTOP' => null,
    'FIXED' => null,
    'MOBILE' => null,
    'BANNER' => null,
    'BANNER_DISPLAY' => 'main',
    'ADDRESS' => null,
    'EMAIL' => null,
    'TAGLINE' => null,
    'REGIONALITY_USE' => 'N',
    'REGIONALITY_TEMPLATE' => 'template.1',
    'PHONES_ADVANCED_MODE' => 'N',
    'FORMS_CALL_SHOW' => 'N',
    'FORMS_CALL_ID' => null,
    'FORMS_CALL_TEMPLATE' => null,
    'FORMS_CALL_TITLE' => null,
    'COMPARE_IBLOCK_ID' => null,
    'COMPARE_IBLOCK_TYPE' => null,
    'COMPARE_CODE' => null,
    'MENU_MAIN_ROOT' => null,
    'MENU_MAIN_CHILD' => null,
    'MENU_MAIN_LEVEL' => null,
    'MENU_POPUP_TEMPLATE' => 'main.popup.1',
    'MENU_PERSONAL_SECTION' => 'personal',
    'BASKET_POPUP' => 'N',
    'SEARCH_MODE' => 'site',
    'SEARCH_BIG_TEMPLATE' => 'popup.2',
    'SEARCH_SMALL_TEMPLATE' => 'input.4',
    'CONTACTS_REGIONALITY_USE' => 'N',
    'CONTACTS_REGIONALITY_STRICT' => 'Y',
    'CONTACTS_IBLOCK_ID' => null,
    'CONTACTS_ELEMENT' => null,
    'CONTACTS_ELEMENTS' => null,
    'CONTACTS_ADDRESS_SHOW' => 'Y',
    'CONTACTS_SCHEDULE_SHOW' => 'Y',
    'CONTACTS_EMAIL_SHOW' => 'Y',
    'COMPANY_NAME' => null,
    'CONTACTS_PROPERTY_PHONE' => null,
    'CONTACTS_PROPERTY_ADDRESS' => null,
    'CONTACTS_PROPERTY_SCHEDULE' => null,
    'CONTACTS_PROPERTY_EMAIL' => null,
    'CONTACTS_PROPERTY_ICON' => null,
    'CONTACTS_MOBILE_FORM_USE' => 'Y',
    'CONTACTS_MOBILE_FORM_TEMPLATE' => 'template.1',
    'SECOND_PHONES_SHOW' => 'N',
    'MOBILE_FIXED' => 'N',
    'TRANSPARENCY' => 'N',
    'SETTINGS_USE' => 'N',
    'MOBILE_HIDDEN' => 'N'
], $arParams);

$arResult['COMPANY_NAME'] = $arParams['COMPANY_NAME'];

$arCodes = [
    'CONTACTS' => [
        'CITY' => $arParams['CONTACTS_PROPERTY_CITY'],
        'ADDRESS' => $arParams['CONTACTS_PROPERTY_ADDRESS'],
        'EMAIL' => $arParams['CONTACTS_PROPERTY_EMAIL'],
        'PHONE' => $arParams['CONTACTS_PROPERTY_PHONE'],
        'SCHEDULE' => $arParams['CONTACTS_PROPERTY_SCHEDULE'],
        'ICON' => $arParams['CONTACTS_PROPERTY_ICON']
    ]
];

$arResult['CONTACTS_MOBILE_FORM'] = [
    'USE' => $arParams['CONTACTS_MOBILE_FORM_USE'] === 'Y',
    'TEMPLATE' => ArrayHelper::fromRange([
        'template.1',
        'template.2'
    ], $arParams['CONTACTS_MOBILE_FORM_TEMPLATE'])
];

$hDisplay = function ($sKey, $fCondition = null, $sPrefix = '_SHOW') use (&$arParams) {
    $arResult = [
        'DESKTOP' => ArrayHelper::getValue($arParams, $sKey . $sPrefix) == 'Y',
        'FIXED' => ArrayHelper::getValue($arParams, $sKey . $sPrefix.'_FIXED') == 'Y',
        'MOBILE' => ArrayHelper::getValue($arParams, $sKey . $sPrefix.'_MOBILE') == 'Y'
    ];

    if ($fCondition instanceof Closure)
        foreach ($arResult as $sKey => $bValue)
            $arResult[$sKey] = $bValue && $fCondition($sKey);

    return $arResult;
};

if ($arParams['SETTINGS_USE'] == 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/',
    'TEMPLATE_PATH' => $this->GetFolder().'/'
];

$arResult['REGIONALITY'] = [
    'USE' => $arParams['REGIONALITY_USE'] === 'Y' && Loader::includeModule('intec.regionality'),
    'TEMPLATE' => $arParams['REGIONALITY_TEMPLATE']
];

$arResult['LOGOTYPE']['SHOW'] = $hDisplay(
    'LOGOTYPE',
    function () use (&$arResult) {
        return !empty($arResult['LOGOTYPE']['PATH']);
    }
);

$arResult['LOGOTYPE']['LINK'] = [
    'USE' => true,
    'VALUE' => $APPLICATION->GetCurPage(false) !== SITE_DIR ? SITE_DIR : null
];

if (empty($arResult['LOGOTYPE']['LINK']['VALUE']))
    $arResult['LOGOTYPE']['LINK']['USE'] = false;

$arResult['CONTACTS'] = [
    'SHOW' => null,
    'ADVANCED' => $arParams['PHONES_ADVANCED_MODE'] === 'Y',
    'SECOND' => $arParams['SECOND_PHONES_SHOW'] === 'Y',
    'VALUES' => [],
    'ALL' => []
];

if ($arResult['CONTACTS']['ADVANCED']) {
    $bShow = false;
    $arShow = [
        'CITY' => !empty($arCodes['CONTACTS']['CITY']),
        'ADDRESS' => !empty($arCodes['CONTACTS']['ADDRESS']),
        'EMAIL' => !empty($arCodes['CONTACTS']['EMAIL']),
        'PHONE' => !empty($arCodes['CONTACTS']['PHONE']),
        'SCHEDULE' => !empty($arCodes['CONTACTS']['SCHEDULE']),
        'ICON' => !empty($arCodes['CONTACTS']['ICON'])
    ];

    foreach ($arShow as $bShow)
        if ($bShow) break;

    if (!empty($arParams['CONTACTS_IBLOCK_ID']) && $bShow) {
        $arFilter = [
            'IBLOCK_ID' => $arParams['CONTACTS_IBLOCK_ID'],
            'ACTIVE' => 'Y',
            'ACTIVE_DATE' => 'Y'
        ];

        if (!empty($arParams['CONTACTS_ELEMENTS']) && Type::isArray($arParams['CONTACTS_ELEMENTS']))
            $arFilter['ID'] = $arParams['CONTACTS_ELEMENTS'];

        if ($arResult['REGIONALITY']['USE'] && $arParams['CONTACTS_REGIONALITY_USE'] === 'Y' && !empty($arParams['CONTACTS_PROPERTY_REGION'])) {
            $oRegion = Region::getCurrent();

            if (!empty($oRegion)) {
                $arConditions = [
                    'LOGIC' => 'OR',
                    ['PROPERTY_'.$arParams['CONTACTS_PROPERTY_REGION'] => $oRegion->id]
                ];

                if ($arParams['CONTACTS_REGIONALITY_STRICT'] !== 'Y')
                    $arConditions[] = ['PROPERTY_'.$arParams['CONTACTS_PROPERTY_REGION'] => false];

                $arFilter[] = $arConditions;
            }
        }

        $arContacts = new Arrays();
        $rsContacts = CIBlockElement::GetList([
            'SORT' => 'ASC'
        ], $arFilter);

        while ($rsContact = $rsContacts->GetNextElement()) {
            $arContact = $rsContact->GetFields();
            $arContact['PROPERTIES'] = $rsContact->GetProperties();
            $arContacts->set($arContact['ID'], $arContact);
        }

        unset($arFilter);
        unset($arContact);
        unset($rsContact);
        unset($rsContacts);

        $arContacts = $arContacts->asArray(function ($iId, $arContact) use (&$arShow, &$arCodes) {
            $bEmpty = true;
            $arItem = [
                'ID' => $arContact['ID'],
                'NAME' => $arContact['NAME']
            ];

            foreach ($arShow as $sKey => $bShow) {
                $sValue = null;

                if ($bShow) {
                    $arProperty = ArrayHelper::getValue($arContact, [
                        'PROPERTIES',
                        $arCodes['CONTACTS'][$sKey]
                    ]);

                    if (!empty($arProperty)) {
                        $sValue = $arProperty['VALUE'];

                        if (Type::isArray($sValue) && $sKey !== 'SCHEDULE')
                            $sValue = reset($sValue);

                        if (empty($sValue) && !Type::isNumeric($sValue)) {
                            $sValue = null;
                        } else {
                            if ($sKey === 'SCHEDULE' && Type::isArray($sValue) && !empty($arProperty['DESCRIPTION'])) {
                                foreach ($sValue as $iValueId => $mValue) {
                                    $sDescription = ArrayHelper::getValue($arProperty, ['DESCRIPTION', $iValueId]);

                                    if (!empty($sDescription))
                                        $sValue[$iValueId] = $sDescription.(!empty($mValue) ? ' '.$mValue : null);
                                }
                            }

                            $bEmpty = false;
                        }
                    }
                }

                $arItem[$sKey] = $sValue;
            }

            if ($bEmpty)
                return ['skip' => true];

            if (!empty($arItem['PHONE']))
                $arItem['PHONE'] = [
                    'DISPLAY' => $arItem['PHONE'],
                    'VALUE' => StringHelper::replace($arItem['PHONE'], [
                        '(' => '',
                        ')' => '',
                        ' ' => '',
                        '-' => ''
                    ])
                ];

            if (!empty($arItem['CITY']))
                $arItem['ADDRESS'] = Loc::getMessage('C_HEADER_TEMP1_CONTACTS_CITY', [
                        '#CITY#' => $arItem['CITY']
                    ]).(!empty($arItem['ADDRESS']) ? ', '.$arItem['ADDRESS'] : null);

            unset($arItem['CITY']);

            return [
                'key' => $iId,
                'value' => $arItem
            ];
        });

        $arResult['CONTACTS']['VALUES'] = $arContacts;
        $arResult['CONTACTS']['ALL'] = $arContacts;

        unset($arContacts);
    }

    unset($arShow);
    unset($bShow);
} else {
    $arResult['CONTACTS']['VALUES'] = $arResult['PHONES']['VALUES'];
}

$arResult['CONTACTS']['SELECTED'] = [];

$iContactsCount = count($arResult['CONTACTS']['VALUES']);

if ($iContactsCount < 2)
    $arResult['CONTACTS']['SECOND'] = false;

if ($arResult['CONTACTS']['ADVANCED'] && !empty($arParams['CONTACTS_ELEMENT']))
    $arResult['CONTACTS']['SELECTED'] = ArrayHelper::getValue(
        $arResult['CONTACTS']['VALUES'],
        $arParams['CONTACTS_ELEMENT']
    );

if (empty($arResult['CONTACTS']['SELECTED'])) {
    $iCount = 1;

    if ($arResult['CONTACTS']['SECOND'])
        $iCount = 0;

    foreach ($arResult['CONTACTS']['VALUES'] as $key => $value) {
        if ($iCount >= 2)
            break;

        if ($arResult['CONTACTS']['ADVANCED']) {
            $arResult['CONTACTS']['SELECTED'][$key] = $value;
        } else {
            $arResult['CONTACTS']['SELECTED'][$key] = $value;
            unset($arResult['CONTACTS']['VALUES'][$key]);
        }

        $iCount++;
    }
} else {
    $sSelectedPhone = $arResult['CONTACTS']['SELECTED'];

    unset($arResult['CONTACTS']['SELECTED']);

    $arResult['CONTACTS']['SELECTED'][$sSelectedPhone['ID']] = $sSelectedPhone;

    if ($arResult['CONTACTS']['SECOND']) {
        $arSecondPhone = ArrayHelper::shift($arResult['CONTACTS']['VALUES']);
        $arResult['CONTACTS']['SELECTED'][$arSecondPhone['ID']] = $arSecondPhone;
    }
}

if (!empty($arResult['CONTACTS']['SELECTED'])) {
    if ($arResult['CONTACTS']['ADVANCED']) {
        foreach ($arResult['CONTACTS']['SELECTED'] as $key => $value) {
            if (empty($value['PHONE']))
                $arResult['CONTACTS']['SELECTED'][$key] = null;
        }
    }
}

$arResult['CONTACTS']['SHOW'] = $hDisplay(
    'PHONES',
    function () use (&$arResult) {
        return !empty($arResult['CONTACTS']['SELECTED']);
    }
);

unset($arResult['PHONES']);

$arResult['AUTHORIZATION'] = [
    'SHOW' => $hDisplay('AUTHORIZATION'),
    'PANEL' => [
        'DESKTOP' => null,
        'FIXED' => null,
        'MOBILE' => null
    ],
    'FORM' => [
        'COMPONENT' => $arParams['AUTHORIZATION_FORM_USE_REGISTRATION'] === 'Y' ? 'bitrix:system.auth.form' : 'bitrix:system.auth.authorize',
        'TEMPLATE' => $arParams['AUTHORIZATION_FORM_USE_REGISTRATION'] === 'Y' ? 'template.2' : 'popup.1'
    ]
];

$arResult['EMAIL'] = [
    'SHOW' => null,
    'VALUE' => $arParams['EMAIL']
];

$arResult['ADDRESS'] = [
    'SHOW' => null,
    'VALUE' => $arParams['ADDRESS']
];

if ($arResult['CONTACTS']['ADVANCED']) {
    $arResult['ADDRESS']['VALUE'] = null;
    $arResult['EMAIL']['VALUE'] = null;
    $arItem = ArrayHelper::getFirstValue($arResult['CONTACTS']['SELECTED']);

    if (!empty($arItem)) {
        $arResult['ADDRESS']['VALUE'] = $arItem['ADDRESS'];
        $arResult['EMAIL']['VALUE'] = $arItem['EMAIL'];
    }

    unset($arItem);

    if (empty($arResult['CONTACTS']['ALL']))
        $arResult['CONTACTS_MOBILE_FORM']['USE'] = false;
} else {
    $arResult['CONTACTS_MOBILE_FORM']['USE'] = false;
}

$arFiles = new FilesQuery();

foreach ($arResult['CONTACTS']['ALL'] as &$arItem) {
    if (!empty($arItem['ICON']))
        $arFiles->add($arItem['ICON']);
}

unset($arItem);

$arFiles = $arFiles->execute();

if (!$arFiles->isEmpty())
    foreach ($arResult['CONTACTS']['ALL'] as &$arItem)
        if (!empty($arItem['ICON']))
            $arItem['ICON'] = $arFiles->get($arItem['ICON']);

unset($arItem, $arFiles);

$arResult['EMAIL']['SHOW'] = $hDisplay(
    'EMAIL',
    function () use (&$arResult) {
        return !empty($arResult['EMAIL']['VALUE']);
    }
);

$arResult['ADDRESS']['SHOW'] = $hDisplay(
    'ADDRESS',
    function () use (&$arResult) {
        return !empty($arResult['ADDRESS']['VALUE']);
    }
);

$arResult['SOCIAL'] = [
    'SHOW' => null,
    'ITEMS' => [],
    'GREY' => $arParams['SOCIAL_GREY'] === 'Y' ? 'true' : 'false',
    'SQUARE' => $arParams['SOCIAL_SQUARE'] === 'Y' ? 'true' : 'false'
];

$bSocialShow = false;

foreach ([
             'VK',
             'INSTAGRAM',
             'FACEBOOK',
             'TWITTER',
             'YOUTUBE',
             'ODNOKLASSNIKI',
             'VIBER',
             'WHATSAPP',
             'YANDEX_DZEN',
             'MAIL_RU',
             'TELEGRAM',
             'PINTEREST',
             'TIKTOK',
             'SNAPCHAT',
             'LINKEDIN'
         ] as $sSocial) {
    $sValue = ArrayHelper::getValue($arParams, 'SOCIAL_'.$sSocial);
    $sCodeSocial = StringHelper::toLowerCase($sSocial);
    $arSocial = [
        'SHOW' => !empty($sValue),
        'LINK' => $sValue,
        'CODE' => $sSocial
    ];

    $bSocialShow = $bSocialShow || $arSocial['SHOW'];
    $arResult['SOCIAL']['ITEMS'][$sSocial] = $arSocial;
}

$arResult['SOCIAL']['SHOW'] = $hDisplay(
    'SOCIAL',
    function () use (&$bSocialShow) {
        return $bSocialShow;
    }
);

$arResult['TAGLINE'] = [
    'SHOW' => $hDisplay('TAGLINE'),
    'VALUE' => $arParams['TAGLINE']
];

$arResult['TAGLINE']['SHOW'] = $hDisplay(
    'TAGLINE',
    function () use (&$arResult) {
        return !empty($arResult['TAGLINE']['VALUE']);
    }
);

$arResult['SEARCH'] = [
    'SHOW' => $hDisplay('SEARCH'),
    'MODE' => ArrayHelper::fromRange([
        'site',
        'catalog'
    ], $arParams['SEARCH_MODE'])
];

$arResult['BASKET'] = [
    'SHOW' => $hDisplay('BASKET'),
    'POPUP' => $arParams['BASKET_POPUP'] === 'Y'
];

$arResult['DELAY'] = array(
    'SHOW' => $hDisplay('DELAY')
);

$arResult['COMPARE'] = [
    'SHOW' => $hDisplay('COMPARE'),
    'IBLOCK' => [
        'ID' => $arParams['COMPARE_IBLOCK_ID'],
        'TYPE' => $arParams['COMPARE_IBLOCK_TYPE']
    ],
    'CODE' => $arParams['COMPARE_CODE']
];

$arResult['FORMS'] = [];
$arResult['FORMS']['CALL'] = [
    'SHOW' => $arParams['FORMS_CALL_SHOW'] === 'Y',
    'ID' => $arParams['FORMS_CALL_ID'],
    'TEMPLATE' => $arParams['FORMS_CALL_TEMPLATE'],
    'TITLE' => $arParams['FORMS_CALL_TITLE']
];

if ($arResult['FORMS']['CALL']['SHOW'] && empty($arResult['FORMS']['CALL']['ID']))
    $arResult['FORMS']['CALL']['SHOW'] = false;

$arResult['FORMS']['FEEDBACK'] = [
    'SHOW' => $arParams['FORMS_FEEDBACK_SHOW'] === 'Y',
    'ID' => $arParams['FORMS_FEEDBACK_ID'],
    'TEMPLATE' => $arParams['FORMS_FEEDBACK_TEMPLATE'],
    'TITLE' => $arParams['FORMS_FEEDBACK_TITLE']
];

if ($arResult['FORMS']['FEEDBACK']['SHOW'] && empty($arResult['FORMS']['FEEDBACK']['ID']))
    $arResult['FORMS']['FEEDBACK']['SHOW'] = false;

if ($arResult['REGIONALITY']['USE'])
    $arResult['ADDRESS']['SHOW']['DESKTOP'] = false;

$arResult['MENU'] = [];
$arResult['MENU']['MAIN'] = [
    'SHOW' => $hDisplay('MENU_MAIN'),
    'ROOT' => $arParams['MENU_MAIN_ROOT'],
    'CHILD' => $arParams['MENU_MAIN_CHILD'],
    'LEVEL' => $arParams['MENU_MAIN_LEVEL']
];

$arResult['MENU']['POPUP'] = [
    'TEMPLATE' => ArrayHelper::fromRange([
            'main.popup.1',
            'main.popup.2',
            'main.popup.3',
        ], $arParams['MENU_POPUP_TEMPLATE']).'.php'
];

if ($arParams['BANNER_DISPLAY'] === 'main')
    if ($APPLICATION->GetCurPage(false) !== SITE_DIR)
        $arParams['BANNER'] = null;

$arTemplates = [];
$arTemplates['DESKTOP'] = InnerTemplates::findOne($this, 'templates/desktop', $arParams['DESKTOP']);
$arTemplates['FIXED'] = InnerTemplates::findOne($this, 'templates/fixed', $arParams['FIXED']);
$arTemplates['MOBILE'] = InnerTemplates::findOne($this, 'templates/mobile', $arParams['MOBILE']);
$arTemplates['BANNER'] = InnerTemplates::findOne($this, 'templates/banners', $arParams['BANNER']);

$arResult['MOBILE'] = [
    'FIXED' => $arParams['MOBILE_FIXED'] === 'Y',
    'HIDDEN' => $arParams['MOBILE_HIDDEN'] === 'Y'
];

$arResult['URL'] = [
    'LOGIN' => ArrayHelper::getValue($arParams, 'LOGIN_URL'),
    'PROFILE' => ArrayHelper::getValue($arParams, 'PROFILE_URL'),
    'PASSWORD' => ArrayHelper::getValue($arParams, 'PASSWORD_URL'),
    'REGISTER' => ArrayHelper::getValue($arParams, 'REGISTER_URL'),
    'SEARCH' => ArrayHelper::getValue($arParams, 'SEARCH_URL'),
    'CATALOG' => ArrayHelper::getValue($arParams, 'CATALOG_URL'),
    'BASKET' => ArrayHelper::getValue($arParams, 'BASKET_URL'),
    'COMPARE' => ArrayHelper::getValue($arParams, 'COMPARE_URL'),
    'CONSENT' => ArrayHelper::getValue($arParams, 'CONSENT_URL'),
    'ORDER' => ArrayHelper::getValue($arParams, 'ORDER_URL')
];

foreach ($arResult['URL'] as $sKey => $sUrl)
    $arResult['URL'][$sKey] = StringHelper::replaceMacros(
        $sUrl,
        $arMacros
    );

$arVisual = [
    'TRANSPARENCY' => $arParams['TRANSPARENCY'] === 'Y'
];

if (empty($arTemplates['BANNER']))
    $arVisual['TRANSPARENCY'] = false;

$arResult['TEMPLATES'] = $arTemplates;
$arResult['VISUAL'] = $arVisual;

/** @var InnerTemplate $oTemplate */
foreach ($arTemplates as $oTemplate) {
    if (empty($oTemplate))
        continue;

    $oTemplate->modify($arParams, $arResult);
}