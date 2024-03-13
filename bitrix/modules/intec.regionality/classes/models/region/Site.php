<?php
namespace intec\regionality\models\region;

use Bitrix\Main\Localization\Loc;
use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\regionality\models\Region;

Loc::loadMessages(__FILE__);

/**
 * Модель привязки сайта региона.
 * Class Site
 * @property integer $regionId Идентификатор региона.
 * @property string $siteId Идентификатор сайта.
 * @package intec\regionality\models\region
 * @author apocalypsisdimon@gmail.com
 */
class Site extends ActiveRecord
{
    /** @var array $cache */
    protected static $cache = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'regionality_regions_sites';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'regionId' => [['regionId'], 'integer'],
            'siteId' => [['siteId'], 'string', 'max' => 2],
            'required' => [['regionId', 'siteId'], 'required'],
            'unique' => [['regionId', 'siteId'], 'unique', 'targetAttribute' => ['regionId', 'siteId']]
        ];
    }

    /**
     * Реляция. Возвращает регион, к которому относится сайт.
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