<?php
namespace intec\importexport\models\excel;

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\collections\Arrays;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

class Price
{
    private $elementId = null;
    private $defaultCurrency = null;
    private $roundMod = null; // default, up, down
    private $fields = [];
    private $statistic = [
        'update' => [
            'success' => 0,
            'error' => [],
        ],
        'add' => [
            'success' => 0,
            'error' => [],
        ],
        'delete' => [
            'success' => 0,
            'error' => [],
        ],
        'hasPrice' => false,
        'hasQuantity' => false,
    ];

    public function __construct($elementId = null, $defaultCurrency = null)
    {
        if (!empty($elementId))
            $this->elementId = $elementId;

        if (Loader::includeModule('sale')) {
            if (empty($defaultCurrency))
                $this->defaultCurrency = \CCurrency::GetBaseCurrency();
            else
                $this->defaultCurrency = $defaultCurrency;
        }
    }

    private function setElementId ($id)
    {
        if (!empty($id))
            $this->elementId = $id;
    }

    public function getElementId ()
    {
        return $this->elementId;
    }

    private function setFields ($fields)
    {
        if (!empty($fields) && Type::isArray($fields))
            $this->fields = $fields;
    }

    public function getFields ()
    {
        return $this->fields;
    }

    public function setStatistic ($data, $field = '')
    {
        if (empty($data))
            return;

        if (empty($field)) {
            $this->statistic = $data;
        } else {
            $this->statistic[$field] = $data;
        }
    }

    public function getStatistic ($field = '')
    {
        if (!empty($field))
            return $this->statistic[$field];
        else
            return $this->statistic;
    }

    public function setCurrency ($data)
    {
        if (empty($data) || Type::isArray($data))
            $this->defaultCurrency = \CCurrency::GetBaseCurrency();

        $this->defaultCurrency = $data;
    }

    public function getCurrency ()
    {
        return $this->defaultCurrency;
    }

    public function setRoundMod ($data = null)
    {
        $mods = ['default', 'up', 'down'];

        if (empty($data) || !ArrayHelper::isIn($data, $mods))
            $this->roundMod = null;
        else
            $this->roundMod = $data;
    }

    public function getRoundMod ()
    {
        return $this->roundMod;
    }

    public function getPrices($elementId, $siteId, $prefix = '')
    {
        global $USER;
        $result = [];

        $pricePrefix = $prefix . 'CATALOG_PROPERTY_PRICE';
        $prices = Arrays::fromDBResult(\CPrice::GetList([],['PRODUCT_ID' => $elementId]))->asArray();

        if(empty($prices))
            return null;

        $discounts = \CCatalogDiscount::GetDiscountByProduct($elementId, $USER->GetUserGroupArray(), 'N', [], $siteId);

        foreach ($prices as $price) {
            if (!empty($price['QUANTITY_FROM']))
                continue;

            $key = $pricePrefix . $price['CATALOG_GROUP_ID'];
            $result[$key . '_PRICE'] = $price['PRICE'];
            $result[$key . '_CURRENCY'] = $price['CURRENCY'];
            $result[$key . '_PRICE_DISCOUNT'] = \CCatalogProduct::CountPriceWithDiscount($price['PRICE'], $price['CURRENCY'], $discounts);

            if ($price['BASE'] === 'N') {
                $extra = Arrays::fromDBResult(\CExtra::GetList([], ['ID' => $price['EXTRA_ID']], false, false, []))->asArray();
                $extra = ArrayHelper::getFirstValue($extra);
                $result[$key . '_EXTRA'] = $extra['PERCENTAGE'];
                $result[$key . '_EXTRA_NAME'] = $extra['NAME'];
                $result[$key . '_EXTRA_ID'] = $extra['ID'];
            }
        }

        return $result;
    }

    public function getRangePrice ($elementId, $priceCodePrefix = '', $priceCodePostfix = '')
    {
        if (empty($elementId))
            return null;

        $result = null;
        $rangePrices = [];

        Arrays::fromDBResult(\CPrice::GetList(
            [],
            ['PRODUCT_ID' => $elementId]
        ))->asArray(function ($index, $range) use (&$rangePrices) {
            $rangePrices['PRICE' . $range['CATALOG_GROUP_ID']][] = $range;
        });

        foreach ($rangePrices as $priceCode => $rangePrice) {
            $sPrice = null;

            foreach ($rangePrice as $price) {
                $sPrice = $sPrice . $price['QUANTITY_FROM'] .
                    ':' . $price['QUANTITY_TO'] .
                    ':' . $price['PRICE'] .
                    ':' . $price['CURRENCY'] . ';';
            }

            $result[$priceCodePrefix . $priceCode . $priceCodePostfix] =  $sPrice;
        }

        return $result;
    }

    public function prepareFields ($elementId, $fields, $setFields = true)
    {

        if (empty($fields) || empty($elementId))
            return $fields;

        $result = [];

        foreach ($fields as $key => $field) {
            if (!StringHelper::startsWith($key, 'CATALOG_PROPERTY_'))
                continue;

            $fieldResult = self::dePrefix($key);
            $priceId = $fieldResult['priceId'];
            $key = $fieldResult['property'];

            if (!empty($priceId)) {
                if ($key === 'PRICE_EXT') {
                    $result['group'][$priceId][$key] = self::extraPriceToArray($field, $priceId, $elementId);
                } else {
                    if ($key === 'PRICE')
                        $field = $this->roundPrice($field);

                    $result['group'][$priceId][$key] = $field;
                }
            } else {
                if ($key === 'QUANTITY_TRACE' ||
                    $key === 'CAN_BUY_ZERO' ||
                    $key === 'NEGATIVE_AMOUNT_TRACE' ||
                    $key === 'SUBSCRIBE') {
                    $result['nonGroup'][$key] = self::convertLogic($field);
                } else {
                    if ($key === 'PRICE')
                        $field = $this->roundPrice($field);

                    $result['nonGroup'][$key] = $field;
                }
            }
        }

        if ($setFields)
            self::setFields($result);

        return $result;
	}

	private function convertLogic ($property)
    {
        $property = StringHelper::toLowerCase($property);

        if ($property === 'true' || $property === 1 || $property === '1' || $property == 'y' || $property == 'yes')
            return 'Y';
        elseif ($property === 'false' || $property === 0 || $property === '0' || $property == 'n' || $property == 'no' || $property == 'not')
            return 'N';
        else
            return 'D';
    }

    /**
     * Убирает префикс каталога
     * Возвращает массив вида [priceId, property]
     *
     * @param $key
     * @return array
     */
    private function dePrefix ($key)
    {
        if (empty($key))
            return $key;

        $priceId = null;
        $prefixLength = StringHelper::length('CATALOG_PROPERTY_');
        $key = StringHelper::cut($key, $prefixLength);

        if (StringHelper::startsWith($key, 'PRICE')) {
            $prefixLength = StringHelper::length('PRICE');
            $key = StringHelper::cut($key, $prefixLength);
            $priceId = ArrayHelper::getFirstValue(StringHelper::explode($key, '_'));
            $prefixLength = StringHelper::length($priceId . '_');
            $key = StringHelper::cut($key, $prefixLength);
        }

        return ['priceId' => $priceId, 'property' => $key];
    }

    private function extraPriceToArray ($price, $priceId, $productId)
    {
        if (empty($price) || empty($priceId) || empty($productId))
            return $price;

        $result = [];
        $price = StringHelper::explode($price, ';');

        foreach ($price as $item) {
            $item = StringHelper::explode($item, ':');
            $result[] = [
                'PRODUCT_ID' => $productId,
                'CATALOG_GROUP_ID' => $priceId,
                'QUANTITY_FROM' => !empty($item[0]) ? $item[0] : false,
                'QUANTITY_TO' => !empty($item[1]) ? $item[1] : false,
                'PRICE' => $this->roundPrice($item[2]),
                'CURRENCY' => $item[3]
            ];
        }

        return $result;
    }

    public function updateCatalogProperties($elementId)
    {
        if (!empty($this->fields['nonGroup']['QUANTITY']))
            $this->fields['nonGroup']['QUANTITY'] = Type::toFloat($this->fields['nonGroup']['QUANTITY']);

        if (!empty($this->fields['nonGroup']['QUANTITY_RESERVED']))
            $this->fields['nonGroup']['QUANTITY_RESERVED'] = Type::toFloat($this->fields['nonGroup']['QUANTITY_RESERVED']);

        $fields = $this->fields['nonGroup'];

        if (!empty($fields['MEASURE'])) {
            $measures = Arrays::fromDBResult(\CCatalogMeasure::getList())->asArray();

            foreach ($measures as $measure) {
                if ($measure['MEASURE_TITLE'] === $fields['MEASURE']) {
                    $fields['MEASURE'] = $measure['ID'];
                    break;
                }
            }
        }

        $result = \CCatalogProduct::Update($elementId, $fields);

        if ($result) {
            $quantity = Type::toFloat($this->fields['nonGroup']['QUANTITY']);

            if (!empty($quantity))
                $this->statistic['hasQuantity'] = true;
            else
                $this->statistic['hasQuantity'] = false;
        }

        unset($quantity, $measure, $measures);

        return $result;
    }

    public function updateVatProperties($elementId)
    {
        if (empty($elementId))
            return false;

        $result = false;
        $fields = [];

        if (!empty($this->fields['nonGroup']['VAT'])) {
            $vats = \CCatalogVat::GetListEx([], ['ACTIVE' => 'Y', 'RATE' => $this->fields['nonGroup']['VAT']]);
            $vats = Arrays::fromDBResult($vats)->asArray();
            $vat = ArrayHelper::getFirstValue($vats);

            if (!empty($vat))
                $fields['VAT_ID'] = $vat['ID'];

            unset($vats, $vat);
        }

        /** @todo rework */
        if (!empty($this->fields['nonGroup']['VAT_INCLUDED'])) {
            $words = iconv('UTF8', 'CP1251', 'Да,да,True,true,Yes,yes,Y,y,1');
            $words = explode(',', $words);

            if (ArrayHelper::isIn($this->fields['nonGroup']['VAT_INCLUDED'], $words))
                $fields['VAT_INCLUDED'] = 'Y';

            $words = 'Да,да,True,true,Yes,yes,Y,y,1';
            $words = explode(',', $words);

            if (ArrayHelper::isIn($this->fields['nonGroup']['VAT_INCLUDED'], $words))
                $fields['VAT_INCLUDED'] = 'Y';

            $words = iconv('UTF8', 'CP1251', 'Нет,нет,False,false,No,no,N,n,0');
            $words = explode(',', $words);

            if (ArrayHelper::isIn($this->fields['nonGroup']['VAT_INCLUDED'], $words))
                $fields['VAT_INCLUDED'] = 'N';

            $words = 'Нет,нет,False,false,No,no,N,n,0';
            $words = explode(',', $words);

            if (ArrayHelper::isIn($this->fields['nonGroup']['VAT_INCLUDED'], $words))
                $fields['VAT_INCLUDED'] = 'N';

            unset($words);
        }

        if (!empty($fields))
            $fields['ID'] = $elementId;
        else
            return false;
        
        $result = \CCatalogProduct::Add($fields);

        return $result;
    }

    public function updateRatio($elementId)
    {
        if (!empty($this->fields['nonGroup']['MEASURE_RATIO'])) {
            $elementRatioID = \CCatalogMeasureRatio::getList([], ["PRODUCT_ID" => $elementId], false , false);
            $elementRatioID = $elementRatioID->Fetch();
            $elementRatioID = $elementRatioID['ID'];
            $ratio = Type::toFloat($this->fields['nonGroup']['MEASURE_RATIO']);

            if (!empty($ratio))
                $elementRatio = \CCatalogMeasureRatio::update($elementRatioID, ['RATIO' => $ratio]);
        }
    }

    public function getPriceIds ($elementIds)
    {
        return Arrays::fromDBResult(\CPrice::GetList([],["PRODUCT_ID" => $elementIds]))
            ->asArray(function ($index, $element){
                return [
                    'value' => $element['ID']
                ];
            });
    }

    public function updatePrice($elementId, $prices = [], $recalc = false)
    {
        $result = [];

        if (empty($prices))
            $prices = Arrays::fromDBResult(\CPrice::GetList([],["PRODUCT_ID" => $elementId]))->asArray();

        $needUpdate = self::isUpdatePrice($prices);

        if (empty($needUpdate))
            return false;

        $this->statistic['hasPrice'] = false;

        if (!empty($prices)) {
            foreach ($needUpdate as $groupId) {
                foreach ($prices as $price) {
                    if ($price['CATALOG_GROUP_ID'] == $groupId)
                        self::delete($price['ID']);
                }
            }
        }

        foreach ($this->fields['group'] as $key => $fields) {
            if (empty($fields['CATALOG_GROUP_ID']))
                $fields['CATALOG_GROUP_ID'] = $key;

            if (empty($fields['PRODUCT_ID']))
                $fields['PRODUCT_ID'] = $elementId;

            if (empty($fields['CURRENCY']))
                $fields['CURRENCY'] = $this->defaultCurrency;

            if (!empty($fields['PRICE_EXT'])) {
                foreach ($fields['PRICE_EXT'] as $item) {
                    $result[$key]['PRICE_EXT'][] = self::add($item);
                }
            } else {
                $result[$key] = self::add($fields);
            }
        }

        return $result;
    }

    private function isUpdatePrice ($prices)
    {
        $updateGroupId = [];

        if (!empty($this->fields['group'])) {
            foreach ($this->fields['group'] as $key => $group) {
                if (!empty($group['PRICE_EXT'])) {
                    foreach ($group['PRICE_EXT'] as $item) {
                        $isSame = false;
                        $isSame = self::compareArray($prices, $item);

                        if (!$isSame) {
                            $updateGroupId[] = $key;
                        }
                    }
                } else {
                    $isSame = false;
                    $isSame = self::compareArray($prices, $group);

                    if (!$isSame) {
                        $updateGroupId[] = $key;
                    }
                }
            }
        }

        return $updateGroupId;
    }

    private function compareArray ($prices, $fields)
    {
        $isSame = false;

        foreach ($prices as $price) {
            if ($price['QUANTITY_FROM'] == $fields['QUANTITY_FROM'] &&
                $price['QUANTITY_TO'] == $fields['QUANTITY_TO'] &&
                $price['PRICE'] == $fields['PRICE'] &&
                $price['CATALOG_GROUP_ID'] == $fields['CATALOG_GROUP_ID'] &&
                $price['PRODUCT_ID'] == $fields['PRODUCT_ID']) {

                if (!empty($price['PRICE']))
                    $this->statistic['hasPrice'] = true;

                $isSame = true;
                break;
            }
        }

        return $isSame;
    }

    public function add($fields, $recalc = false)
    {
        if (empty($fields))
            return false;

        $result = \CPrice::Add($fields, $recalc);

        if (!$result) {
            $this->statistic['add']['error'][] = [
                'fields' => $fields
            ];
        } else {
            $this->statistic['add']['success']++;

            if (!empty($fields['PRICE']))
                $this->statistic['hasPrice'] = true;
        }

        return $result;
    }

    public function update($id, $fields, $recalc = false)
    {
        if (empty($id) || empty($fields))
            return false;

        $result = \CPrice::Update($id, $fields, $recalc);

        if (!$result) {
            $this->statistic['update']['error'][] = [
                'id' => $id,
                'fields' => $fields
            ];
        } else {
            $this->statistic['update']['success']++;

            if (!empty($fields['PRICE']))
                $this->statistic['hasPrice'] = true;
        }

        return $result;
    }

    public function delete($id)
    {
        if (empty($id))
            return false;

        $result = \CPrice::Delete($id);

        if (!$result) {
            $this->statistic['delete']['error'][] = [
                'id' => $id
            ];
        } else {
            $this->statistic['delete']['success']++;
        }

        return $result;
    }

    public function deleteByProductId($ids)
    {
        if (empty($ids))
            return false;

        $result = [];

        if (Type::isArray($ids)) {
            foreach ($ids as $id) {
                $result = \CPrice::DeleteByProduct($id);

                if (!$result) {
                    $this->statistic['delete']['error'][] = [
                        'id' => $id
                    ];
                    return false;
                } else {
                    $this->statistic['delete']['success']++;
                }
            }
        }

        return true;
    }

    public function roundPrices ($prices)
    {
        if (empty($prices))
            return null;

        if (empty($this->roundMod))
            return $prices;

        if (Type::isArray($prices)) {
            foreach ($prices as &$price) {
                $price = $this->roundPrice($price);
            }
        } else {
            return $this->roundPrice($prices);
        }

        return $prices;
    }

    public function roundPrice ($price)
    {
        if (empty($price))
            return null;

        if (empty($this->roundMod))
            return $price;

        if (Type::isString($price))
            $price = str_replace(',', '.', $price);

        if (!Type::isFloat($price)) {
            $price = Type::toFloat($price);

            if (empty($price))
                return null;
        }

        if ($this->roundMod === 'default')
            return round($price);
        elseif ($this->roundMod === 'up')
            return ceil($price);
        elseif ($this->roundMod === 'down')
            return floor($price);
        else
            return $price;



    }
}