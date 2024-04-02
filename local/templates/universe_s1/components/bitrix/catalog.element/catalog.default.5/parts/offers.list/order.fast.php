<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

?>
<?php return function () { ?>
    <?= Html::tag('div', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_BUY_BUTTON_ORDER_FAST'), [
        'class' => [
            'catalog-element-offers-list-item-buy-button',
            'catalog-element-offers-list-item-buy-fast',
            'intec-cl-text',
            'intec-cl-border',
            'intec-cl-background-hover'
        ],
        'data-role' => 'orderFast'
    ]) ?>
<?php } ?>
