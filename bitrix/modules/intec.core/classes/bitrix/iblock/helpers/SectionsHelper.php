<?php

namespace intec\core\bitrix\iblock\helpers;

use intec\core\bitrix\Files;
use intec\core\bitrix\FilesQuery;
use intec\core\helpers\Type;

/**
 * Class SectionsHelper
 * @package intec\core\bitrix\iblock\helpers
 * @deprecated
 */
class SectionsHelper
{
    /**
     * @param array $sections Разделы.
     */
    public static function handleFiles(&$sections)
    {
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

        foreach ($sections as &$section) {
            $import($section['PICTURE']);
            $import($section['DETAIL_PICTURE']);
        }

        unset($section);

        $result = $query->execute();

        if (!$result->isEmpty()) {
            foreach ($sections as &$section) {
                $export($section['PICTURE']);
                $export($section['DETAIL_PICTURE']);
            }

            unset($section);
        }
    }
}
