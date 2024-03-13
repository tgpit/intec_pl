<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/search/classes/general/title.php");

use \Arturgolubev\Smartsearch\Unitools as UTools;

class CSearchTitleExt extends CAllSearchTitle
{
	function searchTitle($phrase = "", $nTopCount = 5, $arParams = array(), $bNotFilter = false, $order = "")
	{
		$extended_mode = (UTools::getSetting("mode_stitle") != 'standart');
	
		$DB = CDatabase::GetModuleConnection('search');
		
		$arWords = array();
		
		$sqlHaving = array();
		$sqlWords = array();
		if (!empty($this->_arPhrase))
		{
			$last = true;
			foreach (array_reverse($this->_arPhrase, true) as $word => $pos)
			{
				$arWords[] = $word;
				
				if ($last && !preg_match("/[\\n\\r \\t]$/", $phrase))
				{
					$last = false;
					if (strlen($word) >= $this->minLength)
					{
						if($extended_mode)
							$s = $sqlWords[] = "ct.WORD like '%".$DB->ForSQL($word)."%'";
						else
							$s = $sqlWords[] = "ct.WORD like '".$DB->ForSQL($word)."%'";
					}
					else
						$s = "";
				}
				else
				{
					if($extended_mode)
						$s = $sqlWords[] = "ct.WORD like '%".$DB->ForSQL($word)."%'";
					else
						$s = $sqlWords[] = "ct.WORD = '".$DB->ForSQL($word)."'";
				}

				if ($s)
					$sqlHaving[] = "(sum(".$s.") > 0)";
			}
		}

		if (!empty($sqlWords)){
			$bIncSites = false;
			$strSqlWhere = CSearch::__PrepareFilter($arParams, $bIncSites);
			if ($bNotFilter)
			{
				if (!empty($strSqlWhere))
					$strSqlWhere = "NOT (".$strSqlWhere.")";
				else
					$strSqlWhere = "1=0";
			}

			/*
			$bOrderByRank = ($order == "rank");
			
			$strSql = "
				SELECT
					sc.ID
					,sc.MODULE_ID
					,sc.ITEM_ID
					,sc.TITLE
					,sc.PARAM1
					,sc.PARAM2
					,sc.DATE_CHANGE
					,sc.URL as URL
					,sc.CUSTOM_RANK as CUSTOM_RANK
					,scsite.URL as SITE_URL
					,scsite.SITE_ID
					,if(locate('".$DB->ForSQL(ToUpper($phrase))."', upper(sc.TITLE)) > 0, 1, 0) TITLE_RANK
					,locate('".$DB->ForSQL(ToUpper($phrase))."', upper(sc.TITLE)) RANK2
					,min(ct.POS) RANK3
				FROM
					b_search_content_title ct
					inner join b_search_content sc on sc.ID = ct.SEARCH_CONTENT_ID
					INNER JOIN b_search_content_site scsite ON sc.ID = scsite.SEARCH_CONTENT_ID and ct.SITE_ID = scsite.SITE_ID
				WHERE
					".CSearch::CheckPermissions("sc.ID")."
					AND ct.SITE_ID = '".SITE_ID."'
					AND (".implode(" OR ", $sqlWords).")
					".(!empty($strSqlWhere)? "AND ".$strSqlWhere: "")."
				GROUP BY
					ID, MODULE_ID, ITEM_ID, TITLE, PARAM1, PARAM2, DATE_CHANGE, URL, SITE_URL, SITE_ID
				".(count($sqlHaving) > 1? "HAVING ".implode(" AND ", $sqlHaving): "")."
				ORDER BY ".(
				$bOrderByRank?
					"TITLE_RANK DESC, CUSTOM_RANK DESC, RANK2 ASC, RANK3 ASC, TITLE":
					"DATE_CHANGE DESC, TITLE_RANK DESC, RANK2 ASC, RANK3 ASC, TITLE"
				)."
				LIMIT 0, ".($nTopCount + 1)."
			"; */
			
			$strSelect = "
			sc.ID
			,sc.MODULE_ID
			,sc.ITEM_ID
			,sc.TITLE
			,sc.PARAM1
			,sc.PARAM2
			,sc.DATE_CHANGE
			,sc.URL as URL
			,scsite.URL as SITE_URL
			,scsite.SITE_ID
			,sc.CUSTOM_RANK as CUSTOM_RANK
			";
			
			if(!empty($arWords)){
				$strSelectRank = '';

				foreach ($arWords as $word){
					$word = $DB->ForSql(ToLower($word));

					if (strlen($strSelectRank) > 0)
						$strSelectRank .= " + ";

					// $strSelectRank .= "if(locate('".$word."', sc.TITLE) > 0, locate('".$word."', sc.TITLE), 250)";

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

				$strSelect .= ', '.$strSelectRank." as TITLE_RANK";
			}
			
			
			$strSql = "
				SELECT
					".$strSelect."
				FROM
					b_search_content_title ct
					inner join b_search_content sc on sc.ID = ct.SEARCH_CONTENT_ID
					INNER JOIN b_search_content_site scsite ON sc.ID = scsite.SEARCH_CONTENT_ID and ct.SITE_ID = scsite.SITE_ID
				WHERE
					".CSearch::CheckPermissions("sc.ID")."
					AND ct.SITE_ID = '".SITE_ID."'
					AND (".implode(" OR ", $sqlWords).")
					".(!empty($strSqlWhere)? "AND ".$strSqlWhere: "")."
				GROUP BY
					ID, MODULE_ID, ITEM_ID, TITLE, PARAM1, PARAM2, DATE_CHANGE, URL, SITE_URL, SITE_ID
				".(count($sqlHaving) > 1? "HAVING ".implode(" AND ", $sqlHaving): "")."
				ORDER BY ".self::getSqlOrderExt($order)."
				LIMIT 0, ".($nTopCount + 1)."
			";
			
			// AddMessage2Log($strSql, 'search.title strSql', 0);
			// AddMessage2Log($arWords, 'search.title arWords', 0);

			$r = $DB->Query($strSql);
			// parent::CDBResult($r);
			CDBResult::__construct($r);

			return true;
		}else{
			return false;
		}
	}

	function Fetch()
	{
		$r = parent::Fetch();

		if($r){
			foreach(array_keys($this->_arPhrase) as $v){
				$v = (defined("BX_UTF")) ? mb_strtolower($v) : strtolower($v);
				$r["NAME"] = (defined("BX_UTF")) ? mb_strtolower($r["NAME"]) : strtolower($r["NAME"]);
				
				$parts = preg_split('/(<\/?b>)/', $r["NAME"], null, PREG_SPLIT_DELIM_CAPTURE);
				$r["NAME"] = '';
				foreach($parts as $i => $part){
					if($i % 4 == 2){
						$r["NAME"] .= $part;
					}else{
						$r["NAME"] .= str_replace($v, '<b>'.$v.'</b>', $part);
					}
				}
			}
		}

		return $r;
	}
	
	function getRankFunction($phrase)
	{
		$DB = CDatabase::GetModuleConnection('search');
		return "if(locate('".$DB->ForSQL(ToUpper($phrase))."', upper(sc.TITLE)) > 0, 1, 0)";
	}

	function getSqlOrderExt($order)
	{
		if ($order == 'rank')
			return "CUSTOM_RANK DESC, TITLE_RANK ASC, TITLE";
		else
			return "CUSTOM_RANK DESC, DATE_CHANGE DESC, TITLE_RANK ASC, TITLE";
	}
	
	function getSqlOrder($bOrderByRank){
		if ($bOrderByRank)
			return "CUSTOM_RANK DESC, RANK1 DESC, TITLE";
		else
			return "CUSTOM_RANK DESC, DATE_CHANGE DESC, RANK1 DESC, TITLE";
	}
}
