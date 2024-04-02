<?php

namespace VKapi\Market\Exception;


class TimeoutException extends \VKapi\Market\Exception\BaseException
{
    public function __construct()
    {
        parent::__construct(\VKapi\Market\Manager::getInstance()->getMessage("\x54\111\115\x45\117\125\124\x5f\105\130\x43\105\x50\124\x49\117\116"), "\124\x49\x4d\x45\117\x55\124\137\x45\130\x43\x45\120\124\111\117\116");
    }
}
?>