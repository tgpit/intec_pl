<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\core\helpers\Html;
use intec\core\helpers\Type;
use intec\core\net\Url;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (Loader::includeModule('intec.seo')) {
    $APPLICATION->IncludeComponent('intec.seo:filter.loader', '', [
        'FILTER_RESULT' => $arResult
    ], $component);
}

if (!Type::isArray($arParams['PRICES_EXPANDED']))
    $arParams['PRICES_EXPANDED'] = [];

if (Loader::includeModule('intec.startshop'))
    include(__DIR__.'/modifier/lite.php');

foreach ($arResult['ITEMS'] as $sKey => &$arItem) {
    if (!isset($arItem['DISPLAY_EXPANDED']))
        $arItem['DISPLAY_EXPANDED'] = 'N';

    if (isset($arItem['PRICE']) && $arItem['PRICE']) {
        $arItem['DISPLAY_TYPE'] = 'A';

        if (ArrayHelper::isIn($sKey, $arParams['PRICES_EXPANDED']))
            $arItem['DISPLAY_EXPANDED'] = 'Y';
    }

    foreach ($arItem['VALUES'] as &$arValue) {
        if (isset($arValue['CONTROL_ID']))
            $arValue['CONTROL_ID'] = $arValue['CONTROL_ID'].'_mobile';
    }

    unset($arValue);
}

unset($arItem);

$oRequest = Core::$app->request;

if (($oRequest->getIsAjax() || isset($_SERVER['HTTP_BX_AJAX'])) && $oRequest->get('ajax') === 'y') {
    $oUrl = new Url(Html::decode($arResult['FILTER_URL']));
    $sQuery = $oUrl->getQuery()->get('q');
    $sQuery = Encoding::convert($sQuery, null, Encoding::UTF8);

    $oUrl->getQuery()->set('q', $sQuery);
    $arResult['FILTER_URL'] = Html::encode($oUrl->build());
}