<?php

namespace VKapi\Market\Condition;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use VKapi\Market\Condition\Control\Logic;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class IblockElementField extends \VKapi\Market\Condition\Base
{
    private $arExistsFields = array("\111\102\x4c\117\x43\x4b\137\x53\x45\x43\124\111\x4f\116\x5f\111\104", "\103\117\104\105", "\130\115\x4c\x5f\x49\104", "\x4e\x41\x4d\105", "\x41\103\124\x49\x56\105", "\101\103\124\111\x56\x45\137\104\101\x54\x45", "\104\101\124\105\x5f\101\103\x54\x49\x56\x45\x5f\x46\x52\117\x4d", "\x44\x41\x54\x45\137\x41\x43\124\x49\x56\x45\137\124\x4f", "\123\117\x52\x54", "\120\122\x45\126\x49\x45\127\137\x54\105\130\x54", "\x44\x45\x54\101\111\114\137\124\x45\x58\124", "\104\x41\x54\105\x5f\x43\122\x45\101\x54\x45", "\103\x52\105\x41\124\105\104\137\102\x59", "\x54\x49\115\x45\123\x54\x41\x4d\120\137\130", "\x4d\117\x44\111\106\x49\x45\x44\x5f\102\x59", "\124\x41\x47\123");
    private $groupLabel = "";
    public function __construct($arParams = array())
    {
        
        $this->groupLabel = self::getMessage("\107\122\117\125\120\137\114\x41\102\105\x4c");
        if (isset($arParams["\x4c\101\102\105\x4c"])) {
            $this->groupLabel = $arParams["\114\x41\x42\x45\x4c"];
        }
        
        if (!isset($arParams["\x49\102\x4c\117\103\113\x5f\111\x44"])) {
            $arParams["\111\x42\114\117\103\113\137\x49\104"] = array();
        }
        $arParams["\111\102\x4c\x4f\x43\x4b\137\111\x44"] = (array) $arParams["\111\x42\114\x4f\x43\x4b\x5f\111\104"];
        
        if (empty($arParams["\x49\102\x4c\117\103\x4b\137\x49\104"])) {
            $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df = \VKapi\Market\Condition\IblockElementField::getIblockList();
            $arParams["\111\x42\x4c\x4f\103\113\x5f\x49\x44"] = array_keys($h3jh4oc43rrs3lu158mjh0vuozyfrwv81df);
        }
        parent::__construct($arParams);
    }
    
    protected static function getMessage($v0s6p9xp365, $et3pij9za16ska2d0tetvoihtov5fe7nd = array())
    {
        return parent::getMessage("\x49\102\x4c\117\x43\113\105\x4c\x45\x4d\x45\116\124\x46\x49\x45\114\x44\x53\56" . $v0s6p9xp365, $et3pij9za16ska2d0tetvoihtov5fe7nd);
    }
    
    protected static function isInstalledCatalogModule()
    {
        static $vg99ejd118y72lkhkqky433;
        if (!isset($vg99ejd118y72lkhkqky433)) {
            $vg99ejd118y72lkhkqky433 = \Bitrix\Main\Loader::includeModule("\x63\141\x74\141\154\x6f\x67");
        }
        return $vg99ejd118y72lkhkqky433;
    }
    
    public static function getIblockList()
    {
        static $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df;
        if (!isset($h3jh4oc43rrs3lu158mjh0vuozyfrwv81df)) {
            \Bitrix\Main\Loader::includeModule("\151\x62\154\x6f\143\153");
            $mekwu180dwog63b0zsdywrxysy1 = \CIBlock::GetList(array("\116\101\115\105" => "\101\123\x43"));
            while ($xspudkepa = $mekwu180dwog63b0zsdywrxysy1->fetch()) {
                $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df[$xspudkepa["\x49\x44"]] = $xspudkepa["\116\101\x4d\105"] . "\40\x5b" . $xspudkepa["\x49\104"] . "\135";
            }
        }
        return $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df;
    }
    
    public function getInternalConditions()
    {
        $zslbvdz8c5xio4kaftc = array();
        if (!empty($this->arParams["\x49\x42\x4c\117\x43\x4b\x5f\x49\x44"])) {
            $ipj4t7dk = \VKapi\Market\Condition\IblockElementField::getIblockList();
        }
        foreach ($this->arParams["\111\x42\x4c\117\103\x4b\137\111\x44"] as $aoc34nzah7vc31n0eyg52tyt35gk342ed) {
            foreach ($this->arExistsFields as $g7ortqhx) {
                if (in_array($g7ortqhx, array("\103\122\105\101\124\x45\x44\137\x42\131", "\115\117\104\x49\106\111\x45\x44\x5f\x42\131"))) {
                    $ydknml44cpq0rt = array(new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\103\117\116\124\122\x4f\x4c\137\124\x45\x58\x54", array("\x23\116\x41\x4d\x45\43" => self::getMessage($g7ortqhx), "\x23\111\102\x4c\117\x43\x4b\x5f\116\x41\115\x45\43" => $ipj4t7dk[$aoc34nzah7vc31n0eyg52tyt35gk342ed]))), new \VKapi\Market\Condition\Control\Logic("\143\x6f\x6e\144\x69\164\151\157\156", array(\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL), \VKapi\Market\Condition\Control\Logic::EQUAL), new \VKapi\Market\Condition\Control\Input("\x76\x61\154\x75\145"));
                } elseif (in_array($g7ortqhx, array("\x41\x43\124\111\126\x45\137\104\101\x54\105", "\x41\103\x54\x49\x56\105"))) {
                    $ydknml44cpq0rt = array(new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\x43\117\116\124\122\117\114\x5f\x54\105\x58\x54", array("\43\116\101\x4d\x45\43" => self::getMessage($g7ortqhx), "\43\x49\102\x4c\117\103\x4b\x5f\x4e\x41\x4d\x45\43" => $ipj4t7dk[$aoc34nzah7vc31n0eyg52tyt35gk342ed]))), new \VKapi\Market\Condition\Control\Logic("\143\x6f\156\144\151\x74\x69\x6f\x6e", array(\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL), \VKapi\Market\Condition\Control\Logic::EQUAL), new \VKapi\Market\Condition\Control\Select("\x76\x61\x6c\x75\x65", array("\131" => self::getMessage("\x59\105\123"), "\116" => self::getMessage("\x4e\x4f"))));
                } elseif ($g7ortqhx == "\x49\x42\x4c\x4f\103\x4b\x5f\x53\105\x43\124\x49\117\x4e\x5f\x49\x44") {
                    $ydknml44cpq0rt = array(new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\103\117\x4e\x54\x52\117\114\137\x54\x45\130\124", array("\x23\x4e\x41\115\105\x23" => self::getMessage($g7ortqhx), "\43\x49\102\x4c\x4f\103\113\137\116\x41\x4d\x45\x23" => $ipj4t7dk[$aoc34nzah7vc31n0eyg52tyt35gk342ed]))), new \VKapi\Market\Condition\Control\Logic("\x63\157\x6e\144\151\x74\x69\157\x6e", array(\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL), \VKapi\Market\Condition\Control\Logic::EQUAL), new \VKapi\Market\Condition\Control\IblockSectionFind("\x76\x61\154\165\x65"));
                } elseif (in_array($g7ortqhx, array("\104\101\x54\x45\x5f\x43\122\105\x41\x54\105", "\x54\x49\x4d\x45\x53\x54\101\x4d\120\137\130", "\x44\x41\124\105\137\x41\x43\x54\111\x56\105\x5f\106\x52\117\x4d", "\104\x41\124\x45\137\x41\x43\x54\111\126\x45\137\x54\x4f"))) {
                    $ydknml44cpq0rt = array(new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\x43\117\x4e\124\122\117\x4c\x5f\124\105\x58\x54", array("\x23\116\x41\115\105\x23" => self::getMessage($g7ortqhx), "\x23\111\102\x4c\117\x43\x4b\137\x4e\x41\x4d\x45\x23" => $ipj4t7dk[$aoc34nzah7vc31n0eyg52tyt35gk342ed]))), new \VKapi\Market\Condition\Control\Logic("\143\x6f\156\x64\x69\164\x69\157\156", array(\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL, \VKapi\Market\Condition\Control\Logic::MORE, \VKapi\Market\Condition\Control\Logic::MORE_EQUAL, \VKapi\Market\Condition\Control\Logic::LESS, \VKapi\Market\Condition\Control\Logic::LESS_EQUAL), \VKapi\Market\Condition\Control\Logic::EQUAL), new \VKapi\Market\Condition\Control\Calendar("\166\x61\154\165\x65"));
                } else {
                    $ydknml44cpq0rt = array(new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\x43\x4f\116\x54\x52\x4f\x4c\x5f\124\x45\x58\x54", array("\x23\x4e\101\115\105\43" => self::getMessage($g7ortqhx), "\x23\111\102\x4c\x4f\x43\x4b\x5f\x4e\x41\x4d\x45\x23" => $ipj4t7dk[$aoc34nzah7vc31n0eyg52tyt35gk342ed]))), new \VKapi\Market\Condition\Control\Logic("\x63\x6f\156\x64\x69\164\x69\x6f\156", array(\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL, \VKapi\Market\Condition\Control\Logic::HAS, \VKapi\Market\Condition\Control\Logic::NOT_HAS, \VKapi\Market\Condition\Control\Logic::START, \VKapi\Market\Condition\Control\Logic::END), \VKapi\Market\Condition\Control\Logic::EQUAL), new \VKapi\Market\Condition\Control\Input("\166\141\x6c\165\145"));
                }
                $zslbvdz8c5xio4kaftc[] = array("\151\144" => $g7ortqhx . "\x5f" . $aoc34nzah7vc31n0eyg52tyt35gk342ed, "\x6e\141\x6d\145" => self::getMessage($g7ortqhx, array("\43\111\x42\x4c\x4f\103\x4b\137\116\x41\x4d\105\x23" => $ipj4t7dk[$aoc34nzah7vc31n0eyg52tyt35gk342ed])), "\x67\x72\x6f\165\x70" => str_replace(array("\43\111\x42\114\117\103\x4b\x5f\116\101\115\x45\43"), array($ipj4t7dk[$aoc34nzah7vc31n0eyg52tyt35gk342ed]), $this->groupLabel), "\143\157\155\x70\x6f\156\x65\x6e\x74" => "\x76\x6b\x61\160\151\55\155\141\x72\x6b\145\164\x2d\x63\x6f\x6e\x64\x69\x74\151\x6f\x6e\55\x69\x62\154\157\143\x6b\55\x65\154\x65\155\x65\x6e\x74\55\146\151\145\154\x64", "\143\157\x6e\164\162\157\x6c\163" => $ydknml44cpq0rt, "\x70\x61\162\x61\155\163" => array("\151\x62\x6c\157\x63\x6b\x49\144" => $aoc34nzah7vc31n0eyg52tyt35gk342ed), "\x6d\x6f\x72\x65" => array());
            }
        }
        return $zslbvdz8c5xio4kaftc;
    }
    
    public static function getEval($a0dazxatho69j7rlhn)
    {
        $arResult = array();
        $nsj2zwwjbbot25df9nv = $a0dazxatho69j7rlhn["\151\144"];
        $hrq4pmvsqugnhgj22o51iczlq = $a0dazxatho69j7rlhn["\x76\141\154\165\145\163"]["\x63\x6f\156\x64\x69\x74\151\x6f\x6e"];
        $mx22vsg2tfd18rde66v6twlm62k = str_replace("\x22", "\134\42", $a0dazxatho69j7rlhn["\166\x61\154\165\145\x73"]["\x76\x61\154\165\x65"]);
        switch (preg_replace("\x2f\x28\137\134\144\53\x29\x24\57", "", $nsj2zwwjbbot25df9nv)) {
            case "\x43\122\x45\101\124\105\104\137\102\x59":
            case "\115\x4f\x44\x49\x46\111\105\x44\x5f\x42\x59":
                if (in_array($hrq4pmvsqugnhgj22o51iczlq, array(\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL))) {
                    return \VKapi\Market\Condition\Control\Logic::getEvalRule($hrq4pmvsqugnhgj22o51iczlq, $nsj2zwwjbbot25df9nv, $mx22vsg2tfd18rde66v6twlm62k);
                }
                break;
            case "\x41\103\x54\111\126\105\x5f\104\x41\124\105":
            case "\101\103\x54\111\126\x45":
            case "\111\102\114\117\103\x4b\137\123\x45\103\124\x49\117\x4e\137\111\104":
                switch ($hrq4pmvsqugnhgj22o51iczlq) {
                    case \VKapi\Market\Condition\Control\Logic::EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::NOT_EQUAL:
                        return \VKapi\Market\Condition\Control\Logic::getEvalRule($hrq4pmvsqugnhgj22o51iczlq, $nsj2zwwjbbot25df9nv, $mx22vsg2tfd18rde66v6twlm62k);
                }
                break;
            case "\x44\101\x54\x45\137\x43\122\x45\x41\124\x45":
            case "\124\x49\115\105\x53\124\101\x4d\x50\137\130":
            case "\x44\x41\124\x45\137\101\x43\124\x49\126\105\x5f\x46\x52\x4f\115":
            case "\x44\x41\124\105\x5f\101\103\x54\111\x56\105\137\x54\117":
                switch ($hrq4pmvsqugnhgj22o51iczlq) {
                    case \VKapi\Market\Condition\Control\Logic::EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::NOT_EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::MORE:
                    case \VKapi\Market\Condition\Control\Logic::MORE_EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::LESS:
                    case \VKapi\Market\Condition\Control\Logic::LESS_EQUAL:
                        return \VKapi\Market\Condition\Control\Logic::getEvalRule($hrq4pmvsqugnhgj22o51iczlq, $nsj2zwwjbbot25df9nv, $mx22vsg2tfd18rde66v6twlm62k);
                }
                break;
            case "\103\x4f\104\x45":
            case "\x58\115\114\x5f\111\x44":
            case "\116\x41\115\x45":
            case "\x53\117\x52\x54":
            case "\x50\122\105\126\x49\105\x57\137\x54\105\x58\124":
            case "\104\x45\x54\x41\x49\114\137\x54\105\130\124":
            case "\124\101\107\123":
                switch ($hrq4pmvsqugnhgj22o51iczlq) {
                    case \VKapi\Market\Condition\Control\Logic::EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::NOT_EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::HAS:
                    case \VKapi\Market\Condition\Control\Logic::NOT_HAS:
                    case \VKapi\Market\Condition\Control\Logic::START:
                    case \VKapi\Market\Condition\Control\Logic::END:
                        return \VKapi\Market\Condition\Control\Logic::getEvalRule($hrq4pmvsqugnhgj22o51iczlq, $nsj2zwwjbbot25df9nv, $mx22vsg2tfd18rde66v6twlm62k);
                }
                break;
            default:
                return 0;
        }
    }
    
    public function getPrepiredValuePreview($n1wntz681nnj47j42cuz)
    {
        $vplekvu = $n1wntz681nnj47j42cuz["\x76\x61\154\x75\145\x73"];
        
        if (preg_replace("\x2f\50\x5f\x5c\x64\53\x29\x24\57", "", $n1wntz681nnj47j42cuz["\151\x64"]) == "\111\102\114\x4f\103\x4b\137\x53\x45\103\x54\111\117\116\x5f\111\104") {
            if (intval($vplekvu["\166\x61\x6c\x75\x65"])) {
                if ($qlouj7zk2 = \CIBlockSection::GetByID(intval($vplekvu["\166\x61\154\x75\145"]))->fetch()) {
                    $vplekvu["\x76\x61\x6c\x75\x65\x50\x72\x65\166\x69\145\167"] = $qlouj7zk2["\116\x41\x4d\x45"] . "\x20\133" . $qlouj7zk2["\x49\x44"] . "\x5d";
                }
            }
        }
        return $vplekvu;
    }
}
?>