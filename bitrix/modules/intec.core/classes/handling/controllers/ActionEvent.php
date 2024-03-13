<?php
namespace intec\core\handling\controllers;

use intec\core\base\Event;

/**
 * Класс, представляющий событие действия.
 * Class ActionEvent
 * @property Action $action Действие. Только для чтения.
 * @property array $parameters Параметры. Только для чтения.
 * @package intec\core\handling\controllers
 * @since 1.0.0
 * @author Codelab
 * @author apocalypsisdimon@gmail.com
 */
class ActionEvent extends Event
{
    /**
     * Действие.
     * @var Action
     * @since 1.0.0
     */
    protected $_action;

    /**
     * Параметры.
     * @var array
     * @since 1.0.0
     */
    protected $_parameters;

    /**
     * Результат выполнения.
     * @var mixed
     * @since 1.0.0
     */
    public $result;

    /**
     * Действие может быть запущено.
     * @var boolean
     * @since 1.0.0
     */
    public $isValid = true;

    /**
     * Возвращает действие.
     * @return Action
     * @since 1.0.0
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Возвращает параметры.
     * @return array
     * @since 1.0.0
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * @inheritdoc
     * @param Action $action Действие.
     * @param array $parameters Параметры.
     * @since 1.0.0
     */
    public function __construct($action, $parameters, $config = [])
    {
        $this->_action = $action;
        $this->_parameters = $parameters;
        parent::__construct($config);
    }
}