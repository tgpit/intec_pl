<?php
namespace intec\seo\text\generator\tokens;

use intec\core\base\BaseObject;
use intec\seo\text\generator\Token;
use intec\seo\text\generator\Tokens;

/**
 * Класс, представляющий элемент группы.
 * Class GroupItem
 * @package intec\seo\text\generator\tokens
 * @author apocalypsisdimon@gmail.com
 */
class GroupItem extends BaseObject
{
    /**
     * @var Tokens
     */
    protected $_tokens;

    /**
     * @inheritdoc
     */
    public function __construct(array $config = [])
    {
        $this->_tokens = new Tokens();

        parent::__construct($config);
    }

    /**
     * Возвращает коллекцию токенов.
     * @return Tokens
     */
    public function getTokens()
    {
        return $this->_tokens;
    }

    /**
     * Устанавливает новые токены коллекции.
     * @param Tokens|Token[]|array $value
     * @return $this
     */
    public function setTokens($value)
    {
        $this->_tokens->removeAll();
        $this->_tokens->setRange($value);

        return $this;
    }
}