<?php
namespace intec\core\handling\controllers;

use intec\core\base\Exception;
use Throwable;

/**
 * Класс, представляющий исключение, если предоставленный путь неверный.
 * Class RouterPathException
 * @property string $path Путь. Только для чтения.
 * @package intec\core\handling\controllers
 * @since 1.0.0
 * @author Codelab
 * @author apocalypsisdimon@gmail.com
 */
class RouterPathException extends Exception
{
    /**
     * Путь.
     * @var string|null
     * @since 1.0.0
     */
    public $_path;

    /**
     * Возвращает путь.
     * @return string|null
     * @since 1.0.0
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * @inheritdoc
     * @param string|null $path Путь.
     */
    public function __construct($path = null, $message = '', $code = 0, Throwable $previous = null)
    {
        $this->_path = $path;
        parent::__construct($message, $code, $previous);
    }
}