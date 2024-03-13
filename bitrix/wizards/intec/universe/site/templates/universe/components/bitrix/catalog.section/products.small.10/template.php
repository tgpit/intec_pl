<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section',
        'c-catalog-section-products-small-9'
    ]
]) ?>
    <div class="catalog-section-items">
        <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
        <?php
            $sPicture = null;

            if (!empty($arItem['PICTURES']['VALUES'])) {
                $sPicture = reset($arItem['PICTURES']['VALUES']);
                $sPicture = CFile::ResizeImageGet($sPicture, [
                    'width' => 74,
                    'height' => 74
                ], BX_RESIZE_IMAGE_PROPORTIONAL);

                if (!empty($sPicture))
                    $sPicture = $sPicture['src'];
            }

            if (empty($sPicture))
                $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

            $arPrice = [
                'base' => null,
                'discount' => null
            ];

            if (!empty($arItem['MIN_PRICE'])) {
                $arPrice['base'] = $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'];

                if ($arItem['MIN_PRICE']['DISCOUNT_DIFF'] > 0)
                    $arPrice['discount'] = $arItem['MIN_PRICE']['PRINT_VALUE'];

                if (!empty($arItem['CATALOG_MEASURE_NAME'])) {
                    $arPrice['base'] .= ' / '.$arItem['CATALOG_MEASURE_NAME'];

                    if ($arPrice['discount'] !== null)
                        $arPrice['discount'] .= ' / '.$arItem['CATALOG_MEASURE_NAME'];
                }

                if (!empty($arItem['OFFERS'])) {
                    $arPrice['base'] = Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_10_TEMPLATE_PRICE_FROM', [
                        '#PRICE#' => $arPrice['base']
                    ]);

                    if ($arPrice['discount'] !== null)
                        $arPrice['discount'] = Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_10_TEMPLATE_PRICE_FROM', [
                            '#PRICE#' => $arPrice['discount']
                        ]);
                }
            }
        ?>
            <?= Html::beginTag('a', [
                'class' => 'catalog-section-item',
                'href' => $arItem['DETAIL_PAGE_URL']
            ]) ?>
                <div class="catalog-section-item-wrapper">
                    <div class="catalog-section-item-image" >
                        <div class="catalog-section-item-image-wrapper intec-ui-picture">
                            <?= Html::img($sPicture) ?>
                        </div>
                    </div>
                    <div class="catalog-section-item-information">
                        <div class="catalog-section-item-name">
                            <?= $arItem['NAME'] ?>
                        </div>
                        <?php if ($arPrice['base'] !== null) { ?>
                            <div class="catalog-section-item-price">
                                <div class="catalog-section-item-price-base">
                                    <?= $arPrice['base'] ?>
                                </div>
                                <?php if ($arPrice['discount'] !== null) { ?>
                                    <div class="catalog-section-item-price-discount">
                                        <?= $arPrice['discount'] ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?= Html::endTag('a') ?>
        <?php } ?>
    </div>
<?= Html::endTag('div') ?>