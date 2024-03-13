<?php
namespace intec\core\platform\iblock;

/**
 * Класс, представляющий коллекцию свойств элемента инфоблока.
 * Class ElementProperties
 * @package intec\core\platform\iblock
 * @author apocalypsisdimon@gmail.com
 */
class ElementProperties extends Properties
{
    /**
     * @inheritdoc
     */
    protected function verify($item)
    {
        return $item instanceof ElementProperty;
    }

    /**
     * Возвращает значение свойства для отображения.
     * @param string $key Ключ свойства.
     * @return mixed
     */
    public function getDisplayValueOf($key)
    {
        return $this->exists($key) ? $this->get($key)->getDisplayValue() : null;
    }

    /**
     * Возвращает обработанное значение свойства.
     * @param string $key Ключ свойства.
     * @param ElementPropertyValueHandlerInterface|null $handler Обработчик.
     * @return mixed
     */
    public function getHandledValueOf($key, $handler = null)
    {
        return $this->exists($key) ? $this->get($key)->getHandledValue($handler) : null;
    }

    /**
     * Возвращает значение свойства.
     * @param string $key Ключ свойства.
     * @return mixed
     */
    public function getValueOf($key)
    {
        return $this->exists($key) ? $this->get($key)->getValue() : null;
    }

    /**
     * Производит поиск свойства с определенным кодом.
     * @param string $code Код.
     * @return ElementProperty|null
     */
    public function getByCode($code)
    {
        foreach ($this->items as $item) {
            /** @var ElementProperty $item */
            if ($item->getCode() == $code)
                return $item;
        }

        return null;
    }
}
