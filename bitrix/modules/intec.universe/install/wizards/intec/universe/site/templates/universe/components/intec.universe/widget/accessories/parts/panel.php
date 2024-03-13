<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
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
$arFilter['SHOW'] = true;
?>
<!--noindex-->
<div class="widget-panel intec-grid intec-grid-nowrap intec-grid-a-h-end intec-grid-a-h-768-between intec-grid-i-h-10 intec-grid-a-v-center" data-role="widget.panel">
    <?php if (!$bIsAjax) { ?>
        <div class="widget-panel-form-overlay" data-role="form-overlay">
            <div class="widget-panel-form widget-panel-view-form intec-grid intec-grid-wrap">
                <svg class="widget-panel-form-close-icon" data-role="close-icon" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 1L9 9" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 1L1 9" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div class="widget-panel-form-header intec-grid-item-auto">
                    <?= Loc::getMessage('C_WIDGET_ACCESSORIES_1_VIEW_HEADER') ?>
                </div>
                <div class="intec-grid intec-grid-wrap intec-grid-item-1 widget-panel-form-list">
                    <?php $count = 1;
                    foreach($arViews as $sView => $arView) { ?>
                        <label class="intec-grid-item-1 intec-ui intec-ui-control-radiobox intec-ui-scheme-current">
                            <?= Html::beginTag('input', [
                                'type' => 'radio',
                                'checked' => $arView['ACTIVE'],
                                'name' => 'radio_'.$count
                            ]) ?>
                            <span class="intec-ui-part-selector">
                                <div class="widget-panel-form-checked-elem intec-cl-background"></div>
                            </span>
                            <?= Html::beginTag('a', [
                                'href' => $APPLICATION->GetCurPageParam('view='.$arView['VALUE'], ['view']),
                                'class' => [
                                    'widget-panel-view',
                                    'intec-grid-item-auto'
                                ],
                                'data' => [
                                    'active' => $arView['ACTIVE'] ? 'true' : 'false'
                                ]
                            ]) ?>
                            <span class="intec-ui-part-content">
                                <img src="<?= $arView['MOBILE_ICON'] ?>">
                                <span>
                                    <?php switch ($arView['VALUE']) {
                                        case 'text':
                                            echo Loc::getMessage('C_WIDGET_ACCESSORIES_1_SORTING_TYPE_TEXT');
                                            break;
                                        case 'tile':
                                            echo Loc::getMessage('C_WIDGET_ACCESSORIES_1_SORTING_TYPE_TILE');
                                            break;
                                        case 'list':
                                            echo Loc::getMessage('C_WIDGET_ACCESSORIES_1_SORTING_TYPE_LIST');
                                            break;
                                    }?>
                                </span>
                            </span>
                            <?= Html::endTag('a') ?>
                        </label>
                    <?php $count++;
                    }
                    unset($count); ?>
                </div>
            </div>
            <div class="widget-panel-form widget-panel-sort-form intec-grid intec-grid-wrap">
                <svg class="widget-panel-form-close-icon" data-role="close-icon" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 1L9 9" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 1L1 9" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div class="widget-panel-form-header intec-grid-item-auto">
                    <?= Loc::getMessage('C_WIDGET_ACCESSORIES_1_SORTING_HEADER') ?>
                </div>
                <div class="intec-grid intec-grid-wrap intec-grid-item-1 widget-panel-form-list">
                    <?php $count = 1;
                    foreach ($arSort['PROPERTIES'] as $sSortProperty => $arSortProperty) {?>
                        <label class="intec-grid-item-1 intec-ui intec-ui-control-radiobox intec-ui-scheme-current">
                            <?= Html::beginTag('input', [
                                'type' => 'radio',
                                'checked' => $arSortProperty['ACTIVE'],
                                'name' => 'sort_radio_'.$count
                            ]) ?>
                            <span class="intec-ui-part-selector">
                            <div class="widget-panel-form-checked-elem intec-cl-background"></div>
                        </span>
                            <?= Html::beginTag('a', [
                                'href' => $APPLICATION->GetCurPageParam('sort='.$arSortProperty['VALUE'].'&order='.$arSortProperty['ORDER'], ['sort', 'order']),
                                'class' => [
                                    'widget-panel-sort',
                                    'intec-grid-item-auto'
                                ],
                                'data' => [
                                    'active' => $arSortProperty['ACTIVE'] ? 'true' : 'false'
                                ]
                            ]) ?>
                        <span class="intec-grid intec-grid-wrap intec-ui-part-content">
                            <div class="intec-grid-item-1 widget-panel-form-property-name">
                                <?= $arSortProperty['NAME'] ?>
                            </div>
                            <?php if ($arSortProperty['SUBTITLE']) { ?>
                            <div class="intec-grid-item-1 widget-panel-form-property-subtitle">
                                <?= $arSortProperty['SUBTITLE'] ?>
                            </div>
                            <?php } ?>
                        </span>
                            <?= Html::endTag('a') ?>
                        </label>
                        <? $count++; ?>
                    <?php }
                    unset($count); ?>
                </div>
            </div>
        </div>
        <div class="widget-panel-sorting intec-grid-item-auto" data-order="<?= $arSort['ORDER'] ?>">
            <div class="widget-panel-sorting-wrapper intec-grid intec-grid-nowrap intec-grid-a-v-center">
                <img src="<?= $templateFolder.'/icons/sort.png' ?>" alt="switch">
                <div class="widget-panel-sort-text">
                    <?php
                    $arCount = [];
                    foreach ($arSort['PROPERTIES'] as $sSortProperty => $arSortProperty) {
                        $sSortOrder = $arSort['ORDER'];
                        if ($arSortProperty['ACTIVE']) {
                            $sSortOrder = $arSort['ORDER'] === $arSortProperty['ORDER'];
                            $arCount['ACTIVE']['NAME'] = $arSortProperty['NAME'];
                            $arCount['ACTIVE']['SUBTITLE'] = $arSortProperty['SUBTITLE'];
                        } else {
                            continue;
                        }
                    }
                    if (count($arCount) > 0) {
                        echo $arCount['ACTIVE']['NAME'];
                    } else {
                        echo Loc::getMessage('C_WIDGET_ACCESSORIES_1_SORTING_POPULAR');
                    }?>
                </div>
                <?php
                unset($sSortOrder);
                unset($arSortProperty);
                unset($sSortProperty);
                unset($arCount);
                ?>
            </div>
            <div class="widget-panel-sort-form-desktop intec-grid intec-grid-wrap">
                <div class="intec-grid intec-grid-wrap intec-grid-item-1 widget-panel-sort-form-desktop-list">
                    <?php $count = 1;
                    foreach ($arSort['PROPERTIES'] as $sSortProperty => $arSortProperty) { ?>
                        <label class="intec-grid-item-1 intec-ui intec-ui-control-radiobox intec-ui-scheme-current">
                            <?= Html::beginTag('input', [
                                'type' => 'radio',
                                'checked' => $arSortProperty['ACTIVE'],
                                'name' => 'desktop_sort_radio_'.$count
                            ]) ?>
                            <span class="intec-ui-part-selector">
                            <div class="widget-panel-sort-form-desktop-checked-elem intec-cl-background"></div>
                        </span>
                            <?= Html::beginTag('a', [
                                'href' => $APPLICATION->GetCurPageParam('sort='.$arSortProperty['VALUE'].'&order='.$arSortProperty['ORDER'], ['sort', 'order']),
                                'class' => [
                                    'widget-panel-sort',
                                    'intec-grid-item-auto'
                                ],
                                'data' => [
                                    'active' => $arSortProperty['ACTIVE'] ? 'true' : 'false'
                                ]
                            ]) ?>
                            <span class="intec-grid intec-grid-nowrap intec-ui-part-content">
                                <?= Html::beginTag('div', [
                                    'class' => [
                                        'intec-grid-item-auto',
                                        'widget-panel-sort-form-desktop-property-name'
                                    ],
                                    'data-active' => $arSortProperty['ACTIVE'] ? 'true' : 'false'
                                ]) ?>
                                        <?= $arSortProperty['NAME'] ?>
                                <?= Html::endTag('div') ?>

                                <?if ($arSortProperty['FIELD'] === 'name') { ?>
                                    <div class="intec-grid-item-auto widget-panel-sort-form-desktop-property-subtitle">
                                        <?= $arSortProperty['SUBTITLE'] ?>
                                    </div>
                                <?php } ?>
                            </span>
                            <?= Html::endTag('a') ?>
                        </label>
                        <?php $count++; ?>
                    <?php }
                    unset($count); ?>
                </div>
            </div>
        </div>
        <?php if ($arFilter['SHOW'] || $searchFilterShow) { ?>
            <div class="intec-grid-item-auto widget-panel-wrapper" data-device="mobile">
                <div class="widget-panel-popups">
                    <div class="widget-panel-popup" data-role="widget.panel.filterMobilePopup">
                        <div class="widget-panel-popup-overlay" data-type="overlay"></div>
                        <div class="widget-panel-popup-filter" data-type="window">
                            <div class="widget-panel-popup-filter-close widget-panel-popup-window-close" data-type="button" data-action="close">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 8L16 16" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M16 8L8 16" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="widget-panel-popup-filter-content">
                                <?php $APPLICATION->IncludeComponent(
                                    'bitrix:catalog.smart.filter',
                                    'mobile.1',
                                    ArrayHelper::merge($arFilter['PROPERTIES'], [
                                        'MOBILE' => 'Y'
                                    ]),
                                    $component
                                ) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="widget-panel-filter" data-role="widget.panel.filter">
                    <div class="widget-panel-filter-button" data-role="widget.panel.filter.button">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 13.333L3.36 7.8C3.132 7.61 3 7.328 3 7.032V4.5C3 3.948 3.448 3.5 4 3.5H20C20.552 3.5 21 3.948 21 4.5V7.032C21 7.329 20.868 7.61 20.64 7.8L14 13.333V17.882C14 18.261 13.786 18.607 13.447 18.776L10.723 20.138C10.391 20.304 10 20.063 10 19.691V13.333V13.333Z" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3.12012 7.5H20.8801" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
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
                    elements.panel = $('[data-role="widget.panel"]', elements.root);
                    elements.filter = $('[data-role="widget.panel.filter"]', elements.root);
                    elements.filterButton = $('[data-role="widget.panel.filter.button"]', elements.filter);

                    elements.filterButton.on('click', function () {
                        filterMobilePopup.open();
                    });

                    filterMobilePopup = $('[data-role="widget.panel.filterMobilePopup"]', elements.panel).uiControl('popup', popupOptions)[0];


                    filterMobilePopup.on('beforeOpen', function () {
                        app.api.emit('popup.beforeOpen', filterMobilePopup);
                    }).on('open', function () {
                        app.api.emit('popup.open', filterMobilePopup);
                    }).on('close', function () {
                        app.api.emit('popup.close', filterMobilePopup);
                    });
                }, {
                    'name': '[Component] bitrix:widget (widget.1) panel',
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