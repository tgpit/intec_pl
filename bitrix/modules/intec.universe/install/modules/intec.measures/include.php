<?php

use Bitrix\Main\Localization\Loc;

if (!require_once(__DIR__.'/classes/Loader.php'))
    return false;

Loc::loadMessages(__FILE__);

class IntecMeasures
{
    protected static $MODULE_ID = 'intec.measures';
}

?>