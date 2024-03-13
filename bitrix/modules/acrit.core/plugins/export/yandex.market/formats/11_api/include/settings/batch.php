<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

?>

<div>
	<input type="text" name="PROFILE[PARAMS][BATCH]" value="<?=$this->getBatchSize();?>"
		data-role="acrit_exp_yandex_market_api_batch" size="10" maxlength="3" />
</div>
