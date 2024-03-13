<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

if (empty($arResult['CATEGORIES']))
    return;

$arVisual = &$arResult['VISUAL'];

if (!$arVisual['SHOW'])
    return;

Loc::loadMessages(__FILE__);

$APPLICATION->ShowAjaxHead(true, true, false, false);

$arAllItem = null;

if (!empty($arResult['CATEGORIES']['all']) && !empty($arResult['CATEGORIES']['all']['ITEMS'][0]))
    $arAllItem = $arResult['CATEGORIES']['all']['ITEMS'][0];

?>
<div class="ns-bitrix c-search-title c-search-title-input-1 search-title-results">
    <div class="search-title-categories">
        <?php if ($arVisual['RESULTS']['SHOW']) { ?>
            <div class="search-title-category">
                <div class="search-title-category-title">
                    <?= Loc::getMessage('C_SEARCH_TITLE_INPUT_1_CATEGORIES_RESULTS_TITLE') ?>
                </div>
                <div class="search-title-category-items">
                    <?php foreach ($arResult['CATEGORIES'] as $sKey => &$arCategory) { ?>
                        <?php foreach ($arCategory['ITEMS'] as &$arItem) { ?>
                            <?php if ($sKey === 'all' || empty($arItem['ITEM_ID'])) continue; ?>
                            <div class="search-title-category-item">
                                <?= Html::beginTag('a', [
                                    'class' => [
                                        'search-title-category-item-link',
                                        'intec-cl-text-hover'
                                    ],
                                    'href' => $arItem['URL']
                                ]) ?>
                                    <?= $arItem['NAME'] ?>
                                <?= Html::endTag('a') ?>
                            </div>
                        <?php } ?>
                        <?php unset($arItem) ?>
                    <?php } ?>
                    <?php unset($arCategory) ?>
                </div>
            </div>
        <?php } ?>
        <?php if ($arVisual['SECTIONS']['SHOW']) { ?>
            <div class="search-title-category">
                <div class="search-title-category-title">
                    <?= Loc::getMessage('C_SEARCH_TITLE_INPUT_1_CATEGORIES_SECTIONS_TITLE') ?>
                </div>
                <div class="search-title-category-items">
                    <?php foreach ($arResult['SECTIONS'] as &$arSection) { ?>
                        <div class="search-title-category-item">
                            <?= Html::beginTag('a', [
                                'class' => [
                                    'search-title-category-item-link',
                                    'intec-cl-text-hover'
                                ],
                                'href' => $arSection['SECTION_PAGE_URL']
                            ]) ?>
                                <?= $arSection['NAME'] ?>
                            <?= Html::endTag('a') ?>
                        </div>
                    <?php } ?>
                    <?php unset($arSection) ?>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php if ($arVisual['PRODUCTS']['SHOW'] || !empty($arAllItem)) { ?>
    <div class="search-title-additional">
        <?php if ($arVisual['PRODUCTS']['SHOW']) { ?>
            <?php include(__DIR__.'/parts/products.php') ?>
        <?php } ?>
        <?php if (!empty($arAllItem)) { ?>
            <div class="search-title-additional-button">
                <?= Html::beginTag('a', [
                    'class' => [
                        'intec-ui' => [
                            '',
                            'control-button',
                            'mod-round-2',
                            'scheme-current'
                        ],
                        'search-title-additional-button-control'
                    ],
                    'href' => $arAllItem['URL']
                ]) ?>
                    <?= $arAllItem['NAME'] ?>
                <?= Html::endTag('a') ?>
            </div>
        <?php } ?>
    </div>
    <?php } ?>
</div>