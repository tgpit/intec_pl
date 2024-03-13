<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 */

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

if (!$arResult['FORMS'][0]['SHOW'] && !$arResult['FORMS'][1]['SHOW'])
    return;

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-form',
        'c-form-template-4'
    ]
]) ?>
    <div class="widget-wrapper intec-content intec-content-visible">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <div class="widget-wrapper-3">
                <div class="intec-grid intec-grid-wrap intec-grid-a-h-center intec-grid-i-12 intec-grid-a-v-center">
                    <div class="intec-grid-item intec-grid-item-900-auto intec-grid-item-600-1 intec-grid-item-shrink-1 widget-text">
                        <div class="widget-title">
                            <?= $arResult['TITLE']['TEXT'] ?>
                        </div>
                        <?php if ($arResult['DESCRIPTION']['SHOW']) { ?>
                            <div class="widget-description">
                                <?= $arResult['DESCRIPTION']['TEXT'] ?>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if ($arResult['IMAGE']['SHOW']) { ?>
                        <div class="intec-grid-item-auto widget-image">
                            <?= Html::img($arResult['LAZYLOAD']['USE'] ? $arResult['LAZYLOAD']['STUB'] : $arResult['IMAGE']['SRC'], [
                                'loading' => 'lazy',
                                'data' => [
                                    'lazyload-use' => $arResult['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arResult['LAZYLOAD']['USE'] ? $arResult['IMAGE']['SRC'] : null
                                ]
                            ]) ?>
                        </div>
                    <?php } ?>
                    <div class="intec-grid-item intec-grid-item-900-auto intec-grid-item-shrink-1">
                        <div class="widget-consultant">
                            <div class="widget-consultant-name">
                                <?= $arResult['CONSULTANT']['NAME'] ?>
                            </div>
                            <div class="widget-consultant-post">
                                <?= $arResult['CONSULTANT']['POST'] ?>
                            </div>
                            <div class="widget-buttons">
                                <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-a-h-700-center intec-grid-i-h-3 intec-grid-i-v-5">
                                    <?php if ($arResult['FORMS'][0]['SHOW']) { ?>
                                        <div class="widget-button-wrap intec-grid-item-auto intec-grid-item-600-1">
                                            <?= Html::tag('div', $arResult['FORMS'][0]['BUTTON'], [
                                                'class' => [
                                                    'widget-button',
                                                    'intec-ui' => [
                                                        '',
                                                        'control-button',
                                                        'scheme-current'
                                                    ]
                                                ],
                                                'data-role' => 'form.button.1'
                                            ]) ?>
                                        </div>
                                    <?php } ?>
                                    <?php if ($arResult['FORMS'][0]['SHOW'] && $arResult['FORMS'][1]['SHOW']) { ?>
                                        <div class="widget-button-or intec-grid-item-auto">
                                            <?= Loc::getMessage('C_MAIN_FORM_TEMPLATE_4_OR') ?>
                                        </div>
                                    <?php } ?>
                                    <?php if ($arResult['FORMS'][1]['SHOW']) { ?>
                                        <div class="widget-button-wrap intec-grid-item-auto intec-grid-item-600-1">
                                            <?= Html::tag('div', $arResult['FORMS'][1]['BUTTON'], [
                                                'class' => [
                                                    'widget-button',
                                                    'intec-ui' => [
                                                        '',
                                                        'control-button',
                                                        'scheme-current'
                                                    ]
                                                ],
                                                'data-role' => 'form.button.2'
                                            ]) ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>
