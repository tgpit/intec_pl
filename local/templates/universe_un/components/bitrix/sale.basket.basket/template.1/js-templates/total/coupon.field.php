<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

?>

<div class="basket-coupon-field">
    <div class="basket-coupon-field-input intec-grid intec-grid-a-h-start intec-grid-a-v-center intec-grid-nowrap">
        <label class="basket-coupon-field-label" for="control-input-coupon">
            <?=Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_COUPON_FIELD_NAME');?>
        </label>
        <?= Html::input('text', null, null, [
            'class' => [
                'intec-ui' => [
                    '',
                    'control-input',
                    'view-1',
                    'size-2',
                    'mod-block'
                ],
                'intec-grid-auto'
            ],
            'data-entity' => 'basket-coupon-input',
            //'placeholder' => Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_COUPON_FIELD_NAME'),
            'id' => 'control-input-coupon'
        ]) ?>
        <button class="intec-grid-auto intec-ui intec-ui-control-button intec-ui-mod-link intec-ui-scheme-current"><i class="fal fa-angle-right"></i></button>
    </div>
</div>