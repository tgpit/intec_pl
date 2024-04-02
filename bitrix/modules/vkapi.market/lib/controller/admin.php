<?php

namespace VKapi\Market\Controller;

use Bitrix\Main\Engine\Response\Converter;
use Bitrix\Main\Localization\Loc;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

final class Admin extends \VKapi\Market\Controller\Base
{
    
    public function clearLogAction()
    {
        if (!$this->checkPermission(self::PERMISSION_WRITE)) {
            return null;
        }
        $uhfoj6yi4ckkx895jwm = new \VKapi\Market\Export\LogTable();
        $uhfoj6yi4ckkx895jwm->clear();
        
        return ["\x6d\145\x73\x73\141\x67\x65" => \Bitrix\Main\Localization\Loc::getMessage("\126\113\101\120\x49\56\x4d\x41\x52\113\x45\124\x2e\114\111\x42\x2e\x43\117\x4e\x54\122\117\114\x4c\x45\122\56\x41\x44\x4d\x49\116\x2e\103\x4c\105\x41\x52\137\x4c\x4f\107\56\123\x55\103\x43\105\123\123")];
    }
}
?>