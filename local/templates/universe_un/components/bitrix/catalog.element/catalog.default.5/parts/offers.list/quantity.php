<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

/**
 * @var array $arVisual
 */

?>
<?php return function (&$arOffer) use (&$arVisual) { ?>
    <div class="catalog-element-offers-list-item-quantity">
        <?php if ($arOffer['CAN_BUY']) { ?>
            <?php if ($arVisual['QUANTITY']['MODE'] === 'number') {

                if ($arOffer['CATALOG_QUANTITY'] > 0) {
                    $iOffset = StringHelper::position('.', $arOffer['CATALOG_QUANTITY']);

                    $iPrecision = 0;

                    if ($iOffset)
                        $iPrecision = StringHelper::length(
                            StringHelper::cut($arOffer['CATALOG_QUANTITY'], $iOffset + 1)
                        );

                    $sQuantity = number_format(
                        $arOffer['CATALOG_QUANTITY'],
                        $iPrecision,
                        '.',
                        ' '
                    );

                    unset($iOffset, $iPrecision);
                }

            ?>
                <div class="catalog-element-offers-list-item-quantity-part">
                    <?= Html::tag('div', null, [
                        'class' => 'catalog-element-offers-list-item-quantity-indicator',
                        'data-quantity-state' => 'many'
                    ]) ?>
                </div>
                <div class="catalog-element-offers-list-item-quantity-part">
                    <?= Html::tag('span', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_QUANTITY_AVAILABLE'), [
                        'class' => 'catalog-element-offers-list-item-quantity-text',
                        'data-quantity-state' => 'many'
                    ]) ?>
                    <?php if ($arOffer['CATALOG_QUANTITY'] > 0) { ?>
                        <?= Html::tag('span', $sQuantity, [
                            'class' => 'catalog-element-offers-list-item-quantity-number'
                        ]) ?>
                    <?php } ?>
                </div>
                <?php unset($sQuantity) ?>
            <?php } else if ($arVisual['QUANTITY']['MODE'] === 'text') {

                $sState = 'empty';

                if ($arOffer['CATALOG_QUANTITY'] >= $arVisual['QUANTITY']['BOUNDS']['MANY'])
                    $sState = 'many';
                else if ($arOffer['CATALOG_QUANTITY'] < $arVisual['QUANTITY']['BOUNDS']['MANY'] && $arOffer['CATALOG_QUANTITY'] > $arVisual['QUANTITY']['BOUNDS']['FEW'])
                    $sState = 'enough';
                else if ($arOffer['CATALOG_QUANTITY'] <= $arVisual['QUANTITY']['BOUNDS']['FEW'] && $arOffer['CATALOG_QUANTITY'] > 0)
                    $sState = 'few';
                else if ($arOffer['CATALOG_QUANTITY_TRACE'] === 'N' || $arOffer['CATALOG_CAN_BUY_ZERO'] === 'Y')
                    $sState = 'many';

            ?>
                <div class="catalog-element-offers-list-item-quantity-part">
                    <?= Html::tag('div', null, [
                        'class' => 'catalog-element-offers-list-item-quantity-indicator',
                        'data-quantity-state' => $sState,
                    ]) ?>
                </div>
                <div class="catalog-element-offers-list-item-quantity-part">
                    <?= Html::beginTag('span', [
                        'class' => 'catalog-element-offers-list-item-quantity-text',
                        'data-quantity-state' => $sState
                    ]) ?>
                        <?php if ($sState === 'many') { ?>
                            <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_QUANTITY_BOUNDS_MANY') ?>
                        <?php } else if ($sState === 'enough') { ?>
                            <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_QUANTITY_BOUNDS_ENOUGH') ?>
                        <?php } else if ($sState === 'few') { ?>
                            <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_QUANTITY_BOUNDS_FEW') ?>
                        <?php } ?>
                    <?= Html::endTag('span') ?>
                </div>
            <?php } else { ?>
                <div class="catalog-element-offers-list-item-quantity-part">
                    <?= Html::tag('div', null, [
                        'class' => 'catalog-element-offers-list-item-quantity-indicator',
                        'data-quantity-state' => 'many',
                    ]) ?>
                </div>
                <div class="catalog-element-offers-list-item-quantity-part">
                    <?= Html::tag('span', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_QUANTITY_AVAILABLE'), [
                        'class' => 'catalog-element-offers-list-item-quantity-text',
                        'data-quantity-state' => 'many'
                    ]) ?>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="catalog-element-offers-list-item-quantity-part">
                <?= Html::tag('div', null, [
                    'class' => 'catalog-element-offers-list-item-quantity-indicator',
                    'data-quantity-state' => 'empty'
                ]) ?>
            </div>
            <div class="catalog-element-offers-list-item-quantity-part">
                <?= Html::tag('span', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_QUANTITY_UNAVAILABLE'), [
                    'class' => 'catalog-element-offers-list-item-quantity-text',
                    'data-quantity-state' => 'empty'
                ]) ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>