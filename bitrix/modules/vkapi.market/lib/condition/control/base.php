<?php

namespace VKapi\Market\Condition\Control;

use Bitrix\Main\Localization\Loc;

abstract class Base implements \VKapi\Market\Condition\Control\IBase
{
    
    protected $arParams = array();
    
    public function __construct(array $arParams)
    {
        $hfzrbkyr3qsr3pp87wxw0 = array("\x6e\x61\155\x65" => "", "\x76\x61\x6c\x75\145\x73" => array(), "\166\141\154\x75\145" => "");
        $this->arParams = array_merge($hfzrbkyr3qsr3pp87wxw0, $arParams);
    }
    
    public static function getMessage($v0s6p9xp365, $et3pij9za16ska2d0tetvoihtov5fe7nd = array())
    {
        return \Bitrix\Main\Localization\Loc::getMessage("\x56\113\101\120\x49\x2e\x4d\101\122\113\x45\124\56\x43\117\x4e\x44\111\124\x49\117\x4e\x2e\x43\x4f\116\124\x52\x4f\x4c\x2e" . $v0s6p9xp365, $et3pij9za16ska2d0tetvoihtov5fe7nd);
    }
    
    public function getParams()
    {
        return $this->arParams;
    }
    
    public function getParameter($v0s6p9xp365)
    {
        if (array_key_exists($v0s6p9xp365, $this->arParams)) {
            return $this->arParams[$v0s6p9xp365];
        }
        return null;
    }
    
    public function setParameter($v0s6p9xp365, $mx22vsg2tfd18rde66v6twlm62k)
    {
        $this->arParams[$v0s6p9xp365] = $mx22vsg2tfd18rde66v6twlm62k;
        return $this;
    }
    public static final function getType()
    {
        return get_called_class();
    }
    
    public final function getJsData()
    {
        return array("\143\x6f\x6d\x70\x6f\x6e\145\x6e\164" => static::getComponent(), "\164\x79\160\145" => static::getType(), "\160\x61\162\x61\x6d\x73" => $this->getParams());
    }
}
?>