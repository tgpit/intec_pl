<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('intec.core')) {
    require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

    ShowError(Loc::getMessage('requirements.module', [
        '#MODULE#' => 'intec.core'
    ]));

    require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
    die();
}

if (!Loader::includeModule('intec.seo')) {
    require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

    ShowError(Loc::getMessage('requirements.module', [
        '#MODULE#' => 'intec.core'
    ]));

    require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
    die();
}

if (!Loader::includeModule('iblock')) {
    require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

    ShowError(Loc::getMessage('requirements.module', [
        '#MODULE#' => 'intec.core'
    ]));

    require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
    die();
}