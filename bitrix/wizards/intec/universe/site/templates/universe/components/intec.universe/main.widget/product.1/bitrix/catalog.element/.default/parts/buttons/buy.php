<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$arPrice = ArrayHelper::getValue($arResult, ['ITEM_PRICES', 0]);

?>
<?= Html::beginTag('div', [
    'class' => [
        'catalog-element-button-buy',
        'catalog-element-button-buy-add',
        'intec-ui' => [
            '',
            'control-button',
            'control-basket-button',
            'scheme-current'
        ]
    ],
    'data' => [
        'basket-id' => $arResult['ID'],
        'basket-action' => 'add',
        'basket-state' => !defined('EDITOR') ? 'processing' : 'none',
        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null,
        'basket-quantity' => $arResult['CATALOG_MEASURE_RATIO'],
    ]
]) ?>
    <div class="catalog-element-button-buy-content intec-ui-part-content">
        <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_BUY_ADD') ?>
    </div>
<?= Html::endTag('div') ?>
<?= Html::beginTag(!empty($arResult['ACTIONS']['BUY']['BASKET']) ? 'a' : 'div', [
    'class' => [
        'catalog-element-button-buy',
        'catalog-element-button-buy-added',
        'intec-ui' => [
            '',
            'control-button',
            'control-basket-button',
            'scheme-current'
        ]
    ],
    'href' => !empty($arResult['ACTIONS']['BUY']['BASKET']) ? $arResult['ACTIONS']['BUY']['BASKET'] : null,
    'data' => [
        'basket-id' => $arResult['ID'],
        'basket-state' => !defined('EDITOR') ? 'processing' : 'none'
    ]
]) ?>
    <div class="catalog-element-button-buy-content intec-ui-part-content">
        <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_BUY_ADDED') ?>
    </div>
    <div class="intec-ui-part-effect intec-ui-part-effect-bounce">
        <div class="intec-ui-part-effect-wrapper">
            <i></i>
            <i></i>
            <i></i>
        </div>
    </div>
<?= Html::endTag(!empty($arResult['ACTIONS']['BUY']['BASKET']) ? 'a' : 'div') ?>
<?php unset($arPrice) ?>