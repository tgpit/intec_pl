<?php
namespace intec\seo\filter\condition;

use Bitrix\Main\Loader;
use CCatalogSku;
use CIBlockElement;
use CIBlockProperty;
use intec\core\bitrix\conditions\IBlockPropertyCondition;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

class CombinationsHelper
{
    /**
     * Проверяет комбинации на наличие пустых значений.
     * @param array $combinations Комбинации.
     * @return boolean
     */
    public static function combinationsIsNormalized($combinations)
    {
        $result = true;

        foreach ($combinations as $combination) {
            if (!self::combinationIsNormalized($combination))
                $result = false;

            if (!$result)
                break;
        }

        return $result;
    }

    /**
     * Проверяет комбинацию на наличие пустых значений.
     * @param array $combination Комбинация.
     * @return boolean
     */
    public static function combinationIsNormalized($combination)
    {
        $result = true;

        foreach ($combination as $condition) {
            if ($condition instanceof IBlockPropertyCondition) {
                if (empty($condition->value) && !Type::isNumeric($condition->value))
                    $result = false;
            }

            if (!$result)
                break;
        }

        return $result;
    }

    /**
     * Заполняет пустые значения комбинаций, генерируя из них новые комбинации.
     * @param array $combinations Комбинации.
     * @param array $iblock Инфоблок.
     * @param array $sections Разделы инфоблока.
     * @param boolean $recursive Рекурсивный поиск значений.
     * @return array
     */
    public static function normalizeCombinations($combinations, $iblock, $sections, $recursive = true)
    {
        $result = [];

        foreach ($combinations as $combination) {
            if (!self::combinationIsNormalized($combination)) {
                $generated = self::normalizeCombination($combination, $iblock, $sections, $recursive);

                foreach ($generated as $generatedCombination) {
                    $exists = false;

                    foreach ($result as $resultCombination) {
                        $exists = self::compareCombinations($generatedCombination, $resultCombination);

                        if ($exists)
                            break;
                    }

                    if ($exists)
                        continue;

                    $result[] = $generatedCombination;
                }
            } else {
                $result[] = $combination;
            }
        }

        return $result;
    }

    /**
     * Заполняет пустые значения комбинации, генерируя из нее новые комбинации.
     * @param array $combination Комбинация.
     * @param array $iblock Инфоблок.
     * @param array $sections Разделы инфоблока.
     * @param boolean $recursive Рекурсивный поиск значений.
     * @return array
     */
    public static function normalizeCombination($combination, $iblock, $sections, $recursive = true)
    {
        $result = [];

        /** Если комбинация не пустая */
        if (!empty($combination)) {
            /** Добавляем пустую комбинацию */
            $result[] = [];

            /** Идем по условиям комбинации */
            foreach ($combination as $id => $condition) {
                /** Новые комбинации */
                $collection = [];
                $values = null;

                /** Если условие является свойством инфоблока и его значение пустое */
                if (($condition instanceof IBlockPropertyCondition) && empty($condition->value) && !Type::isNumeric($condition->value)) {
                    /** Получаем значения для условия */
                    $values = self::getConditionValues($condition, $iblock, $sections, $recursive);
                }

                /** Для каждой новой комбинации */
                foreach ($result as $resultCombination) {
                    /** Если значения не пустые */
                    if (!empty($values)) {
                        foreach ($values as $value) {
                            /** Создаем новое условие со значением */
                            $newCondition = new IBlockPropertyCondition([
                                'id' => $condition->id,
                                'operator' => $condition->operator,
                                'value' => $value
                            ]);

                            /** Проверяем, если такого условия с таким значением еще нет в комбинации */
                            foreach ($resultCombination as $resultCondition)
                                if ($resultCondition == $newCondition) {
                                    $newCondition = null;
                                    break;
                                }

                            if ($newCondition !== null) {
                                /** Добавляем новое условие и помещаем комбинацию в результирующий набор */
                                $resultCombination[$id] = $newCondition;
                                $collection[] = $resultCombination;
                            }
                        }
                    } else {
                        /** Иначе просто добавляем свойство в комбинацию, а комбинацию в результирующий набор каждой комбинации */
                        $resultCombination[$id] = $condition;
                        $collection[] = $resultCombination;
                    }
                }

                /** Заменяем новые комбинации результирующим набором */
                $result = $collection;
            }
        }

        return $result;
    }

    /**
     * Сравнивает 2 комбинации.
     * @param array $combination1 Комбинация 1.
     * @param array $combination2 Комбинация 2.
     * @return boolean Комбинации идентичны.
     */
    public static function compareCombinations($combination1, $combination2)
    {
        $map = [];

        if (count($combination1) !== count($combination2))
            return false;

        foreach ($combination1 as $key1 => $condition1) {
            /** @var  $result */
            $result = false;

            foreach ($combination2 as $key2 => $condition2) {
                if (!empty($map[$key2]))
                    continue;

                if ($condition1 == $condition2) {
                    $result = true;
                    $map[$key2] = true;
                    break;
                }
            }

            if (!$result)
                return false;
        }

        return true;
    }

    /**
     * Получает список значений для свойства.
     * @param IBlockPropertyCondition $condition
     * @param array $iblock
     * @param array $sections
     * @param boolean $recursive
     * @return array
     */
    public static function getConditionValues($condition, $iblock, $sections, $recursive = true)
    {
        $result = [];

        if (!($condition instanceof IBlockPropertyCondition))
            return $result;

        /** Получаем свойство */
        $property = CIBlockProperty::GetList([], ['ID' => $condition->id])->Fetch();

        if (empty($property))
            return $result;

        /** Формируем код свойства */
        $code = $property['CODE'];

        if (empty($code) && !Type::isNumeric($code))
            $code = $property['ID'];

        $code = StringHelper::toUpperCase($code);
        $sku = false;

        /** Является ли инфоблок свойства инфоблоком торговых предложений */
        if (Loader::includeModule('catalog'))
            $sku = CCatalogSku::GetInfoByProductIBlock($iblock['ID']);

        /** Если свойство не из инфоблока товаров или торговых предлодений - уходим */
        if ($property['IBLOCK_ID'] != $iblock['ID'] && ($sku === false || $property['IBLOCK_ID'] != $sku['IBLOCK_ID']))
            return $result;

        /** Если свойство не торгового предложения, устанавливаем торговое предложение как false */
        if ($property['IBLOCK_ID'] != $sku['IBLOCK_ID'])
            $sku = false;

        /** Проход по всем разделам */
        foreach ($sections as $section) {
            /** Формируем фильтр для выборки элементов инфоблока */
            $filter = [
                'IBLOCK_ID' => $iblock['ID'],
                'ACTIVE' => 'Y',
                'ACTIVE_DATE' => 'Y',
                'SECTION_ID' => $section['ID'],
                'INCLUDE_SUBSECTIONS' => $recursive ? 'Y' : 'N',
                '!PROPERTY_'.$code => false
            ];

            /** Формируем поля для выборки */
            $select = [
                'ID'
            ];

            if ($sku === false)
                $select[] = 'PROPERTY_'.$code;

            $elementsId = [];
            /** Получаем список элементов */
            $elements = CIBlockElement::GetList([], $filter, false, false, $select);

            /** Проходим по элементам */
            while ($element = $elements->Fetch()) {
                /** Если свойство торгового предложения */
                if ($sku !== false) {
                    $elementsId[] = $element['ID'];
                } else {
                    $value = null;

                    /** Если существует идентификатор варианта выбора - берем его, иначе обычное значение если существует */
                    if (isset($element['PROPERTY_'.$code.'_ENUM_ID'])) {
                        $value = $element['PROPERTY_'.$code.'_ENUM_ID'];
                    } else if (isset($element['PROPERTY_'.$code.'_VALUE'])) {
                        $value = $element['PROPERTY_'.$code.'_VALUE'];
                    }

                    if (!empty($value) || Type::isNumeric($value))
                        $result[] = $value;
                }
            }

            if ($sku !== false && !empty($elementsId)) {
                $elementsId = array_unique($elementsId);

                /** Формируем фильтр для выборки элементов инфоблока торговых предложений */
                $filter = [
                    'IBLOCK_ID' => $sku['IBLOCK_ID'],
                    'ACTIVE' => 'Y',
                    'ACTIVE_DATE' => 'Y',
                    '!PROPERTY_'.$code => false,
                    'PROPERTY_'.$sku['SKU_PROPERTY_ID'] => $elementsId
                ];

                /** Получаем список элементов */
                $elements = CIBlockElement::GetList([], $filter, false, false, [
                    'ID',
                    'PROPERTY_'.$code
                ]);

                /** Проходим по элементам */
                while ($element = $elements->Fetch()) {
                    $value = null;

                    /** Если существует идентификатор варианта выбора - берем его, иначе обычное значение если существует */
                    if (isset($element['PROPERTY_'.$code.'_ENUM_ID'])) {
                        $value = $element['PROPERTY_'.$code.'_ENUM_ID'];
                    } else if (isset($element['PROPERTY_'.$code.'_VALUE'])) {
                        $value = $element['PROPERTY_'.$code.'_VALUE'];
                    }

                    if (!empty($value) || Type::isNumeric($value))
                        $result[] = $value;
                }
            }
        }

        $result = array_unique($result);

        return $result;
    }
}