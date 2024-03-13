<?php
namespace Acrit\Core\Export\Plugins\WildberriesV4Helpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json;

Helper::loadMessages();

# Get data from wb
$arItems = [];
$arData = [
	'{objectName}' => rawurlencode(trim($arPost['category_name'])),
];
$obResponse = $this->API->execute('/content/v1/object/characteristics/{objectName}', $arData);
if($obResponse->getStatus() == 200){
	if(is_array($arAttributes = $obResponse->getJsonResult()['data'])){
		foreach($arAttributes as $arAttribute){
			$arItem = [
				'objectName' => $arAttribute['objectName'],
				'name' => $arAttribute['name'],
				'required' => $arAttribute['required'] ? 'Y' : 'N',
				'unitName' => $arAttribute['unitName'],
				'maxCount' => $arAttribute['maxCount'],
				'popular' => $arAttribute['popular'] ? 'Y' : 'N',
				'charcType' => $arAttribute['charcType'],
			];
			$arItems[] = $arItem;
		}
	}

	# Display
	$sTableID = 'acrit_wb_cards_explorer_cards';
	$obSort = new \CAdminSorting($sTableID, 'name', 'ASC');
	$obAdminList = new \CAdminList($sTableID, $obSort);
	$obAdminList->addHeaders([
		[
			'id' => 'objectName',
			'content' => static::getMessage('CARDS_BROWSER_objectName'),
			'align' => 'left',
			'default' => true,
		],
		[
			'id' => 'name',
			'content' => static::getMessage('CARDS_BROWSER_name'),
			'align' => 'left',
			'default' => true,
		],
		[
			'id' => 'required',
			'content' => static::getMessage('CARDS_BROWSER_required'),
			'align' => 'center',
			'default' => true,
		],
		[
			'id' => 'unitName',
			'content' => static::getMessage('CARDS_BROWSER_unitName'),
			'align' => 'left',
			'default' => true,
		],
		[
			'id' => 'maxCount',
			'content' => static::getMessage('CARDS_BROWSER_maxCount'),
			'align' => 'right',
			'default' => true,
		],
		[
			'id' => 'popular',
			'content' => static::getMessage('CARDS_BROWSER_popular'),
			'align' => 'center',
			'default' => true,
		],
		[
			'id' => 'charcType',
			'content' => static::getMessage('CARDS_BROWSER_charcType'),
			'align' => 'right',
			'default' => true,
		],
	]);
	usort($arItems, function($a, $b){
		return strcmp($a['name'], $b['name']);
	});
	foreach($arItems as $arItem){
		$obRow = &$obAdminList->addRow(md5($arItem['name']), $arItem);
		$obRow->addViewField('name', sprintf('<b>%s</b>', $arItem['name']));
		$obRow->addViewField('required', Helper::getMessage('MAIN_'.($arItem['required'] == 'Y' ? 'YES' : 'NO')));
		$obRow->addViewField('popular', Helper::getMessage('MAIN_'.($arItem['popular'] == 'Y' ? 'YES' : 'NO')));
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