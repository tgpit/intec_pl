<?php
namespace intec\regionality\services\locator;

use intec\core\base\Component;
use intec\core\helpers\Type;
use intec\regionality\services\locator\service\ExtensionActionEvent;

/**
 * Представляет сервис для разрешения местоположения по IP-адресу.
 * Class Service
 * @property Extensions|Extension[] $extensions
 * @package intec\regionality\services\locator
 * @author apocalypsisdimon@gmail.com
 */
class Service extends Component
{
    /**
     * Событие: Добавление расширения.
     */
    const EVENT_ADD = 'add';
    /**
     * Событие: Удаление расширения.
     */
    const EVENT_REMOVE = 'remove';

    /**
     * Экземпляр сервиса.
     * @var static
     */
    protected static $_instance;

    /**
     * Возвращает экземпляр сервиса.
     * @return static
     */
    public static function getInstance()
    {
        if (static::$_instance === null)
            static::$_instance = new static();

        return static::$_instance;
    }

    /**
     * Расширения сервиса.
     * @var Extensions
     */
    protected $_extensions;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->_extensions = new Extensions();
        $this->add(new extensions\BitrixGeoIPManager());
        $this->add(new extensions\BitrixStatistic());
        $this->add(new extensions\IPGeoBase());
        $this->add(new extensions\GeoIP());
    }

    /**
     * Добавляет новое расширение.
     * @param Extension $extension
     */
    public function add($extension)
    {
        if (!($extension instanceof Extension))
            return;

        $code = $extension->getCode();

        if (empty($code))
            return;

        if (
            $this->_extensions->has($extension) ||
            $this->_extensions->exists($code)
        ) return;

        $this->_extensions->set($code, $extension);
        $event = new ExtensionActionEvent();
        $event->extension = $extension;
        $this->trigger(self::EVENT_ADD, $event);
    }

    /**
     * Возвращает зарегистрированные расширения.
     * @param boolean $collection Возвращать как коллекцию.
     * @return Extensions|Extension[]
     */
    public function getExtensions($collection = true)
    {
        if ($collection)
            return Extensions::from($this->_extensions);

        return $this->_extensions->asArray();
    }

    /**
     * Удаляет расширение.
     * @param Extension $extension
     */
    public function remove($extension)
    {
        if ($this->_extensions->has($extension)) {
            $this->_extensions->remove($extension);

            $event = new ExtensionActionEvent();
            $event->extension = $extension;
            $this->trigger(self::EVENT_REMOVE, $event);
        }
    }

    /**
     * Удаляет все расширения.
     */
    public function removeAll()
    {
        $extensions = $this->_extensions->asArray();
        $this->_extensions->removeAll();

        foreach ($extensions as $extension) {
            $event = new ExtensionActionEvent();
            $event->extension = $extension;
            $this->trigger(self::EVENT_REMOVE, $event);
        }
    }

    /**
     * Разрешает код региона за счет всех доступных расширений.
     * @param string $address
     * @param array|null $extensions
     * @return null|string
     */
    public function resolve($address, $extensions = null)
    {
        if (!Type::isArray($extensions))
            $extensions = [];

        if (empty($extensions))
            $extensions = $this->_extensions;

        foreach ($extensions as $extension) {
            if (!($extension instanceof Extension))
                continue;

            /** @var Extension $extension */
            if ($extension->getIsAvailable()) {
                $result = $extension->resolve($address);

                if (!empty($result))
                    return $result;
            }
        }

        return null;
    }
}