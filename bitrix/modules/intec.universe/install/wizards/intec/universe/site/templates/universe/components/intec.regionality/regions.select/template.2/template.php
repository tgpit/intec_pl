<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
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
    <div id="<?= $sTemplateId ?>" class="ns-intec-regionality c-regions-select c-regions-select-template-2">
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
                    <?= Loc::getMessage('C_REGIONS_SELECT_TEMPLATE_2_REGION_UNSET') ?>
                </span>
                <span class="regions-select-region-icon intec-grid-item-auto">
                    <i class="far fa-chevron-down"></i>
                </span>
            <?php } ?>
        </div>
        <div class="regions-select-dialog" data-role="select.list">
            <div class="regions-select-dialog-regions scrollbar-inner" data-role="dialog.regions" data-scroll="scrollbar">
                <?php foreach ($arResult['REGIONS'] as $oRegion) { ?>
                    <?php if ($oRegion === $oRegionCurrent) { ?>
                        <div class="regions-select-dialog-region regions-select-dialog-region-current">
                            <div class="regions-select-dialog-region-selector regions-select-dialog-region-selector-current ">
                                <?= Html::encode($oRegion->name) ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="regions-select-dialog-region" data-id="<?= $oRegion->id ?>" data-role="dialog.region">
                            <div class="regions-select-dialog-region-selector" data-role="dialog.region.selector">
                                <?= Html::encode($oRegion->name) ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
        <div class="regions-select-question" data-role="question" data-region="<?= $oRegionCurrent->id ?>">
            <div class="regions-select-question-title-wrap">
                <div class="regions-select-question-title">
                    <?= Loc::getMessage('C_REGIONS_SELECT_TEMPLATE_2_QUESTION_TEXT') ?>
                </div>
                <div class="regions-select-question-name">
                    <?= Html::encode($oRegionCurrent->name) ?>
                </div>
            </div>
            <div class="regions-select-question-buttons">
                <button class="regions-select-question-button intec-cl-background intec-cl-background-light-hover" data-role="question.yes">
                    <?= Loc::getMessage('C_REGIONS_SELECT_TEMPLATE_2_QUESTION_BUTTONS_YES') ?>
                </button>
                <button class="regions-select-question-button" data-role="question.no">
                    <?= Loc::getMessage('C_REGIONS_SELECT_TEMPLATE_2_QUESTION_BUTTONS_NO') ?>
                </button>
            </div>
            <div class="regions-select-question-close" data-role="question.close">
                <i class="fal fa-times intec-cl-text-hover"></i>
            </div>
        </div>
        <script type="text/javascript">
            (function () {
                var root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
                var dialog = $('[data-role="select.list"]', root);
                var select = $('[data-role="select"]', root);
                var scrollbar = $('[data-scroll="scrollbar"]', root);
                var question = $('[data-role="question"]', root);
                var questionData;
                var windowQuestion;
                var questionShow = <?= JavaScript::toObject($bQuestionShow) ?>;
                var component = new JCIntecRegionalityRegionsSelect(<?= JavaScript::toObject([
                    'action' => $arResult['ACTION'],
                    'site' => $sSite
                ]) ?>);

                dialog.regions = $('[data-role="dialog.region"]', dialog);
                dialog.open = function () {
                    $(this).slideToggle(200);
                    select.toggleClass('region-select-opened');
                };

                dialog.close = function () {
                    $(this).slideUp();
                };

                question.yes = $('[data-role="question.yes"]', question);
                question.no = $('[data-role="question.no"]', question);
                question.close = function () {
                    question.remove();
                };

                select.on('click', function () {
                    dialog.open();
                    question.close();
                });

                dialog.regions.on('click', function () {
                    var region = $(this);
                    var id = region.data('id');

                    component.select(id);
                    dialog.close();
                });

                questionData = <?= JavaScript::toObject([
                    'id' => $sTemplateId.'-question'
                ]) ?>;

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

                windowQuestion.setContent(question.get(0));

                if (questionShow) {
                    question.open();
                }

                question.yes.on('click', function () {
                    var id = question.data('region');

                    question.close();
                    component.select(id);
                });

                question.no.on('click', function () {
                    question.close();
                    dialog.open();

                    $('html, body').animate({
                        scrollTop: dialog.offset().top-20
                    }, 500);
                });

                $('[data-role="question.close"]', question).on('click', function () {
                    var id = question.data('region');

                    question.close();
                    component.select(id);
                });

                scrollbar.scrollbar();
            })();
        </script>
    </div>
<?php $oFrame->beginStub() ?>
    <div class="ns-intec-regionality c-regions-select c-regions-select-default">
        <div class="regions-select-region intec-grid intec-grid-nowrap intec-grid-a-v-center intec-grid-i-h-1 intec-cl-text intec-cl-text-light-hover" data-role="select">
            <span class="adr regions-select-region-text intec-grid-item-auto">
                <span class="region"><?= !empty($oRegionDefault) ? Html::encode($oRegionDefault->name) : Loc::getMessage('C_REGIONS_SELECT_TEMPLATE_2_REGION_UNSET') ?></span>
            </span>
            <span class="regions-select-region-icon intec-grid-item-auto">
                <i class="far fa-chevron-down"></i>
            </span>
        </div>
    </div>
<?php $oFrame->end() ?>