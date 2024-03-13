<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

?>
<div class="intec-grid-item-auto intec-grid-item-600-1" data-print="false">
    <div class="basket-order">
        <div class="basket-order-wrapper intec-grid intec-grid-wrap">
            <div class="intec-grid-item-1 intec-grid-item-375-1 intec-grid-item-600-2 intec-grid-item-1200-auto">
                {{#DISABLE_CHECKOUT}}
                    <?= Html::tag('button', Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_TOTAL_ORDER'), [
                        'class' => [
                            'basket-order-button',
                            'intec-ui' => [
                                '',
                                'control-button',
                                'mod-round-2',
                                'scheme-current',
                                'mod-block',
                                'size-2'
                            ]
                        ],
                        'disabled' => 'disabled',
                        'data-entity' => 'basket-checkout-button'
                    ]) ?>
                {{/DISABLE_CHECKOUT}}
                {{^DISABLE_CHECKOUT}}
                    <?= Html::tag('button', Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_TOTAL_ORDER'), [
                        'class' => [
                            'basket-order-button',
                            'intec-ui' => [
                                '',
                                'control-button',
                                'mod-round-2',
                                'scheme-current',
                                'mod-block',
                                'size-2'
                            ]
                        ],
                        'data-entity' => 'basket-checkout-button'
                    ]) ?>
                {{/DISABLE_CHECKOUT}}
            </div>
            <div class="intec-grid-item-1 intec-grid-item-375-1 intec-grid-item-600-2 intec-grid-item-1200-auto">
                <?php include(__DIR__.'/total.order.fast.php') ?>
            </div>
        </div>
    </div>
</div>