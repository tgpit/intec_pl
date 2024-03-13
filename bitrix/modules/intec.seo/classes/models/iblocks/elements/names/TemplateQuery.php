<?php
namespace intec\seo\models\iblocks\elements\names;

use intec\core\db\ActiveQuery;
use intec\core\helpers\Type;
use intec\seo\models\iblocks\elements\names\template\Section;
use intec\seo\models\iblocks\elements\names\template\Site;

/**
 * Class TemplateQuery
 * @package intec\seo\models\iblocks\elements\names
 * @author apocalypsisdimon@gmail.com
 */
class TemplateQuery extends ActiveQuery
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