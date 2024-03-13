<?php
namespace intec\core\handling\controllers;

use intec\Core;
use intec\core\base\Component;
use intec\core\base\InvalidConfigException;
use intec\core\helpers\Type;

/**
 * Класс, представляющий контроллер.
 * Class Controller
 * @property string $defaultAction Действие, выполняемое по умолчанию.
 * @property string $action Текущее выполняемое действие. Только для чтения.
 * @property string $actionPrefix Префикс действий. Только для чтения.
 * @package intec\core\handling\controllers
 * @since 1.0.0
 * @author Codelab
 * @author apocalypsisdimon@gmail.com
 */
class Controller extends Component
{
    /**
     * Событие: Перед действием.
     */
    const EVENT_BEFORE_ACTION = 'beforeAction';
    /**
     * Событие: После действия.
     */
    const EVENT_AFTER_ACTION = 'afterAction';

    /**
     * Идентификатор действия по умолчанию.
     * @var string
     * @since 1.0.0
     */
    public $defaultAction = 'index';

    /**
     * @var Action Действие, выполняемое в данный момент.
     * @since 1.0.0
     */
    protected $_action;

    /**
     * Возвращает действие, выполняемое в данный момент.
     * @return Action
     * @since 1.0.0
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Возвращает префикс действия.
     * @return string
     * @since 1.0.0
     */
    public function getActionPrefix()
    {
        return 'action';
    }

    /**
     * Возвращает классы действий.
     * @return array
     * @since 1.0.0
     */
    public function actions()
    {
        return [];
    }

    /**
     * @param $id
     * @param array $parameters
     * @return bool|mixed|null
     * @throws ActionNotFoundException
     * @throws InvalidConfigException
     * @since 1.0.0
     */
    public function runAction($id, $parameters = [])
    {
        if (!Type::isArray($parameters))
            $parameters = [];

        $action = $this->createAction($id);

        if ($action === null) {
            $this->noAction($id, $parameters);
        }

        $oldAction = $this->_action;
        $this->_action = $action;
        $result = null;

        if ($this->beforeAction($action, $parameters)) {
            $result = $action->runWithParameters($parameters);
            $result = $this->afterAction($action, $parameters, $result);
        }

        $this->_action = $oldAction;

        return $result;
    }

    /**
     *
     * Связывает параметры действия.
     * @param Action $action Действие.
     * @param array $parameters Параметры.
     * @return array
     * @since 1.0.0
     */
    public function bindActionParameters($action, $parameters)
    {
        return $parameters;
    }

    /**
     * Создает действие по идентификатору. Если действия не существует, возвращает `null`.
     * @param string $id Идентификатор действия.
     * @return Action|null
     * @since 1.0.0
     */
    public function createAction($id)
    {
        if (empty($id) && !Type::isNumeric($id))
            $id = $this->defaultAction;

        $actions = $this->actions();

        if (isset($actions[$id])) {
            return Core::createObject($actions[$id], [$id, $this]);
        } else if (preg_match('/^[a-z0-9\\-_]+$/', $id) && strpos($id, '--') === false && trim($id, '-') === $id) {
            $methodName = $this->getActionPrefix().str_replace(' ', '', ucwords(str_replace('-', ' ', $id)));

            if (method_exists($this, $methodName)) {
                $method = new \ReflectionMethod($this, $methodName);

                if ($method->isPublic() && $method->getName() === $methodName)
                    return new InlineAction($id, $this, $methodName);
            }
        }

        return null;
    }

    /**
     * Проверяет существование действия.
     * @param string $id Идентификатор действия.
     * @return boolean
     * @since 1.0.0
     */
    public function isActionExists($id)
    {
        if (!empty($id) || Type::isNumeric($id))
            $id = $this->defaultAction;

        $actions = $this->actions();

        if (isset($actions[$id])) {
            return true;
        } else if (preg_match('/^[a-z0-9\\-_]+$/', $id) && strpos($id, '--') === false && trim($id, '-') === $id) {
            $methodName = $this->getActionPrefix().str_replace(' ', '', ucwords(str_replace('-', ' ', $id)));

            return method_exists($this, $methodName);
        }

        return false;
    }

    /**
     * Этот метод вызывается перед тем, как действие будет выполнено.
     * Если возвращает `true`, выполнение действия будет продолжено.
     * @param Action $action Объект действия.
     * @param array $parameters Параметры действия.
     * @return boolean
     * @since 1.0.0
     */
    public function beforeAction($action, $parameters)
    {
        $event = new ActionEvent($action, $parameters);
        $this->trigger(self::EVENT_BEFORE_ACTION, $event);

        return $event->isValid;
    }

    /**
     * Этот метод вызывается после того, как действие будет выполнено.
     * Возвращает результат выполнения действия.
     * @param Action $action Объект действия.
     * @param array $parameters Параметры действия.
     * @param mixed $result Результат выполнения действия.
     * @return boolean
     * @since 1.0.0
     */
    public function afterAction($action, $parameters, $result)
    {
        $event = new ActionEvent($action, $parameters);
        $event->result = $result;
        $this->trigger(self::EVENT_AFTER_ACTION, $event);

        return $event->result;
    }

    /**
     * Этот метод вызывается, если при запуске не было найдено действие.
     * @param string $id Идентификатор вызываемого действия.
     * @param mixed $parameters Параметры.
     * @return mixed
     * @throws ActionNotFoundException
     * @since 1.0.0
     */
    public function noAction($id, $parameters)
    {
        throw new ActionNotFoundException($id, 'Action with identifier "'.$id.'" not found.');
    }
}