<?php

use Bitrix\Main\Loader;
use intec\Core;

if (Loader::includeModule('intec.core')) {
    Core::setAlias('@intec/seo', __DIR__);
    Core::setAlias('@intec/seo/module', dirname(__DIR__));
    Core::setAlias('@intec/seo/resources', '@resources/intec.seo');
    Core::setAlias('@intec/seo/upload', '@upload/intec/seo');
}