<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Loader;
use intec\Core;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);

$oRequest = Core::$app->request;
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$bIsBase = Loader::includeModule('catalog') && Loader::includeModule('sale');
$bIsLite = !$bIsBase && Loader::includeModule('intec.startshop');
$bIsAjax = false;
$bSeo = Loader::includeModule('intec.seo');

if ($oRequest->getIsAjax()) {
    $bIsAjax = $oRequest->get('catalog');
    $bIsAjax = ArrayHelper::getValue($bIsAjax, 'ajax') === 'Y';
}

$arParams = ArrayHelper::merge([
    'SECTIONS_CHILDREN_SECTION_DESCRIPTION_SHOW' => 'Y',
    'SECTIONS_CHILDREN_SECTION_DESCRIPTION_POSITION' => 'top',
    'SECTIONS_CHILDREN_CANONICAL_URL_USE' => 'N',
    'SECTIONS_CHILDREN_CANONICAL_URL_TEMPLATE' => null,
    'SECTIONS_CHILDREN_MENU_SHOW' => 'Y',
    'SECTIONS_CHILDREN_EXTENDING_USE' => 'N',
    'SECTIONS_CHILDREN_EXTENDING_COUNT' => 30,
    'SECTIONS_CHILDREN_EXTENDING_PROPERTY' => 'UF_SECTIONS',
    'SECTIONS_CHILDREN_EXTENDING_TEMPLATE' => null,
    'SECTIONS_ARTICLES_EXTENDING_PROPERTY' => 'UF_SECTIONS_ARTICLES',
    'SECTIONS_ARTICLES_EXTENDING_TEMPLATE' => null,
    'FILTER_AJAX' => 'N',
    'SECTIONS_LAYOUT' => '1',
    'GIFTS_SECTION_LIST_POSITION' => 'bottom'
], $arParams);

$sLayout = ArrayHelper::fromRange([1, 2], $arParams['SECTIONS_LAYOUT']);

include(__DIR__.'/parts/sort.php');

$arIBlock = $arResult['IBLOCK'];
$arSection = $arResult['SECTION'];
$arSeo = null;
$arCanonicalUrl = [
    'USE' => $arParams['SECTIONS_CHILDREN_CANONICAL_URL_USE'] === 'Y',
    'TEMPLATE' => $arParams['SECTIONS_CHILDREN_CANONICAL_URL_TEMPLATE']
];

$sPrefix = 'INTEREST_PRODUCTS_';
$iLength = StringHelper::length($sPrefix);
$arInterestProductsProperties = [];

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $sPrefix))
        continue;

    $sKey = StringHelper::cut($sKey, $iLength);
    $arInterestProductsProperties[$sKey] = $sValue;
}

unset($sPrefix, $iLength, $sKey, $sValue);

$arInterestProducts = $arSection['INTEREST_PRODUCTS'];
$GLOBALS['arrFiltersInterestProducts'] = [
    'ID' => $arInterestProducts['ITEMS']
];

$arInterestProductsProperties = ArrayHelper::merge($arInterestProductsProperties, [
    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
    'FILTER_NAME' => 'arrFiltersInterestProducts',
    'ELEMENT_COUNT' => $arInterestProducts['COUNT'],
    'PRICE_CODE' => $arParams['PRICE_CODE'],
    'SET_BROWSER_TITLE' => 'N',
    'SET_TITLE' => 'N',
    'SET_META_KEYWORDS' => 'N',
    'SET_META_DESCRIPTION' => 'N',
    'SHOW_ALL_WO_SECTION' => 'Y',
    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
    'COMPATIBLE_MODE' => $arParams['COMPATIBLE_MODE']
]);

if (empty($arCanonicalUrl['TEMPLATE']) || empty($arSection))
    $arCanonicalUrl['USE'] = false;

$arDescription = [
    'SHOW' => $arParams['SECTIONS_CHILDREN_SECTION_DESCRIPTION_SHOW'] === 'Y',
    'POSITION' => ArrayHelper::fromRange([
        'top',
        'bottom'
    ], $arParams['SECTIONS_CHILDREN_SECTION_DESCRIPTION_POSITION']),
    'VALUE' => !empty($arSection) ? $arSection['DESCRIPTION'] : null
];

if (empty($arDescription['VALUE']))
    $arDescription['SHOW'] = false;

$sLevel = 'CHILDREN';

include(__DIR__.'/parts/menu.php');
include(__DIR__.'/parts/tags.php');
include(__DIR__.'/parts/filter.php');
include(__DIR__.'/parts/sections.php');
include(__DIR__.'/parts/elements.php');

$arSectionsExtending['PARAMETERS']['ELEMENT_SORT_FIELD'] = 'ID';
$arArticlesExtending['PARAMETERS']['FILTER_NAME'] = 'arCatalogArticlesExtendingFilter';
$arArticlesExtending['PARAMETERS']['SORT_BY1'] = 'ID';
$arSectionsExtending['PARAMETERS']['FILTER_NAME'] = 'arCatalogSectionsExtendingFilter';
$arTags['SHOWED'] = [
    'DESKTOP' => false,
    'MOBILE' => false
];
$arMenu['SHOW'] = $arMenu['SHOW'] && $arParams['SECTIONS_CHILDREN_MENU_SHOW'] === 'Y';
$arColumns = [
    'SHOW' => $arMenu['SHOW'] || ($arFilter['SHOW'] && $arFilter['TYPE'] === 'vertical')
];

if ($arColumns['SHOW']) {
    $arSections['PARAMETERS']['WIDE'] = 'N';
    $arElements['PARAMETERS']['WIDE'] = 'N';
    $arSectionsExtending['PARAMETERS']['WIDE'] = 'N';
} elseif ($arResult['PRODUCTS']['INTEREST']['SHOW']) {
    $arResult['PRODUCTS']['INTEREST']['POSITION'] = 'footer';
}

if ($arParams['SECTIONS_LAYOUT'] == 2) {
    $arSections['PARAMETERS']['WIDE'] = 'Y';
}

if ($arCanonicalUrl['USE'])
    $APPLICATION->SetPageProperty('canonical', CIBlock::ReplaceSectionUrl(
        $arCanonicalUrl['TEMPLATE'],
        $arSection,
        true,
        'S'
    ));

if (!empty($arSection['PICTURE'])) {
    $sPicture = CFile::GetPath($arSection['PICTURE']);
    $APPLICATION->SetPageProperty('og:image', Core::$app->request->getHostInfo() . $sPicture);
    unset($sPicture);
}

if ($arTags['SHOW'] && !$bSeo) {
    $this->SetViewTarget($sTemplateId.'-tags');

    $APPLICATION->IncludeComponent(
        'intec.universe:tags.list',
        $arTags['TEMPLATE'],
        $arTags['PARAMETERS'],
        $component
    );

    $this->EndViewTarget();
}

if ($arTags['SHOW']['MOBILE'] && $bSeo) {
    $this->SetViewTarget($sTemplateId.'-tags-mobile');

    $APPLICATION->IncludeComponent('intec.seo:filter.tags', '',
        $arTags['PARAMETERS'], $component);

    $this->EndViewTarget();
}

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog',
        'c-catalog-catalog-1',
        'p-section'
    ],
    'data' => [
        'layout' => $arParams['SECTIONS_LAYOUT']
    ]
]) ?>
    <div class="catalog-wrapper intec-content intec-content-visible">
        <div class="catalog-wrapper-2 intec-content-wrapper">
            <?php if ($sLayout === '2') { ?>
                <?php $APPLICATION->ShowViewContent($sTemplateId.'-description-top') ?>
                <?php if ($arSections['SHOW']) { ?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.section.list',
                        $arSections['TEMPLATE'],
                        $arSections['PARAMETERS'],
                        $component
                    ) ?>
                <?php } ?>
                <?php if ($arTags['POSITION']['DESKTOP'] == 'top') { ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-menu-tags',
                            'desktop'
                        ],
                        'data' => [
                            'position' => 'top',
                            'mobile-use' => $arTags['SHOW']['MOBILE'] ? 'true' : 'false'
                        ]
                    ]) ?>
                        <?php $arTags['SHOWED']['DESKTOP'] = true ?>
                        <?php $APPLICATION->ShowViewContent($sTemplateId.'-tags') ?>
                    <?= Html::endTag('div') ?>
                <?php } elseif ($arTags['SHOW']['MOBILE'] && $arTags['POSITION']['MOBILE'] == 'top') { ?>
                    <div class="catalog-menu-tags mobile" data-position="top">
                        <?php $arTags['SHOWED']['MOBILE'] = true ?>
                        <?php $APPLICATION->ShowViewContent($sTemplateId.'-tags-mobile') ?>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if ($arFilter['SHOW'] && $arFilter['TYPE'] === 'horizontal') { ?>
                <?php if ($sLayout === '1') { ?>
                    <?php if ($arTags['POSITION']['DESKTOP'] == 'top') { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-menu-tags',
                                'desktop'
                            ],
                            'data' => [
                                'position' => 'top',
                                'mobile-use' => $arTags['SHOW']['MOBILE'] ? 'true' : 'false'
                            ]
                        ]) ?>
                            <?php $arTags['SHOWED']['DESKTOP'] = true ?>
                            <?php $APPLICATION->ShowViewContent($sTemplateId.'-tags') ?>
                        <?= Html::endTag('div') ?>
                    <?php } elseif ($arTags['SHOW']['MOBILE'] && $arTags['POSITION']['MOBILE'] == 'top') { ?>
                        <div class="catalog-menu-tags mobile" data-position="top">
                            <?php $arTags['SHOWED']['MOBILE'] = true ?>
                            <?php $APPLICATION->ShowViewContent($sTemplateId.'-tags-mobile') ?>
                        </div>
                    <?php } ?>
                <?php } ?>
                    <!--noindex-->
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.smart.filter',
                        $arFilter['TEMPLATE'],
                        $arFilter['PARAMETERS'],
                        $component
                    ) ?>
                    <!--/noindex-->
            <?php } ?>
            <?= Html::beginTag('div', [
                'class' => 'catalog-content',
                'data' => [
                    'role' => !$arColumns['SHOW'] ? 'catalog.content' : null
                ]
            ]) ?>
                <?php if ($arColumns['SHOW']) { ?>
                    <div class="catalog-content-left intec-content-left">
                        <?php if ($arFilter['SHOW'] && $arFilter['TYPE'] === 'vertical') { ?>
                            <!--noindex-->
                            <?php $APPLICATION->IncludeComponent(
                                'bitrix:catalog.smart.filter',
                                $arFilter['TEMPLATE'],
                                $arFilter['PARAMETERS'],
                                $component
                            ) ?>
                            <!--/noindex-->
                        <?php } ?>
                        <?php if ($arMenu['SHOW']) { ?>
                            <div class="catalog-menu">
                                <?php $APPLICATION->IncludeComponent(
                                    'bitrix:menu',
                                    $arMenu['TEMPLATE'],
                                    $arMenu['PARAMETERS'],
                                    $component
                                ) ?>
                            </div>
                        <?php } ?>
                        <?php if ($arTags['POSITION']['DESKTOP'] == 'menu' && !$arTags['SHOWED']['DESKTOP']) { ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'catalog-menu-tags',
                                    'desktop'
                                ],
                                'data' => [
                                    'mobile-use' => $arTags['SHOW']['MOBILE'] ? 'true' : 'false'
                                ]
                            ]) ?>
                                <?php $arTags['SHOWED']['DESKTOP'] = true; ?>
                                <?php $APPLICATION->ShowViewContent($sTemplateId.'-tags'); ?>
                            <?= Html::endTag('div') ?>
                        <?php } elseif ($arTags['SHOW']['MOBILE'] && !$arTags['SHOWED']['MOBILE'] && $arTags['POSITION']['MOBILE'] == 'menu') { ?>
                            <div class="catalog-menu-tags mobile">
                                <?php $arTags['SHOWED']['MOBILE'] = true ?>
                                <?php $APPLICATION->ShowViewContent($sTemplateId.'-tags-mobile') ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="catalog-content-right intec-content-right">
                        <div class="catalog-content-right-wrapper intec-content-right-wrapper" data-role="catalog.content">
                <?php } ?>
                <?php if ($sLayout === '1') { ?>
                    <?php if (!$arTags['SHOWED']['DESKTOP'] && $arTags['POSITION']['DESKTOP'] == 'top') { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-menu-tags',
                                'desktop'
                            ],
                            'data' => [
                                'position' => 'top',
                                'mobile-use' => $arTags['SHOW']['MOBILE'] ? 'true' : 'false'
                            ]
                        ]) ?>
                            <?php $arTags['SHOWED']['DESKTOP'] = true ?>
                            <?php $APPLICATION->ShowViewContent($sTemplateId.'-tags') ?>
                        <?= Html::endTag('div') ?>
                    <?php } elseif ($arTags['SHOW']['MOBILE'] && !$arTags['SHOWED']['MOBILE'] && $arTags['POSITION']['MOBILE'] == 'top') { ?>
                        <div class="catalog-menu-tags mobile">
                            <?php $arTags['SHOWED']['MOBILE'] = true ?>
                            <?php $APPLICATION->ShowViewContent($sTemplateId.'-tags-mobile') ?>
                        </div>
                    <?php } ?>
                <?php } ?>
                <?php if ($bIsAjax) $APPLICATION->RestartBuffer() ?>
                <?php if ($sLayout === '1') { ?>
                    <?php $APPLICATION->ShowViewContent($sTemplateId.'-description-top') ?>
                    <?php if ($arSections['SHOW']) { ?>
                        <?php $APPLICATION->IncludeComponent(
                            'bitrix:catalog.section.list',
                            $arSections['TEMPLATE'],
                            $arSections['PARAMETERS'],
                            $component
                        ) ?>
                    <?php } ?>
                <?php } ?>
                <?php if ($arElements['SHOW']) { ?>
                    <?php include(__DIR__.'/parts/panel.php') ?>
                    <?php include(__DIR__.'/parts/preloader.php') ?>
                    <?php
                        foreach ($arSort['PROPERTIES'] as $arSortProperty) {
                            if ($arSortProperty['ACTIVE']) {
                                $arElements['PARAMETERS']['ELEMENT_SORT_FIELD'] = $arSortProperty['FIELD'];
                                $arElements['PARAMETERS']['ELEMENT_SORT_ORDER'] = $arSort['ORDER'];

                                break;
                            }
                        }

                        unset($arSortProperty);
                    ?>
                <?php } ?>
                <?php if ($arElements['SHOW'] || !empty($arElements['TEMPLATE'])) { ?>                    

					<?php
						if ($arElements['TEMPLATE'] == 'catalog.tile.3') {
							$APPLICATION->IncludeComponent(
    	                    	'bitrix:catalog.section',
        	                	'catalog.tabr',
            	            	$arElements['PARAMETERS'],
                	        	$component); 
						}else{
							$APPLICATION->IncludeComponent(
    	                    	'bitrix:catalog.section',
        	                	$arElements['TEMPLATE'],
            	            	$arElements['PARAMETERS'],
                	        	$component);
						}
					?>

<?// var_dump($arElements);?>
                    <?php if ($sLayout === '1') { ?>
                        <?php if (!$arTags['SHOWED']['DESKTOP'] && $arTags['POSITION']['DESKTOP'] == 'bottom') { ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'catalog-menu-tags',
                                    'desktop'
                                ],
                                'data' => [
                                    'position' => 'top',
                                    'mobile-use' => $arTags['SHOW']['MOBILE'] ? 'true' : 'false'
                                ]
                            ]) ?>
                                <?php $arTags['SHOWED']['DESKTOP'] = true ?>
                                <?php $APPLICATION->ShowViewContent($sTemplateId.'-tags') ?>
                            <?= Html::endTag('div') ?>
                        <?php } elseif ($arTags['SHOW']['MOBILE'] && !$arTags['SHOWED']['MOBILE'] && $arTags['POSITION']['MOBILE'] == 'bottom') { ?>
                            <div class="catalog-menu-tags mobile">
                                <?php $arTags['SHOWED']['MOBILE'] = true ?>
                                <?php $APPLICATION->ShowViewContent($sTemplateId.'-tags-mobile') ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <?php if ($arSectionsExtending['SHOW']) { ?>
                        <?php $arSectionsExtending['RESULT'] = $APPLICATION->IncludeComponent('intec.seo:iblocks.section.extend.filter', '', [
                            'IBLOCK_ID' => $arIBlock['ID'],
                            'SECTION_ID' => $arSection['ID'],
                            'SECTIONS_ID' => ArrayHelper::getValue($arSection, $arSectionsExtending['PROPERTY']),
                            'HAS_COUNT' => $arElements['COUNT'],
                            'CURRENT_URL' => $_SERVER['REQUEST_URI'],
                            'FILTER_NAME' => 'arCatalogSectionsExtendingFilter',
                            'INCLUDE_SUBSECTIONS' => 'Y',
                            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                            'CACHE_TIME' => $arParams['CACHE_TIME']
                        ], $component) ?>
                        <?php if (!empty($arSectionsExtending['RESULT']) && !empty($arSectionsExtending['RESULT']['FILTER'])) { ?>
                            <div class="catalog-section-extending">
                                <?php $arSectionsExtending['PARAMETERS']['ELEMENT_SORT_ORDER'] = $arSectionsExtending['RESULT']['FILTER']['ID']; ?>
                                <?php $arSectionsExtending['PARAMETERS']['PAGE_ELEMENT_COUNT'] = count($arSectionsExtending['RESULT']['FILTER']['ID']); ?>
                                <?php if (!empty($arSectionsExtending['TITLE']) || Type::isNumeric($arSectionsExtending['TITLE'])) { ?>
                                    <div class="catalog-title">
                                        <?= $arSectionsExtending['TITLE'] ?>
                                    </div>
                                <?php } ?>
                                <?php $APPLICATION->IncludeComponent(
                                    'bitrix:catalog.section',
                                    $arSectionsExtending['TEMPLATE'],
                                    $arSectionsExtending['PARAMETERS'],
                                    $component
                                ) ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
                <?php if ($arResult['ADDITIONAL']['ARTICLES']['SHOW']) { ?>
                    <div class="catalog-additional">
                        <?php include(__DIR__.'/parts/articles.php') ?>
                    </div>
                <?php } ?>
                <?php if ($bSeo) { ?>
                    <?php $APPLICATION->IncludeComponent('intec.seo:iblocks.metadata.loader', '', [
                        'IBLOCK_ID' => $arIBlock['ID'],
                        'SECTION_ID' => $arSection['ID'],
                        'TYPE' => 'section',
                        'MODE' => 'single',
                        'METADATA_SET' => 'Y',
                        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                        'CACHE_TIME' => $arParams['CACHE_TIME']
                    ], $component) ?>
                    <?php $arArticlesExtending['RESULT'] = $APPLICATION->IncludeComponent('intec.seo:iblocks.articles.extend.filter', '', [
                        'IBLOCK_ID' => $arIBlock['ID'],
                        'SECTION_ID' => $arSection['ID'],
                        'ELEMENT_ID' => null,
                        'FILTER_MODE' => 'single',
                        'QUANTITY' => $arArticlesExtending['QUANTITY'],
                        'CURRENT_URL' => $_SERVER['REQUEST_URI'],
                        'FILTER_NAME' => 'arCatalogArticlesExtendingFilter',
                        'INCLUDE_SUBSECTIONS' => 'Y',
                        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                        'CACHE_TIME' => $arParams['CACHE_TIME']
                    ], $component) ?>
                    <?php if (!empty($arArticlesExtending['RESULT']) && !empty($arArticlesExtending['RESULT']['FILTER'])) { ?>
                        <?php
                            if ($arArticlesExtending['RESULT']['FILTER_MODE_SINGLE'])
                                $arArticlesExtending['PARAMETERS']['IBLOCK_ID'] = $arArticlesExtending['RESULT']['FILTER']['IBLOCK_ID'];

                            $arArticlesExtending['PARAMETERS']['SORT_ORDER1'] = 'ASC';
                            $arArticlesExtending['PARAMETERS']['NEWS_COUNT'] = count($arArticlesExtending['RESULT']['FILTER']['ID']);
                        ?>
                        <div class="catalog-section-extending">
                            <?php if (!empty($arArticlesExtending['TITLE']) || Type::isNumeric($arArticlesExtending['TITLE'])) { ?>
                                <div class="catalog-title">
                                    <?= $arArticlesExtending['TITLE'] ?>
                                </div>
                            <?php } ?>
                            <?php $APPLICATION->IncludeComponent(
                                'bitrix:news.list',
                                $arArticlesExtending['TEMPLATE'],
                                $arArticlesExtending['PARAMETERS'],
                                $component
                            ) ?>
                        </div>
                    <?php } ?>
                    <?php $arSeo = $APPLICATION->IncludeComponent('intec.seo:filter.meta', '', [
                        'IBLOCK_ID' => $arIBlock['ID'],
                        'SECTION_ID' => $arSection['ID'],
                        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                        'CACHE_TIME' => $arParams['CACHE_TIME']
                    ], $component) ?>
                    <?php if ($arTags['USE']) {
                        $this->SetViewTarget($sTemplateId.'-tags');

                        $APPLICATION->IncludeComponent('intec.seo:filter.tags', '',
                            $arTags['PARAMETERS'], $component);

                        $this->EndViewTarget();
                    } ?>
                <?php } ?>
                <?php $APPLICATION->ShowViewContent($sTemplateId.'-description-bottom') ?>
                <?php if (!empty($arSeo) && !empty($arSeo['META']['descriptionTop']) || $arDescription['SHOW'] && $arDescription['POSITION'] === 'top') { ?>
                    <?php $this->SetViewTarget($sTemplateId.'-description-top') ?>
                    <div class="<?= Html::cssClassFromArray([
                        'catalog-description',
                        'catalog-description-top',
                        'intec-ui-markup-text'
                    ]) ?>"><?= !empty($arSeo) && !empty($arSeo['META']['descriptionTop']) ? $arSeo['META']['descriptionTop'] : $arDescription['VALUE'] ?></div>
                    <?php $this->EndViewTarget() ?>
                <?php } ?>
                <?php if (!empty($arSeo) && !empty($arSeo['META']['descriptionBottom']) || $arDescription['SHOW'] && $arDescription['POSITION'] === 'bottom') { ?>
                    <?php $this->SetViewTarget($sTemplateId.'-description-bottom') ?>
                    <div class="<?= Html::cssClassFromArray([
                        'catalog-description',
                        'catalog-description-bottom',
                        'intec-ui-markup-text'
                    ]) ?>"><?= !empty($arSeo) && !empty($arSeo['META']['descriptionBottom']) ? $arSeo['META']['descriptionBottom'] : $arDescription['VALUE'] ?></div>
                    <?php $this->EndViewTarget() ?>
                <?php } ?>
                <?php if ($bIsAjax) exit() ?>
                <?php if ($arColumns['SHOW']) { ?>
                            <?php if ($arInterestProducts['SHOW'] && $arInterestProducts['POSITION'] == 'content') { ?>
                                <div class="catalog-section-products-interest-container">
                                    <div class="catalog-section-products-interest-block-title">
                                        <?= $arInterestProducts['TITLE'] ?>
                                    </div>
                                    <div class="catalog-section-products-interest-block-content">
                                        <?php $APPLICATION->IncludeComponent(
                                            'bitrix:catalog.section',
                                            'products.small.6',
                                            $arInterestProductsProperties,
                                            $component
                                        ) ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="intec-ui-clearfix"></div>
                <?php } ?>
                <?php if ($arTags['POSITION']['DESKTOP'] == 'bottom' && !$arTags['SHOWED']['DESKTOP']) { ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-menu-tags',
                            'desktop'
                        ],
                        'data' => [
                            'position' => 'top',
                            'mobile-use' => $arTags['SHOW']['MOBILE'] ? 'true' : 'false'
                        ]
                    ]) ?>
                        <?php $arTags['SHOWED']['DESKTOP'] = true ?>
                        <?php $APPLICATION->ShowViewContent($sTemplateId.'-tags') ?>
                    <?= Html::endTag('div') ?>
                <?php } elseif ($arTags['SHOW']['MOBILE'] && !$arTags['SHOWED']['MOBILE'] && $arTags['POSITION']['MOBILE'] == 'bottom') { ?>
                    <div class="catalog-menu-tags mobile">
                        <?php $arTags['SHOWED']['MOBILE'] = true ?>
                        <?php $APPLICATION->ShowViewContent($sTemplateId.'-tags-mobile') ?>
                    </div>
                <?php } ?>
                <?php if ($arInterestProducts['SHOW'] && $arInterestProducts['POSITION'] == 'footer') { ?>
                    <div class="catalog-section-products-interest-container">
                        <div class="catalog-section-products-interest-block-title">
                            <?= $arInterestProducts['TITLE'] ?>
                        </div>
                        <div class="catalog-section-products-interest-block-content">
                            <?php $APPLICATION->IncludeComponent(
                                'bitrix:catalog.section',
                                'products.small.6',
                                $arInterestProductsProperties,
                                $component
                            ) ?>
                        </div>
                    </div>
                <?php } ?>
            <?= Html::endTag('div') ?>
        </div>
    </div>
    <script type="text/javascript">
        template.load(function (data) {
            var app = this;
            var $ = this.getLibrary('$');

            var root = data.nodes;
            var filter = $('[data-role="catalog.filter"]', root);
            var content = $('[data-role="catalog.content"]', root);

            filter.state = false;
            filter.button = $('[data-role="catalog.filter.button"]', root);
            filter.button.on('click', function () {
                if (filter.state) {
                    filter.hide();
                } else {
                    filter.show();
                }

                filter.state = !filter.state;
            });

            content.refresh = function (url) {
                var panel = $('[data-role="catalog.panel"]', content);
                var preloader = $('[data-role="catalog.preloader"]', content);

                if (url == null)
                    url = null;

                $.ajax({
                    'url': url,
                    'data': {
                        'catalog': {
                            'ajax': 'Y'
                        }
                    },
                    'cache': false,
                    'beforeSend': function() {
                        preloader.attr('data-active', 'true');
                    },
                    'success': function (response) {
                        preloader.attr('data-active', 'false');
                        panel.detach();
                        filter.detach();
                        preloader.detach();
                        content.html(response);
                        content.find('[data-role="preloader"]').replaceWith(preloader);
                        content.find('[data-role="catalog.panel"]').replaceWith(panel);
                        content.find('[data-role="catalog.filter"]').replaceWith(filter);
                        app.api.basket.update();
                    }
                });
            };


            <?php
            /*Если у каталога включен AJAX, то обновление фильтра происходит через
             * параметр INSTANT_RELOAD в компоненте фильтра*/

             if ($arFilter['SHOW'] && $arFilter['AJAX'] && $arParams['AJAX_MODE']!='Y') { ?>
                var updater = function (url) {
                    if (window.history.enabled || window.history.pushState != null) {
                        window.history.pushState(null, document.title, url);
                    } else {
                        window.location.href = url;
                    }
                    content.refresh(url);
                };

                if (smartFilter && smartFilter.on)
                    smartFilter.on('refresh', updater);

            <?php } ?>
        }, {
            'name': '[Component] bitrix:catalog (catalog.1)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?= Html::endTag('div') ?>