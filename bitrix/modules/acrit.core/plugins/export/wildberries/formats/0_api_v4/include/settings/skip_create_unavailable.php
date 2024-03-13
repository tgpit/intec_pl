<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

?>

<div>
	<input type="hidden" name="PROFILE[PARAMS][SKIP_CREATE_UNAVAILABLE]" value="N" />
	<label>
		<input type="checkbox" name="PROFILE[PARAMS][SKIP_CREATE_UNAVAILABLE]" value="Y"
			<?if($this->arParams['SKIP_CREATE_UNAVAILABLE'] == 'Y'):?> checked="Y"<?endif?>
			data-role="acrit_exp_wildberries_skip_create_unavailable" />
		<span><?=static::getMessage('SKIP_CREATE_UNAVAILABLE_CHECKBOX');?></span>
	</label>
	<?=Helper::showHint(static::getMessage('SKIP_CREATE_UNAVAILABLE_HINT'));?>
</div>
