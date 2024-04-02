<?php

namespace VKapi\Market\Exception;

class ORMException extends \VKapi\Market\Exception\BaseException
{
    
    public function __construct($kjhq0dk0v44kgxtnwp8crhzs236a4sqtcl)
    {
        
        $kjhq0dk0v44kgxtnwp8crhzs236a4sqtcl->getErrorCollection()->rewind();
        $t3hocufreqvkcv = $kjhq0dk0v44kgxtnwp8crhzs236a4sqtcl->getErrorCollection()->current();
        parent::__construct($t3hocufreqvkcv->getMessage(), "\105\x52\x52\117\122\x5f\x4f\122\115", $t3hocufreqvkcv->getCustomData());
    }
}
?>