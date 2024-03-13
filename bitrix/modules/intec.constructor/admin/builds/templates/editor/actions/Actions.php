<?php
namespace intec\constructor\builds\templates\editor\actions;

use intec\Core;
use intec\core\web\Request;
use intec\constructor\models\Build;
use intec\constructor\models\build\Template;

abstract class Actions extends \intec\core\handling\Actions
{
    /**
     * @var Build
     */
    public $build;
    /**
     * @var Template
     */
    public $template;
    /**
     * @var Request
     */
    public $request;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        global $build;
        global $template;

        $this->build = $build;
        $this->template = $template;
        $this->request = Core::$app->request;
    }
}