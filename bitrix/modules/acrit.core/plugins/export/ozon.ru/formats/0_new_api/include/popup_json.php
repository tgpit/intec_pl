<?
/**
 * Acrit Core: create tables for ozon
 * @documentation https://docs.ozon.ru/api/seller
 */

namespace Acrit\Core\Export\Plugins\OzonRuHelpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Exporter;
?>
<div id="acrit_exp_ozon_json_preview_popup">
	<?if($arParams['DISPLAY_OFFER_NOTE']):?>
		<?print Helper::showNote(static::getMessage('OFFER_NOTE', [
			'#TASK_ID_OZON#' => $arData['TASK_ID_OZON'],
			'#TIMESTAMP_X#' => $arData['TIMESTAMP_X'],
			'#OFFER_ID#' => $arData['OFFER_ID'],
			'#TASK_ID#' => $arData['TASK_ID'],
		]), true);?><br/>
	<?endif?>
	<?if($arParams['DISPLAY_TASK_NOTE']):?>
		<?print Helper::showNote(static::getMessage('TASK_NOTE', [
			'#TASK_ID#' => $arData['TASK_ID'],
			'#TIMESTAMP_X#' => $arData['TIMESTAMP_X'],
			'#ID#' => $arData['ID'],
		]), true);?><br/>
	<?endif?>
	<?
	$arSubTabs = [];
	$arSubTabs[] = [
		'DIV' => 'json_formatted', 
		'TAB' => static::getMessage('TAB_FORMATTED'), 
		'ONSELECT' => 'acritExpOzonPopupJsonChangeTab();',
	];
	$arSubTabs[] = [
		'DIV' => 'json_unformatted', 
		'TAB' => static::getMessage('TAB_UNFORMATTED'), 
		'ONSELECT' => 'acritExpOzonPopupJsonChangeTab();',
	];
	if(Helper::strlen($arData['RESPONSE'])){
		$arSubTabs[] = [
			'DIV' => 'response', 
			'TAB' => static::getMessage('TAB_RESPONSE'), 
			'ONSELECT' => 'acritExpOzonPopupJsonChangeTab();',
		];
	}
	if($arParams['SHOW_STOCKS']){
		$arSubTabs[] = [
			'DIV' => 'stocks', 
			'TAB' => static::getMessage('TAB_STOCKS'), 
			'ONSELECT' => 'acritExpOzonPopupJsonChangeTab();',
		];
	}
	$obTabControl = new \CAdminViewTabControl('AcritExpOzonJsonPreview', $arSubTabs);
	$obTabControl->begin();
	$obTabControl->beginNextTab();
	$arJson = Json::decode($strJson);
	$strJsonFormatted = Json::encode($arJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	if(!Helper::isUtf()){
		$strJsonFormatted = Helper::convertEncoding($strJsonFormatted, 'UTF-8', 'CP1251');
	}
	?>
		<pre style="margin-top:0;"><code class="json"><?=$strJsonFormatted;?></code></pre>
		<div data-role="acrit_ozon_json_copy_source" style="height:1px; width:1px; overflow:hidden; white-space:pre;"><?
			print $strJsonFormatted;
		?></div>
	<?
	$obTabControl->beginNextTab();
	?>
		<pre style="margin-top:0;"><code class="json"><?=$strJson;?></code></pre>
		<div data-role="acrit_ozon_json_copy_source" style="height:1px; width:1px; overflow:hidden;"><?
			print $strJson;
		?></div>
	<?if(Helper::strlen($arData['RESPONSE'])):?>
		<?
		$obTabControl->beginNextTab();
		?>
		<pre style="margin-top:0;"><code class="json"><?=$arData['RESPONSE'];?></code></pre>
		<div data-role="acrit_ozon_json_copy_source" style="height:1px; width:1px; overflow:hidden; white-space:pre;"><?
			print $arData['RESPONSE'];
		?></div>
	<?endif?>
	<?if($arParams['SHOW_STOCKS']):?>
		<?
		$obTabControl->beginNextTab();
		?>
		<style>
		.acrit_exp_ozon_table_stocks th {
			padding:8px!important;
		}
		.acrit_exp_ozon_table_stocks td {
			padding:8px!important;
		}
		.acrit_exp_ozon_table_stocks td:first-child {
			text-align:left!important;
		}
		.acrit_exp_ozon_table_stocks td img {
			margin-right:2px;
			vertical-align:middle;
		}
		.acrit_exp_ozon_table_stocks td img + span {
			vertical-align:middle;
		}
		</style>
		<table class="acrit_exp_ozon_table_stocks adm-list-table">
			<thead>
				<tr class="adm-list-table-header">
					<th class="adm-list-table-cell"><?=static::getMessage('STOCK_TYPE');?></th>
					<th class="adm-list-table-cell"><?=static::getMessage('STOCK_VALUE');?></th>
					<th class="adm-list-table-cell"><?=static::getMessage('STOCK_RESULT');?></th>
				</tr>
			</thead>
			<tbody>
				<?foreach($arData['STOCKS'] as $arStock):?>
					<tr class="adm-list-table-row">
						<td class="adm-list-table-cell">
							<?if(is_numeric($arStock['WAREHOUSE_ID']) && $arStock['WAREHOUSE_ID'] > 0):?>
								<?=static::getMessage('STOCK_TYPE_WAREHOUSE', ['#ID#' => $arStock['WAREHOUSE_ID']]);?>
							<?else:?>
								<?=static::getMessage('STOCK_TYPE_GENERAL');?>
							<?endif?>
						</td>
						<td class="adm-list-table-cell align-right">
							<?=$arStock['STOCK'];?>
						</td>
						<td class="adm-list-table-cell">
							<?$strLight = $arStock['UPDATED'] == 'Y' ? 'green' : 'red';?>
							<img src="/bitrix/themes/.default/images/lamp/<?=$strLight;?>.gif" width="14" height="14" alt="" >
							<span><?=$arStock['ERRORS'];?></span>
						</td>
					</tr>
				<?endforeach?>
			</tbody>
		</table>
	<?endif?>
	<?
	$obTabControl->end();
	?>
	<?if($arParams['ALLOW_COPY']):?>
		<script>
			$('#acrit_exp_ozon_json_preview_popup > .adm-detail-subtabs-block').append(
				$('<span class="adm-detail-subtabs"/>')
					.attr('id', 'acrit_exp_ozon_json_preview_popup_copy')
					.text('<?=static::getMessage('JSON_COPY');?>')
					.css({background:'transparent', color:'green'})
					.bind('click', function(e){
						let
							element = $('#acrit_exp_ozon_json_preview_popup div[data-role="acrit_ozon_json_copy_source"]:visible');
						e.preventDefault();
						acritCoreCopyToClipboard(element.get(0), function(){
							alert('<?=static::getMessage('JSON_COPIED');?>');
						});
					})
			);
		</script>
	<?endif?>
</div>
<script>
function acritExpOzonPopupJsonChangeTab(){
	let tab = $('#acrit_exp_ozon_json_preview_popup .adm-detail-subtab-active'),
		tabCode = tab.attr('id').replace(/^view_tab_/, ''),
		bntCopy = $('#acrit_exp_ozon_json_preview_popup_copy');
	if(tabCode.match(/^json_/) || tabCode == 'response'){
		bntCopy.show();
	}
	else{
		bntCopy.hide();
	}
}
acritExpOzonPopupJsonChangeTab();
</script>