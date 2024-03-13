<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$arNavigation = !empty($arResult['NAV_RESULT']) ? [
    'NavPageCount' => $arResult['NAV_RESULT']->NavPageCount,
    'NavPageNomer' => $arResult['NAV_RESULT']->NavPageNomer,
    'NavNum' => $arResult['NAV_RESULT']->NavNum
] : [
    'NavPageCount' => 1,
    'NavPageNomer' => 1,
    'NavNum' => $this->randString()
];

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$sTemplateContainer = $sTemplateId.'-'.$arNavigation['NavNum'];

$arVisual = $arResult['VISUAL'];

$arVisual['NAVIGATION']['LAZY']['BUTTON'] =
    $arVisual['NAVIGATION']['LAZY']['BUTTON'] &&
    $arNavigation['NavPageNomer'] < $arNavigation['NavPageCount'];

$arSvg = [
    'STUB' => FileHelper::getFileData(__DIR__.'/svg/stub.svg')
];

?>
<div class="ns-bitrix c-catalog-section c-catalog-section-services-tile-4" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arVisual['NAVIGATION']['TOP']['SHOW']) { ?>
                <div class="catalog-section-navigation-top" data-pagination-num="<?= $arNavigation['NavNum'] ?>">
                    <!-- pagination-container -->
                    <?= $arResult['NAV_STRING'] ?>
                    <!-- pagination-container -->
                </div>
            <?php } ?>
            <!-- items-container -->
            <div class="catalog-section-content intec-grid intec-grid-wrap intec-grid-a-v-stretch" data-entity="<?= $sTemplateContainer ?>">
                <?php foreach ($arResult['ITEMS'] as $arItem) {

                    $sId = $sTemplateId.'_'.$arItem['ID'];
                    $sAreaId = $this->GetEditAreaId($sId);
                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                    $arData = $arItem['DATA'];

                ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'intec-grid-item' => [
                                $arVisual['COLUMNS'] => true,
                                '1024-3' => $arVisual['COLUMNS'] >= 4,
                                '768-2' => true,
                                '500-1' => true
                            ]
                        ], true),
                        'data-entity' => 'items-row'
                    ]) ?>
                        <div class="catalog-section-item" id="<?= $sAreaId ?>">
                            <div class="catalog-section-item-content">
                                <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                                    <?= Html::beginTag('div', [
                                        'class' => Html::cssClassFromArray([
                                            'catalog-section-item-picture' => true,
                                            'intec-ui-picture' => true,
                                            'intec-cl-svg' => $arVisual['PICTURE']['COLOR_USE']
                                        ], true)
                                    ]) ?>
                                        <?php if (!empty($arData['PICTURE']['VALUE']['SRC'])) { ?>
                                            <?= FileHelper::getFileData($_SERVER['DOCUMENT_ROOT'].'/'.$arData['PICTURE']['VALUE']['SRC']) ?>
                                        <?php } else { ?>
                                            <?= $arSvg['STUB'] ?>
                                        <?php } ?>
                                     <?= Html::endTag('div') ?>
                                <?php } ?>
                                <div class="catalog-section-item-name">
                                    <?= Html::tag('a', $arItem['NAME'], [
                                        'class' => 'intec-cl-text-hover',
                                        'href' => $arItem['DETAIL_PAGE_URL']
                                    ]) ?>
                                </div>
                                <?php if ($arVisual['PROPERTIES']['SHOW'] && !empty($arItem['DISPLAY_PROPERTIES'])) { ?>
                                    <?php $iCount = 0 ?>
                                    <div class="catalog-section-item-properties">
                                        <?php foreach ($arItem['DISPLAY_PROPERTIES'] as $arProperty) {
                                            if ($arVisual['PROPERTIES']['COUNT'] > 0 && $iCount == $arVisual['PROPERTIES']['COUNT'])
                                                break;

                                            if (empty($arProperty['DISPLAY_VALUE']))
                                                continue;

                                            if (Type::isArray($arProperty['DISPLAY_VALUE']))
                                                $arProperty['DISPLAY_VALUE'] = implode(', ', $arProperty['DISPLAY_VALUE']);

                                        ?>
                                            <div class="catalog-section-item-properties-item">
                                                <?= Html::tag('div', $arProperty['NAME'].':', [
                                                    'class' => [
                                                        'catalog-section-item-properties-item-name',
                                                        'catalog-section-item-properties-item-part'
                                                    ]
                                                ]) ?>
                                                <?= Html::tag('div', $arProperty['DISPLAY_VALUE'], [
                                                    'class' => [
                                                        'catalog-section-item-properties-item-value',
                                                        'catalog-section-item-properties-item-part'
                                                    ]
                                                ]) ?>
                                            </div>
                                            <?php $iCount++ ?>
                                        <?php } ?>
                                    </div>
                                    <?php unset($iCount); ?>
                                <?php } ?>
                                <?php if ($arData['PRICE']['CURRENT']['SHOW']) { ?>
                                    <div class="catalog-section-item-price">
                                        <?= Html::tag('div', $arData['PRICE']['CURRENT']['VALUE'], [
                                            'class' => [
                                                'catalog-section-item-price-current',
                                                'catalog-section-item-price-item'
                                            ]
                                        ]) ?>
                                        <?php if ($arData['PRICE']['OLD']['SHOW']) { ?>
                                            <?= Html::tag('div', $arData['PRICE']['OLD']['VALUE'], [
                                                'class' => [
                                                    'catalog-section-item-price-old',
                                                    'catalog-section-item-price-item'
                                                ]
                                            ]) ?>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            </div>
            <!-- items-container -->
            <?php if ($arVisual['NAVIGATION']['LAZY']['BUTTON']) { ?>
                <!-- noindex -->
                    <div class="catalog-section-more" data-use="show-more-<?= $arNavigation['NavNum'] ?>">
                        <div class="catalog-section-more-button">
                            <div class="catalog-section-more-icon intec-cl-svg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                    <path d="M16.5059 9.00153L15.0044 10.5015L13.5037 9.00153"  stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4.75562 4.758C5.84237 3.672 7.34312 3 9.00137 3C12.3171 3 15.0051 5.6865 15.0051 9.0015C15.0051 9.4575 14.9496 9.9 14.8536 10.3268" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M1.4939 8.99847L2.9954 7.49847L4.49615 8.99847"  stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M13.2441 13.242C12.1574 14.328 10.6566 15 8.99838 15C5.68263 15 2.99463 12.3135 2.99463 8.99853C2.99463 8.54253 3.05013 8.10003 3.14613 7.67328" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="catalog-section-more-text intec-cl-text">
                                <?= Loc::getMessage('C_CATALOG_SECTION_SERVICES_TILE_4_TEMPLATE_LAZY_LOAD_TEXT') ?>
                            </div>
                        </div>
                    </div>
                <!-- /noindex -->
            <?php } ?>
            <?php if ($arVisual['NAVIGATION']['BOTTOM']['SHOW']) { ?>
                <div class="catalog-section-navigation-bottom" data-pagination-num="<?= $arNavigation['NavNum'] ?>">
                    <!-- pagination-container -->
                    <?= $arResult['NAV_STRING'] ?>
                    <!-- pagination-container -->
                </div>
            <?php } ?>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>