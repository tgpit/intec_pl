<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\bitrix\Component;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Html;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this, $arResult['VISUAL']['RANDOMIZE']));

$arVisual = $arResult['VISUAL'];
$bShowSeconds = $arVisual['BLOCKS']['SECONDS'];
$bQuantityShow = $arVisual['QUANTITY']['SHOW'];
$sStatus = $arResult['DATE']['STATUS'];
$sDataStatus = empty($sStatus) ? 'enable' : 'disable';

?>
<?= Html::beginTag('div', [
    'class' => [
        'widget',
        'c-product-timer' => [
            '',
            'template-1'
        ]
    ],
    'id' => $sTemplateId,
    'data' => [
        'role' => 'timer',
        'status' => $sDataStatus
    ]
]) ?>
    <?php if ($sStatus === 'incorrect') { ?>
        <?= Html::tag('div', Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_1_INCORRECT_DATE'), [
            'class' => [
                'intec-ui' => [
                    '',
                    'control-alert',
                    'scheme-current',
                    'm-b-20'
                ]
            ]
        ]) ?>
    <?php } else if (empty($sStatus) || ($sStatus === 'passed' && !$arResult['DATA']['TIMER']['ZERO'])) { ?>
        <?php if ($arVisual['TITLE']['SHOW']) { ?>
            <div class="widget-title">
                <?= $arVisual['TITLE']['VALUE'] ?>
            </div>
        <?php } ?>
        <div class="widget-content" data-role="timer.content">
            <?= Html::tag('div', trim($arResult['DATE']['END']), [
                'class' => 'widget-date-end',
                'data' => [
                    'role' => 'date-end',
                    'value' => trim($arResult['DATE']['END'])
                ]
            ]) ?>
            <div class="intec-grid intec-grid-wrap intec-grid-a-v-end intec-grid-i-4">
                <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                    <div class="widget-time-wrapper">
                        <?php if ($arVisual['HEADER']['SHOW']) { ?>
                            <div class="widget-time-header">
                                <?= $arVisual['HEADER']['VALUE'] ?>
                            </div>
                        <?php } ?>
                        <div class="widget-timer-items">
                            <div class="intec-grid intec-grid-wrap intec-grid-a-h-768-center intec-grid-i-2">
                                <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                    <div class="widget-timer-item">
                                        <div class="widget-timer-item-title" data-role="days">
                                            <?= $arResult['DATE']['REMAINING']['DAYS'] ?>
                                        </div>
                                        <div class="widget-timer-item-description" data-role="days.sign">
                                            <?= Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_1_UNTIL_THE_END_' . $arVisual['DAYS']) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="intec-grid-item-auto intec-grid-item-shrink-1 intec-grid intec-grid-i-2">
                                    <?php foreach ($arVisual['CASES'] as $sKey => $sValue) {
                                        if (empty($sValue))
                                            continue;

                                        $sLowerKey = StringHelper::toLowerCase($sKey);
                                    ?>
                                        <?= Html::beginTag('div', [
                                            'class' => [
                                                'intec-grid-item-auto'
                                            ]
                                        ]) ?>
                                            <div class="widget-timer-item">
                                                <?= Html::tag('div', $arResult['DATE']['REMAINING'][$sKey], [
                                                    'class' => 'widget-timer-item-title',
                                                    'data' => [
                                                        'role' => $sLowerKey
                                                    ]
                                                ]) ?>
                                                <?php if ($sKey === 'HOURS' || ($arVisual['BLOCKS']['SECONDS'] && $sKey === 'MINUTES')) { ?>
                                                    <span class="widget-timer-item-title-delimeter">:</span>
                                                <?php } ?>
                                                <?= Html::tag('div', Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_1_UNTIL_THE_END_' . $sValue), [
                                                    'class' => 'widget-timer-item-description',
                                                    'data' => [
                                                        'role' => $sLowerKey . '.sign'
                                                    ]
                                                ]) ?>
                                            </div>
                                        <?= Html::endTag('div') ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($bQuantityShow) { ?>
                    <div class="intec-grid-item-auto">
                        <div class="widget-quantity-wrapper">
                            <?php if ($arVisual['QUANTITY']['HEADER']['SHOW']) { ?>
                                <div class="widget-quantity-header">
                                    <?= $arVisual['QUANTITY']['HEADER']['VALUE'] ?>
                                </div>
                            <?php } ?>
                            <div class="widget-timer-item widget-timer-item-quantity">
                                <div class="widget-timer-item-title" data-role="timer-quantity">
                                    <?= $arResult['DATA']['TIMER']['PRODUCT']['QUANTITY']; ?>
                                </div>
                                <div class="widget-timer-item-description">
                                    <?php if ($arVisual['QUANTITY']['UNITS']['USE'] && !empty($arVisual['QUANTITY']['UNITS']['VALUE'])) { ?>
                                        <?= $arVisual['QUANTITY']['UNITS']['VALUE'] ?>
                                    <?php } else { ?>
                                        <?= Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_1_UNTIL_QUANTITY_VALUE') ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php if (empty($sStatus)) { ?>
            <?php include(__DIR__.'/parts/script.php'); ?>
        <?php } ?>
    <?php } ?>
<?= Html::endTag('div') ?>