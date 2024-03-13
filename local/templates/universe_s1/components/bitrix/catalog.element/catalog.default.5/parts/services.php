<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arVisual
 * @var array $arFields
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

$sPrefix = 'SERVICES_';

$iLength = StringHelper::length($sPrefix);

$arProperties = [];
$arExcluded = [
    'SHOW',
    'NAME'
];

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $sPrefix))
        continue;

    $sKey = StringHelper::cut($sKey, $iLength);

    if (ArrayHelper::isIn($sKey, $arExcluded))
        continue;

    $arProperties[$sKey] = $sValue;
}

unset($sPrefix, $iLength, $arExcluded, $sKey, $sValue);

$arProperties = ArrayHelper::merge([
    'FILTER' => [
        'ID' => $arFields['SERVICES']['VALUES']
    ],
    'SECTIONS_MODE' => 'id',
    'SECTIONS' => [],
    'ELEMENTS_COUNT' => null,
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'HEADER_SHOW' => 'N',
    'DESCRIPTION_SHOW' => 'N',
    'FOOTER_SHOW' => 'N',
    'SECTION_SHOW' => 'Y',
    'SLIDER_NAV' => 'Y',
    'SLIDER_LOOP' => 'N',
    'SLIDER_AUTOPLAY' => 'N',
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'SORT_BY' => 'SORT',
    'ORDER_BY' => 'ASC'
], $arProperties);

$arProperties['COLUMNS'] = $arVisual['MENU']['SHOW'] ? 2 : $arProperties['COLUMNS'];

if (empty($arVisual['SERVICES']['NAME']))
    $arVisual['SERVICES']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_SERVICES_NAME_DEFAULT');

?>
<div class="catalog-element-services-container catalog-element-additional-block">
    <div class="catalog-element-additional-block-name">
        <?= $arVisual['SERVICES']['NAME'] ?>
    </div>
    <div class="catalog-element-additional-block-content">
        <?php $APPLICATION->IncludeComponent(
            'intec.universe:main.services',
            'template.26',
            $arProperties,
            $component
        ) ?>
    </div>
</div>
<?php unset($arProperties) ?>