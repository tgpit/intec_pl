<?php

namespace VKapi\Market;


final class State
{
    
    protected $code = null;
    
    protected $dir = null;
    
    protected $variableName = "\x61\x72\x44\141\x74\x61";
    
    protected $data = null;
    
    public function __construct($d1fi7, $mqgojraz8 = null)
    {
        $mqgojraz8 = trim($mqgojraz8, "\x2f");
        if (empty($mqgojraz8)) {
            $mqgojraz8 = "\x65\x78\x70\x6f\162\x74";
        }
        $this->code = trim($d1fi7);
        $this->dir = "\57" . $mqgojraz8;
    }
    
    public function getVariableName()
    {
        return $this->variableName;
    }
    
    public function get()
    {
        if (is_null($this->data)) {
            $this->data = array();
            try {
                
                if ($this->isExists()) {
                    
                    include $this->getFilename();
                    
                    if (isset(${$this->getVariableName()}) && is_array(${$this->getVariableName()})) {
                        $this->data = ${$this->getVariableName()};
                    }
                }
            } catch (\ParseError $bwcm1vrvt426tee878z0) {
                
                $this->clean();
            }
        }
        return $this->data;
    }
    
    public function getField($v0s6p9xp365)
    {
        $d72p4och4h5ikxh032vvlfi32649wlpkp = $this->get();
        if (array_key_exists($v0s6p9xp365, $d72p4och4h5ikxh032vvlfi32649wlpkp)) {
            return $d72p4och4h5ikxh032vvlfi32649wlpkp[$v0s6p9xp365];
        }
        return null;
    }
    
    public function set($d72p4och4h5ikxh032vvlfi32649wlpkp)
    {
        if (is_null($this->data)) {
            $this->data = array();
        }
        $this->data = array_merge($this->data, $d72p4och4h5ikxh032vvlfi32649wlpkp);
        return $this;
    }
    
    public function setField($v0s6p9xp365, $d72p4och4h5ikxh032vvlfi32649wlpkp)
    {
        if (is_null($this->data)) {
            $this->data = array();
        }
        $this->data[$v0s6p9xp365] = $d72p4och4h5ikxh032vvlfi32649wlpkp;
        return $this;
    }
    
    public function setOnlyKey($d72p4och4h5ikxh032vvlfi32649wlpkp, $lmpnk494tt)
    {
        if (array_key_exists($lmpnk494tt, $d72p4och4h5ikxh032vvlfi32649wlpkp)) {
            $this->setField($lmpnk494tt, $d72p4och4h5ikxh032vvlfi32649wlpkp[$lmpnk494tt]);
        }
        return $this;
    }
    
    public function save()
    {
        if (is_null($this->data)) {
            $this->data = array();
        }
        \Bitrix\Main\IO\File::putFileContents($this->getFilename(), "\x3c" . "\x3f\40\44" . $this->getVariableName() . "\x20\75\40" . var_export($this->data, true) . "\73\x20\77\x3e");
    }
    
    public function clean()
    {
        $this->data = null;
        return \Bitrix\Main\IO\File::deleteFile($this->getFilename());
    }
    
    public function cleanDir()
    {
        \Bitrix\Main\IO\Directory::deleteDirectory($this->getDirectory());
    }
    
    public function isExists()
    {
        return file_exists($this->getFilename());
    }
    
    public function getBaseDirectory()
    {
        return \Bitrix\Main\Application::getDocumentRoot() . "\x2f\x75\x70\154\157\141\x64\57\166\x6b\141\160\x69\56\155\x61\x72\x6b\145\164\x2f\163\x74\x61\164\145";
    }
    
    public function getDirectory()
    {
        try {
            return \Bitrix\Main\IO\Path::normalize($this->getBaseDirectory() . $this->dir);
        } catch (\Exception $bwcm1vrvt426tee878z0) {
            return $this->getBaseDirectory();
        }
    }
    
    public function getFilename()
    {
        return $this->getDirectory() . "\x2f" . $this->code . "\56\x70\150\160";
    }
    
    public function calcPercentByData($d72p4och4h5ikxh032vvlfi32649wlpkp)
    {
        $n9y3bgnp6 = 0;
        if (isset($d72p4och4h5ikxh032vvlfi32649wlpkp["\x73\164\x65\x70\x73"])) {
            $n9y3bgnp6 = floor(array_sum(array_column($d72p4och4h5ikxh032vvlfi32649wlpkp["\x73\x74\145\160\163"], "\160\145\162\143\x65\156\x74")) / count($d72p4och4h5ikxh032vvlfi32649wlpkp["\163\x74\145\x70\163"]));
        } else {
            $n9y3bgnp6 = $this->calcPercent($d72p4och4h5ikxh032vvlfi32649wlpkp["\143\157\165\156\164"], $d72p4och4h5ikxh032vvlfi32649wlpkp["\x6f\x66\x66\163\145\x74"]);
        }
        return $n9y3bgnp6;
    }
    
    public function calcPercent($ma8in5gmfb63pv3, $d2udzte1k835qz0tfbmlkmil5kz7k)
    {
        if ($ma8in5gmfb63pv3 <= 0) {
            return 100;
        }
        if ($d2udzte1k835qz0tfbmlkmil5kz7k <= 0) {
            return 0;
        }
        $d2udzte1k835qz0tfbmlkmil5kz7k = min($d2udzte1k835qz0tfbmlkmil5kz7k, $ma8in5gmfb63pv3);
        $n9y3bgnp6 = floor($d2udzte1k835qz0tfbmlkmil5kz7k * 100 / $ma8in5gmfb63pv3);
        return max(min($n9y3bgnp6, 100), 0);
    }
}
?>