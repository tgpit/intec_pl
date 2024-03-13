<?php
namespace intec\regionality\models\region;

use Bitrix\Main\Localization\Loc;
use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\regionality\models\Region;

Loc::loadMessages(__FILE__);

/**
 * Модель привязки склада региона.
 * Class Store
 * @property integer $regionId Идентификатор региона.
 * @property integer $storeId Идентификатор типа цены.
 * @package intec\regionality\models\region
 * @author apocalypsisdimon@gmail.com
 */
class Store extends ActiveRecord
{
    /** @var array $cache */
    protected static $cache = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'regionality_regions_stores';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'regionId' => [['regionId'], 'integer'],
            'storeId' => [['storeId'], 'integer'],
            'required' => [['regionId', 'storeId'], 'required'],
            'unique' => [['regionId', 'storeId'], 'unique', 'targetAttribute' => ['regionId', 'storeId']]
        ];
    }

    /**
     * Реляция. Возвращает регион, к которому относится тип цены.
     * @param bool $result
     * @return Region|ActiveQuery
     */
    public function getRegion($result = false)
    {
        return $this->relation(
            'region',
            $this->hasOne(Region::className(), ['id' => 'regionId']),
            $result
        );
    }
}