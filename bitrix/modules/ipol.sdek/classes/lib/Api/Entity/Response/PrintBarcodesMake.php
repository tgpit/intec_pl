<?php
namespace Ipolh\SDEK\Api\Entity\Response;

use Ipolh\SDEK\Api\BadResponseException;
use Ipolh\SDEK\Api\Entity\Response\Part\Common\Entity;
use Ipolh\SDEK\Api\Entity\Response\Part\Common\RequestList;

/**
 * Class PrintBarcodesMake
 * @package Ipolh\SDEK\Api\Entity\Response
 */
class PrintBarcodesMake extends AbstractResponse
{
    /**
     * @var Entity|null
     */
    protected $entity;

    /**
     * @var RequestList
     */
    protected $requests;

    /**
     * @return Entity|null
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param array $entity
     * @return PrintBarcodesMake
     */
    public function setEntity($entity)
    {
        $this->entity = new Entity($entity);
        return $this;
    }

    /**
     * @return RequestList
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * @param array $array
     * @return PrintBarcodesMake
     * @throws BadResponseException
     */
    public function setRequests($array)
    {
        $collection = new RequestList();
        $this->requests = $collection->fillFromArray($array);
        return $this;
    }
}