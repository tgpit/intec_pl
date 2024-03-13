<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

if (ArrayHelper::keyExists('PAGINATION_SAVE', $arParams))
    $arResult['bSavePage'] = $arParams['PAGINATION_SAVE'] === 'Y';
else
    $arResult['nav_page_in_session'] = Option::get('main', 'nav_page_in_session') !== 'N';