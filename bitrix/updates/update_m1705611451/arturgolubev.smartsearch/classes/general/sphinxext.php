<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/search/tools/sphinx.php");

class CSearchSphinxExt extends CSearchSphinx
{

    function getRowFormatter(){
		return new CSearchSphinxFormatterExt($this);
	}
    
    function __PrepareSort($aSort = array()){
        // echo '<pre>'; print_r($aSort); echo '</pre>';

		$arOrder = array();
		if(!is_array($aSort))
			$aSort = array($aSort => "ASC");

		$this->flagsUseRatingSort = 0;
		foreach($aSort as $key => $ord)
		{
			$ord = mb_strtoupper($ord) <> "ASC"? "DESC": "ASC";
			$key = mb_strtolower($key);
            
			switch($key)
			{
				case "date_change":
				case "custom_rank":
				case "id":
				case "param1":
				case "param2":
				case "date_from":
				case "date_to":
					$arOrder[] = $key." ".$ord;
					break;
				case "item_id":
					$arOrder[] = "item ".$ord;
					break;
				case "module_id":
					$arOrder[] = "module ".$ord;
					break;
				case "rank":
					$arOrder[] = "rank ".$ord;
					break;

				case "title_rank":
					$arOrder[] = "rank DESC"; //.$ord
					break;
			}
		}

		if(count($arOrder) == 0)
		{
			$arOrder[] = "custom_rank DESC";
			$arOrder[] = "rank DESC";
		}

		return " ORDER BY ".implode(", ",$arOrder);
	}

    public function search1($arParams, $aSort, $aParamsEx, $bTagsCloud) // no use
	{
		$result = array();
		$this->errorText = "";
		$this->errorNumber = 0;

		$this->tags = trim($arParams["TAGS"]);

		$limit = 0;
		if (is_array($aParamsEx) && isset($aParamsEx["LIMIT"]))
		{
			$limit = intval($aParamsEx["LIMIT"]);
			unset($aParamsEx["LIMIT"]);
		}

		$offset = 0;
		if (is_array($aParamsEx) && isset($aParamsEx["OFFSET"]))
		{
			$offset = intval($aParamsEx["OFFSET"]);
			unset($aParamsEx["OFFSET"]);
		}

		if (is_array($aParamsEx) && !empty($aParamsEx))
		{
			$aParamsEx["LOGIC"] = "OR";
			$arParams[] = $aParamsEx;
		}

		$this->SITE_ID = $arParams["SITE_ID"];

		$arWhere = array();
		$cond1 = implode("\n\t\t\t\t\t\tand ", $this->prepareFilter($arParams, true));

		$rights = $this->CheckPermissions();
		if ($rights)
			$arWhere[] = "right in (".$rights.")";

		$strQuery = trim($arParams["QUERY"]);
		if ($strQuery != "")
		{
			$arWhere[] = "MATCH('".$this->recodeTo($this->Escape($strQuery))."')";
			$this->query = $strQuery;
		}

		if ($cond1 != "")
			$arWhere[] = "cond1 = 1";

		if ($strQuery || $this->tags || $bTagsCloud)
		{
			if ($limit <= 0)
			{
				$limit = intval(COption::GetOptionInt("search", "max_result_size"));
			}

			if ($limit <= 0)
			{
				$limit = 500;
			}

			$ts = time()-CTimeZone::GetOffset();
			if ($bTagsCloud)
			{
				$sql = "
					select groupby() tag_id
					,count(*) cnt
					,max(date_change) dc_tmp
					,if(date_to, date_to, ".$ts.") date_to_nvl
					,if(date_from, date_from, ".$ts.") date_from_nvl
					".($cond1 != ""? ",$cond1 as cond1": "")."
					from ".$this->indexName."
					where ".implode("\nand\t", $arWhere)."
					group by tags
					order by cnt desc
					limit 0, ".$limit."
					option max_matches = ".$limit."
				";

				$DB = CDatabase::GetModuleConnection('search');
				$startTime = microtime(true);

				$r =  $this->query($sql);

				if($DB->ShowSqlStat)
					$DB->addDebugQuery($sql, microtime(true)-$startTime);

				if (!$r)
				{
					throw new \Bitrix\Main\Db\SqlQueryException('Sphinx select error', $this->getError(), $sql);
				}
				else
				{
					while($res = $this->fetch($r))
						$result[] = $res;
				}
			}
			else
			{
				$sql = "
					select id
					,item
					,param1
					,param2
					,module_id
					,param2_id
					,date_change
					,custom_rank
					,weight() as rank
					,1 as TITLE_RANK
					".($cond1 != ""? ",$cond1 as cond1": "")."
					,if(date_to, date_to, ".$ts.") date_to_nvl
					,if(date_from, date_from, ".$ts.") date_from_nvl
					from ".$this->indexName."
					where ".implode("\nand\t", $arWhere)."
					".$this->__PrepareSort($aSort)."
					limit ".$offset.", ".$limit."
					option max_matches = ".($offset + $limit)."
				";

				$DB = CDatabase::GetModuleConnection('search');
				$startTime = microtime(true);

				echo '<pre>'; print_r($sql); echo '</pre>';

				$r =  $this->query($sql);

				if($DB->ShowSqlStat)
					$DB->addDebugQuery($sql, microtime(true)-$startTime);

				if (!$r)
				{
					throw new \Bitrix\Main\Db\SqlQueryException('Sphinx select error', $this->getError(), $sql);
				}
				else
				{
					$forum = sprintf("%u", crc32("forum"));
					while($res = $this->fetch($r))
					{
						if($res["module_id"] == $forum)
						{
							if (array_key_exists($res["param2_id"], $this->arForumTopics))
								continue;
							$this->arForumTopics[$res["param2_id"]] = true;
						}
						$result[] = $res;
					}
				}
			}
		}
		else
		{
			$this->errorText = GetMessage("SEARCH_ERROR3");
			$this->errorNumber = 3;
		}

        echo '<pre>'; print_r($result); echo '</pre>';

		return $result;
	}
}

class CSearchSphinxFormatterExt extends CSearchSphinxFormatter
{
	private $sphinx = null;
	function __construct($sphinx){
		$this->sphinx = $sphinx;
	}

    function formatRow($r){
		$strSelectRank = '';

		$rankWord = [];

		foreach(explode(' ', $this->sphinx->query) as $word){
			$rankWord[] = ToLower(str_replace('*', '', $word));
		}

		foreach ($rankWord as $word){
			if ($strSelectRank)
				$strSelectRank .= " + ";

			/* $strSelectRank .= "if(
				(locate(' ".$word."', sc.TITLE) > 0 OR locate('".$word."', sc.TITLE) = 1),
				(if(locate('".$word."', sc.TITLE) = 1, 1, locate(' ".$word."', sc.TITLE))),
				(if(
					locate('".$word."', sc.TITLE) > 0,
					(locate('".$word."', sc.TITLE) + 1000),
					10000
				))
			)"; */

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

		// echo '<pre>'; print_r($rankWord); echo '</pre>';
		// echo '<pre>'; print_r($strSelectRank); echo '</pre>';

		$DB = CDatabase::GetModuleConnection('search');
		if ($this->sphinx->SITE_ID)
		{
			$sql = "
				select
					sc.ID
					,sc.MODULE_ID
					,sc.ITEM_ID
					,sc.TITLE
					,sc.TAGS
					,sc.BODY
					,sc.PARAM1
					,sc.PARAM2
					,sc.UPD
					,sc.DATE_FROM
					,sc.DATE_TO
					,sc.URL
					,sc.CUSTOM_RANK
					,".(($strSelectRank) ? $strSelectRank : 1)." as TITLE_RANK
					,".$DB->DateToCharFunction("sc.DATE_CHANGE")." as FULL_DATE_CHANGE
					,".$DB->DateToCharFunction("sc.DATE_CHANGE", "SHORT")." as DATE_CHANGE
					,scsite.SITE_ID
					,scsite.URL SITE_URL
					".(BX_SEARCH_VERSION > 1? ",sc.USER_ID": "")."
				from b_search_content sc
				INNER JOIN b_search_content_site scsite ON sc.ID=scsite.SEARCH_CONTENT_ID
				where ID = ".$r["id"]."
				and scsite.SITE_ID = '".$DB->ForSql($this->sphinx->SITE_ID)."'
			";
		}
		else
		{
			$sql = "
				select
					sc.ID
					,sc.MODULE_ID
					,sc.ITEM_ID
					,sc.TITLE
					,sc.TAGS
					,sc.BODY
					,sc.PARAM1
					,sc.PARAM2
					,sc.UPD
					,sc.DATE_FROM
					,sc.DATE_TO
					,sc.URL
					,sc.CUSTOM_RANK
					,".(($strSelectRank) ? $strSelectRank : 1)." as TITLE_RANK
					,".$DB->DateToCharFunction("sc.DATE_CHANGE")." as FULL_DATE_CHANGE
					,".$DB->DateToCharFunction("sc.DATE_CHANGE", "SHORT")." as DATE_CHANGE
					".(BX_SEARCH_VERSION < 1? ",sc.LID as SITE_ID": "")."
				from b_search_content sc
				where ID = ".$r["id"]."
			";

		}

		$rs = $DB->Query($sql);

		// echo '<pre>'; print_r($sql); echo '</pre>';

		$r = $rs->Fetch();
		if ($r){
			$r["TITLE_FORMATED"] = $this->buildExcerpts(htmlspecialcharsex($r["TITLE"]));
			$r["TITLE_FORMATED_TYPE"] = "html";
			$r["TAGS_FORMATED"] = tags_prepare($r["TAGS"], SITE_ID);
			$r["BODY_FORMATED"] = $this->buildExcerpts(htmlspecialcharsex($r["BODY"]));
			$r["BODY_FORMATED_TYPE"] = "html";
		}
		return $r;
	}

	public function buildExcerpts($str)
	{
		$sql = "CALL SNIPPETS(
			'".$this->sphinx->Escape2($this->sphinx->recodeTo($str))."'
			,'".$this->sphinx->Escape($this->sphinx->indexName)."'
			,'".$this->sphinx->Escape($this->sphinx->recodeTo($this->sphinx->query." ".$this->sphinx->tags))."'
			,500 as limit
			,1 as query_mode
		)";
		$result = $this->sphinx->query($sql);

		if ($result)
		{
			$res = $this->sphinx->fetch($result);
			if ($res)
			{
				return $this->sphinx->recodeFrom($res["snippet"]);
			}
			else
			{
				return "";
			}
		}
		else
		{
			throw new \Bitrix\Main\Db\SqlQueryException('Sphinx select error', $this->sphinx->getError(), $sql);
		}
	}
}