<?php
namespace intec\constructor\builds\templates\editor\ajax;

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

    /**
     * Отправляет успешный ответ.
     * @param mixed|null $data Данные.
     * @return array
     */
    public function successResponse($data = null)
    {
        return [
            'status' => 'success',
            'data' => $data
        ];
    }

    /**
     * Отправляет ответ с ошибкой.
     * @param string|null $code
     * @param string|null $message
     * @param mixed|null $data
     * @return array
     */
    public function errorResponse($code = null, $message = null, $data = null)
    {
        return [
            'status' => 'error',
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
    }
}