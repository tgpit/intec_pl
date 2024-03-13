<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
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
        'c-widget-form-6'
    ]
]) ?>
    <?php if (!$arVisual['WIDE']) { ?>
    <div class="intec-content">
        <div class="intec-content-wrapper">
    <?php } ?>
        <div class="widget-form intec-cl-background" data-borders="<?= $arVisual['BORDERS'] ?>">
            <div class="intec-content widget-form-wrapper">
                <div class="intec-content-wrapper widget-form-wrapper-2">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-h-24">
                        <div class="widget-form-title-wrap intec-grid-item-auto intec-grid-item-768-1">
                            <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-a-h-center">
                                <div class="intec-grid-item-auto">
                                    <div class="widget-form-image"></div>
                                </div>
                                <div class="intec-grid-item-auto intec-grid-item-768-1">
                                    <div class="widget-form-title">
                                        <?php if (!empty($arResult['DATA']['TITLE'])) { ?>
                                            <?= $arResult['DATA']['TITLE'] ?>
                                        <?php } else { ?>
                                            <?= Loc::getMessage('C_WIDGET_FORM_6_TITLE') ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="widget-form-description-wrap intec-grid-item">
                            <div class="widget-form-description">
                                <?php if (!empty($arResult['DATA']['DESCRIPTION'])) { ?>
                                    <?= $arResult['DATA']['DESCRIPTION'] ?>
                                <?php } else { ?>
                                    <?= Loc::getMessage('C_WIDGET_FORM_6_DESCRIPTION') ?>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="widget-form-button-wrap intec-grid-item-4 intec-grid-item-768-1">
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'widget-form-button',
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'scheme-current',
                                        'size-4',
                                        'mod-transparent',
                                        'mod-round-2'
                                    ]
                                ],
                                'data-role' => 'form'
                            ]) ?>
                                <?php if (!empty($arResult['DATA']['BUTTON']['TEXT'])) { ?>
                                    <?= $arResult['DATA']['BUTTON']['TEXT'] ?>
                                <?php } else { ?>
                                    <?= Loc::getMessage('C_WIDGET_FORM_6_BUTTON_TEXT') ?>
                                <?php } ?>
                            <?= Html::endTag('div') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php if (!$arVisual['WIDE']) { ?>
        </div>
    </div>
    <?php } ?>
<?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>
