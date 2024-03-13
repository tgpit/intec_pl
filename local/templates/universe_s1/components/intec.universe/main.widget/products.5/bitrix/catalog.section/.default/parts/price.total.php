<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<?php return function (&$arItem) use (&$arVisual) {

    $arPrice = null;

    if (!empty($arItem['ITEM_PRICES']))
        $arPrice = ArrayHelper::getFirstValue($arItem['ITEM_PRICES']);

?>
    <?= Html::beginTag('div', [
        'class' => [
            'widget-item-price-total',
            'intec-grid-item-auto'
        ],
        'data-show' => !empty($arPrice)
    ]) ?>
        <div class="widget-item-price-total-caption">
            <?= Loc::getMessage('C_WIDGET_PRODUCTS_5_PRICE_TOTAL') ?>
        </div>
        <div class="widget-item-price-total-value" data-role="item.price.total">
            <?= !empty($arPrice) ? $arPrice['PRINT_PRICE'] : null ?>
        </div>
    <?= Html::endTag('div') ?>
<?php } ?>