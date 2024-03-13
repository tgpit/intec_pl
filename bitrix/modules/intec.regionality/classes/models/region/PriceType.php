<?php
namespace intec\regionality\models\region;

use Bitrix\Main\Localization\Loc;
use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\regionality\models\Region;

Loc::loadMessages(__FILE__);

/**
 * Модель привязки типа цены региона.
 * Class PriceType
 * @property integer $regionId Идентификатор региона.
 * @property integer $priceTypeId Идентификатор типа цены.
 * @package intec\regionality\models\region
 * @author apocalypsisdimon@gmail.com
 */
class PriceType extends ActiveRecord
{
    /** @var array $cache */
    protected static $cache = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'regionality_regions_prices_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'regionId' => [['regionId'], 'integer'],
            'priceTypeId' => [['priceTypeId'], 'integer'],
            'required' => [['regionId', 'priceTypeId'], 'required'],
            'unique' => [['regionId', 'priceTypeId'], 'unique', 'targetAttribute' => ['regionId', 'priceTypeId']]
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