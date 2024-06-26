<?
/**
 * Acrit Core: create tables for WB
 * @documentation https://suppliers-portal.wildberries.ru/goods/products-card/
 */

namespace Acrit\Core\Export\Plugins\WildberriesV4Helpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Plugins\WildberriesV4Helpers\HistoryStockTable as HistoryStock;

?>
<div id="acrit_wb_json_preview_popup">
	<?
	$arSubTabs = [];
	$arSubTabs[] = [
		'DIV' => 'json_formatted', 
		'TAB' => static::getMessage('TAB_FORMATTED'), 
		'ONSELECT' => 'acritExpWbPopupJsonChangeTab();',
	];
	$arSubTabs[] = [
		'DIV' => 'json_unformatted', 
		'TAB' => static::getMessage('TAB_UNFORMATTED'), 
		'ONSELECT' => 'acritExpWbPopupJsonChangeTab();',
	];
	$obTabControl = new \CAdminViewTabControl('AcritExpWbJsonPreview', $arSubTabs);
	$obTabControl->begin();
	$obTabControl->beginNextTab();
	$arJson = Json::decode($strJson);
	$strJsonFormatted = Json::encode($arJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	if(!Helper::isUtf()){
		$strJsonFormatted = Helper::convertEncoding($strJsonFormatted, 'UTF-8', 'CP1251');
	}
	?>
		<pre style="margin-top:0;"><code class="json"><?=$strJsonFormatted;?></code></pre>
		<div data-role="acrit_wb_json_copy_source" style="height:1px; width:1px; overflow:hidden; white-space:pre;"><?
			print $strJsonFormatted;
		?></div>
	<?
	$obTabControl->beginNextTab();
	?>
		<pre style="margin-top:0;"><code class="json"><?=$strJson;?></code></pre>
		<div data-role="acrit_wb_json_copy_source" style="height:1px; width:1px; overflow:hidden;"><?
			print $strJson;
		?></div>
	<?
	$obTabControl->end();
	?>
	<?if($arParams['ALLOW_COPY']):?>
		<script>
			$('#acrit_wb_json_preview_popup > .adm-detail-subtabs-block').append(
				$('<span class="adm-detail-subtabs"/>')
					.attr('id', 'acrit_wb_json_preview_popup_copy')
					.text('<?=static::getMessage('JSON_COPY');?>')
					.css({background:'transparent', color:'green'})
					.bind('click', function(e){
						let
							element = $('#acrit_wb_json_preview_popup div[data-role="acrit_wb_json_copy_source"]:visible');
						e.preventDefault();
						console.log(element.get(0));
						acritCoreCopyToClipboard(element.get(0), function(){
							alert('<?=static::getMessage('JSON_COPIED');?>');
						});
					})
			);
		</script>
	<?endif?>
</div>
<script>
function acritExpWbPopupJsonChangeTab(){
	let tab = $('#acrit_wb_json_preview_popup .adm-detail-subtab-active'),
		tabCode = tab.attr('id').replace(/^view_tab_/, ''),
		bntCopy = $('#acrit_wb_json_preview_popup_copy');
	if(tabCode.match(/^json_/)){
		bntCopy.show();
	}
	else{
		bntCopy.hide();
	}
}
acritExpWbPopupJsonChangeTab();
</script>