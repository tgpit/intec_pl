<?php
namespace Pecom\Delivery\Core\Delivery;

use Pecom\Delivery\Core\Entity\Collection;
use Pecom\Delivery\Core\Entity\FieldsContainer;
use Pecom\Delivery\Core\Entity\Money;
use Pecom\Delivery\Core\Entity\Packing\MebiysDimMerger;
use Pecom\Delivery\Core\Entity\Packing\MergerResult;

/**
 * Class Cargo
 * @package Pecom\Delivery\Core
 * @subpackage Delivery
 * @method false|CargoItem getFirst
 * @method false|CargoItem getNext
 * @method false|CargoItem getLast
 */
class Cargo extends Collection
{
    use FieldsContainer;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var int - mm
     */
    protected $length;

    /**
     * @var int - mm
     */
    protected $width;

    /**
     * @var int - mm
     */
    protected $height;

    /**
     * @var int - gram
     */
    protected $weight;

    /**
     * @var array
     */
    protected $Items;

    /**
     * @var MebiysDimMerger|mixed
     */
    protected $packer;

    /**
     * Cargo constructor.
     * @param $packer
     */
    public function __construct($packer = false)
    {
        parent::__construct('Items');
        $this->packer = $packer ? new $packer : new MebiysDimMerger();
    }

    /**
     * @param bool $setCalculatedToo Do set calculated result in Cargo object or not
     * @return MergerResult
     */
    public function calculateDimensions($setCalculatedToo = false)
    {
        $arGabs = array();

        $this->reset();
        while ($item = $this->getNext()) {
            $arGabs[] = array($item->getLength(), $item->getWidth(), $item->getHeight(), $item->getQuantity());
        }

        $packer = $this->packer;
        $mergerResult = $packer::getSumDimensions($arGabs);

        if ($setCalculatedToo) {
            $this->length = (int)$mergerResult->getLength();
            $this->width  = (int)$mergerResult->getWidth();
            $this->height = (int)$mergerResult->getHeight();
        }

        return $mergerResult;
    }

    /**
     * @param bool $setCalculatedToo Do set calculated result in Cargo object or not
     * @return int
     */
    public function calculateWeight($setCalculatedToo = false)
    {
        $weight = 0;

        $this->reset();
        while ($item = $this->getNext()) {
            $weight += $item->getWeight() * $item->getQuantity();
        }

        if ($setCalculatedToo) {
            $this->weight = (int)$weight;
        }

        return $weight;
    }

    /**
     * Returns total price to be payed for items
     * @return Money
     */
    public function getTotalPrice()
    {
        $price = new Money(0);

        $this->reset();
        while ($item = $this->getNext()) {
            if ($item->getPrice())
                $price = Money::sum(array($price, Money::multiply($item->getPrice(), $item->getQuantity())));
        }

        return $price;
    }

    /**
     * Returns total estimated cost for insurance
     * @return Money
     */
    public function getTotalCost()
    {
        $cost = new Money(0);

        $this->reset();
        while ($item = $this->getNext()) {
            if ($item->getCost())
                $cost = Money::sum(array($cost, Money::multiply($item->getCost(), $item->getQuantity())));
        }

        return $cost;
    }

    /**
     * @return bool
     */
    public function checkOverSize()
    {
        $this->reset();
        while ($item = $this->getNext()) {
            if ($item->getOverSize())
                return true;
        }

        return false;
    }

    /**
     * Makes Cargo from associative array
     * @param array $data
     * @return $this
     */
    public function fromArray($data)
    {
        $this
            ->setName(isset($data['name']) ? $data['name'] : null)
            ->setLength((int)$data['length'])
            ->setWidth((int)$data['width'])
            ->setHeight((int)$data['height'])
            ->setWeight((int)$data['weight']);

        if (!empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $cargoItem = new CargoItem();
                $this->add($cargoItem->fromArray($item));
            }
        }

        if (!empty($data['fields']) && is_array($data['fields'])) {
            $this->setFields($data['fields']);
        }

        return $this;
    }

    /**
     * Returns Cargo data as associative array
     * @return array
     */
    public function toArray()
    {
        $data = [
            'name'        => $this->getName(),
            'length'      => $this->getLength(),
            'width'       => $this->getWidth(),
            'height'      => $this->getHeight(),
            'weight'      => $this->getWeight(),
            'items'       => [],
            'fields'      => null,
        ];

        $this->reset();
        while ($item = $this->getNext()) {
            $data['items'][] = $item->toArray();
        }

        if (!empty($this->getContainer())) {
            foreach($this->getContainer() as $key => $val) {
                $data['fields'][$key] = $val;
            }
        }

        return $data;
    }

    // Only getters and setters below this line

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param int $length
     * @return $this
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     * @return $this
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }
}