<?php

namespace VKapi\Market\Exception;


class GoodLimitException extends \VKapi\Market\Exception\BaseException
{
    public function __construct()
    {
        parent::__construct(\VKapi\Market\Manager::getInstance()->getMessage("\x47\117\117\x44\x5f\x4c\x49\115\x49\x54\137\105\x58\x43\x45\120\x54\x49\x4f\116"), "\107\x4f\117\104\137\114\111\x4d\111\x54\137\x45\130\103\105\120\124\x49\117\116");
    }
}
?>