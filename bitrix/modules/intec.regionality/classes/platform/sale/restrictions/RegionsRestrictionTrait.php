<?php
namespace intec\regionality\platform\sale\restrictions;

use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Internals\Entity;
use intec\core\helpers\ArrayHelper;
use intec\regionality\models\Region;

Loc::loadMessages(__FILE__);

/**
 * Предстваляет собой расширение ограничения по регионам.
 * Trait RegionsRestrictionTrait
 * @package intec\regionality\platform\sale\restrictions
 * @author apocalypsisdimon@gmail.com
 */
trait RegionsRestrictionTrait
{
    /**
     * @inheritdoc
     */
    public static function check($options, array $parameters, $serviceId = 0)
    {
        if (!isset($parameters['REGIONS']))
            return false;

        $region = Region::getCurrent();

        if (empty($region))
            return false;

        return ArrayHelper::isIn($region->id, $parameters['REGIONS']);
    }

    /**
     * @inheritdoc
     */
    protected static function extractParams(Entity $entity)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public static function getClassTitle()
    {
        return Loc::getMessage('intec.regionality.platform.sale.restrictions.regionsRestrictionTrait.title');
    }

    /**
     * @inheritdoc
     */
    public static function getClassDescription()
    {
        return Loc::getMessage('intec.regionality.platform.sale.restrictions.regionsRestrictionTrait.description');
    }

    /**
     * @inheritdoc
     */
    public static function getParamsStructure($entityId = 0)
    {
        $regions = Region::find()
            ->where(['active' => 1])
            ->all();

        return [
            'REGIONS' => [
                'TYPE' => 'ENUM',
                'MULTIPLE' => 'Y',
                'LABEL' => Loc::getMessage('intec.regionality.platform.sale.restrictions.regionsRestrictionTrait.parameters.regions'),
                "OPTIONS" => $regions->asArray(function ($index, $region) {
                    return [
                        'key' => $region->id,
                        'value' => '['.$region->id.'] '.$region->name
                    ];
                })
            ]
        ];
    }
}