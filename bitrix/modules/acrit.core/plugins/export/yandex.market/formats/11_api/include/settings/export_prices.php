<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

?>

<div>
	<input type="hidden" name="PROFILE[PARAMS][EXPORT_PRICES]" value="N" />
	<label>
		<input type="checkbox" name="PROFILE[PARAMS][EXPORT_PRICES]" value="Y"
			<?if($this->arParams['EXPORT_PRICES'] == 'Y'):?> checked="Y"<?endif?>
			data-role="acrit_exp_yandex_market_api_export_prices" />
		<span><?=static::getMessage('EXPORT_PRICES_CHECKBOX');?></span>
	</label>
	<?=Helper::showHint(static::getMessage('EXPORT_PRICES_HINT'));?>
</div>
