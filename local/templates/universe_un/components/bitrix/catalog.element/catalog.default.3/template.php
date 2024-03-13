<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Json;
use intec\core\helpers\FileHelper;

/**
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

Loc::loadMessages(__FILE__);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];

if ($arResult['TAB']['USE'] && !empty($arResult['TAB']['VALUE']))
    return;

/**
 * @var array $arData
 */
include(__DIR__.'/parts/data.php');
include(__DIR__.'/parts/price.range.php');

$arPrice = null;
$bOffers = !empty($arResult['OFFERS']);

if (!empty($arResult['ITEM_PRICES']))
    $arPrice = ArrayHelper::getFirstValue($arResult['ITEM_PRICES']);

$arSvg = [
    'PLAY' => FileHelper::getFileData(__DIR__.'/svg/play.svg'),
    'GIF' => FileHelper::getFileData(__DIR__.'/svg/gif.svg'),
    'MEASURES' => [
        'ARROW' => FileHelper::getFileData(__DIR__.'/svg/measures.select.arrow.svg')
    ]
];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-element',
        'c-catalog-element-catalog-default-3'
    ],
    'data' => [
        'data' => Json::encode($arData, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true),
        'properties' => !empty($arResult['SKU_PROPS']) ? Json::encode($arResult['SKU_PROPS'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true) : '',
        'available' => $arData['available'] ? 'true' : 'false',
        'wide' => $arVisual['WIDE'] ? 'true' : 'false',
        'panel-mobile' => $arVisual['PANEL']['MOBILE']['SHOW'] ? 'true' : 'false'
    ]
]) ?>
    <div class="catalog-element-content intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <div data-role="dynamic">
                <?php if ($arVisual['PANEL']['DESKTOP']['SHOW']) { ?>
                    <!--noindex-->
                    <? include(__DIR__.'/parts/panel.php') ?>
                    <!--/noindex-->
                <?php } ?>
                <?php if ($arVisual['PANEL']['MOBILE']['SHOW']) { ?>
                    <!--noindex-->
                    <? include(__DIR__.'/parts/panel.mobile.php') ?>
                    <!--/noindex-->
                <?php } ?>
                <div class="intec-grid intec-grid-wrap">
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'intec-grid-item' => [
                                '2' => true,
                                '720-1' => $arVisual['WIDE'],
                                '1000-1' => !$arVisual['WIDE']
                            ]
                        ], true),
                    ]) ?>
                        <div class="catalog-element-block-left">
                            <div class="catalog-element-gallery-block">
                                <?php if ($arVisual['MARKS']['SHOW']) { ?>
                                    <div class="catalog-element-marks">
                                        <?php $APPLICATION->IncludeComponent(
                                            'intec.universe:main.markers',
                                            'template.1',
                                            $arResult['MARKS'],
                                            $component
                                        ) ?>
                                    </div>
                                <?php } ?>
                                <?php include(__DIR__.'/parts/gallery.php') ?>
                            </div>
                        </div>
                    <?= Html::endTag('div') ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'intec-grid-item' => [
                                '2' => true,
                                '720-1' => $arVisual['WIDE'],
                                '1000-1' => !$arVisual['WIDE']
                            ]
                        ], true)
                    ]) ?>
                        <div class="catalog-element-block-right">
                            <div class="intec-grid intec-grid-i-h-10 intec-a-v-center intec-ui-m-b-20">
                                <?php if ($arVisual['ARTICLE']['SHOW']) { ?>
                                     <div class="intec-grid-item">
                                        <?php include(__DIR__.'/parts/article.php') ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['BRAND']['SHOW']) { ?>
                                     <div class="intec-grid-item-auto intec-grid intec-grid-a-v-center">
                                        <?php include(__DIR__.'/parts/brand.php') ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['PRINT']['SHOW']) { ?>
                                    <!--noindex-->
                                    <div class="catalog-element-print-wrap intec-grid-item-auto intec-grid intec-grid-a-v-center" data-print="false">
                                        <div class="catalog-element-print" data-role="print">
                                            <svg width="21" height="19" viewBox="0 0 21 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20.7427 5.12061H0.742676V14.1206H4.74268V18.1206H16.7427V14.1206H20.7427V5.12061ZM14.7427 16.1206H6.74268V11.1206H14.7427V16.1206ZM17.7427 9.12061C17.1927 9.12061 16.7427 8.67061 16.7427 8.12061C16.7427 7.57061 17.1927 7.12061 17.7427 7.12061C18.2927 7.12061 18.7427 7.57061 18.7427 8.12061C18.7427 8.67061 18.2927 9.12061 17.7427 9.12061ZM16.7427 0.120605H4.74268V4.12061H16.7427V0.120605Z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <!--/noindex-->
                                <?php } ?>
                                <?php if ($arResult['SHARES']['SHOW']) { ?>
                                    <div class="intec-grid-item-auto intec-grid intec-grid-a-v-center" data-print="false">
                                        <?php include(__DIR__.'/parts/shares.php') ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php if ($arVisual['TIMER']['SHOW']) { ?>
                                <?php if ($arResult['SKU_VIEW'] == 'dynamic' || empty($arResult['OFFERS'])) { ?>
                                    <div class="catalog-element-timer" data-print="false">
                                        <?php include(__DIR__ . '/parts/timer.php') ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <?php if ($arVisual['QUANTITY']['MAIN']['SHOW']  && (empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'dynamic')) { ?>
                                <div class="catalog-element-main-quantity-wrap">
                                    <!--noindex-->
                                        <?php include(__DIR__.'/parts/quantity.php') ?>
                                    <!--/noindex-->
                                </div>
                            <?php } ?>
                            <?php if (empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'dynamic') { ?>
                                <?php if ($arVisual['PRICE']['SHOW'])
                                    include(__DIR__.'/parts/price.php');
                                ?>
                                <?php if ($arVisual['PRICE']['RANGE']) { ?>
                                    <div class="catalog-element-price-ranges">
                                        <?php $vPriceRange($arResult);

                                        if (!empty($arResult['OFFERS']))
                                            foreach ($arResult['OFFERS'] as &$arOffer) {
                                                $vPriceRange($arOffer, true);

                                                unset($arOffer);
                                            }
                                        ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['MEASURES']['USE'])
                                    include(__DIR__ . '/parts/measures.php');
                                ?>
                                <?php if (($arResult['FORM']['CHEAPER']['SHOW'])
                                    || ($arResult['DELIVERY_CALCULATION']['USE']
                                        && (empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'dynamic'))) { ?>
                                    <div class="catalog-element-information-part" data-print="false">
                                        <?php if ($arVisual['CREDIT']['SHOW']) { ?>
                                            <?php include(__DIR__.'/parts/credit.php'); ?>
                                        <?php } ?>
                                        <div class="intec-ui-m-t-15">
                                            <div class="intec-grid intec-grid-wrap intec-grid-i-h-15 intec-grid-i-v-8 intec-grid-a-v-center ">
                                                <?php if ($arResult['FORM']['CHEAPER']['SHOW']) { ?>
                                                    <div class="intec-grid-item-auto">
                                                        <?php include(__DIR__.'/parts/cheaper.php'); ?>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($arResult['DELIVERY_CALCULATION']['USE']
                                                    && (empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'dynamic')) { ?>
                                                    <div class="intec-grid-item-auto">
                                                        <?php include(__DIR__.'/parts/delivery.calculation.php'); ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($arResult['ACTION'] !== 'none') { ?>
                                    <div class="catalog-element-purchase-block" data-print="false">
                                        <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-v-10">
                                            <?php if ($arVisual['COUNTER']['SHOW']) { ?>
                                                <?= Html::beginTag('div', [
                                                    'class' => Html::cssClassFromArray([
                                                        'catalog-element-counter' => true,
                                                        'intec-grid-item' => [
                                                            'auto' => true,
                                                            '500-1' => true
                                                        ]
                                                    ], true)
                                                ]) ?>
                                                    <!--noindex-->
                                                        <?php include(__DIR__.'/parts/counter.php') ?>
                                                    <!--/noindex-->
                                                <?= Html::endTag('div') ?>
                                            <?php } ?>
                                            <?= Html::beginTag('div', [
                                                'class' => Html::cssClassFromArray([
                                                    'catalog-element-purchase' => true,
                                                    'intec-grid-item' => [
                                                        'auto' => true
                                                    ]
                                                ], true),
                                                'data-role' => 'purchase'
                                            ]) ?>
                                                <?php include(__DIR__.'/parts/purchase.php') ?>
                                            <?= Html::endTag('div') ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } else if (!empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'list') { ?>
                                <div class="catalog-element-information-part intec-grid intec-grid-wrap intec-grid-i-5 intec-grid-a-h-start intec-grid-a-v-center" data-print="false">
                                    <?php if ($arVisual['PRICE']['SHOW'] && !empty($arPrice)) { ?>
                                        <div class="intec-grid-item">
                                            <div class="catalog-element-price-discount intec-grid-item-auto" data-role="price.discount">
                                                <?= Loc::GetMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_PRICE_FROM') ?>
                                                <?= $arPrice['PRINT_PRICE'] ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($arResult['FORM']['CHEAPER']['SHOW']) { ?>
                                        <?php include(__DIR__.'/parts/cheaper.php') ?>
                                    <?php } ?>
                                    <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                        <div class="intec-ui intec-ui-control-button intec-ui-scheme-current intec-ui-size-5 intec-ui-mod-round-half" onclick="template.load(function (data) {
                                            var $ = this.getLibrary('$');
                                            var id = <?= JavaScript::toObject('#'.$sTemplateId.'-sku-list') ?>;
                                            var content = $(id, data.nodes);

                                            $(document).scrollTo(content, 500);
                                        }, {
                                            'name': '[Component] bitrix:catalog.element (catalog.default.3) > sku anchor',
                                            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
                                            'loader': {
                                                'name': 'lazy'
                                            }
                                        })">
                                            <?= Loc::GetMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_SKU_MORE');?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($arVisual['ADDITIONAL']['SHOW']) { ?>
                                <div class="catalog-element-additional-products" data-print="false">
                                    <?php include(__DIR__.'/parts/additional.php') ?>
                                </div>
                            <?php } ?>
                            <?php if ($arVisual['DESCRIPTION']['SHOW']) { ?>
                                <?= Html::beginTag('div', [
                                    'class' => [
                                        'catalog-element-description',
                                        'catalog-element-section'
                                    ],
                                    'data' => [
                                        'role' => 'section',
                                        'expanded' => $arVisual['DESCRIPTION']['EXPANDED'] ? 'true' : 'false'
                                    ]
                                ]) ?>
                                    <div class="catalog-element-section-name intec-ui-markup-header">
                                        <div class="catalog-element-section-name-wrapper" data-role="section.name">
                                            <span>
                                                <?php if (!empty($arVisual['DESCRIPTION']['NAME'])) { ?>
                                                    <?= $arVisual['DESCRIPTION']['NAME'] ?>
                                                <?php } else { ?>
                                                    <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_DESCRIPTION_NAME') ?>
                                                <?php } ?>
                                            </span>
                                            <div class="catalog-element-section-name-decoration"></div>
                                        </div>
                                    </div>
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'catalog-element-section-content',
                                            'catalog-element-description-value',
                                            'intec-ui-markup-text'
                                        ],
                                        'data' => [
                                            'role' => 'section.content',
                                            'code' => 'properties'
                                        ]
                                    ]) ?>
                                        <?php include(__DIR__.'/parts/description.php') ?>
                                    <?= Html::endTag('div') ?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                            <?php if (!empty($arResult['SKU_PROPS']) && !empty($arResult['OFFERS']) && $arResult['SKU_VIEW'] == 'dynamic') { ?>
                                <?= Html::beginTag('div', [
                                    'class' => [
                                        'catalog-element-offers',
                                        'catalog-element-section'
                                    ],
                                    'data' => [
                                        'role' => 'section',
                                        'expanded' => $arVisual['OFFERS']['EXPANDED'] ? 'true' : 'false'
                                    ]
                                ]) ?>
                                    <div class="catalog-element-section-name intec-ui-markup-header">
                                        <div class="catalog-element-section-name-wrapper" data-role="section.name">
                                            <span>
                                                <?php if (!empty($arVisual['OFFERS']['NAME'])) { ?>
                                                    <?= $arVisual['OFFERS']['NAME'] ?>
                                                <?php } else { ?>
                                                    <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_OFFERS_NAME') ?>
                                                <?php } ?>
                                            </span>
                                            <div class="catalog-element-section-name-decoration"></div>
                                        </div>
                                    </div>
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'catalog-element-offers-wrapper',
                                            'catalog-element-section-content'
                                        ],
                                        'data-role' => 'section.content'
                                    ]) ?>
                                        <div class="catalog-element-section-content-wrapper">
                                            <?php include(__DIR__.'/parts/sku.php') ?>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                            <?php if (
                                    $arVisual['PROPERTIES']['SHOW'] && !empty($arResult['DISPLAY_PROPERTIES']) ||
                                    $arVisual['OFFERS']['PROPERTIES']['SHOW'] && !empty($arResult['OFFERS_PROPERTIES'])
                            ) { ?>
                                <?= Html::beginTag('div', [
                                    'class' => [
                                        'catalog-element-properties',
                                        'catalog-element-section'
                                    ],
                                    'data' => [
                                        'role' => 'section',
                                        'expanded' => $arVisual['PROPERTIES']['EXPANDED'] ? 'true' : 'false',
                                    ]
                                ]) ?>
                                    <div class="catalog-element-section-name intec-ui-markup-header">
                                        <div class="catalog-element-section-name-wrapper" data-role="section.name">
                                            <span>
                                                <?php if (!empty($arVisual['PROPERTIES']['NAME'])) { ?>
                                                    <?= $arVisual['PROPERTIES']['NAME'] ?>
                                                <?php } else { ?>
                                                    <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_PROPERTIES_NAME') ?>
                                                <?php } ?>
                                            </span>
                                            <div class="catalog-element-section-name-decoration"></div>
                                        </div>
                                    </div>
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'catalog-element-properties-wrapper',
                                            'catalog-element-section-content'
                                        ],
                                        'data' => [
                                            'role' => 'section.content',
                                             'code' => 'properties'
                                        ]
                                    ]) ?>
                                        <div class="catalog-element-section-content-wrapper">
                                            <?php include(__DIR__.'/parts/properties.php') ?>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                            <?php if ($arVisual['INFORMATION']['PAYMENT']['SHOW'] || $arVisual['INFORMATION']['SHIPMENT']['SHOW']) { ?>
                                <div class="catalog-element-information" data-print="false">
                                    <?php include(__DIR__.'/parts/information.php') ?>
                                </div>
                            <?php } ?>
                            <?php if ($arVisual['ADVANTAGES']['SHOW']) { ?>
                                <div class="catalog-element-advantages">
                                    <?php include(__DIR__.'/parts/advantages.php') ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?= Html::endTag('div') ?>
                </div>
                <?php if (!empty($arResult['OFFERS']) && $arResult['SKU_VIEW'] == 'list') {
                    include(__DIR__.'/parts/sku.list.php');
                } ?>
            </div>

            <?php if ($arVisual['GIFTS']['SHOW']) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-gifts',
                        'catalog-element-section'
                    ],
                    'data' => [
                        'role' => 'section',
                        'expanded' => $arVisual['GIFTS']['EXPANDED'] ? 'true' : 'false',
                        'print' => "false"
                    ]
                ]) ?>
                    <div class="catalog-element-section-content-wrapper">
                        <?php include(__DIR__.'/parts/sale.products.gift.php') ?>
                    </div>
                <?= Html::endTag('div') ?>
            <?php } ?>

            <?php if ($arVisual['ACCESSORIES']['SHOW']) { ?>
                <?php $bIsLink = $arVisual['ACCESSORIES']['VIEW'] === 'link' ?>
                <?= Html::beginTag($bIsLink ? 'a' : 'div', [
                    'class' => [
                        'catalog-element-accessories',
                        'catalog-element-section',
                        $bIsLink ? 'intec-cl-text-hover' : null
                    ],
                    'data' => [
                        'role' => 'section',
                        'expanded' => $arVisual['ACCESSORIES']['EXPANDED'] ? 'true' : 'false',
                        'print' => "false"
                    ],
                    'href' => $bIsLink ? $arVisual['ACCESSORIES']['LINK'] : null,
                    'target' => $bIsLink ? '_blank' : null
                ]) ?>
                    <div class="catalog-element-section-name">
                        <div class="catalog-element-section-name-wrapper" data-role="section.name">
                                <span>
                                    <?php if (!empty($arVisual['ACCESSORIES']['NAME'])) { ?>
                                        <?= $arVisual['ACCESSORIES']['NAME'] ?>
                                    <?php } else { ?>
                                        <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_ACCESSORIES_NAME') ?>
                                    <?php } ?>
                                </span>
                            <?php if (!$bIsLink) { ?>
                                <div class="catalog-element-section-name-decoration"></div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if (!$bIsLink) { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-element-accessories-wrapper',
                                'catalog-element-section-content',

                            ],
                            'data' => [
                                'role' => 'section.content'
                            ]
                        ]) ?>
                            <div class="catalog-element-section-content-wrapper">
                                <?php include(__DIR__.'/parts/accessories.php') ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag($bIsLink ? 'a' : 'div') ?>
            <?php } ?>
            <?php if ($arVisual['ASSOCIATED']['SHOW']) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-associated',
                        'catalog-element-section'
                    ],
                    'data' => [
                        'role' => 'section',
                        'expanded' => $arVisual['ASSOCIATED']['EXPANDED'] ? 'true' : 'false',
                        'print' => "false"
                    ]
                ]) ?>
                    <div class="catalog-element-section-name intec-ui-markup-header">
                        <div class="catalog-element-section-name-wrapper" data-role="section.name">
                            <span>
                                <?php if (!empty($arVisual['ASSOCIATED']['NAME'])) { ?>
                                    <?= $arVisual['ASSOCIATED']['NAME'] ?>
                                <?php } else { ?>
                                    <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_ASSOCIATED_NAME') ?>
                                <?php } ?>
                            </span>
                            <div class="catalog-element-section-name-decoration"></div>
                        </div>
                    </div>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-element-associated-wrapper',
                            'catalog-element-section-content'
                        ],
                        'data' => [
                            'role' => 'section.content'
                        ]
                    ]) ?>
                        <div class="catalog-element-section-content-wrapper">
                            <?php include(__DIR__.'/parts/associated.php') ?>
                        </div>
                    <?= Html::endTag('div') ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
            <?php if ($arVisual['RECOMMENDED']['SHOW']) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-associated',
                        'catalog-element-section'
                    ],
                    'data' => [
                        'role' => 'section',
                        'expanded' => $arVisual['RECOMMENDED']['EXPANDED'] ? 'true' : 'false',
                        'print' => "false"
                    ]
                ]) ?>
                    <div class="catalog-element-section-name intec-ui-markup-header">
                        <div class="catalog-element-section-name-wrapper" data-role="section.name">
                            <span>
                                <?php if (!empty($arVisual['RECOMMENDED']['NAME'])) { ?>
                                    <?= $arVisual['RECOMMENDED']['NAME'] ?>
                                <?php } else { ?>
                                    <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_RECOMMEND_NAME') ?>
                                <?php } ?>
                            </span>
                            <div class="catalog-element-section-name-decoration"></div>
                        </div>
                    </div>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-element-associated-wrapper',
                            'catalog-element-section-content'
                        ],
                        'data' => [
                            'role' => 'section.content'
                        ]
                    ]) ?>
                        <div class="catalog-element-section-content-wrapper">
                            <?php include(__DIR__.'/parts/recommended.php') ?>
                        </div>
                    <?= Html::endTag('div') ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
            <?php if ($arVisual['SERVICES']['SHOW']) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-associated',
                        'catalog-element-section'
                    ],
                    'data' => [
                        'role' => 'section',
                        'expanded' => $arVisual['SERVICES']['EXPANDED'] ? 'true' : 'false',
                        'print' => "false"
                    ]
                ]) ?>
                    <div class="catalog-element-section-name intec-ui-markup-header">
                        <div class="catalog-element-section-name-wrapper" data-role="section.name">
                            <span>
                                <?php if (!empty($arVisual['SERVICES']['NAME'])) { ?>
                                    <?= $arVisual['SERVICES']['NAME'] ?>
                                <?php } else { ?>
                                    <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_SERVICES_NAME') ?>
                                <?php } ?>
                            </span>
                            <div class="catalog-element-section-name-decoration"></div>
                        </div>
                    </div>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-element-associated-wrapper',
                            'catalog-element-section-content'
                        ],
                        'data' => [
                            'role' => 'section.content'
                        ]
                    ]) ?>
                        <div class="catalog-element-section-content-wrapper">
                            <?php include(__DIR__.'/parts/services.php') ?>
                        </div>
                    <?= Html::endTag('div') ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
            <?php if ($arVisual['ARTICLES']['SHOW']) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-articles',
                        'catalog-element-section'
                    ],
                    'data' => [
                        'role' => 'section',
                        'expanded' => $arVisual['ARTICLES']['EXPANDED'] ? 'true' : 'false',
                        'print' => "false"
                    ]
                ]) ?>
                    <div class="catalog-element-section-name intec-ui-markup-header">
                        <div class="catalog-element-section-name-wrapper" data-role="section.name">
                                <span>
                                    <?php if (!empty($arVisual['ARTICLES']['NAME'])) { ?>
                                        <?= $arVisual['ARTICLES']['NAME'] ?>
                                    <?php } else { ?>
                                        <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_ARTICLES_NAME') ?>
                                    <?php } ?>
                                </span>
                            <div class="catalog-element-section-name-decoration"></div>
                        </div>
                    </div>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-element-associated-wrapper',
                            'catalog-element-section-content'
                        ],
                        'data' => [
                            'role' => 'section.content'
                        ]
                    ]) ?>
                        <div class="catalog-element-section-content-wrapper">
                            <?php include(__DIR__.'/parts/articles.php') ?>
                        </div>
                    <?= Html::endTag('div') ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
            <?php include(__DIR__.'/parts/microdata.php') ?>
        </div>
    </div>
<?= Html::endTag('div') ?>
<?php include(__DIR__.'/parts/script.php') ?>
