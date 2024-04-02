<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

?>

<div class="basket-coupon-field">
    <div class="basket-coupon-field-name">
        <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_COUPON_FIELD_NAME') ?>
    </div>
    <div class="basket-coupon-field-description">
        <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_COUPON_FIELD_DESCRIPTION') ?>
    </div>
    <div class="basket-coupon-field-input intec-grid intec-grid-nowrap intec-grid-a-v-center intec-grid-a-h-start">
        <?= Html::input('text', null, null, [
            'class' => [
                'intec-ui' => [
                    '',
                    'control-input',
                    'view-1',
                    'size-1',
                    'mod-block'
                ]
            ],
            'placeholder' => Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_COUPON_FIELD_PLACEHOLDER'),
            'data-entity' => 'basket-coupon-input'
        ]) ?>
        <button class="intec-grid-auto intec-ui intec-ui-control-button intec-ui-mod-link intec-ui-scheme-current"><i class="fal fa-angle-right"></i></button>
    </div>
</div>