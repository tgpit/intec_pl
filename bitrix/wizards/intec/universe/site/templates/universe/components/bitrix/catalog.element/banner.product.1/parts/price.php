<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arResult
 */

$bShowDiscount =  $arPrice['PERCENT'] > 0;

?>

<div class="catalog-element-price" data-discount="<?= $bShowDiscount ? 'true' : 'false' ?>">
    <?= Html::beginTag('div', [
        'class' => [
            'intec-grid' => [
                '',
                'wrap',
                'i-h-8',
                'i-v-4',
                'a-v-end',
                'a-h-768-center'
            ]
        ]
    ]) ?>
        <?php
        $sMeasure = !empty($arResult['ITEM_MEASURE']['TITLE']) ? ' / '.$arResult['ITEM_MEASURE']['TITLE'] : '';
        $sDiscountPrice = (empty($arResult['OFFERS'])) ? $arPrice['PRINT_PRICE'].$sMeasure : Loc::getMessage('C_CATALOG_ELEMENT_BANNER_PRODUCT_1_PRICE_FROM').' '.$arPrice['PRINT_PRICE'].$sMeasure;
        $sBasePrice = (empty($arResult['OFFERS'])) ? $arPrice['PRINT_BASE_PRICE'].$sMeasure : Loc::getMessage('C_CATALOG_ELEMENT_BANNER_PRODUCT_1_PRICE_FROM').' '.$arPrice['PRINT_BASE_PRICE'].$sMeasure;
        ?>
        <div class="catalog-element-price-discount intec-grid-item-auto">
            <?= $sDiscountPrice ?>
        </div>
        <div class="catalog-element-price-base intec-grid-item-auto">
            <?= $sBasePrice ?>
        </div>
        <?php unset($sMeasure, $sDiscountPrice, $sBasePrice); ?>
    <?= Html::endTag('div') ?>
</div>
<?php if ($bShowDiscount) { ?>
    <div class="catalog-element-discount">
        <div class="catalog-element-discount-percent">
            <?= '-'.$arPrice['PERCENT'].'%' ?>
        </div>
        <?php if ($arVisual['PRICE']['DIFFERENCE'] && $arPrice['RATIO_DISCOUNT'] !== 0) { ?>
            <div class="catalog-element-discount-difference">
                <?= $arPrice['RATIO_DISCOUNT'] ?>
            </div>
            <div class="intec-ui-picture">
                <?= $arSvg['DISCOUNT'] ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>
