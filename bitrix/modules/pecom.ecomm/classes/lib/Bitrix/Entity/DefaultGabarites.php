<?php
namespace Pecom\Delivery\Bitrix\Entity;

/**
 * Class DefaultGabarites
 * @package Pecom\Delivery\Bitrix
 * @subpackage Entity
 */
class DefaultGabarites
{
    /**
     * Defines type of object to which default gabs are set
     */
    const MODE_GOOD  = 'G';
    const MODE_CARGO = 'C';

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var int
     */
    protected $weight;

    /**
     * @var int
     */
    protected $length;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    public function __construct()
    {
        // $this->mode   = \COption::GetOptionString(PECOM_ECOMM, 'defMode', self::MODE_CARGO);
        $this->mode   = self::MODE_GOOD;
        $this->weight = (int)(\COption::GetOptionString(PECOM_ECOMM, 'PEC_WEIGHT', 0.05) * 1000); // From kg to g
        $this->length = (int)\COption::GetOptionString(PECOM_ECOMM, 'PEC_LENGTH_D', 200);
        $this->width  = (int)\COption::GetOptionString(PECOM_ECOMM, 'PEC_WIDTH_D', 200);
        $this->height = (int)\COption::GetOptionString(PECOM_ECOMM, 'PEC_HEIGHT_D', 200);
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }
}