<?php

namespace intec\core\bitrix\iblock\helpers;

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * Class Catalog
 * @package intec\core\bitrix\iblock\helpers
 * @deprecated
 */
class Catalog
{
    /**
     * Возвращает массив свойств предложений товара
     * @param array $SKUProperties
     * @return array
     */
    public static function getSKUProperties($SKUProperties)
    {
        $result = [];

        foreach ($SKUProperties as $SKUProperty) {
            $property = [
                'id' => $SKUProperty['ID'],
                'code' => 'P_'.$SKUProperty['CODE'],
                'name' => $SKUProperty['NAME'],
                'type' => $SKUProperty['SHOW_MODE'] === 'TEXT' ? 'text' : 'picture',
                'values' => []
            ];

            foreach ($SKUProperty['VALUES'] as $value) {
                $property['values'][$value['ID']] = [
                    'id' => !empty($value['XML_ID']) ? $value['XML_ID'] : $value['ID'],
                    'name' => $value['NAME'],
                    'stub' => $value['NA'] == 1,
                    'picture' => !empty($value['PICT']) ? $value['PICT']['SRC'] : null
                ];
            }

            $result[] = $property;
        }

        return $result;
    }

    /**
     * Возвращает минимальную цену предложений товара
     * @param array $offers
     * @return array
     */
    public static function getMinimalOffersPrices($offers)
    {
        $result = [
            'MIN_PRICE' => null,
            'ITEM_PRICES' => null
        ];

        foreach ($offers as &$offer) {
            if (!empty($offer['ITEMS_PRICES'])) {
                if ($result['ITEM_PRICES'] === null || $result['ITEM_PRICES'][0]['PRICE'] > $offer['ITEM_PRICES'][0]['PRICE']) {
                    $result['ITEM_PRICES'] = $offer['ITEM_PRICES'];
                }
            }

            if (!empty($offer['MIN_PRICE'])) {
                if ($result['MIN_PRICE'] === null || $result['MIN_PRICE']['DISCOUNT_VALUE'] > $offer['MIN_PRICE']['DISCOUNT_VALUE']) {
                    $result['MIN_PRICE'] = $offer['MIN_PRICE'];
                }
            }
        }

        return $result;
    }

    /**
     * Возвращает индексированный список типов цен товара
     * @param array $prices
     * @return array
     */
    public static function getPriceTypes($prices)
    {
        $result = [];

        if (empty($prices) || !Type::isArray($prices)) {
            return $result;
        }

        foreach ($prices as $price) {
            $result[$price['ID']] = $price;
        }

        return $result;
    }

    /**
     * Получает свойство для указанной цены из списка
     * @param string $key
     * @param int|string $priceId
     * @param array $prices
     * @return mixed
     */
    public static function getPriceProperty($key, $priceId, $prices)
    {
        if (
            ArrayHelper::keyExists($priceId, $prices) &&
            Type::isArray($prices[$priceId]) &&
            ArrayHelper::keyExists($key, $prices[$priceId])
        ) {
            return $prices[$priceId][$key];
        }

        return null;
    }

    /**
     * Устанавливает указанные свойства для цены из списка цен
     * @param array $price
     * @param array $prices
     * @param string|array $properties
     * @return bool|array|mixed
     */
    public static function setPriceProperties($price, $prices, $properties)
    {
        if (
            empty($price) || empty($prices) || empty($properties) ||
            !Type::isArray($price) || !Type::isArray($prices) ||
            !ArrayHelper::keyExists('ID', $price) || !ArrayHelper::keyExists($price['ID'], $prices)
        ) {
            return false;
        }

        $prices = $prices[$price['ID']];

        if (Type::isArray($properties)) {
            $applied = false;

            foreach ($properties as $property) {
                if (!ArrayHelper::keyExists($property, $prices)) {
                    continue;
                }

                $price[$property] = $prices[$property];

                if (!$applied) {
                    $applied = true;
                }
            }

            if (!$applied) {
                return false;
            }
        } else {
            if (ArrayHelper::keyExists($properties, $prices)) {
                $price[$properties] = $prices[$properties];
            }
        }

        return $price;
    }
}