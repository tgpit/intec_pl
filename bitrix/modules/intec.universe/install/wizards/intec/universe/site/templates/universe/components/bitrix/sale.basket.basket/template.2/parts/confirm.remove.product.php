<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arParams
 */

?>

<?= Html::beginTag('div', [
    'class' => [
        'basket-confirm-remove-product'
    ],
    'data' => [
        'role' => 'confirm.remove.product',
        'state' => 'hidden'
    ]
]) ?>
    <div class="basket-confirm-remove-product-wrapper">
        <div class="basket-confirm-remove-product-close" data-entity="confirm.remove.product.cancel">
            <?= $arSvg['CONFIRM_REMOVE_PRODUCT_CLOSE'] ?>
        </div>
        <div class="basket-confirm-remove-product-title" data-role="confirm.remove.product.title">
            <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_CONFIRM_REMOVE_PRODUCT_TITLE') ?>
        </div>
        <div class="basket-confirm-remove-product-text" data-role="confirm.remove.product.text">
            <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_CONFIRM_REMOVE_PRODUCT_TEXT') ?>
        </div>
        <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
            <div class="intec-grid-item-auto">
                <?= Html::tag('div', Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_CONFIRM_REMOVE_PRODUCT_BUTTON_YES'), [
                    'class' => [
                        'basket-confirm-remove-product-button',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'scheme-current',
                            'mod-round-4'
                        ]
                    ],
                    'data' => [
                        'entity' => 'confirm.remove.product.agree'
                    ]
                ]) ?>
            </div>
            <div class="intec-grid-item-auto">
                <?= Html::tag('div', Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_CONFIRM_REMOVE_PRODUCT_BUTTON_NO'), [
                    'class' => [
                        'basket-confirm-remove-product-button',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'scheme-current',
                            'mod-round-4',
                            'mod-transparent'
                        ]
                    ],
                    'data' => [
                        'entity' => 'confirm.remove.product.cancel'
                    ]
                ]) ?>
            </div>
            <div class="intec-grid-item-auto">
                <?= Html::beginTag('div', [
                    'class' => [
                        'basket-confirm-remove-product-delay',
                        'intec-grid' => [
                            '',
                            'nowrap',
                            'a-v-center',
                            'a-h-start',
                            'i-4'
                        ],
                        'intec-cl' => [
                            'svg-path-stroke-hover',
                            'text-hover'
                        ]
                    ],
                    'data' => [
                        'entity' => 'confirm.remove.product.delay',
                        'state' => 'visible'
                    ]
                ]) ?>
                    <div class="intec-grid-item-auto intec-ui-picture">
                        <?= $arSvg['CONFIRM_REMOVE_PRODUCT_DELAY'] ?>
                    </div>
                    <div class="intec-grid-item-auto">
                        <div data-role="confirm.remove.product.delay.message">
                            <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_CONFIRM_REMOVE_PRODUCT_BUTTON_DELAY') ?>
                        </div>
                    </div>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>
