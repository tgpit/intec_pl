<?php

namespace VKapi\Market\Condition\Control;

use Bitrix\Main\Loader;

class SelectHighloadBlock extends \VKapi\Market\Condition\Control\Select
{
    
    public function __construct($v0s6p9xp365, $a2xjli0ftadna41hzch4)
    {
        parent::__construct($v0s6p9xp365);
        $this->setParameter("\x48\111\x47\x48\x4c\117\101\104\102\114\117\x43\113\137\x54\x41\102\114\105", $a2xjli0ftadna41hzch4);
    }
    
    public function checkValue($lh7ak4ueenq2xqznjy6mld1c6i, $enq2ziha38ir0)
    {
        $v0s6p9xp365 = $this->getParameter("\156\x61\155\145");
        
        if (!array_key_exists($v0s6p9xp365, $lh7ak4ueenq2xqznjy6mld1c6i)) {
            return false;
        }
        try {
            $a2xjli0ftadna41hzch4 = $enq2ziha38ir0->getParameter("\110\x49\x47\x48\x4c\x4f\101\104\102\x4c\117\103\113\x5f\124\101\x42\114\105");
            
            $dedlah3web0z2c8iq2jo0acjg2814a = \VKapi\Market\Manager::getInstance()->getHighloadBlockClassByTableName($a2xjli0ftadna41hzch4);
            if (!is_null($dedlah3web0z2c8iq2jo0acjg2814a)) {
                $c3y3e2rdi1f5dac2tnc97hfn8yv1yc2rp8 = $dedlah3web0z2c8iq2jo0acjg2814a::getEntity();
                $rgwgf81gi2 = ["\x49\x44" => (string) $lh7ak4ueenq2xqznjy6mld1c6i[$v0s6p9xp365]];
                if ($c3y3e2rdi1f5dac2tnc97hfn8yv1yc2rp8->hasField("\x55\x46\x5f\130\115\114\x5f\x49\104")) {
                    $rgwgf81gi2 = ["\125\x46\x5f\x58\115\x4c\x5f\111\104" => (string) $lh7ak4ueenq2xqznjy6mld1c6i[$v0s6p9xp365]];
                }
                $mekwu180dwog63b0zsdywrxysy1 = $dedlah3web0z2c8iq2jo0acjg2814a::getList(["\x6c\x69\155\151\164" => 1, "\146\151\x6c\164\x65\162" => $rgwgf81gi2]);
                if ($mekwu180dwog63b0zsdywrxysy1->fetch()) {
                    return true;
                }
            }
        } catch (\Exception $bwcm1vrvt426tee878z0) {
            
        }
        return false;
    }
}
?>