<?php
namespace Acrit\Core\Export\Plugins\WildberriesV4Helpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json;

Helper::loadMessages();

# Get data from wb
$arItems = [];
$arVendorCodes = Helper::splitSpaceValues(trim($arPost['filter_vendor_code']));
$arData = [
	'vendorCodes' => $arVendorCodes,
];
$obResponse = $this->API->execute('/content/v1/cards/filter', $arData);
if($obResponse->getStatus() == 200){
	if(is_array($arCards = $obResponse->getJsonResult()['data'])){
		foreach($arCards as $arCard){
			$arMediaFiles = [];
			if(is_array($arCard['mediaFiles'])){
				foreach($arCard['mediaFiles'] as $strUrl){
					$arMediaFiles[] = sprintf('<a href="%s" target="_blank">%s</a>', $strUrl,
						pathinfo(parse_url($strUrl, PHP_URL_PATH), PATHINFO_BASENAME));
				}
			}
			$arItem = [
				'ID' => null,
				'imtID' => $arCard['imtID'],
				'nmID' => $arCard['nmID'],
				'vendorCode' => $arCard['vendorCode'],
				'object' => null,
				'sizes' => null,
				'characteristics' => null,
				'mediaFiles' => implode(PHP_EOL, is_array($arCard['mediaFiles']) ? $arCard['mediaFiles'] : []),
			];
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
					if(isset($arSize['chrtID'])){
						$arDisplaySize[] = sprintf('chrtID: %s', $arSize['chrtID']);
					}
					if(isset($arSize['price'])){
						$arDisplaySize[] = sprintf('price: %s', $arSize['price']);
					}
					if(!empty($arDisplaySize)){
						$arDisplaySizes[] = $arDisplaySize;
					}
				}
				if(!empty($arDisplaySizes)){
					$arItem['sizes'] = $arDisplaySizes;
				}
			}
			if(is_array($arCard['characteristics'])){
				$strTitleObject = static::getMessage('CARDS_BROWSER_object');
				$arCharacterAll = [];
				foreach($arCard['characteristics'] as $index => $arCharacter){
					foreach($arCharacter as $strTitle => $arValue){
						if($strTitle == $strTitleObject){
							$arItem['object'] = print_r($arValue, true);
							unset($arCard['characteristics'][$index]);
						}
						else{
							$arCharacterAll[] = [
								$strTitle,
								is_array($arValue) ? implode(', ', $arValue) : $arValue,
							];
						}
					}
				}
				ksort($arCharacterAll);
				if(!empty($arCharacterAll)){
					$arItem['characteristics'] = $arCharacterAll;
				}
			}
			$arItem['mediaFiles'] = $arMediaFiles;
			$arItems[] = $arItem;
		}
	}

	# Display
	$sTableID = 'acrit_wb_cards_explorer_cards';
	$obSort = new \CAdminSorting($sTableID, 'vendorCode', 'ASC');
	$obAdminList = new \CAdminList($sTableID, $obSort);
	$obAdminList->addHeaders([
		[
			'id' => 'ID',
			'content' => static::getMessage('CARDS_BROWSER_ID'),
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
			'id' => 'sizes',
			'content' => static::getMessage('CARDS_BROWSER_sizes'),
			'align' => 'left',
			'default' => true,
		],
		[
			'id' => 'characteristics',
			'content' => static::getMessage('CARDS_BROWSER_characteristics'),
			'align' => 'left',
			'default' => true,
		],
		[
			'id' => 'mediaFiles',
			'content' => static::getMessage('CARDS_BROWSER_mediaFiles'),
			'align' => 'left',
			'default' => true,
		],
	]);
	foreach($arItems as $arItem){
		$obRow = &$obAdminList->addRow($arItem['nmID'], $arItem);
		#
		$obRow->addViewField('ID', implode('<br>', [$arItem['imtID'], '<b>'.$arItem['nmID'].'</b>', $arItem['vendorCode']]));
		#
		$strSizesHtml = '<div style="height:1px; min-width:180px;"></div>';
		if(is_array($arItem['sizes']) && !empty($arItem['sizes'])){
			$arSize = array_shift($arItem['sizes']);
			$strSizesHtml .= implode('<br>', $arSize);
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
		#
		$strCharsHtml = '<div style="height:1px; min-width:180px;"></div>';
		if(is_array($arItem['characteristics']) && !empty($arItem['characteristics'])){
			$arItem['characteristics'] = array_map(function($arChar){
				return sprintf('<li>%s: <b>%s</b></li>', $arChar[0], $arChar[1]);
			}, $arItem['characteristics']);
			$strCharsHtml .= '<ul style="margin:0; padding:0;">'.implode('', $arItem['characteristics']).'</ul>';
		}
		$obRow->addViewField('characteristics', $strCharsHtml);
		$obRow->addViewField('mediaFiles', implode('<br>', $arItem['mediaFiles']));
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