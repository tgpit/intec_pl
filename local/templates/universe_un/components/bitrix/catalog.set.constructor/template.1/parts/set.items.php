<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php $vItems = function ($arItems = [], $bOther = false) use (&$arVisual) { ?>
    <?php foreach ($arItems as $arItem) {

        $sPicture = $arItem['DETAIL_PICTURE'];

        if (empty($sPicture))
            $sPicture = $arItem['PREVIEW_PICTURE'];

        if (!empty($sPicture)) {
            $sPicture = CFile::ResizeImageGet($sPicture, [
                'width' => 120,
                'height' => 124
            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

            if (!empty($sPicture))
                $sPicture = $sPicture['src'];
        }

        if (empty($sPicture))
            $sPicture = SITE_TEMPLATE_PATH . '/images/picture.missing.png';

    ?>
        <?= Html::beginTag('div', [
            'class' => [
                'intec-grid-item' => [
                    '5',
                    '1024-4',
                    '768-3',
                    '600-2'
                ]
            ],
            'data' => [
                'role' => 'set.item',
                'id' => $arItem['ID'],
                'available' => $arItem['CAN_BUY'] ? 'true' : 'false',
                'selected' => $bOther || !$arItem['CAN_BUY'] ? 'false' : 'true',
                'quantity' => $arItem['BASKET_QUANTITY'],
                'price-current' => $arItem['PRICE_DISCOUNT_VALUE'],
                'price-old' => $arItem['PRICE_VALUE'],
                'price-difference' => $arItem['PRICE_DISCOUNT_DIFFERENCE_VALUE']
            ]
        ]) ?>
            <div class="constructor-set-item intec-cl-border-hover">
                <?= Html::beginTag('a', [
                    'class' => 'constructor-set-item-picture',
                    'href' => $arItem['DETAIL_PAGE_URL'],
                    'target' => '_blank'
                ]) ?>
                    <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                        'class' => 'intec-image-effect',
                        'alt' => $arItem['NAME'],
                        'title' => $arItem['NAME'],
                        'loading' => 'lazy',
                        'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                        'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                    ]) ?>
                <?= Html::endTag('a') ?>
                <div class="constructor-set-item-price">
                    <?php if ($arItem['PRICE_VALUE'] !== $arItem['PRICE_DISCOUNT_VALUE']) { ?>
                        <?= Html::tag('div', $arItem['PRICE_PRINT_VALUE'], [
                            'class' => [
                                'constructor-set-item-price-old',
                                'constructor-set-item-price-item'
                            ]
                        ]) ?>
                    <?php } ?>
                    <?= Html::tag('div', $arItem['PRICE_PRINT_DISCOUNT_VALUE'], [
                        'class' => [
                            'constructor-set-item-price-current',
                            'constructor-set-item-price-item'
                        ]
                    ]) ?>
                </div>
                <div class="constructor-set-item-name">
                    <?= Html::tag('a', $arItem['NAME'], [
                        'class' => 'intec-cl-text-hover-light',
                        'title' => $arItem['NAME'],
                        'href' => $arItem['DETAIL_PAGE_URL'],
                        'target' => '_blank'
                    ]) ?>
                </div>
                <div class="constructor-set-item-quantity">
                    <div class="constructor-set-item-quantity-content">
                        <?= Html::tag('div', Loc::getMessage('C_CONSTRUCTOR_SET_TEMPLATE_1_TEMPLATE_ITEM_QUANTITY'), [
                            'class' => [
                                'constructor-set-item-quantity-name',
                                'constructor-set-item-quantity-part'
                            ]
                        ]) ?>
                        <?= Html::tag('div', $arItem['BASKET_QUANTITY'], [
                            'class' => [
                                'constructor-set-item-quantity-value',
                                'constructor-set-item-quantity-part'
                            ]
                        ]) ?>
                    </div>
                </div>
                <div class="constructor-set-item-action">
                    <?php if ($arItem['CAN_BUY']) { ?>
                        <?= Html::tag('div', Loc::getMessage('C_CONSTRUCTOR_SET_TEMPLATE_1_TEMPLATE_ITEM_ACTION_ADD'), [
                            'class' => [
                                'constructor-set-item-button',
                                'constructor-set-item-button-add',
                                'intec-cl-background',
                                'intec-cl-background-light-hover'
                            ],
                            'data' => [
                                'set-action' => 'add',
                                'set-action-id' => $arItem['ID']
                            ]
                        ]) ?>
                        <?= Html::tag('div', Loc::getMessage('C_CONSTRUCTOR_SET_TEMPLATE_1_TEMPLATE_ITEM_ACTION_ADDED'), [
                            'class' => [
                                'constructor-set-item-button',
                                'constructor-set-item-button-added',
                                'intec-cl-background',
                                'intec-cl-background-light-hover'
                            ],
                            'data' => [
                                'set-action' => 'remove',
                                'set-action-id' => $arItem['ID']
                            ]
                        ]) ?>
                    <?php } else { ?>
                        <?= Html::tag('div', Loc::getMessage('C_CONSTRUCTOR_SET_TEMPLATE_1_TEMPLATE_ITEM_ACTION_UNAVAILABLE'), [
                            'class' => [
                                'constructor-set-item-button',
                                'constructor-set-item-button-unavailable'
                            ]
                        ]) ?>
                    <?php } ?>
                </div>
            </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
<?php } ?>
<div class="constructor-set-items">
    <div class="constructor-set-items-wrapper intec-grid intec-grid-wrap intec-grid-a-v-stretch intec-grid-i-8">
        <?php $vItems($arResult['SET_ITEMS']['DEFAULT']) ?>
        <?php $vItems($arResult['SET_ITEMS']['OTHER'], true) ?>
    </div>
</div>
<?php unset($vItems) ?>