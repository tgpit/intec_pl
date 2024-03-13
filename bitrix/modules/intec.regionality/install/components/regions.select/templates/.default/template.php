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

?>
<?php $oFrame = $this->createFrame()->begin() ?>
<div id="<?= $sTemplateId ?>" class="ns-intec-regionality c-regions-select c-regions-select-default">
    <div class="regions-select-region" data-role="select">
        <?php if (!empty($oRegionCurrent)) { ?>
            <?= Loc::getMessage('C_REGIONS_SELECT_DEFAULT_REGION') ?>: <?= Html::encode($oRegionCurrent->name) ?>
        <?php } else { ?>
            <?= Loc::getMessage('C_REGIONS_SELECT_DEFAULT_REGION_UNSET') ?>
        <?php } ?>
    </div>
    <div class="regions-select-dialog" data-role="dialog">
        <div class="regions-select-dialog-overlay"></div>
        <div class="regions-select-dialog-window">
            <div class="regions-select-dialog-window-header">
                <div class="regions-select-dialog-window-title">
                    <?= Loc::getMessage('C_REGIONS_SELECT_DEFAULT_DIALOG_TITLE') ?>
                </div>
                <div class="regions-select-dialog-window-close" data-role="dialog.close">
                    <?= Loc::getMessage('C_REGIONS_SELECT_DEFAULT_DIALOG_CLOSE') ?>
                </div>
            </div>
            <div class="regions-select-dialog-window-content">
                <div class="regions-select-dialog-search">
                    <div class="regions-select-dialog-search-title">
                        <?= Loc::getMessage('C_REGIONS_SELECT_DEFAULT_DIALOG_SEARCH_TITLE') ?>
                    </div>
                    <input type="text" placeholder="<?= Loc::getMessage('C_REGIONS_SELECT_DEFAULT_DIALOG_SEARCH_PLACEHOLDER') ?>" class="regions-select-dialog-search-input" data-role="dialog.search" />
                </div>
                <div class="regions-select-dialog-regions">
                    <?php foreach ($arResult['REGIONS'] as $oRegion) { ?>
                        <?php if ($oRegion === $oRegionCurrent) continue ?>
                        <div class="regions-select-dialog-region" data-id="<?= $oRegion->id ?>" data-role="dialog.region">
                            <?= Html::encode($oRegion->name) ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php if (!empty($oRegionCurrent) && !$arResult['SELECTED']) { ?>
        <div class="regions-select-question" data-role="question" data-region="<?= $oRegionCurrent->id ?>">
            <div class="regions-select-question-text">
                <?= Loc::getMessage('C_REGIONS_SELECT_DEFAULT_QUESTION_TEXT', [
                    '#name#' => Html::encode($oRegionCurrent->name)
                ]) ?>
            </div>
            <div class="regions-select-question-buttons">
                <button class="regions-select-question-button" data-role="question.yes">
                    <?= Loc::getMessage('C_REGIONS_SELECT_DEFAULT_QUESTION_BUTTONS_YES') ?>
                </button>
                <button class="regions-select-question-button" data-role="question.no">
                    <?= Loc::getMessage('C_REGIONS_SELECT_DEFAULT_QUESTION_BUTTONS_NO') ?>
                </button>
            </div>
        </div>
    <?php } ?>
    <script type="text/javascript">
        (function () {
            var root = BX(<?= JavaScript::toObject($sTemplateId) ?>);
            var dialog = BX.findChild(root, {
                'attribute': {
                    'data-role': 'dialog'
                }
            }, true);

            var dialogClose = BX.findChild(dialog, {
                'attribute': {
                    'data-role': 'dialog.close'
                }
            }, true);

            var dialogSearch = BX.findChild(dialog, {
                'attribute': {
                    'data-role': 'dialog.search'
                }
            }, true);

            var dialogRegions = BX.findChildren(dialog, {
                'attribute': {
                    'data-role': 'dialog.region'
                }
            }, true);

            var dialogSelector = function () {
                component.select(this.getAttribute('data-id'));
                dialog.style.display = '';
            };

            var question = BX.findChild(root, {
                'attribute': {
                    'data-role': 'question'
                }
            }, true);

            var questionRegion;
            var questionYes;
            var questionNo;

            if (question) {
                questionRegion = question.getAttribute('data-region');
                questionYes = BX.findChild(question, {
                    'attribute': {
                        'data-role': 'question.yes'
                    }
                }, true);

                questionNo = BX.findChild(question, {
                    'attribute': {
                        'data-role': 'question.no'
                    }
                }, true);

                questionYes.addEventListener('click', function () {
                    question.style.display = 'none';
                    component.select(questionRegion);
                });

                questionNo.addEventListener('click', function () {
                    question.style.display = 'none';
                    dialog.style.display = 'block';
                });
            }

            var select = BX.findChild(root, {
                'attribute': {
                    'data-role': 'select'
                }
            }, true);

            var component = new JCIntecRegionalityRegionsSelect(<?= JavaScript::toObject([
                'action' => $arResult['ACTION'],
                'site' => $sSite
            ]) ?>);

            select.addEventListener('click', function () {
                dialog.style.display = 'block';

                if (question)
                    question.style.display = 'none';
            });

            dialogClose.addEventListener('click', function () {
                dialog.style.display = '';
            });

            dialogSearch.addEventListener('keyup', function () {
                var query = this.value;
                var expression = new RegExp(query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'i');

                for (var index = 0; index < dialogRegions.length; index++) {
                    var region = dialogRegions[index];

                    if (query.length === 0 || region.innerText.match(expression)) {
                        region.style.display = '';
                    } else {
                        region.style.display = 'none';
                    }
                }
            });

            for (var index = 0; index < dialogRegions.length; index++)
                dialogRegions[index].addEventListener('click', dialogSelector);
        })();
    </script>
</div>
<?php $oFrame->beginStub() ?>
<div id="<?= $sTemplateId ?>" class="ns-intec-regionality c-regions-select c-regions-select-default">
    <div class="regions-select-region" data-role="select">
        <?= !empty($oRegionDefault) ? Loc::getMessage('C_REGIONS_SELECT_DEFAULT_REGION').': '.Html::encode($oRegionDefault->name) : Loc::getMessage('C_REGIONS_SELECT_DEFAULT_REGION_UNSET') ?>
    </div>
</div>
<?php $oFrame->end() ?>