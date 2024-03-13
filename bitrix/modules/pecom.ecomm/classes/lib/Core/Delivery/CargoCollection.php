<?php
namespace Pecom\Delivery\Core\Delivery;

use Pecom\Delivery\Core\Entity\Collection;
use Pecom\Delivery\Core\Entity\FieldsContainer;
use Pecom\Delivery\Core\Entity\Money;
use Pecom\Delivery\Core\Entity\Packing\MebiysDimMerger;
use Pecom\Delivery\Core\Entity\Packing\MergerResult;

/**
 * Class CargoCollection
 * @package Pecom\Delivery\Core
 * @subpackage Delivery
 * @method false|Cargo getFirst
 * @method false|Cargo getNext
 * @method false|Cargo getLast
 */
class CargoCollection extends Collection
{
    use FieldsContainer;

    /**
     * @var array
     */
    protected $Cargoes;

    /**
     * @var MebiysDimMerger|mixed
     */
    protected $packer;

    /**
     * CargoCollection constructor.
     * @param string $packer - full class name
     */
    public function __construct($packer = false)
    {
        parent::__construct('Cargoes');
        $this->packer = $packer ? new $packer : new MebiysDimMerger();
    }

    /**
     * Returns total price to be payed for items in all cargoes
     * @return Money
     */
    public function getTotalPrice()
    {
        $price = new Money(0);

        $this->reset();
        while ($cargo = $this->getNext()) {
            $price = Money::sum(array($price, $cargo->getTotalPrice()));
        }

        return $price;
    }

    /**
     * Returns total estimated cost for insurance of all cargoes
     * @return Money
     */
    public function getTotalCost()
    {
        $cost = new Money(0);

        $this->reset();
        while ($cargo = $this->getNext()) {
            $cost = Money::sum(array($cost, $cargo->getTotalCost()));
        }

        return $cost;
    }

    /**
     * @return MergerResult
     */
    public function getTotalDimensions()
    {
        $arGabs = array();

        $this->reset();
        while ($cargo = $this->getNext()) {
            $arGabs[] = array($cargo->getLength(), $cargo->getWidth(), $cargo->getHeight(), 1);
        }

        $packer = $this->packer;
        return $packer::getSumDimensions($arGabs);
    }

    /**
     * @param bool $setCalculatedToo Do set calculated result in Cargo objects or not
     * @return MergerResult
     */
    public function calculateTotalDimensions($setCalculatedToo = false)
    {
        $arGabs = array();

        $this->reset();
        while ($cargo = $this->getNext()) {
            $calculateResult = $cargo->calculateDimensions($setCalculatedToo);
            $arGabs[] = array($calculateResult->getLength(), $calculateResult->getWidth(), $calculateResult->getHeight(), 1);
        }

        $packer = $this->packer;
        return $packer::getSumDimensions($arGabs);
    }

    /**
     * @return int
     */
    public function getTotalWeight()
    {
        $weight = 0;

        $this->reset();
        while ($cargo = $this->getNext()) {
            $weight += $cargo->getWeight();
        }

        return $weight;
    }

    /**
     * @param bool $setCalculatedToo Do set calculated result in Cargo objects or not
     * @return int
     */
    public function calculateTotalWeight($setCalculatedToo = false)
    {
        $weight = 0;

        $this->reset();
        while ($cargo = $this->getNext()) {
            $weight += $cargo->calculateWeight($setCalculatedToo);
        }

        return $weight;
    }

    /**
     * Makes CargoCollection from associative array
     * @param array $data
     * @return $this
     */
    public function fromArray($data)
    {
        if (!empty($data['cargoes']) && is_array($data['cargoes'])) {
            foreach ($data['cargoes'] as $cargo) {
                $coreCargo = new Cargo();
                $this->add($coreCargo->fromArray($cargo));
            }
        }

        if (!empty($data['fields']) && is_array($data['fields'])) {
            $this->setFields($data['fields']);
        }

        return $this;
    }

    /**
     * Returns CargoCollection data as associative array
     * @return array
     */
    public function toArray()
    {
        $data = [
            'cargoes' => [],
            'fields'  => null,
        ];

        $this->reset();
        while ($cargo = $this->getNext()) {
            $data['cargoes'][] = $cargo->toArray();
        }

        if (!empty($this->getContainer())) {
            foreach($this->getContainer() as $key => $val) {
                $data['fields'][$key] = $val;
            }
        }

        return $data;
    }
}