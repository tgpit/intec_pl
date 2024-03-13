<?php
namespace Acrit\Core\Export\Plugins\WildberriesV4Helpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json;

Helper::loadMessages();

# Get data from wb
$arItems = [];
$arData = [];
$obResponse = $this->API->execute('/content/v1/cards/error/list', $arData);
if($obResponse->getStatus() == 200){
	if(is_array($arErrors = $obResponse->getJsonResult()['data'])){
		foreach($arErrors as $arError){
			$arItem = [
				'vendorCode' => $arError['vendorCode'],
				'object' => $arError['object'],
				'updateAt' => $arError['updateAt'],
				'errors' => $arError['errors'],
			];
			$arItems[] = $arItem;
		}
	}


	# Display
	$sTableID = 'acrit_wb_cards_explorer_errors';
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
			'id' => 'updateAt',
			'content' => static::getMessage('CARDS_BROWSER_updateAt'),
			'align' => 'left',
			'default' => true,
		],
		[
			'id' => 'errors',
			'content' => static::getMessage('CARDS_BROWSER_errors'),
			'align' => 'left',
			'default' => true,
		],
	]);
	foreach($arItems as $arItem){
		$obRow = &$obAdminList->addRow($arItem['nmID'], $arItem);
		$strSizesHtml = '';
		if(is_array($arItem['errors']) && !empty($arItem['errors'])){
			$arItem['errors'] = array_map(function($strError){
				return sprintf('<li>%s</li>', $strError);
			}, $arItem['errors']);
			$strSizesHtml = '<ul style="margin:0; padding:0 0 0 20px;">'.implode('', $arItem['errors']).'</ul>';
			
		}
		$obRow->addViewField('errors', $strSizesHtml);
	}
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

?>