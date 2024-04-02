<?php

namespace VKapi\Market\Condition;

use Bitrix\Main\Localization\Loc;

abstract class Base implements \VKapi\Market\Condition\IBase
{
    
    protected $arParams = array();
    
    public function __construct(array $arParams = array())
    {
        $this->arParams = $arParams;
    }
    
    protected static function getMessage($v0s6p9xp365, $et3pij9za16ska2d0tetvoihtov5fe7nd = array())
    {
        return \Bitrix\Main\Localization\Loc::getMessage("\126\x4b\x41\120\111\56\115\101\122\113\105\x54\x2e\103\117\116\x44\111\124\111\117\116\56" . $v0s6p9xp365, $et3pij9za16ska2d0tetvoihtov5fe7nd);
    }
    
    public function getParams()
    {
        return $this->arParams;
    }
    
    public static final function getType()
    {
        return get_called_class();
    }
    
    public function getInternalConditionById($jc2mhwl2g3w408jvk)
    {
        $iiwjjkrbkdmaygjxb3yjt6fw6q56 = array_filter($this->getInternalConditions(), function ($n1wntz681nnj47j42cuz) use($jc2mhwl2g3w408jvk) {
            return $n1wntz681nnj47j42cuz["\x69\144"] == $jc2mhwl2g3w408jvk;
        });
        if (!empty($iiwjjkrbkdmaygjxb3yjt6fw6q56)) {
            return reset($iiwjjkrbkdmaygjxb3yjt6fw6q56);
        }
        return null;
    }
    
    public final function getJsData()
    {
        $rbwz8q59oihcxpo5dg5 = array();
        
        $o3s2cjoe95dztmel7tp1sbt4frfubc7vx2 = $this->getInternalConditions();
        
        foreach ($o3s2cjoe95dztmel7tp1sbt4frfubc7vx2 as $zc32cy) {
            $qlouj7zk2 = array("\164\x79\160\x65" => static::getType() . "\x3a" . $zc32cy["\151\x64"], "\143\x6f\x6d\160\157\x6e\x65\156\x74" => $zc32cy["\143\157\x6d\160\x6f\x6e\145\x6e\164"], "\x67\162\x6f\x75\160" => $zc32cy["\x67\x72\157\x75\160"], "\x6e\141\x6d\x65" => $zc32cy["\156\x61\x6d\145"], "\x63\157\156\164\x72\x6f\154\163" => array(), "\160\x61\x72\141\155\x73" => array_merge(array("\x5f\166\x65\x72\163\x69\157\156" => 1), isset($zc32cy["\160\141\162\x61\155\x73"]) ? $zc32cy["\160\141\x72\141\155\x73"] : array()), "\155\157\162\145" => array_merge(array("\137\x76\x65\x72\x73\x69\157\x6e" => 1), isset($zc32cy["\155\x6f\x72\145"]) ? $zc32cy["\155\x6f\162\145"] : array()));
            foreach ($zc32cy["\143\157\156\164\x72\x6f\x6c\163"] as $enq2ziha38ir0) {
                if ($enq2ziha38ir0 instanceof \VKapi\Market\Condition\Control\Base) {
                    $qlouj7zk2["\143\157\156\x74\162\x6f\154\163"][] = $enq2ziha38ir0->getJsData();
                }
            }
            $rbwz8q59oihcxpo5dg5[] = $qlouj7zk2;
        }
        return $rbwz8q59oihcxpo5dg5;
    }
    
    public function parse($dmj3ewes8oj7vlbwce3ngkw4nny62jneh, $s1dbp0yd7ti0jrh)
    {
        $zslbvdz8c5xio4kaftc = array();
        $kk6ggohtpi33fpl = $this->getInternalConditionById($dmj3ewes8oj7vlbwce3ngkw4nny62jneh);
        
        if (is_null($kk6ggohtpi33fpl)) {
            return false;
        }
        do {
            $zslbvdz8c5xio4kaftc = array("\x69\x64" => $dmj3ewes8oj7vlbwce3ngkw4nny62jneh, "\x74\171\160\x65" => $this->getType(), "\x63\150\151\x6c\144\163" => array(), "\x76\141\x6c\x75\145\x73" => array());
            if (!empty($kk6ggohtpi33fpl["\x63\x6f\156\164\x72\x6f\154\x73"])) {
                foreach ($kk6ggohtpi33fpl["\143\x6f\x6e\x74\162\x6f\x6c\163"] as $enq2ziha38ir0) {
                    
                    if (!$enq2ziha38ir0->checkValue($s1dbp0yd7ti0jrh, $enq2ziha38ir0)) {
                        return false;
                    } else {
                        $zslbvdz8c5xio4kaftc["\166\141\154\165\x65\x73"] = array_merge($zslbvdz8c5xio4kaftc["\166\141\x6c\165\145\x73"], $enq2ziha38ir0->getValue($s1dbp0yd7ti0jrh, $kk6ggohtpi33fpl));
                    }
                }
            }
            return $zslbvdz8c5xio4kaftc;
        } while (false);
        return false;
    }
    
    public static abstract function getEval($a0dazxatho69j7rlhn);
    
    public static function getEvalForChilds($vhv515r93dgbe3b5d5emeahr8y64vnqq)
    {
        $arResult = array();
        foreach ($vhv515r93dgbe3b5d5emeahr8y64vnqq as $b1s0u2il2juo2d3nz2) {
            if (isset($b1s0u2il2juo2d3nz2["\x69\x64"]) && isset($b1s0u2il2juo2d3nz2["\x74\x79\x70\145"]) && class_exists($b1s0u2il2juo2d3nz2["\164\x79\160\x65"])) {
                
                $lodyoswy = $b1s0u2il2juo2d3nz2["\x74\x79\x70\x65"];
                $i2z7cv5yy55g9qhdet8ya5v3rayyvhl61r = $lodyoswy::getEval($b1s0u2il2juo2d3nz2);
                if ($i2z7cv5yy55g9qhdet8ya5v3rayyvhl61r !== false) {
                    $arResult[] = "\x28" . trim($i2z7cv5yy55g9qhdet8ya5v3rayyvhl61r) . "\x29";
                }
            }
        }
        return $arResult;
    }
    
    public function getPrepiredValuePreview($n1wntz681nnj47j42cuz)
    {
        return $n1wntz681nnj47j42cuz["\x76\141\154\x75\145\x73"];
    }
}
?>