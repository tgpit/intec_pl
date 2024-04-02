<?php

namespace VKapi\Market\Exception;


class ApiResponseException extends \VKapi\Market\Exception\BaseException
{
    
    protected $oHttpClient = null;
    
    protected $apiCode = 0;
    protected $apiMessage = "";
    public function __construct($rg6bir420crqiiasgr3nu3h2, $twp9cm2 = null)
    {
        $this->apiCode = $rg6bir420crqiiasgr3nu3h2["\145\162\162\x6f\162\137\143\157\x64\145"];
        $this->apiMessage = $rg6bir420crqiiasgr3nu3h2["\x65\162\x72\x6f\162\x5f\x6d\163\147"];
        $this->oHttpClient = $twp9cm2;
        parent::__construct($this->apiCode . "\40" . $this->apiMessage, "\x41\120\111\x5f\122\x45\x53\x50\117\x4e\x53\x45\x5f\x45\x58\x43\x45\120\124\111\x4f\116", $rg6bir420crqiiasgr3nu3h2);
    }
    
    public function is($za4mz2xm80zufo76n7lzflna61sgp5q5w5)
    {
        return $this->apiCode == $za4mz2xm80zufo76n7lzflna61sgp5q5w5;
    }
    
    public function getApiCode()
    {
        return $this->apiCode;
    }
    public function getApiMessage()
    {
        return $this->apiMessage;
    }
}
?>