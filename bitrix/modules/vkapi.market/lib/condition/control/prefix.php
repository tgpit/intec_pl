<?php

namespace VKapi\Market\Condition\Control;

use Bitrix\Main\Localization\Loc;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class Prefix extends \VKapi\Market\Condition\Control\Text
{
    
    public static function getComponent()
    {
        return "\166\x6b\141\x70\x69\x2d\x6d\x61\x72\x6b\x65\164\x2d\143\x6f\x6e\x64\x69\164\x69\x6f\x6e\55\143\157\156\164\162\x6f\x6c\x2d\x70\162\145\146\x69\170";
    }
}
?>