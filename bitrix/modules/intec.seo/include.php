<?php

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

require_once('classes/Loader.php');

Loc::loadMessages(__FILE__);

class IntecSeo
{
    protected static $_1 = 'intec.seo';
    protected static $_0 = 0;

    public static function Initialize()
    {
        $M=Array(base64_decode('aXN' .'fZmlsZ' .'Q=='),base64_decode('aW' .'50d' .'m' .'F' .'s'),base64_decode('Zm' .'lsZV' .'9nZXRfY29udGV' .'udHM' .'='),base64_decode('aXN' .'fZmlsZQ=' .'='),base64_decode('bWQ1'),base64_decode('aX' .'NfZmls' .'ZQ=' .'='),base64_decode('ZmlsZV9nZXR' .'fY29udGVudH' .'M='),base64_decode('' .'c3R' .'ycG9z'),base64_decode('YmluMm' .'hle' .'A=='),base64_decode('c' .'3RyX3Jl' .'cG' .'xhY2U='),base64_decode('Zml' .'sZV9wdX' .'RfY29' .'udGVudHM' .'='),base64_decode('' .'cHJlZ19tYXRjaA' .'=='),base64_decode('cmF3dXJsZ' .'W' .'5j' .'b2' .'Rl'),base64_decode('cmF3' .'dXJsZ' .'W5jb2Rl'),base64_decode('ZmlsZV9nZXRfY29udG' .'VudHM' .'='),base64_decode('c3RyZWFtX2N' .'v' .'bnRleHRfY3JlY' .'XRl'),base64_decode('' .'Z' .'m' .'ls' .'Z' .'V' .'9wd' .'XRfY29' .'u' .'d' .'G' .'VudHM=')); ?><? function _882895976($i){$a=Array('L2JpdHJpeC9tb2R1bGVzLw==','L3RlbXAuZGF0','L2JpdHJpeC9saWNlbnNlX2tleS5waHA=','aHR0cDovL2xpY2Vuc2UuaW50ZWN3b3JrMS5ydS9saWNlbnNlcy92ZXJpZnk=','c29sdXRpb24=','a2V5','aGFzaA==','QklUUklY','TElDRU5DRQ==','ZG9tYWlu','U0VSVkVSX05BTUU=','ZW1haWw=','Zmlyc3ROYW1l','bGFzdE5hbWU=','c2Vjb25kTmFtZQ==','bWFyaw==','ZW1haWw=','RU1BSUw=','Zmlyc3ROYW1l','TkFNRQ==','bGFzdE5hbWU=','TEFTVF9OQU1F','c2Vjb25kTmFtZQ==','U0VDT05EX05BTUU=','L29wdGlvbnMucGhw','L29wdGlvbnMucGhw','I0hBU0gj','bWFyaw==','I0hBU0gj','bWFyaw==','L29wdGlvbnMucGhw','L0Bjb21tZW50XHMqKFxTKikvaQ==','bWFyaw==','','Jg==','PQ==','aHR0cA==','bWV0aG9k','UE9TVA==','aGVhZGVy','Q29udGVudC1UeXBlOiBhcHBsaWNhdGlvbi94LXd3dy1mb3JtLXVybGVuY29kZWQ=','DQo=','Y29udGVudA==','YmxvY2tlZA==','cmVtb3ZlZA==','L2JpdHJpeC9tb2R1bGVzLw==');return base64_decode($a[$i]);} ?><? static::$_0=CModule::IncludeModuleEx(static::$_1);$_0=new DateTime();$_1=Application::getDocumentRoot() ._882895976(0) .static::$_1 ._882895976(1);$_2=$M[0]($_1);$_3=new DateTime();if($_2)$_3->setTimestamp($M[1]($M[2]($_1)));$_4=$_0->diff($_3);if($_4->_5>round(0+0.5+0.5)||!$_2){$_6=Application::getDocumentRoot() ._882895976(2);if($M[3]($_6)){include($_6);$_6=null;if(isset($LICENSE_KEY))$_6=$LICENSE_KEY;}else{$_6=null;}$_7=_882895976(3);$_8=[_882895976(4)=> static::$_1,_882895976(5)=> $_6,_882895976(6)=>!empty($_6)?$M[4](_882895976(7) .$_6 ._882895976(8)):null,_882895976(9)=> $_SERVER[_882895976(10)],_882895976(11)=> null,_882895976(12)=> null,_882895976(13)=> null,_882895976(14)=> null,_882895976(15)=> null];$_9=CUser::GetByID(round(0+0.25+0.25+0.25+0.25))->Fetch();if(!empty($_9)){$_8[_882895976(16)]=$_9[_882895976(17)];$_8[_882895976(18)]=$_9[_882895976(19)];$_8[_882895976(20)]=$_9[_882895976(21)];$_8[_882895976(22)]=$_9[_882895976(23)];}if($M[5](__DIR__ ._882895976(24))){$_10=@$M[6](__DIR__ ._882895976(25));if($M[7]($_10,_882895976(26))!== false){$_8[_882895976(27)]=$M[8](random_bytes(round(0+16)));$_10=$M[9](_882895976(28),$_8[_882895976(29)],$_10);@$M[10](__DIR__ ._882895976(30),$_10);}else{$_11=[];if($M[11](_882895976(31),$_10,$_11))$_8[_882895976(32)]=$_11[round(0+0.25+0.25+0.25+0.25)];unset($_11);}unset($_10);}$_10=_882895976(33);foreach($_8 as $_6 => $_12){if(!empty($_10))$_10 .= _882895976(34);$_10 .= $M[12]($_6) ._882895976(35) .$M[13]($_12);}$_13=@$M[14]($_7,false,$M[15]([_882895976(36)=>[_882895976(37)=> _882895976(38),_882895976(39)=> _882895976(40) ._882895976(41),_882895976(42)=> $_10]]));if($_13 == _882895976(43)){static::$_0=round(0);}else if($_13 == _882895976(44)){DeleteDirFilesEx(_882895976(45) .static::$_1);static::$_0=round(0);}else{$M[16]($_1,$_0->getTimestamp());}}
        static::Validate();
    }

    protected static function Validate()
    {
        if (static::$_0 != 1 && static::$_0 != 2)
            die(Loc::getMessage('intec.seo.demo', ['#MODULE_ID#' => static::$_1]));
    }
}

?>