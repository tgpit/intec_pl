<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

$bLite = false;

if (
    !(Loader::includeModule('catalog') && Loader::includeModule('sale')) &&
    Loader::includeModule('intec.startshop')
)
    $bLite = true;

?>
<?php if ($arResult['CAN_BUY']) { ?>
    <?php if ($arVisual['QUANTITY']['MODE'] === 'number') { ?>
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-element-quantity',
                'intec-grid' => [
                    '',
                    'a-v-center'
                ]
            ],
            'data-state' => 'many'
        ]) ?>
            <div class="catalog-element-quantity-indicator intec-grid-item-auto"></div>
            <div class="intec-grid-item-auto">
                <span class="catalog-element-quantity-text">
                    <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_QUANTITY_AVAILABLE') ?>
                </span>
                <?php if (!($bLite && $arResult['CATALOG_QUANTITY_TRACE'] === 'N')) { ?>
                    <span class="catalog-element-quantity-value">
                        <?= $arResult['CATALOG_QUANTITY'] ?>
                    </span>
                    <span class="catalog-element-quantity-value">
                        <?= $arResult['CATALOG_MEASURE_NAME'] ?>
                    </span>
                <?php } ?>
            </div>
        <?= Html::endTag('div') ?>
    <?php } else if ($arVisual['QUANTITY']['MODE'] === 'logic') { ?>
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-element-quantity',
                'intec-grid' => [
                    '',
                    'a-v-center'
                ]
            ],
            'data-state' => 'many'
        ]) ?>
            <div class="catalog-element-quantity-indicator intec-grid-item-auto"></div>
            <div class="intec-grid-item-auto">
                <span class="catalog-element-quantity-text">
                    <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_QUANTITY_AVAILABLE') ?>
                </span>
            </div>
        <?= Html::endTag('div') ?>
    <?php } else if ($arVisual['QUANTITY']['MODE'] === 'text') {

        $sState = 'enough';
        $sText = Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_QUANTITY_BOUNDS_ENOUGH');

        if ($arResult['CATALOG_QUANTITY'] >= $arVisual['QUANTITY']['BOUNDS']['MANY']) {
            $sState = 'many';
            $sText = Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_QUANTITY_BOUNDS_MANY');
        } else if ($arResult['CATALOG_QUANTITY'] <= $arVisual['QUANTITY']['BOUNDS']['FEW']) {
            $sState = 'few';
            $sText = Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_QUANTITY_BOUNDS_FEW');
        }

    ?>
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-element-quantity',
                'intec-grid' => [
                    '',
                    'a-v-center'
                ]
            ],
            'data-state' => $sState
        ]) ?>
            <div class="catalog-element-quantity-indicator intec-grid-item-auto"></div>
            <div class="intec-grid-item-auto">
                <span class="catalog-element-quantity-text">
                    <?= $sText ?>
                </span>
            </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
<?php } else { ?>
    <?= Html::beginTag('div', [
        'class' => [
            'catalog-element-quantity',
            'intec-grid' => [
                '',
                'a-v-center'
            ]
        ],
        'data-state' => 'empty'
    ]) ?>
        <div class="catalog-element-quantity-indicator intec-grid-item-auto"></div>
        <div class="intec-grid-item-auto">
            <span class="catalog-element-quantity-text">
                <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_QUANTITY_EMPTY') ?>
            </span>
        </div>
    <?= Html::endTag('div') ?>
<?php } ?>