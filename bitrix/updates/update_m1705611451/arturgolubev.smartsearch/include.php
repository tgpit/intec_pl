<?
use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

use \Arturgolubev\Smartsearch\Hl;
use \Arturgolubev\Smartsearch\Tools as Tools;
use \Arturgolubev\Smartsearch\Unitools as UTools;
use \Arturgolubev\Smartsearch\Encoding;

CModule::AddAutoloadClasses(
	"arturgolubev.smartsearch",
	array(
		"CSearchFullTextExt" => "classes/general/fulltextext.php",
		"CSearchSphinxExt" => "classes/general/sphinxext.php",
		"CSearchTitleExt" => "classes/mysql/title.php",
		"CSearchExt" => "classes/mysql/search.php",
	)
);

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/arturgolubev.smartsearch/include.php");

include 'jscore.php';
include 'autoload.php';

Class CArturgolubevSmartsearch 
{
	const MODULE_ID = 'arturgolubev.smartsearch';
	public $MODULE_ID = 'arturgolubev.smartsearch'; 

	const RULES_FILE = '/bitrix/tools/arturgolubev.smartsearch/rules.txt';
	const REDIRECT_FILE = '/bitrix/tools/arturgolubev.smartsearch/redirect_SITE_ID.txt';
	
	const CACHE_TIME = 86400;
	const CACHE_VERSION = 'v502';
	
	/* handlers */
	static function iblockLinkPropHandler($arFields, $intIndexIblockId, $arIndexProperty){
		if($arFields["MODULE_ID"] == 'iblock' && $arFields["TITLE"] && $arFields["ITEM_ID"]){
			if($arFields["PARAM2"] == $intIndexIblockId && !empty($arIndexProperty) && Encoding::exSubstr($arFields["ITEM_ID"], 0, 1) != "S" && Loader::includeModule("iblock")){
				foreach($arIndexProperty as $pid)
				{
					$db_props = CIBlockElement::GetProperty($arFields["PARAM2"], $arFields["ITEM_ID"], array("sort" => "asc"), Array("ID"=>$pid));
					while($ar_props = $db_props->Fetch()){
						if($ar_props["PROPERTY_TYPE"] == 'E' && $ar_props["VALUE"]){
							$res = CIBlockElement::GetList([], array("ID"=>$ar_props["VALUE"]), false, array("nPageSize"=>1), array("ID", "NAME"));
							while($arFields2 = $res->Fetch()){
								$arFields["TITLE"] .= ' '.$arFields2["NAME"];
							}
						}
						
						if($ar_props["USER_TYPE"] == 'ElementXmlID' && $ar_props["VALUE"]){
							$res = CIBlockElement::GetList([], array("XML_ID"=>$ar_props["VALUE"]), false, array("nPageSize"=>1), array("ID", "NAME"));
							while($arFields2 = $res->Fetch()){
								$arFields["TITLE"] .= ' '.$arFields2["NAME"];
							}
						}
					}
				}
			}
		}
		
		return $arFields;
	}
	
	static function onProductChange(\Bitrix\Main\Entity\Event $event)
	{
		$product_id = $event->getParameter("id");
		if(Loader::includeModule(self::MODULE_ID) && IntVal($product_id["ID"]) > 0 && Loader::includeModule("iblock")){
			CIBlockElement::UpdateSearch($product_id["ID"], true);
		}
	}
	
	static function checkIbSectionActive($iblockID, $sectionID){
		$result = UTools::getStorage('section_cache', 'section_'.$sectionID);
		if(!is_array($result)){
			$result = [
				'ID' => $sectionID,
				'ACTIVE' => 'Y',
				'ITEMS' => []
			];
			
			$nav = CIBlockSection::GetNavChain($iblockID, $sectionID, array("ID", "ACTIVE"), true);
			foreach($nav as $item){
				if($item["ACTIVE"] == 'N'){
					$result['ACTIVE'] = 'N';
				}
			}
			UTools::setStorage('section_cache', 'section_'.$sectionID, $result);
		}

		return $result;
	}

	static function onIndexHandler($arFields){
		$exclude_by_module = trim(UTools::getSetting("exclude_by_module"));
		if($exclude_by_module && $arFields["MODULE_ID"] && $arFields["TITLE"] && $arFields["ITEM_ID"]){
			$arExM = explode(',', $exclude_by_module);
			$arExM = array_map(function($a){return trim($a);}, $arExM);
			
			if(in_array($arFields["MODULE_ID"], $arExM)){
				$arFields["TITLE"] = ''; $arFields["BODY"] = ''; $arFields["TAGS"] = '';
			}
		}
		
		
		$start = microtime(true);
		
		if($arFields["MODULE_ID"] == "iblock" && $arFields["TITLE"] && $arFields["ITEM_ID"])
		{
			$arFields["CUSTOM_RANK"] = 0;
			
			$exclude = 0;
			
			// $arFields['ITEM_ID'] = 'smartseo_64_7434';
			
			$sett = array(
				'is_section' => (Encoding::exSubstr($arFields["ITEM_ID"], 0, 1) == 'S'),
				'is_subsection' => (Encoding::exSubstr($arFields["ITEM_ID"], 0, 9) == 'smartseo_'),
			
				'cache' => (UTools::getSetting("disable_cache") != 'Y'),
				"tags" => (UTools::getSetting("use_title_tag_search") == "Y" ? 1 : 0),
				"props" => (UTools::getSetting("use_title_prop_search") == "Y" ? 1 : 0),
				"id_include" => (UTools::getSetting("use_title_id") == "Y" ? 1 : 0),
				"sname_include" => (UTools::getSetting("use_title_sname") == "Y" ? 1 : 0),
				"page_stop_body" => (UTools::getSetting("use_page_text_nosearch") == "Y" ? 1 : 0),
				"section_findby_parent" => (UTools::getSetting("find_section_by_parent") == "Y" ? 1 : 0),
				
				"section_first" => (UTools::getSetting("sort_secton_first") == "Y" ? 1 : 0),
				"available_first" => (UTools::getSetting("sort_available_first") == "Y" ? 1 : 0),
				"available_qt_first" => (UTools::getSetting("sort_available_qt_first") == "Y" ? 1 : 0),
				"picture_first" => (UTools::getSetting("sort_picture_first", 'Y') == "Y" ? 1 : 0),
				
				'exclude_by_section' => (UTools::getSetting("exclude_by_section") == 'Y'),
				'exclude_by_wo_section' => (UTools::getSetting("exclude_by_wo_section") == 'Y'),
				'exclude_by_product' => (UTools::getSetting("exclude_by_product") == 'Y'),
				'exclude_by_available' => (UTools::getSetting("exclude_by_available") == 'Y'),
				'exclude_by_quantity' => (UTools::getSetting("exclude_by_quantity") == 'Y'),
				'use_seo_title' => (UTools::getSetting("use_seo_title") == 'Y'),
			);
			
			if(!$sett["tags"] && $arFields["TAGS"] != ''){
				$arFields["TAGS"] = '';
			}
			
			$info = $arFields["TITLE"];
			
			if($sett['is_section'])
			{
				$realSectionID = Encoding::exSubstr($arFields["ITEM_ID"], 1);
				
				if($sett['use_seo_title']){
					$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arFields["PARAM2"], $realSectionID);
					$arMeta = $ipropValues->getValues();
					if($arMeta['SECTION_PAGE_TITLE']){
						$info = $arMeta['SECTION_PAGE_TITLE'].' '.$info;
					}
				}
				
				$arFields["PARAMS"]["catalog_available"] = 'Y';
				
				if($sett["id_include"]){
					$info .= ' '.$realSectionID;
				}
				
				if($sett["props"]){
					$arSearchableFields = UTools::getStorage('reindex_cache', 'searchable_fields_'.$arFields["PARAM2"]);
					if(!is_array($arSearchableFields)){
						$arSearchableFields = [];
						$rsData = CUserTypeEntity::GetList(array("FIELD_NAME"=>"ASC"), array("ENTITY_ID" => "IBLOCK_".$arFields["PARAM2"]."_SECTION", "IS_SEARCHABLE" => "Y"));
						while($arRes = $rsData->Fetch()){
							$arSearchableFields[] = $arRes["FIELD_NAME"];
						}
						UTools::setStorage('reindex_cache', 'searchable_fields_'.$arFields["PARAM2"], $arSearchableFields);
					}
					
					if(!empty($arSearchableFields)){
						$arFilterSection = Array('IBLOCK_ID'=>$arFields["PARAM2"], 'ID'=>$realSectionID);
						$dbTmpList = CIBlockSection::GetList(Array($by=>$order), $arFilterSection, false, array_merge($arSearchableFields, array("ID", "NAME", "IBLOCK_ID")));
						while($arTmpFields = $dbTmpList->GetNext()){
							foreach($arSearchableFields as $v){
								if($arTmpFields[$v]){
									$info .= ' '.$arTmpFields[$v];
								}
							}
						}
					}
				}
				
				if($sett['section_findby_parent']){
					$nav = CIBlockSection::GetNavChain($arFields['PARAM2'], $realSectionID, array("ID", "NAME", "ACTIVE"), true);
					foreach($nav as $item){
						if($item['ID'] != $realSectionID){
							$info .= ' '.$item['NAME'];
						}
					}
				}
				
				if($sett["section_first"]){
					$arFields["CUSTOM_RANK"] = 20000;
				}
			}elseif($sett['is_subsection']){
				$arFields["PARAMS"]["catalog_available"] = 'Y';
				
				if($sett["section_first"]){
					$arFields["CUSTOM_RANK"] = 17500;
				}
			}elseif(intval($arFields['ITEM_ID'])){
				if($sett['use_seo_title']){
					$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arFields["PARAM2"], $arFields["ITEM_ID"]);
					$arMeta = $ipropValues->getValues();
					if($arMeta['ELEMENT_PAGE_TITLE']){
						$info = $arMeta['ELEMENT_PAGE_TITLE'].' '.$info;
					}
				}
				
				$getElementSelect = Array("ID", "IBLOCK_ID", "NAME", 'IBLOCK_SECTION_ID');
				$getElementFilter = Array("IBLOCK_ID"=>$arFields["PARAM2"], "ID"=>$arFields["ITEM_ID"]);
				
				if($sett["id_include"]){
					$info .= ' '.$arFields["ITEM_ID"];
				}
				
				if($sett["tags"] && $arFields["TAGS"] != ''){
					$info .= ' '.$arFields["TAGS"];
				}
				
				if($sett["props"])
				{
					$arSearchProps = UTools::getStorage('reindex_cache', 'searchable_props_'.$arFields["PARAM2"]);
					if(!is_array($arSearchProps)){
						$arSearchProps = [];
						$properties = CIBlockProperty::GetList(Array("sort"=>"asc"), Array("ACTIVE"=>"Y", "SEARCHABLE"=>"Y", "IBLOCK_ID"=>$arFields["PARAM2"]));
						while ($prop_fields = $properties->GetNext(true, false)){
							$arSearchProps[] = $prop_fields;
						}

						$properties = CIBlockProperty::GetList(Array("sort"=>"asc"), Array("ACTIVE"=>"Y", "CODE"=>"CML2_LINK", "IBLOCK_ID"=>$arFields["PARAM2"]));
						while ($prop_fields = $properties->GetNext(true, false)){
							$arSearchProps[] = $prop_fields;
						}

						UTools::setStorage('reindex_cache', 'searchable_props_'.$arFields["PARAM2"], $arSearchProps);
					}
				}
				
				if(Loader::includeModule("catalog")){
					$getElementSelect[] = 'CATALOG_AVAILABLE';
					$getElementSelect[] = 'CATALOG_QUANTITY';
				}

				if($sett['picture_first']){
					$getElementSelect[] = 'PREVIEW_PICTURE';
					$getElementSelect[] = 'DETAIL_PICTURE';
				}
				
				if(true){ 
					$res = CIBlockElement::GetList([], $getElementFilter, false, Array("nPageSize"=>1), $getElementSelect);
					if($ob = $res->GetNextElement()){
						$arElement = $ob->GetFields();  
						// $arProps = $ob->GetProperties();
						
						$arElementProps = [];
						if(is_array($arSearchProps) && count($arSearchProps)){
							$propFilter = ['ID' => []];
							foreach($arSearchProps as $sProp){
								$propFilter['ID'][] = $sProp['ID'];
							}

							$propDb = CIBlockElement::GetPropertyValues($getElementFilter['IBLOCK_ID'], ['ID'=>$arElement['ID']], false, $propFilter);
							while ($propRow = $propDb->Fetch()){
								// echo '<pre>'; print_r($propRow); echo '</pre>';

								foreach($arSearchProps as $sProp){
									$sProp['VALUE'] = $propRow[$sProp['ID']];
									$arElementProps[$sProp['CODE']] = $sProp;
								}
							}
						}

						// echo '<pre>arElementProps '; print_r($arElementProps); echo '</pre>';
						// die();
						
						// check exclude
						if($sett['exclude_by_wo_section'] && !$arElement["IBLOCK_SECTION_ID"] && $arElement['CATALOG_TYPE'] != 4){
							$exclude = 1;
							$exclude_resson = 'WO section';
						}
						
						if(!$exclude && is_array($arElementProps["CML2_LINK"]) && $arElementProps["CML2_LINK"]["VALUE"]){
							if($sett['exclude_by_product'] || $sett['exclude_by_section']){
								$dbMainProd = CIBlockElement::GetList([], array("ID" => $arElementProps["CML2_LINK"]["VALUE"]), false, Array("nPageSize"=>1), Array("ID", 'IBLOCK_ID', "ACTIVE", "IBLOCK_SECTION_ID"));
								if($itemMainProd = $dbMainProd->Fetch()){
									if($sett['exclude_by_product'] && $itemMainProd["ACTIVE"] == "N"){
										$exclude = 1;
										$exclude_resson = 'Active Main product';
									}

									if(!$exclude && $sett['exclude_by_section'] && $itemMainProd['IBLOCK_SECTION_ID']){
										$sAct = self::checkIbSectionActive($itemMainProd['IBLOCK_ID'], $itemMainProd['IBLOCK_SECTION_ID']);
										if($sAct['ACTIVE'] == 'N'){
											$exclude = 1;
											$exclude_resson = 'Active Main product Section';
										}
									}
								}
							}
						}
						
						if($arElement["CATALOG_AVAILABLE"]){
							if($sett['exclude_by_available'] && $arElement["CATALOG_AVAILABLE"] == 'N'){
								$exclude = 1;
								$exclude_resson = 'Catalog available';
							}
							
							if($sett['exclude_by_quantity'] && $arElement['CATALOG_TYPE'] != 3 && $arElement["CATALOG_QUANTITY"] < 1){
								$exclude = 1;
								$exclude_resson = 'Catalog quantity';
							}
						}
						
						// index catalog props
						$arFields["PARAMS"]["catalog_available"] = ($arElement["CATALOG_AVAILABLE"]) ? $arElement["CATALOG_AVAILABLE"] : 'Y';
						
						if($arElement["CATALOG_AVAILABLE"]){
							if($sett["available_first"] && $arElement["CATALOG_AVAILABLE"] == 'Y'){
								if($sett["available_qt_first"] && $arElement["CATALOG_QUANTITY"] > 0){
									$arFields["CUSTOM_RANK"] += 10001;
								}else{
									$arFields["CUSTOM_RANK"] += 10000;
								}
							}elseif($sett["available_qt_first"] && $arElement["CATALOG_QUANTITY"] > 0){
								$arFields["CUSTOM_RANK"] += 10000;
							}
						}
						
						// index element props
						if(!$exclude){
							foreach($arSearchProps as $sProp){
								if($sProp["CODE"] == 'CML2_LINK') continue;

								$itemProp = $arElementProps[$sProp["CODE"]];
								
								if(($sProp["PROPERTY_TYPE"] == 'S' || $sProp["PROPERTY_TYPE"] == 'L' || $sProp["PROPERTY_TYPE"] == 'N') && !$sProp["USER_TYPE"]){
									if(is_array($itemProp["VALUE"])){
										$info .= ' '.implode(' ', $itemProp["VALUE"]);
									}elseif($itemProp["VALUE"] != ''){
										$info .= ' '.$itemProp["VALUE"];
									}
								}
								elseif($sProp["PROPERTY_TYPE"] == 'S' && $sProp["USER_TYPE"] == 'directory'){
									$arVal = (is_array($itemProp["VALUE"])) ? $itemProp["VALUE"] : array($itemProp["VALUE"]);
									$arRealVals = Hl::getPropValueField($sProp, $arVal);
									if(count($arRealVals)){
										$info .= ' '.implode(' ', $arRealVals);
									}
								}elseif($sProp["PROPERTY_TYPE"] == 'S' && $sProp["USER_TYPE"] == 'HTML'){
									if(!is_array($itemProp["VALUE"])){
										$itemProp["VALUE"] = unserialize($itemProp["VALUE"]);
									}

									if(is_array($itemProp["VALUE"]) && $itemProp["VALUE"]['TEXT']){
										$info .= ' '.$itemProp["VALUE"]['TEXT'];
									}
								}else{
									// echo '<pre>'; print_r($sProp); echo '</pre>';
									// echo '<pre>'; print_r($itemProp); echo '</pre>';
									// echo '<pre>'; print_r('=============================='); echo '</pre>';
								}
							}
						}

						if($sett['picture_first']){
							if($arElement['PREVIEW_PICTURE'] || $arElement['DETAIL_PICTURE']){
								$arFields["CUSTOM_RANK"] += 1;
							}
						}
					}
				}

				// check dop weight
				if(!$exclude && $arElement["IBLOCK_SECTION_ID"]){
					$sectionInfo = self::_getSectionsInfo($arElement);
					
					if($sett['exclude_by_section'] && $sectionInfo['exclude']){
						$exclude = 1;
						$exclude_resson = 'By section';
					}
					
					if($sett["sname_include"]){
						$info .= ' '.$sectionInfo['name_path'];
					}
					
					$arFields["CUSTOM_RANK"] += $sectionInfo['weight'];
				}
				
				if($exclude){
					// echo '<pre>'; print_r($exclude_resson); echo '</pre>';
					// die();
					
					$arFields["TITLE"] = ''; $arFields["BODY"] = ''; $arFields["TAGS"] = '';
					return $arFields;
				}
			}
			
			
			$arFields["TITLE"] = strip_tags(htmlspecialchars_decode($info));
						
			$arFields["TITLE"] = self::checkReplaceSymbols($arFields["TITLE"]);
			
			$arFields["TITLE"] = self::checkReplaceRules($arFields["TITLE"]);
			$arFields["TITLE"] = self::prepareQuery($arFields["TITLE"]);
			$arFields["TITLE"] = self::checkReplaceRules($arFields["TITLE"]);
			
			$arFields["TITLE"] = self::clearExceptionsWords($arFields["TITLE"]);
			
			if($sett["page_stop_body"]){
				$arFields["BODY"] = '';
			}else{
				$arFields["BODY"] = strip_tags(htmlspecialchars_decode($arFields["BODY"]));
				$arFields["BODY"] = self::prepareQuery($arFields["BODY"]);
				$arFields["BODY"] = self::clearExceptionsWords($arFields["BODY"]);
			}
		}
		
		
		// echo '<pre>'; print_r($sett); echo '</pre>';
		// echo '<pre>'; print_r(round(microtime(true) - $start, 3)); echo '</pre>';
		// echo '<pre>'; print_r($arFields); echo '</pre>';
		// die();
		
		return $arFields;
	}
		static function _getSectionsInfo($arElement){
			$sectionInfo = array(
				'base_groups' => [],
				'weight' => 0,
				'name_path' => '',
				'exclude' => 1,
			);
			
			// get all groups
			$elGroups = CIBlockElement::GetElementGroups($arElement["ID"], true, array("ID"));
			while($group = $elGroups->Fetch()){
				$sectionInfo['base_groups'][] = $group['ID'];
			}

			foreach($sectionInfo['base_groups'] as $group){
				// check all groups chain
				$groupInfo = UTools::getStorage('section_info_cache', $group);
				if(!isset($groupInfo)){
					$groupInfo = [];
					$nav = CIBlockSection::GetNavChain($arElement["IBLOCK_ID"], $group, array("ID", "NAME", "ACTIVE"), true);
					foreach($nav as $item){
						if($item["ACTIVE"] == 'N'){
							$groupInfo['exclude'] = 1;
						}
						
						$groupInfo['name_path'] .= ' '.$item["NAME"];
							
						$db_list = CIBlockSection::GetList(Array("ID" => "ASC"), Array('IBLOCK_ID'=>$arElement["IBLOCK_ID"], 'ID'=>$item["ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_PROD_WEIGHT"));
						if($arSectResult = $db_list->GetNext(false, false)){
							if(strlen($arSectResult["UF_PROD_WEIGHT"]) > 0){
								$groupInfo['weight'] = intval($arSectResult["UF_PROD_WEIGHT"]);
							}
						}
					}
					
					UTools::setStorage('section_info_cache', $group, $groupInfo);
				}
				
				// collect groups info
				if(!$groupInfo['exclude']){
					$sectionInfo['exclude'] = 0;
				}
				
				$sectionInfo['name_path'] .= $groupInfo['name_path'];
				
				if($sectionInfo['weight'] < $groupInfo['weight']){
					$sectionInfo['weight'] = $groupInfo['weight'];
				}
			}
			
			return $sectionInfo;
		}
	
	
	/* system helpers */
	static function getProductIdByMixed($ids = []){return Tools::getProductIdByMixed($ids);}
	
	static function getRealElementsName($arMixedIDs){
		$result = [];
		
		if(!Loader::includeModule("iblock") || empty($arMixedIDs)) return $result;
		
		$use_seo_title = (UTools::getSetting("use_seo_title") == 'Y');
				
		$tmpElementIDs = [];
		$tmpSectionIDs = [];
		foreach($arMixedIDs as $id){
			if(Encoding::exStrstr($id, 'S'))
				$tmpSectionIDs[] = str_replace('S', '', $id);
			else
				$tmpElementIDs[] = str_replace('S', '', $id);
		}
		
		if(!empty($tmpElementIDs)){
			$rsElements = CIBlockElement::GetList([], array("ID" => $tmpElementIDs), false, false, array("ID", "NAME", "IBLOCK_ID"));
			while($arElement = $rsElements->Fetch()){
				if($use_seo_title){
					$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arElement["IBLOCK_ID"], $arElement["ID"]);
					$arMeta = $ipropValues->getValues();
					if($arMeta['ELEMENT_PAGE_TITLE']){
						$arElement["NAME"] = $arMeta['ELEMENT_PAGE_TITLE'];
					}
				}
				
				$arElement["NAME"] = str_replace("&nbsp;", ' ', $arElement["NAME"]);
				
				$result[$arElement["ID"]] = array(
					"ID" => $arElement["ID"],
					"NAME" => htmlspecialchars_decode($arElement["NAME"]),
				);
			}
		}
		
		if(!empty($tmpSectionIDs)){
			$db_list = CIBlockSection::GetList(Array($by=>$order), array("ID"=>$tmpSectionIDs), false, array("ID", "NAME", "IBLOCK_ID"));
			while($ar_result = $db_list->GetNext()){
				if($use_seo_title){
					$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($ar_result["IBLOCK_ID"], $ar_result['ID']);
					$arMeta = $ipropValues->getValues();
					if($arMeta['SECTION_PAGE_TITLE']){
						$ar_result["NAME"] = $arMeta['SECTION_PAGE_TITLE'];
					}
				}
				
				$ar_result["NAME"] = str_replace("&nbsp;", ' ', $ar_result["NAME"]);
				
				$result["S".$ar_result["ID"]] = array(
					"ID" => $ar_result["ID"],
					"NAME" => htmlspecialchars_decode($ar_result["NAME"]),
				);
			}
		}
		
		return $result;
	}
	
	static function formatElementName($oldName, $newName){
		$saved_words = UTools::getStorage('search_component', 'words');

		preg_match_all('/\<b\>(.*)\<\/b\>/Usi', $oldName, $matches);
		$replaceList = $matches[1];

		if(is_array($saved_words) && count($saved_words)){
			$replaceList = array_merge($replaceList, $saved_words);
			$replaceList = array_unique($replaceList);
		}

		if(!empty($replaceList)){
			$rSearch = [];
			$rReplace = [];

			$nnE = explode(' ', str_replace("\n", ' ', $newName));
			if(count($nnE)>0){
				foreach($nnE as $v){					
					foreach($replaceList as $vm){
						$v = trim($v);
						if(!$v) continue;

						if(Encoding::exStripos(self::prepareQuery($v), $vm) !== false){
							$rSearch[] = $v;
							$rReplace[] = '<b>'.$v.'</b>';
						}
					}
				}

				if(count($rSearch)>0){
					$newName = str_replace($rSearch, $rReplace, $newName);
				}
			}
		}
		
		return $newName;
	}

	/* workers; work register - lower */
	static function checkReplaceSymbols($text){
		$splits = UTools::getSetting('break_letters');
		if($splits){
			$arReplace = preg_split('##'.BX_UTF_PCRE_MODIFIER, $splits, -1, PREG_SPLIT_NO_EMPTY);
			
			$arq = explode(' ', ToLower($text));
			
			foreach($arq as $qk=>$qw){
				$tmp = str_replace($arReplace, ' ', $qw);
				if($tmp != $qw){
					$arq[$qk] = $qw . ' '. $tmp;
				}
			}
			return implode(' ', $arq);
		}else{
			return $text;
		}
	}
	static function checkReplaceRules($q){
		$rules = self::_getReplaceRules();
		
		// echo '<pre>'; print_r($rules); echo '</pre>';
		
		$arq = explode(' ', ToLower($q));
		
		if(count($rules['many'])){
			foreach($rules['many'] as $rk=>$rw){
				$find = [];
				$arRuleWord = explode(' ', $rk);
				
				foreach($arRuleWord as $rule_word){
					foreach($arq as $qk=>$qw){
						if($rw['regular']){
							if(preg_match('/^'.$rule_word.'$/', $qw)){
								$find[] = $qk;
								break;
							}
						}else{
							if($rule_word == $qw){
								$find[] = $qk;
								break;
							}
						}
					}
				}
				
				if(count($arRuleWord) == count($find)){
					foreach($find as $qk){
						unset($arq[$qk]);
					}
					
					$arq[] = $rw['word'];
				}
			}
		}
		
		if(count($rules['one'])){
			foreach($arq as $qk=>$qw){
				foreach($rules['one'] as $rk=>$rw){
					if($rw['regular']){
						if(preg_match('/^'.$rk.'$/', $qw)){
							$arq[$qk] = $rw['word'];
							break;
						}
					}else{
						if($rk == $qw){
							$arq[$qk] = $rw['word'];
							break;
						}
					}
				}
			}
		}
		
		return implode(' ', $arq);
	}
		static function _getReplaceRules(){
			$rules = ['one'=>[], 'many'=>[]];
			
			$file = $_SERVER["DOCUMENT_ROOT"].self::RULES_FILE;
			if(file_exists($file)){
				$obCache = new CPHPCache();
				$cacheId = md5("ag_smartsearch_rules_".filemtime($file));
				$cachePath = '/arturgolubev.smartsearch/'.self::CACHE_VERSION.'_'.SITE_ID.'/rules';	
				
				if($obCache->InitCache(self::CACHE_TIME, $cacheId, $cachePath)){
					$vars = $obCache->GetVars();
					$rules = $vars['rules'];
				}elseif($obCache->StartDataCache()){
					$arFileContent = explode(PHP_EOL, file_get_contents($file));
					if(is_array($arFileContent)){
						foreach($arFileContent as $fileLine){
							$arLine = explode('||', trim(ToLower($fileLine)));
							if(!$arLine[0] || !$arLine[1]) continue;
							
							$to = trim($arLine[0]);
							
							$arFrom = explode('|', $arLine[1]);
							foreach($arFrom as $from){
								$from = str_replace(['.', '*'], ['\.', '.*'], trim($from));
								if($from){
									if(Encoding::exStrpos($from, ' ')){
										$rules['many'][$from] = [
											'regular' => (Encoding::exStrpos($from, '*') !== false),
											'word' => $to
										];
									}else{
										$rules['one'][$from] = [
											'regular' => (Encoding::exStrpos($from, '*') !== false),
											'word' => $to
										];
									}
								}
							}
						}
					}
					
					$obCache->EndDataCache(array('rules' => $rules));
				}
			}
			
			return $rules;
		}
	/* check redirect rules */
	static function checkRedirectRules($siteID, $query){
		$arq = explode(' ', $query);
		
		$rules = self::_getRedirectRules($siteID, count($arq));
		
		if(count($rules)){
			foreach($rules as $ruleText=>$ruleHref){
				$arRule = explode(' ', $ruleText);
				foreach($arRule as $rWord){
					if(!preg_match("/$rWord/i".BX_UTF_PCRE_MODIFIER, $query)){
						// echo '<pre>'; print_r('breaker '.$rWord); echo '</pre>';
						continue(2);
					}else{
						// echo '<pre>'; print_r('match '.$rWord); echo '</pre>';
					}
				}
				
				LocalRedirect($ruleHref, false, "301 Moved permanently");
				exit;
			}
			
			// echo '<pre>'; print_r($rules); echo '</pre>';
		}
	}
		static function _getRedirectRules($siteID, $wconunt){
			$rules = [];
			
			$file = $_SERVER["DOCUMENT_ROOT"].str_replace('SITE_ID', $siteID, self::REDIRECT_FILE);
			if(file_exists($file)){
				$obCache = new CPHPCache();
				$cacheId = md5("agsm_rdrules_".$siteID."_".filemtime($file));
				$cachePath = '/arturgolubev.smartsearch/'.self::CACHE_VERSION.'_'.SITE_ID.'/rdrules/'.$wconunt;	
				
				if($obCache->InitCache(self::CACHE_TIME, $cacheId, $cachePath)){
					$vars = $obCache->GetVars();
					$rules = $vars['rules'];
				}elseif($obCache->StartDataCache()){
					$arFileRules = explode(PHP_EOL, file_get_contents($file));
					if(is_array($arFileRules)){
						foreach($arFileRules as $lineRule){
							$arRule = explode('||', ToLower($lineRule));							
							if(!$arRule[0] || !$arRule[1]) continue;
							
							foreach(explode('|', $arRule[1]) as $rule){
								$rule = trim($rule);
								if($rule){
									if(count(explode(' ', $rule)) != $wconunt) continue;
									$rule = str_replace(array('.', '*'), array('\.', '.*'), $rule);
									$rules[$rule] = trim($arRule[0]);
								}
							}
						}
					}
					
					$obCache->EndDataCache(array('rules' => $rules));
				}
			}
			
			return $rules;
		}
	
	static function prepareQuery($query){
		if(defined("SMARTSEARCH_REPLACE_REGULAR")){
			$replace = SMARTSEARCH_REPLACE_REGULAR;
		}else{
			$replace = (defined("BX_UTF")) ? '/[^\w\d]/ui' : '/[\'\"?!:^~|@$=+*&.,;()\-_#\[\]\<\>\/]/i';
		}
		
		$query = preg_replace('/(\s+)/i', ' ', ToLower($query));
		
		if(Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_E_REPLACE"))
			$query = str_replace(Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_E_REPLACE"), Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_E_REPLACE_S"), $query);
		
		$tmp = explode(' ', $query);
		$arQuery = [];
		
		foreach($tmp as $word)
		{
			$word = preg_replace($replace, '', $word);
			if($word && !in_array($word, $arQuery, true)){
				$arQuery[] = $word;
			}
		}

		return trim(implode(' ', $arQuery));
	}
	
	static function clearExceptionsWords($query){
		$arExc = self::_getExceptionsWords();
		if(is_array($arExc) && !empty($arExc)){
			$tmp = explode(' ', $query);
			$arQuery = [];
			foreach($tmp as $word)
			{
				if(in_array($word, $arExc)){
					continue;
				}
				
				if($word && !in_array($word, $arQuery, true)){
					$arQuery[] = $word;
				}
			}
			
			$query = implode(' ', $arQuery);
		}
		
		return $query;
	}
		static function _getExceptionsWords(){
			$st = UTools::getStorage('page_cache', 'exception_words');
			if(is_array($st)){
				$r = $st;
			}else{
				$r = [];
				$dbW = UTools::getSetting('exception_words_list');
				if($dbW){
					$r = [];
					$arW = explode(',', ToLower($dbW));
					foreach($arW as $k=>$v){
						$r[$k] = trim($v);
					}
				}
				
				UTools::setStorage('page_cache', 'exception_words', $r);
			}
			
			return $r;
		}
	
	static function prepBaseArray($words, $checkLength){
		$result = [];
		
		if(is_array($words) && count($words)){
			$replace = Tools::getReplaceParams();
			$min_length = Tools::getMinWordLenght();
			
			foreach($words as $word){
				// $word = self::prepareQuery($word);
				if($checkLength && Encoding::exStrlen($word) < $min_length) continue;
				
				if(preg_match('/[\d]+/i', $word)){
					$trans = str_replace(array('s'), array('c'), $word);
					$trans = Tools::num_translit($trans, "ru", $replace);
				}else{
					$trans = Tools::ex_translit($word, "ru", $replace);
				}
				
				if($trans)
					$result[$word] = $trans;
			}
		}
		
		return $result;
	}
	
	static function getWordsListFromDb($params){
		$obCache = new CPHPCache();
		$cacheId = md5("base_cache_smart_search_mixed_".$params['filter_map']);
		$cachePath = '/arturgolubev.smartsearch/'.self::CACHE_VERSION.'_'.SITE_ID.'/bd';	
		
		if($obCache->InitCache(self::CACHE_TIME, $cacheId, $cachePath)){
			$vars = $obCache->GetVars();
			$result = $vars['result'];
		}elseif($obCache->StartDataCache()){
			$words = [];
			
			// $res = Tools::dbQuery("SELECT DISTINCT LOWER(WORD) as WORD FROM b_search_content_title WHERE SITE_ID = '".SITE_ID."';");
			
			$select = 'LOWER(WORD) as WORD';
			// $select = 'LOWER(WORD) as WORD, sc.PARAM2 as IBLOCK, sc.MODULE_ID as MODULE';
			$from = 'b_search_content_title as st inner join b_search_content sc on sc.ID = st.SEARCH_CONTENT_ID';
			$where = "st.SITE_ID = '".SITE_ID."'";
			
			if(is_array($params['filter']) && count($params['filter'])){
				$where2 = '';
				foreach($params['filter'] as $k=>$filter){
					$and = 0;
					if($k){
						$where2 .= ' OR ';
					}
					
					$where2 .= '(';
						if($filter['MODULE_ID']){
							$where2 .= 'sc.MODULE_ID = "'.$filter['MODULE_ID'].'"';
							$and = 1;
						}
						if($filter['PARAM1']){
							if($and){
								$where2 .= ' AND ';
							}
							$where2 .= 'sc.PARAM1 = "'.$filter['PARAM1'].'"';
							$and = 1;
						}
						if(is_array($filter['PARAM2']) && count($filter['PARAM2'])){
							if($and){
								$where2 .= ' AND ';
							}
							if(count($filter['PARAM2']) == 1){
								$where2 .= 'sc.PARAM2 = '.$filter['PARAM2'][0];
							}else{
								$where2 .= 'sc.PARAM2 IN('.implode(', ', $filter['PARAM2']).')';
							}
						}
					$where2 .= ')';
				}
				
				$where .= ' AND ('.$where2.')';
			}
			
			$sql = "SELECT DISTINCT ".$select." FROM ".$from." WHERE ".$where.";";
			
			// echo '<pre>'; print_r($params); echo '</pre>';
			// echo '<pre>'; print_r($sql); echo '</pre>';
			
			$res = Tools::dbQuery($sql);
			while ($arFields = $res->Fetch()){
				// echo '<pre>'; print_r($arFields); echo '</pre>';
				$words[] = $arFields["WORD"];
			}
			unset($res);
			
			$result = self::prepBaseArray($words, 0);
			unset($words);
			
			$obCache->EndDataCache(array('result' => $result));
		}
		
		return $result;
	}
		static function getBaseWords($params){			
			$obCache = new CPHPCache();
			$cacheId = md5("base_cache_smart_search_clear_".$params['filter_map']);
			$cachePath = '/arturgolubev.smartsearch/'.self::CACHE_VERSION.'_'.SITE_ID.'/bd';	
			
			if($obCache->InitCache(self::CACHE_TIME, $cacheId, $cachePath)){
				$vars = $obCache->GetVars();
				$result = $vars['result'];
			}elseif($obCache->StartDataCache()){
				$result = self::getWordsListFromDb($params);
				$result = array_keys($result);
				$obCache->EndDataCache(array('result' => $result));
			}
			
			return $result;
		}
		
	static function getSimilarWordsList($query, $type = 'full', $params = []){
		$start = microtime(true);
		
		$params['filter_map'] = '';
		if(is_array($params['filter']) && count($params['filter'])){
			foreach($params['filter'] as $fk=>$fv){
				if($fv['MODULE_ID']){
					$params['filter_map'] .= $fv['MODULE_ID'].'_';
				}
				if($fv['PARAM1']){
					$params['filter_map'] .= $fv['PARAM1'].':';
				}
				if(is_array($fv['PARAM2'])){
					$params['filter_map'] .= implode('_', $fv['PARAM2']).'_';
				}
			}
		}
		
		if(!is_array($query)){
			$query = self::prepareQuery($query);
			$queryWordsList = self::prepBaseArray(explode(' ', $query), 1);
			// $queryWordsList = self::prepareQueryWords($query);
		}else{
			$queryWordsList = $query;
		}
		
		if(count($queryWordsList) < 1) return [];
		
		$params['cache'] = (UTools::getSetting("disable_cache") != 'Y');
		$params['engine'] = \COption::GetOptionString("search", 'full_text_engine');
				
		if($params['engine'] == 'sphinx'){
			$mode = 'standart';
		}else{
			$mode = (($type == 'title') ? UTools::getSetting("mode_stitle") : UTools::getSetting("mode_spage"));
		}
		
		$obCache = new CPHPCache();
		$cacheId = md5(implode('_', $queryWordsList).$mode.$params['filter_map']);
		$cachePath = '/arturgolubev.smartsearch/'.self::CACHE_VERSION.'_'.SITE_ID.'/combinations_'.$type.'/'. Encoding::exSubstr(implode('_', array_keys($queryWordsList)), 0, 40);	
		
		if($params['cache'] && $obCache->InitCache(self::CACHE_TIME, $cacheId, $cachePath)){
			$from = 'cache';
			$vars = $obCache->GetVars();
			$result = $vars['result'];
		}elseif($obCache->StartDataCache()){
			$from = 'get';
			$result = self::_getSimilarWordsList($queryWordsList, $type, $mode, $params);
			$obCache->EndDataCache(array('result' => $result));
		}
				
		if(UTools::getSetting("debug") == 'Y'){
			$finish = microtime(true);
			$delta = round($finish - $start, 3);
			AddMessage2Log("Similarity Words " . $from . " " .$delta, self::MODULE_ID, 0);
		}
		
		return $result;
	}
		static function _getSimilarWordsList($queryWordsList, $type, $mode, $params = []){
			$result = [];
			
			$dbWordsList = self::getWordsListFromDb($params);
			
			$preCountVariation = 0;
			foreach ($queryWordsList as $queryWord => $translated) {
				$settings = array(
					"cache" => (UTools::getSetting("disable_cache") != 'Y'),
					"word" => $queryWord,
					"trans" => $translated,
					"type" => $type,
					"wordscount" => count($queryWordsList),
					"mode" => $mode,
					"engine" => $params['engine'],
					"filter_map" => $params['filter_map'],
				);
				
				$arWords = self::getSimilarQueryWord($dbWordsList, $settings);			
				if(!empty($arWords))
				{
					$arFindedWords[] = $arWords;
					$preCountVariation += ($preCountVariation+1)*count($arWords);
				}
			}	
			unset($dbWordsList); 
			
			$cutCount = 200;
			
			if(!empty($arFindedWords))
			{
				if($preCountVariation < $cutCount){
					$wordMatrix = self::generateVariation($arFindedWords);
					$variation = self::generateVariants($arFindedWords);
					
					foreach(array_merge($wordMatrix, $variation) as $wordAr){
						$result[count($wordAr)][] = implode(' ', $wordAr);
					}
					unset($wordMatrix); unset($variation);
				}
				else
				{
					$wordMatrix = self::generateVariation($arFindedWords);
					if(count($wordMatrix) < $cutCount)
					{
						foreach($wordMatrix as $wordAr){
							$result[count($wordAr)][] = implode(' ', $wordAr);
						}
					}
					
					$result[1] = [];
					foreach($arFindedWords as $k=>$v){
						foreach($v as $kk=>$vv)
						{
							$result[1][] = $vv;
						}
					}
				}
				
				foreach($result as $key=>$arVals){
					$result[$key] = array_values(array_unique($arVals));
				}
			}
			
			// echo '<pre>'; print_r($arFindedWords); echo '</pre>';
			// echo '<pre>'; print_r($preCountVariation); echo '</pre>';
			// echo '<pre>'; print_r($result); echo '</pre>';
			
			return $result;
		}
	
	static function getSimilarQueryWord($dbWordsList, $settings){
		$results = [];
		
		$obCache = new CPHPCache();
		$cacheId = md5($settings["type"].'_'.$settings["word"].'_'.$settings['mode'].$settings['filter_map']);
		$cachePath = '/arturgolubev.smartsearch/'.self::CACHE_VERSION.'_'.SITE_ID.'/words_'.$settings["type"].'/'.$settings["trans"];	
		
		if($settings["cache"] && $obCache->InitCache(self::CACHE_TIME, $cacheId, $cachePath)){
			$settings["from"] = 'cache';
			
			$vars = $obCache->GetVars();
			$results = $vars['results'];
		}
		elseif($obCache->StartDataCache()){
			$settings["from"] = 'get';

			$results = self::_getSimilar($dbWordsList, $settings);

			$obCache->EndDataCache(array('results' => $results));
		}

		return $results;
	}

	static function _prepareSimilarSetting($settings){
		$settings["extended_mode"] = ($settings['mode'] != 'standart');
		$settings["metaphone_mode"] = (UTools::getSetting("mode_metaphone") != 'N');
		$settings["stripos_mode"] = ($settings["extended_mode"] || $settings["type"] == 'full');
		$settings["is_num"] = preg_match('/[\d]+/i', $settings["trans"]);
		
		if(!$settings["is_num"] && $settings["extended_mode"] && function_exists("stemming")){
			$settings["stemming_full"] = stemming($settings["word"]);
			if(!empty($settings["stemming_full"])){
				foreach($settings["stemming_full"] as $k=>$v){
					if($k) $settings["word_stemming"] = ToLower($k);
					break;
				}
			}
		}

		$settings["word_len"] = min(Encoding::exStrlen($settings["trans"]), Encoding::exStrlen($settings["word"]));
		if($settings["word_len"] <= 5){
			$settings["word_len_check"] = 1;
		}elseif($settings["word_len"] >= 9){
			$settings["word_len_check"] = 3;
		}else{
			$settings["word_len_check"] = 2;
		}

		return $settings;
	}

	static function _getSimilar($dbWordsList, $settings){
		$results = [];

		$times = [
			'start' => microtime(true)
		];

		$settings = self::_prepareSimilarSetting($settings);
		$symbol = ($settings['engine'] == 'sphinx') ? '*' : '"';
		
		$times['prepare'] = round((microtime(true) - $times['start']), 5);
		
		$as = array_search($settings["trans"], $dbWordsList);
		if($as){
			unset($dbWordsList[$as]);
			
			if($settings["wordscount"] > 1 && $settings["word_stemming"] && $settings["word"] == $as){
				$as = $settings["word_stemming"];
			}
			
			if($settings["type"] == 'title' || ($settings["type"] == 'full' && !$settings["extended_mode"])){
				$results[] = $as;
			}else{
				$results[] = $symbol.$as.$symbol;
			}
			
			$settings["metaphone_mode"] = 0;
			$settings["stripos_mode"] = 0;
		}
		$times['keysearch'] = round((microtime(true) - $times['start']), 5);
		
		if($settings["stripos_mode"]){
			$settings["stripos_stemming"] = ($settings["word_stemming"]) ? $settings["word_stemming"] : $settings["word"];
			
			foreach($dbWordsList as $rus=>$trans){
				$stpos = Encoding::exStripos($rus, $settings["stripos_stemming"]);
				
				if(($settings["extended_mode"] && $stpos !== false) || (!$settings["extended_mode"] && $stpos === 0)){
					if($settings["type"] == 'title'){
						$results[] = $settings["stripos_stemming"];
					}else{
						$results[] = $symbol.$settings["stripos_stemming"].$symbol;
					}
					
					unset($dbWordsList[$rus]);
				}
			}
			
			if(count($results)){
				$results = array_unique($results);
			}
			
			$times['stripos'] = round((microtime(true) - $times['start']), 5);
		}
		
		if($settings["metaphone_mode"] && !$settings["is_num"] && $settings['word_len'] > 2){
			$tmpResults = [];
			
			foreach ($dbWordsList as $rus => $trans) {
				if(preg_match('/[\d]+/i', $trans)) continue;
				
				$lvs = levenshtein($settings["trans"], $trans);
				if ($lvs <= $settings["word_len_check"]) {
					similar_text($settings["word"], $rus, $lvs2);
					$lvs3 = levenshtein($settings["word"], $rus);
					
					$tmpResults[] = array(
						"word" => array($rus => $trans),
						"similarity" => $lvs,
						"similarity_r" => $lvs2,
						"similarity_rl" => $lvs3,
					);
				}
			}
			
			if(!count($tmpResults)){
				usort($tmpResults, array("CArturgolubevSmartsearch", "cmpSimilaritySort"));
			}
			
			foreach($tmpResults as $tmpResult){
				foreach($tmpResult["word"] as $k=>$v)
					$results[] = $k;
			}
			
			$times['metaphone'] = round((microtime(true) - $times['start']), 5);
		}
		
		if(!count($results)){
			$subresdb = [];
			foreach ($dbWordsList as $rus => $trans){
				$subresdb[$rus] = Encoding::exStrlen($rus);
			}
			
			arsort($subresdb);
			
			$subres1 = '';
			$subres2 = $settings['word'];
			
			foreach ($subresdb as $rus => $length){
				if(!$subres2) break;
				
				$stpos = Encoding::exStripos($subres2, $rus);
				if($stpos !== false){
					$subres1 .= $rus.' ';
					$subres2 = str_replace($rus, '', $subres2);
				}
			}
			
			if(!$subres2 && $subres1){
				$results[] = $subres1;
			}
			
			$times['explode'] = round((microtime(true) - $times['start']), 5);
		}
	
		// echo 'dbWordsList <pre>'; print_r($dbWordsList); echo '</pre>';
		// echo 'settings <pre>'; print_r($settings); echo '</pre>';

		// echo 'tmpResults <pre>'; print_r($tmpResults); echo '</pre>';
		// echo 'results <pre>'; print_r($results); echo '</pre>';

		// echo '<pre>'; print_r($times); echo '</pre>';

		return $results;
	}

	
	static function guessLanguage($text, $params = []){
		if(!$text) return 0;
		
		// $start = microtime(true);
		
		$params['filter_map'] = '';
		if(is_array($params['filter']) && count($params['filter'])){
			foreach($params['filter'] as $fk=>$fv){
				if($fv['MODULE_ID']){
					$params['filter_map'] .= $fv['MODULE_ID'].'_';
				}
				if($fv['PARAM1']){
					$params['filter_map'] .= $fv['PARAM1'].':';
				}
				if(is_array($fv['PARAM2'])){
					$params['filter_map'] .= implode('_', $fv['PARAM2']).'_';
				}
			}
		}
		
		$obCache = new CPHPCache();
		
		$result = array(
			'result' => [],
			'variants' => [],
			'error' => 0,
			'cicle' => 0,
		);
		
		$result['main_arr'] = explode(' ', preg_replace('/(\s+)/i', ' ', trim($text)));
		
		$replace = Tools::getReplaceParams();
		
		$dbWordsList = self::getBaseWords($params);
		
		foreach($result['main_arr'] as $k=>$word){
			$tmp = CSearchLanguage::ConvertKeyboardLayout($word, 'en', 'ru');
			$tmp = CArturgolubevSmartsearch::checkReplaceRules($tmp);
			$tmp = CArturgolubevSmartsearch::prepareQuery($tmp);
			$tmp = CArturgolubevSmartsearch::clearExceptionsWords($tmp);
			$result['variants']["ru"][] = $tmp;
			
			$tmp = CSearchLanguage::ConvertKeyboardLayout($word, 'ru', 'en');
			$tmp = CArturgolubevSmartsearch::checkReplaceRules($tmp);
			$tmp = CArturgolubevSmartsearch::prepareQuery($tmp);
			$tmp = CArturgolubevSmartsearch::clearExceptionsWords($tmp);
			$result['variants']["en"][] = $tmp;
		}
		
		foreach($result['variants']["ru"] as $k=>$word){
			$eWord = $result['variants']["en"][$k];
			$wTrans = Tools::ex_translit($word.$eWord, "ru", $replace);
			
			$cachePath = '/arturgolubev.smartsearch/'.self::CACHE_VERSION.'_'.SITE_ID.'/guess_word/'.$wTrans;	
			if($obCache->InitCache(self::CACHE_TIME, $wTrans, $cachePath))
			{
				$vars = $obCache->GetVars();
				$find = $vars['find'];
			}
			elseif($obCache->StartDataCache())
			{
				$find = 0;
				$result['cicle']++;
				foreach($dbWordsList as $rus){
					$stpos = Encoding::exStripos($rus, $word);
					if($stpos !== false){
						$find = 1;
						break;
					}
				}
				if(!$find){
					$result['cicle']++;
					foreach($dbWordsList as $rus){
						$stpos = Encoding::exStripos($rus, $eWord);
						if($stpos !== false){
							$find = 2;
							break;
						}
					}
				}
				
				$obCache->EndDataCache(array('find' => $find));
			}
			
			if(!$find){
				return 0;
			}elseif($find == 2){
				$result["result"][] = $eWord;
			}elseif($find){
				$result["result"][] = $word;
			}
		}
		
		// echo '<pre>time: '; print_r(round(microtime(true) - $start, 3)); echo '</pre>';
		// echo '<pre>'; print_r($result); echo '</pre>';
		
		if(count($result["result"])){
			return implode(' ', $result["result"]);
		}
		
		return 0;
	}
	
	
	static function generateVariation($A, $i = 0){
		// echo '<pre>'; print_r('generateVariation'); echo '</pre>';
		$result = [];
		
		if ($i < count($A)){
			$variations = self::generateVariation($A, $i + 1);
			for ($j = 0; $j < count($A[$i]); $j++){
				if ($variations){
					foreach ($variations as $variation){
						$result[] = array_merge(array($A[$i][$j]), $variation);
					}
				}else{
					$result[] = array($A[$i][$j]);
				}
			}
		}
		
		return $result;
	}
	static function generateVariants($ar){		
		$result = [];
		if(count($ar)>1)
		{
			for($i=count($ar);$i>0;$i--){
				$arCopy = $ar;
				
				unset($arCopy[($i-1)]);
				$arCopy = array_values($arCopy);
				
				// $tmpVariation = self::generateVariation($arCopy);
				// foreach($tmpVariation as $variation) $result[] = $variation;
				
				$result = array_merge($result, self::generateVariation($arCopy)); 
				
				if(count($arCopy)>1){
					// $result2 = self::generateVariants($arCopy);
					// foreach($result2 as $v) $result[] = $v;
					
					$result = array_merge($result, self::generateVariants($arCopy));
				}
			}
		}
		
		return $result;
	}
	static function cmpSimilaritySort($a, $b){
		if ($a["similarity"] == $b["similarity"]){
			if($a["similarity_rl"] == $b["similarity_rl"]){
				return 0;
			}
			return ($a["similarity_rl"] < $b["similarity_rl"]) ? -1 : 1;
		}
		
		return ($a["similarity"] < $b["similarity"]) ? -1 : 1;
	}
	
	/* empty lines */
	static function getMatrixLineHash($query){
		$arWords = explode(' ', str_replace('"', '', $query));
		sort($arWords);
		$md = implode('_', $arWords);
		
		return $md;
	}
	
	static function saveMatrixLineEmpty($query, $cnt){
		$hash = self::getMatrixLineHash($query);
		// echo '<pre>'; print_r($hash); echo '</pre>';
		
		if($cnt < 1){
			$obCache = new CPHPCache();
			$cachePath = '/arturgolubev.smartsearch/'.self::CACHE_VERSION.'_'.SITE_ID.'/emptylines';	
			if($obCache->InitCache(self::CACHE_TIME, md5($hash), $cachePath)){
				$vars = $obCache->GetVars();
				// echo '<pre>save has cache: '; print_r($vars); echo '</pre>';
			}elseif($obCache->StartDataCache()){
				$vars = ['empty' => 'Y'];
				$obCache->EndDataCache($vars);
				// echo '<pre>save add cache: '; print_r($vars); echo '</pre>';
			}
		}
	}
	
	static function checkMatrixLineEmpty($query){	
		if(UTools::getSetting("disable_cache") == 'Y') return 0;
	
		$hash = self::getMatrixLineHash($query);
		
		$obCache = new CPHPCache();
		$cachePath = '/arturgolubev.smartsearch/'.self::CACHE_VERSION.'_'.SITE_ID.'/emptylines';	
		if($obCache->InitCache(self::CACHE_TIME, md5($hash), $cachePath)){
			$vars = $obCache->GetVars();
			// echo '<pre>check has cache: '; print_r($vars); echo '</pre>';
			if($vars['empty'] == 'Y'){
				return 1;
			}
		}
		
		return 0;
	}
	
	/* old versions ready for delete */
	static function prepareQueryWords($q){
		$result = [];
		$aw = explode(' ', $q);
		
		$replace = Tools::getReplaceParams();
		$min_length = Tools::getMinWordLenght();
		
		foreach($aw as $sWord){
			if(Encoding::exStrlen($sWord) < $min_length) continue;
			
			if(preg_match('/[\d]+/i', $sWord)){
				$sWord = str_replace(array('s'), array('c'), $sWord);
				$tmpWord = Tools::num_translit($sWord, "ru", $replace);
			}else{
				$tmpWord = Tools::ex_translit($sWord, "ru", $replace);
			}
			
			if($tmpWord && !in_array($tmpWord, $result))
				$result[$tmpWord] = $sWord;
		}
		
		return $result;
	}
}
?>