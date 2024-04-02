<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

?>

<div>
	<input type="hidden" name="PROFILE[PARAMS][EXPORT_STOCKS_BY_1]" value="N" />
	<label>
		<input type="checkbox" name="PROFILE[PARAMS][EXPORT_STOCKS_BY_1]" value="Y"
			<?if($this->arParams['EXPORT_STOCKS_BY_1'] == 'Y'):?> checked="Y"<?endif?>
			data-role="acrit_exp_wildberries_export_stocks_by_1" />
		<span><?=static::getMessage('EXPORT_STOCKS_BY_1_CHECKBOX');?></span>
	</label>
	<?=Helper::showHint(static::getMessage('EXPORT_STOCKS_BY_1_HINT'));?>
</div>
