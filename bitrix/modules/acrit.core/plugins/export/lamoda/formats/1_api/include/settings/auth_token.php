<?
namespace Acrit\Core\Export\Plugins;

use \Acrit\Core\Helper;

?>
<input type="text" name="PROFILE[PARAMS][AUTH_TOKEN]" data-role="acrit_exp_lamoda_api_key" value="<?= htmlspecialcharsbx($this->arProfile['PARAMS']['AUTH_TOKEN']) ?>" size="40" />
<input type="button" data-role="acrit_exp_lamoda_access_check" value="<?=static::getMessage('API_KEY_CHECK');?>"
	style="height:25px;">
