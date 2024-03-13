<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this, $arResult['VISUAL']['RANDOMIZE']));

$arVisual = $arResult['VISUAL'];
$bShowSeconds = $arVisual['BLOCKS']['SECONDS'];
$bSaleShow = $arVisual['SALE']['SHOW'];
$sStatus = $arResult['DATE']['STATUS'];
$sDataStatus = empty($sStatus) ? 'enable' : 'disable';

?>
<?= Html::beginTag('div', [
    'class' => [
        'widget',
        'c-product-timer' => [
            '',
            'template-4'
        ]
    ],
    'id' => $sTemplateId,
    'data' => [
        'role' => 'timer',
        'status' => $sDataStatus
    ]
]) ?>
    <?php if ($sStatus === 'incorrect') { ?>
        <?= Html::tag('div', Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_4_INCORRECT_DATE'), [
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
        <div class="widget-content" data-role="timer.content">
            <?= Html::tag('div', trim($arResult['DATE']['END']), [
                'class' => 'widget-date-end',
                'data' => [
                    'role' => 'date-end',
                    'value' => trim($arResult['DATE']['END'])
                ]
            ]) ?>
            <?= Html::beginTag('div', [
                'class' => 'widget-timer-item-time-wrapper',
                'data-seconds' => $bShowSeconds ? 'true' : 'false'
            ]) ?>
                <div class="intec-grid intec-grid-wrap intec-grid-a-v-end intec-grid-i-3">
                    <div class="widget-product-timer-items intec-grid-item-auto">
                        <div class="widget-timer-item-wrapper">
                            <div class="intec-grid intec-grid-i-h-3">
                                <?php foreach ($arVisual['CASES'] as $sKey => $sValue) {
                                    if (empty($sValue))
                                        continue;

                                    $sLowerKey = StringHelper::toLowerCase($sKey);
                                ?>
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'widget-timer-item-block',
                                            'intec-grid-item-auto'
                                        ],
                                        'data-code' => $sLowerKey
                                    ]) ?>
                                        <div class="widget-timer-item-wrapper-2 intec-grid intec-grid-a-h-center intec-grid-a-v-end intec-grid-i-h-2">
                                            <?= Html::beginTag('div', [
                                                'class' => [
                                                    'widget-timer-item-time',
                                                    'intec-grid-item-auto'
                                                ],
                                                'data-role' => $sLowerKey
                                            ]) ?>
                                                <?= $arResult['DATE']['REMAINING'][$sKey] ?>
                                            <?= Html::endTag('div') ?>
                                            <div class="widget-timer-item-description intec-grid-item-auto">
                                                <?= Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_4_UNTIL_THE_END_' . $sValue) ?>
                                            </div>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                    <?php if (
                                            ($arVisual['BLOCKS']['SECONDS'] && $sKey !== 'SECONDS')
                                            || (!$arVisual['BLOCKS']['SECONDS'] && $sKey !== 'MINUTES')
                                    ) { ?>
                                        <div class="widget-timer-item-separator intec-grid-item-auto">:</div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($bSaleShow) { ?>
                        <div class="intec-grid-item-auto">
                            <div class="widget-timer-item-wrapper widget-timer-item-wrapper-sale">
                                <div class="widget-timer-item-block-sale">
                                    <div class="widget-timer-item-sale">
                                        <?= $arVisual['SALE']['VALUE'] ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?= Html::endTag('div') ?>
        </div>
    <?php } ?>
<?= Html::endTag('div') ?>
<?php if (empty($sStatus)) { ?>
    <?php include(__DIR__.'/parts/script.php'); ?>
<?php } ?>