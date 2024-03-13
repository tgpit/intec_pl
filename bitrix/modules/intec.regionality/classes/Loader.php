<?php

use Bitrix\Main\Loader;
use intec\Core;

if (Loader::includeModule('intec.core')) {
    Core::setAlias('@intec/regionality', __DIR__);
    Core::setAlias('@intec/regionality/module', dirname(__DIR__));
    Core::setAlias('@intec/regionality/resources', '@resources/intec.regionality');
    Core::setAlias('@intec/regionality/upload', '@upload/intec/regionality');
}