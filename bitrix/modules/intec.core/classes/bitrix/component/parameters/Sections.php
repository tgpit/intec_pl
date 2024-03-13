<?php

namespace intec\core\bitrix\component\parameters;

use Bitrix\Main\Loader;
use intec\core\base\Collection;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

if (!Loader::includeModule('iblock'))
    return;

/**
 * Class Sections
 * @package intec\core\bitrix\component\parameters
 * @deprecated
 */
class Sections extends Collection
{
    /**
     * Возвращает коллекцию разделов
     * @param array $filter
     * @param array $sort
     * @param string $indexBy
     * @return Sections
     */
    public static function getSections($filter, $sort = ['SORT' => 'ASC'], $indexBy = 'ID')
    {
        if (empty($filter) || !Type::isArray($filter)) {
            return new static([]);
        }

        $filter = ArrayHelper::merge([
            'ACTIVE' => 'Y',
            'GLOBAL_ACTIVE' => 'Y',
            'CHECK_PERMISSIONS' => 'Y',
            'MIN_PERMISSION' => 'R'
        ], $filter);

        if (empty($sort) || !Type::isArray($sort)) {
            $sort = ['SORT' => 'ASC'];
        }

        if (empty($indexBy) || !Type::isString($indexBy)) {
            $indexBy = 'ID';
        }

        $items = [];
        $result = \CIBlockSection::GetList($sort, $filter);

        while ($item = $result->GetNext(true, false)) {
            $items[$item[$indexBy]] = $item;
        }

        return new static($items);
    }

    /**
     * Возвращает индексированный массив разделов
     * @param string $indexBy
     * @return array
     */
    public function getList($indexBy = '')
    {
        if (!Type::isString($indexBy)) {
            $indexBy = '';
        }

        return $this->asArray(function ($key, $value) use (&$indexBy) {
            if (!empty($indexBy)) {
                $returnKey = ArrayHelper::getValue($value, $indexBy);

                if (!empty($returnKey)) {
                    $key = $returnKey;
                }
            }

            return [
                'key' => $key,
                'value' => '['.$key.'] '.$value['NAME']
            ];
        });
    }
}