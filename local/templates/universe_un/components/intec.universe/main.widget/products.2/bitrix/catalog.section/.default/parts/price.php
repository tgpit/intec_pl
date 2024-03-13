<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<?php return function (&$arPrice) use (&$arVisual, &$arSvg) { ?>
    <?= Html::beginTag('div', [
        'class' => 'widget-item-price',
        'data' => [
            'role' => 'item.price',
            'show' => !empty($arPrice),
            'discount' => !empty($arPrice) && $arPrice['PERCENT'] > 0 ? 'true' : 'false'
        ]
    ]) ?>
        <?= Html::beginTag('div', [
            'class' => [
                'widget-item-price-wrapper',
                'intec-grid' => [
                    '',
                    'wrap',
                    'i-5',
                    'a-v-center',
                    'a-h-'.$arVisual['PRICE']['ALIGN']
                ]
            ]
        ]) ?>
            <div class="widget-item-price-limited-width intec-grid-item-auto">
                <div class="widget-item-price-discount" data-role="item.price.discount">
                    <?= !empty($arPrice) ? $arPrice['PRINT_PRICE'] : null ?>
                </div>
            </div>
            <div class="widget-item-price-limited-width intec-grid-item-auto">
                <div class="widget-item-price-base" data-role="item.price.base">
                    <?= !empty($arPrice) ? $arPrice['PRINT_BASE_PRICE'] : null ?>
                </div>
            </div>
        <?= Html::endTag('div') ?>
        <?php if ($arVisual['PRICE']['PERCENT']) { ?>
            <div class="widget-item-price-percent-container">
                <div class="widget-item-price-percent">
                    <div class="widget-item-price-percent-value" data-role="price.percent">
                        <?= '-'.$arPrice['PERCENT'].'%' ?>
                    </div>
                    <?php if ($arVisual['PRICE']['ECONOMY']) { ?>
                        <div class="widget-item-price-percent-difference" data-role="price.difference">
                            <?= $arPrice['PRINT_DISCOUNT'] ?>
                        </div>
                        <div class="widget-item-price-percent-decoration">
                            <?= $arSvg['PRICE_DIFFERENCE'] ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?php } ?>