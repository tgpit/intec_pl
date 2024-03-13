<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;

if (!Loader::includeModule('intec.core'))
    return;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 */

$arSvg = [
    'DATE_ICON' => FileHelper::getFileData(__DIR__.'/images/date_icon.svg')
];

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];
$arVisual['TIMER']['SECONDS']['SHOW'] = true;
$sTimerStatus = $arVisual['TIMER']['SECONDS']['SHOW'] ? 'true' : 'false';
$sLink = $arResult['ITEM']['DETAIL_PAGE_URL'];

?>
<div class="widget c-widget c-widget-catalog-shares-1" id="<?= $sTemplateId ?>">
    <div class="widget-header">
        <?= $arResult['ITEM']['NAME'] ?>
    </div>
    <div class="widget-content">
        <?php if ($arVisual['STATUS']['SHOW']) { ?>
            <div class="shares-status intec-grid intec-grid-a-v-center">
                <?php if ($arVisual['DISCOUNT']['SHOW']) { ?>
                    <div class="shares-status-item shares-status-item-discount">
                        <?= $arVisual['DISCOUNT']['VALUE'] ?>
                    </div>
                <?php } ?>
                <?php if ($arVisual['TIMER']['SHOW']) { ?>
                    <div class="shares-status-item shares-status-item-timer" data-status="<?= $sTimerStatus ?>">
                        <span class="shares-timer-day-value" data-role="days"></span>
                        <span class="shares-timer-day-title">
                            <?= Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_TIMER_DAYS_TITLE')?>
                        </span>
                        <span class="shares-delimiter"> : </span>
                        <span class="shares-timer-hour-value" data-role="hours"></span>
                        <span class="shares-timer-hour-title">
                            <?= Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_TIMER_HOURS_TITLE')?>
                        </span>
                        <span class="shares-delimiter"> : </span>
                        <span class="shares-timer-minute-value" data-role="minutes"></span>
                        <span class="shares-timer-minute-title">
                            <?= Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_TIMER_MINUTES_TITLE')?>
                        </span>
                        <?php if ($arVisual['TIMER']['SECONDS']['SHOW']) { ?>
                            <span class="shares-delimiter"> : </span>
                            <span class="shares-timer-second-value" data-role="seconds"></span>
                            <span class="shares-timer-second-title">
                                <?= Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_TIMER_SECONDS_TITLE')?>
                            </span>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if ($arVisual['DATE']['SHOW']) { ?>
                    <div class="shares-status-item shares-status-item-date intec-grid intec-grid-a-v-center">
                        <div class="shares-status-item-date-icon">
                            <?= $arSvg['DATE_ICON'] ?>
                        </div>
                        <div class="shares-status-item-date-text">
                            <?= $arVisual['DATE']['VALUE'] ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <?= Html::tag('div', $arVisual['TEXT']['VALUE'], [
            'class' => Html::cssClassFromArray([
                'shares-text' => true,
                'scrollbar-outer' => $arVisual['TEXT']['ALL']
            ], true),
            'data' => [
                'role' => $arVisual['TEXT']['ALL'] ? 'scrollbar' : null
            ]
        ]) ?>
        <?php if ($arVisual['BUTTON']['SHOW'] && !empty($sLink)) { ?>
            <div class="shares-button-wrapper">
                <a class="shares-button intec-ui intec-ui-control-button intec-ui-mod-round-2 intec-ui-scheme-current" href="<?= $sLink ?>">
                    <?= $arVisual['BUTTON']['VALUE']?>
                </a>
            </div>
        <?php } ?>
    </div>
</div>
<?php if ($arVisual['TIMER']['SHOW']) { ?>
    <?php include(__DIR__.'/parts/script.php'); ?>
<?php } ?>