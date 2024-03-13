<?php

namespace intec\core\bitrix;

use intec\core\base\BaseObject;

/**
 * Class Query
 * @package intec\core\bitrix
 * @deprecated
 */
abstract class Query extends BaseObject
{

    /**
     * Запускает запрос и возвращает результат.
     * @return mixed
     */
    public abstract function execute();
}
