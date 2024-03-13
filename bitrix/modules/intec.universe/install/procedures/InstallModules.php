<?php

use Bitrix\Main\Loader;

$directory = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/modules";
        
if (!Loader::includeModule('catalog') && !Loader::includeModule('intec.startshop')) {
    $file = $directory.'/intec.startshop/install/index.php';

    if (is_file($file)) {
        require_once($file);
        $module = new intec_startshop();
        $module->__construct();
        $module->MODE = 'SILENT';
        $module->DoInstall();
    }
}

if (Loader::includeModule('catalog') && !Loader::includeModule('intec.measures')) {
    $file = $directory.'/intec.measures/install/index.php';

    if (is_file($file)) {
        require_once($file);
        $module = new intec_measures();
        $module->__construct();
        $module->DoInstall();
    }
}

if (!Loader::includeModule('intec.constructorlite')) {
    $file = $directory.'/intec.constructorlite/install/index.php';

    if (is_file($file)) {
        require_once($file);
        $module = new intec_constructorlite();
        $module->__construct();
        $module->DoInstall();
    }
}