<?php

namespace VKapi\Market\Condition;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class CatalogField extends \VKapi\Market\Condition\Base
{
    private $arExistsFields = array("\103\x41\124\101\114\x4f\107\x5f\101\126\x41\x49\114\101\x42\114\x45", "\103\101\124\x41\x4c\117\x47\x5f\127\x45\111\107\110\x54", "\103\101\124\101\114\x4f\x47\x5f\x51\125\x41\116\x54\111\x54\131");
    public function __construct($arParams = array())
    {
        
        $b03hprt3qyjbx7zcqvz0ietyx0hssb = $this->getStoreList();
        foreach ($b03hprt3qyjbx7zcqvz0ietyx0hssb as $jc2mhwl2g3w408jvk => $v0s6p9xp365) {
            $this->arExistsFields[] = "\103\101\x54\x41\114\117\x47\137\x53\124\x4f\122\x45\x5f" . $jc2mhwl2g3w408jvk;
        }
        
        $r4ww21tib0t9ze0tv132zb4 = $this->getPriceList();
        foreach ($r4ww21tib0t9ze0tv132zb4 as $jc2mhwl2g3w408jvk => $v0s6p9xp365) {
            $this->arExistsFields[] = "\x43\x41\124\101\114\117\x47\x5f\x47\x52\x4f\x55\120\137" . $jc2mhwl2g3w408jvk;
            $this->arExistsFields[] = "\103\x41\x54\101\x4c\x4f\x47\x5f\x50\x52\111\103\x45\137" . $jc2mhwl2g3w408jvk;
            $this->arExistsFields[] = "\103\x41\x54\x41\x4c\117\x47\137\104\x49\123\103\117\125\116\124\x5f\120\105\122\x43\105\116\x54\x5f" . $jc2mhwl2g3w408jvk;
            $this->arExistsFields[] = "\x43\101\x54\101\x4c\x4f\x47\137\104\x49\123\103\117\125\116\x54\137\x50\x52\111\x43\105\137" . $jc2mhwl2g3w408jvk;
        }
        parent::__construct($arParams);
    }
    
    protected static function getMessage($v0s6p9xp365, $et3pij9za16ska2d0tetvoihtov5fe7nd = array())
    {
        return parent::getMessage("\103\101\124\x41\114\x4f\107\106\x49\105\x4c\104\56" . $v0s6p9xp365, $et3pij9za16ska2d0tetvoihtov5fe7nd);
    }
    
    protected static function isInstalledCatalogModule()
    {
        static $vg99ejd118y72lkhkqky433;
        if (!isset($vg99ejd118y72lkhkqky433)) {
            $vg99ejd118y72lkhkqky433 = \Bitrix\Main\Loader::includeModule("\x63\x61\x74\x61\x6c\x6f\x67");
        }
        return $vg99ejd118y72lkhkqky433;
    }
    
    public static function getStoreList()
    {
        static $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df;
        if (!isset($h3jh4oc43rrs3lu158mjh0vuozyfrwv81df)) {
            $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df = array();
            if (self::isInstalledCatalogModule()) {
                if (class_exists("\x5c\103\x43\x61\x74\x61\x6c\157\147\123\164\x6f\162\145")) {
                    $mekwu180dwog63b0zsdywrxysy1 = \CCatalogStore::GetList(array("\116\101\x4d\105" => "\101\x53\103"), array());
                    while ($xspudkepa = $mekwu180dwog63b0zsdywrxysy1->fetch()) {
                        $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df[$xspudkepa["\x49\104"]] = $xspudkepa["\x54\111\x54\x4c\x45"] . "\x20\133" . $xspudkepa["\x49\x44"] . "\135";
                    }
                }
            }
        }
        return $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df;
    }
    
    public static function getPriceList()
    {
        static $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df;
        if (!isset($h3jh4oc43rrs3lu158mjh0vuozyfrwv81df)) {
            $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df = array();
            if (self::isInstalledCatalogModule()) {
                if (class_exists("\134\x43\x43\x61\x74\x61\154\x6f\x67\x47\162\157\165\160")) {
                    $mekwu180dwog63b0zsdywrxysy1 = \CCatalogGroup::GetList();
                    while ($xspudkepa = $mekwu180dwog63b0zsdywrxysy1->fetch()) {
                        $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df[$xspudkepa["\111\104"]] = $xspudkepa["\x4e\x41\x4d\x45\137\114\101\x4e\x47"] . "\x20\x28" . $xspudkepa["\116\x41\x4d\105"] . "\x29\40\x5b" . $xspudkepa["\x49\104"] . "\135";
                    }
                }
            }
        }
        return $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df;
    }
    
    public static function getElementDefaultValues()
    {
        static $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df;
        if (!isset($h3jh4oc43rrs3lu158mjh0vuozyfrwv81df)) {
            $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df = array();
            $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df["\x43\101\x54\x41\114\117\x47\137\127\105\x49\107\x48\x54"] = 0;
            $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df["\x43\101\x54\101\114\117\107\137\121\125\x41\116\x54\x49\124\131"] = 0;
            $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df["\x43\x41\124\x41\x4c\117\107\137\101\x56\101\x49\114\101\102\114\105"] = "\x4e";
            foreach (self::getStoreList() as $jc2mhwl2g3w408jvk => $v0s6p9xp365) {
                $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df["\x43\x41\x54\x41\114\x4f\x47\x5f\x53\124\x4f\x52\x45\137" . $jc2mhwl2g3w408jvk] = 0;
            }
            foreach (self::getPriceList() as $jc2mhwl2g3w408jvk => $v0s6p9xp365) {
                $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df["\x43\x41\x54\101\x4c\x4f\107\x5f\107\x52\x4f\x55\x50\137" . $jc2mhwl2g3w408jvk] = 0;
                $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df["\103\101\124\x41\114\x4f\107\137\x50\122\x49\103\105\137" . $jc2mhwl2g3w408jvk] = 0;
                $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df["\x43\101\124\101\114\117\107\x5f\x44\x49\123\x43\x4f\125\x4e\124\137\x50\x45\122\103\x45\x4e\x54\137" . $jc2mhwl2g3w408jvk] = 0;
                $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df["\x43\101\124\x41\x4c\x4f\x47\137\104\x49\123\103\x4f\125\116\x54\x5f\120\x52\111\103\x45\137" . $jc2mhwl2g3w408jvk] = 0;
            }
        }
        return $h3jh4oc43rrs3lu158mjh0vuozyfrwv81df;
    }
    
    public function getInternalConditions()
    {
        $zslbvdz8c5xio4kaftc = array();
        $bawna8v63b = self::getStoreList();
        $kfjzt99vddq66 = self::getPriceList();
        foreach ($this->arExistsFields as $g7ortqhx) {
            $v0s6p9xp365 = self::getMessage($g7ortqhx);
            if (preg_match("\57\103\101\x54\101\114\x4f\x47\137\x53\x54\x4f\x52\105\x5f\x28\134\x64\53\51\x2f", $g7ortqhx, $tqryhfcxzkw365n5)) {
                $v0s6p9xp365 = self::getMessage("\123\113\x4c\x41\x44", array("\x23\123\x4b\114\x41\104\x23" => $bawna8v63b[$tqryhfcxzkw365n5[1]]));
            } elseif (preg_match("\x2f\103\101\x54\101\x4c\117\x47\137\x47\122\x4f\125\120\x5f\50\x5c\x64\x2b\51\57", $g7ortqhx, $tqryhfcxzkw365n5)) {
                $v0s6p9xp365 = self::getMessage("\107\x52\x4f\x55\120", array("\x23\120\x52\111\103\105\43" => $kfjzt99vddq66[$tqryhfcxzkw365n5[1]]));
            } elseif (preg_match("\x2f\103\101\x54\x41\114\x4f\x47\137\x50\x52\111\x43\x45\x5f\x28\x5c\144\x2b\51\57", $g7ortqhx, $tqryhfcxzkw365n5)) {
                $v0s6p9xp365 = self::getMessage("\120\122\111\x43\x45", array("\x23\120\122\111\x43\x45\x23" => $kfjzt99vddq66[$tqryhfcxzkw365n5[1]]));
            } elseif (preg_match("\x2f\103\101\124\101\x4c\117\107\137\x44\111\123\103\x4f\x55\116\x54\137\120\105\x52\x43\x45\x4e\124\x5f\50\x5c\144\x2b\x29\x2f", $g7ortqhx, $tqryhfcxzkw365n5)) {
                $v0s6p9xp365 = self::getMessage("\104\111\x53\x43\x4f\125\x4e\x54\x5f\x50\x45\x52\x43\x45\x4e\124", array("\43\x50\122\111\103\105\x23" => $kfjzt99vddq66[$tqryhfcxzkw365n5[1]]));
            } elseif (preg_match("\57\x43\x41\124\101\114\x4f\x47\137\104\111\123\103\x4f\x55\x4e\124\137\120\x52\111\x43\105\137\50\x5c\144\53\x29\57", $g7ortqhx, $tqryhfcxzkw365n5)) {
                $v0s6p9xp365 = self::getMessage("\104\x49\123\x43\x4f\125\116\124\137\120\x52\111\x43\105", array("\x23\x50\122\111\103\x45\x23" => $kfjzt99vddq66[$tqryhfcxzkw365n5[1]]));
            }
            if ($g7ortqhx == "\x43\101\124\101\x4c\117\107\137\x41\x56\x41\111\x4c\101\102\114\105") {
                $ydknml44cpq0rt = array(new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\103\117\x4e\x54\x52\117\114\137\x54\105\x58\x54", array("\x23\116\x41\115\x45\43" => $v0s6p9xp365))), new \VKapi\Market\Condition\Control\Logic("\143\x6f\156\144\x69\x74\x69\157\156", array(\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL), \VKapi\Market\Condition\Control\Logic::EQUAL), new \VKapi\Market\Condition\Control\Select("\x76\x61\154\x75\x65", array("\x59" => self::getMessage("\131\105\x53"), "\116" => self::getMessage("\x4e\x4f"))));
            } else {
                $ydknml44cpq0rt = array(new \VKapi\Market\Condition\Control\Prefix(self::getMessage("\x43\x4f\116\124\122\117\x4c\137\x54\x45\130\124", array("\43\x4e\101\x4d\x45\x23" => $v0s6p9xp365))), new \VKapi\Market\Condition\Control\Logic("\x63\157\156\144\x69\x74\x69\x6f\x6e", array(\VKapi\Market\Condition\Control\Logic::EQUAL, \VKapi\Market\Condition\Control\Logic::NOT_EQUAL, \VKapi\Market\Condition\Control\Logic::MORE, \VKapi\Market\Condition\Control\Logic::MORE_EQUAL, \VKapi\Market\Condition\Control\Logic::LESS, \VKapi\Market\Condition\Control\Logic::LESS_EQUAL), \VKapi\Market\Condition\Control\Logic::EQUAL), new \VKapi\Market\Condition\Control\Input("\166\x61\154\x75\145"));
            }
            $zslbvdz8c5xio4kaftc[] = array("\151\x64" => $g7ortqhx, "\156\x61\155\x65" => $v0s6p9xp365, "\x67\162\157\165\x70" => self::getMessage("\x47\x52\117\x55\120\137\x4c\101\x42\105\x4c"), "\x63\x6f\x6d\x70\x6f\156\x65\x6e\x74" => "\166\x6b\x61\x70\151\55\155\141\162\x6b\145\x74\55\143\157\x6e\144\151\164\151\x6f\156\x2d\143\141\164\x61\154\157\x67\x2d\x66\x69\145\154\144", "\x63\x6f\x6e\x74\x72\x6f\x6c\x73" => $ydknml44cpq0rt, "\160\x61\x72\x61\x6d\x73" => $this->getParams(), "\x6d\x6f\x72\x65" => array());
        }
        return $zslbvdz8c5xio4kaftc;
    }
    
    public static function getEval($a0dazxatho69j7rlhn)
    {
        $nsj2zwwjbbot25df9nv = $a0dazxatho69j7rlhn["\x69\x64"];
        $hrq4pmvsqugnhgj22o51iczlq = $a0dazxatho69j7rlhn["\166\141\154\x75\145\163"]["\x63\x6f\x6e\x64\151\164\151\x6f\156"];
        $mx22vsg2tfd18rde66v6twlm62k = str_replace("\x22", "\134\42", $a0dazxatho69j7rlhn["\166\x61\x6c\x75\145\163"]["\166\x61\x6c\165\145"]);
        switch ($nsj2zwwjbbot25df9nv) {
            case "\x43\x41\124\101\114\117\107\x5f\x41\x56\101\111\x4c\101\102\114\105":
                switch ($hrq4pmvsqugnhgj22o51iczlq) {
                    case \VKapi\Market\Condition\Control\Logic::EQUAL:
                    case \VKapi\Market\Condition\Control\Logic::NOT_EQUAL:
                        return \VKapi\Market\Condition\Control\Logic::getEvalRule($hrq4pmvsqugnhgj22o51iczlq, $nsj2zwwjbbot25df9nv, $mx22vsg2tfd18rde66v6twlm62k);
                }
                break;
            default:
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
        }
        return 0;
    }
}
?>