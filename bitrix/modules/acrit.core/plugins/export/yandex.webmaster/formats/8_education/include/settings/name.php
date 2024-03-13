<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

?>

<div>
	<input type="text" name="PROFILE[PARAMS][YML_NAME]" value="<?=htmlspecialcharsbx($this->arParams['YML_NAME']);?>"
		data-role="acrit_exp_yandex_market_education_name" size="40" maxlength="30" />
</div>
