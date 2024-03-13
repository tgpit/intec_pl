<?php
namespace intec\seo\text\generator\tokens;

use intec\seo\text\generator\Token;

/**
 * Класс, представляющий текст.
 * Class Text
 * @package intec\seo\text\generator\tokens
 * @author apocalypsisdimon@gmail.com
 */
class Text extends Token
{
    /**
     * Содержимое.
     * @var string
     */
    public $content;

    /**
     * @inheritdoc
     */
    public function transform($macros = [])
    {
        return $this->content;
    }
}