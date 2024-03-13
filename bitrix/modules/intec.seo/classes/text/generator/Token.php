<?php
namespace intec\seo\text\generator;

use intec\core\base\BaseObject;

/**
 * Класс, представляющий синтаксический элемент.
 * Class Token
 * @package intec\seo\text\generator
 * @author apocalypsisdimon@gmail.com
 */
abstract class Token extends BaseObject
{
    /**
     * Преобразует себя в конечное текстовое представление.
     * @param array $macros Макросы.
     * @return string
     */
    public abstract function transform($macros = []);
}