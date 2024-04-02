<?php

namespace VKapi\Market\Condition\Control;


class IblockSectionFind extends \VKapi\Market\Condition\Control\Base
{
    
    public function __construct($v0s6p9xp365, $rb5d8u1yuqkhygcxd8ds2dknnjrp26ps4y = null, $aoc34nzah7vc31n0eyg52tyt35gk342ed = null)
    {
        parent::__construct(array("\156\x61\155\145" => $v0s6p9xp365, "\x6c\x61\142\145\x6c" => $rb5d8u1yuqkhygcxd8ds2dknnjrp26ps4y, "\151\142\154\157\143\x6b\111\144" => intval($aoc34nzah7vc31n0eyg52tyt35gk342ed)));
    }
    
    public static function getComponent()
    {
        return "\x76\x6b\x61\160\151\55\155\x61\162\x6b\145\x74\55\143\157\x6e\144\151\x74\151\x6f\156\x2d\x63\x6f\156\164\x72\157\154\55\x69\142\x6c\157\x63\x6b\163\x65\x63\164\151\157\x6e\146\x69\x6e\x64";
    }
    
    public function checkValue($lh7ak4ueenq2xqznjy6mld1c6i, $enq2ziha38ir0)
    {
        $v0s6p9xp365 = $this->getParameter("\x6e\141\155\x65");
        
        if (!array_key_exists($v0s6p9xp365, $lh7ak4ueenq2xqznjy6mld1c6i)) {
            return false;
        }
        return true;
    }
    
    public function getValue($lh7ak4ueenq2xqznjy6mld1c6i, $enq2ziha38ir0)
    {
        $zslbvdz8c5xio4kaftc = array();
        $zslbvdz8c5xio4kaftc[$this->getParameter("\x6e\141\x6d\145")] = $lh7ak4ueenq2xqznjy6mld1c6i[$this->getParameter("\x6e\x61\x6d\x65")];
        return $zslbvdz8c5xio4kaftc;
    }
}
?>