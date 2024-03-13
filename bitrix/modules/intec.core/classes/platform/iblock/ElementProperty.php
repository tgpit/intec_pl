<?php
namespace intec\core\platform\iblock;

use CIBlockFormatProperties;

/**
 * Класс, представляющий свойство элемента инфоблока.
 * Class ElementProperty
 * @package intec\core\platform\iblock
 * @author apocalypsisdimon@gmail.com
 */
class ElementProperty extends Property
{
    /**
     * Обработчик значения свойства по умолчанию.
     * @var ElementPropertyValueHandlerInterface
     */
    protected static $_defaultValueHandler;

    /**
     * Возвращает обработчик значений свойств по умолчанию.
     * @return ElementPropertyValueHandlerInterface
     */
    public static function getDefaultValueHandler()
    {
        if (static::$_defaultValueHandler === null)
            static::$_defaultValueHandler = null;

        return static::$_defaultValueHandler;
    }

    /**
     * Устанавливает обработчик значений свойств по умолчанию.
     * @param ElementPropertyValueHandlerInterface $handler
     */
    public static function setDefaultValueHandler($handler)
    {
        if (!($handler instanceof ElementPropertyValueHandlerInterface))
            $handler = null;

        static::$_defaultValueHandler = $handler;
    }

    /**
     * Возвращает зачение для отображения.
     * @return mixed
     */
    public function getDisplayValue()
    {
        return CIBlockFormatProperties::GetDisplayValue($this, $this->asArray(), '');
    }

    /**
     * Возвращает обработанное значение.
     * @param ElementPropertyValueHandlerInterface $handler Обработчик.
     * @return mixed
     */
    public function getHandledValue($handler = null)
    {
        if ($handler === null)
            $handler = static::getDefaultValueHandler();

        return $handler->handle($this);
    }

    /**
     * Возвращает оригинальное значение.
     * @return mixed
     */
    public function getValue()
    {
        return $this->_fields['~VALUE'];
    }
}
