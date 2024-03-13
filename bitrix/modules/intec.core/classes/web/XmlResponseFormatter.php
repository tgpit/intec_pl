<?php
namespace intec\core\web;

use DOMDocument;
use DOMElement;
use DOMException;
use DOMText;
use intec\core\base\Arrayable;
use intec\core\base\Component;
use intec\core\helpers\StringHelper;

/**
 * Класс, представляющий преобразователь ответа в XML формат.
 * Class XmlResponseFormatter
 * @package intec\core\web
 */
class XmlResponseFormatter extends Component implements ResponseFormatterInterface
{
    /**
     * Тип контента.
     * @var string
     */
    public $contentType = 'application/xml';
    /**
     * Версия.
     * @var string
     */
    public $version = '1.0';
    /**
     * Кодировка.
     * @var string
     */
    public $encoding;
    /**
     * Корневой тег.
     * @var string
     */
    public $rootTag = 'response';
    /**
     * Тег элемента.
     * @var string
     */
    public $itemTag = 'item';
    /**
     * Использовать обходной объект как массив.
     * @var boolean
     */
    public $useTraversableAsArray = true;
    /**
     * Использовать теги объекта.
     * @var boolean
     */
    public $useObjectTags = true;

    /**
     * @inheritdoc
     */
    public function format($response)
    {
        $charset = $this->encoding === null ? $response->charset : $this->encoding;

        if (stripos($this->contentType, 'charset') === false)
            $this->contentType .= '; charset=' . $charset;

        $response->getHeaders()->set('Content-Type', $this->contentType);

        if ($response->data !== null) {
            $dom = new DOMDocument($this->version, $charset);

            if (!empty($this->rootTag)) {
                $root = new DOMElement($this->rootTag);
                $dom->appendChild($root);
                $this->buildXml($root, $response->data);
            } else {
                $this->buildXml($dom, $response->data);
            }

            $response->content = $dom->saveXML();
        }
    }

    /**
     * Строит XML.
     * @param DOMElement $element
     * @param mixed $data
     */
    protected function buildXml($element, $data)
    {
        if (is_array($data) ||
            ($data instanceof \Traversable && $this->useTraversableAsArray && !$data instanceof Arrayable)
        ) {
            foreach ($data as $name => $value) {
                if (is_int($name) && is_object($value)) {
                    $this->buildXml($element, $value);
                } elseif (is_array($value) || is_object($value)) {
                    $child = new DOMElement($this->getValidXmlElementName($name));
                    $element->appendChild($child);
                    $this->buildXml($child, $value);
                } else {
                    $child = new DOMElement($this->getValidXmlElementName($name));
                    $element->appendChild($child);
                    $child->appendChild(new DOMText($this->formatScalarValue($value)));
                }
            }
        } elseif (is_object($data)) {
            if ($this->useObjectTags) {
                $child = new DOMElement(StringHelper::basename(get_class($data)));
                $element->appendChild($child);
            } else {
                $child = $element;
            }
            if ($data instanceof Arrayable) {
                $this->buildXml($child, $data->toArray());
            } else {
                $array = [];
                foreach ($data as $name => $value) {
                    $array[$name] = $value;
                }
                $this->buildXml($child, $array);
            }
        } else {
            $element->appendChild(new DOMText($this->formatScalarValue($data)));
        }
    }

    /**
     * Форматирует скаларное значение.
     * @param mixed $value Значение.
     * @return string
     */
    protected function formatScalarValue($value)
    {
        if ($value === true)
            return 'true';

        if ($value === false)
            return 'false';

        return (string) $value;
    }

    /**
     * Возвращает корректное имя для XML.
     * @param string $name Имя.
     * @return string
     */
    protected function getValidXmlElementName($name)
    {
        if (empty($name) || is_int($name) || !$this->isValidXmlName($name)) {
            return $this->itemTag;
        }

        return $name;
    }

    /**
     * Проверяет имя XML на корректность.
     * @param string $name Имя.
     * @return bool
     */
    protected function isValidXmlName($name)
    {
        try {
            new DOMElement($name);
            return true;
        } catch (DOMException $e) {
            return false;
        }
    }
}
