<?php
namespace Ipolh\SDEK\Api\Logger;

use DateTime;
use InvalidArgumentException;
use Ipolh\SDEK\Api\Logger\Psr\Log\AbstractLogger;
use Ipolh\SDEK\Api\Logger\Psr\Log\LoggerInterface;

/**
 * Class Logger
 * @package Ipolh\SDEK\Api
 * @subpackage Logger
 */
class Logger extends AbstractLogger implements LoggerInterface
{
    /**
     * @var Route[]
     */
    protected $routes;
    /**
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s.u';

    /**
     * Logger constructor.
     * @param array $routes
     * @throws InvalidArgumentException
     */
    public function __construct(array $routes = [])
    {
        $this->routes = [];
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
    }

    /**
     * @param $route
     * @throws InvalidArgumentException
     */
    public function addRoute($route)
    {
        if (is_a($route, Route::class)) {
            $this->routes[] = $route;
        } else {
            throw new InvalidArgumentException(sprintf('Route for %1s::%2s should be inherited from %3s. %4s provided.',
                __CLASS__, __FUNCTION__, Route::class, get_class($route)));
        }
    }

    /**
     * @param $level
     * @param $message
     * @param array $context
     */
    public function log($level, $message, array $context = [])
    {
        $dataString =
            trim(strtr(self::getMsgTemplate(), [
                '{date}' => $this->getDate(),
                '{level}' => $level,
                '{message}' => $message,
                '{context}' => ($this->contextStringify($context)) ?
                    'context:' . PHP_EOL . $this->contextStringify($context) . PHP_EOL :
                    '',
            ]));

        foreach ($this->routes as $route) {
            if (!$route instanceof Route) {
                continue;
            }
            if (!$route->isEnabled()) {
                continue;
            }

            $route->log($dataString);
        }
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return (new DateTime())->format($this->dateFormat);
    }

    /**
     * @param array $context
     * @return false|string|null
     */
    public function contextStringify(array $context = [])
    {
        return !empty($context) ? json_encode($context) : null;
    }

    /**
     * @return string
     */
    public static function getMsgTemplate()
    {
        return '{date}' . PHP_EOL .
            '{level}' . PHP_EOL .
            '{message}' . PHP_EOL .
            '{context}';
    }

    /**
     * @param $message
     * @param array $context
     * @return string
     */
    protected function interpolate($message, array $context = array())
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (is_array($val)) {
                $val = print_r($val, true);
            }
            if (!is_object($val) || method_exists($val, '__toString')) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }
}