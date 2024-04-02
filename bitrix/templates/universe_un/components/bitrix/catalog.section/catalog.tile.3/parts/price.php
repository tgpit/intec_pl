<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<?php $vPrice = function (&$arPrice) use (&$arVisual, &$arItem, &$arSvg) { ?>
    <?= Html::beginTag('div', [
        'class' => 'catalog-section-item-price',
        'data' => [
            'role' => 'item.price',
            'show' => !empty($arPrice),
            'discount' => !empty($arPrice) && $arPrice['PERCENT'] > 0 ? 'true' : 'false',
            'align' => $arVisual['PRICE']['ALIGN']
        ]
    ]) ?>
        <div class="catalog-section-item-price-discount">
            <?php if (!$arVisual['OFFERS']['USE'] && !empty($arItem['OFFERS'])) { ?>
                <span>
                    <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_3_PRICE_FROM') ?>
                </span>
            <?php } ?>
            <span data-role="item.price.discount">
                <?= !empty($arPrice) ? $arPrice['PRINT_PRICE'] : null ?>
            </span>
            <?php if (!empty($arPrice) && $arVisual['MEASURE']['SHOW'] && !empty($arItem['CATALOG_MEASURE_NAME'])) { ?>
                /
                <span data-role="item.price.measure">
                    <?= $arItem['CATALOG_MEASURE_NAME'] ?>
                </span>
            <?php } ?>
        </div>
        <div class="catalog-section-item-price-base" data-role="item.price.base">
            <?= !empty($arPrice) ? $arPrice['PRINT_BASE_PRICE'] : null ?>
        </div>
        <?php if ($arVisual['PRICE']['PERCENT']) { ?>
            <div class="catalog-section-item-price-percent-container">
                <div class="catalog-section-item-price-percent">
                    <div class="catalog-section-item-price-percent-value" data-role="price.percent">
                        <?= '-'.$arPrice['PERCENT'].'%' ?>
                    </div>
                    <?php if ($arVisual['PRICE']['ECONOMY']) { ?>
                        <div class="catalog-section-item-price-percent-difference" data-role="price.difference">
                            <?= $arPrice['PRINT_DISCOUNT'] ?>
                        </div>
                        <div class="catalog-section-item-price-percent-decoration">
                            <?= $arSvg['PRICE_DIFFERENCE'] ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?php } ?>