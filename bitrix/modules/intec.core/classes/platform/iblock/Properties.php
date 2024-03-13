<?php
namespace intec\core\platform\iblock;

use intec\core\base\Collection;

/**
 * Класс, представляющий коллекцию свойств инфоблока.
 * Class Properties
 * @package intec\core\platform\iblock
 * @author apocalypsisdimon@gmail.com
 */
class Properties extends Collection
{
    /**
     * @inheritdoc
     */
    protected function verify($item)
    {
        return $item instanceof Property;
    }

    /**
     * Производит поиск свойства с определенным кодом.
     * @param string $code Код.
     * @return Property|null
     */
    public function getByCode($code)
    {
        foreach ($this->items as $item) {
            /** @var Property $item */
            if ($item->getCode() == $code)
                return $item;
        }

        return null;
    }

    /**
     * Производит поиск свойства с определенным идентификатором.
     * @param integer $id Идентификатор.
     * @return static
     */
    public function getById($id)
    {
        foreach ($this->items as $item) {
            /** @var Property $item */
            if ($item->getId() == $id)
                return $item;
        }

        return null;
    }

    /**
     * Производит поиск свойства с определенным специальным идентификатором.
     * @param string|integer $sid Специальный идентификатор.
     * @return static
     */
    public function getBySId($sid)
    {
        foreach ($this->items as $item) {
            /** @var Property $item */
            if ($item->getSId() == $sid)
                return $item;
        }

        return null;
    }
}