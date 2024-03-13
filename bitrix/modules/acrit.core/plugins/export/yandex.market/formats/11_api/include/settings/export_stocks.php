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
			data-role="acrit_exp_yandex_market_api_export_stocks" />
		<span><?=static::getMessage('EXPORT_STOCKS_CHECKBOX');?></span>
	</label>
	<?=Helper::showHint(static::getMessage('EXPORT_STOCKS_HINT'));?>
</div>

<div data-role="acrit_exp_yandex_market_api_stores_wrapper">
	
	<div data-role="acrit_exp_yandex_market_api_stores" style="padding-top:10px;">
		<div data-role="acrit_exp_yandex_market_api_stores_list">
			<?foreach($this->getStores(true) as $intStoreId => $strStoreName):?>
				<div data-role="acrit_exp_yandex_market_api_store">
					<input type="text" name="PROFILE[PARAMS][STOCKS][ID][]" size="14" maxlength="36"
						placeholder="<?=static::getMessage('STOCK_ID');?>"
						value="<?=htmlspecialcharsbx($intStoreId);?>"
						data-role="acrit_exp_yandex_market_api_store_id" />
					<input type="text" name="PROFILE[PARAMS][STOCKS][NAME][]" size="40" maxlength="255"
						placeholder="<?=static::getMessage('STOCK_NAME');?>"
						value="<?=htmlspecialcharsbx($strStoreName);?>"
						data-role="acrit_exp_yandex_market_api_store_name" />
					<?=Helper::showHint(static::getMessage('STOCK_HINT'));?>
					<input type="button" data-role="acrit_exp_yandex_market_api_store_delete" 
						value="<?=static::getMessage('EXPORT_STOCKS_DELETE');?>"
						data-confirm="<?=static::getMessage('EXPORT_STOCKS_DELETE_CONFIRM');?>">
				</div>
			<?endforeach?>
		</div>
		<div data-role="acrit_exp_yandex_market_api_stores_add_wrapper">
			<input type="button" data-role="acrit_exp_yandex_market_api_store_add"
				value="<?=static::getMessage('EXPORT_STOCKS_ADD');?>">
		</div>
	</div>

</div>
