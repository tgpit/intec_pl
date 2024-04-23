<?php require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\AdminNotify;

global $APPLICATION;

Loc::loadMessages(__FILE__);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client_partner.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if (!CModule::IncludeModule("intec.core") || !class_exists('intec\core\AdminNotify'))
    return;

Core::$app->web->css->addFile(Core::getAlias('@intec/core/resources/css/notify.css'));

$APPLICATION->SetTitle(Loc::getMessage('title'));
$notifier = new AdminNotify();
$notifier->setDateFormat();
$notifier->sortByActivity();

$monthsList = [
    Loc::getMessage('title.month'),
    Loc::getMessage('title.month.few'),
    Loc::getMessage('title.month.many'),
];

$daysList = [
    Loc::getMessage('title.day'),
    Loc::getMessage('title.day.few'),
    Loc::getMessage('title.day.many'),
];

?>
<div class="intec-notify">
    <?php if ($notifier->getErrors() !== false && !empty($notifier->getErrors())) { ?>
        <div class="fragment-errors">
            <?= $notifier->getErrors() ?>
        </div>
    <?php } else { ?>
        <div class="fragment-description">
            <?= Loc::getMessage('description') ?>
        </div>
        <div class="fragment-items">
            <?php foreach ($notifier->getModules() as $key => $module) { ?>
                <?php

                    $days = '-';
                    $link = 'https://intecweb.ru/pay_online/?SECTION_ID=362#prodlenie';

                    if (!empty($module['DAYS_LEFT'])) {
                        if ($module['DAYS_LEFT'] > 30) {
                            $days = $notifier->getDeclination(intdiv($module['DAYS_LEFT'], 30), $monthsList);
                        } else {
                            $days = $notifier->getDeclination($module['DAYS_LEFT'], $daysList);
                        }
                    }

                    if ($key === 'bitrix') {
                        $link = 'https://intecweb.ru/pay_online/?SECTION_ID=365#prodlenie-litsenzii-1s-bitriks';
                    }
                ?>
                <div class="fragment-item">
                    <div class="fragment-item-picture" data-icon="<?= $key ?>"></div>
                    <div class="fragment-item-name">
                        <?= $module['NAME'] ?>
                    </div>
                    <div class="fragment-item-date-from">
                        <div class="fragment-item-row-title">
                            <?= Loc::getMessage('title.date.from') ?>
                        </div>
                        <div class="fragment-item-row-value">
                            <?= $module['DATE_FROM'] ?: "-" ?>
                        </div>
                    </div>
                    <div class="fragment-item-days">
                        <div class="fragment-item-row-title">
                            <?= Loc::getMessage('title.date.days') ?>
                        </div>
                        <div class="fragment-item-row-value">
                            <?= $days ?>
                        </div>
                    </div>
                    <div class="fragment-item-licence">
                        <div class="fragment-item-row-title">
                            <?= Loc::getMessage('title.date.licence') ?>
                        </div>
                        <div class="fragment-item-row-value">
                            <?php if ($module['UPDATE_END'] !== 'Y') { ?>
                                <div class="fragment-item-licence-state active">
                                    <div class="fragment-item-licence-state-dot"></div>
                                    <div class="fragment-item-licence-state-description">
                                        <?= Loc::getMessage('title.date.licence.active') ?>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="fragment-item-licence-state deactive">
                                    <div class="fragment-item-licence-state-dot"></div>
                                    <div class="fragment-item-licence-state-description">
                                        <?= Loc::getMessage('title.date.licence.deactive') ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="fragment-item-date-to">
                        <div class="fragment-item-row-title">
                            <?= Loc::getMessage('title.date.to') ?>
                        </div>
                        <div class="fragment-item-row-value">
                            <?= $module['DATE_TO'] ?: "-" ?>
                        </div>
                    </div>
                    <div class="fragment-item-extend">
                        <?php if ($module['FREE_MODULE'] !== 'Y' && $module['UPDATE_END'] === 'Y' && $module['DAYS_LEFT'] <= 0) { ?>
                            <a class="fragment-item-extend-button danger" href="<?= $link ?>" target="_blank">
                                <?= Loc::getMessage('title.extend') ?>
                            </a>
                        <?php } elseif ($module['FREE_MODULE'] !== 'Y' && $module['UPDATE_END'] !== 'Y' && $module['DAYS_LEFT'] < 14) { ?>
                            <a class="fragment-item-extend-button warning" href="<?= $link ?>" target="_blank">
                                <?= Loc::getMessage('title.extend') ?>
                            </a>
                        <?php } elseif ($key !== 'bitrix') { ?>
                            <a class="fragment-item-extend-link" href="https://marketplace.1c-bitrix.ru/solutions/<?= $key ?>/#tab-log" target="_blank">
                                <?= Loc::getMessage('title.whats.up') ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); ?>
