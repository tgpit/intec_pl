<?php
namespace intec\core\web;

use intec\Core;
use intec\core\base\Component;
use intec\core\base\InvalidConfigException;
use intec\core\helpers\Encoding;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Inflector;
use intec\core\helpers\StringHelper;
use InvalidArgumentException;

/**
 * Класс, представляющий ответ.
 * Class Response
 * @package intec\core\web
 */
class Response extends Component
{
    /**
     * Событие: Перед отправкой.
     */
    const EVENT_BEFORE_SEND = 'beforeSend';
    /**
     * Событие: После отправки.
     */
    const EVENT_AFTER_SEND = 'afterSend';
    /**
     * Событие: После подготовки.
     */
    const EVENT_AFTER_PREPARE = 'afterPrepare';

    /**
     * Формат: Сырой.
     */
    const FORMAT_RAW = 'raw';
    /**
     * Формат: HTML.
     */
    const FORMAT_HTML = 'html';
    /**
     * Формат: JSON.
     */
    const FORMAT_JSON = 'json';
    /**
     * Формат: JSONP.
     */
    const FORMAT_JSONP = 'jsonp';
    /**
     * Формат: XML.
     */
    const FORMAT_XML = 'xml';

    /**
     * Куки ответа.
     * @var CookieCollection
     */
    protected $_cookies;
    /**
     * Заголовки ответа.
     * @var HeaderCollection
     */
    protected $_headers;
    /**
     * Код ответа.
     * @var integer
     */
    protected $_statusCode = 200;

    /**
     * Принимаемый MIME тип.
     * @var string
     */
    public $acceptMimeType;
    /**
     * Кодировка.
     * @var string
     */
    public $charset;
    /**
     * Контент.
     * @var mixed
     */
    public $content;
    /**
     * Данные.
     * @var mixed
     */
    public $data;
    /**
     * Формат.
     * @var string
     */
    public $format = self::FORMAT_HTML;
    /**
     * Форматеры.
     * @var array
     */
    public $formatters = [];
    /**
     * Статус отправки.
     * @var boolean
     */
    public $isSent = false;
    /**
     * Текст статуса.
     * @var string
     */
    public $statusText = 'OK';
    /**
     * Поток.
     * @var resource
     */
    public $stream;
    /**
     * Версия.
     * @var string
     */
    public $version;

    /**
     * Форматеры, доступные по умолчанию.
     * @return array
     */
    protected function defaultFormatters()
    {
        return [
            self::FORMAT_HTML => [
                'class' => HtmlResponseFormatter::className()
            ],
            self::FORMAT_XML => [
                'class' => XmlResponseFormatter::className()
            ],
            self::FORMAT_JSON => [
                'class' => JsonResponseFormatter::className()
            ],
            self::FORMAT_JSONP => [
                'class' => JsonResponseFormatter::className(),
                'useJsonp' => true
            ]
        ];
    }

    /**
     * Возвращает значение заголовка Disposition.
     * @param string $disposition Расположение.
     * @param string $attachmentName Наименование вложения.
     * @return string
     */
    protected function getDispositionHeaderValue($disposition, $attachmentName)
    {
        $fallbackName = str_replace(
            ['%', '/', '\\', '"', "\x7F"],
            ['_', '_', '_', '\\"', '_'],
            Inflector::transliterate($attachmentName, Inflector::TRANSLITERATE_LOOSE)
        );

        $utfName = rawurlencode(str_replace(['%', '/', '\\'], '', $attachmentName));
        $dispositionHeader = "{$disposition}; filename=\"{$fallbackName}\"";

        if ($utfName !== $fallbackName)
            $dispositionHeader .= "; filename*=utf-8''{$utfName}";

        return $dispositionHeader;
    }

    /**
     * Возвращает диапазон вложения.
     * @param $fileSize
     * @return array|bool
     */
    protected function getHttpRange($fileSize)
    {
        $rangeHeader = Core::$app->getRequest()->getHeaders()->get('Range', '-');

        if ($rangeHeader === '-')
            return [0, $fileSize - 1];

        if (!preg_match('/^bytes=(\d*)-(\d*)$/', $rangeHeader, $matches))
            return false;

        if ($matches[1] === '') {
            $start = $fileSize - $matches[2];
            $end = $fileSize - 1;
        } elseif ($matches[2] !== '') {
            $start = $matches[1];
            $end = $matches[2];

            if ($end >= $fileSize)
                $end = $fileSize - 1;
        } else {
            $start = $matches[1];
            $end = $fileSize - 1;
        }

        if ($start < 0 || $start > $end)
            return false;

        return [$start, $end];
    }

    /**
     * Возвращает значение, указывающее на возможность поиска.
     * @param $handle
     * @return bool
     */
    protected function isSeekable($handle)
    {
        if (!is_resource($handle))
            return true;

        $metaData = stream_get_meta_data($handle);

        return isset($metaData['seekable']) && $metaData['seekable'] === true;
    }

    /**
     * Выполняет подготовку ответа (форматирование).
     * @throws InvalidConfigException
     */
    protected function prepare()
    {
        if (in_array($this->getStatusCode(), [204, 304])) {
            // A 204/304 response cannot contain a message body according to rfc7231/rfc7232
            $this->content = '';
            $this->stream = null;

            return;
        }

        if ($this->stream !== null)
            return;

        if (isset($this->formatters[$this->format])) {
            $formatter = $this->formatters[$this->format];

            if (!is_object($formatter))
                $this->formatters[$this->format] = $formatter = Core::createObject($formatter);

            if ($formatter instanceof ResponseFormatterInterface) {
                $formatter->format($this);
            } else {
                throw new InvalidConfigException("The '{$this->format}' response formatter is invalid. It must implement the ResponseFormatterInterface.");
            }
        } elseif ($this->format === self::FORMAT_RAW) {
            if ($this->data !== null) {
                $this->content = $this->data;
            }
        } else {
            throw new InvalidConfigException("Unsupported response format: {$this->format}");
        }

        if (is_array($this->content)) {
            throw new InvalidArgumentException('Response content must not be an array.');
        } elseif (is_object($this->content)) {
            if (method_exists($this->content, '__toString')) {
                $this->content = $this->content->__toString();
            } else {
                throw new InvalidArgumentException('Response content must be a string or an object implementing __toString().');
            }
        }
    }

    /**
     * Отправляет заголовки.
     * @throws HeadersAlreadySentException
     */
    protected function sendHeaders()
    {
        if (headers_sent($file, $line))
            throw new HeadersAlreadySentException($file, $line);

        if ($this->_headers) {
            foreach ($this->getHeaders() as $name => $values) {
                $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
                // set replace for first occurrence of header but false afterwards to allow multiple
                $replace = true;
                foreach ($values as $value) {
                    header("$name: $value", $replace);
                    $replace = false;
                }
            }
        }

        $statusCode = $this->getStatusCode();

        header("HTTP/{$this->version} {$statusCode} {$this->statusText}");

        $this->sendCookies();
    }

    /**
     * Отправляет куки.
     */
    protected function sendCookies()
    {
        if ($this->_cookies === null)
            return;

        foreach ($this->_cookies as $cookie) {
            $value = $cookie->value;

            if (PHP_VERSION_ID >= 70300) {
                setcookie($cookie->name, $value, [
                    'expires' => $cookie->expire,
                    'path' => $cookie->path,
                    'domain' => $cookie->domain,
                    'secure' => $cookie->secure,
                    'httpOnly' => $cookie->httpOnly,
                    'sameSite' => !empty($cookie->sameSite) ? $cookie->sameSite : null,
                ]);
            } else {
                $cookiePath = $cookie->path;

                if (!is_null($cookie->sameSite))
                    $cookiePath .= '; samesite=' . $cookie->sameSite;

                setcookie($cookie->name, $value, $cookie->expire, $cookiePath, $cookie->domain, $cookie->secure, $cookie->httpOnly);
            }
        }
    }

    /**
     * Отправляет контент.
     */
    protected function sendContent()
    {
        if ($this->stream === null) {
            echo $this->content;

            return;
        }

        // Try to reset time limit for big files
        @set_time_limit(0);

        if (is_callable($this->stream)) {
            $data = call_user_func($this->stream);

            foreach ($data as $datum) {
                echo $datum;
                flush();
            }

            return;
        }

        $chunkSize = 8 * 1024 * 1024; // 8MB per chunk

        if (is_array($this->stream)) {
            list($handle, $begin, $end) = $this->stream;

            // only seek if stream is seekable
            if ($this->isSeekable($handle))
                fseek($handle, $begin);

            while (!feof($handle) && ($pos = ftell($handle)) <= $end) {
                if ($pos + $chunkSize > $end)
                    $chunkSize = $end - $pos + 1;

                echo fread($handle, $chunkSize);

                flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
            }

            fclose($handle);
        } else {
            while (!feof($this->stream)) {
                echo fread($this->stream, $chunkSize);

                flush();
            }

            fclose($this->stream);
        }
    }

    /**
     * Статусы ответа.
     * @var array
     */
    public static $httpStatuses = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        118 => 'Connection timed out',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        210 => 'Content Different',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        310 => 'Too many Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range unsatisfiable',
        417 => 'Expectation failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable entity',
        423 => 'Locked',
        424 => 'Method failure',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway or Proxy Error',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        507 => 'Insufficient storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->version === null) {
            if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0') {
                $this->version = '1.0';
            } else {
                $this->version = '1.1';
            }
        }

        if ($this->charset === null)
            $this->charset = Encoding::getDefault();

        $this->formatters = array_merge($this->defaultFormatters(), $this->formatters);
    }

    /**
     * Очищает ответ и приводит его к первоначальному виду.
     */
    public function clear()
    {
        $this->_headers = null;
        $this->_cookies = null;
        $this->_statusCode = 200;
        $this->statusText = 'OK';
        $this->data = null;
        $this->stream = null;
        $this->content = null;
        $this->isSent = false;
    }

    /**
     * Возвращает коллекцию кук.
     * @return CookieCollection
     */
    public function getCookies()
    {
        if ($this->_cookies === null)
            $this->_cookies = new CookieCollection();

        return $this->_cookies;
    }

    /**
     * Возвращает коллекцию заголовков.
     * @return HeaderCollection
     */
    public function getHeaders()
    {
        if ($this->_headers === null)
            $this->_headers = new HeaderCollection();

        return $this->_headers;
    }

    /**
     * Возвращает значение, указывающее, является ли ответ клиентской ошибкой.
     * @return boolean
     */
    public function getIsClientError()
    {
        return $this->getStatusCode() >= 400 && $this->getStatusCode() < 500;
    }

    /**
     * Возвращает значение, указывающее, является ли ответ пустым.
     * @return boolean
     */
    public function getIsEmpty()
    {
        return in_array($this->getStatusCode(), [201, 204, 304]);
    }

    /**
     * Возвращает значение, указывающее, является ли ответ запрещенным.
     * @return boolean
     */
    public function getIsForbidden()
    {
        return $this->getStatusCode() == 403;
    }

    /**
     * Возвращает значение, указывающее, является ли ответ информационным.
     * @return boolean
     */
    public function getIsInformational()
    {
        return $this->getStatusCode() >= 100 && $this->getStatusCode() < 200;
    }

    /**
     * Возвращает значение, указывающее, является ли ответ некорректным.
     * @return boolean
     */
    public function getIsInvalid()
    {
        return $this->getStatusCode() < 100 || $this->getStatusCode() >= 600;
    }

    /**
     * Возвращает значение, указывающее, является ли ответ типа "Не найдено".
     * @return boolean
     */
    public function getIsNotFound()
    {
        return $this->getStatusCode() == 404;
    }

    /**
     * Возвращает значение, указывающее, является ли ответ нормальным.
     * @return boolean
     */
    public function getIsOk()
    {
        return $this->getStatusCode() == 200;
    }

    /**
     * Возвращает значение, указывающее, является ли ответ перенаправлением.
     * @return boolean
     */
    public function getIsRedirection()
    {
        return $this->getStatusCode() >= 300 && $this->getStatusCode() < 400;
    }

    /**
     * Возвращает значение, указывающее, является ли ответ серверной ошибкой.
     * @return boolean
     */
    public function getIsServerError()
    {
        return $this->getStatusCode() >= 500 && $this->getStatusCode() < 600;
    }

    /**
     * Возвращает значение, указывающее, является ли ответ успешным.
     * @return boolean
     */
    public function getIsSuccessful()
    {
        return $this->getStatusCode() >= 200 && $this->getStatusCode() < 300;
    }

    /**
     * Возвращает код статуса.
     * @return boolean
     */
    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    /**
     * Перезагружает страницу.
     * @param string|null $anchor Якорь.
     * @return Response
     * @throws InvalidConfigException
     */
    public function refresh($anchor = null)
    {
        return $this->redirect(Core::$app->getRequest()->getUrl() . $anchor);
    }

    /**
     * Генерирует перенаправленеие.
     * @param string $url Адрес.
     * @param integer $statusCode Статус.
     * @param boolean $checkAjax Проверять AJAX.
     * @return $this
     */
    public function redirect($url, $statusCode = 302, $checkAjax = true)
    {
        $request = Core::$app->getRequest();

        if (strncmp($url, '/', 1) === 0 && strncmp($url, '//', 2) !== 0)
            $url = $request->getHostInfo() . $url;

        if ($checkAjax) {
            if ($request->getIsAjax()) {
                if (in_array($statusCode, [301, 302]) && preg_match('/Trident\/|MSIE[ ]/', $request->getUserAgent()))
                    $statusCode = 200;

                if ($request->getIsPjax()) {
                    $this->getHeaders()->set('X-Pjax-Url', $url);
                } else {
                    $this->getHeaders()->set('X-Redirect', $url);
                }
            } else {
                $this->getHeaders()->set('Location', $url);
            }
        } else {
            $this->getHeaders()->set('Location', $url);
        }

        $this->setStatusCode($statusCode);

        return $this;
    }

    /**
     * Отправляет ответ.
     * @throws HeadersAlreadySentException
     * @throws InvalidConfigException
     */
    public function send()
    {
        if ($this->isSent)
            return;

        $this->trigger(self::EVENT_BEFORE_SEND);
        $this->prepare();
        $this->trigger(self::EVENT_AFTER_PREPARE);
        $this->sendHeaders();
        $this->sendContent();
        $this->trigger(self::EVENT_AFTER_SEND);
        $this->isSent = true;
    }

    /**
     * Отправляет контент как файл.
     * @param string $content Контент.
     * @param string $attachmentName Наименование вложения.
     * @param array $options Опции.
     * @return $this
     * @throws RangeNotSatisfiableHttpException
     */
    public function sendContentAsFile($content, $attachmentName, $options = [])
    {
        $headers = $this->getHeaders();

        $contentLength = StringHelper::byteLength($content);
        $range = $this->getHttpRange($contentLength);

        if ($range === false) {
            $headers->set('Content-Range', "bytes */$contentLength");
            throw new RangeNotSatisfiableHttpException();
        }

        list($begin, $end) = $range;
        if ($begin != 0 || $end != $contentLength - 1) {
            $this->setStatusCode(206);
            $headers->set('Content-Range', "bytes $begin-$end/$contentLength");
            $this->content = StringHelper::byteSubstr($content, $begin, $end - $begin + 1);
        } else {
            $this->setStatusCode(200);
            $this->content = $content;
        }

        $mimeType = isset($options['mimeType']) ? $options['mimeType'] : 'application/octet-stream';
        $this->setDownloadHeaders($attachmentName, $mimeType, !empty($options['inline']), $end - $begin + 1);

        $this->format = self::FORMAT_RAW;

        return $this;
    }

    /**
     * Отправляет файл.
     * @param string $filePath Путь до файла.
     * @param string|null $attachmentName Наименование вложения.
     * @param array $options Опции.
     * @return $this
     * @throws RangeNotSatisfiableHttpException
     */
    public function sendFile($filePath, $attachmentName = null, $options = [])
    {
        if (!isset($options['mimeType']))
            $options['mimeType'] = FileHelper::getMimeTypeByExtension($filePath);

        if ($attachmentName === null)
            $attachmentName = basename($filePath);

        $handle = fopen($filePath, 'rb');
        $this->sendStreamAsFile($handle, $attachmentName, $options);

        return $this;
    }

    /**
     * Отправляет поток как файл.
     * @param resource $handle Ресурс.
     * @param string $attachmentName Наименование вложения.
     * @param array $options Опции.
     * @return $this
     * @throws RangeNotSatisfiableHttpException
     */
    public function sendStreamAsFile($handle, $attachmentName, $options = [])
    {
        $headers = $this->getHeaders();
        if (isset($options['fileSize'])) {
            $fileSize = $options['fileSize'];
        } else {
            if ($this->isSeekable($handle)) {
                fseek($handle, 0, SEEK_END);
                $fileSize = ftell($handle);
            } else {
                $fileSize = 0;
            }
        }

        $range = $this->getHttpRange($fileSize);
        if ($range === false) {
            $headers->set('Content-Range', "bytes */$fileSize");
            throw new RangeNotSatisfiableHttpException();
        }

        list($begin, $end) = $range;
        if ($begin != 0 || $end != $fileSize - 1) {
            $this->setStatusCode(206);
            $headers->set('Content-Range', "bytes $begin-$end/$fileSize");
        } else {
            $this->setStatusCode(200);
        }

        $mimeType = isset($options['mimeType']) ? $options['mimeType'] : 'application/octet-stream';
        $this->setDownloadHeaders($attachmentName, $mimeType, !empty($options['inline']), $end - $begin + 1);

        $this->format = self::FORMAT_RAW;
        $this->stream = [$handle, $begin, $end];

        return $this;
    }

    /**
     * Устанавливает загрузки для скачивания файла.
     * @param string $attachmentName Наименование вложения.
     * @param string|null $mimeType MIME-тип.
     * @param boolean $inline Внутренний.
     * @param integer|null $contentLength Длина контента.
     * @return $this
     */
    public function setDownloadHeaders($attachmentName, $mimeType = null, $inline = false, $contentLength = null)
    {
        $headers = $this->getHeaders();

        $disposition = $inline ? 'inline' : 'attachment';
        $headers->setDefault('Pragma', 'public')
            ->setDefault('Accept-Ranges', 'bytes')
            ->setDefault('Expires', '0')
            ->setDefault('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->setDefault('Content-Disposition', $this->getDispositionHeaderValue($disposition, $attachmentName));

        if ($mimeType !== null) {
            $headers->setDefault('Content-Type', $mimeType);
        }

        if ($contentLength !== null) {
            $headers->setDefault('Content-Length', $contentLength);
        }

        return $this;
    }

    /**
     * Устанавливает код статуса.
     * @param integer $value Значение.
     * @param string|null $text Сообщение.
     * @return $this
     */
    public function setStatusCode($value, $text = null)
    {
        if ($value === null)
            $value = 200;

        $this->_statusCode = (int) $value;

        if ($this->getIsInvalid())
            throw new InvalidArgumentException("The HTTP status code is invalid: $value");

        if ($text === null) {
            $this->statusText = isset(static::$httpStatuses[$this->_statusCode]) ? static::$httpStatuses[$this->_statusCode] : '';
        } else {
            $this->statusText = $text;
        }

        return $this;
    }

    /**
     * Устанравливает код ответа по исключению.
     * @param \Exception $e Исключение.
     * @return $this
     */
    public function setStatusCodeByException($e)
    {
        if ($e instanceof HttpException) {
            $this->setStatusCode($e->statusCode);
        } else {
            $this->setStatusCode(500);
        }

        return $this;
    }

    /**
     * Отправляет файл методом X-Sendfile.
     * @param string $filePath Путь до файла.
     * @param string|null $attachmentName Наименование вложения.
     * @param array $options Опции.
     * @return $this
     */
    public function xSendFile($filePath, $attachmentName = null, $options = [])
    {
        if ($attachmentName === null)
            $attachmentName = basename($filePath);

        if (isset($options['mimeType'])) {
            $mimeType = $options['mimeType'];
        } elseif (($mimeType = FileHelper::getMimeTypeByExtension($filePath)) === null) {
            $mimeType = 'application/octet-stream';
        }
        if (isset($options['xHeader'])) {
            $xHeader = $options['xHeader'];
        } else {
            $xHeader = 'X-Sendfile';
        }

        $disposition = empty($options['inline']) ? 'attachment' : 'inline';
        $this->getHeaders()
            ->setDefault($xHeader, $filePath)
            ->setDefault('Content-Type', $mimeType)
            ->setDefault('Content-Disposition', $this->getDispositionHeaderValue($disposition, $attachmentName));

        $this->format = self::FORMAT_RAW;

        return $this;
    }
}