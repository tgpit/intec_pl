<?php
namespace intec\constructor\models;
IncludeModuleLangFile(__FILE__);

use intec\Core;
use intec\core\base\Exception;
use intec\core\base\BaseObject;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\RegExp;
use intec\core\helpers\Type;
use intec\constructor\models\build\File;
use intec\constructor\models\build\Gallery;
use intec\constructor\models\build\Layout;
use intec\constructor\models\build\Page;
use intec\constructor\models\build\Presets;
use intec\constructor\models\build\Template;

/**
 * Class Build
 * @package intec\constructor\models
 */
class Build extends BaseObject
{
    /**
     * Текущая сборка.
     * @var Build
     */
    protected static $current;

    /**
     * Мета-информация сборки.
     * @var array|null
     */
    protected $_meta = null;
    /**
     * Текущая страница.
     * @var Page
     */
    protected $_page;
    /**
     * Layout сборки.
     * @var Layout[]|null
     */
    protected $_layouts;
    /**
     * Галерея сборки.
     * @var Gallery|null
     */
    protected $_gallery;
    /**
     * Виджеты сборки.
     * @var Presets|null
     */
    protected $_presets;

    /**
     * Текущий шаблон.
     * @var Template
     */
    protected $_template;

    /**
     * Возвращает текущую сборку в соответствии с шаблоном сайта.
     * @return Build|null
     */
    public static function getCurrent()
    {
        if (static::$current === null)
            static::$current = new static();

        return static::$current;
    }

    /**
     * Возвращает путь до сборки.
     * @param bool $old
     * @param bool $relative
     * @param string $separator
     * @return string|null
     */
    public function getDirectory($old = false, $relative = false, $separator = DIRECTORY_SEPARATOR)
    {
        $code = SITE_TEMPLATE_ID;
        $path = null;
        $local = FileHelper::isDirectory('@root/local/templates/'.$code);

        if ($local) {
            if (!$relative) {
                $path = Core::getAlias('@root/local/templates/'.$code);
            } else {
                $path = '/local/templates/'.$code;
            }
        } else {
            if (!$relative) {
                $path = Core::getAlias('@templates/'.$code);
            } else {
                $path = '/bitrix/templates/'.$code;
            }
        }

        return FileHelper::normalizePath($path, $separator);
    }

    /**
     * Возвращает мета-данные шаблона.
     * @param bool $reset
     * @return array|mixed
     */
    public function getMeta($reset = false)
    {
        if ($this->_meta === null || $reset) {
            $directory = $this->getDirectory();
            $path = $directory . DIRECTORY_SEPARATOR . 'meta.php';
            $this->_meta = [];

            if (FileHelper::isFile($path))
                $this->_meta = include($path);
        }

        return $this->_meta;
    }

    /**
     * Возвращает значение мета-переменной шаблона.
     * @param string|array $key
     * @param bool $reset
     * @return mixed|null
     */
    public function getMetaValue($key, $reset = false)
    {
        $meta = $this->getMeta($reset);
        return ArrayHelper::getValue($meta, $key);
    }

    /**
     * Возвращает текущую страницу.
     * @param bool $reset
     * @return Page
     */
    public function getPage($reset = false)
    {
        if ($this->_page === null || $reset) {
            $directory = SITE_DIR;
            $path = Core::$app->request->getScriptUrl();

            if (!empty($_SERVER['REAL_FILE_PATH']))
                $path = $_SERVER['REAL_FILE_PATH'];

            $path = RegExp::replaceBy(
                '/^'.RegExp::escape($directory).'/',
                '',
                $path
            );

            $this->_page = new Page($this, $path, $directory);
        }

        return $this->_page;
    }

    /**
     * Возвращает Layout для текущей сборки.
     * @param boolean $reset
     * @return Layout[]
     */
    public function getLayouts($reset = false)
    {
        if ($this->_layouts === null || $reset) {
            $this->_layouts = [];
            $directory = $this->getDirectory(false, false, '/').'/layouts';
            $entries = FileHelper::getDirectoryEntries($directory, false);

            foreach ($entries as $code) {
                try {
                    $layout = new Layout($this, $directory.'/'.$code, $code);
                    $this->_layouts[$layout->getCode()] = $layout;
                } catch (Exception $exception) {}
            }
        }

        return $this->_layouts;
    }

    /**
     * Возвращает файлы шаблона.
     * @return File[]
     */
    public function getFiles()
    {
        $meta = $this->getMeta();
        $files = ArrayHelper::getValue($meta, 'files');
        $result = [];

        if (Type::isArray($files)) {
            foreach ($files as $file) {
                $file = ArrayHelper::merge([
                    'type' => null,
                    'content' => null,
                    'path' => null
                ], $file);

                $file = new File($this, $file['type'], $file['type'] !== File::TYPE_VIRTUAL ? $file['path'] : $file['content']);

                if ($file->isExists())
                    $result[] = $file;
            }
        }

        return $result;
    }

    /**
     * Возвращает шаблон, подходящий под условия.
     * @param string|null $directory Текущая директория сайта.
     * @param string|null $path Текущий путь.
     * @param string|null $url Текущий адрес Url.
     * @param array|null $parametersGet Параметры запроса.
     * @param array|null $parametersPage Параметры страницы.
     * @return Template|null
     */
    public function getTemplate($directory = null, $path = null, $url = null, $parametersGet = null, $parametersPage = null)
    {
        if ($this->_template === null)
            $this->_template = new Template();

        return $this->_template;
    }

    /**
     * Возвращает экземпляр галереи для сборки.
     * @param boolean $reset Сбросить кеш.
     * @return Gallery
     */
    public function getGallery($reset = false)
    {
        if ($this->_gallery === null || $reset)
            $this->_gallery = new Gallery($this);

        return $this->_gallery;
    }

    /**
     * Возвращает пресеты для сборки.
     * @param boolean $reset Сбросить кеш.
     * @return Presets
     */
    public function getPresets($reset = false)
    {
        if ($this->_presets === null || $reset)
            $this->_presets = Presets::all($this);

        return $this->_presets;
    }
}