<?php
namespace intec\core\db;

use intec\core\base\BaseObject;
use intec\core\base\InvalidParamException;

/**
 * Обработчик запроса.
 * Class QueryHandler
 * @property Query $query Запрос. Только для чтения.
 * @package intec\core\db
 * @author apocalypsisdimon@gmail.com
 */
abstract class QueryHandler extends BaseObject
{
    /**
     * Запрос.
     * @var Query
     */
    protected $_query;

    /**
     * PartialQuery constructor.
     * @param $query
     * @param array $config
     */
    public function __construct($query, array $config = [])
    {
        if (!($query instanceof Query))
            throw new InvalidParamException('Invalid query object');

        $this->_query = $query;

        parent::__construct($config);
    }

    /**
     * Возвращает запрос.
     * @return Query
     */
    public function getQuery()
    {
        return $this->_query;
    }
}
