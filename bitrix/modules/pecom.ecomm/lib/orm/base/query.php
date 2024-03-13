<?php
namespace Pecom\Ecomm\ORM\Base;

use Bitrix\Main\ORM\Query\Query as BitrixQuery;
use Bitrix\Main\Application;

class Query extends BitrixQuery
{
    public function exec()
    {
        try {
            return parent::exec();
        } catch (\Throwable $throwable) {
            $this->fixDataTable();
            return parent::exec();
        }
    }

    protected function fixDataTable(): void
    {
        $this->getDataClass()::fixTable();
    }

    /**
     * Возвращает полное имя класса с картой данных
     * @return string
     */
    protected function getDataClass(): string
    {
        return $this->getEntity()->getDataClass();
    }
}