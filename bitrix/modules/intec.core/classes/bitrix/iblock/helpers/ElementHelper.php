<?php

namespace intec\core\bitrix\iblock\helpers;

use Closure;
use intec\core\bitrix\Files;
use intec\core\bitrix\FilesQuery;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * Class ElementHelper
 * @package intec\core\bitrix\iblock\helpers
 * @deprecated
 */
class ElementHelper
{
    /**
     * Возвращает свойство.
     * @param array $element Элемент.
     * @param string $code Код.
     * @param boolean|Closure $handler Обработчик.
     * @param string $propertiesKey Ключ поля, в котором хранятся свойства.
     * @return array|null
     */
    public static function getProperty(&$element, $code, $handler = false, $propertiesKey = 'PROPERTIES')
    {
        if (!static::isPropertyExists($element, $code, $propertiesKey))
            return null;

        if ($handler === true)
            $handler = function ($key, &$property) {
                static::handleProperty($key, $property);
            };

        if (!Type::isFunction($handler))
            $handler = null;

        $property = $element[$propertiesKey][$code];

        if ($handler !== null)
            $handler($code, $property);

        return $property;
    }

    /**
     * Возвращает значение свойства.
     * @param array $element Элемент.
     * @param string $code Код.
     * @param mixed $default Значение по умолчанию.
     * @param boolean|Closure $handler Обработчик.
     * @param string $propertiesKey Ключ поля, в котором хранятся свойства.
     * @return array|null
     */
    public static function getPropertyValue(&$element, $code, $default = null, $handler = false, $propertiesKey = 'PROPERTIES')
    {
        $property = static::getProperty($element, $code, $handler, $propertiesKey);

        if (!empty($property))
            return $property['VALUE'];

        return $default;
    }

    /**
     * Возвращает значение свойства как логическое.
     * @param array $element Элемент.
     * @param string $code Код.
     * @param boolean $default Значение по умолчанию.
     * @param string $propertiesKey Ключ поля, в котором хранятся свойства.
     * @return mixed|boolean
     */
    public static function getPropertyValueAsBoolean(&$element, $code, $default = false, $propertiesKey = 'PROPERTIES')
    {
        if (!static::isPropertyExists($element, $code, $propertiesKey))
            return $default;

        return !empty($element[$propertiesKey][$code]['VALUE']) || Type::isNumeric($element[$propertiesKey][$code]['VALUE']);
    }

    /**
     * Возвращает значение свойства как строку.
     * @param array $element Элемент.
     * @param string $code Код.
     * @param null $default Значение по умолчанию.
     * @param string $propertiesKey Ключ поля, в котором хранятся свойства.
     * @return mixed|null|string
     */
    public static function getPropertyValueAsString(&$element, $code, $default = null, $propertiesKey = 'PROPERTIES')
    {
        if (!static::isPropertyExists($element, $code, $propertiesKey))
            return $default;

        $result = $default;

        if (!empty($element[$propertiesKey][$code]['VALUE']) || Type::isNumeric($element[$propertiesKey][$code]['VALUE'])) {
            $property = &$element[$propertiesKey][$code];

            if ($property['PROPERTY_TYPE'] === 'S' && $property['USER_TYPE'] === 'HTML') {
                if ($property['MULTIPLE'] !== 'Y') {
                    if ($property['VALUE']['TYPE'] === 'HTML') {
                        $result = Html::decode($property['VALUE']['TEXT']);
                    } else {
                        $result = Html::encode($property['VALUE']['TEXT']);
                    }
                } else {
                    $result = ArrayHelper::getFirstValue($property['VALUE']);

                    if ($property['VALUE']['TYPE'] === 'HTML') {
                        $result = Html::decode($result['TEXT']);
                    } else {
                        $result = Html::encode($result['TEXT']);
                    }
                }
            } else {
                if ($property['MULTIPLE'] !== 'Y') {
                    $result = $property['VALUE'];
                } else {
                    $result = ArrayHelper::getFirstValue($property['VALUE']);
                }
            }

            if (empty($result) && !Type::isNumeric($result)) {
                $result = '';
            }

            unset($property);
        }

        return $result;
    }

    /**
     * Возвращает значение свойства как множественную строку.
     * @param array $element Элемент.
     * @param string $code Код.
     * @param array $default Значение по умолчанию.
     * @param string $propertiesKey Ключ поля, в котором хранятся свойства.
     * @return mixed|array
     */
    public static function getPropertyValueAsMultipleString(&$element, $code, $default = [], $propertiesKey = 'PROPERTIES')
    {
        if (!static::isPropertyExists($element, $code, $propertiesKey))
            return $default;

        $result = $default;

        if (!empty($element[$propertiesKey][$code]['VALUE']) || Type::isNumeric($element[$propertiesKey][$code]['VALUE'])) {
            $result = [];
            $property = &$element[$propertiesKey][$code];

            if ($property['PROPERTY_TYPE'] === 'S' && $property['USER_TYPE'] === 'HTML') {
                if ($property['MULTIPLE'] !== 'Y') {
                    if ($property['VALUE']['TYPE'] === 'HTML') {
                        $value = Html::decode($property['VALUE']['TEXT']);
                    } else {
                        $value = Html::encode($property['VALUE']['TEXT']);
                    }

                    if (!empty($value) || Type::isNumeric($value))
                        $result[] = $value;
                } else {
                    foreach ($property['VALUE'] as $item) {
                        if ($item['TYPE'] === 'HTML') {
                            $value = Html::decode($item['TEXT']);
                        } else {
                            $value = Html::encode($item['TEXT']);
                        }

                        if (!empty($value) || Type::isNumeric($value))
                            $result[] = $value;
                    }
                }
            } else {
                if ($property['MULTIPLE'] !== 'Y') {
                    if (!empty($property['VALUE']) || Type::isNumeric($property['VALUE']))
                        $result[] = $property['VALUE'];
                } else {
                    foreach ($property['VALUE'] as $key => $value) {
                        if (!empty($value) || Type::isNumeric($value))
                            $result[] = $value;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Возвращает свойства элемента.
     * @param array $element Элемент.
     * @param boolean|Closure $handler Обработчик.
     * @param array|null $map Карта.
     * @param string $propertiesKey Ключ поля, в котором хранятся свойства.
     * @return array
     */
    public static function getProperties(&$element, $handler = false, $map = null, $propertiesKey = 'PROPERTIES')
    {
        $result = [];

        if ($handler === true)
            $handler = function ($key, &$property) {
                static::handleProperty($key, $property);
            };

        if (!Type::isFunction($handler))
            $handler = null;

        if (!empty($element[$propertiesKey]) && Type::isArray($element[$propertiesKey]))
            if (!empty($map) && Type::isArrayable($map)) {
                foreach ($map as $keyTo => $keyFrom) {
                    if (empty($keyFrom) && !Type::isNumeric($keyFrom))
                        continue;

                    foreach ($element[$propertiesKey] as $key => $property) {
                        if ($keyFrom === $key) {
                            if (!empty($handler))
                                $handler($keyTo, $property);

                            $result[$keyTo] = $property;

                            break;
                        }
                    }
                }
            } else {
                foreach ($element[$propertiesKey] as $key => $property) {
                    if (!empty($handler))
                        $handler($key, $property);

                    $result[$key] = $property;
                }
            }

        return $result;
    }

    /**
     * Возвращает значения свойств элемента.
     * @param array $element Элемент.
     * @param boolean|Closure $handler Обработчик.
     * @param array|null $map Карта.
     * @param string $propertiesKey Ключ поля, в котором хранятся свойства.
     * @return array
     */
    public static function getPropertiesValues(&$element, $handler = false, $map = null, $propertiesKey = 'PROPERTIES')
    {
        $properties = static::getProperties($element, $handler, $map, $propertiesKey);
        $result = [];

        foreach ($properties as $key => $property)
            $result[$key] = $property['VALUE'];

        return $result;
    }

    /**
     * Режим обработки файлов: Только внутренние.
     */
    const HANDLE_FILES_MODE_INTERNAL = 0b1;
    /**
     * Режим обработки файлов: Только свойства.
     */
    const HANDLE_FILES_MODE_PROPERTIES = 0b10;
    /**
     * Режим обработки файлов: Все.
     */
    const HANDLE_FILES_MODE_ALL = self::HANDLE_FILES_MODE_INTERNAL | self::HANDLE_FILES_MODE_PROPERTIES;

    /**
     * Обрабатывает файлы в элементе.
     * @param array $element Элемент.
     * @param integer $mode Режим.
     */
    public static function handleFiles(&$element, $mode = self::HANDLE_FILES_MODE_INTERNAL)
    {
        if ($mode === 0)
            return;

        $query = new FilesQuery();

        /** @var Files $result */
        $result = null;

        $verify = function (&$value) {
            return !empty($value) && !Type::isArray($value);
        };

        $export = function (&$value) use (&$result, &$verify) {
            if ($verify($value))
                $value = $result->get($value);
        };

        $import = function (&$value) use (&$query, &$verify) {
            if ($verify($value))
                $query->add($value);
        };

        if ($mode & self::HANDLE_FILES_MODE_INTERNAL) {
            $import($element['PREVIEW_PICTURE']);
            $import($element['DETAIL_PICTURE']);
        }

        if (!empty($element['PROPERTIES']) && ($mode & self::HANDLE_FILES_MODE_PROPERTIES)) {
            foreach ($element['PROPERTIES'] as &$property) {
                if ($property['PROPERTY_TYPE'] === 'F') {
                    if ($property['MULTIPLE'] !== 'Y') {
                        $import($property['VALUE']);
                    } else {
                        if (!empty($property['VALUE']))
                            foreach ($property['VALUE'] as $value)
                                $import($value);

                        unset($value);
                    }
                }
            }

            unset($property);
        }

        $result = $query->execute();

        if (!$result->isEmpty()) {
            if ($mode & self::HANDLE_FILES_MODE_INTERNAL) {
                $export($element['PREVIEW_PICTURE']);
                $export($element['DETAIL_PICTURE']);
            }

            if (!empty($element['PROPERTIES']) && ($mode & self::HANDLE_FILES_MODE_PROPERTIES)) {
                foreach ($element['PROPERTIES'] as &$property) {
                    if ($property['PROPERTY_TYPE'] === 'F') {
                        if ($property['MULTIPLE'] !== 'Y') {
                            $export($property['VALUE']);
                        } else {
                            $values = [];

                            if (!empty($property['VALUE']))
                                foreach ($property['VALUE'] as $value) {
                                    $export($value);

                                    if (!empty($value))
                                        $values[] = $value;
                                }

                            $property['VALUE'] = $values;

                            unset($value, $values);
                        }
                    }
                }

                unset($property);
            }
        }
    }

    /**
     * Базовый обработчик для свойства элемента.
     * @param string $key
     * @param array $property
     */
    public static function handleProperty($key, &$property)
    {
        // Обработка строк
        if ($property['PROPERTY_TYPE'] === 'S') {
            if (empty($property['USER_TYPE'])) {
                if ($property['MULTIPLE'] !== 'Y') {
                    if (empty($property['VALUE']) && !Type::isNumeric($property['VALUE']))
                        $property['VALUE'] = '';
                } else {
                    $values = [];

                    if (!empty($property['VALUE']))
                        foreach ($property['VALUE'] as $value) {
                            if (!empty($value) || Type::isNumeric($value))
                                $values[] = $value;
                        }

                    $property['VALUE'] = $values;

                    unset($values);
                }
            } else if ($property['USER_TYPE'] === 'HTML') {
                if ($property['MULTIPLE'] !== 'Y') {
                    if ($property['VALUE']['TYPE'] === 'TEXT') {
                        $property['VALUE'] = Html::encode($property['VALUE']['TEXT']);
                    } else {
                        $property['VALUE'] = $property['VALUE']['TEXT'];
                    }

                    if (empty($property['VALUE']) && !Type::isNumeric($property['VALUE']))
                        $property['VALUE'] = '';
                } else {
                    $values = [];

                    if (!empty($property['VALUE']))
                        foreach ($property['VALUE'] as $value) {
                            if ($value['TYPE'] === 'TEXT') {
                                $value = Html::encode($value['TEXT']);
                            } else {
                                $value = $value['TEXT'];
                            }

                            if (!empty($value) || Type::isNumeric($value))
                                $values[] = $value;
                        }

                    $property['VALUE'] = $values;

                    unset($values);
                }
            }
            // Обработка чисел
        } else if ($property['PROPERTY_TYPE'] === 'N') {
            if (empty($property['USER_TYPE'])) {
                if ($property['MULTIPLE'] !== 'Y') {
                    if (Type::isNumeric($property['VALUE'])) {
                        $property['VALUE'] = Type::toFloat($property['VALUE']);
                    } else {
                        $property['VALUE'] = 0;
                    }
                } else {
                    $values = [];

                    if (!empty($property['VALUE']))
                        foreach ($property['VALUE'] as $value)
                            if (Type::isNumeric($value)) {
                                $value = Type::toFloat($value);
                                $values[] = $value;
                            }

                    $property['VALUE'] = $values;

                    unset($values);
                }
            }
            // Обработка списков и логических переменных
        } else if ($property['PROPERTY_TYPE'] === 'L') {
            if (empty($property['USER_TYPE'])) {
                if ($property['MULTIPLE'] !== 'Y') {
                    if (
                        (!empty($property['VALUE']) || Type::isNumeric($property['VALUE'])) &&
                        (!empty($property['VALUE_XML_ID']) || Type::isNumeric($property['VALUE_XML_ID']))
                    ) {
                        $property['VALUE'] = [
                            'CODE' => $property['VALUE_XML_ID'],
                            'NAME' => $property['VALUE']
                        ];
                    } else {
                        $property['VALUE'] = null;
                    }
                } else {
                    $values = [];

                    if (!empty($property['VALUE']))
                        foreach ($property['VALUE'] as $index => $value) {
                            $code = ArrayHelper::getValue($property, ['VALUE_XML_ID', $index]);
                            $name = ArrayHelper::getValue($property, ['VALUE', $index]);

                            if (
                                (!empty($code) || Type::isNumeric($code)) &&
                                (!empty($name) || Type::isNumeric($name))
                            ) {
                                $values[$code] = [
                                    'CODE' => $code,
                                    'NAME' => $name
                                ];
                            }

                            unset($code, $name);
                        }

                    $property['VALUE'] = $values;

                    unset($values);
                }
            }
        }
    }

    /**
     * Базовый обработчик для свойств элемента.
     * @param array $properties
     */
    public static function handleProperties(&$properties)
    {
        foreach ($properties as $key => &$property)
            static::handleProperty($key, $property);
    }

    /**
     * Проверяет, существует ли свойство в элементе.
     * @param array $element Элемент.
     * @param string $code Код.
     * @param string $propertiesKey Ключ поля, в котором хранятся свойства.
     * @return boolean
     */
    public static function isPropertyExists(&$element, $code, $propertiesKey = 'PROPERTIES')
    {
        return !empty($element[$propertiesKey]) && !empty($element[$propertiesKey][$code]);
    }
}