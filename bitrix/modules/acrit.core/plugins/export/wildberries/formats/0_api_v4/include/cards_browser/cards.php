<?php
namespace Acrit\Core\Export\Plugins\WildberriesV4Helpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json;

Helper::loadMessages();

$arPost['page'] = max(1, intVal($arPost['page']));
$arPost['count_per_page'] = max(1, intVal($arPost['count_per_page']));

# Get data from wb
$arItems = [];
$arData = [
	'sort' => [
		'cursor' => [
			'limit' => intVal($arPost['count_per_page']),
		],
		'sort' => [
			'sortColumn' => $arPost['sort_field'],
			'ascending' => $arPost['sort_order'] == 'desc' ? false : true,
		],
		'filter' => [
			'withPhoto' => intVal(in_array($arPost['with_photo'], ['-1', '1', '0'], true) ? $arPost['with_photo'] : '-1'),
		],
	],
];
if(Helper::strlen($arPost['text_search'])){
	$arData['sort']['filter']['textSearch'] = $arPost['text_search'];
}
if(Helper::strlen($arPost['nav_updated_at'])){
	$arData['sort']['cursor']['updatedAt'] = $arPost['nav_updated_at'];
}
if(Helper::strlen($arPost['nav_nm_id'])){
	$arData['sort']['cursor']['nmID'] = intVal($arPost['nav_nm_id']);
}
$obResponse = $this->API->execute('/content/v1/cards/cursor/list', $arData);
if($obResponse->getStatus() == 200){
	$strNavUpdatedAt = null;
	$intNavNmId = null;
	if(is_array($arCards = $obResponse->getJsonResult()['data']['cards'])){
		foreach($arCards as $arCard){
			$arItem = [
				'nmID' => $arCard['nmID'],
				'updateAt' => $arCard['updateAt'],
				'object' => $arCard['object'],
				'brand' => $arCard['brand'],
				'vendorCode' => $arCard['vendorCode'],
				'colors' => is_array($arCard['colors']) ? implode(', ', $arCard['colors']) : $arCard['colors'],
				'mediaFiles' => count($arCard['mediaFiles']),
				'sizes' => null,
			];
			$strNavUpdatedAt = $arCard['updateAt'];
			$intNavNmId = $arCard['nmID'];
			if(is_array($arCard['sizes'])){
				$arDisplaySizes = [];
				foreach($arCard['sizes'] as $arSize){
					$arDisplaySize = [];
					if(isset($arSize['techSize'])){
						$arDisplaySize[] = sprintf('Tech size: %s', $arSize['techSize']);
					}
					if(isset($arSize['wbSize'])){
						$arDisplaySize[] = sprintf('Wb size: %s', $arSize['wbSize']);
					}
					if(isset($arSize['skus'])){
						$arDisplaySize[] = sprintf('Skus: %s', is_array($arSize['skus']) ? implode(', ', $arSize['skus']) : $arSize['skus']);
					}
					if(!empty($arDisplaySize)){
						$arDisplaySizes[] = $arDisplaySize;
					}
				}
				if(!empty($arDisplaySizes)){
					$arItem['sizes'] = $arDisplaySizes;
				}
			}
			$arItems[] = $arItem;
		}
	}

	# Display
	$sTableID = 'acrit_wb_cards_explorer_cards';
	$obSort = new \CAdminSorting($sTableID, 'vendorCode', 'ASC');
	$obAdminList = new \CAdminList($sTableID, $obSort);
	$obAdminList->addHeaders([
		[
			'id' => 'vendorCode',
			'content' => static::getMessage('CARDS_BROWSER_vendorCode'),
			'align' => 'left',
			'default' => true,
		],
		[
			'id' => 'object',
			'content' => static::getMessage('CARDS_BROWSER_object'),
			'align' => 'left',
			'default' => true,
		],
		[
			'id' => 'brand',
			'content' => static::getMessage('CARDS_BROWSER_brand'),
			'align' => 'left',
			'default' => true,
		],
		[
			'id' => 'sizes',
			'content' => static::getMessage('CARDS_BROWSER_sizes'),
			'align' => 'left',
			'default' => true,
		],
		[
			'id' => 'colors',
			'content' => static::getMessage('CARDS_BROWSER_colors'),
			'align' => 'left',
			'default' => true,
		],
		[
			'id' => 'mediaFiles',
			'content' => static::getMessage('CARDS_BROWSER_mediaFiles'),
			'align' => 'right',
			'default' => true,
		],
		[
			'id' => 'nmID',
			'content' => static::getMessage('CARDS_BROWSER_nmID'),
			'align' => 'right',
			'default' => true,
		],
		[
			'id' => 'updateAt',
			'content' => static::getMessage('CARDS_BROWSER_updateAt'),
			'align' => 'left',
			'default' => true,
		],
	]);
	foreach($arItems as $arItem){
		$obRow = &$obAdminList->addRow($arItem['nmID'], $arItem);
		#
		$strSizesHtml = '';
		if(is_array($arItem['sizes']) && !empty($arItem['sizes'])){
			$arSize = array_shift($arItem['sizes']);
			$strSizesHtml = implode('<br>', $arSize);
			if(!empty($arItem['sizes'])){
				$strSizesHtml .= '<br>';
				$strSizesHtml .= sprintf('<a href="#" class="acrit-inline-link" onclick="if(!$(this).next().is(\':animated\'))$(this).next().slideToggle(); return false;";>%s (%s)</a>', static::getMessage('CARDS_BROWSER_VIEW_OTHERS'), count($arItem['sizes']));
				$strSizesHtml .= '<div style="display:none"><br>';
				foreach($arItem['sizes'] as $key => $arSize){
					$arItem['sizes'][$key] = implode('<br>', $arSize);
				}
				$strSizesHtml .= implode('<div style="background:#999;height:1px;margin:4px 0;overflow:hidden;"></div>', $arItem['sizes']);
				$strSizesHtml .= '</br>';
			}
		}
		$obRow->addViewField('sizes', $strSizesHtml);
	}
	$obAdminList->displayList();
	?>
	<br/>
	<?if($intNavNmId):?>
		<div>
			<?=static::getMessage('CARDS_BROWSER_COUNT', [
				'#COUNT#' => count($arItems),
			]);?>
		</div>
		<div>
			<?=static::getMessage('CARDS_BROWSER_NAV_DATA', [
				'#UPDATED_AT#' => $strNavUpdatedAt,
				'#NM_ID#' => $intNavNmId,
			]);?>
		</div>
		<div>
			<?=Helper::showNote(static::getMessage('CARDS_BROWSER_NAV_DISCLAIMER'), true);?>
		</div>
		<div><br/></div>
	<?endif?>
	<script>
		if($('input[data-role="acrit_exp_wildberries_cards_browser_auto_nav"]').prop('checked')){
			$('input[data-role="acrit_exp_wildberries_cards_browser_updated_at"]').val('<?=trim($strNavUpdatedAt);?>');
			$('input[data-role="acrit_exp_wildberries_cards_browser_nm_id"]').val('<?=trim($intNavNmId);?>');
		}
	</script>
	<?
	$this->cardsBrowserDisplayJson($arData, $obResponse);
}
else{
	print static::getMessage('CARDS_BROWSER_ERROR', [
		'#HTTP_CODE#' => $obResponse->getStatus(),
		'#RESPONSE#' => Json::prettyPrint($obResponse->getJsonResult()),
		'#HEADERS#' => print_r($obResponse->getResponseHeaders(), true),
	]);
}