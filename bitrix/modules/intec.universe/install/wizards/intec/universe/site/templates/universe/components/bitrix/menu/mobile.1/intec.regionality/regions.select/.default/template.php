<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;
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

$oRegionCurrent = $arResult['REGION'];
$oRegionDefault = Region::getDefault();
$bHasChildren = count($arResult['REGIONS']) > 1;
$bQuestionShow = !empty($oRegionCurrent) && !$arResult['SELECTED'] && !defined('EDITOR');
$arSvg = [
    'CLOSE' => FileHelper::getFileData(__DIR__.'/svg/close.svg')
];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'menu-item' => [
            '',
            'level-0',
            'extra'
        ]
    ],
    'data' => [
        'role' => 'item',
        'level' => 0,
        'expanded' => 'false',
        'current' => 'false',
        'code' => 'region.select'
    ]
]) ?>
    <div class="menu-item-wrapper">
        <?= Html::beginTag('div', [
            'class' => [
                'menu-item-content',
                'intec-cl' => [
                    'text-hover'
                ]
            ],
            'data' => [
                'action' => 'menu.item.open'
            ]
        ]) ?>
            <div class="intec-grid intec-grid-nowrap intec-grid-i-h-10 intec-grid-a-v-center">
                <div class="menu-item-icon-wrap intec-grid-item-auto">
                    <div class="menu-item-icon intec-cl-text">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                </div>
                <div class="menu-item-text-wrap intec-grid-item intec-grid-item-shrink-1">
                    <div class="menu-item-text">
                        <?php $oFrame = $this->createFrame()->begin() ?>
                            <?= !empty($oRegionCurrent) ? Html::encode($oRegionCurrent->name) : Loc::getMessage('C_MENU_MOBILE_1_REGIONS_SELECT') ?>
                        <?php $oFrame->beginStub() ?>
                            <?= !empty($oRegionDefault) ? Html::encode($oRegionDefault->name) : Loc::getMessage('C_MENU_MOBILE_1_REGIONS_SELECT') ?>
                        <?php $oFrame->end() ?>
                    </div>
                </div>
                <?php if ($bHasChildren) { ?>
                    <div class="menu-item-icon-wrap intec-grid-item-auto">
                        <div class="menu-item-icon">
                            <i class="far fa-angle-right"></i>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?= Html::endTag('div') ?>
        <div class="menu-item-items" data-role="items">
            <?= Html::beginTag('div', [
                'class' => [
                    'menu-item' => [
                        '',
                        'level-1',
                        'button'
                    ]
                ],
                'data' => [
                    'level' => 1
                ]
            ]) ?>
                <div class="menu-item-wrapper">
                    <div class="menu-item-content intec-cl-text-hover" data-action="menu.item.close">
                        <div class="intec-grid intec-grid-nowrap intec-grid-i-h-10 intec-grid-a-v-center">
                            <div class="menu-item-icon-wrap intec-grid-item-auto">
                                <div class="menu-item-icon">
                                    <i class="far fa-angle-left"></i>
                                </div>
                            </div>
                            <div class="menu-item-text-wrap intec-grid-item intec-grid-item-shrink-1">
                                <div class="menu-item-text">
                                    <?= Loc::getMessage('C_MENU_MOBILE_1_BACK') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?= Html::endTag('div') ?>
            <?php if ($bHasChildren) { ?>
                <?php foreach ($arResult['REGIONS'] as $oRegion) { ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'menu-item' => [
                                '',
                                'level-1',
                                'button'
                            ]
                        ],
                        'data' => [
                            'action' => 'menu.close',
                            'role' => 'item',
                            'region' => $oRegion->id,
                            'level' => 1
                        ]
                    ]) ?>
                        <div class="menu-item-wrapper">
                            <div class="menu-item-content intec-cl-text-hover" data-action="menu.close">
                                <?= Html::encode($oRegion->name) ?>
                            </div>
                        </div>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <div class="menu-region-question" data-role="question" data-region="<?= $oRegionCurrent->id ?>">
        <div class="menu-region-question-title-wrap">
            <div class="menu-region-question-title">
                <?= Loc::getMessage('C_MENU_MOBILE_1_QUESTION_TITLE', [
                    '#REGION#' => Html::encode($oRegionCurrent->name)
                ]) ?>
            </div>
        </div>
        <div class="menu-region-question-buttons">
            <?= Html::tag('div', Loc::getMessage('C_MENU_MOBILE_1_QUESTION_BUTTONS_YES'), [
                'class' => [
                    'menu-region-question-button',
                    'intec-ui' => [
                        '',
                        'control-button',
                        'scheme-current',
                        'mod-round-2'
                    ]
                ],
                'data' => [
                    'role' => 'question.yes'
                ]
            ]) ?>
            <?= Html::tag('div', Loc::getMessage('C_MENU_MOBILE_1_QUESTION_BUTTONS_NO'), [
                'class' => [
                    'menu-region-question-button',
                    'intec-ui' => [
                        '',
                        'control-button',
                        'mod-transparent',
                        'scheme-current',
                        'mod-round-2'
                    ]
                ],
                'data' => [
                    'role' => 'question.no'
                ]
            ]) ?>
        </div>
        <div class="menu-region-question-close intec-ui-picture" data-role="question.close">
            <?= $arSvg['CLOSE'] ?>
        </div>
    </div>
    <?php $oFrame = $this->createFrame()->begin() ?>
        <script type="text/javascript">
            template.load(function (data) {
                var $ = this.getLibrary('$');

                var root = data.nodes;
                var regions = $('[data-role="item"][data-region]', root);
                var questionShow = <?= JavaScript::toObject($bQuestionShow) ?>;
                var questionParent = $('[data-role="header-mobile-region-select"]');
                var question = $('[data-role="question"]', root);
                var component = new JCIntecRegionalityRegionsSelect(<?= JavaScript::toObject([
                    'action' => $arResult['ACTION'],
                    'site' => $sSite
                ]) ?>);

                if (questionShow) {
                    questionParent.html(question[0]);
                }

                question.yes = $('[data-role="question.yes"]', questionParent);
                question.close = function () {
                    question.remove();
                };

                question.yes.on('click', function () {
                    var id = question.data('region');

                    question.close();
                    component.select(id);
                });

                $('[data-role="question.close"]', questionParent).on('click', function () {
                    var id = question.data('region');

                    question.close();
                    component.select(id);
                });

                regions.on('click', function () {
                    var region = $(this);
                    var id = region.data('region');

                    component.select(id);
                })
            }, {
                'name': '[Component] bitrix:menu (mobile.1) > intec.regionality:regions.select (.default)',
                'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
                'loader': {
                    'name': 'default',
                    'options': {
                        'await': [
                            'composite'
                        ]
                    }
                }
            });
        </script>
    <?php $oFrame->beginStub() ?>
    <?php $oFrame->end() ?>
<?= Html::endTag('div') ?>