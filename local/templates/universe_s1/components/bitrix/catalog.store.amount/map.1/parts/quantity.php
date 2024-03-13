<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

?>
<?php return function(&$arStore) { ?>
    <?= Html::beginTag('div', [
        'class' => 'store-amount-quantity',
        'data' => [
            'role' => 'store.state',
            'store-state' => $arStore['AMOUNT_STATUS']
        ]
    ]) ?>
        <div class="store-amount-quantity-content">
            <?= Html::tag('div', null, [
                'class' => [
                    'store-amount-quantity-indicator',
                    'store-amount-quantity-part'
                ]
            ]) ?>
            <div class="store-amount-quantity-part">
                <?php if ($arVisual['MIN_AMOUNT']['USE']) { ?>
                    <?= Html::tag('span', $arStore['AMOUNT_PRINT'], [
                        'class' => [
                            'store-amount-quantity-value',
                            'store-amount-quantity-color'
                        ],
                        'data-role' => 'store.quantity'
                    ]) ?>
                <?php } else { ?>
                    <?= Html::tag('span', Loc::getMessage('C_CATALOG_STORE_AMOUNT_TEMPLATE_MAP_1_TEMPLATE_IN_STOCK'), [
                        'class' => [
                            'store-amount-quantity-value',
                            'store-amount-quantity-color'
                        ]
                    ]) ?>
                    <?= Html::tag('span', $arStore['AMOUNT_PRINT'], [
                        'class' => 'store-amount-quantity-value',
                        'data-role' => 'store.quantity'
                    ]) ?>
                    <?php if (empty($arParams['OFFER_ID'])) { ?>
                        <?= Html::tag('span', ArrayHelper::getFirstValue($arResult['MEASURES']), [
                            'class' => 'store-amount-quantity-value',
                            'data-role' => 'store.measure'
                        ]) ?>
                    <?php } else { ?>
                        <?= Html::tag('span', ArrayHelper::getValue($arResult, ['MEASURES', $arParams['OFFER_ID']]), [
                            'class' => 'store-amount-quantity-value',
                            'data-role' => 'store.measure'
                        ]) ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    <?= Html::endTag('div') ?>
<?php } ?>