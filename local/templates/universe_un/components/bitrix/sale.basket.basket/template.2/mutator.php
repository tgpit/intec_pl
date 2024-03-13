<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\PriceMaths;
use intec\Core;
use intec\core\helpers\Type;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Json;
use intec\core\helpers\ArrayHelper;
use intec\core\bitrix\iblock\ElementsQuery;
use intec\template\Properties;

/**
 *
 * This file modifies result for every request (including AJAX).
 * Use it to edit output result for "{{ mustache }}" templates.
 *
 * @var array $result
 */

if (!Loader::includeModule('intec.core'))
    return;

if (!empty($this->arParams['COLUMNS_LIST_MOBILE']) && Type::isArray($this->arParams['COLUMNS_LIST_MOBILE']))
    $mobileColumns = $this->arParams['COLUMNS_LIST_MOBILE'];
else
    $mobileColumns = $this->arParams['COLUMNS_LIST'];

$mobileColumns = array_fill_keys($mobileColumns, true);

$result['BASKET_ITEM_RENDER_DATA'] = [];
$arVolume = [
    'SHOW' => $this->arParams['TOTAL_VOLUME_SHOW'] === 'Y',
    'EXACT' => true,
    'VALUE' => 0,
    'VALUE_FORMATTED' => null
];

if (!defined('EDITOR')) {
    Core::setAlias('@intec/template', $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/classes');
}

if ($this->arParams['SETTINGS_USE'] === 'Y') {
    $this->arParams['QUICK_VIEW_USE'] = Properties::get('basket-quick-view-use') ? 'Y' : 'N';
}

if ($this->arParams['QUICK_VIEW_USE'] === 'Y') {
    $arProductsIds = [];

    if (Type::isArray($arProductsIds))
        $arProductsIds = array_column($this->basketItems, 'PRODUCT_ID');

    $arOffers = CCatalogSKU::getProductList($arProductsIds);

    if (!$arOffers)
        $arOffers = [];

    $arElements = array_unique(array_merge(array_diff($arProductsIds, ArrayHelper::getKeys($arOffers)), array_column($arOffers, 'ID')));
    $arProductsIds = ArrayHelper::flip($arProductsIds);

    foreach ($arProductsIds as $sKeyProductId => $sValue) {
        if (ArrayHelper::keyExists($sKeyProductId, $arOffers)) {
            $arProductsIds[$sKeyProductId] = $arOffers[$sKeyProductId]['ID'];
        } else {
            $arProductsIds[$sKeyProductId] = $sKeyProductId;
        }
    }

    $arFilter = [
        'IBLOCK_ID' => $this->arParams['QUICK_VIEW_IBLOCK_ID'],
        'ID' => $arElements
    ];
    $arSelect = [
        'ID',
        'IBLOCK_ID',
        'IBLOCK_SECTION_ID',
        'CODE',
        'NAME'
    ];
    $rsItems = new ElementsQuery([
        'filter' => $arFilter,
        'select' => $arSelect
    ]);

    $arBasketProducts = $rsItems
        ->setWithProperties(false)
        ->execute()
        ->indexBy('ID')
        ->asArray();
}

foreach ($this->basketItems as $row) {
	$rowData = array(
		'ID' => $row['ID'],
		'PRODUCT_ID' => $row['PRODUCT_ID'],
		'NAME' => isset($row['~NAME']) ? $row['~NAME'] : $row['NAME'],
		'QUANTITY' => $row['QUANTITY'],
		'PROPS_SHOW' => !empty($row['PROPS']),
		'PROPS' => $row['PROPS'],
		'PROPS_ALL' => $row['PROPS_ALL'],
		'HASH' => $row['HASH'],
		'SORT' => $row['SORT'],
		'DETAIL_PAGE_URL' => $row['DETAIL_PAGE_URL'],
		'CURRENCY' => $row['CURRENCY'],
		'DISCOUNT_PRICE_PERCENT' => $row['DISCOUNT_PRICE_PERCENT'],
		'DISCOUNT_PRICE_PERCENT_FORMATED' => $row['DISCOUNT_PRICE_PERCENT_FORMATED'],
		'SHOW_DISCOUNT_PRICE' => (float)$row['DISCOUNT_PRICE'] > 0,
		'PRICE' => $row['PRICE'],
		'PRICE_FORMATED' => $row['PRICE_FORMATED'],
		'FULL_PRICE' => $row['FULL_PRICE'],
		'FULL_PRICE_FORMATED' => $row['FULL_PRICE_FORMATED'],
		'DISCOUNT_PRICE' => $row['DISCOUNT_PRICE'],
		'DISCOUNT_PRICE_FORMATED' => $row['DISCOUNT_PRICE_FORMATED'],
		'SUM_PRICE' => $row['SUM_VALUE'],
		'SUM_PRICE_FORMATED' => $row['SUM'],
		'SUM_FULL_PRICE' => $row['SUM_FULL_PRICE'],
		'SUM_FULL_PRICE_FORMATED' => $row['SUM_FULL_PRICE_FORMATED'],
		'SUM_DISCOUNT_PRICE' => $row['SUM_DISCOUNT_PRICE'],
		'SUM_DISCOUNT_PRICE_FORMATED' => $row['SUM_DISCOUNT_PRICE_FORMATED'],
		'MEASURE_RATIO' => isset($row['MEASURE_RATIO']) ? $row['MEASURE_RATIO'] : 1,
        'MEASURE_RATIO_SINGLE' => $row['MEASURE_RATIO'] == 1,
		'MEASURE_TEXT' => $row['MEASURE_TEXT'],
		'AVAILABLE_QUANTITY' => $row['AVAILABLE_QUANTITY'],
		'CHECK_MAX_QUANTITY' => $row['CHECK_MAX_QUANTITY'],
		'MODULE' => $row['MODULE'],
		'PRODUCT_PROVIDER_CLASS' => $row['PRODUCT_PROVIDER_CLASS'],
		'NOT_AVAILABLE' => $row['NOT_AVAILABLE'] === true,
		'DELAYED' => $row['DELAY'] === 'Y',
		'SKU_BLOCK_LIST' => array(),
		'COLUMN_LIST_SHOW' => false,
		'COLUMN_LIST' => array(),
		'SHOW_LABEL' => false,
		'LABEL_VALUES' => array(),
		'BRAND' => isset($row[$this->arParams['BRAND_PROPERTY'].'_VALUE'])
			? $row[$this->arParams['BRAND_PROPERTY'].'_VALUE']
			: '',
	);

	// show price including ratio
	if ($rowData['MEASURE_RATIO'] != 1) {

        $rowData['PRICE_FORMATED'] = CCurrencyLang::CurrencyFormat(
            $rowData['PRICE'],
            $rowData['CURRENCY'],
            true
        );

		$fullPrice = PriceMaths::roundPrecision($rowData['FULL_PRICE'] * $rowData['MEASURE_RATIO']);

		if ($fullPrice != $rowData['FULL_PRICE']) {
			$rowData['FULL_PRICE'] = $fullPrice;
			$rowData['FULL_PRICE_FORMATED'] = CCurrencyLang::CurrencyFormat(
			    $fullPrice,
                $rowData['CURRENCY'],
                true
            );
		}

		$discountPrice = PriceMaths::roundPrecision($rowData['DISCOUNT_PRICE'] * $rowData['MEASURE_RATIO']);

		if ($discountPrice != $rowData['DISCOUNT_PRICE']) {
			$rowData['DISCOUNT_PRICE'] = $discountPrice;
			$rowData['DISCOUNT_PRICE_FORMATED'] = CCurrencyLang::CurrencyFormat(
			    $discountPrice,
                $rowData['CURRENCY'],
                true
            );
		}
	}

	$rowData['SHOW_PRICE_FOR'] = (float)$rowData['QUANTITY'] !== (float)$rowData['MEASURE_RATIO'];

	$hideDetailPicture = false;

	if (!empty($row['PREVIEW_PICTURE_SRC'])) {
		$rowData['IMAGE_URL'] = $row['PREVIEW_PICTURE_SRC'];
	} elseif (!empty($row['DETAIL_PICTURE_SRC'])) {
		$hideDetailPicture = true;
		$rowData['IMAGE_URL'] = $row['DETAIL_PICTURE_SRC'];
	}

	if (!empty($row['SKU_DATA'])) {
		$propMap = array();

		foreach($row['PROPS'] as $prop) {
			$propMap[$prop['CODE']] = !empty($prop['~VALUE']) ? $prop['~VALUE'] : $prop['VALUE'];
		}

		$notSelectable = true;

		foreach ($row['SKU_DATA'] as $skuBlock) {
			$skuBlockData = array(
				'ID' => $skuBlock['ID'],
				'CODE' => $skuBlock['CODE'],
				'NAME' => $skuBlock['NAME']
			);

			$isSkuSelected = false;
			$isImageProperty = false;

			if (count($skuBlock['VALUES']) > 1) {
				$notSelectable = false;
			}

			foreach ($skuBlock['VALUES'] as $skuItem) {
				if ($skuBlock['TYPE'] === 'S' && $skuBlock['USER_TYPE'] === 'directory') {
					$valueId = $skuItem['XML_ID'];
				} elseif ($skuBlock['TYPE'] === 'E') {
					$valueId = $skuItem['ID'];
				} else {
					$valueId = $skuItem['NAME'];
				}

				$skuValue = array(
					'ID' => $skuItem['ID'],
					'NAME' => $skuItem['NAME'],
					'SORT' => $skuItem['SORT'],
					'PICT' => !empty($skuItem['PICT']) ? $skuItem['PICT']['SRC'] : false,
					'XML_ID' => !empty($skuItem['XML_ID']) ? $skuItem['XML_ID'] : false,
					'VALUE_ID' => $valueId,
					'PROP_ID' => $skuBlock['ID'],
					'PROP_CODE' => $skuBlock['CODE']
				);

				if (
					!empty($propMap[$skuBlockData['CODE']]) && ($propMap[$skuBlockData['CODE']] == $skuItem['NAME'] ||
                        $propMap[$skuBlockData['CODE']] == $skuItem['XML_ID'])
				) {
					$skuValue['SELECTED'] = true;
					$isSkuSelected = true;
				}

				$skuBlockData['SKU_VALUES_LIST'][] = $skuValue;
				$isImageProperty = $isImageProperty || !empty($skuItem['PICT']);
			}

			if (!$isSkuSelected && !empty($skuBlockData['SKU_VALUES_LIST'][0])) {
				$skuBlockData['SKU_VALUES_LIST'][0]['SELECTED'] = true;
			}

			$skuBlockData['IS_IMAGE'] = $isImageProperty;

			$rowData['SKU_BLOCK_LIST'][] = $skuBlockData;
		}

		$rowData['IS_SKU'] = true;
	} else {
	    $rowData['IS_SKU'] = false;
    }

    if ($this->arParams['QUICK_VIEW_USE'] === 'Y') {
        include(__DIR__ . '/parts/data.php');

        $arOriginalParams = $this->arParams;
        $arItem = $arBasketProducts[$arProductsIds[$row['PRODUCT_ID']]];
        $rowData['QUICK_VIEW_DATA'] = Json::encode($dData($arItem), JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true);

        unset($arOriginalParams);
    }

	if ($row['NOT_AVAILABLE'])
	{
		foreach ($rowData['SKU_BLOCK_LIST'] as $blockKey => $skuBlock)
		{
			if (!empty($skuBlock['SKU_VALUES_LIST']))
			{
				if ($notSelectable)
				{
					foreach ($skuBlock['SKU_VALUES_LIST'] as $valueKey => $skuValue)
					{
						$rowData['SKU_BLOCK_LIST'][$blockKey]['SKU_VALUES_LIST'][0]['NOT_AVAILABLE_OFFER'] = true;
					}
				}
				elseif (!isset($rowData['SKU_BLOCK_LIST'][$blockKey + 1]))
				{
					foreach ($skuBlock['SKU_VALUES_LIST'] as $valueKey => $skuValue)
					{
						if ($skuValue['SELECTED'])
						{
							$rowData['SKU_BLOCK_LIST'][$blockKey]['SKU_VALUES_LIST'][$valueKey]['NOT_AVAILABLE_OFFER'] = true;
						}
					}
				}
			}
		}
	}

	if (!empty($result['GRID']['HEADERS']) && is_array($result['GRID']['HEADERS']))
	{
		$skipHeaders = [
			'NAME' => true,
			'QUANTITY' => true,
			'PRICE' => true,
			'PREVIEW_PICTURE' => true,
			'SUM' => true,
			'PROPS' => true,
			'DELETE' => true,
			'DELAY' => true,
		];

		foreach ($result['GRID']['HEADERS'] as &$value)
		{
			if (
				empty($value['id'])
				|| isset($skipHeaders[$value['id']])
				|| ($hideDetailPicture && $value['id'] === 'DETAIL_PICTURE'))
			{
				continue;
			}

			if ($value['id'] === 'DETAIL_PICTURE')
			{
				$value['name'] = Loc::getMessage('SBB_DETAIL_PICTURE_NAME');

				if (!empty($row['DETAIL_PICTURE_SRC']))
				{
					$rowData['COLUMN_LIST'][] = array(
						'CODE' => $value['id'],
						'NAME' => $value['name'],
						'VALUE' => array(
							array(
								'IMAGE_SRC' => $row['DETAIL_PICTURE_SRC'],
								'IMAGE_SRC_2X' => $row['DETAIL_PICTURE_SRC_2X'],
								'IMAGE_SRC_ORIGINAL' => $row['DETAIL_PICTURE_SRC_ORIGINAL'],
								'INDEX' => 0
							)
						),
						'IS_IMAGE' => true,
						'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
					);
				}
			}
			elseif ($value['id'] === 'PREVIEW_TEXT')
			{
				$value['name'] = Loc::getMessage('SBB_PREVIEW_TEXT_NAME');

				if ($row['PREVIEW_TEXT_TYPE'] === 'text' && !empty($row['PREVIEW_TEXT']))
				{
					$rowData['COLUMN_LIST'][] = array(
						'CODE' => $value['id'],
						'NAME' => $value['name'],
						'VALUE' => $row['PREVIEW_TEXT'],
						'IS_PREVIEW' => true,
						'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
					);
				}
			}
			elseif ($value['id'] === 'TYPE')
			{
				$value['name'] = Loc::getMessage('SBB_PRICE_TYPE_NAME');

				if (!empty($row['NOTES']))
				{
					$rowData['COLUMN_LIST'][] = array(
						'CODE' => $value['id'],
						'NAME' => $value['name'],
						'VALUE' => isset($row['~NOTES']) ? $row['~NOTES'] : $row['NOTES'],
						'IS_TEXT' => true,
						'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
					);
				}
			}
			elseif ($value['id'] === 'DISCOUNT')
			{
				$value['name'] = Loc::getMessage('SBB_DISCOUNT_NAME');

				if ($row['DISCOUNT_PRICE_PERCENT'] > 0 && !empty($row['DISCOUNT_PRICE_PERCENT_FORMATED']))
				{
					$rowData['COLUMN_LIST'][] = array(
						'CODE' => $value['id'],
						'NAME' => $value['name'],
						'VALUE' => $row['DISCOUNT_PRICE_PERCENT_FORMATED'],
						'IS_TEXT' => true,
						'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
					);
				}
			}
			elseif ($value['id'] === 'WEIGHT')
			{
				$value['name'] = Loc::getMessage('SBB_WEIGHT_NAME');

				if (!empty($row['WEIGHT_FORMATED']))
				{
					$rowData['COLUMN_LIST'][] = array(
						'CODE' => $value['id'],
						'NAME' => $value['name'],
						'VALUE' => $row['WEIGHT_FORMATED'],
						'IS_TEXT' => true,
						'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
					);
				}
			}
			elseif (!empty($row[$value['id'].'_SRC']))
			{
				$i = 0;

				foreach ($row[$value['id'].'_SRC'] as &$image)
				{
					$image['INDEX'] = $i++;
				}

				$rowData['COLUMN_LIST'][] = array(
					'CODE' => $value['id'],
					'NAME' => $value['name'],
					'VALUE' => $row[$value['id'].'_SRC'],
					'IS_IMAGE' => true,
					'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
				);
			}
			elseif (!empty($row[$value['id'].'_DISPLAY']))
			{
				$rowData['COLUMN_LIST'][] = array(
					'CODE' => $value['id'],
					'NAME' => $value['name'],
					'VALUE' => $row[$value['id'].'_DISPLAY'],
					'IS_TEXT' => true,
					'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
				);
			}
			elseif (!empty($row[$value['id'].'_LINK']))
			{
				$linkValues = array();

				foreach ($row[$value['id'].'_LINK'] as $index => $link)
				{
					$linkValues[] = array(
						'LINK' => $link,
						'IS_LAST' => !isset($row[$value['id'].'_LINK'][$index + 1])
					);
				}

				$rowData['COLUMN_LIST'][] = array(
					'CODE' => $value['id'],
					'NAME' => $value['name'],
					'VALUE' => $linkValues,
					'IS_LINK' => true,
					'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
				);
			}
			elseif (!empty($row[$value['id']]))
			{
				$rowData['COLUMN_LIST'][] = array(
					'CODE' => $value['id'],
					'NAME' => $value['name'],
					'VALUE' => $row[$value['id']],
					'IS_TEXT' => true,
					'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
				);
			}
		}

		unset($value);
	}

	if (!empty($row['LABEL_ARRAY_VALUE'])) {
		$labels = array();

		foreach ($row['LABEL_ARRAY_VALUE'] as $code => $value)
		{
			$labels[] = array(
				'NAME' => $value,
				'HIDE_MOBILE' => !isset($this->arParams['LABEL_PROP_MOBILE'][$code])
			);
		}

		$rowData['SHOW_LABEL'] = true;
		$rowData['LABEL_VALUES'] = $labels;
	}

    if ($arVolume['SHOW']) {
        $arDimensions = unserialize($row['~DIMENSIONS']);

        if (empty($arDimensions['WIDTH']) || empty($arDimensions['HEIGHT']) || empty($arDimensions['LENGTH'])) {
            $arVolume['EXACT'] = false;
        } else {
            $arVolume['VALUE'] += round(($row['QUANTITY'] * $arDimensions['WIDTH'] * $arDimensions['HEIGHT'] * $arDimensions['LENGTH'] / 1000000000), 3);
        }
    }

    $rowData['COLUMN_LIST_SHOW'] = !empty($rowData['COLUMN_LIST']);

	$result['BASKET_ITEM_RENDER_DATA'][] = $rowData;
}

$totalData = [
	'DISABLE_CHECKOUT' => (int)$result['ORDERABLE_BASKET_ITEMS_COUNT'] === 0,
	'PRICE' => $result['allSum'],
	'PRICE_FORMATED' => $result['allSum_FORMATED'],
	'PRICE_WITHOUT_DISCOUNT_FORMATED' => $result['PRICE_WITHOUT_DISCOUNT'],
	'CURRENCY' => $result['CURRENCY'],
    'WEIGHT_FORMATED' => null,
    'VOLUME_FORMATED' => null
];

if ($arVolume['SHOW']) {
    if ($arVolume['VALUE'] <= 0) {
        $arVolume['VALUE_FORMATTED'] = null;
    } else if ($arVolume['EXACT']) {
        $arVolume['VALUE_FORMATTED'] = StringHelper::replaceMacros(
            Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_TOTAL_VOLUME_EXACT'),
            ['VALUE' => $arVolume['VALUE']]
        );
    } else {
        $arVolume['VALUE_FORMATTED'] = StringHelper::replaceMacros(
            Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_TOTAL_VOLUME'),
            ['VALUE' => $arVolume['VALUE']]
        );
    }

    $totalData['VOLUME_FORMATTED'] = $arVolume['VALUE_FORMATTED'];
}

if ($result['DISCOUNT_PRICE_ALL'] > 0) {
	$totalData['DISCOUNT_PRICE_FORMATED'] = $result['DISCOUNT_PRICE_FORMATED'];
}

if ($result['allWeight'] > 0 && $this->arParams['TOTAL_WEIGHT_SHOW'] === 'Y') {
	$totalData['WEIGHT_FORMATED'] = $result['allWeight_FORMATED'];
}

if ($this->priceVatShowValue === 'Y') {
	$totalData['SHOW_VAT'] = true;
	$totalData['VAT_SUM_FORMATED'] = $result['allVATSum_FORMATED'];
	$totalData['SUM_WITHOUT_VAT_FORMATED'] = $result['allSum_wVAT_FORMATED'];
}

if ($this->hideCoupon !== 'Y' && !empty($result['COUPON_LIST'])) {
	$totalData['COUPON_LIST'] = $result['COUPON_LIST'];
	
	foreach ($totalData['COUPON_LIST'] as &$coupon) {
		if ($coupon['JS_STATUS'] === 'ENTERED') {
			$coupon['CLASS'] = 'error';
		} elseif ($coupon['JS_STATUS'] === 'APPLYED') {
			$coupon['CLASS'] = 'success';
		} else {
			$coupon['CLASS'] = 'error';
		}
	}
}

$result['TOTAL_RENDER_DATA'] = $totalData;
unset($arVolume);