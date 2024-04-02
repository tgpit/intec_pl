<?php

namespace VKapi\Market\Condition\Control;


class Input extends \VKapi\Market\Condition\Control\Base
{
    
    public function __construct($v0s6p9xp365, $rb5d8u1yuqkhygcxd8ds2dknnjrp26ps4y = null, $arParams = array())
    {
        parent::__construct(array_merge($arParams, array("\156\141\155\145" => $v0s6p9xp365, "\154\141\x62\x65\x6c" => $rb5d8u1yuqkhygcxd8ds2dknnjrp26ps4y)));
    }
    
    public static function getComponent()
    {
        return "\166\153\141\160\151\55\x6d\x61\x72\153\145\x74\x2d\x63\157\x6e\x64\x69\x74\151\157\156\55\x63\x6f\x6e\164\162\x6f\x6c\55\151\156\x70\165\164";
    }
    
    public function checkValue($lh7ak4ueenq2xqznjy6mld1c6i, $enq2ziha38ir0)
    {
        $v0s6p9xp365 = $this->getParameter("\156\x61\155\x65");
        
        if (!array_key_exists($v0s6p9xp365, $lh7ak4ueenq2xqznjy6mld1c6i)) {
            return false;
        }
        return true;
    }
    
    public function getValue($lh7ak4ueenq2xqznjy6mld1c6i, $enq2ziha38ir0)
    {
        $zslbvdz8c5xio4kaftc = array();
        $zslbvdz8c5xio4kaftc[$this->getParameter("\156\x61\x6d\145")] = $lh7ak4ueenq2xqznjy6mld1c6i[$this->getParameter("\x6e\x61\155\145")];
        return $zslbvdz8c5xio4kaftc;
    }
}
?>