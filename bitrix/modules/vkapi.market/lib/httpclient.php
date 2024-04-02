<?php

namespace VKapi\Market;

use VKapi\Market\Exception\BaseException;

class HttpClient
{
    const HTTP_1_0 = "\61\56\x30";
    const HTTP_1_1 = "\x31\56\x31";
    const HTTP_POST = "\120\x4f\123\124";
    const HTTP_GET = "\107\x45\124";
    protected $result = null;
    protected $status = null;
    protected $proxyHost = null;
    protected $proxyPort = null;
    protected $proxyUser = null;
    protected $proxyPass = null;
    protected $timeout = 45;
    protected $maxRedirect = 5;
    protected $httpProtocol = 1.0;
    protected $requestFullUri = false;
    protected $arRequestHeaders = [];
    protected $arResponseHeaders = [];
    protected $arResponseCookies = [];
    protected $stream = null;
    public function __construct()
    {
    }
    public function getVar($lv6dt)
    {
        return $this->{$lv6dt};
    }
    
    public function clearResponse()
    {
        $this->result = null;
        $this->status = null;
        $this->stream = null;
        $this->arRequestHeaders = [];
        $this->arResponseCookies = [];
    }
    public function setProxy($w3ckgn4qwc1vvgvaxctnjid9ot0a, $vj1d7e2clfjqzhvn1832li, $pdvg1ta11jmh = null, $hrv5r1x37snsqacrx8ww3kyxjaj73uk0gs = null)
    {
        $this->proxyHost = $w3ckgn4qwc1vvgvaxctnjid9ot0a;
        $this->proxyPort = $vj1d7e2clfjqzhvn1832li;
        $this->proxyUser = $pdvg1ta11jmh;
        $this->proxyPass = $hrv5r1x37snsqacrx8ww3kyxjaj73uk0gs;
    }
    
    public function setTimeout($lujfxvuq45jp1s6524dza3bp60oztqd0rjd)
    {
        $this->timeout = $lujfxvuq45jp1s6524dza3bp60oztqd0rjd;
    }
    
    public function setMaxRedirect($hput1w7768awsfdx064z1x0)
    {
        $this->maxRedirect = $hput1w7768awsfdx064z1x0;
    }
    
    public function setVersion($g2sgbzg0prbi8dtnxe8u8hjii)
    {
        if ($g2sgbzg0prbi8dtnxe8u8hjii == self::HTTP_1_1) {
            $this->httpProtocol = self::HTTP_1_1;
        } else {
            $this->httpProtocol = self::HTTP_1_0;
        }
    }
    
    public function setRequestFullUrl($jdvqq87wv1n3rv)
    {
        $this->requestFullUri = $jdvqq87wv1n3rv;
    }
    
    public function addHeader($lv6dt, $xtvgb722ccq9wb)
    {
        $this->arRequestHeaders[$lv6dt] = $xtvgb722ccq9wb;
    }
    
    public function getStreamContextOptions()
    {
        $zbwyzm2x5e4lvxobr = ["\x68\164\x74\160" => [
            "\165\x73\x65\x72\137\x61\x67\145\x6e\x74" => "\115\157\172\x69\154\154\x61\x2f\65\56\60\x20\50\130\61\61\73\40\x4c\151\156\x75\x78\x20\x78\70\x36\137\66\x34\51\x20\101\160\x70\154\145\127\145\x62\113\151\164\x2f\65\x33\x37\56\x33\x36\40\50\x4b\110\124\x4d\114\54\40\x6c\x69\153\145\x20\x47\145\x63\x6b\x6f\x29\x20\103\150\162\x6f\x6d\145\57\x37\64\x2e\x30\56\63\67\62\x39\x2e\x31\x33\x31\40\x53\x61\146\141\x72\151\x2f\x35\63\67\x2e\63\66",
            "\x72\x65\x71\165\145\x73\164\x5f\x66\x75\154\154\x75\162\x69" => $this->requestFullUri,
            "\164\x69\155\145\x6f\165\164" => $this->timeout,
            "\x66\157\154\x6c\157\x77\137\154\157\x63\141\x74\x69\157\156" => $this->maxRedirect > 0 ? 1 : 0,
            "\x6d\x61\170\x5f\162\x65\144\151\x72\x65\143\164\x73" => $this->maxRedirect,
            //Извлечь содержимое даже при неуспешных статусах завершения.
            "\151\147\156\157\162\x65\x5f\145\162\x72\x6f\162\x73" => "\61",
            "\160\x72\x6f\164\157\x63\157\154\x5f\166\145\x72\x73\x69\157\x6e" => $this->httpProtocol,
        ], "\163\x73\x6c" => ["\x76\x65\x72\151\x66\171\137\160\x65\145\162\137\156\141\155\145" => false, "\x76\x65\162\x69\x66\171\x5f\x70\x65\x65\x72" => false, "\123\x4e\x49\x5f\145\x6e\x61\x62\154\x65\x64" => false, "\x64\151\163\141\142\x6c\145\137\143\x6f\155\160\162\x65\x73\163\x69\157\156" => true]];
        if (!is_null($this->proxyHost)) {
            $zbwyzm2x5e4lvxobr["\150\x74\164\160"]["\160\x72\157\x78\171"] = "\164\143\x70\72\x2f\57" . $this->proxyHost . "\72" . $this->proxyPort;
        }
        if (!is_null($this->proxyUser)) {
            $zbwyzm2x5e4lvxobr["\x68\164\164\160"]["\101\165\x74\150\157\162\x69\172\141\164\151\157\x6e"] = "\x42\x61\x73\x69\x63\x20" . base64_encode($this->proxyUser . "\72" . $this->proxyPass);
        }
        return $zbwyzm2x5e4lvxobr;
    }
    
    public function get($e7twpo7, $bkayxi00naudp154xlzsbm0sxetnn5c8h5i = [])
    {
        $this->clearResponse();
        $imtyzastjx78a0 = ["\122\x65\x71\165\x65\163\164\125\122\x4c" => "", "\122\x65\x71\165\x65\163\x74\x4d\145\x74\x68\157\x64" => "\x47\x45\124", "\123\164\x61\x74\165\163\x43\x6f\144\145" => "", "\x52\145\x73\160\157\x6e\163\145\110\x65\141\x64\145\x72\163" => "", "\x52\x65\x73\x70\x6f\156\x73\x65\102\157\144\171" => ""];
        $o6chhqwq = $e7twpo7 . "\77" . http_build_query($bkayxi00naudp154xlzsbm0sxetnn5c8h5i);
        $ia2gwjkq68ixjak5c = new \Bitrix\Main\Web\Uri($o6chhqwq);
        $zbwyzm2x5e4lvxobr = $this->getStreamContextOptions();
        $zbwyzm2x5e4lvxobr["\150\164\x74\160"]["\x6d\x65\164\150\157\144"] = self::HTTP_GET;
        $zbwyzm2x5e4lvxobr["\150\164\164\x70"]["\x68\145\141\144\x65\x72"] = $this->getRequestHeaders();
        $nir5mxnrj2kho7czsk0 = stream_context_create($zbwyzm2x5e4lvxobr);
        $this->stream = fopen($ia2gwjkq68ixjak5c->getUri(), "\162", false, $nir5mxnrj2kho7czsk0);
        if (!is_resource($this->stream)) {
            throw new \VKapi\Market\Exception\BaseException("\105\x72\162\157\162\x20\157\x70\x65\156\40\x63\x6f\156\x6e\x65\143\x74\x20\164\x6f\x20\x2d\40" . $ia2gwjkq68ixjak5c->getUri() . "\56\x20\x43\x68\x65\143\153\x20\160\150\160\x2e\x69\x6e\151\x2c\40\x6e\x65\x65\144\72\40\x61\x6c\x6c\157\167\137\x75\x72\x6c\137\146\157\x70\x65\156\40\75\40\117\x6e", "\105\122\x52\117\122\137\x43\117\116\x4e\105\103\124\137\x46\117\120\105\116", ["\143\157\x6e\164\x65\170\x74\137\157\x70\x74\x69\157\156\163" => $zbwyzm2x5e4lvxobr]);
        }
        $imtyzastjx78a0["\122\x65\x71\165\x65\163\164\x55\x52\x4c"] = $o6chhqwq;
        
        $this->parseResponseHeaders();
        
        $this->result = stream_get_contents($this->stream);
        fclose($this->stream);
        $this->stream = null;
        return $this->result;
    }
    
    public function post($e7twpo7, $bkayxi00naudp154xlzsbm0sxetnn5c8h5i = [], $mwjd72cryr3nz5npm425wapc = [])
    {
        $this->clearResponse();
        $imtyzastjx78a0 = ["\122\x65\161\165\x65\x73\164\125\122\114" => "", "\x52\x65\x71\165\145\x73\164\115\x65\x74\150\157\144" => "\120\117\123\124", "\x52\145\161\165\145\163\x74\x48\x65\x61\144\x65\x72\163" => "", "\x52\x65\x71\x75\x65\x73\164\102\x6f\x64\x79" => "", "\123\164\141\164\165\163\103\x6f\x64\145" => "", "\x52\145\x73\160\x6f\156\163\145\110\x65\x61\144\x65\x72\x73" => "", "\x52\145\x73\x70\157\x6e\x73\145\102\x6f\x64\171" => ""];
        $ia2gwjkq68ixjak5c = new \Bitrix\Main\Web\Uri($e7twpo7);
        $wont2xp3lht2lizw3ny38tdpv = md5(uniqid(time()) . time());
        $xf0poshakhgvo9e8s = "";
        $urqevxc29xnql1w7 = "";
        
        if (!empty($mwjd72cryr3nz5npm425wapc)) {
            $this->addHeader("\x43\x6f\x6e\164\x65\156\x74\x2d\124\171\x70\145", "\x6d\165\x6c\x74\151\160\141\x72\164\x2f\x66\x6f\x72\x6d\55\x64\141\164\141\73\40\x62\x6f\165\156\x64\x61\162\x79\75" . $wont2xp3lht2lizw3ny38tdpv);
            $hput1w7768awsfdx064z1x0 = 0;
            foreach ($bkayxi00naudp154xlzsbm0sxetnn5c8h5i as $ik9133o9enfyn0phg1ej7s7bp24dh5q97 => $qb83i1kbqzqj85epqumc9j21pwk9b490) {
                $qb83i1kbqzqj85epqumc9j21pwk9b490 = urlencode($qb83i1kbqzqj85epqumc9j21pwk9b490);
                $xf0poshakhgvo9e8s .= "\x2d\x2d" . $wont2xp3lht2lizw3ny38tdpv . "\xd\12";
                $xf0poshakhgvo9e8s .= "\x43\x6f\156\164\x65\x6e\164\55\x44\151\163\160\157\163\151\x74\x69\157\156\x3a\x20\146\157\x72\155\55\x64\x61\164\x61\x3b\x20\x6e\141\x6d\145\x3d\x22" . $ik9133o9enfyn0phg1ej7s7bp24dh5q97 . "\x22" . "\15\12\xd\12" . $qb83i1kbqzqj85epqumc9j21pwk9b490 . "\15\12";
                $urqevxc29xnql1w7 .= "\x2d\x2d" . $wont2xp3lht2lizw3ny38tdpv . "\xd\xa";
                $urqevxc29xnql1w7 .= "\x43\x6f\x6e\164\x65\156\164\55\x44\151\x73\160\157\163\x69\164\x69\x6f\156\x3a\40\x66\x6f\162\x6d\55\144\141\164\x61\73\x20\156\141\155\145\75\42" . $ik9133o9enfyn0phg1ej7s7bp24dh5q97 . "\42" . "\15\12\xd\xa" . $qb83i1kbqzqj85epqumc9j21pwk9b490 . "\xd\xa";
                $hput1w7768awsfdx064z1x0++;
            }
            foreach ($mwjd72cryr3nz5npm425wapc as $ik9133o9enfyn0phg1ej7s7bp24dh5q97 => $iwfxuo5ticqia43x7lmqcgwinbd5vy2ov) {
                $gki79tlxyqjv515mcr8i = new \Bitrix\Main\IO\File($iwfxuo5ticqia43x7lmqcgwinbd5vy2ov);
                if ($gki79tlxyqjv515mcr8i->isExists()) {
                    $xf0poshakhgvo9e8s .= "\55\x2d" . $wont2xp3lht2lizw3ny38tdpv . "\15\xa";
                    $xf0poshakhgvo9e8s .= "\103\x6f\x6e\x74\x65\156\x74\x2d\x44\151\x73\x70\x6f\163\151\x74\151\157\156\x3a\x20\x66\x6f\x72\155\x2d\x64\141\x74\x61\x3b\x20\156\x61\155\145\75\x22" . $ik9133o9enfyn0phg1ej7s7bp24dh5q97 . "\x22\x3b\x20\x66\x69\154\x65\x6e\x61\x6d\145\x3d\42" . $gki79tlxyqjv515mcr8i->getName() . "\42" . "\15\12";
                    $xf0poshakhgvo9e8s .= "\x43\157\x6e\x74\145\x6e\x74\55\124\x79\160\145\x3a\40" . $this->getFileMimeType($gki79tlxyqjv515mcr8i->getPath()) . "\15\xa";
                    $xf0poshakhgvo9e8s .= "\x43\x6f\x6e\164\145\156\164\55\x54\162\141\x6e\x73\x66\145\162\55\x45\x6e\x63\x6f\144\151\x6e\147\x3a\x20\142\x69\x6e\x61\x72\171" . "\xd\xa\15\xa";
                    $xf0poshakhgvo9e8s .= file_get_contents($gki79tlxyqjv515mcr8i->getPath()) . "\xd\12";
                    $urqevxc29xnql1w7 .= "\x2d\55" . $wont2xp3lht2lizw3ny38tdpv . "\15\xa";
                    $urqevxc29xnql1w7 .= "\x43\x6f\156\x74\145\156\164\x2d\104\151\163\160\157\x73\x69\x74\151\157\x6e\72\40\146\x6f\x72\x6d\55\144\141\164\x61\x3b\40\156\141\x6d\145\x3d\42" . $ik9133o9enfyn0phg1ej7s7bp24dh5q97 . "\42\73\40\x66\151\x6c\145\x6e\x61\155\145\75\x22" . $gki79tlxyqjv515mcr8i->getName() . "\42" . "\15\12";
                    $urqevxc29xnql1w7 .= "\103\157\156\164\x65\x6e\164\55\x54\171\x70\x65\x3a\40" . $this->getFileMimeType($gki79tlxyqjv515mcr8i->getPath()) . "\15\12";
                    $urqevxc29xnql1w7 .= "\x43\157\x6e\164\145\156\x74\55\124\x72\141\156\x73\x66\x65\162\55\x45\x6e\143\x6f\x64\x69\x6e\x67\72\40\142\151\x6e\141\x72\x79" . "\15\xa\15\12";
                    $urqevxc29xnql1w7 .= "\52\52\x2a\x2a\x20\x66\x69\154\145\x20\x63\157\x6e\x74\145\x6e\164\x20\x2a\x2a\x2a\52\xd\xa";
                    $hput1w7768awsfdx064z1x0++;
                }
            }
            if ($hput1w7768awsfdx064z1x0) {
                $xf0poshakhgvo9e8s .= "\55\55" . $wont2xp3lht2lizw3ny38tdpv . "\55\55" . "\xd\xa";
                $urqevxc29xnql1w7 .= "\x2d\55" . $wont2xp3lht2lizw3ny38tdpv . "\x2d\x2d" . "\15\12";
            }
        } else {
            $this->addHeader("\x43\157\156\164\x65\156\x74\x2d\x54\x79\160\x65", "\141\160\160\154\151\x63\x61\164\x69\157\x6e\x2f\170\55\167\x77\x77\55\146\157\x72\x6d\x2d\165\x72\x6c\145\156\x63\157\144\x65\x64");
            $xf0poshakhgvo9e8s = http_build_query($bkayxi00naudp154xlzsbm0sxetnn5c8h5i);
            foreach ($bkayxi00naudp154xlzsbm0sxetnn5c8h5i as $d7f7t6ziajf21p9p0im2k => $xtvgb722ccq9wb) {
                $urqevxc29xnql1w7 .= $d7f7t6ziajf21p9p0im2k . "\x3d" . $xtvgb722ccq9wb . PHP_EOL;
            }
        }
        $zbwyzm2x5e4lvxobr = $this->getStreamContextOptions();
        $zbwyzm2x5e4lvxobr["\150\x74\164\160"]["\x6d\145\164\x68\x6f\x64"] = self::HTTP_POST;
        $zbwyzm2x5e4lvxobr["\x68\164\164\x70"]["\150\x65\141\144\x65\162"] = $this->getRequestHeaders();
        $zbwyzm2x5e4lvxobr["\x68\164\x74\160"]["\143\x6f\x6e\x74\145\x6e\164"] = $xf0poshakhgvo9e8s;
        
        $imtyzastjx78a0["\x52\145\161\x75\x65\x73\164\125\122\x4c"] = $ia2gwjkq68ixjak5c->getUri();
        $imtyzastjx78a0["\x52\x65\161\x75\145\163\x74\x48\145\141\x64\x65\x72\163"] = $this->getRequestHeaders();
        $imtyzastjx78a0["\x52\145\x71\x75\x65\x73\164\x42\157\144\171"] = $urqevxc29xnql1w7;
        $nir5mxnrj2kho7czsk0 = stream_context_create($zbwyzm2x5e4lvxobr);
        $this->stream = fopen($ia2gwjkq68ixjak5c->getUri(), "\162", false, $nir5mxnrj2kho7czsk0);
        if (!is_resource($this->stream)) {
            throw new \VKapi\Market\Exception\BaseException("\x45\x72\x72\157\x72\x20\157\160\x65\156\40\x63\157\156\x6e\145\143\164\x20\164\x6f\x20\x2d\x20" . $ia2gwjkq68ixjak5c->getUri() . "\x2e\x20\103\x68\x65\143\x6b\40\x70\x68\x70\56\x69\156\151\54\40\x6e\x65\x65\x64\x3a\x20\141\154\x6c\x6f\167\x5f\165\x72\x6c\x5f\146\x6f\x70\x65\x6e\40\75\x20\117\x6e", "\x45\122\122\117\122\137\103\x4f\x4e\x4e\105\103\124\x5f\106\x4f\120\x45\116", ["\143\157\156\x74\x65\x78\x74\x5f\157\160\164\x69\x6f\156\163" => $zbwyzm2x5e4lvxobr]);
        }
        
        $this->parseResponseHeaders();
        
        $this->result = stream_get_contents($this->stream);
        
        $imtyzastjx78a0["\123\164\141\x74\165\x73\103\157\144\x65"] = $this->getStatus();
        $imtyzastjx78a0["\122\145\x73\160\x6f\156\x73\145\x48\145\141\144\145\x72\x73"] = "";
        $imtyzastjx78a0["\122\x65\163\x70\157\x6e\163\145\102\x6f\x64\171"] = $this->result;
        $o3nnej32qkqv8e4w = $this->getResponseHeaders();
        foreach ($o3nnej32qkqv8e4w as $d7f7t6ziajf21p9p0im2k => $xtvgb722ccq9wb) {
            $imtyzastjx78a0["\122\145\x73\160\x6f\x6e\163\145\110\145\141\144\x65\162\x73"] .= $d7f7t6ziajf21p9p0im2k . "\x3a\40" . $xtvgb722ccq9wb . PHP_EOL;
        }
        $br5sd8wnsrflz63 = "\55\x2d\55\x2d\55\x2d\x2d\x2d\x2d\x2d\55\55\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x2d\55\x2d\x2d\x2d\55\x2d\x2d\55\x2d\55\55\55\x2d\x2d\55\x2d\55\55\x2d\55\x2d\x2d\55\55\55\55\x2d\55\55\55\x2d\55\55\55\55\x2d\x2d\x2d\x2d\55\55\55" . PHP_EOL;
        $br5sd8wnsrflz63 .= date("\x64\x2e\x6d\x2e\131\x20\x48\72\151\72\163") . PHP_EOL;
        foreach ($imtyzastjx78a0 as $d7f7t6ziajf21p9p0im2k => $xtvgb722ccq9wb) {
            $br5sd8wnsrflz63 .= $d7f7t6ziajf21p9p0im2k . "\72" . PHP_EOL;
            $br5sd8wnsrflz63 .= $xtvgb722ccq9wb . PHP_EOL;
        }
        $br5sd8wnsrflz63 .= PHP_EOL . PHP_EOL;
        
        fclose($this->stream);
        $this->stream = null;
        return $this->result;
    }
    
    public function getFileMimeType($gki79tlxyqjv515mcr8i)
    {
        if (function_exists("\155\x69\x6d\x65\137\x63\157\156\164\145\156\164\137\164\171\160\x65")) {
            return mime_content_type($gki79tlxyqjv515mcr8i);
        }
        $dpyl91 = "\x61\x70\160\x6c\151\x63\141\164\x69\157\156\57\x6f\143\164\145\x74\x2d\x73\164\162\145\x61\155";
        if (preg_match("\57\50\x5c\56\x6a\x70\147\51\x24\x7c\50\x5c\56\x6a\160\145\147\x29\x24\174\x28\x5c\56\160\156\x67\51\44\174\x28\x5c\56\147\151\146\x29\44\57", $gki79tlxyqjv515mcr8i, $vb23okgppzk43cjtihb2aw549z3)) {
            switch ($vb23okgppzk43cjtihb2aw549z3[0]) {
                case "\56\x6a\160\x67":
                    $dpyl91 = "\x69\155\x61\x67\x65\x2f\x6a\160\145\x67";
                    break;
                case "\56\152\x70\x65\x67":
                    $dpyl91 = "\151\155\x61\147\145\x2f\152\160\145\x67";
                    break;
                case "\x2e\x70\156\147":
                    $dpyl91 = "\x69\x6d\141\147\145\x2f\x70\156\x67";
                    break;
                case "\x2e\147\151\146":
                    $dpyl91 = "\151\155\x61\147\145\57\147\151\146";
                    break;
            }
        }
        return $dpyl91;
    }
    
    protected function getRequestHeaders()
    {
        $gd2hruwola9tc9m = [];
        foreach ($this->arRequestHeaders as $vff1nisl3nx01j8vc0u => $d5p1p9nbv2n9a30atm7oybud4m0) {
            $gd2hruwola9tc9m[] = $vff1nisl3nx01j8vc0u . "\72\x20" . $d5p1p9nbv2n9a30atm7oybud4m0 . "\xd\12";
        }
        return implode("", $gd2hruwola9tc9m);
    }
    
    protected function parseResponseHeaders()
    {
        $bkayxi00naudp154xlzsbm0sxetnn5c8h5i = stream_get_meta_data($this->stream);
        if (is_array($bkayxi00naudp154xlzsbm0sxetnn5c8h5i) && isset($bkayxi00naudp154xlzsbm0sxetnn5c8h5i["\x77\x72\x61\160\x70\x65\x72\x5f\144\x61\x74\x61"])) {
            foreach ($bkayxi00naudp154xlzsbm0sxetnn5c8h5i["\167\162\141\160\160\x65\162\x5f\x64\141\x74\x61"] as $ahfhnrtb7nn) {
                [$aihxomdedxw4hr, $om1mt] = explode("\x3a", $ahfhnrtb7nn);
                if (preg_match("\57\136\x68\x74\x74\160\134\x2f\x28\134\x64\x5c\56\134\144\x29\134\x73\53\x28\133\134\144\135\53\x29\57\151", $aihxomdedxw4hr, $vb23okgppzk43cjtihb2aw549z3)) {
                    $this->status = $vb23okgppzk43cjtihb2aw549z3[2];
                    continue;
                }
                if (strtolower($aihxomdedxw4hr) == "\x73\145\x74\55\143\157\x6f\153\151\x65") {
                    $this->parseResponseCookieString($om1mt);
                    continue;
                }
                $this->arResponseHeaders[$aihxomdedxw4hr] = $om1mt;
            }
        }
    }
    
    protected function parseResponseCookieString($ipc75fzfrh1qmpp05obs220g7tv)
    {
        if (($sv7omrszdn01ei4dwxc = strpos($ipc75fzfrh1qmpp05obs220g7tv, "\x3b")) !== false && $sv7omrszdn01ei4dwxc > 0) {
            $z9olyohpw444 = trim(substr($ipc75fzfrh1qmpp05obs220g7tv, 0, $sv7omrszdn01ei4dwxc));
        } else {
            $z9olyohpw444 = trim($ipc75fzfrh1qmpp05obs220g7tv);
        }
        $ta9cj5gbepfn10v4yn = explode("\75", $z9olyohpw444, 2);
        $this->arResponseCookies[rawurldecode($ta9cj5gbepfn10v4yn[0])] = rawurldecode($ta9cj5gbepfn10v4yn[1]);
    }
    
    public function getResult()
    {
        return $this->result;
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function getResponseHeaders()
    {
        return $this->arResponseHeaders;
    }
    
    public function getResponseCookies()
    {
        return $this->arResponseCookies;
    }
}
?>