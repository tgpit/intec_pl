<?php
namespace intec\seo\models\filter;

use intec\core\db\ActiveQuery;
use intec\core\helpers\Type;
use intec\seo\models\filter\condition\Section;
use intec\seo\models\filter\condition\Site;

/**
 * Class ConditionQuery
 * @package intec\seo\models\filter
 * @author apocalypsisdimon@gmail.com
 */
class ConditionQuery extends ActiveQuery
{
    /**
     * Добавляет условие поиска условий по разделам.
     * @param array|string $values
     * @return static
     */
    public function forSections($values)
    {
        $conditions = ['or'];
        $null = false;

        if ($values === null) {
            $null = true;
        } else {
            if (Type::isArray($values)) {
                $sections = [];

                foreach ($values as $value) {
                    if ($value === null) {
                        $null = true;
                        continue;
                    }

                    $sections[] = $value;
                }

                if (!empty($sections))
                    $conditions[] = ['in', Section::tableName().'.iBlockSectionId', $sections];
            } else if (!empty($values) || Type::isNumeric($values)) {
                $conditions[] = ['=', Section::tableName().'.iBlockSectionId', $values];
            }
        }

        if ($null)
            $conditions[] = ['is', Section::tableName().'.iBlockSectionId', null];

        if (!empty($conditions))
            $this->joinWith(['sections'], true, 'LEFT JOIN')
                ->andWhere($conditions);

        return $this;
    }

    /**
     * Добавляет условие поиска условий по сайтам.
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