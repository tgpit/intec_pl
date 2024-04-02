<?php

namespace VKapi\Market\Condition\Control;


class Calendar extends \VKapi\Market\Condition\Control\Input
{
    
    public function __construct($v0s6p9xp365, $rb5d8u1yuqkhygcxd8ds2dknnjrp26ps4y = null)
    {
        parent::__construct($v0s6p9xp365, $rb5d8u1yuqkhygcxd8ds2dknnjrp26ps4y, array("\x73\x68\x6f\x77\124\x69\x6d\x65" => false));
    }
    
    public function setShowTime($s49fx00fme3agdh34ir05tf173pytzay = true)
    {
        $this->setParameter("\x73\x68\157\167\124\151\x6d\145", $s49fx00fme3agdh34ir05tf173pytzay);
    }
    
    public static function getComponent()
    {
        return "\x76\153\141\160\151\55\x6d\x61\x72\x6b\x65\x74\55\143\157\156\144\x69\x74\151\x6f\x6e\55\x63\x6f\x6e\x74\162\x6f\154\55\x63\141\x6c\145\x6e\x64\x61\x72";
    }
    
    public function checkValue($lh7ak4ueenq2xqznjy6mld1c6i, $enq2ziha38ir0)
    {
        $v0s6p9xp365 = $this->getParameter("\156\x61\x6d\x65");
        
        if (!array_key_exists($v0s6p9xp365, $lh7ak4ueenq2xqznjy6mld1c6i)) {
            return false;
        }
        return true;
    }
}
?>