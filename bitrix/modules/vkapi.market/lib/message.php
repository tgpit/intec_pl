<?php

namespace VKapi\Market;

use Bitrix\Main\Localization\Loc;

class Message
{
    protected $moduleId = "";
    protected $block = null;
    
    public function __construct($k4rqalzpxap4k0y, $ar3lknmres = "")
    {
        $this->moduleId = strtoupper($k4rqalzpxap4k0y);
        $this->block = $ar3lknmres;
    }
    
    public function get($d1fi7, $et3pij9za16ska2d0tetvoihtov5fe7nd = array())
    {
        return \Bitrix\Main\Localization\Loc::getMessage($this->moduleId . "\x2e" . $this->block . "\x2e" . $d1fi7, $et3pij9za16ska2d0tetvoihtov5fe7nd);
    }
}
?>