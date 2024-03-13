<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arSection
 * @var array $arSort
 * @var array $arViews
 * @var array $arViewsMobile
 * @var array $arFilter
 * @var array $arElements
 * @var bool $bIsAjax
 */

?>
<!--noindex-->
<div class="catalog-panel" data-role="catalog.panel">
    <?php if (!$bIsAjax) { ?>
        <div class="catalog-panel-wrapper" data-device="desktop">
            <div class="catalog-panel-views" data-role="catalog.panel.views">
                <div class="catalog-panel-views-items">
                    <?php foreach($arViews as $sView => $arView) { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-panel-views-item',
                                'intec-grid-item-auto'
                            ],
                            'data' => [
                                'active' => $arView['ACTIVE'] ? 'true' : 'false',
                                'role' => 'catalog.panel.viewSelection',
                                'value' => $arView['VALUE']
                            ]
                        ]) ?>
                            <i class="<?= $arView['ICON'] ?>"></i>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                </div>
            </div>
            <div class="catalog-panel-sort" data-role="catalog.panel.sort">
                <div class="catalog-panel-sort-wrapper" data-role="catalog.panel.sortButton">
                    <div class="catalog-panel-sort-icon">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3.18176 14.0909L6.45449 17.3636L9.72722 14.0909" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.45454 4.63637L6.45455 17.3636" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18.8182 7.90909L15.5454 4.63636L12.2727 7.90909" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15.5454 17.3636L15.5454 4.63637" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="catalog-panel-sort-text">
                        <?php
                            $arSortProperty = null;

                            foreach ($arSort['PROPERTIES'] as $sSortPropertyKey => $arSortProperty) {
                                if ($arSortProperty['ACTIVE'])
                                    break;

                                $arSortProperty = null;
                            }

                            if (!empty($arSortProperty)) {
                                echo $arSortProperty['NAME'];
                            } else {
                                echo Loc::getMessage('C_CATALOG_CATALOG_1_SORT');
                            }

                            unset($sSortPropertyKey, $arSortProperty);
                        ?>
                    </div>
                    <div class="catalog-panel-sort-items" data-role="catalog.panel.sortItems">
                        <div class="catalog-panel-sort-items-wrapper">
                            <?php foreach ($arSort['PROPERTIES'] as $sSortPropertyKey => $arSortProperty) { ?>
                                <?= Html::beginTag('div', [
                                    'class' => 'catalog-panel-sort-item',
                                    'data' => [
                                        'active' => $arSortProperty['ACTIVE'] ? 'true' : 'false',
                                        'order' => $arSortProperty['ORDER'],
                                        'role' => 'catalog.panel.sortSelection',
                                        'value' => $arSortProperty['VALUE']
                                    ]
                                ]) ?>
                                    <div class="catalog-panel-sort-item-wrapper <?=  $arSortProperty['ACTIVE'] ? 'intec-cl-text' : 'intec-cl-text-hover' ?>">
                                        <div class="catalog-panel-sort-item-name">
                                            <?= $arSortProperty['NAME'] ?>
                                        </div>
                                        <?php if (!empty($arSortProperty['DESCRIPTION']) && $arSortProperty['FIELD'] === 'name') { ?>
                                            <div class="catalog-panel-sort-item-description">
                                                <?= $arSortProperty['DESCRIPTION'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="catalog-panel-wrapper" data-device="mobile">
            <div class="catalog-panel-popups">
                <div class="catalog-panel-popup" data-role="catalog.panel.viewMobilePopup">
                    <div class="catalog-panel-popup-overlay" data-type="overlay"></div>
                    <div class="catalog-panel-popup-window" data-type="window">
                        <div class="catalog-panel-popup-window-wrapper">
                            <div class="catalog-panel-popup-window-header">
                                <div class="catalog-panel-popup-window-title">
                                    <?= Loc::getMessage('C_CATALOG_CATALOG_1_VIEW_HEADER') ?>
                                </div>
                                <div class="catalog-panel-popup-window-close" data-type="button" data-action="close">
                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 1L9 9" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M9 1L1 9" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="catalog-panel-popup-window-content">
                                <form>
                                    <div class="catalog-panel-popup-selections">
                                        <?php foreach ($arViews as $sView => $arView) { ?>
                                        <?php
                                            $sIcon = FileHelper::getFileData(__DIR__.'/../images/views.'.$arView['VALUE'].'.svg');
                                        ?>
                                            <?= Html::beginTag('div', [
                                                'class' => 'catalog-panel-popup-selection',
                                                'data' => [
                                                    'active' => $arView['ACTIVE'] ? 'true' : 'false',
                                                    'role' => 'catalog.panel.viewSelection',
                                                    'value' => $arView['VALUE']
                                                ]
                                            ]) ?>
                                                <label class="catalog-panel-popup-selection-control intec-ui intec-ui-control-radiobox intec-ui-scheme-current intec-ui-size-2">
                                                    <?= Html::radio('selection', $arView['ACTIVE']) ?>
                                                    <span class="catalog-panel-popup-selection-selector intec-ui-part-selector"></span>
                                                    <span class="catalog-panel-popup-selection-content intec-ui-part-content">
                                                        <span class="catalog-panel-popup-selection-title">
                                                            <span class="catalog-panel-popup-selection-icon">
                                                                <?= $sIcon ?>
                                                            </span>
                                                            <span class="catalog-panel-popup-selection-text">
                                                                <?= $arView['NAME'] ?>
                                                            </span>
                                                        </span>
                                                    </span>
                                                </label>
                                            <?= Html::endTag('div') ?>
                                        <?php } ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="catalog-panel-popup" data-role="catalog.panel.sortMobilePopup">
                    <div class="catalog-panel-popup-overlay" data-type="overlay"></div>
                    <div class="catalog-panel-popup-window" data-type="window" style="height: 65%">
                        <div class="intec-grid intec-grid-o-vertical catalog-panel-popup-window-wrapper" style="height: 100%">
                            <div class="intec-grid-item-auto catalog-panel-popup-window-header">
                                <div class="catalog-panel-popup-window-title">
                                    <?= Loc::getMessage('C_CATALOG_CATALOG_1_SORT_HEADER') ?>
                                </div>
                                <div class="catalog-panel-popup-window-close" data-type="button" data-action="close">
                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 1L9 9" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M9 1L1 9" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="intec-grid-item catalog-panel-popup-window-content" style="overflow-y: scroll; overflow-x: hidden">
                                <form>
                                    <div class="catalog-panel-popup-selections">
                                        <?php foreach ($arSort['PROPERTIES'] as $arSortProperty) { ?>
                                            <?= Html::beginTag('div', [
                                                'class' => 'catalog-panel-popup-selection',
                                                'data' => [
                                                    'active' => $arSortProperty['ACTIVE'] ? 'true' : 'false',
                                                    'order' => $arSortProperty['ORDER'],
                                                    'role' => 'catalog.panel.sortSelection',
                                                    'value' => $arSortProperty['VALUE']
                                                ]
                                            ]) ?>
                                                <label class="catalog-panel-popup-selection-control intec-ui intec-ui-control-radiobox intec-ui-scheme-current intec-ui-size-2">
                                                    <?= Html::radio('selection', $arSortProperty['ACTIVE']) ?>
                                                    <span class="catalog-panel-popup-selection-selector intec-ui-part-selector"></span>
                                                    <span class="catalog-panel-popup-selection-content intec-ui-part-content">
                                                        <span class="catalog-panel-popup-selection-title">
                                                            <span class="catalog-panel-popup-selection-text">
                                                                <?= $arSortProperty['NAME'] ?>
                                                            </span>
                                                        </span>
                                                        <?php if (!empty($arSortProperty['DESCRIPTION'])) { ?>
                                                            <span class="catalog-panel-popup-selection-description">
                                                                <?= $arSortProperty['DESCRIPTION'] ?>
                                                            </span>
                                                        <?php } ?>
                                                    </span>
                                                </label>
                                            <?= Html::endTag('div') ?>
                                        <?php } ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="catalog-panel-popup" data-role="catalog.panel.filterMobilePopup">
                    <div class="catalog-panel-popup-overlay" data-type="overlay"></div>
                    <div class="catalog-panel-popup-filter" data-type="window">
                        <div class="catalog-panel-popup-filter-close catalog-panel-popup-window-close" data-type="button" data-action="close">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 8L16 16" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16 8L8 16" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div id="panelFilterMobile" class="catalog-panel-popup-filter-content" data-role="catalog.panel.filterMobile">
                            <?php if (!isset($hideMobileFilter) || !$hideMobileFilter) { //скрываем фильтр для страницы поиска?>
                                <?php $APPLICATION->IncludeComponent(
                                    'bitrix:catalog.smart.filter',
                                    'mobile.1',
                                    $arFilter['PARAMETERS'],
                                    $component
                                ) ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="catalog-panel-views" data-role="catalog.panel.views">
                <div class="catalog-panel-views-button" data-role="catalog.panel.viewsButton">
                    <?php foreach ($arViews as $arView) { ?>
                    <?php
                        if (!$arView['ACTIVE'])
                            continue;

                        $sIcon = FileHelper::getFileData(__DIR__.'/../images/views.'.$arView['VALUE'].'.svg');
                    ?>
                        <?= $sIcon ?>
                    <?php break ?>
                    <?php } ?>
                </div>
            </div>
            <div class="catalog-panel-sort" data-role="catalog.panel.sort">
                <div class="catalog-panel-sort-wrapper" data-role="catalog.panel.sortButton">
                    <div class="catalog-panel-sort-icon">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3.18176 14.0909L6.45449 17.3636L9.72722 14.0909" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.45454 4.63637L6.45455 17.3636" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18.8182 7.90909L15.5454 4.63636L12.2727 7.90909" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15.5454 17.3636L15.5454 4.63637" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="catalog-panel-sort-text">
                        <?php
                            $arSortProperty = null;

                            foreach ($arSort['PROPERTIES'] as $sSortPropertyKey => $arSortProperty) {
                                if ($arSortProperty['ACTIVE'])
                                    break;

                                $arSortProperty = null;
                            }

                            if (!empty($arSortProperty)) {
                                echo $arSortProperty['NAME'];
                            } else {
                                echo Loc::getMessage('C_CATALOG_CATALOG_1_SORT');
                            }

                            unset($sSortPropertyKey, $arSortProperty);
                        ?>
                    </div>
                </div>
            </div>
            <div class="catalog-panel-filter" data-role="catalog.panel.filter">
                <?php if ($arFilter['SHOW'] || $searchFilterShow) { ?>
                    <div class="catalog-panel-filter-button" data-role="catalog.panel.filter.button">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 13.333L3.36 7.8C3.132 7.61 3 7.328 3 7.032V4.5C3 3.948 3.448 3.5 4 3.5H20C20.552 3.5 21 3.948 21 4.5V7.032C21 7.329 20.868 7.61 20.64 7.8L14 13.333V17.882C14 18.261 13.786 18.607 13.447 18.776L10.723 20.138C10.391 20.304 10 20.063 10 19.691V13.333V13.333Z" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3.12012 7.5H20.8801" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                <?php } ?>
            </div>
        </div>
        <script type="text/javascript">
            template.load(function (data) {
                var app = this;
                var _ = app.getLibrary('_');
                var $ = app.getLibrary('$');
                var url = app.getLibrary('url');

                var elements = {};
                var sortOrder = <?= JavaScript::toObject($arSort['ORDER']) ?>;

                var setView = function (view) {
                    var link = url.current();

                    link.query['view'] = view;

                    url.go(link);
                };

                var setSorting = function (sort, order) {
                    var link = url.current();

                    link.query = _.omit(link.query, ['order']);
                    link.query['sort'] = sort;

                    if (!_.isNil(order))
                        link.query['order'] = order;

                    sortOrder = order;

                    url.go(link);
                };

                var popupOptions = {
                    'animation': {
                        'duration': 350,
                        'handlers': {
                            'init': null,
                            'open': function () {
                                var self = this;

                                return new Promise(function (resolve) {
                                    self.nodes.overlay.stop().css({
                                        'opacity': 0
                                    }).animate({
                                        'opacity': 1
                                    }, {
                                        'duration': self.options.animation.duration
                                    });

                                    self.nodes.window.stop().css({
                                        'margin-bottom': -self.nodes.window.outerHeight()
                                    }).animate({
                                        'margin-bottom': 0
                                    }, {
                                        'duration': self.options.animation.duration,
                                        'always': function () {
                                            resolve()
                                        }
                                    });
                                });
                            },
                            'close': function () {
                                var self = this;

                                return new Promise(function (resolve) {
                                    self.nodes.overlay.stop().css({
                                        'opacity': 1
                                    }).animate({
                                        'opacity': 0
                                    }, {
                                        'duration': self.options.animation.duration
                                    });

                                    self.nodes.window.stop().css({
                                        'margin-bottom': 0
                                    }).animate({
                                        'margin-bottom': -self.nodes.window.outerHeight()
                                    }, {
                                        'duration': self.options.animation.duration,
                                        'always': function () {
                                            resolve()
                                        }
                                    });
                                });
                            }
                        }
                    }
                };

                var viewMobilePopup;
                var sortMobilePopup;
                var sortPopupState = false;
                var filterMobilePopup;

                elements.root = data.nodes;
                elements.panel = $('[data-role="catalog.panel"]', elements.root);
                elements.viewSelection = $('[data-role="catalog.panel.viewSelection"]', elements.root);
                elements.views = $('[data-role="catalog.panel.views"]', elements.root);
                elements.viewsButton = $('[data-role="catalog.panel.viewsButton"]', elements.views);
                elements.sort = $('[data-role="catalog.panel.sort"]', elements.root);
                elements.sortButton = $('[data-role="catalog.panel.sortButton"]', elements.sort);
                elements.sortItems = $('[data-role="catalog.panel.sortItems"]', elements.sort);
                elements.sortSelection = $('[data-role="catalog.panel.sortSelection"]', elements.root);
                elements.filter = $('[data-role="catalog.panel.filter"]', elements.root);
                elements.filterButton = $('[data-role="catalog.panel.filter.button"]', elements.filter);

                elements.viewsButton.on('click', function () {
                    viewMobilePopup.open();
                });

                elements.sortButton.on('click', function (event) {
                    var self = $(this);
                    var target = $(event.target);

                    if (target.isOrClosest(elements.sortItems))
                        return;

                    if (self.closest('[data-device="desktop"]').length > 0) {
                        sortPopupState = !sortPopupState;

                        if (sortPopupState) {
                            elements.sortItems.show();
                        } else {
                            elements.sortItems.hide();
                        }
                    } else {
                        sortMobilePopup.open();
                    }
                });

                elements.filterButton.on('click', function () {
                    filterMobilePopup.open();
                });

                elements.viewSelection.on('click', function () {
                    if (event.target.tagName !== 'INPUT') {
                        if (this.getAttribute('data-active') === 'true')
                            return;

                        setView(this.getAttribute('data-value'));
                        viewMobilePopup.close();
                    }
                });

                elements.sortSelection.on('click', function (event) {
                    var active;

                    if (event.target.tagName !== 'INPUT') {
                        active = this.getAttribute('data-active') === 'true';

                        setSorting(this.getAttribute('data-value'), !active ? this.getAttribute('data-order') : null);
                        sortMobilePopup.close();
                    }
                });

                viewMobilePopup = $('[data-role="catalog.panel.viewMobilePopup"]', elements.panel).uiControl('popup', popupOptions)[0];
                sortMobilePopup = $('[data-role="catalog.panel.sortMobilePopup"]', elements.panel).uiControl('popup', popupOptions)[0];
                filterMobilePopup = $('[data-role="catalog.panel.filterMobilePopup"]', elements.panel).uiControl('popup', popupOptions)[0];

                viewMobilePopup.on('beforeOpen', function () {
                    app.api.emit('popup.beforeOpen', viewMobilePopup);
                }).on('open', function () {
                    app.api.emit('popup.open', viewMobilePopup);
                }).on('close', function () {
                    app.api.emit('popup.close', viewMobilePopup);
                });

                sortMobilePopup.on('beforeOpen', function () {
                    app.api.emit('popup.beforeOpen', sortMobilePopup);
                }).on('open', function () {
                    app.api.emit('popup.open', sortMobilePopup);
                }).on('close', function () {
                    app.api.emit('popup.close', sortMobilePopup);
                });

                filterMobilePopup.on('beforeOpen', function () {
                    app.api.emit('popup.beforeOpen', filterMobilePopup);
                }).on('open', function () {
                    app.api.emit('popup.open', filterMobilePopup);
                }).on('close', function () {
                    app.api.emit('popup.close', filterMobilePopup);
                });
            }, {
                'name': '[Component] bitrix:catalog (catalog.1) panel',
                'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
                'loader': {
                    'name': 'lazy'
                }
            });
        </script>
    <?php } ?>
</div>
<!--/noindex-->
<?php /* if ($arFilter['SHOW']) { ?>
<?php
    $sTemplate = $arFilter['TEMPLATE'];

    if (StringHelper::startsWith($sTemplate, 'horizontal'))
        if ($sTemplate === 'horizontal.2') {
            $sTemplate = 'vertical.2';
        } else {
            $sTemplate = 'vertical.1';
        }
?>
    <div class="catalog-filter-mobile" data-role="catalog.filter">
        <?php if (!$bIsAjax) { ?>
            <?php $APPLICATION->IncludeComponent(
                'bitrix:catalog.smart.filter',
                $sTemplate,
                ArrayHelper::merge($arFilter['PARAMETERS'], [
                    'MOBILE' => 'Y'
                ]),
                $component
            ) ?>
        <?php } ?>
    </div>
<?php } */ ?>