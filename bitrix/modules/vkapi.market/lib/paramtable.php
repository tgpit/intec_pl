<?php

namespace VKapi\Market;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
class ParamTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }
    public static function getTableName()
    {
        return "\x76\153\x61\160\151\137\155\x61\162\153\145\x74\x5f\x70\141\x72\x61\155";
    }
    public static function getMap()
    {
        return array(new \Bitrix\Main\Entity\StringField("\x43\117\x44\105", array("\x70\x72\151\x6d\141\162\x79" => true)), new \Bitrix\Main\Entity\StringField("\126\101\114\125\105"), new \Bitrix\Main\Entity\DatetimeField("\105\104\x49\x54\137\124\111\x4d\x45", array("\162\145\161\165\151\162\145\144" => true, "\x64\x65\x66\141\x75\x6c\x74\x5f\x76\141\x6c\165\x65" => new \Bitrix\Main\Type\DateTime())), new \Bitrix\Main\Entity\ExpressionField("\x43\x4e\124", "\103\117\x55\x4e\124\x28\x2a\51"));
    }
    public static function onBeforeAdd(\Bitrix\Main\Entity\Event $k4cj6rkan1a3btkqz75ewnel282a3)
    {
        $rz3menzpkkvl3bl0je = new \Bitrix\Main\Entity\EventResult();
        $d72p4och4h5ikxh032vvlfi32649wlpkp = $k4cj6rkan1a3btkqz75ewnel282a3->getParameter("\146\x69\145\x6c\x64\x73");
        if (!isset($d72p4och4h5ikxh032vvlfi32649wlpkp["\x45\x44\111\x54\x5f\x54\x49\115\105"])) {
            $rz3menzpkkvl3bl0je->modifyFields(array("\x45\104\111\124\137\x54\x49\115\x45" => new \Bitrix\Main\Type\DateTime()));
        }
        return $rz3menzpkkvl3bl0je;
    }
    public static function onBeforeUpdate(\Bitrix\Main\Entity\Event $k4cj6rkan1a3btkqz75ewnel282a3)
    {
        $rz3menzpkkvl3bl0je = new \Bitrix\Main\Entity\EventResult();
        $d72p4och4h5ikxh032vvlfi32649wlpkp = $k4cj6rkan1a3btkqz75ewnel282a3->getParameter("\146\x69\145\154\144\x73");
        if (!isset($d72p4och4h5ikxh032vvlfi32649wlpkp["\105\x44\x49\x54\137\x54\111\x4d\x45"])) {
            $rz3menzpkkvl3bl0je->modifyFields(array("\105\104\x49\124\x5f\124\x49\115\x45" => new \Bitrix\Main\Type\DateTime()));
        }
        return $rz3menzpkkvl3bl0je;
    }
}
?>