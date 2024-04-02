<?php

namespace VKapi\Market\Condition\Control;


class Select extends \VKapi\Market\Condition\Control\Base
{
    
    public function __construct($v0s6p9xp365, $vplekvu = array(), $uxh51yxxinqocxu5s2h9 = null, $rqn79xmrgrsr508hgwi = false)
    {
        if (is_null($uxh51yxxinqocxu5s2h9)) {
            $gfp22mufg12zkv3i8i5npeukxk = array_keys($vplekvu);
            $uxh51yxxinqocxu5s2h9 = reset($gfp22mufg12zkv3i8i5npeukxk);
        }
        if ($rqn79xmrgrsr508hgwi === true) {
            $rqn79xmrgrsr508hgwi = "\x2e\x2e\x2e";
        }
        parent::__construct(array(
            "\156\x61\x6d\145" => $v0s6p9xp365,
            "\x76\x61\x6c\165\x65\163" => self::prepareValues($vplekvu),
            "\x76\x61\154\x75\x65" => $uxh51yxxinqocxu5s2h9,
            "\146\x69\162\x73\164\x45\155\x70\164\x79" => $rqn79xmrgrsr508hgwi,
            "\145\156\x61\142\x6c\145\144\123\145\x61\x72\143\x68" => false,
            // поиск выклчюен
            "\x61\x6a\141\x78\x56\x61\154\165\145\x73\x55\162\x6c" => false,
        ));
    }
    
    public function enableSearch()
    {
        $this->setParameter("\145\156\x61\x62\x6c\x65\x64\x53\145\141\x72\x63\150", true);
        return $this;
    }
    
    public function setAjaxValues($fztwt68ui6gjg6g6azttodg)
    {
        $this->setParameter("\x61\x6a\x61\x78\x56\141\154\x75\145\x73\x55\162\x6c", $fztwt68ui6gjg6g6azttodg);
        return $this;
    }
    
    public static function getComponent()
    {
        return "\166\153\141\x70\151\x2d\155\x61\162\x6b\145\164\55\143\157\156\144\x69\164\x69\x6f\156\55\x63\x6f\156\x74\x72\x6f\x6c\55\163\145\x6c\x65\x63\164";
    }
    
    public static function prepareValues($kjtjyb8j24y36vehs0892)
    {
        $zslbvdz8c5xio4kaftc = array();
        foreach ($kjtjyb8j24y36vehs0892 as $lmpnk494tt => $mx22vsg2tfd18rde66v6twlm62k) {
            $zslbvdz8c5xio4kaftc[] = array("\x69\x64" => $lmpnk494tt, "\x6e\141\155\145" => $mx22vsg2tfd18rde66v6twlm62k);
        }
        return $zslbvdz8c5xio4kaftc;
    }
    
    public function checkValue($lh7ak4ueenq2xqznjy6mld1c6i, $enq2ziha38ir0)
    {
        $v0s6p9xp365 = $this->getParameter("\x6e\x61\155\x65");
        $vplekvu = $this->getParameter("\x76\x61\x6c\x75\x65\x73");
        
        if (!array_key_exists($v0s6p9xp365, $lh7ak4ueenq2xqznjy6mld1c6i)) {
            return false;
        }
        
        $mx22vsg2tfd18rde66v6twlm62k = $lh7ak4ueenq2xqznjy6mld1c6i[$v0s6p9xp365];
        $nsk1yvashlz2jb70r46vr73pzn1wuz = array_filter($vplekvu, function ($n1wntz681nnj47j42cuz) use($mx22vsg2tfd18rde66v6twlm62k) {
            return $n1wntz681nnj47j42cuz["\x69\x64"] == $mx22vsg2tfd18rde66v6twlm62k;
        });
        if (empty($nsk1yvashlz2jb70r46vr73pzn1wuz)) {
            return false;
        }
        return true;
    }
    
    public function getValue($lh7ak4ueenq2xqznjy6mld1c6i, $enq2ziha38ir0)
    {
        $zslbvdz8c5xio4kaftc = array();
        $zslbvdz8c5xio4kaftc[$this->getParameter("\x6e\141\x6d\x65")] = $lh7ak4ueenq2xqznjy6mld1c6i[$this->getParameter("\156\x61\x6d\x65")];
        return $zslbvdz8c5xio4kaftc;
    }
}
?>