<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

?>

<div>
	<input type="text" name="PROFILE[PARAMS][YML_EMAIL]" value="<?=htmlspecialcharsbx($this->arParams['YML_EMAIL']);?>"
		data-role="acrit_exp_yandex_market_education_email" size="40" maxlength="30" />
</div>
