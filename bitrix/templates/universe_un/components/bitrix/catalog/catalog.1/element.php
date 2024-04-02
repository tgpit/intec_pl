<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);

$bSeo = Loader::includeModule('intec.seo');

$arParams = ArrayHelper::merge([
    'DETAIL_MENU_SHOW' => 'Y'
], $arParams);

$arIBlock = $arResult['IBLOCK'];
$arSection = $arResult['SECTION'];

include(__DIR__.'/parts/menu.php');
include(__DIR__.'/parts/element/element.php');

$arMenu['SHOW'] = $arMenu['SHOW'] && $arParams['DETAIL_MENU_SHOW'] === 'Y';
$arColumns = [
    'SHOW' => $arMenu['SHOW'] || ($arFilter['SHOW'] && $arFilter['TYPE'] === 'vertical')
];

if ($arColumns['SHOW']) {
    $arElement['PARAMETERS']['WIDE'] = 'N';
}

?>
<div class="ns-bitrix c-catalog c-catalog-catalog-1 p-element">
    <?php if ($arColumns['SHOW']) { ?>
        <div class="catalog-wrapper intec-content intec-content-visible">
            <div class="catalog-wrapper-2 intec-content-wrapper">
                <div class="catalog-content">
                    <div class="catalog-content-left intec-content-left">
                        <?php if ($arMenu['SHOW']) { ?>
                            <div class="catalog-menu">
                                <?php $APPLICATION->IncludeComponent(
                                    'bitrix:menu',
                                    $arMenu['TEMPLATE'],
                                    $arMenu['PARAMETERS'],
                                    $component
                                ) ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="catalog-content-right intec-content-right">
                        <div class="catalog-content-right-wrapper intec-content-right-wrapper">
    <?php } ?>
    <?php if ($arElement['SHOW']) { ?>
        <?php $arElement['ID'] = $APPLICATION->IncludeComponent(
            'bitrix:catalog.element',
            $arElement['TEMPLATE'],
            $arElement['PARAMETERS'],
            $component
        ) ?>
    <?php } ?>
    <?php if ($bSeo) { ?>
        <?php $APPLICATION->IncludeComponent('intec.seo:iblocks.metadata.loader', '', [
            'IBLOCK_ID' => $arIBlock['ID'],
            'ELEMENT_ID' => $arElement['ID'],
            'TYPE' => 'element',
            'MODE' => 'single',
            'METADATA_SET' => 'Y',
            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
            'CACHE_TIME' => $arParams['CACHE_TIME']
        ], $component) ?>

        <?php $arArticlesExtending['RESULT'] = $APPLICATION->IncludeComponent('intec.seo:iblocks.articles.extend.filter', '', [
            'IBLOCK_ID' => $arIBlock['ID'],
            'SECTION_ID' => $arSection['ID'],
            'ELEMENT_ID' => $arElement['ID'],
            'FILTER_MODE' => 'single',
            'QUANTITY' => $arArticlesExtending['QUANTITY'],
            'CURRENT_URL' => $APPLICATION->GetCurPage(),
            'FILTER_NAME' => 'arCatalogArticlesExtendingFilter',
            'INCLUDE_SUBSECTIONS' => 'Y',
            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
            'CACHE_TIME' => $arParams['CACHE_TIME']
        ], $component) ?>
        <?php if (!empty($arArticlesExtending['RESULT']) && !empty($arArticlesExtending['RESULT']['FILTER'])) { ?>
            <?php
            $arArticlesExtending['PARAMETERS']['IBLOCK_ID'] = $arArticlesExtending['RESULT']['FILTER']['IBLOCK_ID'];
            $arArticlesExtending['PARAMETERS']['IBLOCK_TYPE'] = $arArticlesExtending['RESULT']['IBLOCK']['IBLOCK_TYPE_ID'];
            $arArticlesExtending['PARAMETERS']['ELEMENT_SORT_ORDER'] = $arArticlesExtending['RESULT']['FILTER']['ID'];
            $arArticlesExtending['PARAMETERS']['NEWS_COUNT'] = count($arArticlesExtending['RESULT']['FILTER']['ID']);
            $arArticlesExtending['PARAMETERS']['FILTER_NAME'] = 'arCatalogArticlesExtendingFilter';
            ?>
            <?php if (!empty($arArticlesExtending['TITLE']) || Type::isNumeric($arArticlesExtending['TITLE'])) { ?>
                <div class="intec-content">
                    <div class="catalog-element-extending-title catalog-title">
                        <?= $arArticlesExtending['TITLE'] ?>
                    </div>
                </div>
            <?php } ?>
            <?php $APPLICATION->IncludeComponent(
                'bitrix:news.list',
                $arArticlesExtending['TEMPLATE'],
                $arArticlesExtending['PARAMETERS'],
                $component
            ) ?>
        <?php } ?>

    <?php } ?>
    <?php if ($arColumns['SHOW']) { ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
