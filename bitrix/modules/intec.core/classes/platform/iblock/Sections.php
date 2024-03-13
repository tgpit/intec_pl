<?php
namespace intec\core\platform\iblock;

use intec\core\base\Collection;
use intec\core\helpers\Type;
use intec\core\platform\main\FileQuery;
use intec\core\platform\main\Files;

/**
 * Класс, представляющий коллекцию разделов.
 * Class Sections
 * @package intec\core\platform\iblock
 * @author apocalypsisdimon@gmail.com
 */
class Sections extends Collection
{
    /**
     * @inheritdoc
     */
    protected function verify($item)
    {
        return $item instanceof Section;
    }

    /**
     * Возвращает файлы из полей разделов.
     * @param array $fields
     * @return Files
     */
    public function getFiles($fields = ['PICTURE', 'DETAIL_PICTURE'])
    {
        $query = new FileQuery();

        foreach ($this->items as $item) {
            /** @var Section $item */

            foreach ($fields as $field) {
                $value = $item[$field];

                if (!Type::isEmpty($value)) {
                    if (Type::isArray($value)) {
                        if (isset($value['ID'])) {
                            $query->add($value['ID']);
                        } else {
                            foreach ($value as $part)
                                $query->add($part);
                        }
                    } else {
                        $query->add($value);
                    }
                }
            }
        }

        return $query->execute();
    }
}
