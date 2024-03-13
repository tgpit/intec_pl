<?php
namespace intec\core\handling\controllers;

use intec\Core;
use intec\core\base\Component;
use intec\core\base\InvalidConfigException;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Type;
use intec\core\io\Path;

/**
 * Класс, представляющий маршрутизатор для контроллеров.
 * Class Router
 * @package intec\core\handling\controllers
 * @since 1.0.0
 * @author Codelab
 * @author apocalypsisdimon@gmail.com
 */
class Router extends Component
{
    /**
     * Событие: Перед разрешением.
     */
    const EVENT_BEFORE_RESOLVE = 'beforeResolve';
    /**
     * Событие: После разрешения.
     */
    const EVENT_AFTER_RESOLVE = 'afterResolve';

    /**
     * Директория файлов с контроллерами.
     * @var string|null
     * @since 1.0.0
     */
    public $directory;
    /**
     * Пространство имен с контроллерами.
     * @var string|null
     * @since 1.0.0
     */
    public $namespace;
    /**
     * Разделитель.
     * @var string
     * @since 1.0.0
     */
    public $separator = '/';
    /**
     * Приставка.
     * @var string
     * @since 1.0.0
     */
    public $addition = 'Controller';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->separator) || $this->separator === '-')
            $this->separator = '/';
    }

    /**
     * Разрешает контроллер по пути.
     * @param string $path Путь.
     * @return Route|null
     * @throws RouterPathException
     * @throws InvalidConfigException
     * @since 1.0.0
     */
    public function resolve($path)
    {
        $separator = $this->separator;

        if ($separator === '-')
            $separator = '/';

        if ($separator !== '/') {
            if (strpos($path, '/') !== false)
                throw new RouterPathException($path, 'Incorrect path', 0);

            $path = str_replace($separator, '/', $path);
        }

        if (
            preg_match('/^[a-z0-9\\-_\\/]+$/', $path) == false ||
            strpos($path, '//') !== false ||
            strpos($path, '--') !== false ||
            trim($path, '/-') !== $path
        ) throw new RouterPathException($path, 'Incorrect path', 1);

        $action = null;
        $path = explode('/', $path);

        if (count($path) > 1)
            $action = array_pop($path);

        $path = implode('/', $path);
        $path = str_replace(' ', '', ucwords(str_replace('-', ' ', $path)));
        $route = $this->beforeResolve($path);

        if ($route instanceof Route)
            return $route;

        if ($route) {
            $part = null;

            if (!empty($action) || Type::isNumeric($action))
                $part = str_replace(' ', '', ucwords(str_replace('-', ' ', $action)));

            if (!empty($this->directory)) {
                $file = null;

                if (!empty($part))
                    $file = Path::from($this->directory.'/'.$path.'/'.$part.$this->addition.'.php');

                if (empty($file) || !FileHelper::isFile($file->getValue()))
                    $file = Path::from($this->directory.'/'.$path.$this->addition.'.php');

                if (FileHelper::isFile($file->getValue()))
                    require_once($file->getValue());

                unset($file);
            }

            $class = null;

            if (!empty($part) || Type::isNumeric($part))
                $class = Path::normalize($this->namespace.'/'.$path.'/'.$part.$this->addition, true, '\\');

            if ((empty($class) && !Type::isNumeric($class)) || !is_subclass_of($class, Controller::className())) {
                $class = Path::normalize($this->namespace.'/'.$path.$this->addition, true, '\\');
            } else {
                $part = null;
            }

            if (empty($part) && !Type::isNumeric($part))
                $action = '';

            if (is_subclass_of($class, Controller::className())) {
                /** @var Controller $controller */
                $controller = Core::createObject($class);

                if (get_class($controller) === $class)
                    $route = new Route($controller, $action);
            }

            if ($route === null)
                $route = $this->noRoute($path);

            $route = $this->afterResolve($path, $route);
        }

        if (!($route instanceof Route))
            $route = null;

        return $route;
    }

    /**
     * Вызывается перед разрешением контроллера.
     * @param string $path Путь.
     * @return boolean|Route
     * @since 1.0.0
     */
    public function beforeResolve($path)
    {
        $event = new ResolveEvent($path);
        $this->trigger(self::EVENT_BEFORE_RESOLVE, $event);

        if ($event->route instanceof Route)
            return $event->route;

        return $event->isValid;
    }

    /**
     * Вызывается, когда маршрутизация не удалась (контроллер не был найден).
     * @param string $path Путь.
     * @return Route|null
     * @since 1.0.0
     */
    public function noRoute($path)
    {
        return null;
    }

    /**
     * Вызывается после разрешениея контроллера.
     * @param string $path Путь.
     * @param Route|null $route Маршрут.
     * @return Route|null
     * @since 1.0.0
     */
    public function afterResolve($path, $route)
    {
        $event = new ResolveEvent($path);
        $event->route = $route;

        return $event->route;
    }
}