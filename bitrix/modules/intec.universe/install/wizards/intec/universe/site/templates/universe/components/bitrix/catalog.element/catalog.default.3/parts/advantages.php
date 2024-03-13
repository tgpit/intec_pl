<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

if (empty($arParams['ADVANTAGES_TEMPLATE']) || empty($arResult['ADVANTAGES']))
    return;

$GLOBALS['arCatalogElementFilter'] = [
    'ID' => $arResult['ADVANTAGES']
];

$sPrefix = 'ADVANTAGES_';

$sTemplate = 'catalog.'.$arParams[$sPrefix.'TEMPLATE'];

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $sPrefix))
        continue;

    $sKey = StringHelper::cut($sKey, StringHelper::length($sPrefix));

    if ($sKey === 'TEMPLATE')
        continue;

    $arProperties[$sKey] = $sValue;
}

unset($sPrefix, $sKey, $sValue);

$arProperties = ArrayHelper::merge($arProperties, [
    'FILTER_NAME' => 'arCatalogElementFilter',
    'WIDE' => $arVisual['WIDE'] ? 'Y' : 'N',
    'COLUMNS' => 2,
    'VIEW' => 'view.1'
]);

?>
<div class="catalog-element-advantages">
    <?php $APPLICATION->IncludeComponent(
        'intec.universe:main.advantages',
        $sTemplate,
        $arProperties,
        $component
    ) ?>
</div>
<?php unset($sTemplate, $arProperties) ?>