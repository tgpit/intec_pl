<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

?>

<div>
	<input type="hidden" name="PROFILE[PARAMS][EXPORT_NEW_DATE_FORMAT]" value="N" />
	<label>
		<input type="checkbox" name="PROFILE[PARAMS][EXPORT_NEW_DATE_FORMAT]" value="Y"
			<?if($this->arParams['EXPORT_NEW_DATE_FORMAT'] == 'Y'):?> checked="Y"<?endif?>
			data-role="acrit_exp_yandex_marketplace_export_new_data_formart" />
		<span><?=static::getMessage('EXPORT_NEW_DATE_FORMAT_CHECKBOX');?></span>
	</label>
	<?=Helper::showHint(static::getMessage('EXPORT_NEW_DATE_FORMAT_HINT'));?>
</div>
