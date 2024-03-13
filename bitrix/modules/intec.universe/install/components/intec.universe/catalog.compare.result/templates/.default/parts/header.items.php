<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

?>

<?php return function ($arHeaderItems, $position = 'left') { ?>
    <div class="catalog-compare-result-header-items-wrapper">
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-compare-result-header-items',
                'intec-grid' => [
                    '',
                    'nowrap',
                    'a-h-start',
                    'a-v-center'
                ]
            ],
            'data' => [
                'type' => 'compare.content',
                'position' => $position
            ]
        ]) ?>
        <?php $iCompareContentIndexItem = 0 ?>
        <?php foreach ($arHeaderItems as $arItem) { ?>
            <?php
            $sPicture = $arItem['PREVIEW_PICTURE'];

            if (empty($sPicture))
                $sPicture = $arItem['DETAIL_PICTURE'];

            if (!empty($sPicture)) {
                $sPicture = $sPicture['SRC'];
            }

            if (empty($sPicture))
                $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
            ?>
            <div class="catalog-compare-result-header-item intec-grid-item-5 intec-grid-item-768-1" data-role="slide" data-fixed="false" data-index="<?= $iCompareContentIndexItem ?>">
                <?= Html::beginTag('div', [
                    'class' => [
                        'intec-grid' => [
                            '',
                            'nowrap',
                            'a-h-start',
                            'a-v-center'
                        ]
                    ]
                ]) ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'intec-grid-item-auto'
                        ]
                    ]) ?>
                        <div class="catalog-compare-result-header-item-picture intec-ui-picture">
                            <?= Html::img($sPicture, [
                                'alt' => $arItem['NAME'],
                                'title' => $arItem['NAME']
                            ]) ?>
                        </div>
                    <?= Html::endTag('div') ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'intec-grid-item'
                        ]
                    ]) ?>
                        <?= Html::a($arItem['NAME'], $arItem['DETAIL_PAGE_URL'], [
                            'class' => [
                                'catalog-compare-result-header-item-name',
                                'intec-cl-text-hover'
                            ]
                        ]) ?>
                        <div class="catalog-compare-result-header-item-price">
                            <?php if (!empty($arItem['MIN_PRICE'])) { ?>
                                <?= $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'] ?>
                            <?php } ?>
                        </div>
                    <?= Html::endTag('div') ?>
                <?= Html::endTag('div') ?>
            </div>
            <?php $iCompareContentIndexItem++ ?>
        <?php } ?>
        <?php unset($iCompareContentIndexItem) ?>
        <?= Html::endTag('div') ?>
        <div class="catalog-compare-result-header-items-index">
            <span data-role="header.index" data-position="<?= $position ?>">1</span>
            <?= Loc::getMessage('C_CATALOG_COMPARE_RESULT_DEFAULT_TEMPLATE_INDEX', [
                '#INDEX#' => count($arHeaderItems)
            ]) ?>
        </div>
    </div>
<?php } ?>