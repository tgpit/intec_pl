<?php

namespace VKapi\Market\Exception;

class ResponseErrorException extends \VKapi\Market\Exception\BaseException
{
    
    public function __construct($rz3menzpkkvl3bl0je)
    {
        $aa21oy730uixc0un8eju41eqc2wymond = $rz3menzpkkvl3bl0je->getFirstError();
        parent::__construct($aa21oy730uixc0un8eju41eqc2wymond->getMessage(), $aa21oy730uixc0un8eju41eqc2wymond->getCode(), $aa21oy730uixc0un8eju41eqc2wymond->getMore());
    }
}
?>