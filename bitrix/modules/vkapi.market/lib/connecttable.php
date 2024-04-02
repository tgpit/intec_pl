<?php

namespace VKapi\Market;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class ConnectTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }
    public static function getTableName()
    {
        return "\x76\153\x61\x70\151\x5f\x6d\x61\x72\x6b\x65\x74\137\x61\143\x63\x65\x73\x73\137\154\151\x73\x74";
    }
    public static function getMap()
    {
        return array(new \Bitrix\Main\Entity\IntegerField("\x49\x44", array("\x70\162\x69\155\141\162\171" => true, "\141\165\164\x6f\143\157\155\160\x6c\x65\x74\145" => true)), new \Bitrix\Main\Entity\IntegerField("\125\x53\105\122\137\x49\104", array(
            // bitrix user id
            "\162\x65\x71\165\151\x72\145\x64" => true,
        )), new \Bitrix\Main\Entity\IntegerField("\125\x53\x45\x52\x5f\111\104\137\126\113", array(
            // vk user id
            "\162\x65\161\x75\151\x72\x65\144" => true,
        )), new \Bitrix\Main\Entity\DatetimeField("\x45\x58\120\x49\122\x45\x53\137\x49\116", array("\162\145\x71\x75\x69\162\x65\x64" => true, "\166\x61\154\x69\144\141\x74\x6f\x72" => function () {
            return array(new \Bitrix\Main\Entity\Validator\Date());
        }, "\x64\x65\146\x61\165\x6c\x74\137\x76\x61\154\165\145" => new \Bitrix\Main\Type\DateTime())), new \Bitrix\Main\Entity\StringField("\101\103\103\x45\x53\123\137\124\117\113\105\116", array("\162\x65\161\165\151\162\x65\x64" => true, "\166\x61\154\151\144\x61\x74\157\162" => function () {
            return array(new \Bitrix\Main\Entity\Validator\Range(1, 255));
        })), new \Bitrix\Main\Entity\StringField("\116\101\115\x45", array("\144\145\146\141\x75\x6c\x74" => "\40\55\x20", "\166\x61\x6c\x69\x64\x61\164\157\x72" => function () {
            return array(new \Bitrix\Main\Entity\Validator\Range(0, 255));
        })), new \Bitrix\Main\Entity\ExpressionField("\103\x4e\x54", "\103\x4f\125\x4e\124\50\x49\104\51"));
    }
    
    public static function onBeforeAdd(\Bitrix\Main\Entity\Event $k4cj6rkan1a3btkqz75ewnel282a3)
    {
        $rz3menzpkkvl3bl0je = new \Bitrix\Main\Entity\EventResult();
        $d72p4och4h5ikxh032vvlfi32649wlpkp = $k4cj6rkan1a3btkqz75ewnel282a3->getParameter("\x66\x69\145\154\144\x73");
        $l49odpshsju80bkgjiyyxcqp6qv1lb0rb4 = array();
        if (!isset($d72p4och4h5ikxh032vvlfi32649wlpkp["\x45\130\x50\x49\x52\x45\123\x5f\x49\116"]) || $d72p4och4h5ikxh032vvlfi32649wlpkp["\105\x58\x50\111\122\105\x53\137\111\116"] == 0) {
            $ragb3td3gg3 = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime("\x2b\x30\x73\x65\143\157\156\144"));
            $l49odpshsju80bkgjiyyxcqp6qv1lb0rb4["\105\x58\120\111\x52\x45\123\137\x49\x4e"] = $ragb3td3gg3;
        } else {
            $ragb3td3gg3 = \Bitrix\Main\Type\DateTime::createFromTimestamp(time() + $d72p4och4h5ikxh032vvlfi32649wlpkp["\105\130\x50\111\122\x45\x53\137\x49\116"]);
            $l49odpshsju80bkgjiyyxcqp6qv1lb0rb4["\105\x58\120\x49\122\105\x53\137\111\x4e"] = $ragb3td3gg3;
        }
        $rz3menzpkkvl3bl0je->modifyFields($l49odpshsju80bkgjiyyxcqp6qv1lb0rb4);
        return $rz3menzpkkvl3bl0je;
    }
    
    public static function onBeforeUpdate(\Bitrix\Main\Entity\Event $k4cj6rkan1a3btkqz75ewnel282a3)
    {
        $rz3menzpkkvl3bl0je = new \Bitrix\Main\Entity\EventResult();
        $d72p4och4h5ikxh032vvlfi32649wlpkp = $k4cj6rkan1a3btkqz75ewnel282a3->getParameter("\146\x69\145\154\x64\x73");
        $l49odpshsju80bkgjiyyxcqp6qv1lb0rb4 = array();
        if (!isset($d72p4och4h5ikxh032vvlfi32649wlpkp["\105\130\x50\x49\122\105\123\137\111\116"]) || $d72p4och4h5ikxh032vvlfi32649wlpkp["\x45\x58\x50\x49\122\x45\x53\137\x49\x4e"] == 0) {
            $ragb3td3gg3 = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime("\x2b\60\163\x65\x63\157\156\144"));
            $l49odpshsju80bkgjiyyxcqp6qv1lb0rb4["\105\x58\x50\111\122\x45\x53\137\x49\116"] = $ragb3td3gg3;
        } else {
            $ragb3td3gg3 = \Bitrix\Main\Type\DateTime::createFromTimestamp(time() + $d72p4och4h5ikxh032vvlfi32649wlpkp["\x45\130\120\111\x52\105\x53\137\x49\116"]);
            $l49odpshsju80bkgjiyyxcqp6qv1lb0rb4["\105\x58\120\111\122\105\123\137\111\x4e"] = $ragb3td3gg3;
        }
        $rz3menzpkkvl3bl0je->modifyFields($l49odpshsju80bkgjiyyxcqp6qv1lb0rb4);
        return $rz3menzpkkvl3bl0je;
    }
}
?>