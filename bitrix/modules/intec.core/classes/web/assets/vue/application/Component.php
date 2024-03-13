<?php
namespace intec\core\web\assets\vue\application;

use intec\Core;
use intec\core\base\BaseObject;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;
use intec\core\io\Path;
use intec\core\web\assets\vue\Application;

class Component extends BaseObject
{
    protected $_name;

    protected $_application;

    protected $_directory;

    protected function includeComponentFile()
    {
        return include($this->getComponentFilePath()->getValue());
    }

    protected function includeLanguageFile($language = LANGUAGE_ID)
    {
        return include($this->getLanguageFilePath($language)->getValue());
    }

    protected function includeScriptFile()
    {
        return FileHelper::getFileData($this->getScriptFilePath()->getValue());
    }

    protected function includeStyleFile()
    {
        Core::$app->web->getCss()->addFile($this->getStyleFilePath());
    }

    protected function includeTemplateFile()
    {
        ob_start();
        include($this->getTemplateFilePath()->getValue());
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    public function getId()
    {
        return $this->_application->getComponentId($this);
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getApplication()
    {
        return $this->_application;
    }

    public function getDirectory()
    {
        return $this->_directory;
    }

    public function getMeta($language = LANGUAGE_ID)
    {
        return [
            'id' => $this->getId(),
            'name' => $this->_name,
            'data' => $this->getIsComponentFileExists() ? $this->includeComponentFile() : null,
            'messages' => $this->getIsLanguageFileExists($language) ? $this->includeLanguageFile($language) : [],
            'template' => [
                'id' => $this->getTemplateId()
            ]
        ];
    }

    public function getComponentFilePath()
    {
        return $this->_directory->add('component.php');
    }

    public function getLanguageFilePath($language = LANGUAGE_ID)
    {
        return $this->_directory->add('lang/'.$language.'.php');
    }

    public function getScriptFilePath()
    {
        return $this->_directory->add('component.js');
    }

    public function getStyleFilePath()
    {
        return $this->_directory->add('style.css');
    }

    public function getTemplateId()
    {
        return $this->_application->getComponentTemplateId($this);
    }

    public function getTemplateFilePath()
    {
        return $this->_directory->add('template.php');
    }

    public function __construct($application, $name, $directory, $config = [])
    {
        if (!($application instanceof Application))
            throw new \InvalidArgumentException('application');

        $this->_application = $application;
        $this->_name = $name;
        $this->_directory = Path::from($directory);

        if (!$this->getIsScriptFileExists())
            throw new \Exception();

        parent::__construct($config);
    }

    public function getIsComponentFileExists()
    {
        return FileHelper::isFile($this->getComponentFilePath()->getValue());
    }

    public function getIsLanguageFileExists($language = LANGUAGE_ID)
    {
        return FileHelper::isFile($this->getLanguageFilePath($language)->getValue());
    }

    public function getIsScriptFileExists()
    {
        return FileHelper::isFile($this->getScriptFilePath()->getValue());
    }

    public function getIsStyleFileExists()
    {
        return FileHelper::isFile($this->getStyleFilePath()->getValue());
    }

    public function getIsTemplateFileExists()
    {
        return FileHelper::isFile($this->getTemplateFilePath()->getValue());
    }

    public function getComponent($name)
    {
        return $this->_application->getComponent($name);
    }

    public function getModule($name)
    {
        return $this->_application->getModule($name);
    }

    public function useComponent($name)
    {
        return $this->_application->useComponent($name);
    }

    public function useModule($name)
    {
        return $this->_application->useModule($name);
    }

    public function begin($options = [])
    {
        if (!Type::isArray($options))
            $options = [];

        return Html::beginTag('component', ArrayHelper::merge($options, [
            'is' => $this->getId()
        ]));
    }

    public function end()
    {
        return Html::endTag('component');
    }

    public function apply($options = [])
    {
        return $this->begin($options).$this->end();
    }

    public function build($language = LANGUAGE_ID)
    {
        return 'function (loader) {'.
            'var meta = '.JavaScript::toObject($this->getMeta($language)).';'.
            'var component = '.$this->includeScriptFile().';'.
            ($this->getIsTemplateFileExists() ? 'component.template = \'#\' + meta.template.id;' : null).
            'loader(meta.id, component);'.
        '}';
    }

    public function render()
    {
        $content = null;

        if ($this->getIsStyleFileExists())
            $this->includeStyleFile();

        if ($this->getIsTemplateFileExists()) {
            $content = Html::script($this->includeTemplateFile(), [
                'id' => $this->getTemplateId(),
                'type' => 'text/x-template'
            ]);
        }

        return $content;
    }
}