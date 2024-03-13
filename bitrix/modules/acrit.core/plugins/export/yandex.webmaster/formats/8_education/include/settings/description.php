<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

?>

<div>
	<textarea name="PROFILE[PARAMS][YML_DESCRIPTION]" cols="50" rows="3"
		data-role="acrit_exp_yandex_market_education_description"
		><?=htmlspecialcharsbx($this->arParams['YML_DESCRIPTION']);?></textarea>
</div>
