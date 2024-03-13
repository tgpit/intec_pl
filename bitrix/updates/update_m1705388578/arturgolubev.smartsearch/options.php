<?
use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

use \Arturgolubev\Smartsearch\Settings;

$module_id = 'arturgolubev.smartsearch';
$module_name = str_replace('.', '_', $module_id);

if(!Loader::includeModule($module_id)){
	include 'autoload.php';
}

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/options.php");

global $USER, $APPLICATION;
if (!$USER->IsAdmin()) return;

$aMenu = [];
$aMenu[] = [
	"TEXT"=>Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_FAST_HREFS"),
	"TITLE"=>Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_FAST_HREFS"),
	"LINK_PARAM"=>" target='_blank'",
	"MENU" => [
		[
			"TEXT"=>Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_BX_MODULE_SEARCH"),
			"LINK"=>"/bitrix/admin/settings.php?lang=".LANGUAGE_ID."&mid=search",
			"TITLE"=>Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_BX_MODULE_SEARCH"),
			"LINK_PARAM"=>" target='_blank'",
		],
		[
			"TEXT"=>Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_BX_SEARCH_TABLE"),
			"LINK"=>"/bitrix/admin/perfmon_table.php?lang=".LANGUAGE_ID."&table_name=b_search_content",
			"TITLE"=>Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_BX_SEARCH_TABLE"),
			"LINK_PARAM"=>" target='_blank'",
		]
	],
];


$context = new CAdminContextMenu($aMenu);
$context->Show();

$themes = [
	"blue" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_COLOR_THEME_BLUE"),
	"black" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_COLOR_THEME_BLACK"),
	"yellow" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_COLOR_THEME_YELLOW"),
	"green" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_COLOR_THEME_GREEN"),
	"red" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_COLOR_THEME_RED"),
];

$arOptions = [
	"visual" => [
		["color_theme", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_COLOR_THEME"), "blue", ["selectbox", $themes]],
		["my_color_theme", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_MY_COLOR_THEME"), "", ["colorbox"]],
		["clarify_section", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_CLARIFY_SECTION"), "N", ["checkbox"]],
		["set_title", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SET_TITLE"), "N", ["checkbox"]],
		["set_title_template", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SET_TITLE_TEMPLATE"), Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SET_TITLE_TEMPLATE_DEFAULT"), ["text"]],
	],
	"search" => [],
	"terms" => [],
    "main" => [
		// Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_DEBUG_SETTING"),
        // ["title_max_count", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_TITLE_MAX_COUNT"), "", ["text"]],
        // ["search_max_count", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SEARCH_MAX_COUNT"), "", ["text"]],
		["disable_cache", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_DISABLE_CACHE"), "N", ["checkbox"]],
		["debug", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_DEBUG"), "N", ["checkbox"]],
		Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_EXCLUDE_FROM_SEARCH"),
		["exclude_by_section", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_EXCLUDE_BY_SECTION"), "Y", ["checkbox"]],
		["exclude_by_wo_section", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_EXCLUDE_BY_WO_SECTION"), "", ["checkbox"]],
		["exclude_by_product", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_EXCLUDE_BY_PRODUCT"), "N", ["checkbox"]],
		["exclude_by_available", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_EXCLUDE_BY_AVAILABLE"), "N", ["checkbox"]],
		["exclude_by_quantity", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_EXCLUDE_BY_QUANTITY"), "N", ["checkbox"]],
		["exclude_by_module", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_EXCLUDE_BY_MODULE"), "", ["text"]],
    ]
];
$arOptions["visual"][] = ["note" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_VISUAL_EXTENDED_NOTE")];

$arMode = [
	"extended" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_MODE_EXTENDED"),
	"standart" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_MODE_STANDART"),
];

$arOptions["search"][] = ["note" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SEARCH_EXTENDED_NOTE")];
$arOptions["search"][] = Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SEARCH_ALGORITMS");
$arOptions["search"][] = ["mode_metaphone", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_METAPHONE_MODE"), "", ["checkbox"]];
$arOptions["search"][] = ["min_length", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_MIN_LENGTH"), "3", ["text"]];
$arOptions["search"][] = ["break_letters", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_BREAK_LETTERS"), "", ["text"]];
// $arOptions["search"][] = ["max_words_count", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_MAX_WORDS_COUNT"), "", ["text"]];
$arOptions["search"][] = ["mode_stitle", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_STITLE_MODE"), "", ["selectbox", $arMode], "N", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SEARCH_MODE_DOP")];
$arOptions["search"][] = ["mode_spage", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SPAGE_MODE"), "", ["selectbox", $arMode], "N", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SEARCH_MODE_DOP")];
$arOptions["search"][] = ["mode_guessplus", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_GUESSPLUS_MODE"), "", ["checkbox"]];

// if(COption::GetOptionString("search", "use_stemming") == "Y"){
	// $arOptions["search"][] = ["mode_title_stemming", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_TITLE_STEMMING"), "", ["checkbox"]];
// }

$arOptions["search"][] = Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_DOP_SORTING");
$arOptions["search"][] = ["sort_secton_first", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SORT_SECTION_FIRST"), "Y", ["checkbox"]];
if(Loader::includeModule("catalog")){
	$arOptions["search"][] = ["sort_available_first", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SORT_AVAILABLE_FIRST"), "Y", ["checkbox"]];
	$arOptions["search"][] = ["sort_available_qt_first", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SORT_AVAILABLE_QT_FIRST"), "", ["checkbox"]];
}
$arOptions["search"][] = ["sort_picture_first", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SORT_PICTURE_FIRST"), "Y", ["checkbox"]];

$arOptions["search"][] = Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SEARCH_EXTENDED");
$arOptions["search"][] = ["use_seo_title", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_USE_SEO_TITLE"), "", ["checkbox"]];
$arOptions["search"][] = ["use_title_tag_search", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_USE_TITLE_TAG_SEARCH"), "Y", ["checkbox"]];
$arOptions["search"][] = ["use_title_prop_search", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_USE_TITLE_PROP_SEARCH"), "Y", ["checkbox"]];
$arOptions["search"][] = ["use_title_id", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_USE_TITLE_ID"), "N", ["checkbox"]];
$arOptions["search"][] = ["use_title_sname", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_USE_TITLE_SECTION_NAME"), "N", ["checkbox"]];
$arOptions["search"][] = ["find_section_by_parent", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_FIND_SECTION_BY_PARENT"), "N", ["checkbox"]];
$arOptions["search"][] = ["use_page_text_nosearch", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_USE_PAGE_STOP_TEXT"), "Y", ["checkbox"]];
$arOptions["search"][] = ["exception_words_list", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_EXCEPTIONS_WORDS_LIST"), Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_EXCEPTIONS_WORDS_LIST_DEF"), ["textarea"]];


$arOptions["terms"][] = Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_YOU_RULES");
$file = new \Bitrix\Main\IO\File($_SERVER["DOCUMENT_ROOT"].CArturgolubevSmartsearch::RULES_FILE);
if(!$file->isExists()){
	$file->putContents('');
}

if(file_exists($_SERVER["DOCUMENT_ROOT"].CArturgolubevSmartsearch::RULES_FILE)){
	$arOptions["terms"][] = ["terms_file", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_TERMS_FILE_TITLE"), Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_TERMS_FILE_VALUE"), ["statictext"], false, Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_TERMS_INFO_TITLE")];
}else{
	$arOptions["terms"][] = ["terms_file", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_TERMS_FILE_TITLE"), Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_TERMS_NOFILE_VALUE"), ["statictext"]];
}

$siteList = Settings::getSites();
if(count($siteList)){
	$arOptions["terms"][] = Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_REDIRECT_FROM_PAGES");
	foreach($siteList as $arSite){
		$fileName = $_SERVER["DOCUMENT_ROOT"].str_replace('SITE_ID', $arSite["ID"], CArturgolubevSmartsearch::REDIRECT_FILE);
		
		$file = new \Bitrix\Main\IO\File($fileName);
		if(!$file->isExists()){
			$file->putContents('');
		}
		
		$arOptions["terms"][] = ["terms_file", Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_REDIRECT_FILE_TITLE", ["#sitename#"=>$arSite["NAME"].' ['.$arSite["ID"].']']), Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_REDIRECT_FILE_VALUE", ["#sid#" => $arSite["ID"]]), ["statictext"], false, Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_REDIRECT_FILE_HINT")];
	}
}

$arTabs = [
    ["DIV" => "visual_smartsearch_tab", "TAB" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_VISUAL_TAB_NAME"), "TITLE" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_VISUAL_TAB_TITLE"), "OPTIONS"=>"visual"],
    ["DIV" => "search_smartsearch_tab", "TAB" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SEARCH_TAB_NAME"), "TITLE" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SEARCH_TAB_TITLE"), "OPTIONS"=>"search"],
    ["DIV" => "terms_smartsearch_tab", "TAB" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_TERMS_TAB_NAME"), "TITLE" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_TERMS_TAB_TITLE"), "OPTIONS"=>"terms"],
    ["DIV" => "system_smartsearch_tab", "TAB" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SYSTEM_TAB_NAME"), "TITLE" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_SYSTEM_TAB_TITLE"), "OPTIONS"=>"main"],
];
$tabControl = new CAdminTabControl("tabControl", $arTabs);

// ****** SaveBlock
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply)>0 && check_bitrix_sessid())
{
	foreach ($arOptions as $aOptGroup) {
		foreach ($aOptGroup as $option) {
			__AdmSettingsSaveOption($module_id, $option);
		}
	}
	
    if (strlen($Update) > 0 && strlen($_REQUEST["back_url_settings"]) > 0)
        LocalRedirect($_REQUEST["back_url_settings"]);
    else
        LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . urlencode($mid) . "&lang=" . urlencode(LANGUAGE_ID) . "&back_url_settings=" . urlencode($_REQUEST["back_url_settings"]) . "&" . $tabControl->ActiveTabParam());
}


$arSearchNoteSettings = [];

if(intVal(COption::GetOptionString('search', "max_file_size")) < 1)
	$arSearchNoteSettings[] = Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_ERROS_SETTING_SIZE");

if(intVal(COption::GetOptionString('search', "max_result_size")) > 1000)
	$arSearchNoteSettings[] = Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_ERROS_SETTING_CNT");

if(COption::GetOptionString('search', "use_tf_cache") != "Y")
	$arSearchNoteSettings[] = Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_ERROS_SETTING_FAST");

if(COption::GetOptionString('search', "full_text_engine") == "mysql")
	$arSearchNoteSettings[] = Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_ERROS_SETTING_ENGINE");

if(COption::GetOptionString('search', "agent_stemming") == "Y")
	$arSearchNoteSettings[] = Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_ERROS_SETTING_AGENT_STEMMING");

if(COption::GetOptionString('search', "stat_phrase") != "Y")
	$arSearchNoteSettings[] = Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_ERROS_SETTING_STATISTIC");

if(strstr(COption::GetOptionString('search', "letters"), ' '))
	$arSearchNoteSettings[] = Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_ERROS_SETTING_LETTERS");

// echo '<pre>'; print_r($arSearchNoteSettings); echo '</pre>';
?>

<?
if(count($arSearchNoteSettings)>0)
{
	CAdminMessage::ShowMessage(["DETAILS"=>Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_ERROS_SETTING_MESSAGE_START").implode('<br>', $arSearchNoteSettings), "1MESSAGE" => Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_ERROS_SETTING_TITLE"), "HTML"=>true]);
}

if(!Loader::includeModule($module_id)){
	CAdminMessage::ShowMessage(["DETAILS"=>Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_DEMO_IS_EXPIRED"), "HTML"=>true]);
}
?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
	<?$tabControl->Begin();?>
	
	<?foreach($arTabs as $key=>$tab):
		$tabControl->BeginNextTab();
			Settings::showSettingsList($module_id, $arOptions, $tab);
	endforeach;?>
	
	<?$tabControl->Buttons();?>
		<input type="submit" name="Update" value="<?=Loc::getMessage("MAIN_SAVE")?>" title="<?=Loc::getMessage("MAIN_OPT_SAVE_TITLE")?>">
				
		<?if(strlen($_REQUEST["back_url_settings"])>0):?>
			<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
		<?endif?>
		
		<?=bitrix_sessid_post();?>
	<?$tabControl->End();?>
</form>




<?if(COption::GetOptionString($module_id, "debug") == 'Y'):?>
	<div class="help_note_wrap">
	<?if(Loader::includeModule("iblock")):?>
		<?
		$arIblockInIndex = [];
		$arTableCheck = [];
		
		$res = CIBlock::GetList([], ['ACTIVE'=>'Y', "CHECK_PERMISSIONS" => "N"], true);
		while($ar_res = $res->Fetch()){
			if($ar_res["INDEX_ELEMENT"] == 'Y' || $ar_res["INDEX_SECTION"] == 'Y'){
				$arIblockInIndex[] = $ar_res;
			}
		}
		
		$connection = Bitrix\Main\Application::getConnection();
		$sqlHelper = $connection->getSqlHelper();
		$sql = 'SELECT `MODULE_ID`, COUNT(*) as `CNT` FROM b_search_content GROUP BY `MODULE_ID`;';
		$recordset = $connection->query($sql);
		while ($record = $recordset->fetch()){
			$arTableCheck[] = $record;
		}
		?>

		<?=BeginNote();?>
			<div style="color: #000; font-size: 14px;">
				<b><?=Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_IBLOCKS_IN_INDEX")?></b><br/>
				<?if(count($arIblockInIndex)):?>
					<ul>
					<?foreach($arIblockInIndex as $v):?>
						<li style="margin: 5px 0;">
							<a href="/bitrix/admin/iblock_edit.php?type=<?=$v["IBLOCK_TYPE_ID"]?>&lang=<?=LANGUAGE_ID?>&ID=<?=$v["ID"]?>&admin=Y" target="_blank"><?=$v["NAME"]?></a><br/>
							Index: <?=($v["INDEX_ELEMENT"] == 'Y')? Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_IBLOCKS_IN_INDEX_VIEW") :''?> <?=($v["INDEX_SECTION"] == 'Y')?Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_IBLOCKS_IN_INDEX_VIEW_S"):''?><br/>
							Count: <?=$v["ELEMENT_CNT"]?>
							</li>
					<?endforeach;?>
					</ul>
				<?else:?>
					<?=Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_IBLOCKS_IN_INDEX_EMPTY")?>
				<?endif;?>
				
				<b><?=Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_TABLE_SCAN")?></b><br/>
				<?if(count($arTableCheck)):?>
					<ul>
					<?foreach($arTableCheck as $v):?>
						<li style="margin: 5px 0;"><?=$v["MODULE_ID"]?>: <?=$v["CNT"]?></li>
					<?endforeach;?>
					</ul>
				<?endif;?>
			</div>
		<?=EndNote();?>
	<?endif;?>
	</div>
<?endif?>

<?Settings::showInitUI();?>


<div class="help_note_wrap">
	<?= BeginNote();?>
		<p class="title"><?=Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_HELP_TAB_TITLE")?></p>
		<p><?=Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_HELP_TAB_VALUE")?></p>
	<?= EndNote();?>
</div>
