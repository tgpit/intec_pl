<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

?>

<div>
	<input type="hidden" name="PROFILE[PARAMS][SKIP_APPEND_SIZES]" value="N" />
	<label>
		<input type="checkbox" name="PROFILE[PARAMS][SKIP_APPEND_SIZES]" value="Y"
			<?if($this->arParams['SKIP_APPEND_SIZES'] == 'Y'):?> checked="Y"<?endif?>
			data-role="acrit_exp_wildberries_skip_append_sizes" />
		<span><?=static::getMessage('SKIP_APPEND_SIZES_CHECKBOX');?></span>
	</label>
	<?=Helper::showHint(static::getMessage('SKIP_APPEND_SIZES_HINT'));?>
</div>
