<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

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
<div class="news-detail-panel" data-role="catalog.panel">
    <div class="news-detail-panel-wrapper" data-device="desktop">
        <div class="news-detail-panel-sort" data-role="catalog.panel.sort">
            <div class="news-detail-panel-sort-wrapper" data-role="catalog.panel.sortButton">
                <div class="news-detail-panel-sort-icon">
                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.18176 14.0909L6.45449 17.3636L9.72722 14.0909" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6.45454 4.63637L6.45455 17.3636" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18.8182 7.90909L15.5454 4.63636L12.2727 7.90909" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15.5454 17.3636L15.5454 4.63637" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="news-detail-panel-sort-text">
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
                        echo Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_SORT');
                    }

                    unset($sSortPropertyKey, $arSortProperty);
                    ?>
                </div>
                <div class="news-detail-panel-sort-items" data-role="catalog.panel.sortItems">
                    <div class="news-detail-panel-sort-items-wrapper">
                        <?php foreach ($arSort['PROPERTIES'] as $sSortPropertyKey => $arSortProperty) { ?>
                            <?= Html::beginTag('div', [
                                'class' => 'news-detail-panel-sort-item',
                                'data' => [
                                    'active' => $arSortProperty['ACTIVE'] ? 'true' : 'false',
                                    'order' => $arSortProperty['ORDER'],
                                    'role' => 'catalog.panel.sortSelection',
                                    'value' => $arSortProperty['VALUE']
                                ]
                            ]) ?>
                            <div class="news-detail-panel-sort-item-wrapper">
                                <div class="news-detail-panel-sort-item-name">
                                    <?= $arSortProperty['NAME'] ?>
                                </div>
                                <?php if (!empty($arSortProperty['DESCRIPTION']) && $arSortProperty['FIELD'] === 'name') { ?>
                                    <div class="news-detail-panel-sort-item-description">
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
        <div class="news-detail-panel-views" data-role="catalog.panel.views">
            <div class="news-detail-panel-views-items">
                <?php foreach($arVisual['VIEWS'] as $sView => $arView) { ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'news-detail-panel-views-item',
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
    </div>
    <div class="news-detail-panel-wrapper" data-device="mobile">
        <div class="news-detail-panel-popups">
            <div class="news-detail-panel-popup" data-role="catalog.panel.viewMobilePopup">
                <div class="news-detail-panel-popup-overlay" data-type="overlay"></div>
                <div class="news-detail-panel-popup-window" data-type="window">
                    <div class="news-detail-panel-popup-window-wrapper">
                        <div class="news-detail-panel-popup-window-header">
                            <div class="news-detail-panel-popup-window-title">
                                <?= Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_VIEW_HEADER') ?>
                            </div>
                            <div class="news-detail-panel-popup-window-close" data-type="button" data-action="close">
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L9 9" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9 1L1 9" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                        <div class="news-detail-panel-popup-window-content">
                            <form>
                                <div class="news-detail-panel-popup-selections">
                                    <?php foreach ($arViews as $sView => $arView) { ?>
                                        <?php
                                        $sIcon = FileHelper::getFileData(__DIR__.'/../images/views.'.$arView['VALUE'].'.svg');
                                        ?>
                                        <?= Html::beginTag('div', [
                                            'class' => 'news-detail-panel-popup-selection',
                                            'data' => [
                                                'active' => $arView['ACTIVE'] ? 'true' : 'false',
                                                'role' => 'catalog.panel.viewSelection',
                                                'value' => $arView['VALUE']
                                            ]
                                        ]) ?>
                                        <label class="news-detail-panel-popup-selection-control intec-ui intec-ui-control-radiobox intec-ui-scheme-current intec-ui-size-2">
                                            <?= Html::radio('selection', $arView['ACTIVE']) ?>
                                            <span class="news-detail-panel-popup-selection-selector intec-ui-part-selector"></span>
                                            <span class="news-detail-panel-popup-selection-content intec-ui-part-content">
                                                <span class="news-detail-panel-popup-selection-title">
                                                    <span class="news-detail-panel-popup-selection-icon">
                                                        <?= $sIcon ?>
                                                    </span>
                                                    <span class="news-detail-panel-popup-selection-text">
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
            <div class="news-detail-panel-popup" data-role="catalog.panel.sortMobilePopup">
                <div class="news-detail-panel-popup-overlay" data-type="overlay"></div>
                <div class="news-detail-panel-popup-window" data-type="window">
                    <div class="news-detail-panel-popup-window-wrapper">
                        <div class="news-detail-panel-popup-window-header">
                            <div class="news-detail-panel-popup-window-title">
                                <?= Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_SORT_HEADER') ?>
                            </div>
                            <div class="news-detail-panel-popup-window-close" data-type="button" data-action="close">
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L9 9" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9 1L1 9" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                        <div class="news-detail-panel-popup-window-content">
                            <form>
                                <div class="news-detail-panel-popup-selections">
                                    <?php foreach ($arSort['PROPERTIES'] as $arSortProperty) { ?>
                                        <?= Html::beginTag('div', [
                                            'class' => 'news-detail-panel-popup-selection',
                                            'data' => [
                                                'active' => $arSortProperty['ACTIVE'] ? 'true' : 'false',
                                                'order' => $arSortProperty['ORDER'],
                                                'role' => 'catalog.panel.sortSelection',
                                                'value' => $arSortProperty['VALUE']
                                            ]
                                        ]) ?>
                                        <label class="news-detail-panel-popup-selection-control intec-ui intec-ui-control-radiobox intec-ui-scheme-current intec-ui-size-2">
                                            <?= Html::radio('selection', $arSortProperty['ACTIVE']) ?>
                                            <span class="news-detail-panel-popup-selection-selector intec-ui-part-selector"></span>
                                            <span class="news-detail-panel-popup-selection-content intec-ui-part-content">
                                                <span class="news-detail-panel-popup-selection-title">
                                                    <span class="news-detail-panel-popup-selection-text">
                                                        <?= $arSortProperty['NAME'] ?>
                                                    </span>
                                                </span>
                                                <?php if (!empty($arSortProperty['DESCRIPTION'])) { ?>
                                                    <span class="news-detail-panel-popup-selection-description">
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
        </div>
        <div class="news-detail-panel-sort" data-role="catalog.panel.sort">
            <div class="news-detail-panel-sort-wrapper" data-role="catalog.panel.sortButton">
                <div class="news-detail-panel-sort-icon">
                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.18176 14.0909L6.45449 17.3636L9.72722 14.0909" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6.45454 4.63637L6.45455 17.3636" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18.8182 7.90909L15.5454 4.63636L12.2727 7.90909" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15.5454 17.3636L15.5454 4.63637" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="news-detail-panel-sort-text">
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
                        echo Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_SORT');
                    }

                    unset($sSortPropertyKey, $arSortProperty);
                    ?>
                </div>
            </div>
        </div>
        <div class="news-detail-panel-views" data-role="catalog.panel.views">
            <div class="news-detail-panel-views-button" data-role="catalog.panel.viewsButton">
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

            elements.root = data.nodes;
            elements.panel = $('[data-role="catalog.panel"]', elements.root);
            elements.viewSelection = $('[data-role="catalog.panel.viewSelection"]', elements.root);
            elements.views = $('[data-role="catalog.panel.views"]', elements.root);
            elements.viewsButton = $('[data-role="catalog.panel.viewsButton"]', elements.views);
            elements.sort = $('[data-role="catalog.panel.sort"]', elements.root);
            elements.sortButton = $('[data-role="catalog.panel.sortButton"]', elements.sort);
            elements.sortItems = $('[data-role="catalog.panel.sortItems"]', elements.sort);
            elements.sortSelection = $('[data-role="catalog.panel.sortSelection"]', elements.root);

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
        }, {
            'name': '[Component] bitrix:news.detail (collections.default.1) panel',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
</div>
<!--/noindex-->