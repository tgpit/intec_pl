<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<div class="catalog-element-offer-credit catalog-element-price-credit">
    <?php $iOfferPrice = $arOffer['MIN_PRICE']['DISCOUNT_VALUE'] ?>
    <?php $sOfferCurrency = $arOffer['MIN_PRICE']['CURRENCY'] ?>
    <?= Html::beginTag( 'div', [
        'class' => [
            'catalog-element-credit',
            'catalog-element-block'
        ],
        'data' => [
            'role' => 'credit',
            'price' => $iOfferPrice,
            'currency' => $sOfferCurrency
        ]
    ]) ?>
        <div class="catalog-element-credit-wrapper intec-grid intec-grid-a-v-center">
            <div class="catalog-element-credit-icon intec-cl-svg">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M6 13V9H14V13" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M14 9H15V5H7V9" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17.8284 14.1716C19.3905 15.7337 19.3905 18.2663 17.8284 19.8284C16.2663 21.3905 13.7337 21.3905 12.1716 19.8284C10.6095 18.2663 10.6095 15.7337 12.1716 14.1716C13.7337 12.6095 16.2663 12.6095 17.8284 14.1716" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 17V21H15" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15 13H5V17H11" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="catalog-element-credit-value">
                <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_CREDIT_BUY_BY_CREDIT') ?>
                <?= Html::beginTag($arVisual['CREDIT']['LINK']['USE'] ? 'a' : 'span', [
                    'href' => $arVisual['CREDIT']['LINK']['USE'] ? $arVisual['CREDIT']['LINK']['VALUE'] : null,
                    'class' => $arVisual['CREDIT']['LINK']['USE'] ? null : 'intec-cl-text'
                ]) ?>
                    <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_CREDIT_PER') ?>
                    <span data-role="price.credit"></span>
                    <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_CREDIT_TIME') ?>
                <?=  Html::endTag($arVisual['CREDIT']['LINK']['USE'] ? 'a' : 'span')?>
            </div>
        </div>
    <?= Html::endTag('div') ?>
</div>

