<?php
namespace intec\regionality\tools;

use Bitrix\Main\Localization\Loc;
use intec\core\base\BaseObject;
use intec\core\helpers\Type;

Loc::loadMessages(__FILE__);

/**
 * Инструменты для работы с доменами.
 * Class Domain
 * @package intec\regionality\tools
 * @author apocalypsisdimon@gmail.com
 */
class Domain extends BaseObject
{
    /**
     * Получает корневой домен.
     * @param string $domain Домен.
     * @return string
     */
    public static function getRoot($domain)
    {
        $domain = Type::toString($domain);

        if (empty($domain))
            return $domain;

        $domain = explode('.', $domain);
        $length = count($domain);

        if ($length >= 2) {
            $domain = $domain[$length - 2].'.'.$domain[$length - 1];
        } else {
            $domain = $domain[0];
        }

        return $domain;
    }
}