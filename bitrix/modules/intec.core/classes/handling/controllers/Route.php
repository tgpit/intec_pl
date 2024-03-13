<?php
namespace intec\core\handling\controllers;

use intec\core\base\Component;
use intec\core\base\InvalidConfigException;

/**
 * Класс, представляющий маршрут роутера для контроллеров.
 * Class Route
 * @property string $actionId Идентификатор действия. Только для чтения.
 * @property Controller $controller Контроллер. Только для чтения.
 * @package intec\core\handling\controllers
 * @since 1.0.0
 * @author Codelab
 * @author apocalypsisdimon@gmail.com
 */
class Route extends Component
{
    /**
     * Идентификатор действия.
     * @var string
     * @since 1.0.0
     */
    protected $_actionId;

    /**
     * Контроллер.
     * @var Controller
     * @since 1.0.0
     */
    protected $_controller;

    /**
     * Возвращает идентификатор действия.
     * @return string
     * @since 1.0.0
     */
    public function getActionId()
    {
        return $this->_actionId;
    }

    /**
     * Возвращает контроллер.
     * @return Controller
     * @since 1.0.0
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * @inheritdoc
     * @param Controller $controller Контроллер.
     * @param string $actionId Идентификатор действия.
     * @since 1.0.0
     */
    public function __construct($controller, $actionId, $config = [])
    {
        $this->_controller = $controller;
        $this->_actionId = $actionId;
        parent::__construct($config);
    }

    /**
     * Запускает действие контроллера.
     * @param array $parameters Параметры.
     * @return mixed
     * @throws ActionNotFoundException
     * @throws InvalidConfigException
     * @since 1.0.0
     */
    public function run($parameters)
    {
        return $this->_controller->runAction($this->_actionId, $parameters);
    }
}