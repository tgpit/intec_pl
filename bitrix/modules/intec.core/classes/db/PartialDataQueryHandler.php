<?php
namespace intec\core\db;

/**
 * Обработчик, позволяющий обработать запрос частями.
 * Class PartialDataQueryHandler
 * @property boolean $isFetched Получен полностью. Только для чтения.
 * @property integer $limit Лимит на 1 итерацию запроса данных.
 * @package intec\core\db
 * @author apocalypsisdimon@gmail.com
 */
class PartialDataQueryHandler extends QueryHandler
{
    /**
     * Данные.
     * @var array
     */
    protected $_data = [];
    /**
     * Получен полностью.
     * @var boolean
     */
    protected $_fetched = false;
    /**
     * Ограничение на разовую выборку.
     * @var integer
     */
    protected $_limit = 100;
    /**
     * Смещение.
     * @var integer
     */
    protected $_offset;
    /**
     * Позиция.
     * @var integer
     */
    protected $_position;

    /**
     * Загружает новые данные.
     * @return boolean
     */
    protected function fetchData()
    {
        if ($this->_fetched)
            return false;

        $this->_data = [];
        $this->_position = 0;

        $count = 0;
        $query = $this->_query;
        $queryLimit = $query->limit;
        $queryOffset = $query->offset;

        if ($this->_offset === null)
            $this->_offset = $queryOffset;

        if ($this->_offset === null)
            $this->_offset = 0;

        $query->limit($this->_limit);
        $query->offset($this->_offset);

        $result = $query->all();

        $query->limit($queryLimit);
        $query->offset($queryOffset);

        foreach ($result as $item) {
            $this->_data[] = $item;
            $count++;
        }

        $this->_offset += $count;

        if ($count < $this->_limit)
            $this->_fetched = true;

        return $count !== 0;
    }

    /**
     * Получает следующую запись.
     * @return mixed
     */
    public function fetch()
    {
        if ($this->getIsFetched()) {
            if ($this->_position !== 0) {
                $this->_position = 0;
                $this->_data = [];
            }

            return false;
        }

        if ($this->_position === null || !isset($this->_data[$this->_position]))
            if (!$this->fetchData())
                return false;

        $result = $this->_data[$this->_position];
        $this->_position++;

        return $result;
    }

    /**
     * Возвращает значение, указывающее на полное получение данных.
     * @return boolean
     */
    public function getIsFetched()
    {
        return $this->_fetched && !isset($this->_data[$this->_position]);
    }

    /**
     * Возвращает лимит на 1 итерацию запроса данных.
     * @return integer
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * Сбрасывает состояние на начало.
     * @return static
     */
    public function reset()
    {
        $this->_data = [];
        $this->_fetched = false;
        $this->_offset = null;
        $this->_position = null;

        return $this;
    }

    /**
     * Устанавливает лимит на 1 интерацию запроса данных.
     * @param integer $value
     * @return static
     */
    public function setLimit($value)
    {
        $this->_limit = $value;

        return $this;
    }
}
