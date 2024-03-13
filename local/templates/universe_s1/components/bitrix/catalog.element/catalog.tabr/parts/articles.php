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

$sPrefix = 'ARTICLES_';

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
        'ID' => $arFields['ARTICLES']['VALUES']
    ],
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'ELEMENTS_COUNT' => null,
    'HEADER_BLOCK_SHOW' => 'N',
    'DESCRIPTION_BLOCK_SHOW' => 'N',
    'FOOTER_SHOW' => 'N',
    'LIST_PAGE_URL' => null,
    'SECTION_URL' => null,
    'DETAIL_URL' => null,
    'SORT_BY' => 'SORT',
    'ORDER_BY' => 'ASC',
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME']
], $arProperties);

if (empty($arVisual['ARTICLES']['NAME']))
    $arVisual['ARTICLES']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_ARTICLES_NAME_DEFAULT');

?>
<div class="catalog-element-articles-container catalog-element-additional-block">
    <div class="catalog-element-additional-block-name">
        <?= $arVisual['ARTICLES']['NAME'] ?>
    </div>
    <div class="catalog-element-additional-block-content">
        <?php $APPLICATION->IncludeComponent(
            'intec.universe:main.news',
            'template.8',
            $arProperties,
            $component
        ) ?>
    </div>
</div>
<?php unset($arProperties) ?>