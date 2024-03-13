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
$strGetValue = 'find_categories';

if($arParams['GET'][$strGetParam] == $strGetValue){
	$arSearchResults = [];
	$obResponse = $this->API->execute('/content/v1/object/all', ['name' => $arParams['POST']['q']]);
	if($obResponse->getStatus() == 200){
		if($arQueryResult = $obResponse->getJsonResult()){
			if(is_array($arQueryResult['data'])){
				foreach($arQueryResult['data'] as $arCategory){
					$strCategoryName = '';
					if(Helper::strlen($arCategory['parentName'])){
						$strCategoryName = sprintf(' [%s]', $arCategory['parentName']);
					}
					$arSearchResults[] = [
						'id' => $arCategory['objectName'],
						'text' => $arCategory['objectName'].$strCategoryName,
					];
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
<div id="acrit_wb_popup_category_add">
	<div style="margin-bottom:8px;"><b><?=static::getMessage('POPUP_CATEGORY_ADD_FIELD_TITLE');?>:</b></div>
	<div class="acrit-exp-select-wrapper">
		<select data-role="acrit_wb_popup_category_select"></select>
	</div>
</div>
<script>
	$('#acrit_wb_popup_category_add select').select2({
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
		dropdownParent: $('#acrit_wb_popup_category_add').first(),
		dropdownPosition: 'below',
		matcher:window.acritExpSelect2Matcher,
		language: 'ru'
	});
</script>