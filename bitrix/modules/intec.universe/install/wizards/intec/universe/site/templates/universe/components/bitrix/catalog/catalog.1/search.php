<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use Bitrix\Main\Loader;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$bIsBase = Loader::includeModule('catalog') && Loader::includeModule('sale');
$bIsLite = !$bIsBase && Loader::includeModule('intec.startshop');
$bIsAjax = false;

include(__DIR__.'/parts/search/search.php');
include(__DIR__.'/parts/search/sections.php');
include(__DIR__.'/parts/filter.php');
include(__DIR__.'/parts/elements.php');

$arFilter['SHOW'] = false;

if ($arParams['USE_FILTER'] === 'Y') {
    $searchFilterShow = true;
}

$arFilter['PARAMETERS'] = ArrayHelper::merge($arFilter['PARAMETERS'], [
    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
    'SHOW_ALL_WO_SECTION' => 'Y',
    "INCLUDE_SUBSECTIONS" => "Y",
    "SECTION_ID" => "",
    "PREFILTER_NAME" => "smartPreFilter",
    'SEF_MODE' => 'N',
    'POPUP_USE' => 'Y',
    'AJAX' => 'N'
]);

$arElements['PARAMETERS'] = ArrayHelper::merge($arElements['PARAMETERS'], [
    'SHOW_ALL_WO_SECTION' => 'Y'
]);

include(__DIR__.'/parts/sort.php');

$oRequest = Core::$app->request;

$hideMobileFilter = true;

if (!(($oRequest->getIsAjax() || isset($_SERVER['HTTP_BX_AJAX']))
        && $oRequest->get('ajax') === 'y')) {
    $this->SetViewTarget('panel_sort_search');
    include(__DIR__.'/parts/panel.php');
    $this->EndViewTarget();
}


foreach ($arSort['PROPERTIES'] as $arSortProperty) {
    if ($arSortProperty['ACTIVE']) {
        $arElements['PARAMETERS']['ELEMENT_SORT_FIELD'] = $arSortProperty['FIELD'];
        $arElements['PARAMETERS']['ELEMENT_SORT_ORDER'] = $arSort['ORDER'];

        break;
    }
}
unset($arSortProperty);

if ($arParams['USE_FILTER'] === 'Y') {
    $arFilter['SHOW'] = true;
}

$arSearch['PARAMETERS']['FILTER'] = $arFilter;
$arSearch['PARAMETERS']['ELEMENTS'] = $arElements;
$arSearch['PARAMETERS']['SECTIONS'] = $arSearchSections;

?>
<?= Html::beginTag('div', [
    'class' => [
        'ns-bitrix',
        'c-catalog',
        'c-catalog-catalog-1',
        'p-search'
    ],
    'id' => $sTemplateId
]) ?>
    <div class="catalog-wrapper intec-content intec-content-visible">
        <div class="catalog-wrapper-2 intec-content-wrapper">
            <?php $APPLICATION->IncludeComponent(
                'bitrix:catalog.search',
                $arSearch['TEMPLATE'],
                $arSearch['PARAMETERS'],
                $component
            ) ?>
        </div>
    </div>
<?= Html::endTag('div') ?>

<?php $APPLICATION->SetTitle(Loc::GetMessage('CATALOG_SEARCH_PAGE_TITLE'));?>