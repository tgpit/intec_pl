<?php
namespace intec\constructor\models\build\layout\renderers;

use Closure;
use intec\core\base\InvalidParamException;
use intec\core\helpers\Type;
use intec\constructor\models\build\layout\Renderer;
use intec\constructor\models\build\layout\Zone;

class ClosureRenderer extends Renderer
{
    /**
     * Функция для обработки.
     * @var Closure
     */
    protected $_closure;
    /**
     * @var boolean
     */
    protected $_isRenderAllowed = true;

    /**
     * @inheritdoc
     */
    public function __construct($closure, $config = [])
    {
        if (!($closure instanceof Closure))
            throw new InvalidParamException('Closure is not instance of function');

        $this->_closure = $closure;

        parent::__construct($config);
    }

    /**
     * Возвращает функцию для обработки.
     * @return Closure
     */
    public function getClosure()
    {
        return $this->_closure;
    }

    /**
     * @inheritdoc
     */
    public function getIsRenderAllowed()
    {
        return $this->_isRenderAllowed;
    }

    /**
     * @param boolean $value
     */
    public function setIsRenderAllowed($value)
    {
        $this->_isRenderAllowed = Type::toBoolean($value);
    }

    /**
     * @inheritdoc
     */
    public function renderZone($zone)
    {
        if (!($zone instanceof Zone))
            return;

        $closure = $this->_closure;
        $closure($zone);
    }
}