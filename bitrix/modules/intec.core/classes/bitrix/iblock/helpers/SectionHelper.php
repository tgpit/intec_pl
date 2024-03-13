<?php

namespace intec\core\bitrix\iblock\helpers;

use intec\core\bitrix\Files;
use intec\core\bitrix\FilesQuery;
use intec\core\helpers\Type;

/**
 * Class SectionHelper
 * @package intec\core\bitrix\iblock\helpers
 * @deprecated
 */
class SectionHelper
{
    /**
     * @param array $section Раздел.
     */
    public static function handleFiles(&$section)
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

        $import($section['PICTURE']);
        $import($section['DETAIL_PICTURE']);

        $result = $query->execute();

        if (!$result->isEmpty()) {
            $export($section['PICTURE']);
            $export($section['DETAIL_PICTURE']);

            unset($section);
        }
    }
}
