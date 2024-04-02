<?php

namespace VKapi\Market;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class ExportTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }
    public static function getTableName()
    {
        return "\x76\x6b\x61\160\x69\x5f\155\x61\x72\153\x65\164\x5f\x65\170\x70\157\162\x74\137\x6c\151\163\x74";
    }
    public static function getMap()
    {
        return array(new \Bitrix\Main\Entity\IntegerField("\x49\104", array(
            // инкримет записи
            "\x70\162\151\x6d\141\162\x79" => true,
            "\x61\165\x74\157\x63\157\155\x70\154\x65\x74\145" => true,
        )), new \Bitrix\Main\Entity\StringField("\123\x49\124\x45\x5f\111\104", array(
            //привязка к сайту
            "\162\x65\x71\165\x69\x72\145\x64" => true,
            "\x76\141\154\x69\144\141\x74\157\162" => function () {
                return array(new \Bitrix\Main\Entity\Validator\Range(2, 2));
            },
        )), new \Bitrix\Main\Entity\IntegerField("\x41\103\103\x4f\x55\116\x54\137\x49\x44", array(
            //идентификатор добавленного аккаунта, от имени которого выгружать
            "\x72\145\x71\165\x69\162\145\x64" => true,
        )), new \Bitrix\Main\Entity\IntegerField("\x47\122\117\x55\120\137\111\104", array(
            //идентификатор группы в вконткате, положительное целое число
            "\162\145\161\165\151\162\x65\x64" => true,
        )), new \Bitrix\Main\Entity\StringField("\107\122\117\x55\120\x5f\x4e\101\x4d\105", array(
            // название группы в вконтакте, для вывода в списке выгрузок
            "\x72\145\161\x75\x69\x72\145\x64" => true,
        )), new \Bitrix\Main\Entity\StringField("\x4e\x41\115\x45", array(
            // название выгрузки
            "\162\145\x71\x75\x69\162\145\x64" => true,
        )), new \Bitrix\Main\Entity\BooleanField("\101\103\124\111\126\105", array("\x72\145\x71\165\151\162\x65\x64" => true, "\x64\145\146\x61\165\x6c\164" => false)), new \Bitrix\Main\Entity\BooleanField("\x41\x55\124\x4f", array("\x72\145\x71\x75\151\x72\x65\x64" => true, "\144\x65\x66\x61\x75\x6c\x74" => true)), new \Bitrix\Main\Entity\IntegerField("\103\101\x54\x41\114\x4f\107\x5f\111\x44", array("\162\145\161\165\151\x72\145\144" => true)), new \Bitrix\Main\Entity\TextField("\101\x4c\102\125\115\x53", array(
            // подборки
            "\x72\145\x71\x75\151\x72\145\x64" => false,
            "\x73\145\162\151\141\154\151\x7a\145\x64" => true,
            "\x64\145\x66\141\165\x6c\x74\x5f\x76\141\154\165\145" => array(),
        )), new \Bitrix\Main\Entity\TextField("\120\101\x52\101\115\x53", array("\x72\x65\161\165\x69\x72\145\x64" => true, "\163\145\162\151\141\x6c\x69\x7a\145\144" => true, "\167\x61\x69\x74\x5f\x70\141\x72\141\155\163\137\x6c\x69\x73\164" => array("\103\125\122\122\x45\116\x43\x59"))), new \Bitrix\Main\Entity\ExpressionField("\x43\x4e\124", "\x43\117\x55\x4e\x54\x28\x49\104\51"));
    }
    
    public static function OnBeforeDelete(\Bitrix\Main\Entity\Event $k4cj6rkan1a3btkqz75ewnel282a3)
    {
        $rz3menzpkkvl3bl0je = new \Bitrix\Main\Entity\EventResult();
        $lc5vzjcu = $k4cj6rkan1a3btkqz75ewnel282a3->getParameter("\151\x64");
        if (!isset($lc5vzjcu["\111\104"])) {
            return $rz3menzpkkvl3bl0je;
        }
        $jc2mhwl2g3w408jvk = $lc5vzjcu["\111\104"];
        if (intval($jc2mhwl2g3w408jvk)) {
            $yq9e9s02233fx2utygsdrttf0s = \Bitrix\Main\Application::getConnection();
            $jknqld9rj3o3jv5u8e297yo2m7k2m0h74 = $yq9e9s02233fx2utygsdrttf0s->getSqlHelper();
            
            $yq9e9s02233fx2utygsdrttf0s->query("\x44\x45\114\x45\124\105\x20\x46\122\x4f\x4d\40\140" . \VKapi\Market\Good\Reference\ExportTable::getTableName() . "\140\x20\x57\110\x45\x52\x45\40\105\130\120\117\x52\x54\x5f\111\x44\x3d" . intval($jc2mhwl2g3w408jvk));
            
            $yq9e9s02233fx2utygsdrttf0s->query("\x44\105\114\105\124\105\x20\106\122\x4f\x4d\40\x60" . \VKapi\Market\Export\LogTable::getTableName() . "\x60\x20\127\x48\105\x52\105\x20\105\130\x50\117\x52\x54\137\x49\x44\75" . intval($jc2mhwl2g3w408jvk));
        }
        return $rz3menzpkkvl3bl0je;
    }
}
?>