<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Cli,
	\Acrit\Core\Log,
	\Acrit\Core\Json;

// Core (part 1)
$strCoreId = 'acrit.core';
$strModuleId = $ModuleID = preg_replace('#^.*?/([a-z0-9]+)_([a-z0-9]+).*?$#', '$1.$2', $_SERVER['REQUEST_URI']);
$strModuleCode = preg_replace('#^(.*?)\.(.*?)$#', '$2', $strModuleId);
$strModuleUnderscore = preg_replace('#^(.*?)\.(.*?)$#', '$1_$2', $strModuleId);
define('ADMIN_MODULE_NAME', $strModuleId);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$strModuleId.'/prolog.php');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$strCoreId.'/install/demo.php');
IncludeModuleLangFile(__FILE__);
\CJSCore::Init(['jquery', 'jquery2', 'jquery3', 'fileinput']);
$strModuleCodeLower = toLower($strModuleCode);

// Check rights
$strRight = $APPLICATION->getGroupRight($strModuleId);
if($strRight < 'R'){
	$APPLICATION->authForm(Loc::getMessage('ACCESS_DENIED'));
}

// Input data
$obGet = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList();
$arGet = $obGet->toArray();
$obPost = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList();
$arPost = $obPost->toArray();

// Demo
acritShowDemoExpired($strModuleId);

// Core notice
if(!\Bitrix\Main\Loader::includeModule($strCoreId)){
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
	?><div id="acrit-exp-core-notifier"><?
		print '<div style="margin-top:15px;"></div>';
		print \CAdminMessage::ShowMessage(array(
			'MESSAGE' => Loc::getMessage('ACRIT_EXP_CORE_NOTICE', [
				'#CORE_ID#' => $strCoreId,
				'#LANG#' => LANGUAGE_ID,
			]),
			'HTML' => true,
		));
	?></div><?
	$APPLICATION->SetTitle(Loc::getMessage('ACRIT_EXP_PAGE_TITLE_DEFAULT'));
	die();
}

// Module
\Bitrix\Main\Loader::includeModule($strModuleId);

// Page title
$strPageTitle = Loc::getMessage('ACRIT_EXP_PAGE_TITLE_ADD');

// Get helper data
$arSites = Helper::getSitesList();

// Core (part 2, visual)
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

// Demo
acritShowDemoNotice($strModuleId);

// Params
$strAdminFormName = 'AcritExpGroup';
$strTabParam = $strAdminFormName.'_active_tab';

// Get current group
$intGroupID = IntVal($arGet['ID']);
$arGroup = [];
if($intGroupID>0) {
	$arQuery = [
		'filter' => [
			'ID' => $intGroupID,
		],
		'limit' => 1,
	];
	$arGroup = Helper::call($strModuleId, 'ProfileGroup', 'getList', [$arQuery])->fetch();
	if(!$arGroup){
		LocalRedirect('/bitrix/admin/acrit_'.$strModuleCodeLower.'_new_list.php?lang='.LANGUAGE_ID);
	}
	$strPageTitle = Loc::getMessage('ACRIT_EXP_PAGE_TITLE_EDIT');
}

// Set page title
$APPLICATION->SetTitle($strPageTitle);

// Deleting current group
if($arGet['delete'] == 'Y'){
	Helper::call($strModuleId, 'ProfileGroup', 'delete', [$intGroupID]);
	LocalRedirect('/bitrix/admin/acrit_'.$strModuleCodeLower.'_new_list.php?lang='.LANGUAGE_ID);
}

// Check / get default data
$arGroup['SORT'] = is_numeric($arGroup['SORT']) && $arGroup['SORT']>0 ? $arGroup['SORT'] : 100;
$arGroup['GROUP_ID'] = $arGroup['GROUP_ID'] ?? intVal($arGet['group_id']);

// Default site params
if(!$intGroupID) {
	$arGroup['DOMAIN'] = Helper::getCurrentHost();
	$arGroup['IS_HTTPS'] = Helper::isHttps() ? 'Y' : 'N';
}

// Save form on POST
$bSave = !!strlen($arPost['save']);
$bApply = !!strlen($arPost['apply']);
$bCancel = !!strlen($arPost['cancel']);
if(($bSave || $bApply) && $strRight == 'W'){
	$arGroupFields = $arPost['GROUP'];
	if($intGroupID) {
		$obResult = Helper::call($strModuleId, 'ProfileGroup', 'update', [$intGroupID, $arGroupFields]);
	}
	else {
		$arGroupFields['DATE_CREATED'] = new \Bitrix\Main\Type\DateTime();
		$obResult = Helper::call($strModuleId, 'ProfileGroup', 'add', [$arGroupFields]);
		$intGroupID = $obResult->getID();
	}
	if($obResult->isSuccess()) {
		// Redirect
		if($bApply) {
			$arClearGetParams = array(
				'ID',
				'group_id',
				$strTabParam,
			);
			$strTab = strlen($arPost[$strTabParam]) ? '&'.$strTabParam.'='.$arPost[$strTabParam] : '';
			$strUrl = 'ID='.$intGroupID.$strTab;
			if(is_numeric($arGroupFields['GROUP_ID']) && $arGroupFields['GROUP_ID'] > 0){
				$strUrl .= '&group_id='.$arGroupFields['GROUP_ID'];
			}
			$strUrl = $APPLICATION->getCurPageParam($strUrl, $arClearGetParams);
		}
		else {
			$strUrl = '/bitrix/admin/acrit_'.$strModuleCodeLower.'_new_list.php?lang='.LANGUAGE_ID;
			if(is_numeric($arGroupFields['GROUP_ID']) && $arGroupFields['GROUP_ID'] > 0){
				$strUrl .= '&group_id='.$arGroupFields['GROUP_ID'];
			}
		}
		LocalRedirect($strUrl);
	}
	else {
		$arErrors = $obResult->getErrorMessages();
		print Helper::showError(is_array($arErrors) ? implode('<br/>', $arErrors) : $arErrors);
		$arGroup = $arPost['GROUP'];
	}
}

// Context menu
$arMenu = array();
$arMenu[] = array(
	'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_LIST'),
	'LINK' => 'acrit_'.$strModuleCodeLower.'_new_list.php?lang='.LANGUAGE_ID,
	'ICON' => 'btn_list',
);
$context = new \CAdminContextMenu($arMenu);
$context->Show();

// Tab control
$arTabs = array();
$arTabs[] = array(
	'DIV' => 'general',
	'TAB' => Loc::getMessage('ACRIT_EXP_TAB_GENERAL_NAME'),
	'TITLE' => Loc::getMessage('ACRIT_EXP_TAB_GENERAL_DESC'),
);

# Update notifier
\Acrit\Core\Update::display();

?><div id="acrit_exp_form"><?

// Start TabControl (via CAdminForm, not CAdminTabControl)
$obTabControl = new \CAdminForm($strAdminFormName, $arTabs, true, true);
$obTabControl->Begin(array(
	'FORM_ACTION' => $APPLICATION->GetCurPageParam('', array()),
));
$obTabControl->BeginNextFormTab();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Name
$obTabControl->BeginCustomField('GROUP[NAME]', Loc::getMessage('ACRIT_EXP_FIELD_NAME'));
?>
	<tr id="tr_NAME">
		<td>
			<label for="field_NAME"><?=$obTabControl->GetCustomLabelHTML()?><label>
		</td>
		<td>
			<input type="text" name="GROUP[NAME]" size="50" maxlength="255" data-role="group-name" spellcheck="false"
				data-default-name="<?=Loc::getMessage('ACRIT_EXP_FIELD_NAME_DEFAULT');?>"
				<?if($intGroupID):?>data-custom-name="true"<?endif?>
				value="<?=htmlspecialcharsbx($arGroup['NAME']);?>" />
			<script>$(document).ready(function(){$('input[data-role="group-name"]').focus();});</script>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('GROUP[NAME]');

// Description
$obTabControl->BeginCustomField('GROUP[DESCRIPTION]', Loc::getMessage('ACRIT_EXP_FIELD_DESCRIPTION'));
?>
	<tr id="tr_DESCRIPTION">
		<td>
			<label for="field_DESCRIPTION"><?=$obTabControl->GetCustomLabelHTML()?><label>
		</td>
		<td>
			<textarea name="GROUP[DESCRIPTION]" id="field_DESCRIPTION" class="acrit-exp-group-description" 
				style="min-height:48px;resize:vertical;width:80%;"
				cols="51" rows="3"><?=$arGroup['DESCRIPTION']?></textarea>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('GROUP[DESCRIPTION]');

// Sort
$obTabControl->AddEditField('GROUP[SORT]', Loc::getMessage('ACRIT_EXP_FIELD_SORT'), false, array('size'=>10, 'maxlength'=>10), 
	$arGroup['SORT']);

// Group ID
$obTabControl->BeginCustomField('GROUP[GROUP_ID]', Loc::getMessage('ACRIT_EXP_FIELD_GROUP_ID'));
?>
	<tr id="tr_GROUP_ID">
		<td>
			<label for="field_GROUP_ID"><?=$obTabControl->GetCustomLabelHTML()?><label>
		</td>
		<td>
			<select name="GROUP[GROUP_ID]" id="field_DESCRIPTION" class="acrit-exp-group-group-id">
				<option value=""><?=Loc::getMessage('ACRIT_EXP_FIELD_GROUP_ID_EMPTY');?></option>
				<?foreach(Helper::call($strModuleId, 'ProfileGroup', 'getTree', []) as $arTreeItem):?>
					<?
					$strDisabled = $arTreeItem['LEFT_MARGIN'] >= $arGroup['LEFT_MARGIN'] && $arTreeItem['RIGHT_MARGIN'] <= $arGroup['RIGHT_MARGIN'] ? ' disabled' : '';
					?>
					<option value="<?=$arTreeItem['ID'];?>"<?if($arTreeItem['ID'] == $arGroup['GROUP_ID']):?> selected<?endif?><?=$strDisabled;?>><?
						print sprintf('%s%s [%d]', str_repeat('. . ', $arTreeItem['DEPTH_LEVEL'] - 1),
							htmlspecialcharsbx($arTreeItem['NAME']), $arTreeItem['ID']).', '.$arTreeItem['LEFT_MARGIN'].', '.$arTreeItem['RIGHT_MARGIN'];
					?></option>
				<?endforeach?>
			</select>
		</td>
	</tr>
<?
$obTabControl->EndCustomField('GROUP[GROUP_ID]');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$obTabControl->Buttons(array(
	'disabled' => false,
	'back_url' => 'acrit_'.$strModuleCodeLower.'_new_list.php?lang='.LANGUAGE_ID,
));
$obTabControl->Show();

?></div><?

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
?>