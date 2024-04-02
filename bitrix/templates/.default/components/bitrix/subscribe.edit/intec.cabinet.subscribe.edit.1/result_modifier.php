<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

$arResult['CONSENT'] = [
    'SHOW' => false,
    'URL' => null
];

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('intec.cabinet'))
    return;

IntecCabinet::Initialize();

$arConsent = [
    'SHOW' => false,
    'URL' => ArrayHelper::getValue($arParams, 'CONSENT_URL')
];

if (!empty($arConsent['URL'])) {
    $arConsent['URL'] = StringHelper::replaceMacros($arConsent['URL'], [
        'SITE_DIR' => SITE_DIR
    ]);
} else {
    $arConsent['SHOW'] = false;
}

$arResult['CONSENT'] = $arConsent;