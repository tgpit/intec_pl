<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

?>

<div>
	<input type="hidden" name="PROFILE[PARAMS][EXPORT_PRODUCTS]" value="N" />
	<label>
		<input type="checkbox" name="PROFILE[PARAMS][EXPORT_PRODUCTS]" value="Y"
			<?if($this->arParams['EXPORT_PRODUCTS'] == 'Y'):?> checked="Y"<?endif?>
			data-role="acrit_exp_yandex_market_api_export_products" />
		<span><?=static::getMessage('EXPORT_PRODUCTS_CHECKBOX');?></span>
	</label>
	<?=Helper::showHint(static::getMessage('EXPORT_PRODUCTS_HINT'));?>
</div>
