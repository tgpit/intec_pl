<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<?php $vPrice = function (&$arPrice) use (&$arVisual) { ?>
    <?= Html::beginTag('div', [
        'class' => 'catalog-products-viewed-item-price',
        'data' => [
            'role' => 'item.price',
            'show' => !empty($arPrice)
        ]
    ]) ?>
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-products-viewed-item-price-wrapper',
                'intec-grid' => [
                    '',
                    'wrap',
                    'a-v-center',
                    'a-h-'.$arVisual['PRICE']['ALIGN'],
                    'i-5'
                ]
            ]
        ]) ?>
            <div class="intec-grid-item-auto">
                <div class="catalog-products-viewed-item-price-discount" data-role="item.price.discount">
                    <?= !empty($arPrice) ? $arPrice['PRINT_PRICE'] : null ?>
                </div>
            </div>
            <?php if (!empty($arPrice) && $arPrice['PERCENT'] > 0) { ?>
                <div class="intec-grid-item-auto">
                    <div class="catalog-products-viewed-item-price-base" data-role="item.price.base">
                        <?= !empty($arPrice) ? $arPrice['PRINT_BASE_PRICE'] : null ?>
                    </div>
                </div>
            <?php } ?>
        <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
<?php } ?>