<?php
namespace intec\regionality\services\locator;

use intec\core\base\BaseObject;

/**
 * Представляет расширение сервиса локаций.
 * Class Extensions
 * @property string $code Код.
 * @property string $name Наименование на текущем языке.
 * @property boolean $isAvailable Значение, указывающее доступность расширения сервиса в данный момент.
 * @package intec\regionality\services\locator
 * @author apocalypsisdimon@gmail.com
 */
abstract class Extension extends BaseObject
{
    /**
     * Возвращает символьный код расширения.
     * @return string
     */
    public abstract function getCode();

    /**
     * Возвращает наименование расширения для определенного языка.
     * @param string $language
     * @return string
     */
    public abstract function getName($language = LANGUAGE_ID);

    /**
     * Возвращает значение, указывающее доступность расширения сервиса в данный момент.
     * @return boolean
     */
    public abstract function getIsAvailable();

    /**
     * Разрешает IP-адрес в код региона или `null`, если не определен.
     * @param string $address
     * @param boolean $fullData
     * @return string|null
     */
    public abstract function resolve($address, $fullData = false);
}