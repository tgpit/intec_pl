<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

?>

<div>
	<input type="text" name="PROFILE[PARAMS][BUSINESS_ID]" value="<?=htmlspecialcharsbx($this->arParams['BUSINESS_ID']);?>"
		data-role="acrit_exp_yandex_market_api_business_id" size="10" maxlength="10" />
	<input type="button" value="<?=static::getMessage('SETTINGS_NAME_BUSINESS_ID_CHECK');?>"
		data-role="acrit_exp_yandex_market_api_business_id_check" />
	<input type="button" value="<?=static::getMessage('SETTINGS_NAME_BUSINESS_ID_VIEW');?>"
		data-role="acrit_exp_yandex_market_api_business_id_view" />
	<span class="acrit_exp_yandex_market_api_status" data-role="acrit_exp_yandex_market_api_business_id_status"></span>
</div>
