<?php
namespace intec\regionality;

use Exception;
use Bitrix\Main\EventResult;
use Bitrix\Main\Context;
use intec\Core;
use intec\core\base\BaseObject;
use intec\core\helpers\StringHelper;
use intec\core\net\Url;
use intec\regionality\models\Region;
use intec\regionality\models\region\Value;
use intec\regionality\models\SiteSettings;
use intec\regionality\tools\Domain as DomainTools;

/**
 * Класс модуля.
 * Class Module
 * @package intec\regionality
 * @author apocalypsisdimon@gmail.com
 */
class Module extends BaseObject
{
    /**
     * Системная переменная модуля.
     */
    const VARIABLE = 'REGIONALITY';
}