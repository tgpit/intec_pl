<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

?>

<div>
	<input type="hidden" name="PROFILE[PARAMS][SKIP_UPDATE_CARDS]" value="N" />
	<label>
		<input type="checkbox" name="PROFILE[PARAMS][SKIP_UPDATE_CARDS]" value="Y"
			<?if($this->arParams['SKIP_UPDATE_CARDS'] == 'Y'):?> checked="Y"<?endif?>
			data-role="acrit_exp_wildberries_skip_update_cards" />
		<span><?=static::getMessage('SKIP_UPDATE_CARDS_CHECKBOX');?></span>
	</label>
	<?=Helper::showHint(static::getMessage('SKIP_UPDATE_CARDS_HINT'));?>
</div>
