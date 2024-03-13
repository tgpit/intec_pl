<?php
namespace intec\seo\models\articles;

use intec\core\db\ActiveQuery;
use intec\core\helpers\Type;
use intec\seo\models\articles\template\Article;
use intec\seo\models\articles\template\Section;
use intec\seo\models\articles\template\Element;
use intec\seo\models\articles\template\Site;

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
     * Добавляет условие поиска условий по разделам для выбора элементов.
     * @param array|string $values
     * @return static
     */
    public function forSectionsForElements($values)
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
     * Добавляет условие поиска условий по элементам.
     * @param array|string $values
     * @return static
     */
    public function forElements($values)
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
                    $conditions[] = ['in', Element::tableName().'.iBlockElementId', $sections];
            } else if (!empty($values) || Type::isNumeric($values)) {
                $conditions[] = ['=', Element::tableName().'.iBlockElementId', $values];
            }
        }

        if ($null)
            $conditions[] = ['is', Element::tableName().'.iBlockElementId', null];

        if (!empty($conditions))
            $this->joinWith(['elements'], true, 'LEFT JOIN')
                ->andWhere($conditions);

        return $this;
    }

    /**
     * Добавляет условие поиска условий по статьям.
     * @param array|string $values
     * @return static
     */
    public function forArticles($values)
    {
        $conditions = ['or'];
        $null = false;

        if ($values === null) {
            $null = true;
        } else {
            if (Type::isArray($values)) {
                $elements = [];

                foreach ($values as $value) {
                    if ($value === null) {
                        $null = true;
                        continue;
                    }

                    $elements[] = $value;
                }

                if (!empty($elements))
                    $conditions[] = ['in', Article::tableName().'.iBlockElementId', $elements];
            } else if (!empty($values) || Type::isNumeric($values)) {
                $conditions[] = ['=', Article::tableName().'.iBlockElementId', $values];
            }
        }

        if ($null)
            $conditions[] = ['is', Article::tableName().'.iBlockElementId', null];

        if (!empty($conditions))
            $this->joinWith(['articles'], true, 'LEFT JOIN')
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