<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/search/classes/mysql/search.php");

class CSearchExt extends CSearch
{
	function Search($arParams, $aSort = array(), $aParamsEx = array(), $bTagsCloud = false)
	{
		$DB = CDatabase::GetModuleConnection('search');

		if (!is_array($arParams))
			$arParams = array("QUERY" => $arParams);

		if (!is_set($arParams, "SITE_ID") && is_set($arParams, "LID"))
		{
			$arParams["SITE_ID"] = $arParams["LID"];
			unset($arParams["LID"]);
		}

		if (array_key_exists("TAGS", $arParams))
		{
			$this->strTagsText = $arParams["TAGS"];
			$arTags = explode(",", $arParams["TAGS"]);
			foreach ($arTags as $i => $strTag)
			{
				$strTag = trim($strTag);
				if($strTag <> '')
				{
					$arTags[$i] = str_replace("\"", "\\\"", $strTag);
				}
				else
				{
					unset($arTags[$i]);
				}
			}

			if (count($arTags))
				$arParams["TAGS"] = '"'.implode('","', $arTags).'"';
			else
				unset($arParams["TAGS"]);
		}

		$this->strQueryText = $strQuery = trim($arParams["QUERY"]);
		$this->strTags = $strTags = $arParams["TAGS"];

		if (($strQuery == '') && ($strTags <> ''))
		{
			$strQuery = $strTags;
			$bTagsSearch = true;
		}
		else
		{
			if($strTags <> '')
			{
				$strQuery .= " ".$strTags;
			}
			$strQuery = preg_replace_callback("/&#(\\d+);/", array($this, "chr"), $strQuery);
			$bTagsSearch = false;
		}

		if (!array_key_exists("STEMMING", $aParamsEx))
			$aParamsEx["STEMMING"] = COption::GetOptionString("search", "use_stemming") == "Y";
		$this->Query = new CSearchQuery("and", "yes", 0, $arParams["SITE_ID"]);
		if ($this->_opt_NO_WORD_LOGIC)
			$this->Query->no_bool_lang = true;
		$query = $this->Query->GetQueryString((BX_SEARCH_VERSION > 1? "sct": "sc").".SEARCHABLE_CONTENT", $strQuery, $bTagsSearch, $aParamsEx["STEMMING"], $this->_opt_ERROR_ON_EMPTY_STEM);

		$fullTextParams = $aParamsEx;
		if (!isset($fullTextParams["LIMIT"]))
			$fullTextParams["LIMIT"] = $this->limit;
		$fullTextParams["OFFSET"] = $this->offset;
		$fullTextParams["QUERY_OBJECT"] = $this->Query;
		$result = CSearchFullTextExt::getInstance()->search($arParams, $aSort, $fullTextParams, $bTagsCloud);
		if (is_array($result))
		{
			$this->error = CSearchFullTextExt::getInstance()->getErrorText();
			$this->errorno = CSearchFullTextExt::getInstance()->getErrorNumber();
			$this->formatter = CSearchFullTextExt::getInstance()->getRowFormatter();
			if ($this->errorno > 0)
				return;
		}
		else
		{
			if (!$query || trim($query) == '')
			{
				if ($bTagsCloud)
				{
					$query = "1=1";
				}
				else
				{
					$this->error = $this->Query->error;
					$this->errorno = $this->Query->errorno;
					return;
				}
			}

			if (mb_strlen($query) > 2000)
			{
				$this->error = GetMessage("SEARCH_ERROR4");
				$this->errorno = 4;
				return;
			}
		}

		foreach (GetModuleEvents("search", "OnSearch", true) as $arEvent)
		{
			$r = "";
			if ($bTagsSearch)
			{
				if($strTags <> '')
				{
					$r = ExecuteModuleEventEx($arEvent, array("tags:".$strTags));
				}
			}
			else
			{
				$r = ExecuteModuleEventEx($arEvent, array($strQuery));
			}
			if ($r <> "")
				$this->url_add_params[] = $r;
		}

		if (is_array($result))
		{
			$r = new CDBResult;
			$r->InitFromArray($result);
		}
		elseif (
			BX_SEARCH_VERSION > 1
			&& !empty($this->Query->m_stemmed_words_id)
			&& is_array($this->Query->m_stemmed_words_id)
			&& array_sum($this->Query->m_stemmed_words_id) === 0
		)
		{
			$r = new CDBResult;
			$r->InitFromArray(array());
		}
		else
		{
			$this->strSqlWhere = "";
			$bIncSites = false;

			$arSqlWhere = array();
			if (is_array($aParamsEx) && !empty($aParamsEx))
			{
				foreach ($aParamsEx as $aParamEx)
				{
					$strSqlWhere = CSearch::__PrepareFilter($aParamEx, $bIncSites);
					if ($strSqlWhere != "")
						$arSqlWhere[] = $strSqlWhere;
				}
			}
			if (!empty($arSqlWhere))
			{
				$arSqlWhere = array(
					"\n\t\t\t\t(".implode(")\n\t\t\t\t\tOR(", $arSqlWhere)."\n\t\t\t\t)",
				);
			}

			$strSqlWhere = CSearch::__PrepareFilter($arParams, $bIncSites);
			if ($strSqlWhere != "")
				array_unshift($arSqlWhere, $strSqlWhere);

			$strSqlOrder = $this->__PrepareSort($aSort, "sc.", $bTagsCloud);

			if (!array_key_exists("USE_TF_FILTER", $aParamsEx))
				$aParamsEx["USE_TF_FILTER"] = COption::GetOptionString("search", "use_tf_cache") == "Y";

			$bStem = !$bTagsSearch && count($this->Query->m_stemmed_words) > 0;
			//calculate freq of the word on the whole site_id
			if ($bStem && count($this->Query->m_stemmed_words))
			{
				$arStat = $this->GetFreqStatistics($this->Query->m_lang, $this->Query->m_stemmed_words, $arParams["SITE_ID"]);
				$this->tf_hwm_site_id = ($arParams["SITE_ID"] <> ''? $arParams["SITE_ID"]: "");

				//we'll make filter by it's contrast
				if (!$bTagsCloud && $aParamsEx["USE_TF_FILTER"])
				{
					$hwm = false;
					foreach ($this->Query->m_stemmed_words as $i => $stem)
					{
						if (!array_key_exists($stem, $arStat))
						{
							$hwm = 0;
							break;
						}
						elseif ($hwm === false)
						{
							$hwm = $arStat[$stem]["TF"];
						}
						elseif ($hwm > $arStat[$stem]["TF"])
						{
							$hwm = $arStat[$stem]["TF"];
						}
					}

					if ($hwm > 0)
					{
						$arSqlWhere[] = "st.TF >= ".number_format($hwm, 2, ".", "");
						$this->tf_hwm = $hwm;
					}
				}
			}

			if (!empty($arSqlWhere))
			{
				$this->strSqlWhere = "\n\t\t\t\tAND (\n\t\t\t\t\t(".implode(")\n\t\t\t\t\tAND(", $arSqlWhere).")\n\t\t\t\t)";
			}

			if ($bTagsCloud)
				$strSql = $this->tagsMakeSQL($query, $this->strSqlWhere, $strSqlOrder, $bIncSites, $bStem, $aParamsEx["LIMIT"]);
			else
				$strSql = $this->MakeSQL($query, $this->strSqlWhere, $strSqlOrder, $bIncSites, $bStem);

			$r = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}
		
		CDBResult::__construct($r);
	}

	function GetFilterMD5(){
		return '';
	}
	
	function NavStart($nPageSize = 0, $bShowAll = true, $iNumPage = false){
		CDBResult::NavStart($nPageSize, $bShowAll, $iNumPage);
	}
	
	function MakeSQL($query, $strSqlWhere, $strSort, $bIncSites, $bStem){
		$DB = CDatabase::GetModuleConnection('search');

		$bDistinct = false;
		$arSelect = array(
			"ID" => "sc.ID", "MODULE_ID" => "sc.MODULE_ID", "ITEM_ID" => "sc.ITEM_ID",
			"TITLE" => "sc.TITLE", "TAGS" => "sc.TAGS", "URL" => "sc.URL",
			"PARAM1" => "sc.PARAM1", "PARAM2" => "sc.PARAM2",
			"UPD" => "sc.UPD",
			"DATE_FROM" => "sc.DATE_FROM", "DATE_TO" => "sc.DATE_TO", "FULL_DATE_CHANGE" => $DB->DateToCharFunction("sc.DATE_CHANGE")." as FULL_DATE_CHANGE", "DATE_CHANGE" => $DB->DateToCharFunction("sc.DATE_CHANGE", "SHORT")." as DATE_CHANGE",
			"CUSTOM_RANK" => "sc.CUSTOM_RANK",
		);
		if (BX_SEARCH_VERSION > 1)
		{
			if ($this->Query->bText)
				$arSelect["SEARCHABLE_CONTENT"] = "sct.SEARCHABLE_CONTENT";
			$arSelect["USER_ID"] = "sc.USER_ID";
		}
		else
		{
			$arSelect["LID"] = "sc.LID";
			$arSelect["SEARCHABLE_CONTENT"] = "sc.SEARCHABLE_CONTENT";
		}

		if (strpos($strSort, "TITLE_RANK") !== false)
		{
			/*
			if ($bStem){
				foreach ($this->Query->m_stemmed_words as $stem){
					if (strlen($strSelectRank) > 0)
						$strSelectRank .= " + ";
					$strSelectRank .= "if(locate('".$stem."', upper(sc.TITLE)) > 0, locate('".$stem."', upper(sc.TITLE)), 1000)";
				}
			}else{
				foreach ($this->Query->m_words as $word){
					if (strlen($strSelectRank) > 0)
						$strSelectRank .= " + ";
					$strSelectRank .= "if(locate('".$DB->ForSql(ToUpper($word))."', upper(sc.TITLE)) > 0, locate('".$DB->ForSql(ToUpper($word))."', upper(sc.TITLE)), 1000)";
				}
			}
			*/
			
			$rankWord = [];
			
			if ($bStem){
				$wordE = explode(' ', $this->Query->m_query);
				
				foreach ($this->Query->m_stemmed_words as $stem){
					foreach($wordE as $k=>$baseWord){
						if(stripos($baseWord, $stem) !== false){
							$rankWord[] = $stem;
							unset($wordE[$k]);
						}
					}
				}
				
				if(count($wordE)){
					foreach($wordE as $baseWord){
						$rankWord[] = $baseWord;
					}
				}
			}else{
				foreach ($this->Query->m_words as $word){
					$rankWord[] = $word;
				}
			}
			
			$strSelectRank = '';
			foreach ($rankWord as $word){
				$word = ToLower($word);

				if (strlen($strSelectRank) > 0)
					$strSelectRank .= " + ";

				$strSelectRank .= "if(
					(locate('".$word." ', sc.TITLE) = 1 OR locate(' ".$word." ', sc.TITLE) > 0),
					(if(locate('".$word." ', sc.TITLE) = 1, 1, locate(' ".$word." ', sc.TITLE))),
					(if(
						(locate(' ".$word."', sc.TITLE) > 0),
						(locate(' ".$word."', sc.TITLE) + 1000),
						(if(
							(locate('".$word."', sc.TITLE) > 0),
							(locate('".$word."', sc.TITLE) + 10000),
							100000
						))
					))
				)";
			}
			
			$arSelect["TITLE_RANK"] = $strSelectRank." as TITLE_RANK";
			$strSort .= ', TITLE';
		}

		$strStemList = '';
		if ($bStem)
		{
			if (BX_SEARCH_VERSION > 1)
				$strStemList = implode(", ", $this->Query->m_stemmed_words_id);
			else
				$strStemList = "'".implode("' ,'", $this->Query->m_stemmed_words)."'";
		}

		// $bWordPos = BX_SEARCH_VERSION > 1 && COption::GetOptionString("search", "use_word_distance") == "Y";
		$bWordPos = 0;

		if ($bIncSites && $bStem)
		{
			$arSelect["SITE_URL"] = "scsite.URL as SITE_URL";
			$arSelect["SITE_ID"] = "scsite.SITE_ID";

			if (!preg_match("/(sc|sct)./", $query))
			{
				$strSqlWhere = preg_replace('#AND\\(st.TF >= [0-9\.,]+\\)#i', "", $strSqlWhere);

				if (count($this->Query->m_stemmed_words) > 1)
					$arSelect["RANK"] = "stt.RANK as `RANK`";
				else
					$arSelect["RANK"] = "stt.TF as `RANK`";

				$strSql = "
				FROM b_search_content sc
					".($this->Query->bText? "INNER JOIN b_search_content_text sct ON sct.SEARCH_CONTENT_ID = sc.ID": "")."
					INNER JOIN b_search_content_site scsite ON sc.ID=scsite.SEARCH_CONTENT_ID
					".(count($this->Query->m_stemmed_words) > 1?
						"INNER JOIN  (
							select search_content_id, max(st.TF) TF, ".($bWordPos? "if(STDDEV(st.PS)-".$this->normdev(count($this->Query->m_stemmed_words))." between -0.000001 and 1, 1/STDDEV(st.PS), 0) + ": "")."sum(st.TF/sf.FREQ) as `RANK`
							from b_search_content_stem st, b_search_content_freq sf
							where st.language_id = '".$this->Query->m_lang."'
							and st.stem = sf.stem
							and sf.language_id = st.language_id
							and st.stem in (".$strStemList.")
							".($this->tf_hwm > 0? "and st.TF >= ".number_format($this->tf_hwm, 2, ".", ""): "")."
							".(strlen($this->tf_hwm_site_id) > 0? "and sf.SITE_ID = '".$DB->ForSQL($this->tf_hwm_site_id, 2)."'": "and sf.SITE_ID IS NULL")."
							group by st.search_content_id
							having (".$query.")
						) stt ON sc.id = stt.search_content_id"
						: "INNER JOIN b_search_content_stem stt ON sc.id = stt.search_content_id"
					)."
				WHERE
				".CSearch::CheckPermissions("sc.ID")."
				".(count($this->Query->m_stemmed_words) > 1? "": "
					and stt.language_id = '".$this->Query->m_lang."'
					and stt.stem in (".$strStemList.")
					".($this->tf_hwm > 0? "and stt.TF >= ".number_format($this->tf_hwm, 2, ".", ""): "")."")."
				".$strSqlWhere."
				";
			}
			else
			{
				/*
				if (count($this->Query->m_stemmed_words) > 1){
					if ($bWordPos)
						$arSelect["RANK"] = "if(STDDEV(st.PS)-".$this->normdev(count($this->Query->m_stemmed_words))." between -0.000001 and 1, 1/STDDEV(st.PS), 0) + sum(st.TF/sf.FREQ) as `RANK`";
					else
						$arSelect["RANK"] = "sum(st.TF/sf.FREQ) as `RANK`";
				}else{
					$arSelect["RANK"] = "st.TF as `RANK`";
				}
				*/
				
				$arSelect["RANK"] = "1 as `RANK`";

				$strSql = "
				FROM b_search_content sc
					".($this->Query->bText? "INNER JOIN b_search_content_text sct ON sct.SEARCH_CONTENT_ID = sc.ID": "")."
					INNER JOIN b_search_content_site scsite ON sc.ID=scsite.SEARCH_CONTENT_ID
					INNER JOIN b_search_content_stem st ON sc.id = st.search_content_id+0
					".(count($this->Query->m_stemmed_words) > 1?
						"INNER JOIN b_search_content_freq sf ON
							st.language_id = sf.language_id
							and st.stem=sf.stem
							".(strlen($this->tf_hwm_site_id) > 0?
							"and sf.SITE_ID = '".$DB->ForSQL($this->tf_hwm_site_id, 2)."'":
							"and sf.SITE_ID IS NULL"
						):
						""
					)."
				WHERE
					".CSearch::CheckPermissions("sc.ID")."
					AND st.STEM in (".$strStemList.")
					".(count($this->Query->m_stemmed_words) > 1? "AND sf.STEM in (".$strStemList.")": "")."
					AND st.language_id='".$this->Query->m_lang."'
					".$strSqlWhere."
				GROUP BY
					sc.ID
					,scsite.URL
					,scsite.SITE_ID
				HAVING
					(".$query.")
				";
			}
		}
		elseif ($bIncSites && !$bStem)
		{
			$bDistinct = true;

			$arSelect["SITE_URL"] = "scsite.URL as SITE_URL";
			$arSelect["SITE_ID"] = "scsite.SITE_ID";
			$arSelect["RANK"] = "1 as `RANK`";

			if ($this->Query->bTagsSearch)
			{
				$strSql = "
				FROM b_search_content sc
					".($this->Query->bText? "INNER JOIN b_search_content_text sct ON sct.SEARCH_CONTENT_ID = sc.ID": "")."
					INNER JOIN b_search_content_site scsite ON sc.ID=scsite.SEARCH_CONTENT_ID
					INNER JOIN b_search_tags stags ON (sc.ID = stags.SEARCH_CONTENT_ID)
				WHERE
					".CSearch::CheckPermissions("sc.ID")."
					".$strSqlWhere."
					".(is_array($this->Query->m_tags_words) && count($this->Query->m_tags_words) > 0? "AND stags.NAME in ('".implode("','", $this->Query->m_tags_words)."')": "")."
				GROUP BY
					sc.ID
					,scsite.URL
					,scsite.SITE_ID
				HAVING
					".$query."
				";
			}
			else
			{
				$strSql = "
				FROM
					".($this->Query->bText? "
						b_search_content_text sct
						INNER JOIN b_search_content sc ON sc.ID = sct.SEARCH_CONTENT_ID
						INNER JOIN b_search_content_site scsite ON sc.ID = scsite.SEARCH_CONTENT_ID
					": "
						b_search_content sc
						INNER JOIN b_search_content_site scsite ON sc.ID = scsite.SEARCH_CONTENT_ID
					")."
				WHERE
					".CSearch::CheckPermissions("sc.ID")."
					AND (".$query.")
					".$strSqlWhere."
				";
			}
		}
		elseif (!$bIncSites && $bStem)
		{
			if (BX_SEARCH_VERSION <= 1)
				$arSelect["SITE_ID"] = "sc.LID as SITE_ID";

			if (count($this->Query->m_stemmed_words) > 1)
			{
				if ($bWordPos)
					$arSelect["RANK"] = "if(STDDEV(st.PS)-".$this->normdev(count($this->Query->m_stemmed_words))." between -0.000001 and 1, 1/STDDEV(st.PS), 0) + sum(st.TF/sf.FREQ) as `RANK`";
				else
					$arSelect["RANK"] = "sum(st.TF/sf.FREQ) as `RANK`";
			}
			else
			{
				$arSelect["RANK"] = "st.TF as `RANK`";
			}

			$strSql = "
			FROM b_search_content sc
				".($this->Query->bText? "INNER JOIN b_search_content_text sct ON sct.SEARCH_CONTENT_ID = sc.ID": "")."
				INNER JOIN b_search_content_stem st ON sc.id = st.search_content_id
				".(count($this->Query->m_stemmed_words) > 1?
					"INNER JOIN b_search_content_freq sf ON
						st.language_id = sf.language_id
						and st.stem=sf.stem
						".(strlen($this->tf_hwm_site_id) > 0?
						"and sf.SITE_ID = '".$DB->ForSQL($this->tf_hwm_site_id, 2)."'":
						"and sf.SITE_ID IS NULL"
					):
					""
				)."
			WHERE
				".CSearch::CheckPermissions("sc.ID")."
				AND st.STEM in (".$strStemList.")
				".(count($this->Query->m_stemmed_words) > 1? "AND sf.STEM in (".$strStemList.")": "")."
				AND st.language_id='".$this->Query->m_lang."'
				".$strSqlWhere."
			".(count($this->Query->m_stemmed_words) > 1? "
			GROUP BY
				sc.ID
			HAVING
				(".$query.") ": "")."
			";
		}
		else //if(!$bIncSites && !$bStem)
		{
			$bDistinct = true;

			if (BX_SEARCH_VERSION <= 1)
				$arSelect["SITE_ID"] = "sc.LID as SITE_ID";
			$arSelect["RANK"] = "1 as `RANK`";

			$strSql = "
			FROM b_search_content sc
				".($this->Query->bText? "INNER JOIN b_search_content_text sct ON sct.SEARCH_CONTENT_ID = sc.ID": "")."
				".($this->Query->bTagsSearch? "INNER JOIN b_search_tags stags ON (sc.ID = stags.SEARCH_CONTENT_ID)
			WHERE
				".CSearch::CheckPermissions("sc.ID")."
				".$strSqlWhere."
				".(is_array($this->Query->m_tags_words) && count($this->Query->m_tags_words) > 0? "AND stags.NAME in ('".implode("','", $this->Query->m_tags_words)."')": "")."
			GROUP BY
				sc.ID
			HAVING
				(".$query.")":
					" WHERE
				(".$query.")
				".$strSqlWhere."
			")."
			";
		}

		if ($this->offset === false){
			$limit = $this->limit;
		}else{
			$limit = $this->offset.", ".$this->limit;
		}
		
		if($limit < 1){
			$baseLimit = COption::GetOptionInt("search", "max_result_size");
			$limit = ($baseLimit) ? $baseLimit : 500;
		}

		$strSelect = "SELECT ".($bDistinct? "DISTINCT": "")."\n".implode("\n,", $arSelect);

		$fullQuery = $strSelect."\n".$strSql.$strSort."\nLIMIT ".$limit;

		// echo '<pre>$fullQuery '; print_r($fullQuery); echo '</pre>';
		// AddMessage2Log($fullQuery, 'search.page search sql', 0);

		return $fullQuery;
	}
}
?>