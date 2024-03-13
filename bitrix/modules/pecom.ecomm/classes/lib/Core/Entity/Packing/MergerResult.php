<?php
namespace Pecom\Delivery\Core\Entity\Packing;

/**
 * Class MergerResult
 * @package Pecom\Delivery\Core
 * @subpackage Packing
 */
class MergerResult
{
    /**
     * @var float|int
     */
    protected $length;

    /**
     * @var float|int
     */
    protected $width;

    /**
     * @var float|int
     */
    protected $height;

    public function __construct($length, $width, $height)
    {
        $this->length = $length;
        $this->width  = $width;
        $this->height = $height;
    }

    /**
     * @return float|int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return float|int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return float|int
     */
    public function getHeight()
    {
        return $this->height;
    }
}