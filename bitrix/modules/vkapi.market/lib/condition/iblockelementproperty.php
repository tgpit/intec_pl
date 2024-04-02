<?php

namespace VKapi\Market\Condition;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use VKapi\Market\Condition\Control\Logic;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class IblockElementProperty extends \VKapi\Market\Condition\Base
{
    const PROPERTY_TYPE_NUMBER = "\x4e";
    const PROPERTY_TYPE_STRING = "\123";
    const PROPERTY_TYPE_LIST = "\x4c";
    const PROPERTY_TYPE_FILE = "\x46";
    const PROPERTY_TYPE_GROUP = "\107";
    const PROPERTY_TYPE_ELEMENT = "\105";
    static $arPropertyList = null;
    public function __construct($arParams = [])
    {
        
        if (!isset($arParams["\x49\x42\x4c\x4f\x43\113\137\x49\x44"])) {
            $arParams["\111\x42\x4c\117\103\113\x5f\x49\x44"] = [];
        }
        $arParams["\111\x42\114\117\x43\113\137\111\104"] = (array) $arParams["\111\102\x4c\x4f\x43\113\x5f\x49\104"];
        
        if (empty($arParams["\x49\102\x4c\117\103\113\x5f\111\x44"])) {
            $fpmevw0mgn2mc3v0uj0d7z02mxehy = \VKapi\Market\Condition\IblockElementField::getIblockList();
            $arParams["\x49\102\114\x4f\103\x4b\137\111\104"] = array_keys($fpmevw0mgn2mc3v0uj0d7z02mxehy);
        }
        parent::__construct($arParams);
    }
    
    protected static function getMessage($fg0pwmggdwabvrhod3lxwj03a, $enjgw1ul2bnwsdc3dmor6i = [])
    {
        return parent::getMessage("\111\x42\x4c\x4f\103\113\x45\114\x45\115\105\x4e\x54\x50\122\117\120\x45\x52\x54\x59\56" . $fg0pwmggdwabvrhod3lxwj03a, $enjgw1ul2bnwsdc3dmor6i);
    }
    
    public static function getPropertyList()
    {
        if (is_null(self::$arPropertyList)) {
            self::$arPropertyList = [];
            if (!\Bitrix\Main\Loader::includeModule("\x69\x62\x6c\x6f\143\153")) {
                return self::$arPropertyList;
            }
            
            $c56m5q6f4valc16o3u = \CIBlockProperty::GetList(["\111\102\x4c\117\x43\113\137\x49\104" => "\x41\x53\x43", "\116\x41\x4d\105" => "\101\123\x43"]);
            while ($axvdv330buf8v5x83oqf2f53j5evz = $c56m5q6f4valc16o3u->fetch()) {
                self::$arPropertyList["\x50\x52\x4f\x50\105\x52\124\x59\137" . $axvdv330buf8v5x83oqf2f53j5evz["\111\x44"]] = ["\x49\x44" => $axvdv330buf8v5x83oqf2f53j5evz["\111\104"], "\x49\102\114\x4f\x43\x4b\137\111\x44" => $axvdv330buf8v5x83oqf2f53j5evz["\111\102\x4c\117\x43\x4b\x5f\111\x44"], "\116\x41\x4d\x45" => $axvdv330buf8v5x83oqf2f53j5evz["\x4e\x41\115\x45"], "\103\117\104\105" => $axvdv330buf8v5x83oqf2f53j5evz["\103\x4f\x44\x45"], "\x50\x52\x4f\x50\x45\122\x54\131\x5f\x54\x59\120\x45" => $axvdv330buf8v5x83oqf2f53j5evz["\120\122\x4f\120\105\122\x54\131\x5f\124\x59\x50\105"], "\x55\x53\105\x52\137\x54\131\120\x45" => $axvdv330buf8v5x83oqf2f53j5evz["\x55\x53\x45\x52\x5f\x54\131\x50\105"], "\x55\x53\105\122\x5f\124\x59\120\x45\x5f\x53\105\124\x54\111\116\107\x53" => $axvdv330buf8v5x83oqf2f53j5evz["\125\123\105\122\x5f\x54\x59\120\x45\137\123\105\x54\124\111\x4e\107\123"], "\114\x49\x4e\x4b\x5f\x49\102\114\117\x43\113\x5f\x49\x44" => $axvdv330buf8v5x83oqf2f53j5evz["\x4c\111\116\x4b\137\111\x42\114\117\103\x4b\x5f\111\104"]];
            }
        }
        return self::$arPropertyList;
    }
    
    public function getInternalConditions()
    {
        $r69z4hfh3jcej0 = [];
        $kdvtoot48jf8kbsbh0xjfnfi9n48q1 = self::getPropertyList();
        if (!empty($this->arParams["\111\x42\x4c\x4f\x43\113\x5f\x49\x44"])) {
            $xri2nzv3b1q7cjxo = \VKapi\Market\Condition\IblockElementField::getIblockList();
        }
        foreach ($kdvtoot48jf8kbsbh0xjfnfi9n48q1 as $dpxnqnwor3zsyjhtkln2ntugw2ldaz9 => $ormlitgwna6igf3o) {
            if (!in_array($ormlitgwna6igf3o["\111\102\114\117\x43\x4b\137\111\x44"], $this->arParams["\111\x42\114\x4f\103\113\x5f\111\x44"])) {
                continue;
            }
            switch ($ormlitgwna6igf3o["\120\x52\x4f\x50\x45\122\124\x59\137\x54\x59\x50\x45"]) {
                case self::PROPERTY_TYPE_LIST:
                    $lrn6ctt1uvpquaz530ds5ygtl8tuqh = new \VKapi\Market\Condition\Control\SelectPropertyEnum("\x76\141\154\165\x65", $ormlitgwna6igf3o["\111\x44"]);
                    $lrn6ctt1uvpquaz530ds5ygtl8tuqh->enableSearch();
                    $lrn6ctt1uvpquaz530ds5ygtl8tuqh->setAjaxValues("\x2f\142\x69\x74\162\151\170\x2f\164\x6f\x6f\x6c\x73\57\x76\153\141\x70\151\x2e\155\141\162\153\145\164\57\141\x6a\141\170\56\x70\150\x70\77\155\x65\x74\x68\157\144\x3d\x67\x65\164\x49\142\154\157\x63\x6b\120\x72\157\160\x65\162\164\171\105\156\x75\155\114\151\163\x74\46\151\142\x6c\157\143\153\x49\144\x3d" . $ormlitgwna6igf3o["\111\x42\x4c\x4f\x43\x4b\x5f\111\104"] . "\x26\x70\x72\157\x70\145\162\164\171\x49\144\75" . $ormlitgwna6igf3o["\111\x44"]);
                    $loui351x = [new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\x43\x4f\116\124\122\117\114\137\124\x45\130\x54", ["\43\116\x41\115\105\43" => $ormlitgwna6igf3o["\x4e\101\115\105"], "\x23\x43\117\104\105\43" => $ormlitgwna6igf3o["\103\117\x44\x45"], "\x23\x49\104\x23" => $ormlitgwna6igf3o["\x49\x44"], "\x23\x49\x42\x4c\117\103\x4b\x5f\x4e\101\115\105\43" => $xri2nzv3b1q7cjxo[$ormlitgwna6igf3o["\x49\102\114\117\x43\113\x5f\111\x44"]]])), new \VKapi\Market\Condition\Control\Logic("\143\x6f\x6e\144\151\x74\151\157\x6e", [\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL], \VKapi\Market\Condition\Control\Logic::EQUAL), $lrn6ctt1uvpquaz530ds5ygtl8tuqh];
                    break;
                case self::PROPERTY_TYPE_FILE:
                case self::PROPERTY_TYPE_NUMBER:
                    $loui351x = [new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\103\117\116\x54\x52\x4f\x4c\137\x54\x45\130\124", ["\43\116\x41\x4d\x45\x23" => $ormlitgwna6igf3o["\116\101\115\x45"], "\43\103\117\x44\105\43" => $ormlitgwna6igf3o["\103\117\104\x45"], "\43\x49\104\x23" => $ormlitgwna6igf3o["\x49\x44"], "\x23\111\x42\114\117\103\x4b\137\x4e\101\x4d\105\x23" => $xri2nzv3b1q7cjxo[$ormlitgwna6igf3o["\x49\x42\x4c\117\x43\x4b\137\111\104"]]])), new \VKapi\Market\Condition\Control\Logic("\143\x6f\x6e\x64\151\164\151\157\x6e", [\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL, \VKapi\Market\Condition\Control\Logic::MORE, \VKapi\Market\Condition\Control\Logic::MORE_EQUAL, \VKapi\Market\Condition\Control\Logic::LESS, \VKapi\Market\Condition\Control\Logic::LESS_EQUAL], \VKapi\Market\Condition\Control\Logic::EQUAL), new \VKapi\Market\Condition\Control\Input("\166\141\x6c\165\x65")];
                    break;
                case self::PROPERTY_TYPE_ELEMENT:
                    $oa6w3w0tly6j91v9kxaltsiao7kwxchy = new \VKapi\Market\Condition\Control\IblockElementFind("\x76\x61\x6c\165\x65");
                    $oa6w3w0tly6j91v9kxaltsiao7kwxchy->setIblockId($ormlitgwna6igf3o["\x4c\111\x4e\113\137\x49\102\114\117\x43\113\x5f\111\104"]);
                    $loui351x = [new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\103\x4f\116\x54\x52\117\x4c\x5f\x54\x45\130\124", ["\x23\116\x41\x4d\105\43" => $ormlitgwna6igf3o["\x4e\101\x4d\x45"], "\43\x43\117\104\105\43" => $ormlitgwna6igf3o["\x43\x4f\104\x45"], "\43\x49\104\43" => $ormlitgwna6igf3o["\x49\x44"], "\43\x49\102\x4c\117\103\113\137\116\x41\115\105\x23" => $xri2nzv3b1q7cjxo[$ormlitgwna6igf3o["\x49\x42\114\117\103\x4b\137\111\104"]]])), new \VKapi\Market\Condition\Control\Logic("\x63\x6f\156\144\151\164\151\x6f\x6e", [\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL], \VKapi\Market\Condition\Control\Logic::EQUAL), $oa6w3w0tly6j91v9kxaltsiao7kwxchy];
                    break;
                case self::PROPERTY_TYPE_GROUP:
                    $loui351x = [new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\x43\117\116\x54\x52\117\x4c\x5f\124\x45\130\x54", ["\x23\x4e\101\x4d\x45\x23" => $ormlitgwna6igf3o["\116\101\x4d\x45"], "\43\x43\x4f\104\x45\x23" => $ormlitgwna6igf3o["\x43\117\x44\x45"], "\43\x49\104\43" => $ormlitgwna6igf3o["\111\104"], "\x23\111\x42\x4c\117\x43\x4b\137\x4e\101\115\x45\43" => $xri2nzv3b1q7cjxo[$ormlitgwna6igf3o["\x49\102\114\117\x43\113\137\x49\x44"]]])), new \VKapi\Market\Condition\Control\Logic("\x63\157\156\x64\x69\x74\x69\157\156", [\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL], \VKapi\Market\Condition\Control\Logic::EQUAL), new \VKapi\Market\Condition\Control\IblockSectionFind("\x76\141\x6c\165\x65", null, $ormlitgwna6igf3o["\x4c\x49\116\113\137\111\x42\114\x4f\103\x4b\x5f\x49\x44"])];
                    break;
                case self::PROPERTY_TYPE_STRING:
                    switch ($ormlitgwna6igf3o["\125\x53\x45\122\137\124\x59\x50\105"]) {
                        
                        case "\125\163\x65\x72\111\x44":
                            $loui351x = [new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\x43\x4f\x4e\x54\x52\117\114\137\124\105\130\124", ["\43\116\x41\x4d\105\x23" => $ormlitgwna6igf3o["\x4e\x41\x4d\x45"], "\x23\x43\x4f\104\x45\43" => $ormlitgwna6igf3o["\103\117\104\105"], "\x23\111\x44\43" => $ormlitgwna6igf3o["\111\104"], "\x23\x49\102\114\117\103\x4b\137\116\101\x4d\x45\43" => $xri2nzv3b1q7cjxo[$ormlitgwna6igf3o["\x49\x42\114\x4f\103\x4b\137\x49\104"]]])), new \VKapi\Market\Condition\Control\Logic("\x63\157\x6e\144\151\164\x69\157\156", [\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL], \VKapi\Market\Condition\Control\Logic::EQUAL), new \VKapi\Market\Condition\Control\Input("\x76\x61\x6c\x75\145")];
                            break;
                        
                        case "\104\141\164\145\124\151\x6d\x65":
                        case "\x44\x61\164\x65":
                            $tmlyb3le73pyi = new \VKapi\Market\Condition\Control\Calendar("\x76\x61\154\165\x65");
                            $tmlyb3le73pyi->setShowTime($ormlitgwna6igf3o["\x55\x53\x45\122\x5f\x54\x59\120\x45"] == "\x44\141\x74\x65\x54\151\155\145");
                            $loui351x = [new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\103\117\116\124\x52\x4f\114\137\124\x45\x58\124", ["\x23\116\x41\x4d\105\43" => $ormlitgwna6igf3o["\x4e\101\x4d\x45"], "\43\x43\117\x44\x45\43" => $ormlitgwna6igf3o["\x43\117\x44\x45"], "\x23\x49\x44\43" => $ormlitgwna6igf3o["\x49\x44"], "\x23\x49\102\x4c\117\103\113\137\x4e\x41\x4d\105\x23" => $xri2nzv3b1q7cjxo[$ormlitgwna6igf3o["\x49\x42\114\117\103\x4b\x5f\111\104"]]])), new \VKapi\Market\Condition\Control\Logic("\143\x6f\x6e\144\151\x74\151\157\156", [\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL, \VKapi\Market\Condition\Control\Logic::MORE, \VKapi\Market\Condition\Control\Logic::MORE_EQUAL, \VKapi\Market\Condition\Control\Logic::LESS, \VKapi\Market\Condition\Control\Logic::LESS_EQUAL], \VKapi\Market\Condition\Control\Logic::EQUAL), $tmlyb3le73pyi];
                            break;
                        
                        case "\x45\x6c\145\x6d\145\x6e\x74\x58\155\x6c\x49\104":
                            $oa6w3w0tly6j91v9kxaltsiao7kwxchy = new \VKapi\Market\Condition\Control\IblockElementFind("\166\141\154\x75\x65");
                            $oa6w3w0tly6j91v9kxaltsiao7kwxchy->setSearchXmlId();
                            $loui351x = [new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\x43\117\x4e\124\122\117\x4c\x5f\x54\105\x58\124", ["\43\x4e\101\x4d\105\43" => $ormlitgwna6igf3o["\x4e\x41\x4d\x45"], "\x23\x43\x4f\x44\x45\43" => $ormlitgwna6igf3o["\x43\x4f\104\105"], "\x23\x49\x44\x23" => $ormlitgwna6igf3o["\x49\x44"], "\43\x49\x42\x4c\117\x43\x4b\137\116\x41\x4d\105\43" => $xri2nzv3b1q7cjxo[$ormlitgwna6igf3o["\111\x42\x4c\x4f\x43\x4b\x5f\111\104"]]])), new \VKapi\Market\Condition\Control\Logic("\143\x6f\156\144\x69\x74\x69\157\156", [\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL], \VKapi\Market\Condition\Control\Logic::EQUAL), $oa6w3w0tly6j91v9kxaltsiao7kwxchy];
                            break;
                        
                        case "\x64\151\162\145\143\x74\157\162\x79":
                            $lrn6ctt1uvpquaz530ds5ygtl8tuqh = new \VKapi\Market\Condition\Control\SelectHighloadBlock("\166\141\154\x75\145", $ormlitgwna6igf3o["\125\123\105\122\x5f\124\131\120\x45\137\x53\105\x54\124\x49\116\107\x53"]["\124\101\x42\114\105\x5f\116\x41\x4d\x45"]);
                            $lrn6ctt1uvpquaz530ds5ygtl8tuqh->enableSearch();
                            $lrn6ctt1uvpquaz530ds5ygtl8tuqh->setAjaxValues("\57\x62\x69\164\x72\x69\170\57\x74\x6f\x6f\154\x73\x2f\166\153\x61\160\151\56\x6d\x61\162\x6b\x65\164\57\x61\x6a\141\170\x2e\x70\150\160\x3f\x6d\145\x74\150\157\x64\x3d\147\145\x74\110\151\x67\x68\154\157\x61\x64\x42\154\157\x63\153\x56\141\x6c\x75\x65\114\151\163\x74\x26\x74\141\x62\x6c\145\x4e\x61\x6d\145\x3d" . $ormlitgwna6igf3o["\125\123\x45\x52\x5f\x54\x59\x50\x45\137\123\x45\x54\124\x49\x4e\107\x53"]["\124\101\102\114\x45\137\116\101\x4d\x45"]);
                            $loui351x = [new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\x43\x4f\x4e\x54\x52\x4f\x4c\137\x54\x45\130\124", ["\x23\x4e\x41\x4d\105\x23" => $ormlitgwna6igf3o["\x4e\101\115\105"], "\x23\103\117\x44\105\x23" => $ormlitgwna6igf3o["\103\x4f\x44\x45"], "\x23\x49\x44\43" => $ormlitgwna6igf3o["\111\x44"], "\x23\x49\x42\114\117\x43\113\137\116\x41\115\x45\43" => $xri2nzv3b1q7cjxo[$ormlitgwna6igf3o["\111\x42\x4c\x4f\x43\x4b\x5f\111\x44"]]])), new \VKapi\Market\Condition\Control\Logic("\x63\157\x6e\x64\151\164\x69\157\x6e", [\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL], \VKapi\Market\Condition\Control\Logic::EQUAL), $lrn6ctt1uvpquaz530ds5ygtl8tuqh];
                            break;
                        default:
                            $loui351x = [new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\x43\x4f\x4e\x54\122\x4f\114\x5f\124\x45\130\x54", ["\43\x4e\101\115\x45\x23" => $ormlitgwna6igf3o["\x4e\101\x4d\105"], "\x23\x43\117\104\105\43" => $ormlitgwna6igf3o["\103\x4f\x44\x45"], "\43\x49\x44\x23" => $ormlitgwna6igf3o["\111\104"], "\x23\x49\102\x4c\x4f\103\113\x5f\116\x41\115\105\43" => $xri2nzv3b1q7cjxo[$ormlitgwna6igf3o["\x49\x42\x4c\x4f\x43\x4b\x5f\111\x44"]]])), new \VKapi\Market\Condition\Control\Logic("\143\x6f\x6e\144\x69\164\x69\157\x6e", [\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL, \VKapi\Market\Condition\Control\Logic::HAS, \VKapi\Market\Condition\Control\Logic::NOT_HAS, \VKapi\Market\Condition\Control\Logic::START, \VKapi\Market\Condition\Control\Logic::END], \VKapi\Market\Condition\Control\Logic::EQUAL), new \VKapi\Market\Condition\Control\Input("\166\x61\154\165\145")];
                    }
                    break;
            }
            
            $r69z4hfh3jcej0[] = ["\151\x64" => $dpxnqnwor3zsyjhtkln2ntugw2ldaz9, "\156\141\x6d\145" => self::getMessage("\106\111\x45\x4c\x44\x5f\116\x41\115\x45", ["\43\116\101\x4d\105\x23" => $ormlitgwna6igf3o["\116\101\115\105"], "\43\x43\117\104\105\x23" => $ormlitgwna6igf3o["\103\117\104\105"], "\x23\x49\x44\43" => $ormlitgwna6igf3o["\111\104"], "\x23\111\102\x4c\x4f\103\113\137\116\101\x4d\x45\x23" => $xri2nzv3b1q7cjxo[$ormlitgwna6igf3o["\x49\102\114\117\103\113\137\111\104"]]]), "\x67\x72\157\165\160" => self::getMessage("\107\x52\117\x55\120\137\114\101\x42\x45\x4c", ["\43\x49\x42\114\x4f\103\113\x5f\116\x41\x4d\105\x23" => $xri2nzv3b1q7cjxo[$ormlitgwna6igf3o["\111\x42\x4c\117\103\113\x5f\x49\104"]]]), "\143\157\x6d\160\x6f\156\x65\156\x74" => "\166\153\141\160\151\x2d\x6d\x61\162\153\x65\164\x2d\x63\x6f\x6e\x64\x69\x74\x69\157\x6e\55\151\x62\154\x6f\143\153\x2d\x65\154\145\x6d\x65\156\x74\x2d\x70\x72\x6f\x70\145\x72\x74\x79", "\143\157\156\164\162\157\154\x73" => $loui351x, "\160\x61\162\x61\x6d\163" => ["\151\142\154\x6f\x63\153\111\144" => $ormlitgwna6igf3o["\x49\102\x4c\x4f\103\x4b\137\111\x44"]], "\x6d\157\162\x65" => []];
        }
        return $r69z4hfh3jcej0;
    }
    
    public static function getEval($cq09zkit47muzl7lq568u5n91kq)
    {
        $lmq174eh64d0nql = $cq09zkit47muzl7lq568u5n91kq["\151\144"];
        $xkdox1t8tv1paboewg68t = $cq09zkit47muzl7lq568u5n91kq["\x76\x61\154\165\145\163"]["\x63\x6f\x6e\x64\x69\x74\x69\x6f\156"];
        $lmyn6xzzw5b44j3ij = str_replace("\42", "\x5c\x22", $cq09zkit47muzl7lq568u5n91kq["\166\x61\154\x75\x65\163"]["\x76\141\154\165\145"]);
        $kdvtoot48jf8kbsbh0xjfnfi9n48q1 = self::getPropertyList();
        $ormlitgwna6igf3o = $kdvtoot48jf8kbsbh0xjfnfi9n48q1[$lmq174eh64d0nql];
        switch ($ormlitgwna6igf3o["\x50\122\117\x50\105\x52\124\131\x5f\124\131\x50\x45"]) {
            case self::PROPERTY_TYPE_FILE:
            case self::PROPERTY_TYPE_NUMBER:
                switch ($xkdox1t8tv1paboewg68t) {
                    case \VKapi\Market\Condition\Control\Logic::EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::NOT_EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::MORE:
                    case \VKapi\Market\Condition\Control\Logic::MORE_EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::LESS:
                    case \VKapi\Market\Condition\Control\Logic::LESS_EQUAL:
                        return \VKapi\Market\Condition\Control\Logic::getEvalRule($xkdox1t8tv1paboewg68t, $lmq174eh64d0nql, $lmyn6xzzw5b44j3ij);
                }
                break;
            case self::PROPERTY_TYPE_ELEMENT:
            case self::PROPERTY_TYPE_LIST:
            case self::PROPERTY_TYPE_GROUP:
                switch ($xkdox1t8tv1paboewg68t) {
                    case \VKapi\Market\Condition\Control\Logic::EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::NOT_EQUAL:
                        return \VKapi\Market\Condition\Control\Logic::getEvalRule($xkdox1t8tv1paboewg68t, $lmq174eh64d0nql, $lmyn6xzzw5b44j3ij);
                }
                break;
            case self::PROPERTY_TYPE_STRING:
                switch ($ormlitgwna6igf3o["\x55\x53\105\122\137\x54\131\120\105"]) {
                    
                    case "\105\154\x65\x6d\x65\x6e\164\130\x6d\154\x49\x44":
                    
                    case "\x64\x69\x72\x65\x63\x74\x6f\162\x79":
                    
                    case "\x55\163\145\162\111\104":
                        switch ($xkdox1t8tv1paboewg68t) {
                            case \VKapi\Market\Condition\Control\Logic::EQUAL:
                            case \VKapi\Market\Condition\Control\Logic::NOT_EQUAL:
                                return \VKapi\Market\Condition\Control\Logic::getEvalRule($xkdox1t8tv1paboewg68t, $lmq174eh64d0nql, $lmyn6xzzw5b44j3ij);
                        }
                        break;
                    
                    case "\x44\x61\164\145\124\x69\x6d\x65":
                    
                    case "\x44\x61\164\x65":
                        switch ($xkdox1t8tv1paboewg68t) {
                            case \VKapi\Market\Condition\Control\Logic::EQUAL:
                            case \VKapi\Market\Condition\Control\Logic::NOT_EQUAL:
                            case \VKapi\Market\Condition\Control\Logic::MORE:
                            case \VKapi\Market\Condition\Control\Logic::MORE_EQUAL:
                            case \VKapi\Market\Condition\Control\Logic::LESS:
                            case \VKapi\Market\Condition\Control\Logic::LESS_EQUAL:
                                return \VKapi\Market\Condition\Control\Logic::getEvalRule($xkdox1t8tv1paboewg68t, $lmq174eh64d0nql, $lmyn6xzzw5b44j3ij);
                        }
                        break;
                    default:
                        switch ($xkdox1t8tv1paboewg68t) {
                            case \VKapi\Market\Condition\Control\Logic::EQUAL:
                            case \VKapi\Market\Condition\Control\Logic::NOT_EQUAL:
                            case \VKapi\Market\Condition\Control\Logic::HAS:
                            case \VKapi\Market\Condition\Control\Logic::NOT_HAS:
                            case \VKapi\Market\Condition\Control\Logic::START:
                            case \VKapi\Market\Condition\Control\Logic::END:
                                return \VKapi\Market\Condition\Control\Logic::getEvalRule($xkdox1t8tv1paboewg68t, $lmq174eh64d0nql, $lmyn6xzzw5b44j3ij);
                        }
                }
                break;
        }
        return 0;
    }
    
    public function getPrepiredValuePreview($d95rycyj9ktdxm6gaqrk3z6y3a)
    {
        $xsmvvpp8o8pm3elpnq = $d95rycyj9ktdxm6gaqrk3z6y3a["\x76\x61\154\x75\145\x73"];
        $kdvtoot48jf8kbsbh0xjfnfi9n48q1 = self::getPropertyList();
        $ormlitgwna6igf3o = $kdvtoot48jf8kbsbh0xjfnfi9n48q1[$d95rycyj9ktdxm6gaqrk3z6y3a["\151\144"]];
        
        switch ($ormlitgwna6igf3o["\120\x52\x4f\120\105\122\124\x59\x5f\124\131\120\105"]) {
            case self::PROPERTY_TYPE_LIST:
                if ((int) $xsmvvpp8o8pm3elpnq["\x76\x61\154\165\x65"]) {
                    if ($kurh7en2 = \CIBlockPropertyEnum::GetByID((int) $xsmvvpp8o8pm3elpnq["\166\x61\154\x75\x65"])) {
                        $xsmvvpp8o8pm3elpnq["\x76\x61\154\x75\145\120\162\x65\x76\151\145\167"] = $kurh7en2["\126\x41\x4c\x55\x45"] . "\x20\x5b" . $kurh7en2["\111\x44"] . "\x5d";
                    }
                }
                break;
            case self::PROPERTY_TYPE_GROUP:
                if ((int) $xsmvvpp8o8pm3elpnq["\x76\141\154\165\145"]) {
                    if ($iic9ujgp = \CIBlockSection::GetByID((int) $xsmvvpp8o8pm3elpnq["\x76\141\154\165\x65"])->fetch()) {
                        $xsmvvpp8o8pm3elpnq["\166\x61\x6c\165\x65\120\x72\x65\x76\151\145\x77"] = $iic9ujgp["\116\x41\x4d\105"] . "\x20\x5b" . $iic9ujgp["\x49\x44"] . "\x5d";
                    }
                }
                break;
            case self::PROPERTY_TYPE_ELEMENT:
                if ((int) $xsmvvpp8o8pm3elpnq["\166\141\x6c\x75\145"]) {
                    if ($iic9ujgp = \CIBlockElement::GetByID((int) $xsmvvpp8o8pm3elpnq["\166\x61\154\165\145"])->fetch()) {
                        $xsmvvpp8o8pm3elpnq["\166\141\x6c\165\145\x50\x72\145\x76\151\145\x77"] = $iic9ujgp["\x4e\101\x4d\105"] . "\40\x5b" . $iic9ujgp["\x49\x44"] . "\135";
                    }
                }
                break;
            case self::PROPERTY_TYPE_STRING:
                switch ($ormlitgwna6igf3o["\x55\123\x45\122\x5f\124\x59\x50\105"]) {
                    
                    case "\x45\x6c\x65\x6d\145\156\164\x58\155\x6c\x49\x44":
                        if (trim($xsmvvpp8o8pm3elpnq["\166\x61\x6c\x75\x65"]) !== "") {
                            $yc9ht1yaxdioeih8d0lzvhe23a56 = \CIBlockElement::GetList(["\x49\104" => "\101\x53\x43"], ["\x58\x4d\x4c\x5f\x49\x44" => trim($xsmvvpp8o8pm3elpnq["\166\141\154\165\x65"])], false, ["\156\x54\x6f\x70\x43\157\x75\156\164" => 1], ["\111\104", "\x4e\x41\x4d\x45", "\130\x4d\114\137\x49\104"]);
                            if ($iic9ujgp = $yc9ht1yaxdioeih8d0lzvhe23a56->fetch()) {
                                $xsmvvpp8o8pm3elpnq["\x76\141\x6c\165\x65\120\162\145\x76\151\145\167"] = $iic9ujgp["\x4e\101\115\105"] . "\40\x5b" . $iic9ujgp["\130\x4d\x4c\137\x49\x44"] . "\x5d";
                            }
                        }
                        break;
                    case "\x64\x69\162\145\x63\164\x6f\162\x79":
                        
                        $acl826jfs6upy31nqb9mf0q2 = \VKapi\Market\Manager::getInstance()->getHighloadBlockClassByTableName($ormlitgwna6igf3o["\x55\123\x45\122\137\x54\131\x50\105\137\123\105\x54\x54\111\x4e\x47\x53"]["\124\101\x42\114\105\x5f\116\101\x4d\x45"]);
                        if (!is_null($acl826jfs6upy31nqb9mf0q2)) {
                            $cv1cjyjgljraxy632q9mtxuw72 = $acl826jfs6upy31nqb9mf0q2::getEntity();
                            $qyw2ve6bapvhf39h7 = ["\111\104" => trim($xsmvvpp8o8pm3elpnq["\x76\x61\154\x75\145"])];
                            if ($cv1cjyjgljraxy632q9mtxuw72->hasField("\125\x46\x5f\130\115\114\137\x49\104")) {
                                $qyw2ve6bapvhf39h7 = ["\125\x46\137\130\115\x4c\x5f\x49\x44" => trim($xsmvvpp8o8pm3elpnq["\x76\141\154\x75\145"])];
                            }
                            $c56m5q6f4valc16o3u = $acl826jfs6upy31nqb9mf0q2::getList(["\x6c\x69\155\x69\164" => 1, "\x66\x69\x6c\x74\x65\x72" => $qyw2ve6bapvhf39h7]);
                            if ($iic9ujgp = $c56m5q6f4valc16o3u->fetch()) {
                                $xsmvvpp8o8pm3elpnq["\x76\x61\154\x75\x65\120\162\145\166\151\145\167"] = ($iic9ujgp["\x55\106\x5f\116\x41\x4d\x45"] ?? "") . "\x20\x5b" . $iic9ujgp["\x49\x44"] . "\x5d";
                            }
                        }
                        break;
                }
                break;
        }
        return $xsmvvpp8o8pm3elpnq;
    }
}
?>