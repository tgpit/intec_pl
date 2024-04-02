<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

?>
<div class="ns-bitrix c-search-title c-search-title-popup-1 search-title-results search-title-results-list-2 intec-content-wrap">
    <div class="search-title-results-wrapper intec-content">
        <div class="search-title-results-wrapper-2 intec-content-wrapper">
            <div class="search-title-results-wrapper-3">
                <div class="search-title-categories">
                    <?php if ($arVisual['RESULTS']['SHOW']) { ?>
                        <div class="search-title-category">
                            <div class="search-title-category-title">
                                <?= Loc::getMessage('C_SEARCH_TITLE_POPUP_1_CATEGORIES_RESULTS_TITLE') ?>
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
                                <?= Loc::getMessage('C_SEARCH_TITLE_POPUP_1_CATEGORIES_SECTIONS_TITLE') ?>
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
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'search-title-additional' => true,
                            'search-title-additional-mobile-hide' => !$arVisual['PRODUCTS']['SHOW'] && !empty($arAllItem)
                        ],true)
                    ]) ?>
                        <?php if ($arVisual['PRODUCTS']['SHOW']) { ?>
                            <?php include(__DIR__.'/../../parts/products.php') ?>
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
                    <?= Html::endTag('div')?>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php if (!empty($arAllItem)) { ?>
        <div class="search-title-additional-button mobile">
            <?= Html::tag('a', $arAllItem['NAME'], [
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
        </div>
    <?php } ?>
</div>
