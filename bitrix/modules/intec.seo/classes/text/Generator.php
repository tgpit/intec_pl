<?php
namespace intec\seo\text;

use intec\core\base\BaseObject;
use intec\core\helpers\Type;
use intec\seo\text\generator\Parser;
use intec\seo\text\generator\ParserException;

/**
 * Класс генератора текста.
 * Class Generator
 * @package intec\seo\text
 * @author apocalypsisdimon@gmail.com
 */
class Generator extends BaseObject implements GeneratorInterface
{
    /**
     * @inheritdoc
     * @param array $macros
     * @throws ParserException
     */
    public function process($text, $macros = [])
    {
        if (!Type::isArray($macros))
            $macros = [];

        return Parser::process($text)->transform($macros);
    }
}