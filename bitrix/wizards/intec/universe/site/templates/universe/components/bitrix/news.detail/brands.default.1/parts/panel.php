<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use  intec\core\helpers\JavaScript;

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
<div class="news-detail-panel intec-grid intec-grid-nowrap intec-grid-a-h-end intec-grid-a-v-center" data-role="catalog.panel">
    <?php if (!$bIsAjax) { ?>
        <?php if ($arFilter['SHOW']) { ?>
            <div class="intec-grid-item-auto">
                <div class="news-detail-panel-wrapper" data-device="mobile">
                    <div class="news-detail-panel-popups">
                        <div class="news-detail-panel-popup" data-role="catalog.panel.filterMobilePopup">
                            <div class="news-detail-panel-popup-overlay" data-type="overlay"></div>
                            <div class="news-detail-panel-popup-filter" data-type="window">
                                <div class="news-detail-panel-popup-filter-close news-detail-panel-popup-window-close" data-type="button" data-action="close">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 8L16 16" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M16 8L8 16" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="news-detail-panel-popup-filter-content">
                                    <?php $APPLICATION->IncludeComponent(
                                        'bitrix:catalog.smart.filter',
                                        'mobile.1',
                                        $arFilter['PARAMETERS'],
                                        $component
                                    ) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="news-detail-panel-filter" data-role="catalog.panel.filter">
                        <div class="news-detail-panel-filter-button" data-role="catalog.panel.filter.button">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 13.333L3.36 7.8C3.132 7.61 3 7.328 3 7.032V4.5C3 3.948 3.448 3.5 4 3.5H20C20.552 3.5 21 3.948 21 4.5V7.032C21 7.329 20.868 7.61 20.64 7.8L14 13.333V17.882C14 18.261 13.786 18.607 13.447 18.776L10.723 20.138C10.391 20.304 10 20.063 10 19.691V13.333V13.333Z" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3.12012 7.5H20.8801" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                template.load(function (data) {
                    var app = this;
                    var $ = app.getLibrary('$');
                    var elements = {};

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

                    var filterMobilePopup;

                    elements.root = data.nodes;
                    elements.panel = $('[data-role="catalog.panel"]', elements.root);
                    elements.filter = $('[data-role="catalog.panel.filter"]', elements.root);
                    elements.filterButton = $('[data-role="catalog.panel.filter.button"]', elements.filter);

                    elements.filterButton.on('click', function () {
                        filterMobilePopup.open();
                    });

                    filterMobilePopup = $('[data-role="catalog.panel.filterMobilePopup"]', elements.panel).uiControl('popup', popupOptions)[0];


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
    <?php } ?>
</div>
<!--/noindex-->