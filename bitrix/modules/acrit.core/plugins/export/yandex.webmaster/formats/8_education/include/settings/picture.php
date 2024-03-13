<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

?>

<div>
	<input type="text" name="PROFILE[PARAMS][YML_PICTURE]" value="<?=htmlspecialcharsbx($this->arParams['YML_PICTURE']);?>"
		data-role="acrit_exp_yandex_market_education_picture" size="40" />
</div>
