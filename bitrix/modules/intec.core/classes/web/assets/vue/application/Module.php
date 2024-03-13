<?php
namespace intec\core\web\assets\vue\application;

use intec\core\base\BaseObject;
use intec\core\helpers\FileHelper;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;
use intec\core\io\Path;
use intec\core\web\assets\vue\Application;

class Module extends BaseObject
{
    protected $_name;

    protected $_application;

    protected $_directory;

    protected $_properties;

    protected function includeModuleFile()
    {
        return include($this->getModuleFilePath()->getValue());
    }

    protected function includeLanguageFile($language = LANGUAGE_ID)
    {
        return include($this->getLanguageFilePath($language)->getValue());
    }

    protected function includeScriptFile()
    {
        return FileHelper::getFileData($this->getScriptFilePath()->getValue());
    }

    public function getName()
    {
        return $this->_application;
    }

    public function getApplication()
    {
        return $this->_application;
    }

    public function getDirectory()
    {
        return $this->_directory;
    }

    public function getProperties()
    {
        return $this->_properties;
    }

    public function getMeta($language = LANGUAGE_ID)
    {
        return [
            'name' => $this->_name,
            'data' => $this->getIsModuleFileExists() ? $this->includeModuleFile() : null,
            'properties' => $this->_properties,
            'messages' => $this->getIsLanguageFileExists($language) ? $this->includeLanguageFile($language) : []
        ];
    }

    public function getModuleFilePath()
    {
        return $this->_directory->add('module.php');
    }

    public function getLanguageFilePath($language = LANGUAGE_ID)
    {
        return $this->_directory->add('lang/'.$language.'.php');
    }

    public function getScriptFilePath()
    {
        return $this->_directory->add('module.js');
    }

    public function __construct($application, $name, $directory, $properties = [], $config = [])
    {
        if (!($application instanceof Application))
            throw new \InvalidArgumentException('application');

        $this->_application = $application;
        $this->_name = $name;
        $this->_directory = Path::from($directory);

        if (!Type::isArray($properties))
            $properties = [];

        $this->_properties = $properties;

        if (!$this->getIsScriptFileExists())
            throw new \Exception();

        parent::__construct($config);
    }

    public function getIsModuleFileExists()
    {
        return FileHelper::isFile($this->getModuleFilePath()->getValue());
    }

    public function getIsLanguageFileExists($language = LANGUAGE_ID)
    {
        return FileHelper::isFile($this->getLanguageFilePath($language)->getValue());
    }

    public function getIsScriptFileExists()
    {
        return FileHelper::isFile($this->getScriptFilePath()->getValue());
    }

    public function getModule($name)
    {
        return $this->_application->getModule($name);
    }

    public function useModule($name, $properties = [])
    {
        return $this->_application->useModule($name, $properties);
    }

    public function build($language = LANGUAGE_ID)
    {
        return 'function (loader) {'.
            'var meta = '.JavaScript::toObject($this->getMeta($language)).';'.
            'var module = '.$this->includeScriptFile().';'.
            'loader(module);'.
        '}';
    }
}