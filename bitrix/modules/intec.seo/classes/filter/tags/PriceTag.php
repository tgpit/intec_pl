<?php
namespace intec\seo\filter\tags;

use Closure;
use Bitrix\Iblock\Template\Functions\FunctionBase;
use intec\core\helpers\Type;

/**
 * Класс, представляющий обработчик тегов цен фильтра.
 * Class PriceTag
 * @package intec\seo\filter\tags
 */
class PriceTag extends FunctionBase
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
        $result = '';
        $handler = static::$_handler;

        if (empty($handler))
            return $result;

        $parameters = $this->parametersToArray($parameters);
        $type = isset($parameters[0]) ? $parameters[0] : null;
        $valueType = isset($parameters[1]) ? $parameters[1] : null;

        if ($type === null || $valueType === null)
            return $result;

        return $handler($type, $valueType);
    }
}