<?php
namespace intec\seo\filter\condition;

use CCatalogGroup;
use CIBlockSection;
use CIBlockElement;
use CIBlockProperty;
use CIBlockPropertyEnum;
use CCatalogSku;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity\Query;
use Bitrix\Highloadblock\HighloadBlockTable;
use intec\core\base\Condition;
use intec\core\bitrix\conditions\CatalogPriceCondition;
use intec\core\bitrix\conditions\IBlockPropertyCondition;
use intec\core\bitrix\conditions\IBlockSectionCondition;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\net\Url;
use intec\seo\filter\conditions\CatalogPriceFilteredMinimalCondition;
use intec\seo\filter\conditions\CatalogPriceFilteredMaximalCondition;

class FilterHelper
{
    /**
     * Кешированные свойства.
     * @var array
     */
    protected static $_properties = [];

    /**
     * Кешированные значения свойств типа список.
     * @var array
     */
    protected static $_propertiesEnums = [];

    /**
     * Кешированные разделы.
     * @var array
     */
    protected static $_sections = [];

    /**
     * Кешированные элементы.
     * @var array
     */
    protected static $_elements = [];

    /**
     * Кешированные типы цен.
     * @var array
     */
    protected static $_prices = [];

    /**
     * Собирает комбинации для фильтра из комбинаций условий.
     * @param array $combinations Комбинации условий.
     * @param boolean $strict Страя сборка. Будет собирать комбинацию только если все свойства подходят для фильтра.
     * @param boolean $reset Сбросить кеш.
     * @return array
     */
    public static function getCombinations($combinations, $strict = true, $reset = false)
    {
        $result = [];

        foreach ($combinations as $combination) {
            $objects = self::getCombination($combination, $strict, $reset);

            if (!empty($objects))
                $result[] = $objects;
        }

        return $result;
    }

    /**
     * Собирает комбинацию для фильтра из комбинации условий.
     * @param array $combination Комбинация условий.
     * @param boolean $strict Страя сборка. Будет собирать комбинацию только если все свойства подходят для фильтра.
     * @param boolean $reset Сбросить кеш.
     * @return array
     */
    public static function getCombination($combination, $strict = true, $reset = false)
    {
        $result = [];

        foreach ($combination as $condition) {
            $object = self::getCombinationObject($condition, $reset);

            if (!empty($object)) {
                $result[] = $object;
            } else if ($strict) {
                return null;
            }
        }

        return $result;
    }

    /**
     * Получает объект комбинации из условия.
     * @param Condition $condition
     * @param boolean $reset Сбросить кеш.
     * @return array|null
     */
    public static function getCombinationObject($condition, $reset = false)
    {
        if ($reset) {
            self::$_properties = [];
            self::$_propertiesEnums = [];
            self::$_sections = [];
        }

        /** Если условие - свойство */
        if ($condition instanceof IBlockPropertyCondition) {
            if (empty($condition->value) && !Type::isNumeric($condition->value))
                return null;

            if (!ArrayHelper::keyExists($condition->id, self::$_properties))
                self::$_properties[$condition->id] = CIBlockProperty::GetByID($condition->id)->Fetch();

            $property = self::$_properties[$condition->id];

            if (empty($property))
                return null;

            $property['CONDITION'] = $condition;
            $property['VALUE'] = null;

            /** Если свойство - список */
            if ($property['PROPERTY_TYPE'] === 'L' && empty($property['USER_TYPE']) && !Type::isNumeric($property['USER_TYPE'])) {
                if (!ArrayHelper::keyExists($condition->value, self::$_propertiesEnums))
                    self::$_propertiesEnums[$condition->value] = CIBlockPropertyEnum::GetList([], [
                        'ID' => $condition->value
                    ])->Fetch();

                $value = self::$_propertiesEnums[$condition->value];

                if (!empty($value))
                    $property['VALUE'] = [
                        'ID' => $value['ID'],
                        'HASH' => $value['ID'],
                        'TEXT' => $value['VALUE'],
                        'URL' => $value['XML_ID']
                    ];
            } else if ($property['PROPERTY_TYPE'] === 'S' && $property['USER_TYPE'] === 'directory') { /** Если свойство - справочник */
                try {
                    $entity = null;
                    $block = HighloadBlockTable::getList([
                        'filter' => [
                            'TABLE_NAME' => $property['USER_TYPE_SETTINGS']['TABLE_NAME']
                        ]
                    ])->fetch();

                    if (!empty($block))
                        $entity = HighloadBlockTable::compileEntity($block);

                    if (!empty($entity)) {
                        $query = new Query($entity);
                        $query->setSelect(['*']);
                        $query->setFilter([
                            'UF_XML_ID' => $condition->value
                        ]);

                        $query->setLimit(1);

                        $value = $query->exec()->fetch();

                        if (!empty($value))
                            $property['VALUE'] = [
                                'ID' => $value['UF_XML_ID'],
                                'HASH' => $value['UF_XML_ID'],
                                'TEXT' => !empty($value['UF_NAME']) ? $value['UF_NAME'] : $value['UF_XML_ID'],
                                'URL' => $value['UF_XML_ID']
                            ];
                    }
                } catch (\Exception $exception) {}
            } else if ($property['PROPERTY_TYPE'] === 'G') { /** Если свойство - раздел */
                if (!ArrayHelper::keyExists($condition->value, self::$_sections))
                    self::$_sections[$condition->value] = CIBlockSection::GetList([], [
                        'ID' => $condition->value
                    ], false, [], [
                        'nPageSize' => 1
                    ])->Fetch();

                $value = self::$_sections[$condition->value];

                if (!empty($value))
                    $property['VALUE'] = [
                        'ID' => $value['ID'],
                        'HASH' => $value['ID'],
                        'TEXT' => str_repeat('.', $value['DEPTH_LEVEL']).$value['NAME'],
                        'URL' => !empty($value['CODE']) ? $value['CODE'] : str_repeat('.', $value['DEPTH_LEVEL']).$value['NAME']
                    ];
            } else if ($property['PROPERTY_TYPE'] === 'E') { /** Если свойство - элемент */
                if (!ArrayHelper::keyExists($condition->value, self::$_elements))
                    self::$_elements[$condition->value] = CIBlockElement::GetList([], [
                        'ID' => $condition->value
                    ], false, [
                        'nPageSize' => 1
                    ])->Fetch();

                $value = self::$_elements[$condition->value];

                if (!empty($value))
                    $property['VALUE'] = [
                        'ID' => $value['ID'],
                        'HASH' => $value['ID'],
                        'TEXT' => $value['NAME'],
                        'URL' => !empty($value['CODE']) ? $value['CODE'] : $value['NAME']
                    ];
            } else { /** Если свойство иное */
                $property['VALUE'] = [
                    'ID' => null,
                    'HASH' => $condition->value,
                    'TEXT' => $condition->value,
                    'URL' => $condition->value
                ];
            }

            /** Если значение пустое, уходим */
            if (empty($property['VALUE']))
                return null;

            $property['VALUE']['HASH'] = abs(crc32(Html::encode($property['VALUE']['HASH'])));
            $property['VALUE']['URL'] = StringHelper::toLowerCase($property['VALUE']['URL'], Encoding::getDefault());
            $property['VALUE']['URL'] = StringHelper::replace($property['VALUE']['URL'], ['/' => '-']);

            return $property;
        } else if ($condition instanceof IBlockSectionCondition) { /** Если условие - раздел */
            if (empty($condition->value) && !Type::isNumeric($condition->value))
                return null;

            if (!ArrayHelper::keyExists($condition->value, self::$_sections))
                self::$_sections[$condition->value] = CIBlockSection::GetList([], [
                    'ID' => $condition->value
                ], false, [], [
                    'nPageSize' => 1
                ])->Fetch();

            $section = self::$_sections[$condition->value];

            if (!empty($section)) {
                $section['CONDITION'] = $condition;

                return $section;
            }
        } else if ($condition instanceof CatalogPriceCondition) {
            if (empty($condition->id) && !Type::isNumeric($condition->id))
                return null;

            if (!Type::isNumeric($condition->value))
                return null;

            if (!ArrayHelper::keyExists($condition->id, self::$_prices)) {
                if (!Loader::includeModule('catalog'))
                    return null;

                self::$_prices[$condition->id] = CCatalogGroup::GetList([
                    'SORT' => 'ASC'
                ], [
                    'ID' => $condition->id
                ])->Fetch();
            }

            $price = self::$_prices[$condition->id];

            if (!empty($price)) {
                $price['CONDITION'] = $condition;
                $price['VALUE'] = Type::toFloat($condition->value);

                return $price;
            }
        }

        return null;
    }

    /**
     * Возвращает объекты комбинации, доступные для фильтрации.
     * @param array $combination
     * @param boolean $strict Страя сборка. Будет собирать комбинацию только если все свойства подходят для фильтра.
     * @return array
     */
    public static function getFilterCombination($combination, $strict = true)
    {
        $result = [];

        /** Выбираем только подходящие для построения адреса объекты */
        foreach ($combination as $object) {
            $add = false;

            /** Если объект - свойство */
            if ($object['CONDITION'] instanceof IBlockPropertyCondition) {
                /** Если тип свойства - Число, то подойдут только операторы "Меньше", "Меньше или равно", "Больше", "Больше или равно" */
                if ($object['PROPERTY_TYPE'] === 'N') {
                    if (
                        $object['CONDITION']->operator === IBlockPropertyCondition::OPERATOR_LESS ||
                        $object['CONDITION']->operator === IBlockPropertyCondition::OPERATOR_LESS_OR_EQUAL ||
                        $object['CONDITION']->operator === IBlockPropertyCondition::OPERATOR_MORE ||
                        $object['CONDITION']->operator === IBlockPropertyCondition::OPERATOR_MORE_OR_EQUAL
                    ) $add = true;
                } else { /** Если тип свойства любой другой, то подойдет только оператор "Равно" */
                    if ($object['CONDITION']->operator === IBlockPropertyCondition::OPERATOR_EQUAL)
                        $add = true;
                }
            } else if ($object['CONDITION'] instanceof CatalogPriceCondition) {
                if (
                    (
                        $object['CONDITION'] instanceof CatalogPriceFilteredMinimalCondition ||
                        $object['CONDITION'] instanceof CatalogPriceFilteredMaximalCondition
                    ) &&
                    $object['CONDITION']->operator === CatalogPriceCondition::OPERATOR_EQUAL &&
                    Type::isNumeric($object['CONDITION']->value)
                ) $add = true;
            }

            if ($add) {
                $result[] = $object;
            } else if ($strict) {
                return null;
            }
        }

        return $result;
    }

    /**
     * Тип свойства фильтра: Список.
     */
    const FILTER_PROPERTY_TYPE_LIST = 'list';
    /**
     * Тип свойства фильтра: Диапазон.
     */
    const FILTER_PROPERTY_TYPE_RANGE = 'range';
    /**
     * Тип свойства фильтра: Цена
     */
    const FILTER_PROPERTY_TYPE_PRICE = 'price';

    /**
     * Возвращает свойства фильтра с их значениями.
     * Порядок свойств и порядок значений совпадает с порядком в системе.
     * @param boolean $strict Страя сборка. Будет собирать комбинацию только если все свойства подходят для фильтра.
     * @param array $combination
     * @return array
     */
    public static function getFilterObjects($combination, $strict = true)
    {
        $result = [];
        $objects = [];

        foreach ($combination as $object) {
            /** @var Condition $condition */
            $condition = $object['CONDITION'];
            $added = false;

            if ($condition instanceof IBlockPropertyCondition) {
                $key = $object['ID'];
                $value = $object['VALUE'];

                if (empty($value))
                    continue;

                if (empty($objects[$key])) {
                    unset($object['CONDITION']);
                    unset($object['VALUE']);

                    $object['TYPE'] = $object['PROPERTY_TYPE'] === 'N' ? static::FILTER_PROPERTY_TYPE_RANGE : static::FILTER_PROPERTY_TYPE_LIST;
                    $object['VALUES'] = [];

                    $objects[$key] = $object;
                }

                $object = &$objects[$key];

                if ($object['TYPE'] === static::FILTER_PROPERTY_TYPE_RANGE) {
                    $value['EQUAL'] =
                        $condition->operator === IBlockPropertyCondition::OPERATOR_LESS_OR_EQUAL ||
                        $condition->operator === IBlockPropertyCondition::OPERATOR_MORE_OR_EQUAL;

                    if (
                        $condition->operator === IBlockPropertyCondition::OPERATOR_LESS ||
                        $condition->operator === IBlockPropertyCondition::OPERATOR_LESS_OR_EQUAL
                    ) {
                        $added = true;

                        if (
                            !isset($object['VALUES']['maximal']) ||
                            $object['VALUES']['maximal']['TEXT'] > $value['TEXT']
                        ) {
                            $object['VALUES']['maximal'] = $value;
                        } else if (
                            isset($object['VALUES']['maximal']) &&
                            $object['VALUES']['maximal']['TEXT'] === $value['TEXT']
                        ) {
                            if ($value['EQUAL'])
                                $object['VALUES']['maximal']['EQUAL'] = true;
                        }
                    } else if (
                        $condition->operator === IBlockPropertyCondition::OPERATOR_MORE ||
                        $condition->operator === IBlockPropertyCondition::OPERATOR_MORE_OR_EQUAL
                    ) {
                        $added = true;

                        if (
                            !isset($object['VALUES']['minimal']) ||
                            $object['VALUES']['minimal']['TEXT'] < $value['TEXT']
                        ) {
                            $object['VALUES']['minimal'] = $value;
                        } else if (
                            isset($object['VALUES']['minimal']) &&
                            $object['VALUES']['minimal']['TEXT'] === $value['TEXT']
                        ) {
                            if ($value['EQUAL'])
                                $object['VALUES']['minimal']['EQUAL'] = true;
                        }
                    }
                } else {
                    if ($condition->operator === IBlockPropertyCondition::OPERATOR_EQUAL) {
                        $added = true;
                        $object['VALUES'][] = $value;
                    }
                }

                unset($object);
            } else if ($condition instanceof CatalogPriceCondition) {
                $key = 'P_'.$condition->id;
                $value = $object['VALUE'];

                if (empty($objects[$key])) {
                    unset($object['CONDITION']);
                    unset($object['VALUE']);

                    $object['TYPE'] = static::FILTER_PROPERTY_TYPE_PRICE;
                    $object['VALUES'] = [];

                    $objects[$key] = $object;
                }

                $object = &$objects[$key];

                if ($condition->operator === CatalogPriceCondition::OPERATOR_EQUAL) {
                    if ($condition instanceof CatalogPriceFilteredMinimalCondition) {
                        if (!isset($object['VALUES']['minimal'])) {
                            $added = true;
                            $object['VALUES']['minimal'] = $value;
                        }
                    } else if ($condition instanceof CatalogPriceFilteredMaximalCondition) {
                        if (!isset($object['VALUES']['maximal'])) {
                            $added = true;
                            $object['VALUES']['maximal'] = $value;
                        }
                    }
                }

                unset($object);
            }

            if (!$added && $strict)
                return [];
        }

        foreach ($objects as $key => $object)
            if (!empty($object['VALUES']))
                $result[$key] = $object;

        /** Если нет свойств фильтра, уходим */
        if (empty($result))
            return $result;

        /** Сортируем свойства сначало по сортировке, потом по идентификаторам */
        uasort($result, function ($left, $right) {
            /** Сортировка ценовой группы */
            if ($left['TYPE'] === static::FILTER_PROPERTY_TYPE_PRICE && $right['TYPE'] === static::FILTER_PROPERTY_TYPE_PRICE) {
                if ($left['SORT'] === $right['SORT'])
                    return $left['SORT'] - $right['SORT'];

                return $left['ID'] - $right['ID'];
            } else if ($left['TYPE'] === static::FILTER_PROPERTY_TYPE_PRICE) {
                return -1;
            } else if ($right['TYPE'] === static::FILTER_PROPERTY_TYPE_PRICE) {
                return 1;
            }

            /** Сортировка свойств, если нет цены */
            if ($left['SORT'] === $right['SORT'])
                return $left['ID'] - $right['ID'];

            return $left['SORT'] - $right['SORT'];
        });

        /** Сортируем значения свойств */
        foreach ($result as &$object) {
            if ($object['TYPE'] === static::FILTER_PROPERTY_TYPE_LIST)
                uasort($object['VALUES'], function ($left, $right) {
                    return strcmp($left['TEXT'], $right['TEXT']);
                });
        }

        return $result;
    }

    /**
     * Возвращает условия для фильтра из комбинации.
     * @param array $combination
     * @param boolean $strict Страя сборка. Будет собирать комбинацию только если все свойства подходят для фильтра.
     * @return array
     */
    public static function getFilterConditionsFromCombination($combination, $strict = true)
    {
        return static::getFilterConditions(static::getFilterObjects($combination, $strict));
    }

    /**
     * Возвращает условия для фильтра из свойств.
     * @param array $objects
     * @return array
     */
    public static function getFilterConditions($objects)
    {
        $result = [];

        foreach ($objects as $key => $object) {
            if (
                $object['TYPE'] === static::FILTER_PROPERTY_TYPE_RANGE ||
                $object['TYPE'] === static::FILTER_PROPERTY_TYPE_LIST
            ) {
                $key = $object['CODE'];

                if (empty($key))
                    $key = $object['ID'];

                $key = 'PROPERTY_'.$key;

                if ($object['TYPE'] === static::FILTER_PROPERTY_TYPE_RANGE) {
                    if (isset($object['VALUES']['minimal']))
                        if ($object['VALUES']['minimal']['EQUAL']) {
                            $result['>='.$key] = $object['VALUES']['minimal']['TEXT'];
                        } else {
                            $result['>'.$key] = $object['VALUES']['minimal']['TEXT'];
                        }

                    if (isset($object['VALUES']['maximal']))
                        if ($object['VALUES']['maximal']['EQUAL']) {
                            $result['<='.$key] = $object['VALUES']['maximal']['TEXT'];
                        } else {
                            $result['<'.$key] = $object['VALUES']['maximal']['TEXT'];
                        }
                } else {
                    $values = [];

                    foreach ($object['VALUES'] as $value)
                        $values[] = $value['ID'] !== null ? $value['ID'] : $value['TEXT'];

                    $result['='.$key] = count($values) > 1 ? $values : $values[0];
                }
            } else if ($object['TYPE'] === static::FILTER_PROPERTY_TYPE_PRICE) {
                $key = 'CATALOG_PRICE_'.$object['ID'];

                if (isset($object['VALUES']['minimal']))
                    $result['>='.$key] = $object['VALUES']['minimal'];

                if (isset($object['VALUES']['maximal']))
                    $result['<='.$key] = $object['VALUES']['maximal'];
            }
        }

        return $result;
    }

    /**
     * Возвращает фильтр из комбинации.
     * @param array $combination Комбинация.
     * @param array $iblock Инфоблок.
     * @param array $section Раздел.
     * @param boolean $strict Страя сборка. Будет собирать комбинацию только если все свойства подходят для фильтра.
     * @return array|null
     */
    public static function getFilterFromCombination($combination, $iblock, $section, $strict = true)
    {
        return static::getFilter(static::getFilterObjects($combination, $strict), $iblock, $section);
    }

    /**
     * Возвращает фильтр из свойств фильтра.
     * @param array $objects Свойства.
     * @param array $iblock Инфоблок.
     * @param array $section Раздел.
     * @return array|null
     */
    public static function getFilter($objects, $iblock, $section)
    {
        $result = null;

        if (empty($iblock) || empty($section) || empty($objects))
            return $result;

        $sku = false;

        if (Loader::includeModule('catalog'))
            $sku = CCatalogSku::GetInfoByProductIBlock($iblock['ID']);

        $iblockObjects = [];
        $offersObjects = [];

        foreach ($objects as $object) {
            if ($object['TYPE'] === static::FILTER_PROPERTY_TYPE_PRICE) {
                $iblockObjects[] = $object;
            } else if ($object['IBLOCK_ID'] == $iblock['ID']) {
                $iblockObjects[] = $object;
            } else if ($sku !== false && $object['IBLOCK_ID'] == $sku['IBLOCK_ID']) {
                $offersObjects[] = $object;
            }
        }

        if (empty($iblockObjects) && empty($offersObjects))
            return $result;

        $iblockConditions = [];
        $offersConditions = [];

        if (!empty($iblockObjects))
            $iblockConditions = static::getFilterConditions($iblockObjects);

        if (!empty($offersObjects))
            $offersConditions = static::getFilterConditions($offersObjects);

        if (empty($iblockConditions) && empty($offersConditions))
            return $result;

        $result = ArrayHelper::merge([
            'IBLOCK_ID' => $iblock['ID'],
            'SECTION_ID' => $section['ID']
        ], $iblockConditions);

        if (!empty($offersConditions)) {
            $result['ID'] = CIBlockElement::SubQuery('PROPERTY_'.$sku['SKU_PROPERTY_ID'], ArrayHelper::merge([
                'IBLOCK_ID' => $sku['IBLOCK_ID']
            ], $offersConditions));
        }

        return $result;
    }
}