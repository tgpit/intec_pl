<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

?>

<div>
	<input type="hidden" name="PROFILE[PARAMS][EXPORT_STOCKS]" value="N" />
	<label>
		<input type="checkbox" name="PROFILE[PARAMS][EXPORT_STOCKS]" value="Y"
			<?if($this->arParams['EXPORT_STOCKS'] == 'Y'):?> checked="Y"<?endif?>
			data-role="acrit_exp_wildberries_export_stocks" />
		<span><?=static::getMessage('EXPORT_STOCKS_CHECKBOX');?></span>
	</label>
	<?=Helper::showHint(static::getMessage('EXPORT_STOCKS_HINT'));?>
</div>

<div data-role="acrit_exp_wildberries_stores_wrapper">

	<div data-role="acrit_exp_wildberries_stores">
		<?$strStoreUrl = 'https://suppliers-portal.wildberries.ru/marketplace-pass/warehouses';?>
		<?foreach($this->getStores(true) as $intStoreId => $strStoreName):?>
			<div>
				<input type="text" name="PROFILE[PARAMS][STOCKS][ID][]" size="8" maxlength="10"
					placeholder="<?=static::getMessage('STOCK_ID');?>"
					value="<?=htmlspecialcharsbx($intStoreId);?>" />
				<input type="text" name="PROFILE[PARAMS][STOCKS][NAME][]" size="40" maxlength="255"
					placeholder="<?=static::getMessage('STOCK_NAME');?>"
					value="<?=htmlspecialcharsbx($strStoreName);?>" />
				<?=Helper::showHint(static::getMessage('STOCK_HINT', ['#STORE_URL#' => $strStoreUrl]));?>
			</div>
		<?endforeach?>
	</div>

</div>
