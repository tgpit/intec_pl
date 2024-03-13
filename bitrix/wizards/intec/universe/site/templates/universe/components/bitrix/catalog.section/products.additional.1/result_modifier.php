<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arResult['BASE'] = false;
$arResult['LITE'] = false;

if (!Loader::includeModule('intec.core'))
    return;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $arResult['BASE'] = true;
} else if (Loader::includeModule('intec.startshop')) {
    $arResult['LITE'] = true;
}

$arParams = ArrayHelper::merge([
    'TRIGGER' => null
], $arParams);

$arResult['TRIGGER'] = $arParams['TRIGGER'];

if (empty($arResult['TRIGGER']))
    $arResult['TRIGGER'] = 'additional';

if ($arResult['BASE']) {
    include(__DIR__.'/modifiers/base/catalog.php');
} else if ($arResult['LITE']) {
    include(__DIR__.'/modifiers/lite/catalog.php');
}

if ($arResult['BASE'] || $arResult['LITE'])
    include(__DIR__.'/modifiers/catalog.php');

$arResult['VISUAL'] = [
    'RECALCULATION' => $arParams['CONVERT_CURRENCY'] === 'Y' && !empty($arParams['CURRENCY_ID'])
];

$arResult['CURRENCY'] = [
    'THOUSAND' => null,
    'DECIMAL' => null,
    'PATTERN' => null
];

if ($arResult['LITE'] && $arResult['VISUAL']['RECALCULATION']) {
    $arCurrency = Arrays::fromDBResult(CStartShopCurrency::GetByCode($arParams['CURRENCY_ID']))->getFirst();

    if (!empty($arCurrency)) {
        $arResult['CURRENCY']['THOUSAND'] = ArrayHelper::getValue(
            $arCurrency['FORMAT'][LANGUAGE_ID],
            'DELIMITER_THOUSANDS'
        );
        $arResult['CURRENCY']['DECIMAL'] = ArrayHelper::getValue(
            $arCurrency['FORMAT'][LANGUAGE_ID],
            'DELIMITER_DECIMAL'
        );
        $arResult['CURRENCY']['PATTERN'] = ArrayHelper::getValue(
            $arCurrency['FORMAT'][LANGUAGE_ID],
            'FORMAT'
        );
    }

    unset($arCurrency);
}