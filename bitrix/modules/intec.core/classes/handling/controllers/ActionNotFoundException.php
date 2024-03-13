<?php
namespace intec\core\handling\controllers;

use intec\core\base\Exception;
use Throwable;

/**
 * Класс, представляющий исключение, если действие небыло найдено в результати разрешения маршрутизатором.
 * Class ActionNotFoundException
 * @property string|null $id Идентификатор действия. Только для чтения.
 * @package intec\core\handling\controllers
 * @since 1.0.0
 * @author Codelab
 * @author apocalypsisdimon@gmail.com
 */
class ActionNotFoundException extends Exception
{
    /**
     * Идентификатор действия.
     * @var string|null
     * @since 1.0.0
     */
    protected $_id;

    /**
     * Возвращает идентификатор действия.
     * @return string|null
     * @since 1.0.0
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @inheritdoc
     * @param string|null $id Идентификатор действия.
     */
    public function __construct($id = null, $message = '', $code = 0, Throwable $previous = null)
    {
        $this->_id = $id;
        parent::__construct($message, $code, $previous);
    }
}