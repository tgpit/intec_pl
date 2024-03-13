<?php
namespace intec\seo\text\generator;

use intec\core\base\Collection;

/**
 * Класс, представляющий коллекцию ситаксических элементов.
 * Class Tokens
 * @package intec\seo\text\generator
 * @author apocalypsisdimon@gmail.com
 */
class Tokens extends Collection
{
    /**
     * @inheritdoc
     */
    protected function verify($item)
    {
        return ($item instanceof Token);
    }

    /**
     * Преобразует токены коллекции в конечное текстовое представление.
     * @param array $macros
     * @return string
     */
    public function transform($macros = [])
    {
        $result = '';

        foreach ($this->items as $item) {
            /** @var Token $item */
            $result .= $item->transform($macros);
        }

        return $result;
    }
}