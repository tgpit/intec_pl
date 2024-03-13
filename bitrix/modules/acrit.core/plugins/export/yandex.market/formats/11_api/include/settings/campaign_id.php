<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

?>

<div>
	<input type="text" name="PROFILE[PARAMS][CAMPAIGN_ID]" value="<?=htmlspecialcharsbx($this->arParams['CAMPAIGN_ID']);?>"
		data-role="acrit_exp_yandex_market_api_campaign_id" size="10" maxlength="10" />
	<input type="button" value="<?=static::getMessage('SETTINGS_NAME_CAMPAIGN_ID_CHECK');?>"
		data-role="acrit_exp_yandex_market_api_campaign_id_check" />
	<span class="acrit_exp_yandex_market_api_status" data-role="acrit_exp_yandex_market_api_campaign_id_status"></span>
</div>
