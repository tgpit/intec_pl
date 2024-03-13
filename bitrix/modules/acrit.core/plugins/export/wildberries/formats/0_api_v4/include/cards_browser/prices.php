<?php
namespace Acrit\Core\Export\Plugins\WildberriesV4Helpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json;

Helper::loadMessages();

$intNmId = intVal($arPost['prices_nm_id']);
$intCount = 0;
$arItems = [];
$arData = [];
$obResponse = $this->API->execute('/public/api/v1/info', $arData);
if($obResponse->getStatus() == 200){
	if(is_array($arPrices = $obResponse->getJsonResult())){
		$arItems = $arPrices;
		$intCount = count($arItems);
		if($intNmId > 0){
			foreach($arItems as $key => $arItem){
				if($arItem['nmId'] != $intNmId){
					unset($arItems[$key]);
				}
			}
		}
	}
	# Display
	$sTableID = 'acrit_wb_cards_explorer_cards';
	$obSort = new \CAdminSorting($sTableID, 'name', 'ASC');
	$obAdminList = new \CAdminList($sTableID, $obSort);
	$obAdminList->addHeaders([
		[
			'id' => 'nmId',
			'content' => static::getMessage('CARDS_BROWSER_nmId'),
			'align' => 'left',
			'default' => true,
		],
		[
			'id' => 'price',
			'content' => static::getMessage('CARDS_BROWSER_price'),
			'align' => 'left',
			'default' => true,
		],
		[
			'id' => 'discount',
			'content' => static::getMessage('CARDS_BROWSER_discount'),
			'align' => 'left',
			'default' => true,
		],
		[
			'id' => 'promoCode',
			'content' => static::getMessage('CARDS_BROWSER_promoCode'),
			'align' => 'left',
			'default' => true,
		],
	]);
	foreach($arItems as $arItem){
		$obRow = &$obAdminList->addRow(md5($arItem['nmId']), $arItem);
		// $obRow->addViewField('name', sprintf('<b>%s</b>', $arItem['name']));
		// $obRow->addViewField('required', Helper::getMessage('MAIN_'.($arItem['required'] == 'Y' ? 'YES' : 'NO')));
		// $obRow->addViewField('popular', Helper::getMessage('MAIN_'.($arItem['popular'] == 'Y' ? 'YES' : 'NO')));
	}
	print Helper::showNote(static::getMessage('CARDS_BROWSER_COUNT', ['#COUNT#' => $intCount]), true).'<br/>';
	$obAdminList->displayList();
	$this->cardsBrowserDisplayJson($arData, $obResponse);
}
else{
	print static::getMessage('CARDS_BROWSER_ERROR', [
		'#HTTP_CODE#' => $obResponse->getStatus(),
		'#RESPONSE#' => Json::prettyPrint($obResponse->getJsonResult()),
		'#HEADERS#' => print_r($obResponse->getResponseHeaders(), true),
	]);
}