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

$obTabControl->AddSection('HEADING_LOG', Loc::getMessage('ACRIT_CRM_TAB_LOG_TITLE'));

$obTabControl->BeginCustomField('PROFILE[SYNC][log_block]', Loc::getMessage('ACRIT_CRM_TAB_LOG_BLOCK'));
?>
    <tr id="tr_log">
        <td>
        </td>
        <td>
			<?=Log::getInstance($strModuleId, 'crm')->showLog($intProfileID);?>
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[SYNC][log_block]');


/*// History
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
$obTabControl->EndCustomField('PROFILE[HISTORY]');*/
