<?php
namespace intec\core\handling\controllers;

use intec\core\helpers\Type;

/**
 * Класс, представляющий внутреннее действие контроллера.
 * Class InlineAction
 * @property string $method Вызываемый метод. Только для чтения.
 * @package intec\core\handling\controllers
 * @since 1.0.0
 * @author Codelab
 * @author apocalypsisdimon@gmail.com
 */
class InlineAction extends Action
{
    /**
     * Название вызываемого метода.
     * @var string
     * @since 1.0.0
     */
    protected $_method;

    /**
     * Возвращает название вызываемого метода.
     * @return string
     * @since 1.0.0
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * @inheritdoc.
     * @param string $method Название вызываемого метода.
     * @since 1.0.0
     */
    public function __construct($id, $controller, $method, $config = [])
    {
        $this->_method = $method;
        parent::__construct($id, $controller, $config);
    }

    /**
     * @inheritdoc
     */
    public function bindParameters($parameters)
    {
        $method = new \ReflectionMethod($this->_controller, $this->_method);
        $methodParameters = $method->getParameters();
        $result = [];

        if (!empty($methodParameters)) {
            foreach ($methodParameters as $methodParameter) {
                $name = $methodParameter->getName();
                $value = null;

                if (isset($parameters[$name])) {
                    $value = $parameters[$name];
                } else if ($methodParameter->isDefaultValueAvailable()) {
                    $value = $methodParameter->getDefaultValue();
                }

                $result[$name] = $value;
            }
        } else {
            $result = $parameters;
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function runWithParameters($parameters = [])
    {
        if (!Type::isArray($parameters))
            $parameters = [];

        $arguments = $this->_controller->bindActionParameters($this, $parameters);
        $arguments = $this->bindParameters($arguments);

        if (!Type::isArray($arguments))
            $arguments = [$arguments];

        return call_user_func_array([$this->_controller, $this->_method], $arguments);
    }
}