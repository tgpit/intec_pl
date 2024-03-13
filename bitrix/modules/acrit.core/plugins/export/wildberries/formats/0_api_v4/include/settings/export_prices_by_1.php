<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

$strTokenUrl = 'https://suppliers-portal.wildberries.ru/marketplace-pass/api-access';

?>

<div>
	<input type="hidden" name="PROFILE[PARAMS][EXPORT_PRICES_BY_1]" value="N" />
	<label>
		<input type="checkbox" name="PROFILE[PARAMS][EXPORT_PRICES_BY_1]" value="Y"
			<?if($this->arParams['EXPORT_PRICES_BY_1'] == 'Y'):?> checked="Y"<?endif?>
			data-role="acrit_exp_wildberries_export_prices_by_1" />
		<span><?=static::getMessage('EXPORT_PRICES_BY_1_CHECKBOX');?></span>
	</label>
	<?=Helper::showHint(static::getMessage('EXPORT_PRICES_BY_1_HINT'));?>
</div>
