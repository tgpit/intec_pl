<?
namespace Acrit\Core\Orders\Plugins;

use \Acrit\Core\Helper;

global $arProfile;
?>
<input type="checkbox" name="PROFILE[CONNECT_CRED][USE_V2_API]" value="Y"<?if($arProfile['CONNECT_CRED']['USE_V2_API']=='Y'):?> checked="checked"<?endif?>/>