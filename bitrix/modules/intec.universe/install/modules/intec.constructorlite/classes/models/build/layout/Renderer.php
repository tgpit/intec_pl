<?php
namespace intec\constructor\models\build\layout;

use intec\core\base\BaseObject;
use intec\constructor\base\Renderable;
use intec\constructor\models\build\Layout;
use intec\constructor\models\build\Template;

abstract class Renderer extends BaseObject implements Renderable
{
    /**
     * @var Layout|null
     */
    protected $_layout;
    /**
     * @var Template|null
     */
    protected $_template;

    /**
     * @return Layout|null
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * @return Template|null
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * @return boolean
     */
    public abstract function getIsRenderAllowed();

    /**
     * @param Layout $layout
     * @param Template $template
     * @return boolean
     */
    public function renderStart($layout, $template)
    {
        if (!($layout instanceof Layout) || !($template instanceof Template))
            return false;

        $this->_layout = $layout;
        $this->_template = $template;

        return true;
    }

    /**
     *
     */
    public function render()
    {
        include($this->getLayout()->getTemplatePath());
    }

    /**
     * @param Zone $zone
     */
    public abstract function renderZone($zone);

    /**
     *
     */
    public function renderEnd()
    {
        $this->_layout = null;
        $this->_template = null;
    }
}