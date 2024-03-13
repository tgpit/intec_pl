<?
global $USER;
if(!is_object($USER)){
	$USER = new \CUser();
}
if($USER->IsAdmin()){
	IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/arturgolubev.smartsearch/menu.php");

	$arSubmenu[] = array(
		'text' => GetMessage("ARTURGOLUBEV_SMARTSEARCH_SUBMENU_SETTINGS"),
		'more_url' => array(),
		'url' => '/bitrix/admin/settings.php?lang=ru&mid=arturgolubev.smartsearch',
		'icon' => 'sys_menu_icon',
	);

	$arSubmenu[] = array(
		'text' => GetMessage("ARTURGOLUBEV_SMARTSEARCH_SUBMENU_REINDEX"),
		'more_url' => array(),
		'url' => '/bitrix/admin/search_reindex.php?lang='.LANGUAGE_ID,
		'icon' => 'sys_menu_icon',
	);

	$arSubmenu[] = array(
		"text" => GetMessage("ARTURGOLUBEV_SMARTSEARCH_SUBMENU_STATISTIC"),
		"items_id" => "menu_search_stat",
		"items" => array(
			array(
				"text" => GetMessage("ARTURGOLUBEV_SMARTSEARCH_SUBMENU_STATISTIC_LINKS"),
				"url" => "/bitrix/admin/search_phrase_list.php?lang=".LANGUAGE_ID,
				"more_url" => Array("search_phrase_list.php"),
			),
			array(
				"text" => GetMessage("ARTURGOLUBEV_SMARTSEARCH_SUBMENU_STATISTIC_WORDS"),
				"url" => "/bitrix/admin/search_phrase_stat.php?lang=".LANGUAGE_ID,
				"more_url" => Array("search_phrase_stat.php"),
			),
		),
	);

	$aMenu = array(
		'parent_menu' => 'global_menu_services',
		'section' => 'ARTURGOLUBEV_SMARTSEARCH',
		'sort' => 1,
		'text' => GetMessage("ARTURGOLUBEV_SMARTSEARCH_MENU_MAIN"),
		'icon' => 'arturgolubev_smartsearch_icon_main',
		'items_id' => 'arci_icon_main',
		'items' => $arSubmenu,
	);


	return $aMenu;
}