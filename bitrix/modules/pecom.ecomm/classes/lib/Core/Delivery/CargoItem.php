<?php
namespace Pecom\Delivery\Core\Delivery;

use Pecom\Delivery\Core\Entity\FieldsContainer;
use Pecom\Delivery\Core\Entity\Money;

/**
 * Class CargoItem
 * @package Pecom\Delivery\Core
 * @subpackage Delivery
 * Description of basic product (ware, goods) (length, width, height, quantity)
 * l,w,h - mm
 * w - g
 */
class CargoItem
{
    use FieldsContainer;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var string|null
     */
    protected $articul;

    /**
     * @var string|null
     */
    protected $barcode;

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
     * @var int
     */
    protected $quantity;

    /**
     * @var Money price to be payed for item
     */
    protected $price;

    /**
     * @var Money estimated cost for insurance
     */
    protected $cost;

    /**
     * @var int
     */
    protected $vatRate;

    /**
     * @var Money|null
     */
    protected $vatSum;

    /**
     * @var bool
     */
    protected $overSize = false;

    /**
     * @param int $length
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function setGabs($length, $width, $height)
    {
        $this->setLength($length);
        $this->setWidth($width);
        $this->setHeight($height);

        return $this;
    }

    /**
     * Makes CargoItem from associative array
     * @param array $data
     * @return $this
     */
    public function fromArray($data)
    {
        $this
            ->setId($data['id'])
            ->setName($data['name'])
            ->setDescription(isset($data['description']) ? $data['description'] : null)
            ->setArticul(isset($data['articul']) ? $data['articul'] : null)
            ->setBarcode(isset($data['barcode']) ? $data['barcode'] : null)
            ->setLength((int)$data['length'])
            ->setWidth((int)$data['width'])
            ->setHeight((int)$data['height'])
            ->setWeight((int)$data['weight'])
            ->setQuantity((int)$data['quantity'])
            ->setPrice(new Money((float)$data['price']['amount'], $data['price']['currency']))
            ->setCost(new Money((float)$data['cost']['amount'], $data['cost']['currency']))
            ->setVatRate($data['vatRate'])
            ->setVatSum((isset($data['vatSum']['amount']) && isset($data['vatSum']['currency'])) ? new Money((float)$data['vatSum']['amount'], $data['vatSum']['currency']) : null)
            ->setOverSize(isset($data['overSize']) && ($data['overSize'] === true || $data['overSize'] === 'true'));

        if (!empty($data['fields']) && is_array($data['fields'])) {
            $this->setFields($data['fields']);
        }

        return $this;
    }

    /**
     * Returns CargoItem data as associative array
     * @return array
     */
    public function toArray()
    {
        $data = [
            'id'          => $this->getId(),
            'name'        => $this->getName(),
            'description' => $this->getDescription(),
            'articul'     => $this->getArticul(),
            'barcode'     => $this->getBarcode(),
            'length'      => $this->getLength(),
            'width'       => $this->getWidth(),
            'height'      => $this->getHeight(),
            'weight'      => $this->getWeight(),
            'quantity'    => $this->getQuantity(),
            'price'       => is_null($this->getPrice()) ? ['amount' => null, 'currency' => null] :
                ['amount' => $this->getPrice()->getAmount(), 'currency' => $this->getPrice()->getCurrency()],
            'cost'        => is_null($this->getCost()) ? ['amount' => null, 'currency' => null] :
                ['amount' => $this->getCost()->getAmount(), 'currency' => $this->getCost()->getCurrency()],
            'vatRate'     => $this->getVatRate(),
            'vatSum'      => is_null($this->getVatSum()) ? ['amount' => null, 'currency' => null] :
                ['amount' => $this->getVatSum()->getAmount(), 'currency' => $this->getVatSum()->getCurrency()],
            'overSize'    => $this->getOverSize(),
            'fields'      => null,
        ];

        if (!empty($this->getContainer())) {
            foreach($this->getContainer() as $key => $val) {
                $data['fields'][$key] = $val;
            }
        }

        return $data;
    }

    // Only getters and setters below this line

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getArticul()
    {
        return $this->articul;
    }

    /**
     * @param string|null $articul
     * @return $this
     */
    public function setArticul($articul)
    {
        $this->articul = $articul;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param string|null $barcode
     * @return $this
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;

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

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return Money price to be payed for item
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param Money $price price to be payed for item
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Money estimated cost for insurance
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param Money $cost estimated cost for insurance
     * @return $this
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * @return int
     */
    public function getVatRate()
    {
        return $this->vatRate;
    }

    /**
     * @param int $vatRate
     * @return $this
     */
    public function setVatRate($vatRate)
    {
        $this->vatRate = $vatRate;

        return $this;
    }

    /**
     * @return Money|null
     */
    public function getVatSum()
    {
        return $this->vatSum;
    }

    /**
     * @param Money|null $vatSum
     * @return $this
     */
    public function setVatSum($vatSum)
    {
        $this->vatSum = $vatSum;

        return $this;
    }

    /**
     * @return bool
     */
    public function getOverSize()
    {
        return $this->overSize;
    }

    /**
     * @param bool $overSize
     * @return $this
     */
    public function setOverSize($overSize)
    {
        $this->overSize = $overSize;

        return $this;
    }
}