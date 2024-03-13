<?
namespace Arturgolubev\Smartsearch;

use \Arturgolubev\Smartsearch\Unitools as UTools;

class SearchComponent {
	public $options = []; 
	public $baseQuery = false; 
	public $query = false; 
	public $system_mode = false; 

	public function __construct($q, $type = '') {
		$this->options['disable_item_id_filter'] = 0;
		
		$this->options['debug'] = UTools::getSetting('debug');
		
		$this->options['theme_class'] = UTools::getSetting('color_theme', 'blue');

		$this->options['theme_color'] = UTools::getSetting('my_color_theme');
		
		$this->options['use_fixes'] = (UTools::getSetting('mode_metaphone') == 'Y');
		$this->options['use_clarify'] = (UTools::getSetting('clarify_section') == "Y");
		$this->options['use_guessplus'] = (UTools::getSetting("mode_guessplus") == "Y");
		
		$this->options['symbol'] = '"';
		
		$this->options['engine'] = \COption::GetOptionString("search", 'full_text_engine');
		$this->options['use_stemming'] = (\COption::GetOptionString("search", 'use_stemming') == 'Y');
				
		if($type == 'page'){
			$this->options['mode'] = UTools::getSetting("mode_spage");
		}elseif($type == 'title'){
			$this->options['theme_placeholder'] = UTools::getSetting('input_search_placeholder');
			$this->options['mode'] = UTools::getSetting("mode_stitle");
		}
				
		if($this->options['engine'] == 'sphinx'){
			$this->options['symbol'] = '*';
			// $this->options['mode'] = 'standart';
		}
		
		if($q){
			$q = str_replace('+', ' ', $q);
			
			$this->baseQuery = $q;
			
			foreach(GetModuleEvents(\CArturgolubevSmartsearch::MODULE_ID, "onBeforePrepareQuery", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array(&$q));
			
			$q = str_replace('&nbsp;', ' ', $q);
			
			$q = \CArturgolubevSmartsearch::checkReplaceRules($q);
			$q = \CArturgolubevSmartsearch::prepareQuery($q);
			$q = \CArturgolubevSmartsearch::clearExceptionsWords($q);
			
			$this->query = $q;
		}
	}

	public function getOption($name){
		return $this->options[$name];
	}

	public function addQuerySymbols($q){
		$q = $this->getOption('symbol').str_replace(' ', $this->getOption('symbol').' '.$this->getOption('symbol'), $q).$this->getOption('symbol');

		return $q;
	}

	public function getClarify($resultElements){
		$result = [];

		// $cacheID = md5(implode(',',$resultElements));

		$sections = [];
		$res = \CIBlockElement::GetList([], ["ID"=>$resultElements], ["IBLOCK_SECTION_ID"], false, ["ID", "IBLOCK_SECTION_ID"]);
		while($arFields = $res->Fetch()){
			if($arFields["IBLOCK_SECTION_ID"]){
				$sections[$arFields["IBLOCK_SECTION_ID"]] = $arFields["CNT"];
			}
		}

		if(count($sections)>0){
			$db_list = \CIBlockSection::GetList(["NAME"=>"ASC"], ["ID" => array_keys($sections)], false, ["ID", "NAME"]);
			while($ar_result = $db_list->GetNext()){
				$ar_result["CNT"] = $sections[$ar_result["ID"]];
				$result[] = $ar_result;
			}
		}

		return $result;
	}

	public function applyClarify($resultElements, $selected){
		if(!$selected){
			return $resultElements;
		}

		$result = [];

		$inSection = [];
		$res = \CIBlockElement::GetList([], ["ID"=>$resultElements, "IBLOCK_SECTION_ID" => $selected], false, false, ["ID"]);
		while($arFields = $res->Fetch()){
			$inSection[] = $arFields["ID"];
		}
		
		foreach($resultElements as $pid){
			if(in_array($pid, $inSection)){
				$result[] = $pid;
			}
		}
		
		return $result;
	}


	public static function addWords($query){
		if($query){
			$saved_words = UTools::getStorage('search_component', 'words');
			
			if(!is_array($saved_words)){
				$saved_words = [];
			}

			foreach(explode(' ', $query) as $word){
				$saved_words[] = $word;
			}

			$saved_words = array_unique($saved_words);

			UTools::setStorage('search_component', 'words', $saved_words);
		}
	}
	
	public function setTitle(){
		$this->options['set_page_title'] = (UTools::getSetting("set_title") == 'Y');
		
		if($this->options['set_page_title']){
			$this->options['set_page_title_template'] = UTools::getSetting("set_title_template");
			
			if($this->options['set_page_title_template']){
				global $APPLICATION;
				$APPLICATION->SetPageProperty("title", str_replace('#QUERY#', $this->baseQuery, $this->options['set_page_title_template']));
			}
		}
	}
	
	public function setItemIdFilterMode($disableParam){
		if($disableParam == 'Y' || $this->options['engine'] == 'sphinx'){
			$this->options['disable_item_id_filter'] = 1;
		}
	}
	
	public $folderPath = '';
	public function searchRowPrepare($ar){
		if(!$this->system_mode){
			global $APPLICATION;
			
			$ar["CHAIN_PATH"] = $APPLICATION->GetNavChain($ar["URL"], 0, $this->folderPath."/chain_template.php", true, false);
			$ar["URL"] = htmlspecialcharsbx($ar["URL"]);
			$ar["TAGS"] = array();
			if (!empty($ar["~TAGS_FORMATED"]))
			{
				foreach ($ar["~TAGS_FORMATED"] as $name => $tag)
				{
					if($arParams["TAGS_INHERIT"] == "Y")
					{
						$arTags = $arResult["REQUEST"]["~TAGS_ARRAY"];
						$arTags[$tag] = $tag;
						$tags = implode("," , $arTags);
					}
					else
					{
						$tags = $tag;
					}
					$ar["TAGS"][] = array(
						"URL" => $APPLICATION->GetCurPageParam("tags=".urlencode($tags), array("tags")),
						"TAG_NAME" => htmlspecialcharsex($name),
					);
				}
			}
		}
		
		return $ar;
	}
	
	public static function reformatDescription($old, $newPreview, $textLengh){
		$newPreview = strip_tags(htmlspecialchars_decode($newPreview));
		$newPreview = \CArturgolubevSmartsearch::formatElementName($old, $newPreview);
		
		if(Encoding::exStrlen($newPreview) > $textLengh){
			if(Encoding::exStrpos($newPreview, '<') !== false){
				$startPos = Encoding::exStrpos($newPreview, '<') - ($textLengh / 2);
				if($startPos > 0){
					$newPreview = Encoding::exSubstr($newPreview, $startPos);
				}
				
				if(Encoding::exStrlen($newPreview) > $textLengh){
					$obParser = new \CTextParser;
					$newPreview = $obParser->html_cut($newPreview, $textLengh);
				}
				
				if($startPos > 0){
					$newPreview = '...'.$newPreview;
				}
			}else{
				$obParser = new \CTextParser;
				$newPreview = $obParser->html_cut($newPreview, $textLengh);
			}
		}
		
		return $newPreview;
	}
}