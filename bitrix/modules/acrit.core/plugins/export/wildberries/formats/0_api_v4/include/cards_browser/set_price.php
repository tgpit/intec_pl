<?php
namespace Acrit\Core\Export\Plugins\WildberriesV4Helpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json;

Helper::loadMessages();

if(!strlen($arPost['set_price_nm_id'])){
	print Helper::showNote(static::getMessage('CARDS_BROWSER_ERROR_NO_NM_ID'), true);
	return;
}

if(!strlen($arPost['set_price_value'])){
	print Helper::showNote(static::getMessage('CARDS_BROWSER_ERROR_NO_VALUE'), true);
	return;
}

$arData = [
	[
		'nmId' => intVal($arPost['set_price_nm_id']),
		'price' => intVal($arPost['set_price_value']),
	],
];
$obResponse = $this->API->execute('/public/api/v1/prices', $arData);
if($obResponse->getStatus() == 200){
	$arResponse = $obResponse->getJsonResult();
	if(is_array($arResponse)){
		if(is_numeric($arResponse['uploadId'])){
			print Helper::showNote(static::getMessage('CARDS_BROWSER_SUCCESS'), true);
		}
	}
	$this->cardsBrowserDisplayJson($arData, $obResponse);
}
else{
	print static::getMessage('CARDS_BROWSER_ERROR', [
		'#HTTP_CODE#' => $obResponse->getStatus(),
		'#RESPONSE#' => Json::prettyPrint($obResponse->getJsonResult()),
		'#HEADERS#' => print_r($obResponse->getResponseHeaders(), true),
	]);
}