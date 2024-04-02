<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use Bitrix\Iblock\Model\PropertyFeature;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 */

$arFields = [
    'ARTICLE' => [
        'SHOW' => false,
        'VALUE' => null
    ],
    'BRAND' => [
        'SHOW' => false,
        'VALUE' => []
    ],
    'MARKS' => [
        'SHOW' => false,
        'HIT' => 'N',
        'NEW' => 'N',
        'RECOMMEND' => 'N',
        'SHARE' => 'N'
    ],
    'DOCUMENTS' => [
        'SHOW' => false,
        'VALUES' => []
    ],
    'VIDEO' => [
        'SHOW' => false,
        'VALUES' => []
    ],
    'REVIEWS' => [
        'SHOW' => false,
        'VALUES' => []
    ],
    'ARTICLES' => [
        'SHOW' => false,
        'VALUES' => []
    ],
    'ADDITIONAL' => [
        'SHOW' => false,
        'VALUES' => []
    ],
    'ASSOCIATED' => [
        'SHOW' => false,
        'VALUES' => []
    ],
    'RECOMMENDED' => [
        'SHOW' => false,
        'VALUES' => []
    ],
    'ACCESSORIES' => [
      'SHOW' => false,
      'VALUES' => []
    ],
    'SERVICES' => [
        'SHOW' => false,
        'VALUES' => []
    ],
    'OFFERS' => []
];
$arProperty = [];

/** Артикул */
if (!empty($arParams['PROPERTY_ARTICLE'])) {
    $arProperty = ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_ARTICLE'],
        'VALUE'
    ]);

    if (!empty($arProperty)) {
        if (Type::isArray($arProperty))
            $arProperty = ArrayHelper::getFirstValue($arProperty);

        $arFields['ARTICLE']['VALUE'] = $arProperty;
    }

    if (!empty($arFields['ARTICLE']['VALUE'])) {
        $arFields['ARTICLE']['SHOW'] = $arVisual['ARTICLE']['SHOW'];
    } else {
        $arVisual['ARTICLE']['SHOW'] = false;
        $arFields['ARTICLE']['SHOW'] = false;
    }
}

/** Бренд */
if (!empty($arParams['PROPERTY_BRAND'])) {
    $arProperty = ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_BRAND'],
        'VALUE'
    ]);

    if (!empty($arProperty)) {
        if (Type::isArray($arProperty))
            $arProperty = ArrayHelper::getFirstValue($arProperty);

        $arProperty = CIBlockElement::GetByID($arProperty);
        $arProperty->SetUrlTemplates('', '', '');
        $arProperty = $arProperty->GetNext();

        if (!empty($arProperty)) {
            $arBrandPicture = null;

            if (!empty($arProperty['PREVIEW_PICTURE']))
                $arBrandPicture = $arProperty['PREVIEW_PICTURE'];
            else if (!empty($arProperty['DETAIL_PICTURES']))
                $arBrandPicture = $arProperty['PREVIEW_PICTURE'];

            if (!empty($arBrandPicture)) {
                $arBrandPicture = Arrays::fromDBResult(CFile::GetByID($arBrandPicture))->asArray();

                if (!empty($arBrandPicture)) {
                    $arBrandPicture = ArrayHelper::getFirstValue($arBrandPicture);
                    $arBrandPicture['SRC'] = CFile::GetFileSRC($arBrandPicture);
                }
            }

            $sBrandText = null;

            if (!empty($arProperty['PREVIEW_TEXT']))
                $sBrandText = $arProperty['PREVIEW_TEXT'];
            else if (!empty($arProperty['DETAIL_TEXT']))
                $sBrandText = $arProperty['DETAIL_TEXT'];

            $arProperty = [
                'NAME' => $arProperty['NAME'],
                'PICTURE' => $arBrandPicture,
                'TEXT' => $sBrandText,
                'URL' => [
                    'DETAIL' => $arProperty['DETAIL_PAGE_URL'],
                    'LIST' => $arProperty['LIST_PAGE_URL']
                ]
            ];

            $arFields['BRAND']['VALUE'] = $arProperty;

            unset($arBrandPicture, $sBrandText);
        }
    }

    if (!empty($arFields['BRAND']['VALUE']))
        $arFields['BRAND']['SHOW'] = $arVisual['BRAND']['SHOW'];
}

/** Таблица размеров */
if (!empty($arParams['PROPERTY_SIZES_SHOW'])) {
    $arProperty = ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_SIZES_SHOW']
    ]);

    if (!empty($arProperty)) {
        $arProperty = CIBlockFormatProperties::GetDisplayValue(
            $arResult,
            $arProperty,
            false
        );

        if ($arResult['SIZES']['SHOW'] && $arResult['SIZES']['MODE'] === 'element')
            $arResult['SIZES']['SHOW'] = !empty($arProperty['DISPLAY_VALUE']);
    }
}

/** Файлы */
if (!empty($arParams['PROPERTY_DOCUMENTS'])) {
    $arProperty = ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_DOCUMENTS'],
        'VALUE'
    ]);

    if (!empty($arProperty)) {
        if (!Type::isArray($arProperty))
            $arProperty[] = $arProperty;

        $arProperty = Arrays::fromDBResult(CFile::GetList(['SORT' => 'ASC'], [
            '@ID' => implode(',', $arProperty)
        ]))->indexBy('ID');

        if (!$arProperty->isEmpty()) {
            $arProperty = $arProperty->asArray(function ($key, $arFile) {
                $arFile['SRC'] = CFile::GetFileSRC($arFile);

                return [
                    'key' => $key,
                    'value' => $arFile
                ];
            });

            $arFields['DOCUMENTS']['VALUES'] = $arProperty;
        }
    }

    if (!empty($arFields['DOCUMENTS']['VALUES']))
        $arFields['DOCUMENTS']['SHOW'] = $arVisual['DOCUMENTS']['SHOW'];
}

/** Метки товара */
$arProperties = [
    'HIT',
    'NEW',
    'RECOMMEND',
    'SHARE'
];

foreach ($arProperties as $sProperty) {
    if (!empty($arParams['PROPERTY_MARKS_'.$sProperty])) {
        $arProperty = ArrayHelper::getValue($arResult, [
            'PROPERTIES',
            $arParams['PROPERTY_MARKS_'.$sProperty],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            $arFields['MARKS']['VALUES'][$sProperty] = 'Y';

            if (!$arFields['MARKS']['SHOW'] && $arVisual['MARKS']['SHOW'])
                $arFields['MARKS']['SHOW'] = true;
        }
    }
}

/** Свойства множественной привязки */
$arProperties = [
    'VIDEO',
    'REVIEWS',
    'ARTICLES',
    'ADDITIONAL',
    'ASSOCIATED',
    'ACCESSORIES',
    'RECOMMENDED',
    'SERVICES'
];

foreach ($arProperties as $sProperty) {
    if (!empty($arParams['PROPERTY_'.$sProperty])) {
        $arProperty = ArrayHelper::getValue($arResult, [
            'PROPERTIES',
            $arParams['PROPERTY_'.$sProperty],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arFields[$sProperty]['VALUES'] = $arProperty;
            else
                $arFields[$sProperty]['VALUES'][] = $arProperty;
        }

        if (!empty($arFields[$sProperty]['VALUES']))
            $arFields[$sProperty]['SHOW'] = $arVisual[$sProperty]['SHOW'];
    }
}

if (!empty($arResult['OFFERS'])) {
    if  (!empty($arParams['OFFERS_PROPERTY_ARTICLE'])) {
        foreach ($arResult['OFFERS'] as &$arOffer) {

            $arOffer['FIELDS']['ARTICLE']['VALUE'] = ArrayHelper::getValue($arOffer, [
                'PROPERTIES',
                $arParams['OFFERS_PROPERTY_ARTICLE'],
                'VALUE'
            ]);

            if (!empty($arOffer['FIELDS']['ARTICLE']['VALUE'])) {
                $arOffer['FIELDS']['ARTICLE']['SHOW'] = true;

                if (!$arFields['ARTICLE']['SHOW'])
                    $arFields['ARTICLE']['SHOW'] = true;
            }

            unset($arOffer);
        }
    } else {
        if ($arFields['ARTICLE']['SHOW'])
            $arFields['ARTICLE']['SHOW'] = false;
    }

    if ($arResult['SKU']['VIEW'] == 'list') {
        $arFields['ARTICLE']['SHOW'] = false;
    }

    $arOffersProperties = [];

    if ($arVisual['OFFERS']['PROPERTIES']['SHOW']) {
        $iOfferIblockId = ArrayHelper::getValue(current($arResult['OFFERS']), ['IBLOCK_ID']);
        $arOfferProperties = null;

        if (!empty($iOfferIblockId))
            $arOfferProperties = PropertyFeature::getDetailPageShowProperties($iOfferIblockId);

        if (empty($arOfferProperties) && !empty($arParams['OFFERS_PROPERTY_CODE']))
            $arOfferProperties = $arParams['OFFERS_PROPERTY_CODE'];

        if (!empty($arOfferProperties)) {

            foreach ($arResult['OFFERS'] as $arOffer) {
                foreach ($arOffer['PROPERTIES'] as $sKey => $arProperty) {
                    if (empty($arProperty['VALUE']))
                        continue;

                    $sValue = null;

                    foreach ($arOfferProperties as $arOfferProperty) {
                        if ($arOfferProperty === $arProperty['ID'] || $arOfferProperty === $arProperty['CODE']) {
                            if (Type::isArray($arProperty['VALUE'])) {
                                if ($arProperty['USER_TYPE'] === 'HTML') {

                                    $arValue = CIBlockFormatProperties::GetDisplayValue($arOffer, $arProperty, null);

                                    if (Type::isArray($arValue['DISPLAY_VALUE'])) {
                                        foreach ($arValue['DISPLAY_VALUE'] as $arValueItem) {
                                            if (empty($sValue))
                                                $sValue = $arValueItem;
                                            else
                                                $sValue = $sValue . $arVisual['OFFERS']['PROPERTIES']['DELIMITER'] . ' ' . $arValueItem;
                                        }
                                    } else {
                                        $sValue = $arValue['DISPLAY_VALUE'];
                                    }
                                } else {
                                    foreach ($arProperty['VALUE'] as $sPropertyItem) {
                                        if (empty($sValue))
                                            $sValue = $sPropertyItem;
                                        else
                                            $sValue = $sValue . $arVisual['OFFERS']['PROPERTIES']['DELIMITER'] . ' ' . $sPropertyItem;
                                    }
                                }
                            } else {
                                $sValue = $arProperty['VALUE'];
                            }

                            $arOffersProperties[$arOffer['ID']][$sKey]['ID'] = $arProperty['ID'];
                            $arOffersProperties[$arOffer['ID']][$sKey]['NAME'] = $arProperty['NAME'];
                            $arOffersProperties[$arOffer['ID']][$sKey]['VALUE'] = $sValue;

                            break;
                        }
                    }

                    unset($arOfferProperty);
                }

                unset($arProperty, $sValue);
            }

            unset($arOffer);
        }

        unset($iOfferIblockId, $arOfferProperties);
    }

    if (!empty($arOffersProperties))
        $arFields['OFFERS'] = $arOffersProperties;
}

$arResult['FIELDS'] = $arFields;

unset($arFields, $arProperty, $arOffersProperties);