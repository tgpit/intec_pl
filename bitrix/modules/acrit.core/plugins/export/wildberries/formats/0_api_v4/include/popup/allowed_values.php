<?
/**
 * Acrit Core: create tables for WB
 * @documentation https://suppliers-portal.wildberries.ru/goods/products-card/
 */

namespace Acrit\Core\Export\Plugins\WildberriesV4Helpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Plugins\WildberriesV4Helpers\Api;

$strGetParam = 'subaction';
$strGetValue = 'find_values';
$strDivId = 'acrit_wb_popup_value_copy_helper';

$arAttributeDictionary = $this->getAttributeDictionaryInfo($arAttribute['NAME']);

# Copy mode
$arCopyValueInsteadOfText = [
	'/content/v1/directory/tnved',
	'/content/v1/directory/colors',
	'/content/v1/directory/countries',
];
$bCopySelectValueInsteadOfText = in_array($arAttributeDictionary['PATH'], $arCopyValueInsteadOfText);

if($arParams['GET'][$strGetParam] == $strGetValue && $arAttributeDictionary){
	$arSearchResults = [];
	if(Helper::strlen($arAttributeDictionary['PATH'])){
		$strQuery = trim($arParams['POST']['q']);
		$arParams = [
			'ARGUMENTS' => [
				'SEARCH_TEXT' => $strQuery,
				'CATEGORY_NAME' => $arAttribute['CATEGORY_NAME'],
				'COUNT' => 100,
			],
		];
		$obResponse = $this->API->execute($arAttributeDictionary['PATH'], [], $arParams);
		if($obResponse->getStatus() == 200 && $arJsonResult = $obResponse->getJsonResult()){
			if(is_array($arJsonResult['data'])){
				foreach($arJsonResult['data'] as $key => $value){
					if($arAttributeDictionary['ADDITIONAL_SEARCH']){
						$value_s = is_array($value) ? $value['name'].' '.$value['fullName'] : $value;
						if(stripos(toLower($value_s), toLower($strQuery)) === false){
							continue;
						}
					}
					if(is_string($value)){ // season, kinks, brands
						$arSearchResults[] = [
							'id' => $value,
							'text' => $value,
						];
					}
					elseif(is_array($value) && isset($value['name'])){ // countries, colors, contents, consists, collections
						$strText = $value['name'];
						if(isset($value['fullName'])){
							$strText = sprintf('[%s] %s', $strText, $value['fullName']);
						}
						if(isset($value['parentName'])){
							$strText = sprintf('%s (%s)', $strText, $value['parentName']);
						}
						$arSearchResults[] = [
							'id' => isset($value['id']) ? $value['id'] : $value['name'],
							'text' => $strText,
						];
					}
					elseif(is_string($value['tnvedName'])){ // tnved
						$strText = sprintf('[%s] %s', $value['tnvedName'], $value['description']);
						if($value['isKiz']){
							$strText .= static::getMessage('POPUP_ALLOWED_VALUES_TNVED_KIZ');
						}
						$arSearchResults[] = [
							'id' => $value['tnvedName'],
							'text' => $strText,
						];
					}
				}
			}
		}
	}
	Json::prepare();
	$arSearchResults = [
		'incomplete_results' => false,
		'items' => $arSearchResults,
		'total_count' => count($arSearchResults),
	];
	Json::output($arSearchResults);
	die();
}

?>
<style>
div[data-role="acrit_wb_popup_value_copy_wrapper"]{
	display:none;
}
div[data-role="acrit_wb_popup_value_copy_text"]{
	height:1px;
	width:1px;
	overflow:hidden;
}
div[data-role="acrit_wb_popup_value_copied"]{
	color:green;
	display:none;
	margin:10px 0;
	font-weight:bold;
}
</style>
<div id="acrit_wb_popup_values_search">
	<div style="margin-bottom:8px;"><b><?=static::getMessage('POPUP_ALLOWED_VALUES_FIELD_TITLE');?>:</b></div>
	<div class="acrit-exp-select-wrapper">
		<select data-role="acrit_wb_popup_value_search" data-select-value=<?=($bCopySelectValueInsteadOfText ? 'Y' : 'N')?>></select>
		<div data-role="acrit_wb_popup_value_copy_text">
			<div id="<?=$strDivId;?>"></div>
		</div>
	</div>
	<?=static::getMessage('POPUP_ALLOWED_VALUES_ITEM_ADDITIONAL_INFO_NOTICE');?>
	<br/>
	<div data-role="acrit_wb_popup_value_copy_wrapper">
		<input type="button" value="<?=static::getMessage('POPUP_ALLOWED_VALUES_COPY');?>"
			data-role="acrit_wb_popup_value_copy" />
		<input type="button" value="<?=static::getMessage('POPUP_ALLOWED_VALUES_COPY_CLOSE');?>"
			data-role="acrit_wb_popup_value_copy_close" />
	</div>
	<div data-role="acrit_wb_popup_value_copied">
		<?=static::getMessage('POPUP_ALLOWED_VALUES_COPIED');?>
	</div>
</div>
<script>
	$('#acrit_wb_popup_values_search select').select2({
		ajax: {
			url: '<?=$GLOBALS['APPLICATION']->GetCurPageParam(sprintf('%s=%s', $strGetParam, $strGetValue), [$strGetParam]);?>',
			type: 'post',
			dataType: 'json',
			data: function (params) {
				var query = $.extend({}, <?=Json::encode($arParams['POST']);?>, {
					q: params.term,
					page: params.page
				});
				return query;
			},
			processResults: function(data, params) {
				params.page = params.page || 1;
				return {
					results: data.items,
					pagination: {
						more: false
					}
				};
			},
			cache: false
		},
		dropdownParent: $('#acrit_wb_popup_values_search').first(),
		dropdownPosition: 'below',
		matcher:window.acritExpSelect2Matcher,
		language: 'ru'
	});
	$('select[data-role="acrit_wb_popup_value_search"]').bind('change', function(){
		let copyValue = $(this).attr('data-select-value') == 'Y' ? $(this).val() : $('option:selected', this).text().trim();
		$('#<?=$strDivId;?>').text(copyValue);
		$('div[data-role="acrit_wb_popup_value_copy_wrapper"]').toggle(!!$(this).val().length);
	});
	$('input[data-role="acrit_wb_popup_value_copy"]').bind('click', function(){
		let text = $('#<?=$strDivId;?>').text();
		if(text.length){
			acritCoreCopyToClipboard('<?=$strDivId;?>', function(){
				let
					className = 'acrit-exp-text-blink',
					span = $('div[data-role="acrit_wb_popup_value_copied"]').show().addClass(className),
					timeout1, timeout2;
				clearTimeout(timeout1);
				clearTimeout(timeout2);
				timeout1 = setTimeout(function(){
					span.removeClass(className);
					timeout2 = setTimeout(function(){
						span.hide();
					}, 2000);
				}, 1000);
			});
		}
	});
	$('input[data-role="acrit_wb_popup_value_copy_close"]').bind('click', function(){
		let text = $('#<?=$strDivId;?>').text().trim();
		if(text.length){
			acritCoreCopyToClipboard('<?=$strDivId;?>', function(){
				AcritPopupHint.Close();
			});
		}
	});
</script>