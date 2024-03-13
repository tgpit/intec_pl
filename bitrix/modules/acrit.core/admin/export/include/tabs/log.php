<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

$strLogPreview = Log::getInstance($strModuleId)->getLogPreview($intProfileID);

$strTextareaStyle = '';
if(!strlen($strLogPreview)){
	$strTextareaStyle .= 'height:20px;';
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Log
$obTabControl->BeginCustomField('PROFILE[LOG]', Loc::getMessage('ACRIT_EXP_TAB_LOG_LOG'));
?>
	<tr class="heading" id="tr_LOG_HEADING">
		<td colspan="2"><?=$obTabControl->GetCustomLabelHTML()?></td>
	</tr>
	<tr id="tr_LOG">
		<td>
			<div class="acrit-exp-log-wrapper">
				<div data-role="profile-log-export-file-name-hidden" style="display:none">
					<?if(is_object($obPlugin)):?>
						<?=$obPlugin->showFileOpenLink(false, true, true);?>
					<?endif?>
				</div>
				<?=Log::getInstance($strModuleId)->showLog($intProfileID);?>
			</div>
			<div>
				<input type="hidden" name="PROFILE[PARAMS][LOG_DEBUG_MODE]" value="N" />
				<label>
					<input type="checkbox" name="PROFILE[PARAMS][LOG_DEBUG_MODE]" value="Y"
						<?if($arProfile['PARAMS']['LOG_DEBUG_MODE'] == 'Y'):?>checked<?endif?> />
					<span><?=Loc::getMessage('ACRIT_EXP_TAB_LOG_DEBUG_MODE');?></span>
					<?=Helper::showHint(Loc::getMessage('ACRIT_EXP_TAB_LOG_DEBUG_MODE_HINT'));?>
				</label>
			</div>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[LOG]');

// History
$obTabControl->BeginCustomField('PROFILE[HISTORY]', Loc::getMessage('ACRIT_EXP_TAB_LOG_HISTORY'));
?>
	<tr>
		<td colspan="2"><br/></td>
	</tr>
	<tr class="heading" id="tr_HISTORY_HEADING">
		<td colspan="2"><?=$obTabControl->GetCustomLabelHTML()?></td>
	</tr>
	<tr id="tr_HISTORY">
		<td>
			<?require __DIR__.'/_log_history.php';?>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[HISTORY]');

if(is_object($obPlugin)) {
	$strContent = $obPlugin->getLogContent($strLogCustomTitle, $arGet);
	if(strlen($strContent)){
		$obTabControl->BeginCustomField('PROFILE[LOG_CUSTOM]', $strLogCustomTitle);
		?>
			<tr>
				<td colspan="2"><br/></td>
			</tr>
			<tr class="heading" id="tr_LOG_CUSTOM_HEADING">
				<td colspan="2"><?=$obTabControl->GetCustomLabelHTML()?></td>
			</tr>
			<tr id="tr_LOG_CUSTOM">
				<td>
					<?=$strContent;?>
				</td>
			</tr>
		<?
		$obTabControl->EndCustomField('PROFILE[LOG_CUSTOM]');
	}
}

// Email
$obTabControl->BeginCustomField('PROFILE[EMAIL]', Loc::getMessage('ACRIT_EXP_TAB_LOG_EMAIL'));
?>
	<tr>
		<td colspan="2"><br/></td>
	</tr>
	<tr class="heading" id="tr_EMAIL_HEADING">
		<td colspan="2"><?=$obTabControl->GetCustomLabelHTML()?></td>
	</tr>
	<tr>
		<td colspan="2">
			<?require __DIR__.'/_log_email.php';?>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('PROFILE[EMAIL]');

