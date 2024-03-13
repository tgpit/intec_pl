<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section',
        'c-catalog-section-products-additional-1'
    ],
    'data' => [
        'role' => 'products.additional',
        'trigger' => $arResult['TRIGGER']
    ]
]) ?>
    <div class="catalog-section-items" data-role="items">
        <?php foreach ($arResult['ITEMS'] as $arItem) {
            if (!$arItem['CAN_BUY'])
                continue;

            $arPrice = null;

            if (!empty($arItem['ITEM_PRICES']))
                $arPrice = $arItem['ITEM_PRICES'][0];
            ?>
            <?= Html::beginTag('div', [
                'class' => 'catalog-section-item',
                'data' => [
                    'role' => 'item',
                    'basket-id' => $arItem['ID'],
                    'basket-state' => 'none',
                    'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                ]
            ]) ?>
                <?= Html::beginTag('label', [
                    'class' => [
                        'intec-ui' => [
                            '',
                            'control-switch',
                            'scheme-current',
                            'size-2'
                        ]
                    ]
                ]) ?>
                    <?= Html::input('checkbox', '', $arPrice['PRICE'], [
                        'id' => $arPrice['ID'],
                        'data' => [
                            'role' => 'item.input'
                        ]
                    ]) ?>
                    <?= Html::tag('span', '', [
                        'class' => [
                            'intec-ui-part-selector'
                        ]
                    ]) ?>
                    <span class="intec-ui-part-content">
                        <?= $arItem['NAME'] ?>
                        <?php if (!empty($arPrice)) { ?>
                            + <?= $arPrice['PRINT_PRICE'] ?>
                        <?php } ?>
                    </span>
                <?= Html::endTag('label') ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
        <?php if ($arResult['VISUAL']['RECALCULATION']) { ?>
            <div class="catalog-section-total-price" data-role="products.additional.total" data-active="false">
                <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_ADDITIONAL_1_TOTAL_PRICE_TITLE') ?>
                <?= Html::tag('span', '', [
                    'class' => 'catalog-section-total-price-value',
                    'data' => [
                        'role' => 'products.additional.total.value',
                        'value' => 0,
                        'currency' => $arParams['CURRENCY_ID']
                    ]
                ]) ?>
            </div>
        <?php } ?>
    </div>
    <?php include(__DIR__ . '/parts/script.php'); ?>
<?= Html::endTag('div') ?>