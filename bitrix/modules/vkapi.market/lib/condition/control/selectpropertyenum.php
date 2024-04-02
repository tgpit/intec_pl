<?php

namespace VKapi\Market\Condition\Control;

use Bitrix\Main\Loader;

class SelectPropertyEnum extends \VKapi\Market\Condition\Control\Select
{
    
    public function __construct($v0s6p9xp365, $vdvpj0jxn6ovz440bcdshups2mgo9twct)
    {
        parent::__construct($v0s6p9xp365);
        $this->setParameter("\x50\122\117\120\105\x52\124\131\x5f\x49\104", $vdvpj0jxn6ovz440bcdshups2mgo9twct);
    }
    
    public function checkValue($lh7ak4ueenq2xqznjy6mld1c6i, $enq2ziha38ir0)
    {
        $v0s6p9xp365 = $this->getParameter("\156\141\x6d\145");
        $vplekvu = $this->getParameter("\166\141\154\x75\x65\x73");
        
        if (!array_key_exists($v0s6p9xp365, $lh7ak4ueenq2xqznjy6mld1c6i) || !\VKapi\Market\Condition\Manager::isInstalledIblockModule()) {
            return false;
        }
        
        $mx22vsg2tfd18rde66v6twlm62k = $lh7ak4ueenq2xqznjy6mld1c6i[$v0s6p9xp365];
        $mekwu180dwog63b0zsdywrxysy1 = \CIBlockPropertyEnum::GetList(array(), array("\120\x52\117\120\105\122\124\x59\137\x49\x44" => intval($enq2ziha38ir0->getParameter("\x50\x52\117\x50\x45\122\x54\131\137\x49\x44")), "\x49\x44" => intval($mx22vsg2tfd18rde66v6twlm62k)));
        if ($mekwu180dwog63b0zsdywrxysy1->fetch()) {
            return true;
        }
        return false;
    }
}
?>