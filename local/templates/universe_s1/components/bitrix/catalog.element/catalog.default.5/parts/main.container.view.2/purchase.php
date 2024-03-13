<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arFields
 * @var bool $bOffers
 * @var bool $bSkuDynamic
 */

?>
<div class="catalog-element-purchase-container catalog-element-purchase-container-2 2" data-role="purchase">
    <div class="catalog-element-purchase">
        <div class="catalog-element-purchase-wrapper">
            <?php if ($arVisual['TIMER']['SHOW'] && !$bSkuList) { ?>
                <?php include(__DIR__ . '/../purchase/timer.php') ?>
            <?php } ?>
            <?php if (!$bOffers || $bSkuDynamic) { ?>
                <?php if ($arResult['DELAY']['USE'] || $arResult['COMPARE']['USE']) { ?>
                    <div class="catalog-element-button-action-block-container catalog-element-purchase-block">
                        <? include(__DIR__.'/../buttons.php') ?>
                    </div>
                <?php } ?>
                <?php if ($arVisual['PRICE']['SHOW'])
                    include(__DIR__ . '/../purchase/price.php');
                ?>
                <?php if ($arVisual['MEASURES']['USE'] && $arVisual['MEASURES']['POSITION'] === 'top')
                    include(__DIR__ . '/../purchase/measures.php');
                ?>
                <?php if ($arVisual['PRICE']['RANGE'])
                    include(__DIR__ . '/../purchase/price.range.php');
                ?>
                <?php if ($arFields['ADDITIONAL']['SHOW']) { ?>
                    <div class="catalog-element-purchase-block">
                        <?php include(__DIR__ . '/../purchase/products.additional.php') ?>
                    </div>
                <?php } ?>
                <?php if ($arVisual['CREDIT']['SHOW'] && !$bSkuList) {
                    include(__DIR__ . '/../purchase/credit.php');
                } ?>
                <?php if ($arVisual['QUANTITY']['SHOW'] || $arResult['FORM']['CHEAPER']['SHOW']) { ?>
                    <div class="catalog-element-purchase-block">
                        <div class="intec-grid intec-grid-wrap intec-grid-i-h-12 intec-grid-i-v-6">
                            <?php if ($arVisual['QUANTITY']['SHOW']) { ?>
                                <div class="catalog-element-quantity-container intec-grid-item-auto">
                                    <?php include(__DIR__ . '/../purchase/quantity.php') ?>
                                    <?php if ($arVisual['STORES']['USE'] && $arVisual['STORES']['POSITION'] === 'popup')
                                        include(__DIR__ . '/../purchase/quantity.store.php');
                                    ?>
                                </div>
                            <?php } ?>
                            <?php if ($arResult['FORM']['CHEAPER']['SHOW']) { ?>
                                <div class="intec-grid-item-auto">
                                    <?php include(__DIR__ . '/../purchase/cheaper.php') ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if (!empty($arResult['OFFERS'])) { ?>
                    <div class="catalog-element-purchase-block">
                        <?php include(__DIR__ . '/../offers.php'); ?>
                    </div>
					<?	
						$cc = $arResult["DETAIL_PAGE_URL"];
						$pp = 'ekipirovka';
						$pos1 = stripos($cc, $pp);
						if ($pos1 > 0) {
							if ($nFile != '') {
						    echo "<button type='button' class='btn btn-primary' style='margin-top:4px;margin-bottom:4px'".
						         "data-toggle='modal' ".
								 "data-target='#tablModal' ".
								 "data-img='$nFile' >".
					             "Таблица размеров".
								 "</button>";
							}
						} ?>
                <?php } ?>
                <?php if ($arResult['SHARES']['SHOW']) { ?>
                    <div class="catalog-element-purchase-block">
                        <?php include(__DIR__ . '/../shares.php') ?>
                    </div>
                <?php } ?>
                <?php if ($arResult['SIZES']['SHOW']) { ?>
                    <div class="catalog-element-purchase-block">
                        <?php include(__DIR__ . '/../sizes.php') ?>
                    </div>
                <?php } ?>
                <?php if ($arResult['ACTION'] !== 'none') { ?>
                    <div class="catalog-element-purchase-block catalog-element-purchase-block-400">
                        <div class="intec-grid intec-grid-wrap intec-grid-i-4">
                            <?php if ($arVisual['COUNTER']['SHOW']) { ?>
                                <div class="intec-grid-item-2">
                                    <?php include(__DIR__ . '/../purchase/counter.php') ?>
                                </div>
                            <?php } ?>
                            <div class="intec-grid-item">
                                <?php include(__DIR__ . '/../purchase/order.php') ?>
                            </div>
                            <?php if ($arResult['ORDER_FAST']['USE']) { ?>
                                <div class="catalog-element-buy-fast-container intec-grid-item-1">
                                    <?= Html::tag('div', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_BUY_BUTTON_ORDER_FAST'), [
                                        'class' => [
                                            'catalog-element-buy-fast',
                                            'intec-cl-text',
                                            'intec-cl-border'
                                        ],
                                        'data-role' => 'orderFast'
                                    ]) ?>
                                </div>
                            <?php } ?>
                            <?php if ($bRecalculation) { ?>
                                <div class="catalog-element-purchase-summary intec-grid-item-1" data-role="item.summary" style="display: none">
                                    <div class="catalog-element-purchase-summary-wrapper">
                                        <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_TITLE_SUMMARY') ?>
                                        <span data-role="item.summary.price"></span>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($arResult['DELIVERY_CALCULATION']['USE']) { ?>
                    <div class="catalog-element-purchase-block catalog-element-purchase-calculation">
                        <?php include(__DIR__ . '/../purchase/delivery.calculation.php') ?>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <?php if ($arVisual['PRICE']['SHOW'] && !empty($arResult['ITEM_PRICES']))
                    include(__DIR__ . '/../purchase/price.static.php');
                ?>
                <?php if ($arFields['ADDITIONAL']['SHOW']) { ?>
                    <div class="catalog-element-purchase-block">
                        <?php include(__DIR__ . '/../purchase/products.additional.php') ?>
                    </div>
                <?php } ?>
                <?php if ($arResult['FORM']['CHEAPER']['SHOW']) { ?>
                    <div class="catalog-element-purchase-block">
                        <?php include(__DIR__ . '/../purchase/cheaper.php') ?>
                    </div>
                <?php } ?>
                <?php include(__DIR__ . '/../purchase/order.static.php') ?>
            <?php } ?>
        </div>
    </div>

    <?php if ($arVisual['PROPERTIES']['PREVIEW']['SHOW'] || $arVisual['PROPERTIES']['PREVIEW']['OFFER_SHOW']) { ?>
        <div class="catalog-element-purchase-properties-preview">

            <?php include(__DIR__ . '/../properties.preview.php'); ?>
            <?= Html::tag('span', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_PROPERTIES_PREVIEW_BUTTON'), [
                'class' => [
                    'catalog-element-purchase-properties-preview-button',
                    'intec-cl-text',
                    'intec-cl-text-light-hover'
                ],
                'data-role' => 'properties.preview.button'
            ])?>
        </div>

    <?php } ?>
    <?php if ($arVisual['PRICE_INFO']['SHOW']) { ?>
        <?php if (empty($arVisual['PRICE_INFO']['TEXT']))
            $arVisual['PRICE_INFO']['TEXT'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_PRICE_INFO_TEXT_DEFAULT');
        ?>
        <div class="catalog-element-purchase-information">
            <?= $arVisual['PRICE_INFO']['TEXT'] ?>
        </div>
    <?php } ?>
</div>