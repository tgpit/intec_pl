<?php
namespace intec\seo\text;

/**
 * Интерфейс генератора текста.
 * Interface GeneratorInterface
 * @package intec\seo\text
 * @author apocalypsisdimon@gmail.com
 */
interface GeneratorInterface
{
    /**
     * Генерирует текст из исходного.
     * @param string $text
     * @return string
     */
    public function process($text);
}