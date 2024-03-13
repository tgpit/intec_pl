<?php
namespace Pecom\Ecomm\ORM\Base;

use Bitrix\Main\ORM\Data\DataManager as BitrixDataManager;
use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\DB\Connection;

abstract class DataManager extends BitrixDataManager
{
    public static function query()
    {
        return new Query(static::getEntity());
    }

    /**
     * Устраняет проблемы с таблицей
     * @return void
     */
    public static function fixTable()
    {
        if (!static::isTableExists()) {
            static::createTable();
            static::fillTableDefaultValues();
        }
    }

    /**
     * Возвращает наличие таблицы в базе данных
     * @return bool
     */
    protected static function isTableExists(): bool
    {
        return static::getConnection()->isTableExists(static::getTableName());
    }

    /**
     * Возвращает подключение к базе данных
     * @return Connection
     */
    protected static function getConnection(): Connection
    {
        return Application::getConnection(static::getConnectionName());
    }

    /**
     * Создаёт таблицу
     * @return void
     */
    protected static function createTable(): void
    {
        static::getEntity()->createDBTable();
    }

    /**
     * Заполняет таблицу значениями по умолчанию
     * @return void
     */
    protected static function fillTableDefaultValues()
    {
        // todo: сделать
    }

    /**
     * Возвращает значения для таблицы по умолчанию
     * @return array
     */
    protected static function getTableDefaultValues(): array
    {
        return [];
    }
}