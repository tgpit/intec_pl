<?php
namespace intec\constructor\models\build;
IncludeModuleLangFile(__FILE__);

use intec\core\base\Component;
use intec\core\base\InvalidParamException;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Type;
use intec\constructor\models\Build;


/**
 * Класс для управления файлами галереи.
 * Class Gallery
 * @property Build $build
 * @property string $type
 * @property string $path
 * @package intec\constructor\models
 */
class File extends Component
{
    /**
     * Тип: Стиль Css
     */
    const TYPE_CSS = 'css';
    /**
     * Тип: Скрипт JavaScript
     */
    const TYPE_JAVASCRIPT = 'javascript';
    /**
     * Тип: Стиль SASS (SCSS)
     */
    const TYPE_SCSS = 'scss';
    /**
     * Тип: Виртуальный (Любая строка)
     */
    const TYPE_VIRTUAL = 'virtual';

    /**
     * Возвращает список типов файлов.
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_CSS => GetMessage('intec.constructor.models.build.file.type.css'),
            self::TYPE_JAVASCRIPT => GetMessage('intec.constructor.models.build.file.type.javascript'),
            self::TYPE_SCSS => GetMessage('intec.constructor.models.build.file.type.scss'),
            self::TYPE_VIRTUAL => GetMessage('intec.constructor.models.build.file.type.virtual')
        ];
    }

    /**
     * Возвращает список значений типов файлов.
     * @return array
     */
    public static function getTypesValues()
    {
        $values = self::getTypes();
        $values = ArrayHelper::getKeys($values);

        return $values;
    }

    /**
     * @var Build
     */
    protected $_build;
    /**
     * @var string
     */
    protected $_type;
    /**
     * @var string
     */
    protected $_data;

    /**
     * File constructor.
     * @param Build $build
     * @param string $type
     * @param string $data
     * @param string $config
     * @throws InvalidParamException
     */
    public function __construct($build, $type, $data, $config = [])
    {
        if (!Type::isArray($config))
            $config = [];

        if (!$build instanceof Build)
            throw new InvalidParamException('Invalid Build for '.self::className());

        if (!ArrayHelper::isIn($type, self::getTypesValues()))
            throw new InvalidParamException('Invalid Type for '.self::className());

        $this->_build = $build;
        $this->_type = $type;
        $this->_data = $data;

        parent::__construct($config);
    }

    /**
     * Возвращает сборку файла.
     * @return Build
     */
    public function getBuild()
    {
        return $this->_build;
    }

    /**
     * Возвращает тип подключаемого файла.
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Возвращает контент подключаемого файла.
     * @param bool $minimized
     * @return string
     */
    public function getContent($minimized = false)
    {
        if ($this->_type === self::TYPE_VIRTUAL)
            return $this->_data;

        return FileHelper::getFileData($this->getPath(false, DIRECTORY_SEPARATOR, $minimized));
    }

    /**
     * Возвращает путь до файла.
     * @param bool $relative
     * @param string $separator
     * @param bool $minimized
     * @return mixed
     */
    public function getPath($relative = false, $separator = DIRECTORY_SEPARATOR, $minimized = false)
    {
        if ($this->_type === self::TYPE_VIRTUAL)
            return null;

        $directory = $this->getBuild()->getDirectory(false, false, $separator);
        $path = $this->_data;

        if ($minimized) {
            $parts = FileHelper::getPathParts($path, $separator);
            $path = $parts['directory'].$separator.$parts['name']['less'].'.min.'.$parts['extension'];

            if (!FileHelper::isFile($directory.$separator.$path))
                $path = $this->_data;
        }

        return $this->getBuild()->getDirectory(
            false,
            $relative,
            $separator
        ).$separator.$path;
    }

    /**
     * Проверка существования файла.
     * @return bool
     */
    public function isExists()
    {
        if ($this->_type === self::TYPE_VIRTUAL)
            return true;

        return FileHelper::isFile($this->getPath());
    }
}