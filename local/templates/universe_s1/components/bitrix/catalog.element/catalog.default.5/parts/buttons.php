<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 * @var bool $bOffers
 */

?>
<?php $vButtons = function (&$arItem = [], $bOffer = false) use (&$arResult, &$arVisual, &$arSvg) { ?>
    <?php if (empty($arItem) || !empty($arItem['OFFERS'])) return ?>
    <?= Html::beginTag('div', [
        'class' => 'catalog-element-button-action-block',
        'data-offer' => $bOffer ? $arItem['ID'] : 'false'
    ]) ?>
        <?php if ($arResult['COMPARE']['USE'] && ($bOffer || empty($arItem['OFFERS']))) { ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-button-action',
                    'catalog-element-button-action-add',
                    'intec-ui',
                    'intec-ui-control-basket-button',
                    'intec-cl-background-hover',
                    'intec-cl-border-hover'
                ],
                'data' => [
                    'compare-id' => $arItem['ID'],
                    'compare-action' => 'add',
                    'compare-code' => $arResult['COMPARE']['CODE'],
                    'compare-state' => 'none',
                    'compare-iblock' => $arResult['IBLOCK_ID']
                ]
            ]) ?>
                <span class="catalog-element-button-action-content intec-ui-part-content">
                    <span class="catalog-element-button-action-icon">
                        <?= $arSvg['BUTTONS']['COMPARE'] ?>
                    </span>
                    <span class="catalog-element-button-action-text">
                        <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_COMPARE') ?>
                    </span>
                </span>
            <?= Html::endTag('div') ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-button-action',
                    'catalog-element-button-action-added',
                    'intec-ui',
                    'intec-ui-control-basket-button',
                    'intec-cl-background',
                    'intec-cl-background-light-hover',
                    'intec-cl-border',
                    'intec-cl-border-light-hover'
                ],
                'data' => [
                    'compare-id' => $arItem['ID'],
                    'compare-action' => 'remove',
                    'compare-code' => $arResult['COMPARE']['CODE'],
                    'compare-state' => 'none',
                    'compare-iblock' => $arResult['IBLOCK_ID']
                ]
            ]) ?>
                <span class="catalog-element-button-action-content intec-ui-part-content">
                    <span class="catalog-element-button-action-icon">
                        <?= $arSvg['BUTTONS']['COMPARE'] ?>
                    </span>
                    <span class="catalog-element-button-action-text">
                        <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_COMPARED') ?>
                    </span>
                </span>
                <?= Html::beginTag('span', [
                    'class' => Html::cssClassFromArray([
                        'intec-ui-part-effect' => [
                            '' => true,
                            'bounce' => $arVisual['MAIN_VIEW'] != 3,
                            'folding' => $arVisual['MAIN_VIEW'] == 3
                        ]
                    ], true)
                ]) ?>
                    <span class="intec-ui-part-effect-wrapper">
                        <i></i><i></i><i></i>
                    </span>
                <?= Html::endTag('span') ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
        <?php if ($arResult['DELAY']['USE'] && $arItem['CAN_BUY'] && ($bOffer || empty($arItem['OFFERS']))) { ?>
            <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-button-action',
                    'catalog-element-button-action-add',
                    'intec-ui',
                    'intec-ui-control-basket-button',
                    'intec-cl-background-hover',
                    'intec-cl-border-hover'
                ],
                'data' => [
                    'basket-id' => $arItem['ID'],
                    'basket-action' => 'delay',
                    'basket-state' => 'none',
                    'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                ]
            ]) ?>
                <span class="catalog-element-button-action-content intec-ui-part-content">
                    <span class="catalog-element-button-action-icon">
                        <?= $arSvg['BUTTONS']['DELAY'] ?>
                    </span>
                    <span class="catalog-element-button-action-text">
                        <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_DELAY') ?>
                    </span>
                </span>
            <?= Html::endTag('div') ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-button-action',
                    'catalog-element-button-action-added',
                    'intec-ui',
                    'intec-ui-control-basket-button',
                    'intec-cl-background',
                    'intec-cl-background-light-hover',
                    'intec-cl-border',
                    'intec-cl-border-light-hover'
                ],
                'data' => [
                    'basket-id' => $arItem['ID'],
                    'basket-action' => 'remove',
                    'basket-state' => 'none'
                ]
            ]) ?>
                <span class="catalog-element-button-action-content intec-ui-part-content">
                    <span class="catalog-element-button-action-icon">
                        <?= $arSvg['BUTTONS']['DELAY'] ?>
                    </span>
                    <span class="catalog-element-button-action-text">
                        <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_DELAYED') ?>
                    </span>
                </span>
                <?= Html::beginTag('span', [
                    'class' => Html::cssClassFromArray([
                        'intec-ui-part-effect' => [
                            '' => true,
                            'bounce' => $arVisual['MAIN_VIEW'] != 3,
                            'folding' => $arVisual['MAIN_VIEW'] == 3
                        ]
                    ], true)
                ]) ?>
                    <span class="intec-ui-part-effect-wrapper">
                        <i></i><i></i><i></i>
                    </span>
                <?= Html::endTag('span') ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?php } ?>
<div class="catalog-element-button-action-container">
    <!--noindex-->
        <?php $vButtons($arResult) ?>
        <?php if ($bOffers) {
            foreach ($arResult['OFFERS'] as &$arOffer)
                $vButtons($arOffer, true);

            unset($arOffer);
        } ?>
    <!--/noindex-->
</div>
<?php unset($vButtons) ?>