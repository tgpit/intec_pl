<?php

namespace intec\core\bitrix\iblock\helpers;

use intec\core\bitrix\Files;
use intec\core\bitrix\FilesQuery;
use intec\core\helpers\Type;

/**
 * Class ElementsHelper
 * @package intec\core\bitrix\iblock\helpers
 * @deprecated
 */
class ElementsHelper
{
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
     * @param array $elements Элементы.
     * @param integer $mode Режим.
     */
    public static function handleFiles(&$elements, $mode = self::HANDLE_FILES_MODE_INTERNAL)
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

        foreach ($elements as &$element) {
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
        }

        unset($element);

        $result = $query->execute();

        if (!$result->isEmpty()) {
            foreach ($elements as &$element) {
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

            unset($element);
        }
    }
}