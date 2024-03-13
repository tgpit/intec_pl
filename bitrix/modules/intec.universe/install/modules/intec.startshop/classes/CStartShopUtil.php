<?
define('STARTSHOP_UTIL_ARRAY_FILTER_USE_VALUE', 0);
define('STARTSHOP_UTIL_ARRAY_FILTER_USE_KEY', 1);
define('STARTSHOP_UTIL_ARRAY_FILTER_USE_BOTH', 2);

define('STARTSHOP_UTIL_ARRAY_PREFIX_USE_KEY', 0);
define('STARTSHOP_UTIL_ARRAY_PREFIX_USE_VALUE', 1);
define('STARTSHOP_UTIL_ARRAY_PREFIX_USE_BOTH', 2);

class CStartShopUtil
{
    /**
     * @param string $sContent
     * @param array $arReplacing
     * @return mixed
     */
    public static function ReplaceMacros($sContent, $arReplacing)
    {
        $sReturn = $sContent;

        if (is_array($arReplacing) && !empty($arReplacing))
        {
            foreach ($arReplacing as $arReplacingMacrosKey => $arReplacingMacros)
            {
                if (is_array($arReplacingMacros))
                    continue;

                $sReturn = str_replace('#'.$arReplacingMacrosKey.'#', $arReplacingMacros, $sReturn);
            }
        }

        return $sReturn;
    }

    /**
     * @param string $sFilePath
     * @param array $arReplacing
     */
    public static function ReplaceMacrosInFile($sFilePath, $arReplacing)
    {
        if (is_file($sFilePath))
        {
            $sReplaceable = file_get_contents($sFilePath);
            $sReplaceable = static::ReplaceMacros($sReplaceable, $arReplacing);
            file_put_contents($sFilePath, $sReplaceable);
        }
    }

    public static function ReplaceMacrosInDir($sReplaceableDir, $arReplacing)
    {
        if (is_dir($sReplaceableDir))
        {
            $arEntries = scandir($sReplaceableDir);
            array_shift($arEntries);
            array_shift($arEntries);

            foreach ($arEntries as $sEntry)
            {
                $sFullEntry = preg_replace('/\/{2,}/', '/', $sReplaceableDir.'/'.$sEntry);

                if (is_dir($sFullEntry))
                {
                    self::ReplaceMacrosInDir($sFullEntry, $arReplacing);
                }
                else
                {
                    self::ReplaceMacrosInFile($sFullEntry, $arReplacing);
                }
            }
        }
    }

    public static function DBResultToArray(&$dbResult, $sKey = false, $bAlternative = false)
    {
        $arResults = array();

        if ($dbResult instanceof CDBResult)
            if (!$bAlternative) {
                while ($arResult = $dbResult->Fetch())
                    if ($sKey !== false) {
                        if (array_key_exists($sKey, $arResult) && !empty($arResult[$sKey]))
                            $arResults[$arResult[$sKey]] = $arResult;
                    } else {
                        $arResults[] = $arResult;
                    }
            } else {
                while ($arResult = $dbResult->GetNext())
                    if ($sKey !== false) {
                        if (array_key_exists($sKey, $arResult) && !empty($arResult[$sKey]))
                            $arResults[$arResult[$sKey]] = $arResult;
                    } else {
                        $arResults[] = $arResult;
                    }
            }


        return $arResults;
    }

    public static function ArrayToDBResult($arArray) {
        if (!is_array($arArray))
            $arArray = array();

        $arArray = array_values($arArray);

        $dbResult = new CDBResult();
        $dbResult->InitFromArray($arArray);
        return $dbResult;
    }

    public static function ArrayFilter($arArray, $fCallback, $iFlag = 0)
    {
        $arFiltered = array();

        if (!($fCallback instanceof Closure) || !is_array($arArray))
            return $arFiltered;

        foreach ($arArray as $sKey => $cValue) {
            $bAdd = false;

            if ($iFlag == STARTSHOP_UTIL_ARRAY_FILTER_USE_VALUE)
                $bAdd = (bool)$fCallback($cValue);

            if ($iFlag == STARTSHOP_UTIL_ARRAY_FILTER_USE_KEY)
                $bAdd = (bool)$fCallback($sKey);

            if ($iFlag == STARTSHOP_UTIL_ARRAY_FILTER_USE_BOTH)
                $bAdd = (bool)$fCallback($sKey, $cValue);

            if ($bAdd)
                $arFiltered[$sKey] = $cValue;
        }

        return $arFiltered;
    }

    public static function ArrayPrefix($arArray, $sStartPrefix = null, $sEndPrefix = null, $iFlag = 0)
    {
        $arPrefixed = array();

        if (!is_array($arArray))
            return $arPrefixed;

        $sStartPrefix = strval($sStartPrefix);
        $sEndPrefix = strval($sEndPrefix);

        if (empty($sStartPrefix) && empty($sEndPrefix))
            return $arArray;

        if ($iFlag == STARTSHOP_UTIL_ARRAY_PREFIX_USE_KEY)
            foreach ($arArray as $sKey => $cValue)
                $arPrefixed[$sStartPrefix.$sKey.$sEndPrefix] = $cValue;

        if ($iFlag == STARTSHOP_UTIL_ARRAY_PREFIX_USE_VALUE)
            foreach ($arArray as $sKey => $cValue)
                $arPrefixed[$sKey] = $sStartPrefix.$cValue.$sEndPrefix;

        if ($iFlag == STARTSHOP_UTIL_ARRAY_PREFIX_USE_BOTH)
            foreach ($arArray as $sKey => $cValue)
                $arPrefixed[$sStartPrefix.$sKey.$sEndPrefix] = $sStartPrefix.$cValue.$sEndPrefix;

        return $arPrefixed;
    }

    public static function UrlDisassemble ($sUrl) {
        $arDisassembledUrl = parse_url($sUrl);
        $arDisassembledQuery = array();

        if (!empty($arDisassembledUrl['fragment'])) {
            $arDisassembledUrl['query'] .= '#'.$arDisassembledUrl['fragment'];
            unset($arDisassembledUrl['fragment']);
        }

        if (!empty($arDisassembledUrl['query']))
            parse_str($arDisassembledUrl['query'], $arDisassembledQuery);

        $arDisassembledUrl['query'] = $arDisassembledQuery;

        unset ($arDisassembledQuery);
        return $arDisassembledUrl;
    }

    public static function UrlAssemble ($arUrl) {
        $sAssembledUrl = $arUrl['path'];
        $sAssembledQuery = array();

        if (!empty($arUrl['query']))
            foreach ($arUrl['query'] as $sKey => $sValue)
                if (!empty($sKey)) {
                    $sAssembledQuery[] = urlencode($sKey).'='.urlencode($sValue);
                }

        $sAssembledQuery = implode('&', $sAssembledQuery);

        if (!empty($sAssembledQuery))
            $sAssembledUrl .= '?'.$sAssembledQuery;

        return $sAssembledUrl;
    }

    public static function UrlParametersSet ($sUrl, $arParameters = array()) {
        if (!is_array($arParameters)) return false;

        $arUrl = static::UrlDisassemble($sUrl);

        foreach ($arParameters as $sKey => $sValue)
            $arUrl['query'][$sKey] = $sValue;

        $sUrl = static::UrlAssemble($arUrl);

        return $sUrl;
    }

    public static function UrlParametersRemove ($sUrl, $arParameters = array()) {
        if (!is_array($arParameters)) return false;

        $arUrl = static::UrlDisassemble($sUrl);

        foreach ($arParameters as $sParameter)
            unset($arUrl['query'][strval($sParameter)]);

        $sUrl = static::UrlAssemble($arUrl);

        return $sUrl;
    }

    /**
     * @param $sContent
     * @return bool|string
     */
    public static function ConvertToSiteCharset ($sContent) {
        global $APPLICATION;
        return $APPLICATION->ConvertCharset($sContent, "UTF-8", SITE_CHARSET);
    }

    /**
     * @param $sContent
     * @return bool|string
     */
    public static function ConvertFromSiteCharset ($sContent) {
        global $APPLICATION;
        return $APPLICATION->ConvertCharset($sContent, SITE_CHARSET, "UTF-8");
    }
}
?>