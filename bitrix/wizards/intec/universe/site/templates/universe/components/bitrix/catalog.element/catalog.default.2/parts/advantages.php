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

unset($sKey, $sValue);

$iColumns = $arProperties['COLUMNS'];
$sView = $arProperties['VIEW'];

if ($arVisual['VIEW']['VALUE'] === 'narrow')
    $iColumns = 2;

if (!$arVisual['WIDE'] || $arVisual['VIEW']['VALUE'] !== 'tabs')
    $sView = 'view.1';

$arProperties = ArrayHelper::merge($arProperties, [
    'FILTER_NAME' => 'arCatalogElementFilter',
    'WIDE' => $arVisual['WIDE'] ? 'Y' : 'N',
    'COLUMNS' => $iColumns,
    'VIEW' => $sView
]);

unset($sPrefix, $iColumns, $sView) ?>
<?php if ($arVisual['WIDE'] && $arVisual['VIEW']['VALUE'] !== 'narrow') { ?>
    </div>
</div>
<?php } ?>
    <div class="catalog-element-advantages" <?= $arVisual['WIDE'] && $arVisual['VIEW']['VALUE'] === 'tabs' ? 'data-tabs="true"' : '' ?>>
        <?php $APPLICATION->IncludeComponent(
            'intec.universe:main.advantages',
            $sTemplate,
            $arProperties,
            $component
        ) ?>
    </div>
<?php if ($arVisual['WIDE'] && $arVisual['VIEW']['VALUE'] !== 'narrow') { ?>
<div class="catalog-element-wrapper intec-content intec-content-visible">
    <div class="catalog-element-wrapper-2 intec-content-wrapper">
<?php } ?>
<?php unset($sTemplate, $arProperties) ?>