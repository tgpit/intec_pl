<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;

/**
 * @var array $arResult
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 */

Loc::loadMessages(__FILE__);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

/**
 * @var array $arData
 */
include(__DIR__.'/parts/data.php');

$arVisual = $arResult['VISUAL'];
$arPrice = null;

if (!empty($arResult['ITEM_PRICES']))
    $arPrice = ArrayHelper::getFirstValue($arResult['ITEM_PRICES']);

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-element',
        'c-catalog-element-quick-view-2'
    ],
    'data' => [
        'data' => Json::encode($arData, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true),
        'properties' => Json::encode($arResult['SKU_PROPS'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true),
        'available' => $arData['available'] ? 'true' : 'false',
        'wide' => $arVisual['WIDE'] ? 'true' : 'false'
    ],
    'style' => [
        'opacity' => 0
    ]
]) ?>
    <div class="catalog-element-wrapper">
        <div class="catalog-element-name">
            <?= $arResult['NAME'] ?>
        </div>
        <div class="catalog-element-content scrollbar-inner" data-role="scroll">
            <div class="catalog-element-content-wrapper intec-grid intec-grid-768-wrap">
                <div class="catalog-element-content-left intec-grid-item-auto intec-grid-item-768-1">
                    <div class="catalog-element-gallery-block">
                        <?php if ($arVisual['MARKS']['SHOW']) { ?>
                            <div class="catalog-element-marks">
                                <?php $APPLICATION->IncludeComponent(
                                    'intec.universe:main.markers',
                                    'template.1', [
                                        'RECOMMEND' => $arResult['MARKS']['RECOMMEND'] ? 'Y' : 'N',
                                        'NEW' => $arResult['MARKS']['NEW'] ? 'Y' : 'N',
                                        'HIT' => $arResult['MARKS']['HIT'] ? 'Y' : 'N',
                                        'SHARE' => $arResult['MARKS']['SHARE'] ? 'Y' : 'N',
                                        'ORIENTATION' => $arResult['MARKS']['ORIENTATION']
                                    ],
                                    $component,
                                    ['HIDE_ICONS' => 'Y']
                                ) ?>
                            </div>
                        <?php } ?>
                        <?php include(__DIR__.'/parts/gallery.php') ?>
                    </div>
                </div>
                <div class="catalog-element-content-right intec-grid-item intec-grid-item-768-1 scrollbar-inner" data-role="scroll.right">
                    <div class="catalog-element-content-right-wrapper">
                        <?php if ($arVisual['TIMER']['SHOW']) { ?>
                            <div class="catalog-element-timer-block">
                                <?php include(__DIR__.'/parts/timer.php') ?>
                            </div>
                        <?php } ?>
                        <?php if ($arVisual['QUANTITY']['SHOW']) { ?>
                            <div class="catalog-element-quantity">
                                <?php include(__DIR__.'/parts/quantity.php') ?>
                            </div>
                        <?php } ?>
                        <?php if ($arVisual['PRICE']['SHOW'] || $arResult['ACTION'] !== 'none') { ?>
                            <div class="catalog-element-action-block intec-grid intec-grid-wrap intec-grid-768-wrap intec-grid-i-10">
                                <?php if ($arVisual['PRICE']['SHOW']) { ?>
                                    <div class="intec-grid-item-1">
                                        <?= Html::beginTag('div', [
                                            'class' => [
                                                'catalog-element-price',
                                            ],
                                            'data' => [
                                                'role' => 'price',
                                                'show' => !empty($arPrice) ? 'true' : 'false',
                                                'discount' => !empty($arPrice) && $arPrice['PERCENT'] > 0 ? 'true' : 'false'
                                            ]
                                        ]) ?>
                                        <div class="catalog-element-price-base" data-role="price.discount">
                                            <?= !empty($arPrice) ? $arPrice['PRINT_PRICE'] : null ?>
                                        </div>
                                        <div class="catalog-element-price-discount" data-role="price.base">
                                            <?= !empty($arPrice) ? $arPrice['PRINT_PRICE'] : null ?>
                                        </div>
                                        <div class="catalog-element-price-percent" data-role="price.percent"></div>
                                        <?= Html::endTag('div') ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arResult['ACTION'] !== 'none') { ?>
                                    <div class="intec-grid-item-auto intec-grid-item-768-1">
                                        <div class="intec-grid intec-grid-i-10 intec-grid-a-h-start intec-grid-nowrap intec-grid-1000-wrap">
                                            <?php if ($arVisual['COUNTER']['SHOW']) { ?>
                                                <div class="intec-grid-item-auto intec-grid-item-1000-1">
                                                    <?php include(__DIR__.'/parts/counter.php') ?>
                                                </div>
                                            <?php } ?>
                                            <div class="intec-grid-item-auto intec-grid-item-768-1">
                                                <?php include(__DIR__.'/parts/purchase.php') ?>
                                            </div>
                                            <?php if ($arResult['DELAY']['USE'] || $arResult['COMPARE']['USE']) { ?>
                                                <div class="intec-grid-item-auto intec-grid-item-768-1 intec-grid-item-a-center">
                                                    <?php include(__DIR__.'/parts/buttons.php') ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if (!empty($arResult['OFFERS'])) { ?>
                            <div class="catalog-element-offers">
                                <?php include(__DIR__.'/parts/sku.php') ?>
                            </div>
                        <?php } ?>
                        <?php if ($arVisual['ADDITIONAL_PRODUCTS']['SHOW'] && !is_null($arVisual['ADDITIONAL_PRODUCTS']['VALUES'][0])) { ?>
                            <div class="catalog-element-additional-products intec-grid-item-1">
                                <?php include(__DIR__.'/parts/additional.products.php') ?>
                            </div>
                        <?php } ?>
                        <?php if ($arVisual['DESCRIPTION']['SHOW']) { ?>
                            <div class="catalog-element-description">
                                <?= strip_tags($arResult[$arVisual['DESCRIPTION']['MODE'] === 'preview' ? 'PREVIEW_TEXT' : 'DETAIL_TEXT'], '<br>') ?>
                            </div>
                        <?php } ?>
                        <?php if ($arVisual['PROPERTIES']['SHOW']) { ?>
                            <?php include(__DIR__.'/parts/properties.php') ?>
                        <?php } ?>
                        <?php if ($arVisual['DETAIL']['SHOW']) { ?>
                            <div class="catalog-element-detail">
                                <?= Html::tag('a', Loc::getMessage('C_CATALOG_ELEMENT_QUICK_VIEW_2_BUTTON_DETAIL'), [
                                    'class' => [
                                        'catalog-element-detail-button',
                                        'intec-ui',
                                        'intec-ui-mod-transparent',
                                        'intec-ui-control-button',
                                        'intec-ui-scheme-current'
                                    ],
                                    'href' => Html::decode($arResult['DETAIL_PAGE_URL']),
                                    'data-role' => 'offer.link'
                                ]) ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>
<?php include(__DIR__.'/parts/script.php') ?>