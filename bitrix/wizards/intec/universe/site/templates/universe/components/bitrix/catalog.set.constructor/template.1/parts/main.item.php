<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

$arMain = $arResult['ELEMENT'];

$sPicture = $arMain['DETAIL_PICTURE'];

if (empty($sPicture))
    $sPicture = $arMain['PREVIEW_PICTURE'];

if (!empty($sPicture)) {
    $sPicture = CFile::ResizeImageGet($sPicture, [
        'width' => 80,
        'height' => 80
    ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

    if (!empty($sPicture))
        $sPicture = $sPicture['src'];
}

if (empty($sPicture))
    $sPicture = SITE_TEMPLATE_PATH . '/images/picture.missing.png';

?>
<?= Html::beginTag('div', [
    'class' => 'constructor-main-item',
    'data' => [
        'role' => 'main.item',
        'id' => $arMain['ID'],
        'available' => $arMain['CAN_BUY'] ? 'true' : 'false',
        'quantity' => $arMain['BASKET_QUANTITY'],
        'price-current' => $arMain['PRICE_DISCOUNT_VALUE'],
        'price-old' => $arMain['PRICE_VALUE'],
        'price-difference' => $arMain['PRICE_DISCOUNT_DIFFERENCE_VALUE']
    ]
]) ?>
    <div class="intec-grid intec-grid-i-8">
        <div class="intec-grid-item-auto">
            <div class="constructor-main-item-picture">
                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                    'class' => 'intec-image-effect',
                    'alt' => $arMain['NAME'],
                    'title' => $arMain['NAME'],
                    'loading' => 'lazy',
                    'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                    'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                ]) ?>
            </div>
        </div>
        <div class="intec-grid-item">
            <div class="constructor-main-item-name">
                <?= $arMain['NAME'] ?>
            </div>
            <div class="constructor-main-item-price">
                <?php if ($arMain['PRICE_VALUE'] !== $arMain['PRICE_DISCOUNT_VALUE']) { ?>
                    <?= Html::tag('div', $arMain['PRICE_PRINT_VALUE'], [
                        'class' => [
                            'constructor-main-item-price-old',
                            'constructor-main-item-price-item'
                        ]
                    ]) ?>
                <?php } ?>
                <?= Html::tag('div', $arMain['PRICE_PRINT_DISCOUNT_VALUE'], [
                    'class' => [
                        'constructor-main-item-price-current',
                        'constructor-main-item-price-item'
                    ]
                ]) ?>
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>
<?php unset($arMain, $sPicture) ?>