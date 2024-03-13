<?php
namespace intec\regionality\models;

use intec\core\db\ActiveQuery;
use intec\core\helpers\Type;
use intec\regionality\models\region\Site;

/**
 * Class RegionQuery
 * @package intec\regionality\models
 */
class RegionQuery extends ActiveQuery
{
    /**
     * Добавляет условие поиска региона по сайтам.
     * @param array|string $values
     * @return static
     */
    public function forSites($values)
    {
        $conditions = ['or'];
        $null = false;

        if ($values === null) {
            $null = true;
        } else {
            if (Type::isArray($values)) {
                $sites = [];

                foreach ($values as $value) {
                    if ($value === null) {
                        $null = true;
                        continue;
                    }

                    $sites[] = $value;
                }

                if (!empty($sites))
                    $conditions[] = ['in', Site::tableName().'.siteId', $sites];
            } else if (!empty($values) || Type::isNumeric($values)) {
                $conditions[] = ['=', Site::tableName().'.siteId', $values];
            }
        }

        if ($null)
            $conditions[] = ['is', Site::tableName().'.siteId', null];

        if (!empty($conditions))
            $this->joinWith(['sites'], true, 'LEFT JOIN')
                ->andWhere($conditions);

        return $this;
    }
}