<?php
namespace Pecom\Delivery\Bitrix\Adapter;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

use Pecom\Delivery\Bitrix\Adapter\Cargo;
use Pecom\Delivery\Bitrix\Entity\DefaultGabarites;
use Pecom\Delivery\Bitrix\Handler\GoodsPicker;
use Pecom\Delivery\Core\Delivery\CargoCollection;

/**
 * Class Cargoes
 * @package Pecom\Delivery\Bitrix
 * @subpackage Adapter
 */
class Cargoes
{
    /**
     * @var CargoCollection
     */
    protected $coreCargoes;

    public function __construct()
    {
        $this->coreCargoes = new CargoCollection();
    }

    /**
     * Makes Cargo collection using data from DB row (serialized array)
     * @param string $data
     * @return $this
     */
    public function fromDB($data)
    {
        $data = unserialize($data, ['allowed_classes' => false]);
        $this->fromArray($data);

        return $this;
    }

    /**
     * Makes Cargo collection using data from Bitrix order
     * @param int $bitrixId
     * @return $this
     */
    public function fromOrder($bitrixId)
    {
        $goods = GoodsPicker::fromOrder($bitrixId);

        // Props
        $articul = \COption::GetOptionString(PECOM_ECOMM, 'articul', 'ARTNUMBER');
        $nameFromProp = trim(\COption::GetOptionString(PECOM_ECOMM, 'nameFromProp', ''));

        GoodsPicker::addBasketGoodProperties($goods, [$articul, $nameFromProp]);

        // Marking codes for future using
        // GoodsPicker::addGoodsQRs($goods, $bitrixId);

        $this->fromGoodsArray($goods);

        return $this;
    }

    /**
     * Makes Cargo collection with 1 Cargo and all goods inside it. Default logic for delivery calculation in SOA.
     * @param array $goods use getGoods() result
     * @return $this
     */
    public function fromGoodsArray($goods)
    {
        $cargoAdapter = new Cargo(new DefaultGabarites());
        $cargoAdapter->make($goods);

        $coreCargo = $cargoAdapter->getCargo();
        $this->coreCargoes->add($coreCargo);

        // onItemsToCargoes module event
        $cargoes = $this->toArray();

        $event = new Event(PECOM_ECOMM, "onItemsToCargoes", ['ITEMS' => $goods, 'CARGOES' => $cargoes]);
        $event->send();

        $results = $event->getResults();
        if (!empty($results) && is_array($results)) {
            $newCargoes = [];

            foreach ($results as $eventResult) {
                if ($eventResult->getType() !== EventResult::SUCCESS)
                    continue;

                $params = $eventResult->getParameters();
                if (isset($params["CARGOES"]))
                    $newCargoes = $params["CARGOES"];
            }

            if (!empty($newCargoes) && is_array($newCargoes)) {
                $this->getCoreCargoes()->clear();
                $this->fromArray($newCargoes);
            }
        }

        return $this;
    }

    /**
     * Makes CargoCollection from associative array
     * Also convert to Core types some params like nullable vatRate
     * @return $this
     */
    public function fromArray($data)
    {
        foreach ($data['cargoes'] as &$cargo) {
            foreach ($cargo['items'] as &$item) {
                $item['vatRate'] = ((int)$item['vatRate'] === -1) ? null : (int)$item['vatRate'];
            }
        }

        $this->coreCargoes->fromArray($data);

        return $this;
    }

    /**
     * Returns CargoCollection data as associative array
     * Also convert from Core types some params like nullable vatRate
     * @return array
     */
    public function toArray()
    {
        $data = $this->coreCargoes->toArray();

        foreach ($data['cargoes'] as &$cargo) {
            foreach ($cargo['items'] as &$item) {
                if (is_null($item['vatRate'])) {
                    $item['vatRate'] = -1;
                }
            }
        }

        return $data;
    }

    /**
     * Convert and returns Cargo collection as specific array used for /Calculator API call
     * @return array
     */
    public function makeCalculatorPlaces()
    {
        $places = [];

        $this->coreCargoes->reset();
        while ($cargo = $this->coreCargoes->getNext()) {
            $places[] = [
                'weight' => (int)$cargo->getWeight(),            // g
                'length' => (int)ceil($cargo->getLength() / 10), // cm
                'width'  => (int)ceil($cargo->getWidth()  / 10), // cm
                'height' => (int)ceil($cargo->getHeight() / 10), // cm
            ];
        }

        return $places;
    }

    /**
     * Convert and returns Cargo collection as specific array used for /Orders API call
     * @return array
     */
    public function makeOrderPlaces()
    {
        $places = [];

        $i = 1;
        $this->coreCargoes->reset();
        while ($cargo = $this->coreCargoes->getNext()) {
            $place = [
                'weight'      => (int)$cargo->getWeight(),            // g
                'length'      => (int)ceil($cargo->getLength() / 10), // cm
                'width'       => (int)ceil($cargo->getWidth()  / 10), // cm
                'height'      => (int)ceil($cargo->getHeight() / 10), // cm
                'placeNumber' => (string)$i,
                'items'       => [],
            ];

            $cargo->reset();
            while ($item = $cargo->getNext()) {
                $place['items'][] = [
                    'weight'       => (int)$item->getWeight(),            // g
                    'length'       => (int)ceil($item->getLength() / 10), // cm
                    'width'        => (int)ceil($item->getWidth()  / 10), // cm
                    'height'       => (int)ceil($item->getHeight() / 10), // cm
                    'articul'      => $item->getArticul(),
                    'description'  => $item->getName(),
                    'quantity'     => (int)$item->getQuantity(),
                    'assessedCost' => $item->getCost()->getAmount(),
                    'cost'         => $item->getPrice()->getAmount(),
                    'costVat'      => is_null($item->getVatRate()) ? -1 : (int)$item->getVatRate()
                ];
            }

            $places[] = $place;
            $i++;
        }

        return $places;
    }

    /**
     * @return CargoCollection
     */
    public function getCoreCargoes()
    {
        return $this->coreCargoes;
    }

    /**
     * @param CargoCollection $coreCargoes
     * @return Cargoes
     */
    public function setCoreCargoes($coreCargoes)
    {
        $this->coreCargoes = $coreCargoes;
        return $this;
    }
}