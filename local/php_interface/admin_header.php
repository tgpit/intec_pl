<?php

if(CModule::IncludeModule("intec.core") && class_exists('intec\core\AdminNotify') && file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intec.core/classes/AdminNotify.php"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intec.core/admin/banner.php");
