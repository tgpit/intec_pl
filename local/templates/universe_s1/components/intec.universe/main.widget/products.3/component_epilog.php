<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;

if (Loader::includeModule('currency'))
    CJSCore::Init(['currency']);
