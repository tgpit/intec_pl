<?php

namespace VKapi\Market\Exception;

class BaseException extends \Exception
{
    protected $customCode;
    protected $customData;
    public function __construct($ra2cdo6192cenq514k5a3c7gx, $pcstym1xee8eypq23it05c = "\104\105\106\125\101\x4c\x54", $zbq0lbevmyizuau920gys97brnaouj9q94 = array(), $urcrq3rsgj6v9519 = null)
    {
        parent::__construct($ra2cdo6192cenq514k5a3c7gx, 0, $urcrq3rsgj6v9519);
        $this->customCode = $pcstym1xee8eypq23it05c;
        $this->customData = $zbq0lbevmyizuau920gys97brnaouj9q94;
    }
    public function getCustomCode()
    {
        return $this->customCode;
    }
    public function getCustomData()
    {
        return $this->customData;
    }
    
    public function setCustomDataField($v0s6p9xp365, $mx22vsg2tfd18rde66v6twlm62k)
    {
        $this->customData[$v0s6p9xp365] = $mx22vsg2tfd18rde66v6twlm62k;
    }
}
?>