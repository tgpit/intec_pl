<?
namespace Acrit\Core\Orders;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Log,
	\Bitrix\Main\Page\Asset,
	\Acrit\Core\Cli;

Loc::loadMessages(__FILE__);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

Asset::getInstance()->addString('<style>
.run-disabled { pointer-events: none; cursor: default; color: #888; }
#tr_sync_man_run a.adm-btn.adm-btn-save { margin-left: 0; }
</style>');

$addSyncType = $obPlugin->getAddSyncType();

$obTabControl->AddSection('HEADING_SYNC_MAN', Loc::getMessage('ACRIT_CRM_TAB_SYNC_HEADING'));
$obTabControl->BeginCustomField('PROFILE[SYNC][man]', Loc::getMessage('ACRIT_CRM_TAB_SYNC_MAN_TITLE'));
?>
	<tr id="tr_sync_man_params">
		<td>
			<label for="field_sync_man_params"><?=$obTabControl->GetCustomLabelHTML()?><label>
		</td>
		<td>
            <select name="PROFILE[SYNC][man][period]" id="field_sync_man_period">
                <option value=""<?=$arProfile['SYNC']['man']['period']==''?' selected':'';?>><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_MAN_TERM_ALL');?></option>
                <option value="3m"<?=$arProfile['SYNC']['man']['period']=='3m'?' selected':'';?>><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_MAN_TERM_3M');?></option>
                <option value="1m"<?=$arProfile['SYNC']['man']['period']=='1m'?' selected':'';?>><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_MAN_TERM_1M');?></option>
                <option value="1w"<?=$arProfile['SYNC']['man']['period']=='1w'?' selected':'';?>><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_MAN_TERM_1W');?></option>
                <option value="1d"<?=$arProfile['SYNC']['man']['period']=='1d'?' selected':'';?>><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_MAN_TERM_1D');?></option>
            </select>
            <p>
                <input type="checkbox" name="PROFILE[SYNC][man][only_new]" value="y"<?=$arProfile['SYNC']['man']['only_new']=='y'?' checked':'';?> id="field_sync_man_only_new" />
                <label for="field_sync_man_only_new"><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_MAN_ONLY_NEW');?></label>
            </p>
		</td>
	</tr>
	<?
$obTabControl->EndCustomField('PROFILE[SYNC][man]');
$obTabControl->BeginCustomField('PROFILE[SYNC_MAN_RUN]', Loc::getMessage('ACRIT_CRM_TAB_SYNC_MAN_RUN_TITLE'));
?>
	<tr id="tr_sync_man_run">
		<td>
			<label for="field_sync_man_run"><?=$obTabControl->GetCustomLabelHTML()?><label>
		</td>
		<td>
			<?if ($obPlugin->isCountable()):?>
            <a href="#" class="adm-btn adm-btn-save" id="man_sync_start" style="margin-bottom: 4px;"><?=GetMessage("ACRIT_EXP_RUNNOW_START")?></a>
            <a href="#" class="adm-btn adm-btn-disabled" id="man_sync_stop" style="margin-bottom: 4px;"><?=GetMessage("ACRIT_EXP_RUNNOW_STOP")?></a>
            <div id="start_export_progress">
                <div class="adm-info-message-wrap adm-info-message-gray">
                    <div class="adm-info-message">
                        <div class="adm-progress-bar-outer" style="width: 500px;">
                            <div class="adm-progress-bar-inner" style="width: 400px;">
                                <div class="adm-progress-bar-inner-text" style="width: 500px;">10%</div>
                            </div><span class="adm-progress-bar-outer-text">10%</span>
                        </div>
                        <div class="adm-info-message-buttons"></div>
                    </div>
                </div>
            </div>
            <div class="mansync-result" id="man_sync_result">
                <div class="mansync-result-all"><?=GetMessage("ACRIT_CRM_TAB_SYNC_MAN_ALL")?> <span>0</span></div>
                <div class="mansync-result-done"><?=GetMessage("ACRIT_CRM_TAB_SYNC_MAN_DONE")?> <span>0</span></div>
            </div>
            <?else:?>
            <a href="#" class="adm-btn adm-btn-save" id="man_sync_noprogress_start" style="margin-bottom: 4px;"><?=GetMessage("ACRIT_EXP_RUNNOW_START")?></a>
            <a href="#" class="adm-btn adm-btn-disabled" id="man_sync_stop" style="margin-bottom: 4px;"><?=GetMessage("ACRIT_EXP_RUNNOW_STOP")?></a><br /><br />
            <div class="acrit-crm-man-sync-result">
                <div class="acrit-crm-man-sync-result-row" id="man_sync_result_count"><?=GetMessage("ACRIT_CRM_TAB_SYNC_MAN_DONE_2")?> <span>0</span></div>
            </div>
			<?endif;?>
        </td>
	</tr>
	<?
$obTabControl->EndCustomField('PROFILE[SYNC_MAN_RUN]');

$obTabControl->AddSection('HEADING_SYNC_RANGE', Loc::getMessage('ACRIT_CRM_TAB_SYNC_RANGE_TITLE'));

$obTabControl->BeginCustomField('PROFILE[SYNC][add][range]', Loc::getMessage('ACRIT_CRM_TAB_SYNC_RANGE'));
if ($addSyncType == Plugin::ADD_SYNC_TYPE_SINGLE):
	?>
    <tr id="tr_sync_add_range">
        <td>
	        <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_SYNC_RANGE_HINT'));?>
            <label for="field_sync_add_range"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <input type="text" name="PROFILE[SYNC][add][range]" id="field_sync_add_range" value="<?=$arProfile['SYNC']['add']['range'] ? : $obPlugin::ADD_SYNC_DEFAULT_RANGE_1;?>" placeholder="<?=$obPlugin::ADD_SYNC_DEFAULT_RANGE_1;?>" />
        </td>
    </tr>
<?
elseif ($addSyncType == Plugin::ADD_SYNC_TYPE_DUAL):
	$arValues[1]['range'] = $arProfile['SYNC']['add']['1']['range'] ? : $obPlugin::ADD_SYNC_DEFAULT_RANGE_1;
	$arValues[2]['range'] = $arProfile['SYNC']['add']['2']['range'] ? : $obPlugin::ADD_SYNC_DEFAULT_RANGE_2;
	?>
    <tr id="tr_sync_add_range">
        <td>
	        <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_SYNC_RANGE_1_HINT'));?>
            <label for="field_sync_add_range"><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_RANGE_1');?><label>
        </td>
        <td>
            <input type="text" name="PROFILE[SYNC][add][1][range]" id="field_sync_add_1_range" value="<?=$arValues[1]['range'];?>" placeholder="<?=$obPlugin::ADD_SYNC_DEFAULT_RANGE_1;?>" /> <?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_AGENTS_MINUTES');?>
        </td>
    </tr>
    <tr id="tr_sync_add_range">
        <td>
	        <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_SYNC_RANGE_2_HINT'));?>
            <label for="field_sync_add_range"><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_RANGE_2');?><label>
        </td>
        <td>
            <input type="text" name="PROFILE[SYNC][add][2][range]" id="field_sync_add_2_range" value="<?=$arValues[2]['range'];?>" placeholder="<?=$obPlugin::ADD_SYNC_DEFAULT_RANGE_2;?>" /> <?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_AGENTS_HOURS');?>
        </td>
    </tr>
<?
endif;
$obTabControl->EndCustomField('PROFILE[SYNC][add][range]');

$obTabControl->AddSection('HEADING_SYNC_CRON', Loc::getMessage('ACRIT_CRM_TAB_SYNC_CRON_TITLE'));

$obTabControl->BeginCustomField('PROFILE[SYNC][add][cron]', Loc::getMessage('ACRIT_CRM_TAB_SYNC_AGENTS_PERIOD'));
?>
<tr id="tr_sync_add_active">
    <td class="adm-detail-content-cell-r" colspan="2">
<?
if ($addSyncType == Plugin::ADD_SYNC_TYPE_SINGLE):
	$arCli = Cli::getFullCommand($strModuleId, 'export.php', $intProfileID, Log::getInstance($strModuleId)->getLogFilename($intProfileID), ['variant' => 1]);
	$arCommands[] = [
        'TITLE' => Loc::getMessage('ACRIT_CRM_TAB_SYNC_CRON_PERIOD'),
        'COMMAND' => $arCli['COMMAND'],
    ];
elseif ($addSyncType == Plugin::ADD_SYNC_TYPE_DUAL):
	$arCli = Cli::getFullCommand($strModuleId, 'export.php', $intProfileID, Log::getInstance($strModuleId)->getLogFilename($intProfileID), ['variant' => 1]);
	$arCommands[] = [
		'TITLE' => Loc::getMessage('ACRIT_CRM_TAB_SYNC_CRON_PERIOD_1'),
		'COMMAND' => $arCli['COMMAND'],
	];
	$arCli = Cli::getFullCommand($strModuleId, 'export.php', $intProfileID, Log::getInstance($strModuleId)->getLogFilename($intProfileID), ['variant' => 2]);
	$arCommands[] = [
		'TITLE' => Loc::getMessage('ACRIT_CRM_TAB_SYNC_CRON_PERIOD_2'),
		'COMMAND' => $arCli['COMMAND'],
	];
endif;
	?>
    <?foreach ($arCommands as $strCommand):?>
    <label for="field_sync_add_active"><?=$strCommand['TITLE'];?>:<label>
    <div class="acrit-core-cron-form-command">
        <code id="acrit-core-cron-command-copy"><?=$strCommand['COMMAND'];?></code>
        <a href="javascript:void(0)" class="acrit-core-cron-form-command-copy acrit-inline-link"
            data-role="acrit-core-cron-command-copy" data-message="<?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_CRON_COPY_SUCCESS');?>">
			<?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_CRON_COPY');?>
        </a>
        <span></span>
    </div>
    <br />
    <?endforeach;?>
    <div style="margin-top:4px;"><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_CRON_WARNING');?></div>
	<?if(isset($_SERVER['BITRIX_VA_VER'])):?>
    <div style="margin-top:4px;"><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_CRON_LINK_ARTICLE_BITRIX_ENV');?></div>
    <?endif?>
    </td>
</tr>
<?
$obTabControl->EndCustomField('PROFILE[SYNC][add][cron]');

//$obTabControl->BeginCustomField('PROFILE[SYNC][add_active]', Loc::getMessage('ACRIT_CRM_TAB_SYNC_AGENTS_ACTIVE'));
//?>
<!--    <tr id="tr_sync_add_active">-->
<!--        <td>-->
<!--        </td>-->
<!--        <td>-->
<!--            <input type="checkbox" name="PROFILE[SYNC][add_active]" id="field_sync_add_active" value="Y"--><?//=$arProfile['SYNC']['add_active']=='Y'?' checked':'';?><!-- />-->
<!--            <label for="field_sync_add_active">--><?//=$obTabControl->GetCustomLabelHTML()?><!--<label>-->
<!--        </td>-->
<!--    </tr>-->
<!--	--><?//
//$obTabControl->EndCustomField('PROFILE[SYNC][add_active]');

$obTabControl->AddSection('HEADING_SYNC_AGENTS', Loc::getMessage('ACRIT_CRM_TAB_SYNC_AGENTS_TITLE'));

$obTabControl->BeginCustomField('PROFILE[SYNC][add_active]', Loc::getMessage('ACRIT_CRM_TAB_SYNC_AGENTS_ACTIVE'));
?>
<tr id="tr_sync_add_active">
    <td>
    </td>
    <td>
        <input type="checkbox" name="PROFILE[SYNC][add_active]" id="field_sync_add_active" value="Y"<?=$arProfile['SYNC']['add_active']=='Y'?' checked':'';?> />
        <label for="field_sync_add_active"><?=$obTabControl->GetCustomLabelHTML()?><label>
    </td>
</tr>
<?
$obTabControl->EndCustomField('PROFILE[SYNC][add_active]');

$obTabControl->BeginCustomField('PROFILE[SYNC][add][period]', Loc::getMessage('ACRIT_CRM_TAB_SYNC_AGENTS_PERIOD'));
if ($addSyncType == Plugin::ADD_SYNC_TYPE_SINGLE):
?>
	<tr id="tr_sync_add_period">
		<td>
			<label for="field_sync_add_period"><?=$obTabControl->GetCustomLabelHTML()?><label>
		</td>
		<td>
			<input type="text" name="PROFILE[SYNC][add][period]" id="field_sync_add_period" value="<?=$arProfile['SYNC']['add']['period'] ? : $obPlugin::ADD_SYNC_DEFAULT_PERIOD_1;?>" placeholder="<?=$obPlugin::ADD_SYNC_DEFAULT_PERIOD_1;?>" />
		</td>
	</tr>
<?
elseif ($addSyncType == Plugin::ADD_SYNC_TYPE_DUAL):
	$arValues[1]['period'] = $arProfile['SYNC']['add']['1']['period'] ? : $obPlugin::ADD_SYNC_DEFAULT_PERIOD_1;
	$arValues[2]['period'] = $arProfile['SYNC']['add']['2']['period'] ? : $obPlugin::ADD_SYNC_DEFAULT_PERIOD_2;
?>
    <tr id="tr_sync_add_period">
        <td>
            <label for="field_sync_add_period"><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_AGENTS_PERIOD_1');?><label>
        </td>
        <td>
            <input type="text" name="PROFILE[SYNC][add][1][period]" id="field_sync_add_1_period" value="<?=$arValues[1]['period'];?>" placeholder="<?=$obPlugin::ADD_SYNC_DEFAULT_PERIOD_1;?>" /> <?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_AGENTS_MINUTES');?>
            <input type="hidden" name="PROFILE[SYNC][add][1][measure]" value="m" />
        </td>
    </tr>
    <tr id="tr_sync_add_period">
        <td>
            <label for="field_sync_add_period"><?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_AGENTS_PERIOD_2');?><label>
        </td>
        <td>
            <input type="text" name="PROFILE[SYNC][add][2][period]" id="field_sync_add_2_period" value="<?=$arValues[2]['period'];?>" placeholder="<?=$obPlugin::ADD_SYNC_DEFAULT_PERIOD_2;?>" /> <?=Loc::getMessage('ACRIT_CRM_TAB_SYNC_AGENTS_HOURS');?>
            <input type="hidden" name="PROFILE[SYNC][add][2][measure]" value="h" />
        </td>
    </tr>
<?
endif;
$obTabControl->EndCustomField('PROFILE[SYNC][add][period]');
?>