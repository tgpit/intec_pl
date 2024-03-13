<?php

use Bitrix\Main\Loader;
use intec\Core;

if (Loader::includeModule('intec.core')) {
    Core::setAlias('@intec/importexport', __DIR__);
    Core::setAlias('@intec/importexport/module', dirname(__DIR__));
    Core::setAlias('@intec/importexport/resources', '@resources/intec.importexport');
    Core::setAlias('@intec/importexport/upload', '@upload/intec/importexport');
}