<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

?>
<?= Html::beginTag('div', [
    'class' => [
        'catalog-element-button-order-fast',
        'intec-ui' => [
            '',
            'control-button',
            'scheme-current',
            'mod-transparent'
        ]
    ],
    'data-role' => 'product.orderFast'
]) ?>
    <div class="catalog-element-button-order-fast-content intec-ui-part-content">
        <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_ORDER_FAST') ?>
    </div>
<?= Html::endTag('div') ?>