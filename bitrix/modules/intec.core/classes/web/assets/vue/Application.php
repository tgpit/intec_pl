<?php
namespace intec\core\web\assets\vue;

use intec\core\base\BaseObject;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\io\Path;
use intec\core\web\assets\vue\application\Component;
use intec\core\web\assets\vue\application\Module;

class Application extends BaseObject
{
    /**
     * @var string
     */
    protected $_id;

    /**
     * @var string
     */
    protected $_directory;

    /**
     * @var Component[]
     */
    protected $_components;

    /**
     * @var string
     */
    protected $_componentsDirectory;

    /**
     * @var Module[]
     */
    protected $_modules;

    /**
     * @var string
     */
    protected $_modulesDirectory;

    public function __construct($directory, $id, $config = [])
    {
        $this->_id = $id;
        $this->_directory = $directory;
        $this->_components = [];
        $this->_modules = [];

        parent::__construct($config);
    }

    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param Component $component
     * @return string
     */
    public function getComponentId($component)
    {
        return Html::getId('v-'.$component->getName());
    }

    /**
     * @param Component $component
     * @return string
     */
    public function getComponentTemplateId($component)
    {
        return Html::getId('vue-component-'.$this->_id.'-'.$component->getName());
    }

    public function getDirectory()
    {
        return $this->_directory;
    }

    public function getComponentsDirectory()
    {
        if ($this->_componentsDirectory === null)
            $this->_componentsDirectory = $this->_directory.'/components';

        return $this->_componentsDirectory;
    }

    public function setComponentsDirectory($value)
    {
        $this->_componentsDirectory = $value;
    }

    public function getComponent($name)
    {
        if (isset($this->_components[$name]))
            return $this->_components[$name];

        return null;
    }

    public function useComponent($name)
    {
        if (isset($this->_components[$name]))
            return $this->_components[$name];

        $directory = Path::from($this->getComponentsDirectory().'/'.$name);
        $component = null;

        if (FileHelper::isDirectory($directory->getValue()))
            try {
                $component = new Component($this, $name, $directory);
            } catch (\Exception $exception) {}

        if ($component !== null)
            $this->_components[$name] = $component;

        return $component;
    }

    public function getModulesDirectory()
    {
        if ($this->_modulesDirectory === null)
            $this->_modulesDirectory = $this->_directory.'/modules';

        return $this->_modulesDirectory;
    }

    public function setModulesDirectory($value)
    {
        $this->_modulesDirectory = $value;
    }

    public function getModule($name)
    {
        if (isset($this->_modules[$name]))
            return $this->_modules[$name];

        return null;
    }

    public function useModule($name, $properties = [])
    {
        if (isset($this->_modules[$name]))
            return $this->_modules[$name];

        $directory = Path::from($this->getModulesDirectory().'/'.$name);
        $module = null;

        if (FileHelper::isDirectory($directory->getValue()))
            try {
                $module = new Module($this, $name, $directory, $properties);
            } catch (\Exception $exception) {}

        if ($module !== null)
            $this->_modules[$name] = $module;

        return $module;
    }

    public function build($language = LANGUAGE_ID)
    {
        $content = '(function () {'.
            'var components = {};'.
            'var mixins = [];'.
            'var loader = {'.
                '\'extend\': function (part) { mixins.push(part); },'.
                '\'extendComponents\': function (name, component) { component.name = name; component.components = components; components[name] = component; },'.
                '\'compose\': function () {'.
                    'return {'.
                        '\'components\': components,'.
                        '\'mixins\': mixins'.
                    '};'.
                '}'.
            '};';

        $count = 0;
        $modules = $this->_modules;

        while (count($modules) > 0) {
            foreach ($modules as $module) {
                $content .= '('.$module->build($language).')(loader.extend);';
                $count++;
            }

            $modules = array_slice($this->_modules, $count);
        }

        $count = 0;
        $components = $this->_components;

        while (count($components) > 0) {
            foreach ($components as $component) {
                $content .= '('.$component->build($language).')(loader.extendComponents);';
                $count++;
            }

            $components = array_slice($this->_components, $count);
        }

        $content .= 'return loader;'.
            '})()';

        return $content;
    }

    public function render()
    {
        $content = '';
        $count = 0;
        $components = $this->_components;

        while (count($components) > 0) {
            foreach ($components as $component) {
                $content .= $component->render();
                $count++;
            }

            $components = array_slice($this->_components, $count);
        }

        return $content;
    }
}