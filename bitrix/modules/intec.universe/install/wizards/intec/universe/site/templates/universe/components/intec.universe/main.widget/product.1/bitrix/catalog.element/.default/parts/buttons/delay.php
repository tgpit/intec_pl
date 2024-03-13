<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$arPrice = ArrayHelper::getValue($arResult, ['ITEM_PRICES', 0]);
$sSvg = FileHelper::getFileData(__DIR__.'/../../svg/button.action.delay.svg');

?>
<?= Html::beginTag('div', [
    'class' => [
        'catalog-element-button-action',
        'catalog-element-button-action-add',
        'catalog-element-button-action-delay',
        'intec-cl-border-light-hover',
        'intec-cl-background-light-hover',
        'intec-ui' => [
            '',
            'control-button',
            'control-basket-button',
            'mod-round-2'
        ]
    ],
    'data' => [
        'basket-id' => $arResult['ID'],
        'basket-action' => 'delay',
        'basket-state' => !defined('EDITOR') ? 'processing' : 'none',
        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
    ]
]) ?>
    <div class="catalog-element-button-action-icon intec-ui-part-icon intec-ui-picture">
        <?= $sSvg ?>
    </div>
    <div class="catalog-element-button-action-content intec-ui-part-content">
        <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_ACTION_DELAY') ?>
    </div>
<?= Html::endTag('div') ?>
<?= Html::beginTag('div', [
    'class' => [
        'catalog-element-button-action',
        'catalog-element-button-action-added',
        'catalog-element-button-action-delay',
        'intec-cl-border',
        'intec-cl-background',
        'intec-cl-border-light-hover',
        'intec-cl-background-light-hover',
        'intec-ui' => [
            '',
            'control-button',
            'control-basket-button',
            'mod-round-2'
        ]
    ],
    'data' => [
        'basket-id' => $arResult['ID'],
        'basket-action' => 'remove',
        'basket-state' => !defined('EDITOR') ? 'processing' : 'none',
        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
    ]
]) ?>
    <div class="catalog-element-button-action-icon intec-ui-part-icon intec-ui-picture">
        <?= $sSvg ?>
    </div>
    <div class="catalog-element-button-action-content intec-ui-part-content">
        <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_ACTION_DELAYED') ?>
    </div>
    <div class="intec-ui-part-effect intec-ui-part-effect-bounce">
        <div class="intec-ui-part-effect-wrapper">
            <i></i>
            <i></i>
            <i></i>
        </div>
    </div>
<?= Html::endTag('div') ?>
<?php unset($arPrice, $sSvg) ?>