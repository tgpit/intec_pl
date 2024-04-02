<?php

namespace VKapi\Market\Condition;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use VKapi\Market\Condition\Control\Logic;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class IblockElementFieldBase extends \VKapi\Market\Condition\Base
{
    private $arExistsFields = ["\x49\123\x5f\117\106\x46\x45\x52", "\111\104", "\111\x42\114\117\103\x4b\x5f\x49\104", "\111\x42\x4c\x4f\x43\x4b\137\x53\105\x43\x54\x49\117\116\137\x49\104", "\103\117\104\105", "\130\x4d\114\137\111\104", "\116\101\115\x45", "\x41\103\124\x49\x56\105", "\x41\x43\x54\111\x56\105\x5f\104\x41\x54\x45", "\x44\x41\124\x45\137\101\103\124\x49\126\x45\137\106\x52\x4f\115", "\104\x41\x54\x45\137\101\103\x54\x49\x56\105\137\x54\x4f", "\123\x4f\x52\x54", "\x50\x52\x45\x56\x49\x45\127\137\124\105\x58\x54", "\x44\x45\x54\101\111\x4c\x5f\x54\x45\130\x54", "\104\101\124\105\137\x43\x52\105\101\124\x45", "\x43\122\105\101\124\x45\x44\x5f\x42\131", "\124\x49\x4d\105\x53\x54\x41\115\120\x5f\130", "\x4d\117\x44\111\106\111\105\x44\x5f\102\131", "\x54\x41\x47\123"];
    private $groupLabel = "";
    public function __construct($arParams = [])
    {
        
        $this->groupLabel = self::getMessage("\107\122\117\125\120\x5f\x4c\x41\102\105\114");
        if (isset($arParams["\114\101\x42\x45\114"])) {
            $this->groupLabel = $arParams["\x4c\101\102\x45\114"];
        }
        parent::__construct($arParams);
    }
    
    protected static function getMessage($v0s6p9xp365, $et3pij9za16ska2d0tetvoihtov5fe7nd = [])
    {
        return parent::getMessage("\x49\x42\114\x4f\103\x4b\x45\114\x45\115\105\x4e\x54\x46\x49\x45\x4c\x44\123\102\101\123\x45\x2e" . $v0s6p9xp365, $et3pij9za16ska2d0tetvoihtov5fe7nd);
    }
    
    public static function getIblockList()
    {
        static $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df;
        if (!isset($h3jh4oc43rrs3lu158mjh0vuozyfrwv81df)) {
            \Bitrix\Main\Loader::includeModule("\x69\142\x6c\x6f\x63\x6b");
            $mekwu180dwog63b0zsdywrxysy1 = \CIBlock::GetList(["\116\101\115\x45" => "\x41\123\103"]);
            while ($xspudkepa = $mekwu180dwog63b0zsdywrxysy1->fetch()) {
                $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df[$xspudkepa["\x49\104"]] = $xspudkepa["\x4e\101\x4d\x45"] . "\x20\x5b" . $xspudkepa["\x49\104"] . "\x5d";
            }
        }
        return $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df;
    }
    
    public function getInternalConditions()
    {
        $zslbvdz8c5xio4kaftc = [];
        foreach ($this->arExistsFields as $g7ortqhx) {
            if (in_array($g7ortqhx, ["\x49\104", "\x43\x52\x45\x41\x54\105\x44\137\102\131", "\115\x4f\x44\111\106\111\x45\x44\137\102\x59"])) {
                $ydknml44cpq0rt = [new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\103\x4f\x4e\124\122\117\x4c\x5f\x54\105\130\x54", ["\43\116\x41\115\x45\43" => self::getMessage($g7ortqhx)])), new \VKapi\Market\Condition\Control\Logic("\x63\x6f\156\144\151\164\x69\157\x6e", [\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL, \VKapi\Market\Condition\Control\Logic::STRICT_EQUAL, \VKapi\Market\Condition\Control\Logic::STRICT_NOT_EQUAL], \VKapi\Market\Condition\Control\Logic::EQUAL), new \VKapi\Market\Condition\Control\Input("\x76\x61\x6c\165\145")];
            } elseif (in_array($g7ortqhx, ["\x49\x53\137\x4f\106\106\x45\x52", "\x41\x43\124\x49\x56\x45\137\x44\101\x54\x45", "\x41\x43\124\x49\x56\x45"])) {
                $ydknml44cpq0rt = [new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\x43\117\x4e\124\x52\117\114\x5f\124\x45\130\124", ["\x23\116\x41\x4d\105\x23" => self::getMessage($g7ortqhx)])), new \VKapi\Market\Condition\Control\Logic("\x63\x6f\x6e\144\151\164\x69\x6f\x6e", [\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL, \VKapi\Market\Condition\Control\Logic::STRICT_EQUAL, \VKapi\Market\Condition\Control\Logic::STRICT_NOT_EQUAL], \VKapi\Market\Condition\Control\Logic::EQUAL), new \VKapi\Market\Condition\Control\Select("\166\x61\x6c\165\145", ["\131" => self::getMessage("\x59\x45\x53"), "\x4e" => self::getMessage("\x4e\x4f")])];
            } elseif ($g7ortqhx == "\111\102\114\117\103\113\x5f\x49\104") {
                $ydknml44cpq0rt = [new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\103\117\116\124\x52\x4f\114\x5f\x54\x45\130\x54", ["\x23\x4e\101\x4d\x45\x23" => self::getMessage($g7ortqhx)])), new \VKapi\Market\Condition\Control\Logic("\x63\157\x6e\x64\x69\x74\x69\x6f\156", [\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL, \VKapi\Market\Condition\Control\Logic::STRICT_EQUAL, \VKapi\Market\Condition\Control\Logic::STRICT_NOT_EQUAL], \VKapi\Market\Condition\Control\Logic::EQUAL), new \VKapi\Market\Condition\Control\Select("\166\141\154\165\145", self::getIblockList(), "", true)];
            } elseif ($g7ortqhx == "\111\x42\114\x4f\x43\x4b\x5f\123\x45\x43\124\x49\x4f\116\137\111\104") {
                $ydknml44cpq0rt = [new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\x43\x4f\x4e\124\122\x4f\x4c\x5f\124\x45\x58\124", ["\x23\116\101\115\105\43" => self::getMessage($g7ortqhx)])), new \VKapi\Market\Condition\Control\Logic("\x63\157\x6e\x64\151\164\151\157\x6e", [\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL, \VKapi\Market\Condition\Control\Logic::STRICT_EQUAL, \VKapi\Market\Condition\Control\Logic::STRICT_NOT_EQUAL], \VKapi\Market\Condition\Control\Logic::EQUAL), new \VKapi\Market\Condition\Control\IblockSectionFind("\x76\141\154\165\145")];
            } elseif (in_array($g7ortqhx, ["\104\x41\124\105\137\103\x52\105\101\x54\105", "\124\x49\115\x45\123\x54\101\x4d\x50\137\130", "\x44\101\124\105\137\x41\x43\124\x49\x56\x45\137\106\122\117\x4d", "\104\x41\124\105\137\x41\x43\124\x49\x56\105\137\x54\117"])) {
                $ydknml44cpq0rt = [new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\x43\x4f\x4e\x54\122\x4f\x4c\x5f\x54\x45\x58\124", ["\43\116\101\x4d\105\x23" => self::getMessage($g7ortqhx)])), new \VKapi\Market\Condition\Control\Logic("\x63\157\156\144\151\x74\x69\x6f\x6e", [\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL, \VKapi\Market\Condition\Control\Logic::STRICT_EQUAL, \VKapi\Market\Condition\Control\Logic::STRICT_NOT_EQUAL, \VKapi\Market\Condition\Control\Logic::MORE, \VKapi\Market\Condition\Control\Logic::MORE_EQUAL, \VKapi\Market\Condition\Control\Logic::LESS, \VKapi\Market\Condition\Control\Logic::LESS_EQUAL], \VKapi\Market\Condition\Control\Logic::EQUAL), new \VKapi\Market\Condition\Control\Calendar("\166\141\x6c\165\x65")];
            } else {
                $ydknml44cpq0rt = [new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\x43\x4f\x4e\x54\x52\x4f\x4c\137\124\105\130\124", ["\x23\116\x41\115\105\43" => self::getMessage($g7ortqhx)])), new \VKapi\Market\Condition\Control\Logic("\143\x6f\x6e\x64\x69\x74\x69\157\156", [\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL, \VKapi\Market\Condition\Control\Logic::STRICT_EQUAL, \VKapi\Market\Condition\Control\Logic::STRICT_NOT_EQUAL, \VKapi\Market\Condition\Control\Logic::HAS, \VKapi\Market\Condition\Control\Logic::NOT_HAS, \VKapi\Market\Condition\Control\Logic::START, \VKapi\Market\Condition\Control\Logic::END], \VKapi\Market\Condition\Control\Logic::EQUAL), new \VKapi\Market\Condition\Control\Input("\x76\x61\154\x75\145")];
            }
            $zslbvdz8c5xio4kaftc[] = ["\x69\144" => $g7ortqhx, "\156\141\x6d\145" => self::getMessage($g7ortqhx), "\x67\x72\157\165\160" => $this->groupLabel, "\143\x6f\x6d\x70\x6f\156\145\156\164" => "\x76\153\141\x70\151\x2d\x6d\141\162\x6b\145\x74\x2d\x63\157\x6e\x64\151\164\x69\157\x6e\55\151\142\x6c\x6f\143\x6b\x2d\x65\x6c\x65\x6d\145\x6e\x74\55\146\x69\x65\x6c\x64\x2d\142\x61\x73\x65", "\x63\x6f\x6e\164\162\x6f\154\163" => $ydknml44cpq0rt, "\160\x61\162\x61\155\x73" => [], "\x6d\157\162\145" => []];
        }
        return $zslbvdz8c5xio4kaftc;
    }
    
    public static function getEval($a0dazxatho69j7rlhn)
    {
        $arResult = [];
        $nsj2zwwjbbot25df9nv = $a0dazxatho69j7rlhn["\151\144"];
        $hrq4pmvsqugnhgj22o51iczlq = $a0dazxatho69j7rlhn["\166\x61\x6c\x75\145\163"]["\x63\157\x6e\x64\151\x74\151\157\x6e"];
        $mx22vsg2tfd18rde66v6twlm62k = str_replace("\42", "\134\42", $a0dazxatho69j7rlhn["\x76\141\x6c\x75\x65\x73"]["\166\141\154\x75\x65"]);
        switch ($nsj2zwwjbbot25df9nv) {
            case "\x49\x44":
            case "\x49\102\x4c\117\103\113\137\111\x44":
            case "\x43\x52\105\101\124\105\x44\x5f\102\131":
            case "\x4d\x4f\104\111\106\111\105\104\137\102\131":
            case "\x41\103\x54\111\126\x45\x5f\x44\101\124\105":
            case "\101\103\x54\111\x56\x45":
            case "\111\123\x5f\117\x46\x46\x45\x52":
            case "\x49\x42\114\x4f\x43\113\x5f\123\105\103\124\111\x4f\x4e\137\x49\104":
                switch ($hrq4pmvsqugnhgj22o51iczlq) {
                    case \VKapi\Market\Condition\Control\Logic::EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::NOT_EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::STRICT_EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::STRICT_NOT_EQUAL:
                        return \VKapi\Market\Condition\Control\Logic::getEvalRule($hrq4pmvsqugnhgj22o51iczlq, $nsj2zwwjbbot25df9nv, $mx22vsg2tfd18rde66v6twlm62k);
                }
                break;
            case "\104\101\124\x45\x5f\103\122\105\101\124\105":
            case "\124\111\115\x45\123\x54\x41\x4d\120\137\130":
            case "\x44\x41\124\105\137\x41\103\x54\x49\x56\105\x5f\x46\x52\x4f\115":
            case "\x44\x41\x54\x45\137\101\x43\124\111\x56\x45\x5f\124\x4f":
                switch ($hrq4pmvsqugnhgj22o51iczlq) {
                    case \VKapi\Market\Condition\Control\Logic::EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::NOT_EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::STRICT_EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::STRICT_NOT_EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::MORE:
                    case \VKapi\Market\Condition\Control\Logic::MORE_EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::LESS:
                    case \VKapi\Market\Condition\Control\Logic::LESS_EQUAL:
                        return \VKapi\Market\Condition\Control\Logic::getEvalRule($hrq4pmvsqugnhgj22o51iczlq, $nsj2zwwjbbot25df9nv, $mx22vsg2tfd18rde66v6twlm62k);
                }
                break;
            case "\x43\x4f\x44\105":
            case "\x58\115\114\137\111\104":
            case "\x4e\x41\x4d\105":
            case "\123\x4f\122\x54":
            case "\120\x52\105\126\x49\105\x57\137\x54\x45\130\x54":
            case "\104\105\x54\x41\x49\x4c\x5f\124\x45\130\x54":
            case "\x54\101\x47\123":
                switch ($hrq4pmvsqugnhgj22o51iczlq) {
                    case \VKapi\Market\Condition\Control\Logic::EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::NOT_EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::STRICT_EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::STRICT_NOT_EQUAL:
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
        $vplekvu = $n1wntz681nnj47j42cuz["\166\141\x6c\165\145\x73"];
        
        if ($n1wntz681nnj47j42cuz["\x69\144"] == "\x49\x42\114\x4f\103\x4b\137\x53\105\x43\124\111\x4f\x4e\137\x49\x44") {
            if (intval($vplekvu["\166\x61\154\x75\x65"])) {
                if ($qlouj7zk2 = \CIBlockSection::GetByID(intval($vplekvu["\x76\x61\154\165\x65"]))->fetch()) {
                    $vplekvu["\166\x61\x6c\x75\145\120\162\x65\166\x69\145\167"] = $qlouj7zk2["\116\x41\x4d\x45"] . "\40\x5b" . $qlouj7zk2["\111\x44"] . "\x5d";
                }
            }
        }
        return $vplekvu;
    }
}
?>