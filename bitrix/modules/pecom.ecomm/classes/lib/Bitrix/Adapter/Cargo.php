<?php
namespace Pecom\Delivery\Bitrix\Adapter;

use Pecom\Delivery\Bitrix\Entity\DefaultGabarites;
use Pecom\Delivery\Bitrix\Handler\GoodsPicker;
use Pecom\Delivery\Core\Delivery\CargoItem;
use Pecom\Delivery\Core\Delivery\Cargo as CoreCargo;
use Pecom\Delivery\Core\Entity\Money;
use Pecom\Delivery\Core\Entity\Packing\MebiysDimMerger;

/**
 * Class Cargo
 * Makes Core Cargo from Bitrix items
 * @package Pecom\Delivery\Bitrix
 * @subpackage Adapter
 */
class Cargo
{
    /**
     * @var array Bitrix items
     */
    protected $items;

    /**
     * @var CoreCargo
     */
    protected $cargo;

    /**
     * @var DefaultGabarites
     */
    protected $defaultGabarites;

    public function __construct(DefaultGabarites $defaultGabarites = null)
    {
        if (!is_null($defaultGabarites))
            $this->defaultGabarites = $defaultGabarites;
    }

    /**
     * Makes Cargo from Bitrix items array given
     * @param array $items
     * @return $this
     */
    public function make($items)
    {
        $this->setItems($items);
        $this->formCargo();
        return $this;
    }

    /**
     * Generates Cargo using items data
     * @throws \Exception
     */
    protected function formCargo()
    {
        $items = $this->getItems();
        if (empty($items)) {
            throw new \Exception('No Bitrix items to create Core Cargo in '.get_class());
        }

        $this->cargo = new CoreCargo();

        // Street magic for Cargo dimensions, weight, volume calculation using items data after applying default dimensions
        $reformedItems = $this->reformItems();
        foreach ($reformedItems as $item) {
            $cargoItem = new CargoItem();
            $cargoItem
                ->setGabs((int)$item['DIMENSIONS']['LENGTH'], (int)$item['DIMENSIONS']['WIDTH'], (int)$item['DIMENSIONS']['HEIGHT'])
                ->setWeight((int)$item['WEIGHT'])
                ->setQuantity(((int)$item['QUANTITY']) ?: 1);

            $this->cargo->add($cargoItem);
        }
        $this->cargo->calculateDimensions(true);
        $this->cargo->calculateWeight(true);

        // Clear items
        $this->cargo->clear();

        $articul        = \COption::GetOptionString(PECOM_ECOMM, 'articul', 'ARTNUMBER');
        $useIdAsArticul = \COption::GetOptionString(PECOM_ECOMM, 'useIdAsArticul', 'Y') === 'Y';
        $nameFromProp   = trim(\COption::GetOptionString(PECOM_ECOMM, 'nameFromProp', ''));
        $vatUseCatalog  = \COption::GetOptionString(PECOM_ECOMM, 'vatUseCatalog', 'Y') === 'Y';
        $vatDefault     = (int)\COption::GetOptionString(PECOM_ECOMM, 'vatDefault', '20');

        // Fill items with original data
        foreach ($items as $item) {
            $cargoItem = new CargoItem();
            $cargoItem
                ->setId($item['PRODUCT_ID'])
                ->setName($item['NAME'])
                ->setGabs((int)$item['DIMENSIONS']['LENGTH'], (int)$item['DIMENSIONS']['WIDTH'], (int)$item['DIMENSIONS']['HEIGHT'])
                ->setWeight((int)$item['WEIGHT'])
                ->setQuantity((int)$item['QUANTITY'] ?: 1);

            // Props data added only while OrderSender form makes CargoCollection, not while SOA called usual delivery calculation
            if (!empty($item['PROPERTIES']) && is_array($item['PROPERTIES'])) {
                if ($articul) {
                    $cargoItem->setArticul($item['PROPERTIES'][$articul]);
                }
                if ($useIdAsArticul && empty($cargoItem->getArticul())) {
                    $cargoItem->setArticul($cargoItem->getId());
                }
                if ($nameFromProp) {
                    if (!empty($item['PROPERTIES'][$nameFromProp]))
                        $cargoItem->setName($item['PROPERTIES'][$nameFromProp]);
                }
            }

            $price = new Money($item['PRICE'], $item['CURRENCY']);

            // VAT magic crutches

            // Drop to default rate
            $vatRate = ($vatDefault === -1) ? null : $vatDefault;

            // Override with Catalog data if item's VAT rate is allowed
            if ($vatUseCatalog) {
                $itemVatRate = intval($item['VAT_RATE'] * 100);
                if (in_array($itemVatRate, [0, 10, 20])) {
                    $vatRate = $itemVatRate;
                }
            }

            if ($vatRate > 0 && $item['VAT_INCLUDED'] !== 'Y') {
                // VAT not included in item price
                $vatSum = Money::multiply($price, (float)$item['VAT_RATE']);
                $resultPrice = Money::sum([$price, $vatSum]);

                $cargoItem
                    ->setPrice($resultPrice)
                    ->setCost($resultPrice);
            } else {
                $cargoItem
                    ->setPrice($price)
                    ->setCost($price);
            }
            $cargoItem->setVatRate($vatRate);

            $this->cargo->add($cargoItem);
        }
    }

    protected function reformItems()
    {
        if (!empty($this->defaultGabarites)) {
            $items = $this->getItems();

            if ($this->defaultGabarites->getMode() === DefaultGabarites::MODE_CARGO) {
                // Default gabarites for cargo

                $arDimensions = []; // Items gabs for MebiysDimMerger::getSumDimensions()
                $hasEmpty     = false;
                $totalWeight  = 0;
                $totalPrice   = 0;

                foreach ($items as $key => $item) {
                    // Non-pieces measure types crutch
                    $quantity = ((int)$item['QUANTITY']) ?: 1;

                    if ((int)$item['WEIGHT'] > 0) {
                        $totalWeight += $item['WEIGHT'] * $quantity;
                    } else {
                        $hasEmpty = true;
                    }

                    if (
                        isset($item['DIMENSIONS']['LENGTH']) && (int)$item['DIMENSIONS']['LENGTH'] > 0 &&
                        isset($item['DIMENSIONS']['WIDTH']) && (int)$item['DIMENSIONS']['WIDTH'] > 0 &&
                        isset($item['DIMENSIONS']['HEIGHT']) && (int)$item['DIMENSIONS']['HEIGHT'] > 0
                    ) {
                        $arDimensions[] = array($item['DIMENSIONS']['LENGTH'], $item['DIMENSIONS']['WIDTH'], $item['DIMENSIONS']['HEIGHT'], $quantity);
                    } else {
                        $hasEmpty = true;
                    }

                    $totalPrice += $item['PRICE'] * $item['QUANTITY'];
                }

                if ($hasEmpty) {
                    $packer = new MebiysDimMerger();
                    $mergerResult = $packer::getSumDimensions($arDimensions);

                    $items = array(
                        GoodsPicker::makeSimpleGood(array(
                            'WEIGHT'  => max($this->defaultGabarites->getWeight(), $totalWeight),
                            'LENGTH'  => max($this->defaultGabarites->getLength(), $mergerResult->getLength()),
                            'WIDTH'   => max($this->defaultGabarites->getWidth(),  $mergerResult->getWidth()),
                            'HEIGHT'  => max($this->defaultGabarites->getHeight(), $mergerResult->getHeight()),
                            'PRICE'   => $totalPrice,
                        ))
                    );
                }
            } else {
                // Default gabarites for each good (DefaultGabarites::MODE_GOOD)

                foreach ($items as $key => $item)
                {
                    if (!(isset($item['WEIGHT']) && (int)$item['WEIGHT']))
                        $items[$key]['WEIGHT'] = $this->defaultGabarites->getWeight();
                    if (!(isset($item['DIMENSIONS']['LENGTH']) && (int)$item['DIMENSIONS']['LENGTH']))
                        $items[$key]['DIMENSIONS']['LENGTH'] = $this->defaultGabarites->getLength();
                    if(!(isset($item['DIMENSIONS']['WIDTH']) && (int)$item['DIMENSIONS']['WIDTH']))
                        $items[$key]['DIMENSIONS']['WIDTH'] = $this->defaultGabarites->getWidth();
                    if(!(isset($item['DIMENSIONS']['HEIGHT']) && (int)$item['DIMENSIONS']['HEIGHT']))
                        $items[$key]['DIMENSIONS']['HEIGHT'] = $this->defaultGabarites->getHeight();
                }
            }

            return $items;
        }

        return $this->getItems();
    }

    // Only getters and setters below this line

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return $this
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @return CoreCargo
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * @param CoreCargo $cargo
     */
    public function setCargo($cargo)
    {
        $this->cargo = $cargo;

        return $this;
    }
}