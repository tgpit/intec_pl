<?php
namespace intec\seo\filter\conditions;

use intec\core\bitrix\conditions\IBlockPropertyCondition;

/**
 * Класс, представляющий условие максимального значения свойства.
 * Class IBlockPropertyMaximalCondition
 * @package intec\seo\filter\conditions
 */
class IBlockPropertyMaximalCondition extends IBlockPropertyCondition
{
    /**
     * @inheritdoc
     */
    public static function getOperators()
    {
        return [
            self::OPERATOR_EQUAL,
            self::OPERATOR_NOT_EQUAL,
            self::OPERATOR_LESS,
            self::OPERATOR_LESS_OR_EQUAL,
            self::OPERATOR_MORE,
            self::OPERATOR_MORE_OR_EQUAL
        ];
    }
}