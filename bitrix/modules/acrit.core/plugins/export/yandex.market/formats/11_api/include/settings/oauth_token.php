<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

?>

<div>
	<input type="text" name="PROFILE[PARAMS][OAUTH_TOKEN]" value="<?=htmlspecialcharsbx($this->arParams['OAUTH_TOKEN']);?>"
		data-role="acrit_exp_yandex_market_api_oauth_token" size="36" maxlength="255" />
	<input type="hidden" name="PROFILE[PARAMS][OAUTH_REFRESH_TOKEN]"
		value="<?=htmlspecialcharsbx($this->arParams['OAUTH_REFRESH_TOKEN']);?>"
		data-role="acrit_exp_yandex_market_api_oauth_refresh_token" />
	<input type="hidden" name="PROFILE[PARAMS][OAUTH_TOKEN_TYPE]"
		value="<?=htmlspecialcharsbx($this->arParams['OAUTH_TOKEN_TYPE']);?>"
		data-role="acrit_exp_yandex_market_api_oauth_token_type" />
	<input type="hidden" name="PROFILE[PARAMS][OAUTH_EXPIRES_IN]"
		value="<?=htmlspecialcharsbx($this->arParams['OAUTH_EXPIRES_IN']);?>"
		data-role="acrit_exp_yandex_market_api_oauth_expires_in" />
	<input type="hidden" name="PROFILE[PARAMS][OAUTH_EXPIRE_TIMESTAMP]"
		value="<?=htmlspecialcharsbx($this->arParams['OAUTH_EXPIRE_TIMESTAMP']);?>"
		data-role="acrit_exp_yandex_market_api_oauth_expire_timestamp" />
	<input type="button"
		value="<?=static::getMessage('SETTINGS_NAME_OAUTH_TOKEN_GET');?>"
		data-role="acrit_exp_yandex_market_api_oauth_token_get"
		data-message-label-confirm-code="<?=static::getMessage('SETTINGS_NAME_OAUTH_TOKEN_MESSAGE_LABEL_CONFIRM_CODE');?>"
		data-message-placeholder-confirm-code="<?=static::getMessage('SETTINGS_NAME_OAUTH_TOKEN_MESSAGE_PLACEHOLDER_CONFIRM_CODE');?>"
		data-message-button-confirm-code="<?=static::getMessage('SETTINGS_NAME_OAUTH_TOKEN_MESSAGE_BUTTON_CONFIRM_CODE');?>"
		data-message-no-client-id="<?=static::getMessage('SETTINGS_NAME_OAUTH_TOKEN_MESSAGE_NO_CLIENT_ID');?>"
		data-message-no-client-secret-id="<?=static::getMessage('SETTINGS_NAME_OAUTH_TOKEN_MESSAGE_NO_CLIENT_SECRET_ID');?>"
		data-message-need-auth="<?=static::getMessage('SETTINGS_NAME_OAUTH_TOKEN_MESSAGE_NEED_AUTH');?>"
		data-message-confirm-code="<?=static::getMessage('SETTINGS_NAME_OAUTH_TOKEN_MESSAGE_CONFIRM_CODE');?>"
		data-message-error-get-token="<?=static::getMessage('SETTINGS_NAME_OAUTH_TOKEN_MESSAGE_GET_TOKEN');?>"
	/>
</div>
