<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

?>
<?= Html::beginTag('div', [
    'class' => [
        'catalog-element-button-buy',
        'intec-ui' => [
            '',
            'control-button',
            'control-basket-button',
            'scheme-current'
        ]
    ],
    'data-role' => 'product.order'
]) ?>
    <div class="catalog-element-button-buy-content intec-ui-part-content">
        <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_ORDER') ?>
    </div>
<?= Html::endTag('div') ?>