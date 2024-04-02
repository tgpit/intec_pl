<?php

namespace VKapi\Market\Condition;

use Bitrix\Main\Localization\Loc;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class Group extends \VKapi\Market\Condition\Base
{
    
    public static function getMessage($v0s6p9xp365, $et3pij9za16ska2d0tetvoihtov5fe7nd = array())
    {
        return parent::getMessage("\107\x52\117\125\x50\x2e" . $v0s6p9xp365, $et3pij9za16ska2d0tetvoihtov5fe7nd);
    }
    
    public function getInternalConditions()
    {
        $zslbvdz8c5xio4kaftc = array();
        $zslbvdz8c5xio4kaftc[] = array("\151\144" => "\104\105\x46\101\125\x4c\x54", "\x6e\x61\x6d\145" => self::getMessage("\116\x41\x4d\x45"), "\143\157\155\x70\157\x6e\x65\x6e\164" => "\166\x6b\141\x70\x69\x2d\x6d\x61\162\x6b\145\164\55\x63\157\x6e\x64\x69\x74\151\x6f\156\x2d\x67\162\157\x75\160", "\x63\x6f\156\164\162\x6f\154\163" => array(new \VKapi\Market\Condition\Control\Select("\141\x67\x67\x72\145\x67\x61\164\x6f\x72", array("\x61\156\x64" => self::getMessage("\101\114\114\x5f\103\x4f\x4e\104\111\124\x49\x4f\116"), "\x6f\162" => self::getMessage("\x4f\116\105\137\x4f\x46\137\103\117\116\x44\x49\124\x49\x4f\x4e")), "\141\156\x64"), new \VKapi\Market\Condition\Control\Select("\164\171\160\145", array("\164\162\165\145" => self::getMessage("\x43\x4f\x4e\x44\x49\x54\x49\117\x4e\123\x5f\x54\122\x55\x45"), "\146\141\154\x73\145" => self::getMessage("\103\x4f\x4e\x44\x49\x54\111\x4f\116\x53\137\x46\101\x4c\123\x45")), "\164\x72\x75\x65")), "\x70\x61\162\141\155\x73" => $this->getParams(), "\155\157\x72\x65" => array("\x76\x69\x73\165\141\154" => array(array("\162\165\154\145" => array("\x61\x67\x67\x72\145\147\x61\164\157\162" => "\141\156\144", "\x74\171\x70\145" => "\x74\162\x75\x65"), "\143\x6c\x61\x73\163" => "\x76\153\141\x70\151\55\155\x61\x72\x6b\145\x74\55\143\157\156\144\x69\164\x69\x6f\x6e\55\x6c\157\147\151\x63\55\x2d\x61\156\x64\55\164\x72\x75\145", "\164\145\170\x74" => self::getMessage("\103\x4f\116\104\111\x54\111\117\x4e\x53\x5f\114\117\x47\x49\x43\137\101\x4e\x44")), array("\162\165\x6c\x65" => array("\141\x67\x67\162\x65\147\141\x74\x6f\162" => "\x6f\x72", "\x74\x79\x70\145" => "\164\x72\165\145"), "\x63\154\x61\x73\163" => "\x76\x6b\x61\x70\151\55\155\x61\162\x6b\x65\x74\x2d\143\157\156\x64\x69\164\151\157\156\x2d\154\x6f\147\151\x63\x2d\55\x6f\162\x2d\164\x72\165\x65", "\x74\x65\x78\164" => self::getMessage("\x43\117\116\x44\x49\x54\111\x4f\x4e\x53\x5f\114\117\107\x49\103\x5f\x4f\x52")), array("\x72\x75\154\145" => array("\x61\147\x67\162\145\147\x61\x74\157\162" => "\141\156\x64", "\x74\171\160\145" => "\x66\x61\x6c\x73\145"), "\x63\154\x61\x73\x73" => "\166\x6b\141\160\151\x2d\x6d\x61\162\x6b\145\164\55\143\157\156\144\151\x74\151\x6f\156\55\x6c\x6f\147\151\x63\55\55\x61\x6e\x64\55\146\x61\x6c\x73\145", "\x74\x65\170\164" => self::getMessage("\x43\117\x4e\104\111\x54\x49\117\x4e\123\137\114\x4f\x47\x49\x43\137\x41\116\x44\137\116\x4f")), array("\162\x75\154\145" => array("\141\x67\147\162\145\x67\x61\164\x6f\162" => "\x6f\162", "\164\x79\x70\145" => "\x66\141\x6c\x73\145"), "\143\154\x61\x73\163" => "\x76\x6b\141\160\x69\55\155\x61\x72\153\x65\x74\x2d\143\x6f\x6e\x64\151\164\x69\x6f\x6e\55\154\x6f\x67\151\143\55\x2d\157\x72\x2d\146\141\x6c\x73\x65", "\164\145\170\x74" => self::getMessage("\x43\x4f\116\x44\x49\x54\x49\x4f\116\123\137\114\x4f\107\x49\x43\137\x4f\x52\137\116\x4f")))));
        return $zslbvdz8c5xio4kaftc;
    }
    public static function getEval($a0dazxatho69j7rlhn)
    {
        $arResult = array();
        if (count($a0dazxatho69j7rlhn["\143\150\x69\154\144\163"])) {
            $arResult = self::getEvalForChilds($a0dazxatho69j7rlhn["\x63\150\151\154\144\163"]);
        }
        $xk7bc2nq7aotc7 = $a0dazxatho69j7rlhn["\x76\141\154\x75\x65\x73"]["\141\x67\147\162\x65\147\x61\x74\157\x72"] == "\x61\x6e\x64" ? "\x20\141\x6e\144\40" : "\40\x6f\162\x20";
        $hrq4pmvsqugnhgj22o51iczlq = $a0dazxatho69j7rlhn["\x76\141\x6c\x75\145\163"]["\164\x79\160\145"] == "\164\162\165\x65" ? "" : "\x21";
        if (empty($arResult)) {
            return false;
        } else {
            return $hrq4pmvsqugnhgj22o51iczlq . implode("\x20" . PHP_EOL . $xk7bc2nq7aotc7 . PHP_EOL . "\x20" . $hrq4pmvsqugnhgj22o51iczlq, $arResult);
        }
    }
}
?>