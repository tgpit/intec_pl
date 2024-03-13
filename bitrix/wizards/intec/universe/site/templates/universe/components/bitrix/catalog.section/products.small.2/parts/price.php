<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<?php return function (&$price, $recalculation = false, $offer = false) use (&$arVisual) { ?>
    <?php if ($recalculation) { ?>
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-element-item-price-wrapper'
            ],
            'data' => [
                'role' => 'item.price',
                'show' => !empty($price),
                'discount' => !empty($price) && $price['PERCENT'] > 0 ? 'true' : 'false',
                'align' => $arVisual['PRICE']['ALIGN']
            ]
        ]) ?>
            <div class="catalog-section-item-price-discount">
                <span data-role="item.price.discount">
                    <?= !empty($price) ? $price['PRINT_PRICE'] : null ?>
                </span>
            </div>
            <div class="catalog-section-item-price-base" data-role="item.price.base">
                <?= !empty($price) ? $price['PRINT_BASE_PRICE'] : null ?>
            </div>
        <?= Html::endTag('div') ?>
    <?php } else { ?>
        <div class="catalog-element-item-price-wrapper" data-align="<?= $arVisual['PRICE']['ALIGN'] ?>">
            <div class="catalog-section-item-price-discount">
                <?php if ($offer) { ?>
                    <span>
                        <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_PRICE_FROM') ?>
                    </span>
                <?php } ?>
                <span>
                    <?= $price['PRINT_DISCOUNT_VALUE'] ?>
                </span>
            </div>
            <?php if (!empty($price['PRINT_VALUE'])) { ?>
                <div class="catalog-section-item-price-base">
                    <?= $price['PRINT_VALUE'] ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
<?php } ?>