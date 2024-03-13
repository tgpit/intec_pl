<? if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use intec\Core;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;
use intec\core\helpers\StringHelper;
use intec\core\net\Url;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

$this->setFrameMode(true);

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;
else if (Loader::includeModule('intec.startshop'))
    $bLite = true;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arSections = $arParams['SECTIONS'];
$arElements = $arParams['ELEMENTS'];
$arElements['PARAMETERS']['INCLUDE_SUBSECTIONS'] = "Y";
$arElements['PARAMETERS']['SHOW_ALL_WO_SECTION'] = "N";
$arElements['PARAMETERS']['COMPATIBLE_MODE'] = "Y";
$arElements['SHOW'] = true;

if (empty($arElements['TEMPLATE']))
    $arElements['SHOW'] = false;

if ($arElements['SHOW']) {
    $sPrefix = 'LIST_';

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        if ($sKey === 'TEMPLATE')
            continue;

        if (
            StringHelper::startsWith($sKey, 'SECTION_TIMER_')
        ) continue;

        $arElements['PARAMETERS'][$sKey] = $mValue;
    }
}

$arElements['PARAMETERS'] = ArrayHelper::merge([
    'FILTER_NAME' => 'arrFilter'
], $arElements['PARAMETERS']);

$arFilter = $arParams['FILTER'];

?>
<div id="<?= $sTemplateId ?>" class="catalog-search">
    <?php

    $oRequest = Core::$app->request;

    if ((($oRequest->getIsAjax() || isset($_SERVER['HTTP_BX_AJAX'])) && $oRequest->get('ajax') === 'y')) {

       if ($sQuery = $oRequest->get('q')) {
           $sQuery = Encoding::convert($sQuery, null, Encoding::UTF8);

           $_REQUEST['q'] = $sQuery;
       }

    }

    $arSearchPageParameters = [
        'RESTART' => $arParams['RESTART'],
        'NO_WORD_LOGIC' => $arParams['NO_WORD_LOGIC'],
        'USE_LANGUAGE_GUESS' => $arParams['USE_LANGUAGE_GUESS'],
        'CHECK_DATES' => $arParams['CHECK_DATES'],
        'USE_TITLE_RANK' => 'N',
        'DEFAULT_SORT' => 'rank',
        'FILTER_NAME' => 'searchPageFilter',
        'SHOW_WHERE' => 'N',
        'arrWHERE' => [],
        'SHOW_WHEN' => 'N',
        'PAGE_RESULT_COUNT' => 999,
        'DISPLAY_TOP_PAGER' => 'N',
        'DISPLAY_BOTTOM_PAGER' => 'N',
        'PAGER_TITLE' => '',
        'PAGER_SHOW_ALWAYS' => 'N',
        'PAGER_TEMPLATE' => 'N',
    ];
    $arSearchPageParameters['arrFILTER'] = [];
    $arSearchPageParameters['arrFILTER'][] = 'iblock_'.$arParams['IBLOCK_TYPE'];
    $arSearchPageParameters['arrFILTER_iblock_'.$arParams['IBLOCK_TYPE']] = [$arParams['IBLOCK_ID']];

    if ($bBase) {
        $arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
    }

    if ($bLite) {
        $arSKU = ArrayHelper::getFirstValue(Arrays::fromDBResult(CStartShopCatalog::GetByIBlock($arParams['IBLOCK_ID']))->asArray());
        $arSKU['IBLOCK_ID'] = $arSKU['OFFERS_IBLOCK'];
        $sOffresProperty = $arSKU['OFFERS_LINK_PROPERTY'];
    }

    if (!empty($arSKU['IBLOCK_ID'])) {
        $arSKU = ArrayHelper::getFirstValue(Arrays::fromDBResult(CIBlock::GetByID($arSKU['IBLOCK_ID']))->asArray());

        if (array_search('iblock_'.$arSKU['IBLOCK_TYPE_ID'], $arSearchPageParameters['arrFILTER']) !== false) {
            $arSearchPageParameters['arrFILTER_iblock_'.$arParams['IBLOCK_TYPE']][] = $arSKU['ID'];
        } else {
            $arSearchPageParameters['arrFILTER'][] = 'iblock_'.$arSKU['IBLOCK_TYPE_ID'];
            $arSearchPageParameters['arrFILTER_iblock_'.$arSKU['IBLOCK_TYPE_ID']] = [$arSKU['ID']];
        }
    }

    $GLOBALS['searchPageFilter']['!ITEM_ID'] = 'S%'; //exclude sections from search results

    ?>
    <?php $this->SetViewTarget('component_search');?>
        <?php $arElements['ID'] = $APPLICATION->IncludeComponent(
            'bitrix:search.page',
            'catalog',
            $arSearchPageParameters,
            $component,
            ['HIDE_ICONS' => 'N']
        ) ?>
    <?php $this->EndViewTarget();
	if (!Type::isArray($arElements['ID']))
		$arElements['ID'] = [];

	$arRubrics = [];

    if (!empty($arSKU['ID'])) {
        if ($bBase) {
            $arOffers = CCatalogSKU::getProductList($arElements['ID']);
            $sColumnKey = 'ID';
        }

        if ($bLite) {
            $arOffers = Arrays::fromDBResult(CIBlockElement::GetList(
                ['SORT' => 'ASC'],
                [
                    'IBLOCK_ID' => $arSKU['ID'],
                    'ID' => $arElements['ID']
                ],
                false,
                false,
                ['ID', 'PROPERTY_'.$sOffresProperty]
            ))
            ->indexBy('ID')
            ->asArray();
            $sColumnKey = 'PROPERTY_'.$sOffresProperty.'_VALUE';

            unset($sOffresProperty);
        }

        $arElements['ID'] = array_unique(array_merge(array_diff($arElements['ID'], ArrayHelper::getKeys($arOffers)), array_column(Type::isArray($arOffers) ? $arOffers : [], $sColumnKey)));

        unset($arOffers, $sColumnKey);
    }

    unset($arSKU);

    $arSectionsFound = Arrays::fromDBResult(CIBlockElement::GetList([], [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'ID' => $arElements['ID']
    ]))->sortBy(Type::toInteger('IBLOCK_SECTION_ID'))->asArray(function ($key, $value) use (&$arRubrics) {
        if (!ArrayHelper::keyExists($value['IBLOCK_SECTION_ID'], $arRubrics)) {
            $arRubrics[$value['IBLOCK_SECTION_ID']] = [
                'ID' => $value['IBLOCK_SECTION_ID'],
                'NAME' => null,
                'SORT' => null,
                'COUNT' => 1
            ];
        } else {
            $arRubrics[$value['IBLOCK_SECTION_ID']]['COUNT']++;
        }

        return [
            'key' => $value['IBLOCK_SECTION_ID'],
            'value' => $value['IBLOCK_SECTION_ID']
        ];
    });

    $arSectionsFound = Arrays::fromDBResult(CIBlockSection::GetList([], [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'ID' => $arSectionsFound
    ]))->asArray(function ($key, $value) use (&$arRubrics) {
        if (ArrayHelper::keyExists($value['ID'], $arRubrics)) {
            $arRubrics[$value['ID']]['NAME'] = $value['NAME'];
            $arRubrics[$value['ID']]['SORT'] = $value['SORT'];
        }

        return ['skip' => true];
    });

    unset($arSectionsFound);

	if (empty($arElements['ID']))
		$arElements['SHOW'] = false;

    $GLOBALS['smartPreFilter']['=ID'] = $arElements['ID'];

    if (!empty($_REQUEST['section']) && Type::isNumeric($_REQUEST['section']))
        $GLOBALS['smartPreFilter']['=SECTION_ID'] = $_REQUEST['section'];

    $arColumns = [
        'SHOW' => ($arFilter['SHOW'] && $arFilter['TYPE'] === 'vertical')
    ];

    $sFilterName = $arElements['PARAMETERS']['FILTER_NAME'];

    if ($arElements['SHOW']) {

        if ($arFilter['SHOW']) { ?>
            <div id="searchFilterMobile" style="display: none;">
                <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.smart.filter',
                        'mobile.1',
                        $arFilter['PARAMETERS'],
                        $component
                    );
                ?>
            </div>
        <?php }

        if ($arFilter['SHOW'] && $arFilter['TYPE'] === 'horizontal') { ?>
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
                'role' => !$arColumns['SHOW'] ? 'content' : null
            ]
        ]) ?>
            <?php $APPLICATION->ShowViewContent('component_search') ?>
            <?php if ($arColumns['SHOW']) { ?>
                <div class="catalog-content-left intec-content-left">
                    <?php if ($arSections['SHOW']) {
                        $arSections['PARAMETERS']['ELEMENTS_ID'] = $arElements['ID'];
                        $arSections['PARAMETERS']['DEPTH'] = '3';

                        $arSections['RESULT'] = $APPLICATION->IncludeComponent(
                            "intec.universe:search.sections",
                            $arSections['TEMPLATE'],
                            $arSections['PARAMETERS'],
                            $component
                        );

                        if (!empty($arSections['RESULT']))
                            foreach ($arSections['RESULT'] as &$arSection)
                                if ($arSection['CURRENT'] === 'Y') {
                                    $GLOBALS[$sFilterName]['IBLOCK_SECTION_ID'] = $arSection['ID'];
                                    break;
                                }
                    } ?>
                    <?php if (count($arRubrics) > 0) { ?>
                        <div class="catalog-search-rubrics-container" data-role="rubrics">
                            <div class="catalog-search-rubrics-title">
                                <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-4">
                                    <div class="intec-grid-item-auto">
                                        <div class="catalog-search-rubrics-title-icon intec-ui-picture">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.16667 11.6666C3.24583 11.6666 2.5 10.9208 2.5 9.99998C2.5 9.07915 3.24583 8.33331 4.16667 8.33331C5.0875 8.33331 5.83333 9.07915 5.83333 9.99998C5.83333 10.9208 5.0875 11.6666 4.16667 11.6666Z" stroke="#B0B0B0" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M9.99998 11.6666C9.07915 11.6666 8.33331 10.9208 8.33331 9.99998C8.33331 9.07915 9.07915 8.33331 9.99998 8.33331C10.9208 8.33331 11.6666 9.07915 11.6666 9.99998C11.6666 10.9208 10.9208 11.6666 9.99998 11.6666Z" stroke="#B0B0B0" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M15.8334 11.6666C14.9125 11.6666 14.1667 10.9208 14.1667 9.99998C14.1667 9.07915 14.9125 8.33331 15.8334 8.33331C16.7542 8.33331 17.5 9.07915 17.5 9.99998C17.5 10.9208 16.7542 11.6666 15.8334 11.6666Z" stroke="#B0B0B0" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M4.16667 17.5C3.24583 17.5 2.5 16.7542 2.5 15.8334C2.5 14.9125 3.24583 14.1667 4.16667 14.1667C5.0875 14.1667 5.83333 14.9125 5.83333 15.8334C5.83333 16.7542 5.0875 17.5 4.16667 17.5Z" stroke="#B0B0B0" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M9.99998 17.5C9.07915 17.5 8.33331 16.7542 8.33331 15.8334C8.33331 14.9125 9.07915 14.1667 9.99998 14.1667C10.9208 14.1667 11.6666 14.9125 11.6666 15.8334C11.6666 16.7542 10.9208 17.5 9.99998 17.5Z" stroke="#B0B0B0" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M15.8334 17.5C14.9125 17.5 14.1667 16.7542 14.1667 15.8334C14.1667 14.9125 14.9125 14.1667 15.8334 14.1667C16.7542 14.1667 17.5 14.9125 17.5 15.8334C17.5 16.7542 16.7542 17.5 15.8334 17.5Z" stroke="#B0B0B0" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M4.16667 5.83333C3.24583 5.83333 2.5 5.0875 2.5 4.16667C2.5 3.24583 3.24583 2.5 4.16667 2.5C5.0875 2.5 5.83333 3.24583 5.83333 4.16667C5.83333 5.0875 5.0875 5.83333 4.16667 5.83333Z" stroke="#B0B0B0" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M9.99998 5.83333C9.07915 5.83333 8.33331 5.0875 8.33331 4.16667C8.33331 3.24583 9.07915 2.5 9.99998 2.5C10.9208 2.5 11.6666 3.24583 11.6666 4.16667C11.6666 5.0875 10.9208 5.83333 9.99998 5.83333Z" stroke="#B0B0B0" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M15.8334 5.83333C14.9125 5.83333 14.1667 5.0875 14.1667 4.16667C14.1667 3.24583 14.9125 2.5 15.8334 2.5C16.7542 2.5 17.5 3.24583 17.5 4.16667C17.5 5.0875 16.7542 5.83333 15.8334 5.83333Z" stroke="#B0B0B0" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="intec-grid-item">
                                        <div class="catalog-search-rubrics-title-content">
                                            <?= Loc::getMessage('C_CATALOG_SEARCH_DEFAULT_RUBRICS_TITLE') ?>
                                        </div>
                                    </div>
                                    <div class="intec-grid-item-auto">
                                        <div class="catalog-search-rubrics-title-indicator intec-cl-background-hover intec-ui-picture" data-role="rubrics.switch">
                                            <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.75 6.875L5 3.125L1.25 6.875" stroke="#808080" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="catalog-search-rubrics" data-role="rubrics.content">
                                <div class="catalog-search-rubrics-content">
                                    <?php if (count($arRubrics) > 1) {
                                        $arQuery = $oRequest->getQueryParams();

                                        $arQueryReset = $arQuery;

                                        ArrayHelper::unsetValue($arQueryReset, 'section');

                                    ?>
                                        <?= Html::beginTag('a', [
                                            'class' => 'catalog-search-rubric',
                                            'href' => $APPLICATION->GetCurPage(false).'?'.Url::buildQueryString($arQueryReset),
                                            'data-active' => empty($_REQUEST['section']) ? 'true' : 'false'
                                        ]) ?>
                                            <div class="catalog-search-rubric-content intec-grid intec-grid-i-h-8 intec-grid-a-v-center intec-grid-a-h-between">
                                                <div class="catalog-search-rubric-name intec-grid-item">
                                                    <?= Loc::getMessage('C_CATALOG_SEARCH_DEFAULT_RUBRICS_RESET') ?>
                                                </div>
                                            </div>
                                        <?= Html::endTag('a') ?>
                                        <?php foreach ($arRubrics as $arRubric) {

                                            $arQuery['section'] = $arRubric['ID'];

                                        ?>
                                            <?= Html::beginTag('a', [
                                                'class' => 'catalog-search-rubric',
                                                'href' => $APPLICATION->GetCurPage(false).'?'.Url::buildQueryString($arQuery),
                                                'data-active' => !empty($_REQUEST['section']) && $_REQUEST['section'] === $arRubric['ID'] ? 'true' : 'false'
                                            ]) ?>
                                                <span class="catalog-search-rubric-content intec-grid intec-grid-i-h-8 intec-grid-a-v-center intec-grid-a-h-between">
                                                    <span class="catalog-search-rubric-name intec-grid-item">
                                                        <?= $arRubric['NAME'] ?>
                                                    </span>
                                                    <span class="catalog-search-rubric-count intec-grid-item-auto">
                                                        <?= $arRubric['COUNT'] ?>
                                                    </span>
                                                </span>
                                            <?= Html::endTag('a') ?>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <?php foreach ($arRubrics as $arRubric) { ?>
                                            <div class="catalog-search-rubric" data-active="true">
                                                <span class="catalog-search-rubric-content intec-grid intec-grid-i-h-8 intec-grid-a-v-center intec-grid-a-h-between">
                                                    <span class="catalog-search-rubric-name intec-grid-item">
                                                        <?= $arRubric['NAME'] ?>
                                                    </span>
                                                    <span class="catalog-search-rubric-count intec-grid-item-auto">
                                                        <?= $arRubric['COUNT'] ?>
                                                    </span>
                                                </span>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($arFilter['SHOW']) {
                        $APPLICATION->IncludeComponent(
                            'bitrix:catalog.smart.filter',
                            $arFilter['TEMPLATE'],
                            $arFilter['PARAMETERS'],
                            $component
                        );
                    } ?>
                </div>
                <div class="catalog-content-right intec-content-right">
            <?php } ?>
                <div class="catalog-content-right-wrapper intec-content-right-wrapper" data-role="content">
                    <?php $APPLICATION->ShowViewContent('panel_sort_search');

                    if ($arFilter['SHOW']) { ?>
                        <div class="catalog-filter-mobile" data-role="filter">
                            <?php $APPLICATION->IncludeComponent(
                                'bitrix:catalog.smart.filter',
                                'vertical.1',
                                ArrayHelper::merge($arFilter['PARAMETERS'], [
                                    'MOBILE' => 'Y'
                                ]),
                                $component
                            ) ?>
                        </div>
                    <?php } ?>
                    <?php $GLOBALS[$sFilterName]['ID'] = $arElements['ID']; ?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.section',
                        $arElements['TEMPLATE'],
                        $arElements['PARAMETERS'],
                        $component,
                        ['HIDE_ICONS' => 'Y']
                    ) ?>
            <?php if ($arColumns['SHOW']) { ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            <?php } ?>
        <?= Html::endTag('div') ?>
    <?php } else { ?>
        <?php $APPLICATION->ShowViewContent('component_search') ?>
        <?php if ($oRequest->get('q') == '') { ?>
            <div class="catalog-search-message intec-ui intec-ui-control-alert intec-ui-scheme-blue">
                <?= Loc::getMessage('C_CATALOG_SEARCH_DEFAULT_EMPTY') ?>
            </div>
        <?php } else { ?>
            <div class="catalog-search-message intec-ui intec-ui-control-alert intec-ui-scheme-red">
                <?= Loc::getMessage('C_CATALOG_SEARCH_DEFAULT_NOT_FOUND') ?>
            </div>
        <?php } ?>
        <?php if ($arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS']['SHOW']) { ?>
            <?php include(__DIR__.'/parts/elements.php') ?>
        <?php } ?>
    <?php } ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var _ = this.getLibrary('_');
            var root = data.nodes;
            var filter = $('[data-role="filter"]', root);
            var rubrics = $('[data-role="rubrics"]', root);

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

            if (rubrics.length !== 0) {
                rubrics.switch = $('[data-role="rubrics.switch"]', rubrics);
                rubrics.content = $('[data-role="rubrics.content"]', rubrics);

                rubrics.switch.attr('data-expanded', 'true');

                rubrics.switch.on('click', _.debounce(function () {
                    var self = $(this);
                    var expanded = self.attr('data-expanded') === 'true';

                    if (expanded) {
                        rubrics.switch.attr('data-expanded', 'false');
                        rubrics.content.animate({'height': 0}, 500, function () {
                            rubrics.content.css({
                                'display': 'none',
                                'height': ''
                            });
                        });
                    } else {
                        var height;

                        rubrics.switch.attr('data-expanded', 'true');
                        rubrics.content.css('display', '');
                        height = rubrics.content.outerHeight();
                        rubrics.content.css('height', 0);

                        rubrics.content.animate({'height': height}, 500, function () {
                            rubrics.content.css('height', '');
                        });
                    }
                }, 500, {
                    'leading': true,
                    'trailing': false
                }));
            }

            /*Переносим мобильный фильтр*/
            //catalog.panel.filterMobile
            let mobileFilter = $('#searchFilterMobile > div');
            let mobileFilterPanel = $('#panelFilterMobile');

            if (mobileFilter.length && mobileFilterPanel.length) {
                mobileFilterPanel.append(mobileFilter);
            }
        }, {
            'name': '[Component] bitrix:catalog (catalog.1) > bitrix:catalog.search (.default)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
</div>