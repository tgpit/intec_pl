<?php
namespace intec\seo\text\generator\tokens;

use intec\core\helpers\Type;
use intec\seo\text\generator\Token;
use intec\seo\text\generator\Tokens;

/**
 * Класс, представляющий опциональный токен.
 * Class Optional
 * @package intec\seo\text\generator\tokens
 * @author apocalypsisdimon@gmail.com
 */
class Optional extends Token
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

    /**
     * @inheritdoc
     */
    public function transform($macros = [])
    {
        $render = false;
        $result = '';

        /** @var Token $token */
        foreach ($this->_tokens as $token) {
            $value = $token->transform($macros);
            $result .= $value;

            if ($token instanceof Text)
                continue;

            if (!empty($value) || Type::isNumeric($value))
                $render = true;
        }

        if ($render)
            return $result;

        return '';
    }
}