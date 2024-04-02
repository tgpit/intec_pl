<?php

namespace VKapi\Market\Controller;

use Bitrix\Main\Error;

class Base extends \Bitrix\Main\Engine\Controller
{
    const PERMISSION_WRITE = "\x57";
    const PERMISSION_READ = "\122";
    protected function checkPermission($sg31441bsi7e33z4e)
    {
        $cu737rr7tc3 = self::getApplication()->GetGroupRight("\166\153\x61\x70\x69\x2e\155\x61\x72\153\145\x74") >= $sg31441bsi7e33z4e;
        if (!$cu737rr7tc3) {
            $this->addError(new \Bitrix\Main\Error("\x41\x63\x63\x65\163\163\x20\144\145\x6e\151\145\144"));
        }
        return $cu737rr7tc3;
    }
    protected static function getApplication()
    {
        
        global $APPLICATION;
        return $APPLICATION;
    }
}
?>