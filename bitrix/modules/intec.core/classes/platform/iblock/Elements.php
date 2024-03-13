<?php
namespace intec\core\platform\iblock;

use Bitrix\Iblock\PropertyTable;
use intec\core\base\Collection;
use intec\core\helpers\Type;
use intec\core\platform\main\FileQuery;

/**
 * Класс, представляющий коллекцию элементов.
 * Class Elements
 * @package intec\core\platform\iblock
 * @author apocalypsisdimon@gmail.com
 */
class Elements extends Collection
{
    /**
     * @inheritdoc
     */
    protected function verify($item)
    {
        return $item instanceof Element;
    }

    /**
     * Возвращает элемент по коду.
     * @param string $code
     * @return Element|null
     */
    public function getByCode($code)
    {
        foreach ($this->items as $item) {
            /** @var Element $item */

            if ($item->getCode() === $code)
                return $item;
        }

        return null;
    }

    /**
     * Возвращает элемент по идентификатору.
     * @param integer $id
     * @return Element|null
     */
    public function getById($id)
    {
        $id = Type::toInteger($id);

        foreach ($this->items as $item) {
            /** @var Element $item */

            if ($item->getId() === $id)
                return $item;
        }

        return null;
    }

    /**
     * Возвращает файлы из полей и свойств элементов.
     * @param array $fields Поля.
     * @param array|true|null $properties Свойства.
     * @return \intec\core\platform\main\Files
     */
    public function getFiles($fields = ['PREVIEW_PICTURE', 'DETAIL_PICTURE'], $properties = [])
    {
        $query = new FileQuery();

        foreach ($this->items as $item) {
            /** @var Element $item $field */

            foreach ($fields as $field) {
                $value = $item->getFields()->get($field);

                if (!Type::isEmpty($value)) {
                    if (Type::isArray($value)) {
                        $query->add($value['ID']);
                    } else {
                        $query->add($value);
                    }
                }
            }

            if ($properties === true) {
                foreach ($item->getProperties() as $property) {
                    /** @var ElementProperty $property */
                    if ($property->getType() !== PropertyTable::TYPE_FILE)
                        continue;

                    if ($property->getIsMultiple()) {
                        $values = $property->getValue();

                        foreach ($values as $value) {
                            if (!Type::isEmpty($value))
                                $query->add($value);
                        }
                    } else {
                        $value = $property->getValue();

                        if (!Type::isEmpty($value))
                            $query->add($value);
                    }
                }
            } else if (Type::isArray($properties)) {
                foreach ($properties as $property) {
                    /** @var ElementProperty $property */
                    $property = $item->getProperties()->get($property);

                    if (empty($property) || $property->getType() !== PropertyTable::TYPE_FILE)
                        continue;

                    if ($property->getIsMultiple()) {
                        $values = $property->getValue();

                        foreach ($values as $value) {
                            if (!Type::isEmpty($value))
                                $query->add($value);
                        }
                    } else {
                        $value = $property->getValue();

                        if (!Type::isEmpty($value))
                            $query->add($value);
                    }
                }
            }
        }

        return $query->execute();
    }
}
