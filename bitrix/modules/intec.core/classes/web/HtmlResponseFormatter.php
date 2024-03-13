<?php
namespace intec\core\web;

use intec\core\base\Component;

/**
 * Класс, представляющий преобразователь ответа в HTML формат.
 * Class HtmlResponseFormatter
 * @package intec\core\web
 */
class HtmlResponseFormatter extends Component implements ResponseFormatterInterface
{
    /**
     * Тип контента.
     * @var string
     */
    public $contentType = 'text/html';

    /**
     * @inheritdoc
     */
    public function format($response)
    {
        if (stripos($this->contentType, 'charset') === false)
            $this->contentType .= '; charset=' . $response->charset;

        $response->getHeaders()->set('Content-Type', $this->contentType);

        if ($response->data !== null)
            $response->content = $response->data;
    }
}
