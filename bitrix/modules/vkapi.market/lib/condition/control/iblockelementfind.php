<?php

namespace VKapi\Market\Condition\Control;


class IblockElementFind extends \VKapi\Market\Condition\Control\Base
{
    
    public function __construct($v0s6p9xp365, $rb5d8u1yuqkhygcxd8ds2dknnjrp26ps4y = null)
    {
        parent::__construct(array(
            "\x6e\x61\155\x65" => $v0s6p9xp365,
            "\154\x61\142\145\x6c" => $rb5d8u1yuqkhygcxd8ds2dknnjrp26ps4y,
            "\151\142\154\157\143\153\111\144" => 0,
            //инфоблок для ограничения поиска
            "\x73\145\x61\x72\143\150\x58\x6d\x6c\111\144" => false,
        ));
    }
    
    public function setIblockId($aoc34nzah7vc31n0eyg52tyt35gk342ed)
    {
        $this->setParameter("\151\142\x6c\x6f\143\153\x49\x64", intval($aoc34nzah7vc31n0eyg52tyt35gk342ed));
    }
    
    public function setSearchXmlId($s49fx00fme3agdh34ir05tf173pytzay = true)
    {
        $this->setParameter("\163\145\141\162\x63\150\x58\155\x6c\111\144", !!$s49fx00fme3agdh34ir05tf173pytzay);
    }
    
    public static function getComponent()
    {
        return "\x76\x6b\141\160\151\x2d\x6d\x61\162\x6b\145\x74\55\x63\157\x6e\x64\151\164\x69\157\156\x2d\143\x6f\156\164\162\x6f\154\x2d\151\x62\154\157\x63\x6b\x65\x6c\145\x6d\x65\x6e\164\146\x69\156\144";
    }
    
    public function checkValue($lh7ak4ueenq2xqznjy6mld1c6i, $enq2ziha38ir0)
    {
        $v0s6p9xp365 = $this->getParameter("\x6e\x61\x6d\145");
        
        if (!array_key_exists($v0s6p9xp365, $lh7ak4ueenq2xqznjy6mld1c6i)) {
            return false;
        }
        return true;
    }
    
    public function getValue($lh7ak4ueenq2xqznjy6mld1c6i, $enq2ziha38ir0)
    {
        $zslbvdz8c5xio4kaftc = array();
        $zslbvdz8c5xio4kaftc[$this->getParameter("\x6e\141\155\x65")] = $lh7ak4ueenq2xqznjy6mld1c6i[$this->getParameter("\156\141\155\x65")];
        return $zslbvdz8c5xio4kaftc;
    }
}
?>