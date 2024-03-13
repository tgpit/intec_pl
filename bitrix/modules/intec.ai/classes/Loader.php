<?php

use Bitrix\Main\Loader;
use intec\Core;

if (!Loader::includeModule('iblock'))
    return false;

if (Loader::includeModule('intec.core')) {
    Core::setAlias('@intec/ai', __DIR__);
    Core::setAlias('@intec/ai/models', __DIR__);
    Core::setAlias('@intec/ai/resources', '@resources/intec.ai');
    return true;
}

return false;