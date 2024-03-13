<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

?>

<div style="display:inline-block;">
	<input type="text" value="<?=htmlspecialcharsbx($this->intProfileId ? $this->getAuthToken() : '');?>"
		name="PROFILE[PARAMS][AUTH_TOKEN]" size="40" data-role="acrit_exp_sbermegamarket_auth_token"
		placeholder="<?=static::getMessage('SETTINGS_NAME_AUTH_TOKEN_PLACEHOLDER');?>" />
	<span class="acrit_exp_sbermegamarket_auth_token_status" data-role="acrit_exp_sbermegamarket_auth_token_status"></span>
	<input type="button" value="<?=static::getMessage('SETTINGS_NAME_AUTH_TOKEN_CHECK');?>"
		data-role="acrit_exp_sbermegamarket_auth_token_check" />
</div>
