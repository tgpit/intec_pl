<?php
namespace intec\core\web;

use intec\core\base\Component;
use intec\core\helpers\Json;

/**
 * Класс, представляющий преобразователь ответа в формат JSON.
 * Class JsonResponseFormatter
 * @package intec\core\web
 */
class JsonResponseFormatter extends Component implements ResponseFormatterInterface
{
    /**
     * Тип контента: JSONP.
     */
    const CONTENT_TYPE_JSONP = 'application/javascript; charset=UTF-8';
    /**
     * Тип контента: JSON.
     */
    const CONTENT_TYPE_JSON = 'application/json; charset=UTF-8';
    /**
     * Тип контента: HAL+JSON.
     */
    const CONTENT_TYPE_HAL_JSON = 'application/hal+json; charset=UTF-8';

    /**
     * Тип контента.
     * @var string
     */
    public $contentType;
    /**
     * Использовать JSONP.
     * @var boolean
     */
    public $useJsonp = false;
    /**
     * Конвертировать кодировку при форматировании.
     * @var boolean
     */
    public $convert = false;
    /**
     * Опции кодирования.
     * @var integer
     */
    public $encodeOptions = 320;
    /**
     * Красивый вывод.
     * @var boolean
     */
    public $prettyPrint = false;

    /**
     * @inheritdoc
     */
    public function format($response)
    {
        if ($this->contentType === null) {
            $this->contentType = $this->useJsonp
                ? self::CONTENT_TYPE_JSONP
                : self::CONTENT_TYPE_JSON;
        } else if (strpos($this->contentType, 'charset') === false) {
            $this->contentType .= '; charset=UTF-8';
        }

        $response->getHeaders()->set('Content-Type', $this->contentType);
  
        if ($this->useJsonp) {
            $this->formatJsonp($response);
        } else {
            $this->formatJson($response);
        }
    }

    /**
     * Форматирует ответ в JSON.
     * @param Response $response
     */
    protected function formatJson($response)
    {
        if ($response->data !== null) {
            $options = $this->encodeOptions;

            if ($this->prettyPrint)
                $options |= JSON_PRETTY_PRINT;

            $response->content = Json::encode($response->data, $options, $this->convert);
        } else if ($response->content === null) {
            $response->content = 'null';
        }
    }

    /**
     * Форматирует ответ в JSONP.
     * @param Response $response
     */
    protected function formatJsonp($response)
    {
        if (is_array($response->data) && isset($response->data['data'], $response->data['callback'])) {
            $response->content = sprintf(
                '%s(%s);',
                $response->data['callback'],
                Json::htmlEncode($response->data['data'], $this->convert)
            );
        } else if ($response->data !== null) {
            $response->content = '';
        }
    }
}
