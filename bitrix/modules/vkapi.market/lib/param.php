<?php

namespace VKapi\Market;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
final class Param
{
    private static $instance = null;
    private static $params = array();
    private function __construct()
    {
    }
    private function __clone()
    {
    }
    
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            $bu6r112n8q = __CLASS__;
            self::$instance = new $bu6r112n8q();
        }
        return self::$instance;
    }
    public function getTable()
    {
        if (is_null($this->oTable)) {
            $this->oTable = new \VKapi\Market\ParamTable();
        }
        return $this->oTable;
    }
    public function get($d1fi7, $k44vo7tpejbtis4x9wx7r42bhr2fy0ucdz8 = "")
    {
        $d1fi7 = trim($d1fi7);
        if (!isset(self::$params[$d1fi7])) {
            $mekwu180dwog63b0zsdywrxysy1 = $this->getTable()->getList(["\x66\x69\x6c\164\x65\x72" => ["\103\x4f\x44\x45" => $d1fi7]]);
            if ($xspudkepa = $mekwu180dwog63b0zsdywrxysy1->fetch()) {
                self::$params[$d1fi7] = $xspudkepa["\x56\x41\x4c\x55\x45"];
            } else {
                self::$params[$d1fi7] = $k44vo7tpejbtis4x9wx7r42bhr2fy0ucdz8;
            }
        }
        return self::$params[$d1fi7];
    }
    public function set($d1fi7, $mx22vsg2tfd18rde66v6twlm62k)
    {
        $d1fi7 = trim($d1fi7);
        $mx22vsg2tfd18rde66v6twlm62k = trim($mx22vsg2tfd18rde66v6twlm62k);
        $mekwu180dwog63b0zsdywrxysy1 = $this->getTable()->getList(["\x66\x69\154\164\145\x72" => ["\103\117\104\x45" => $d1fi7]]);
        if ($xspudkepa = $mekwu180dwog63b0zsdywrxysy1->fetch()) {
            $this->getTable()->update($d1fi7, ["\x56\101\x4c\x55\x45" => $mx22vsg2tfd18rde66v6twlm62k, "\105\104\111\x54\137\124\x49\x4d\x45" => new \Bitrix\Main\Type\DateTime()]);
        } else {
            $this->getTable()->add(["\103\x4f\104\x45" => $d1fi7, "\126\x41\x4c\x55\105" => trim($mx22vsg2tfd18rde66v6twlm62k), "\105\104\x49\124\137\x54\111\x4d\105" => new \Bitrix\Main\Type\DateTime()]);
        }
        self::$params[$d1fi7] = $mx22vsg2tfd18rde66v6twlm62k;
    }
    
    public function canExecFastAgent()
    {
        $mekwu180dwog63b0zsdywrxysy1 = $this->getTable()->getList(["\x66\151\154\x74\x65\x72" => ["\x43\117\x44\105" => "\x46\x41\x53\124\137\101\x47\105\x4e\x54\137\x45\130\105\x43"]]);
        if ($xspudkepa = $mekwu180dwog63b0zsdywrxysy1->fetch()) {
            if ($xspudkepa["\126\101\114\125\105"] == "\131") {
                
                $ragb3td3gg3 = $xspudkepa["\105\x44\111\x54\x5f\124\111\x4d\x45"];
                if ($ragb3td3gg3->format("\125") <= time() - 3600) {
                    return true;
                }
                return false;
            }
        }
        return true;
    }
}
?>