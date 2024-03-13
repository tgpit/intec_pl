<?php
namespace intec\seo\filter\tags;

use Closure;
use Bitrix\Iblock\Template\Functions\FunctionBase;

/**
 * Класс, представляющий обработчик тегов свойств фильтра.
 * Class PropertyTag
 * @package intec\seo\filter\tags
 */
class PropertyTag extends FunctionBase
{
    /**
     * Обработчик.
     * @var Closure|null
     */
    protected static $_handler;

    /**
     * Устанавливает текущий обработчик.
     * @param Closure|null $handler
     */
    public static function setHandler($handler = null)
    {
        static::$_handler = null;

        if ($handler instanceof Closure)
            static::$_handler = $handler;
    }

    /**
     * Возвращает текущий обработчик.
     * @return Closure|null
     */
    public static function getHandler()
    {
        return static::$_handler;
    }

    /**
     * @inheritdoc
     */
    public function calculate(array $parameters)
    {
        $result = [];
        $handler = static::$_handler;

        if (empty($handler))
            return $result;

        $parameters = $this->parametersToArray($parameters);

        if (empty($parameters))
            return $result;

        return $handler($parameters);
    }
}