<?php

use Bitrix\Main\Loader;
use intec\Core;

if (!Loader::includeModule('catalog'))
    return false;

if (Loader::includeModule('intec.core')) {
    Core::setAlias('@intec/measures', __DIR__);
    Core::setAlias('@intec/measures/module', dirname(__DIR__));
    Core::setAlias('@intec/measures/resources', '@resources/intec.measures');
    Core::setAlias('@intec/measures/upload', '@upload/intec/measures');

    return true;
}

return false;