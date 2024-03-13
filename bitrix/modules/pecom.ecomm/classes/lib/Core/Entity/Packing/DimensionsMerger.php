<?php
namespace Pecom\Delivery\Core\Entity\Packing;

/**
 * Interface DimensionsMerger
 * @package Pecom\Delivery\Core
 * @subpackage Packing
 */
interface DimensionsMerger
{
    public static function getSumDimensions($arGabs);
}