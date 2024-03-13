<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper,
	\Bitrix\Main\Localization\Loc;
	

Loc::loadMessages(__FILE__);

if(!\Bitrix\Main\Loader::includeModule('acrit.core')){
	return;
}

$bOldCoreDisabled = \Bitrix\Main\Config\Option::get($strModuleId, 'disable_old_core') == 'Y';

if($APPLICATION->GetGroupRight($strModuleId) != 'D'){
	$strModuleCode = str_replace('.', '_', $strModuleId);
	$strModuleCodeLang = toUpper(preg_replace('#^acrit\.#', '', $strModuleId));
	$strModuleName = Loc::getMessage('ACRIT_'.$strModuleCodeLang.'_SECTION');
	if(empty($strModuleName)){
		$strModuleIndexFile = realpath(__DIR__.'/../../..').'/'.$strModuleId.'/install/index.php';
		if(is_file($strModuleIndexFile)){
			require_once $strModuleIndexFile;
			$obModule = new $strModuleCode();
			$strModuleName = $obModule->MODULE_NAME;
			unset($obModule);
		}
	}
	#
	$arProfileGroups = Helper::call($strModuleId, 'ProfileGroup', 'makeTreeMenu', [$arQuery]);
	#
	$arSubmenu = [];
	$arSubmenu[] = array(
		'text' => Loc::getMessage('ACRIT_EXP_MENU_NEW_TITLE').(!$bOldCoreDisabled?Loc::getMessage('ACRIT_EXP_MENU_NEW_TITLE_'):''),
		'url' => $strModuleCode.'_new_list.php?lang='.LANGUAGE_ID,
		'more_url' => array(
			$strModuleCode.'_new_list.php',
			$strModuleCode.'_new_edit.php',
			$strModuleCode.'_new_group.php',
			$strModuleCode.'_new_migrator.php',
		),
		'items_id' => $strModuleCode.'_profile_groups',
		'items' => $arProfileGroups,
	);
	if(!$bOldCoreDisabled){
		$arSubmenu[] = array(
			'text' => Loc::getMessage('ACRIT_EXP_MENU_TITLE'),
			'url' => $strModuleCode.'_list.php?lang='.LANGUAGE_ID,
			'more_url' => array($strModuleCode.'_edit.php'),
		);
		$arSubmenu[] = array(
			'text' => Loc::getMessage('ACRIT_EXP_MENU_PROFILE_EXPORT'),
			'url' => $strModuleCode.'_export.php?lang='.LANGUAGE_ID,
			'more_url' => array($strModuleCode.'_export.php'),
		);
	}
	$arSubmenu[] = array(
		'text' => Loc::getMessage('ACRIT_EXP_MENU_NEW_SETTINGS'),
		'url' => sprintf('/bitrix/admin/settings.php?mid=%s&lang=%s', $strModuleId, LANGUAGE_ID),
		'more_url' => [],
	);
	$arSubmenu[] = array(
		'text' => Loc::getMessage('ACRIT_EXP_MENU_ORDERS_TITLE'),
		'url' => $strModuleCode.'_orders_list.php?lang='.LANGUAGE_ID,
		'more_url' => array($strModuleCode.'_orders_list.php', $strModuleCode.'_orders_edit.php'),
	);
	$arSubmenu[] = array(
		'text' => Loc::getMessage('ACRIT_EXP_MENU_CRM_TITLE'),
		'url' => $strModuleCode.'_crm_list.php?lang='.LANGUAGE_ID,
		'more_url' => array($strModuleCode.'_crm_list.php', $strModuleCode.'_crm_edit.php'),
	);
	// $arSubmenu[] = array(
	// 	'text' => Loc::getMessage('ACRIT_EXP_MENU_NEW_COURSE'),
	// 	'url' => $strModuleCode.'_new_course.php?lang='.LANGUAGE_ID,
	// 	'more_url' => array($strModuleCode.'_new_course.php'),
	// );
	$arSubmenu[] = array(
		'text' => Loc::getMessage('ACRIT_EXP_MENU_NEW_SUPPORT'),
		'url' => $strModuleCode.'_new_support.php?lang='.LANGUAGE_ID,
		'more_url' => array($strModuleCode.'_new_support.php'),
	);
	$arSubmenu[] = array(
		'text' => Loc::getMessage('ACRIT_EXP_MENU_NEW_IDEA'),
		'url' => $strModuleCode.'_new_idea.php?lang='.LANGUAGE_ID,
		'more_url' => array($strModuleCode.'_new_idea.php'),
	);
	$intSort = 10;
	$arModulesAll = [
		'acrit.googlemerchant',
		'acrit.export',
		'acrit.exportpro',
		'acrit.exportproplus',
		'acrit.exportfile',
	];
	$key = array_search($strModuleId, $arModulesAll);
	if(is_numeric($key)){
		$intSort = ($key + 1) * 10;
	}
	$aMenu = array(
		'parent_menu' => 'global_menu_acrit',
		'section' => $strModuleName,
		'sort' => $intSort,
		'text' => $strModuleName,
		'title' => Loc::getMessage('ACRIT_EXP_MENU_TEXT'),
		'url' => '',
		'icon' => 'acrit_exp_menu_icon',
		'page_icon' => '',
		'items_id' => 'menu_'.$strModuleCode,
		'items' => $arSubmenu,
	);
	return $aMenu;
}
return false;
?>