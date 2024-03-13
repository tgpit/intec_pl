<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

?>

<div>
	<input type="hidden" name="PROFILE[PARAMS][GROUP_BY_COLORS]" value="N" />
	<label>
		<input type="checkbox" name="PROFILE[PARAMS][GROUP_BY_COLORS]" value="Y"
			<?if($this->arParams['GROUP_BY_COLORS'] == 'Y'):?> checked="Y"<?endif?>
			data-role="acrit_exp_wildberries_group_by_colors" />
		<span><?=static::getMessage('GROUP_BY_COLORS_CHECKBOX');?></span>
	</label>
	<?=Helper::showHint(static::getMessage('GROUP_BY_COLORS_HINT'));?>
</div>
