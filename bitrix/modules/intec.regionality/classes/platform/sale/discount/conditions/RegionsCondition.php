<?php
namespace intec\regionality\platform\sale\discount\conditions;

use CGlobalCondCtrlGroup as ConditionGroup;
use CGlobalCondCtrlComplex as Condition;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\regionality\models\Region;
use intec\regionality\Module;

Loc::loadMessages(__FILE__);
Loader::includeModule('catalog');

/**
 * Предстваляет собой условие скидки по региону.
 * Class RegionsCondition
 * @package intec\regionality\platform\sale\discount\conditions
 * @author apocalypsisdimon@gmail.com
 */
class RegionsCondition extends Condition
{
    /**
     * @inheritdoc
     */
    public static function GetControlID()
    {
        return [
            'CondRegionalityRegions'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function GetControlDescr()
    {
        $description = parent::GetControlDescr();
        $description['EXECUTE_MODULE'] = 'all';

        return $description;
    }

    /**
     * @inheritdoc
     */
    public static function GetControlShow($parameters)
    {
        $result = array(
            'controlgroup' => true,
            'group' =>  false,
            'label' => Loc::getMessage('intec.regionality.platform.sale.discount.conditions.regionsCondition.group'),
            'showIn' => static::GetShowIn($parameters['SHOW_IN_GROUPS']),
            'children' => []
        );

        $controls = static::GetControls();

        foreach ($controls as &$control) {
            $result['children'][] = [
                'controlId' => $control['ID'],
                'group' => false,
                'label' => Loc::getMessage('intec.regionality.platform.sale.discount.conditions.regionsCondition.name'),
                'showIn' => static::GetShowIn($parameters['SHOW_IN_GROUPS']),
                'control' => [
                    [
                        'id' => 'prefix',
                        'type' => 'prefix',
                        'text' => $control['PREFIX']
                    ],
                    static::GetLogicAtom($control['LOGIC']),
                    static::GetValueAtom($control['JS_VALUE'])
                ]
            ];
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public static function Generate($condition, $parameters, $control, $subs = false)
    {
        if (Type::isString($control))
            $control = static::GetControls($control);

        if (!Type::isArray($control))
            return false;

        $values = static::Check(
            $condition,
            $parameters,
            $control,
            false
        );

        if ($values === false)
            return false;

        $region = Region::getCurrent();

        if (empty($region))
            return false;

        $logic = static::SearchLogic($values['logic'], $control['LOGIC']);

        if (empty($logic['OP'][$control['MULTIPLE']]))
            return false;

        return StringHelper::replace($logic['OP'][$control['MULTIPLE']], [
            '#VALUE#' => '('.
                '('.
                    'isset($_SESSION[\''.Module::VARIABLE.'\']) && is_array($_SESSION[\''.Module::VARIABLE.'\']) && '.
                    'isset($_SESSION[\''.Module::VARIABLE.'\'][\''.Region::VARIABLE.'\']) && is_array($_SESSION[\''.Module::VARIABLE.'\'][\''.Region::VARIABLE.'\']) && '.
                    'isset($_SESSION[\''.Module::VARIABLE.'\'][\''.Region::VARIABLE.'\'][\'ID\'])'.
                ') '.
                '? $_SESSION[\''.Module::VARIABLE.'\'][\''.Region::VARIABLE.'\'][\'ID\'] '.
                ': null'.
            ')',
            '#FIELD#' => var_export($values['value'], true)
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function GetControls($controlId = false)
    {
        $regions = Region::find()
            ->where(['active' => 1])
            ->all();

        $controls = [
            'CondRegionalityRegions' => [
                'ID' => 'CondRegionalityRegions',
                'EXECUTE_MODULE' => 'all',
                'FIELD' => 'id',
                'FIELD_TYPE' => 'int',
                'MODULE_ID' => 'intec.regionality',
                'MODULE_ENTITY' => 'Region',
                'MULTIPLE' => 'Y',
                'PARENT' => true,
                'GROUP' => 'N',
                'LABEL' => Loc::getMessage('intec.regionality.platform.sale.discount.conditions.regionsCondition.name'),
                'PREFIX' => Loc::getMessage('intec.regionality.platform.sale.discount.conditions.regionsCondition.prefix'),
                'LOGIC' => static::GetLogic([BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ]),
                'JS_VALUE' => [
                    'type' => 'select',
                    'multiple' => 'Y',
                    'values' => $regions->asArray(function ($index, $region) {
                        return [
                            'key' => $region->id,
                            'value' => '['.$region->id.'] '.$region->name
                        ];
                    })
                ],
                'PHP_VALUE' => [
                    'VALIDATE' => 'list'
                ]
            ]
        ];

        if ($controlId === false) {
            return $controls;
        } else if (isset($controls[$controlId])) {
            return $controls[$controlId];
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public static function GetShowIn($controls)
    {
        return [ConditionGroup::GetControlID()];
    }
}