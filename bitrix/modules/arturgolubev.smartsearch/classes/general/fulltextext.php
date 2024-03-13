<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/search/classes/general/full_text.php");

class CSearchFullTextExt extends CSearchFullText
{
    public static function getInstance()
	{
		if (!isset(static::$instance))
		{
			$full_text_engine = COption::GetOptionString("search", "full_text_engine");
			if ($full_text_engine === "sphinx")
			{
				self::$instance = new CSearchSphinxExt;
				self::$instance->connect(
					COption::GetOptionString("search", "sphinx_connection"),
					COption::GetOptionString("search", "sphinx_index_name")
				);
			}
			elseif ($full_text_engine === "mysql")
			{
				self::$instance = new CSearchMysql;
				self::$instance->connect();
			}
			else
			{
				self::$instance = new CSearchStemTable();
			}
		}
		return static::$instance;
	}
}