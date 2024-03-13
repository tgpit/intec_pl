<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\regionality\models\Region;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$oContext = Context::getCurrent();
$sSite = $oContext->getSite();

if (empty($arResult['REGIONS']))
    return;

/** @var Region $oRegionCurrent */
$oRegionCurrent = $arResult['REGION'];
$oRegionDefault = Region::getDefault();
$bQuestionShow = !empty($oRegionCurrent) && !$arResult['SELECTED'] && !defined('EDITOR');

?>
<?php $oFrame = $this->createFrame()->begin() ?>
    <div id="<?= $sTemplateId ?>" class="ns-intec-regionality c-regions-select c-regions-select-template-1">
        <div class="regions-select-region intec-grid intec-grid-nowrap intec-grid-a-v-center intec-grid-i-h-1 intec-cl-text-light-hover" data-role="select">
            <?php if (!empty($oRegionCurrent)) { ?>
                <span class="adr regions-select-region-text intec-grid-item-auto">
                    <span class="region"><?= Html::encode($oRegionCurrent->name) ?></span>
                </span>
                <span class="regions-select-region-icon intec-grid-item-auto">
                <i class="far fa-chevron-down"></i>
            </span>
            <?php } else { ?>
                <span class="regions-select-region-text intec-grid-item-auto">
                    <?= Loc::getMessage('C_REGIONS_SELECT_TEMPLATE_1_REGION_UNSET') ?>
                </span>
                <span class="regions-select-region-icon intec-grid-item-auto">
                <i class="far fa-chevron-down"></i>
            </span>
            <?php } ?>
        </div>
        <div class="regions-select-dialog" data-role="dialog">
            <div class="regions-select-dialog-window">
                <div class="regions-select-dialog-window-content">
                    <div class="regions-select-dialog-search">
                        <i class="regions-select-dialog-search-icon regions-select-dialog-search-icon-enter intec-cl-svg-path-stroke-hover" data-role="search.button.enter">
                            <?= FileHelper::getFileData(__DIR__.'/svg/search_icon.svg')?>
                        </i>
                        <i class="regions-select-dialog-search-icon regions-select-dialog-search-icon-clear intec-cl-svg-path-stroke-hover" data-role="search.button.clear">
                            <?= FileHelper::getFileData(__DIR__.'/svg/clear_icon.svg')?>
                        </i>
                        <?= Html::textInput(null, null, [
                            'class' => [
                                'regions-select-dialog-search-input',
                                'intec-ui' => [
                                    '',
                                    'control-input',
                                    'mod-block',
                                    'size-2'
                                ]
                            ],
                            'placeholder' => Loc::getMessage('C_REGIONS_SELECT_TEMPLATE_1_DIALOG_SEARCH_PLACEHOLDER'),
                            'data' => [
                                'role' => 'dialog.search'
                            ]
                        ]) ?>
                        <div class="regions-select-dialog-search-result" data-role="search.result">
                            <div class="regions-select-dialog-search-result-regions scrollbar-inner" data-role="search.result.regions">
                                <?php foreach ($arResult['REGIONS'] as $oRegion) { ?>
                                    <div class="regions-select-dialog-search-result-region" data-id="<?= $oRegion->id ?>" data-role="search.result.region">
                                        <div class="regions-select-dialog-region-search-result-selector intec-cl-text-hover" data-role="search.result.region.selector"><?php
                                            echo Html::encode($oRegion->name)
                                        ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="regions-select-dialog-example-regions-wrap">
                        <div class="intec-grid intec-grid-wrap intec-grid-a-v-baseline intec-grid-i-8">
                            <div class="intec-grid-item-auto">
                                <?= Loc::getMessage('C_REGIONS_SELECT_TEMPLATE_1_DIALOG_CITY_EXAMPLE')?>
                            </div>
                            <div class="intec-grid-item-auto">
                                <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-6" data-role="dialog.regions">
                                    <?php
                                    $arExampleRegions = array_slice($arResult['REGIONS'], 0, 2);

                                    foreach ($arExampleRegions as $oRegion) { ?>
                                        <div class="regions-select-dialog-example-region intec-grid-item-auto" data-id="<?= $oRegion->id ?>" data-role="dialog.region">
                                            <div class="regions-select-dialog-example-region-selector intec-cl-text intec-cl-text-light-hover intec-cl-border" data-role="dialog.region.selector">
                                                <?= Html::encode($oRegion->name) ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if (!empty($oRegionCurrent) && !$arResult['SELECTED']) { ?>
                                <div class="intec-grid-item-auto">
                                    <?= Loc::getMessage('C_REGIONS_SELECT_TEMPLATE_1_DIALOG_OR')?>
                                </div>
                                <div class="intec-grid-item-auto">
                                    <div class="regions-select-dialog-auto intec-cl-text-hover intec-cl-border-hover" data-role="dialog.auto" data-region="<?= $oRegionCurrent->id ?>">
                                        <?= Loc::getMessage('C_REGIONS_SELECT_TEMPLATE_1_DIALOG_AUTO') ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="regions-select-dialog-regions scrollbar-inner" data-role="dialog.regions">
                        <?php foreach ($arResult['REGIONS'] as $oRegion) { ?>
                            <div class="regions-select-dialog-region" data-id="<?= $oRegion->id ?>" data-role="dialog.region">
                                <div class="regions-select-dialog-region-selector intec-cl-text-hover <?=($oRegion === $oRegionCurrent)?'intec-cl-text':''?>" data-role="dialog.region.selector">
                                    <?= Html::encode($oRegion->name) ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="regions-select-question" data-role="question" data-region="<?= $oRegionCurrent->id ?>">
            <div class="regions-select-question-title-wrap">
                <div class="regions-select-question-title">
                    <?= Loc::getMessage('C_REGIONS_SELECT_TEMPLATE_1_QUESTION_TEXT') ?>
                </div>
                <div class="regions-select-question-name">
                    <?= Html::encode($oRegionCurrent->name) ?>
                </div>
            </div>
            <div class="regions-select-question-buttons">
                <button class="regions-select-question-button intec-cl-background intec-cl-background-light-hover" data-role="question.yes">
                    <?= Loc::getMessage('C_REGIONS_SELECT_TEMPLATE_1_QUESTION_BUTTONS_YES') ?>
                </button>
                <button class="regions-select-question-button" data-role="question.no">
                    <?= Loc::getMessage('C_REGIONS_SELECT_TEMPLATE_1_QUESTION_BUTTONS_NO') ?>
                </button>
            </div>
            <div class="regions-select-question-close" data-role="question.close">
                <i class="fal fa-times intec-cl-text-hover"></i>
            </div>
        </div>
        <script type="text/javascript">
            template.load(function () {
                var $ = this.getLibrary('$');

                var root = arguments[0].nodes;
                var data;
                var questionData;
                var dialog = $('[data-role="dialog"]', root);
                var select = $('[data-role="select"]', root);
                var window;
                var windowQuestion;
                var questionShow = <?= JavaScript::toObject($bQuestionShow) ?>;
                var question = $('[data-role="question"]', root);
                var component = new JCIntecRegionalityRegionsSelect(<?= JavaScript::toObject([
                    'action' => $arResult['ACTION'],
                    'site' => $sSite
                ]) ?>);

                dialog.search = $('[data-role="dialog.search"]', dialog);
                dialog.auto = $('[data-role="dialog.auto"]', dialog);
                dialog.regionsContainer = $('[data-role="dialog.regions"]', dialog);
                dialog.regionsContainer.scrollbar();
                dialog.regions = $('[data-role="dialog.region"]', dialog.regionsContainer);
                dialog.open = function () {
                    window.show();
                };

                dialog.search.result = $('[data-role="search.result"]', dialog);
                dialog.search.result.regions = $('[data-role="search.result.region"]', dialog.search.result);
                dialog.search.enter = $('[data-role="search.button.enter"]', dialog);
                dialog.search.clear = $('[data-role="search.button.clear"]', dialog);

                dialog.close = function () {
                    window.close();
                };

                data = <?= JavaScript::toObject([
                    'id' => $sTemplateId.'-dialog',
                    'title' => Loc::getMessage('C_REGIONS_SELECT_TEMPLATE_1_DIALOG_TITLE')
                ]) ?>;

                questionData = <?= JavaScript::toObject([
                    'id' => $sTemplateId.'-question'
                ]) ?>;

                question.yes = $('[data-role="question.yes"]', question);
                question.no = $('[data-role="question.no"]', question);
                question.close = function () {
                    question.remove();
                };

                window = new BX.PopupWindow(data.id, null, {
                    'content': null,
                    'title': data.title,
                    'className': 'regions-select-popup regions-select-popup-default',
                    'closeIcon': {
                        'right': '70px',
                        'top': '70px'
                    },
                    'zIndex': 0,
                    'offsetLeft': 0,
                    'offsetTop': 0,
                    'width': null,
                    'overlay': true,
                    'titleBar': {
                        'content': BX.create('span', {
                            'html': data.title,
                            'props': {
                                'className': 'access-title-bar'
                            }
                        })
                    }
                });

                windowQuestion = new BX.PopupWindow(questionData.id, null, {
                    'content': null,
                    'title': null,
                    'className': 'regions-select-question-popup regions-select-question-popup-default',
                    'closeIcon': {
                        'display': 'none'
                    },
                    'zIndex': 0,
                    'offsetLeft': 0,
                    'offsetTop': 0,
                    'width': null,
                    'overlay': false
                });

                question.open = function () {
                    windowQuestion.show();
                };

                window.setContent(dialog.get(0));
                windowQuestion.setContent(question.get(0));

                if (questionShow) {
                    question.open();
                }

                select.on('click', function () {
                    dialog.open();
                    question.close();
                });

                dialog.search.on('keyup', function () {
                    var query = this.value;
                    var expression = new RegExp(query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'i');
                    var resultQuantity = 0;

                    if (query.length !== 0) {
                        dialog.search.result.regions.each(function () {
                            var region = $(this);
                            var selector = $('[data-role="search.result.region.selector"]', region);

                            if (selector.html().match(expression)) {
                                region.css('display', '');
                                resultQuantity++;
                            } else {
                                region.css('display', 'none');
                            }
                        });
                    }

                    if (resultQuantity > 0) {
                        dialog.search.result.show();
                    } else {
                        dialog.search.result.hide();
                    }
                });

                dialog.search.clear.on('click', function () {
                    dialog.search.val('');
                    dialog.search.focus();
                });
                dialog.search.enter.on('click', function () {
                    dialog.search.trigger('keyup');
                    dialog.search.focus();
                });

                dialog.auto.on('click', function () {
                    var id = dialog.auto.data('region');

                    component.select(id);
                    dialog.auto.remove();
                    dialog.close();
                });

                dialog.regions.on('click', function () {
                    var region = $(this);
                    var id = region.data('id');

                    component.select(id);
                    dialog.close();
                });

                dialog.search.result.regions.on('click', function () {
                    var region = $(this);
                    var id = region.data('id');
                    var selector = $('[data-role="search.result.region.selector"]', region);

                    dialog.search.val(selector.html());

                    component.select(id);
                    dialog.close();
                });

                dialog.search.on('focusout', function () {
                    dialog.search.result.delay(200).hide(0);
                });

                question.yes.on('click', function () {
                    var id = question.data('region');

                    question.close();
                    component.select(id);
                });

                question.no.on('click', function () {
                    question.close();
                    dialog.open();
                });

                $('[data-role="question.close"]', question).on('click', function () {
                    var id = question.data('region');

                    question.close();
                    component.select(id);
                });
            }, {
                'name': '[Component] intec.regionality:regions.select (template.1)',
                'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
                'loader': {
                    'options': {
                        'await': [
                            'composite'
                        ]
                    }
                }
            });
        </script>
    </div>
<?php $oFrame->beginStub() ?>
    <div class="ns-intec-regionality c-regions-select c-regions-select-default">
        <div class="regions-select-region intec-grid intec-grid-nowrap intec-grid-a-v-center intec-grid-i-h-1 intec-cl-text intec-cl-text-light-hover" data-role="select">
        <span class="adr regions-select-region-text intec-grid-item-auto">
            <span class="region"><?= !empty($oRegionDefault) ? Html::encode($oRegionDefault->name) : Loc::getMessage('C_REGIONS_SELECT_TEMPLATE_1_REGION_UNSET') ?></span>
        </span>
            <span class="regions-select-region-icon intec-grid-item-auto">
            <i class="far fa-chevron-down"></i>
        </span>
        </div>
    </div>
<?php $oFrame->end() ?>