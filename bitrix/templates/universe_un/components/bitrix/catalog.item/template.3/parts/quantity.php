<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var bool $bOffers
 */

?>
<?php $vQuantity = function ($arItem, $bOffer = false) use (&$arVisual) { ?>
    <?php if (!empty($arItem['OFFERS']) && !$bOffer) return ?>
    <?= Html::beginTag('div', [
        'class' => 'catalog-item-quantity',
        'data-offer' => $bOffer ? $arItem['ID'] : 'false'
    ]) ?>
        <?php if ($arItem['CAN_BUY']) { ?>
            <?php if ($arVisual['QUANTITY']['MODE'] === 'number') {

                if ($arItem['CATALOG_QUANTITY'] > 0) {
                    $iOffset = StringHelper::position('.', $arItem['CATALOG_QUANTITY']);

                    $iPrecision = 0;

                    if ($iOffset)
                        $iPrecision = StringHelper::length(
                            StringHelper::cut($arItem['CATALOG_QUANTITY'], $iOffset + 1)
                        );

                    $sQuantity = number_format(
                        $arItem['CATALOG_QUANTITY'],
                        $iPrecision,
                        '.',
                        ' '
                    );

                    unset($iOffset, $iPrecision);
                }

            ?>
                <div class="catalog-item-quantity-indicator-container catalog-item-quantity-part">
                    <?= Html::tag('div', null, [
                        'class' => 'catalog-item-quantity-indicator',
                        'data-quantity-state' => 'many'
                    ]) ?>
                </div>
                <div class="catalog-item-quantity-value-container catalog-item-quantity-part">
                    <?= Html::tag('span', Loc::getMessage('C_CATALOG_ITEM_TEMPLATE_3_TEMPLATE_QUANTITY_AVAILABLE'), [
                        'class' => 'catalog-item-quantity-value-text',
                        'data' => [
                            'quantity-state' => 'many',
                            'store-use' => $arVisual['STORES']['USE'] && $arVisual['STORES']['POSITION'] === 'popup' ? 'true' : 'false'
                        ]
                    ]) ?>
                    <?php if ($arItem['CATALOG_QUANTITY'] > 0) { ?>
                        <?= Html::tag('span', $sQuantity, [
                            'class' => 'catalog-item-quantity-value-number'
                        ]) ?>
                        <?php if (!empty($arItem['CATALOG_MEASURE_NAME'])) { ?>
                            <?= Html::tag('span', $arItem['CATALOG_MEASURE_NAME'], [
                                'class' => 'catalog-item-quantity-value-measure'
                            ]) ?>
                        <?php } ?>
                    <?php } ?>
                </div>
                <?php unset($sQuantity) ?>
            <?php } else if ($arVisual['QUANTITY']['MODE'] === 'text') {

                $sState = 'empty';

                if ($arItem['CATALOG_QUANTITY'] >= $arVisual['QUANTITY']['BOUNDS']['MANY'])
                    $sState = 'many';
                else if ($arItem['CATALOG_QUANTITY'] < $arVisual['QUANTITY']['BOUNDS']['MANY'] && $arItem['CATALOG_QUANTITY'] > $arVisual['QUANTITY']['BOUNDS']['FEW'])
                    $sState = 'enough';
                else if ($arItem['CATALOG_QUANTITY'] <= $arVisual['QUANTITY']['BOUNDS']['FEW'] && $arItem['CATALOG_QUANTITY'] > 0)
                    $sState = 'few';
                else if ($arItem['CATALOG_QUANTITY_TRACE'] === 'N' || $arItem['CATALOG_CAN_BUY_ZERO'] === 'Y')
                    $sState = 'many';

            ?>
                <div class="catalog-item-quantity-indicator-container catalog-item-quantity-part">
                    <?= Html::tag('div', null, [
                        'class' => 'catalog-item-quantity-indicator',
                        'data-quantity-state' => $sState,
                    ]) ?>
                </div>
                <div class="catalog-item-quantity-value-container catalog-item-quantity-part">
                    <?= Html::beginTag('span', [
                        'class' => 'catalog-item-quantity-value-text',
                        'data' => [
                            'quantity-state' => $sState,
                            'store-use' => $arVisual['STORES']['USE'] && $arVisual['STORES']['POSITION'] === 'popup' ? 'true' : 'false'
                        ]
                    ]) ?>
                        <?php if ($sState === 'many') { ?>
                            <?= Loc::getMessage('C_CATALOG_ITEM_TEMPLATE_3_TEMPLATE_QUANTITY_BOUNDS_MANY') ?>
                        <?php } else if ($sState === 'enough') { ?>
                            <?= Loc::getMessage('C_CATALOG_ITEM_TEMPLATE_3_TEMPLATE_QUANTITY_BOUNDS_ENOUGH') ?>
                        <?php } else if ($sState === 'few') { ?>
                            <?= Loc::getMessage('C_CATALOG_ITEM_TEMPLATE_3_TEMPLATE_QUANTITY_BOUNDS_FEW') ?>
                        <?php } ?>
                    <?= Html::endTag('span') ?>
                </div>
            <?php } else { ?>
                <div class="catalog-item-quantity-indicator-container catalog-item-quantity-part">
                    <?= Html::tag('div', null, [
                        'class' => 'catalog-item-quantity-indicator',
                        'data-state' => 'many'
                    ]) ?>
                </div>
                <div class="catalog-item-quantity-value-container catalog-item-quantity-part">
                    <?= Html::tag('span', Loc::getMessage('C_CATALOG_ITEM_TEMPLATE_3_TEMPLATE_QUANTITY_AVAILABLE'), [
                        'class' => 'catalog-item-quantity-value-text',
                        'data' => [
                            'quantity-state' => 'many',
                            'store-use' => $arVisual['STORES']['USE'] && $arVisual['STORES']['POSITION'] === 'popup' ? 'true' : 'false'
                        ]
                    ]) ?>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="catalog-item-quantity-indicator-container catalog-item-quantity-part">
                <?= Html::tag('div', null, [
                    'class' => 'catalog-item-quantity-indicator',
                    'data-quantity-state' => 'empty'
                ]) ?>
            </div>
            <div class="catalog-item-quantity-value-container catalog-item-quantity-part">
                <?= Html::tag('span', Loc::getMessage('C_CATALOG_ITEM_TEMPLATE_3_TEMPLATE_QUANTITY_UNAVAILABLE'), [
                    'class' => 'catalog-item-quantity-value-text',
                    'data' => [
                        'quantity-state' => 'empty',
                        'store-use' => $arVisual['STORES']['USE'] && $arVisual['STORES']['POSITION'] === 'popup' ? 'true' : 'false'
                    ]
                ]) ?>
            </div>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?php } ?>
<?php $vQuantity($arResult['ITEM']);

if ($bOffers) {
    foreach ($arResult['OFFERS'] as &$arOffer)
        $vQuantity($arOffer, true);

    unset($arOffer);
}

unset($vQuantity); ?>
