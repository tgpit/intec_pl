<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

?>

<div>
	<input type="hidden" name="PROFILE[PARAMS][CONTINUE_ON_ERROR]" value="N" />
	<label>
		<input type="checkbox" name="PROFILE[PARAMS][CONTINUE_ON_ERROR]" value="Y"
			<?if($this->arParams['CONTINUE_ON_ERROR'] == 'Y'):?> checked="Y"<?endif?>
			data-role="acrit_exp_wb_continue_on_error" />
		<span><?=static::getMessage('CONTINUE_ON_ERROR_CHECKBOX');?></span>
	</label>
	<?=Helper::showHint(static::getMessage('CONTINUE_ON_ERROR_HINT'));?>
</div>

