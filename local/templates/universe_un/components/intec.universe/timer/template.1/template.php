<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

$sStatus = $arResult['DATE']['STATUS'];

?>
<?= Html::beginTag('div', [
    'class' => [
        'widget',
        'c-timer',
        'c-timer-template-1'
    ],
    'id' => $sTemplateId,
    'data' => [
        'role' => 'timer',
        'status' => $sStatus ? 'true' : 'false'
    ]
]) ?>
    <?php if (!$sStatus) { ?>
        <div class="widget-error">
            <?= Loc::getMessage('C_TIMER_TEMPLATE_1_INCORRECT_DATE') ?>
        </div>
    <?php } else { ?>
        <div class="widget-content">
            <div class="widget-timer-wrapper intec-grid">
                <div class="widget-timer-item intec-grid-item-auto">
                    <span data-role="days">
                        <?= $arResult['DATE']['VALUE']['DAYS']?>
                    </span>
                    <span class="widget-timer-item-description">
                        <?= ' '.Loc::getMessage('C_TIMER_TEMPLATE_1_UNTIL_THE_END_DAYS') ?>
                    </span>
                </div>
                <div class="intec-grid-item-auto intec-grid">
                    <div class="widget-timer-item" data-role="hours">
                        <?= $arResult['DATE']['VALUE']['HOURS'] ?>
                    </div>
                    <div class="widget-timer-item intec-grid-item-auto">:</div>
                    <div class="widget-timer-item" data-role="minutes">
                        <?= $arResult['DATE']['VALUE']['MINUTES'] ?>
                    </div>
                    <div class="widget-timer-item intec-grid-item-auto">:</div>
                    <div class="widget-timer-item" data-role="seconds">
                        <?= $arResult['DATE']['VALUE']['SECONDS'] ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
<?= Html::endTag('div') ?>

<?php include(__DIR__.'/parts/script.php'); ?>