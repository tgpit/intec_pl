<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

?>
<div class="catalog-element-purchase-block catalog-element-purchase-action">
    <div class="catalog-element-buy-container">
        <?= Html::tag('div', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_BUY_BUTTON_DETAILED'), [
            'class' => [
                'catalog-element-buy-button',
                'intec-cl-background' => [
                    '',
                    'light-hover'
                ]
            ],
            'data' => [
                'role' => 'anchor',
                'scroll-to' => 'offers'
            ]
        ]) ?>
    </div>
</div>