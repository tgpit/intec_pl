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
        'c-widget-form-5'
    ]
]) ?>
    <?php if (!$arVisual['WIDE']) { ?>
    <div class="intec-content">
        <div class="intec-content-wrapper">
    <?php } ?>
        <div class="widget-form" data-borders="<?= $arVisual['BORDERS'] ?>">
            <div class="intec-content">
                <div class="intec-content-wrapper">
                    <div class="widget-form-wrapper">
                        <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-h-24 intec-grid-i-v-20">
                            <div class="intec-grid-item-auto intec-grid-item-768-1">
                                <div class="widget-form-image"></div>
                                <div class="widget-form-title intec-cl-border">
                                    <?php if (!empty($arResult['DATA']['TITLE'])) { ?>
                                        <?= $arResult['DATA']['TITLE'] ?>
                                    <?php } else { ?>
                                        <?= Loc::getMessage('C_WIDGET_FORM_5_TITLE') ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="intec-grid-item intec-grid-item-768-1">
                                <div class="widget-form-description">
                                    <?php if (!empty($arResult['DATA']['DESCRIPTION'])) { ?>
                                        <?= $arResult['DATA']['DESCRIPTION'] ?>
                                    <?php } else { ?>
                                        <?= Loc::getMessage('C_WIDGET_FORM_5_DESCRIPTION') ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="intec-grid intec-grid-a-h-center intec-grid-item-auto intec-grid-item-768-1">
                                <?= Html::beginTag('div', [
                                    'class' => [
                                        'widget-form-button',
                                        'intec-grid-item-auto',
                                        'intec-ui' => [
                                            '',
                                            'control-button',
                                            'size-2',
                                            'mod-round-3',
                                            'scheme-current',
                                        ]
                                    ],
                                    'data-role' => 'form'
                                ]) ?>
                                    <?php if (!empty($arResult['DATA']['BUTTON']['TEXT'])) { ?>
                                        <?= $arResult['DATA']['BUTTON']['TEXT'] ?>
                                    <?php } else { ?>
                                        <?= Loc::getMessage('C_WIDGET_FORM_5_BUTTON_TEXT') ?>
                                    <?php } ?>
                                <?= Html::endTag('div') ?>
                            </div>
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
