<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['FORM']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-widget',
        'c-widget-form-4'
    ]
]) ?>
    <?php if (!$arVisual['WIDE']) { ?>
    <div class="intec-content">
        <div class="intec-content-wrapper">
    <?php } ?>
        <?= Html::beginTag('div', [
            'class' => [
                'widget-form',
                'intec-cl-background'
            ],
            'data-borders' => $arVisual['BORDERS']
        ]) ?>
            <div class="intec-content widget-form-wrapper">
                <div class="intec-content-wrapper widget-form-wrapper-2">
                    <div class="widget-form-title-wrap intec-grid intec-grid-a-v-center intec-grid-a-h-center">
                        <?= Html::tag('div', null, [
                            'class' => 'widget-form-image',
                            'style' => [
                                'background-image' => 'url(\''.$this->GetFolder().'/images/question_mark.png\')'
                            ]
                        ]) ?>
                        <div class="widget-form-title">
                            <div class="widget-form-title-wrapper">
                                <?php if (!empty($arResult['DATA']['TITLE'])) { ?>
                                    <?= $arResult['DATA']['TITLE'] ?>
                                <?php } else { ?>
                                    <?= Loc::getMessage('C_WIDGET_FORM_4_TITLE') ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="widget-form-description-wrap intec-grid intec-grid-a-v-center intec-grid-a-h-center">
                        <div class="widget-form-description">
                            <?php if (!empty($arResult['DATA']['DESCRIPTION'])) { ?>
                                <?= $arResult['DATA']['DESCRIPTION'] ?>
                            <?php } else { ?>
                                <?= Loc::getMessage('C_WIDGET_FORM_4_DESCRIPTION') ?>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="widget-form-button-wrap intec-grid intec-grid-a-v-center intec-grid-a-h-center">
                        <?= Html::beginTag('div', [
                            'class' => [
                                'widget-form-button',
                                'intec-cl-text-hover',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'size-2',
                                    'scheme-white',
                                    'mod-round-2',
                                    'mod-transparent'
                                ]
                            ],
                            'data-role' => 'form'
                        ]) ?>
                            <?php if (!empty($arResult['DATA']['BUTTON']['TEXT'])) { ?>
                                <?= $arResult['DATA']['BUTTON']['TEXT'] ?>
                            <?php } else { ?>
                                <?= Loc::getMessage('C_WIDGET_FORM_4_BUTTON_TEXT') ?>
                            <?php } ?>
                        <?= Html::endTag('div') ?>
                    </div>
                </div>
            </div>
        <?= Html::endTag('div') ?>
    <?php if (!$arVisual['WIDE']) { ?>
        </div>
    </div>
    <?php } ?>
    <?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>
