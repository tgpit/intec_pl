<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

?>

<div>
	<input type="text" name="PROFILE[PARAMS][OAUTH_CLIENT_SECRET_ID]" value="<?=htmlspecialcharsbx($this->arParams['OAUTH_CLIENT_SECRET_ID']);?>"
		data-role="acrit_exp_avito_api_oauth_client_secret_id" size="40" maxlength="40" />
</div>
