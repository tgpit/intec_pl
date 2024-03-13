<?php
namespace intec\core\handling\controllers;

use intec\core\base\Component;
use intec\core\base\InvalidConfigException;
use intec\core\helpers\Type;

/**
 * Класс, представляющий действие контроллера.
 * Class Action
 * @property string $id Идентификатор действия. Только для чтения.
 * @property Controller $controller Контроллер действия. Только для чтения.
 * @package intec\core\handling\controllers
 * @since 1.0.0
 * @author Codelab
 * @author apocalypsisdimon@gmail.com
 */
class Action extends Component
{
    /**
     * Событие: Перед запуском.
     */
    const EVENT_BEFORE_RUN = 'beforeRun';
    /**
     * Событие: После запуска.
     */
    const EVENT_AFTER_RUN = 'afterRun';

    /**
     * Идентификатор действия.
     * @var string
     * @since 1.0.0
     */
    protected $_id;
    /**
     * Контроллер действия.
     * @var Controller $_controller
     * @since 1.0.0
     */
    protected $_controller;

    /**
     * Возвращает идентификатор действия.
     * @return string
     * @since 1.0.0
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Возвращает контроллер действия.
     * @return Controller
     * @since 1.0.0
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * @inheritdoc
     * @param string $id Идентификатор дейстия.
     * @param Controller $controller Контроллер действия.
     * @since 1.0.0
     */
    public function __construct($id, $controller, $config = [])
    {
        $this->_id = $id;
        $this->_controller = $controller;

        parent::__construct($config);
    }

    /**
     * Используется для привязки параметров к действию.
     * @param array $parameters Входные параметры.
     * @return array Параметры действия.
     * @since 1.0.0
     */
    public function bindParameters($parameters)
    {
        $method = new \ReflectionMethod($this, 'run');
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
     * Запускает действие с параметрами.
     * @param array $parameters
     * @return mixed
     * @throws InvalidConfigException
     * @since 1.0.0
     */
    public function runWithParameters($parameters = [])
    {
        if (!method_exists($this, 'run'))
            throw new InvalidConfigException(get_class($this) . ' must define a "run()" method.');

        if (!Type::isArray($parameters))
            $parameters = [];

        $arguments = $this->_controller->bindActionParameters($this, $parameters);
        $arguments = $this->bindParameters($arguments);

        if (!Type::isArray($arguments))
            $arguments = [];

        if ($this->beforeRun($parameters)) {
            $result = call_user_func_array([$this, 'run'], $arguments);
            $result = $this->afterRun($parameters, $result);

            return $result;
        }

        return null;
    }

    /**
     * Вызывается перед запуском действия.
     * @param array $parameters
     * @return boolean
     * @since 1.0.0
     */
    protected function beforeRun($parameters)
    {
        $event = new ActionEvent($this, $parameters);
        $this->trigger(self::EVENT_BEFORE_RUN, $event);

        return true;
    }

    /**
     * Вызывается после действия.
     * @param array $parameters
     * @param mixed $result
     * @return mixed
     * @since 1.0.0
     */
    protected function afterRun($parameters, $result)
    {
        $event = new ActionEvent($this, $parameters);
        $event->result = $result;
        $this->trigger(self::EVENT_AFTER_RUN, $event);

        return $event->result;
    }
}