<?php
namespace intec\core\base;

/**
 * Класс, представляющий запрос.
 * Class Query
 * @package intec\core\base
 */
abstract class Query extends BaseObject implements QueryInterface
{
    /**
     * @inheritdoc
     */
    public abstract function execute();
}
