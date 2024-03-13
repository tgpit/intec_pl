<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

?>

<div>
	<input type="text" name="PROFILE[PARAMS][YML_COMPANY]" value="<?=htmlspecialcharsbx($this->arParams['YML_COMPANY']);?>"
		data-role="acrit_exp_yandex_market_education_company" size="40" />
</div>
