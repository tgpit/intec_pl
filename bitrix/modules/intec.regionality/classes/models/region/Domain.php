<?php
namespace intec\regionality\models\region;

use Bitrix\Main\Localization\Loc;
use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\regionality\models\Region;

Loc::loadMessages(__FILE__);

/**
 * Модель домена региона.
 * Class Domain
 * @property integer $id Идентификатор.
 * @property integer $regionId Идентификатор региона.
 * @property string $siteId Идентификатор сайта.
 * @property integer $active Активность.
 * @property integer $default По умолчанию.
 * @property string $value Значение.
 * @property integer $sort Сортировка.
 * @package intec\regionality\models\region
 * @author apocalypsisdimon@gmail.com
 */
class Domain extends ActiveRecord
{
    /** @var array $cache */
    protected static $cache = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'regionality_regions_domains';
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->default == 1) {
                $this->active = 1;

                /** @var static[] $domains */
                $domains = static::find()->where([
                    'regionId' => $this->regionId,
                    'siteId' => $this->siteId,
                    'default' => 1
                ])->all();

                /** @var static $region */
                foreach ($domains as $domain) {
                    if ($domain->id == $this->id || !$domain->default)
                        continue;

                    $domain->default = 0;
                    $domain->save();
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Loc::getMessage('intec.regionality.models.region.domain.attributes.id'),
            'regionId' => Loc::getMessage('intec.regionality.models.region.domain.attributes.regionId'),
            'siteId' => Loc::getMessage('intec.regionality.models.region.domain.attributes.siteId'),
            'active' => Loc::getMessage('intec.regionality.models.region.domain.attributes.active'),
            'default' => Loc::getMessage('intec.regionality.models.region.domain.attributes.default'),
            'value' => Loc::getMessage('intec.regionality.models.region.domain.attributes.value'),
            'sort' => Loc::getMessage('intec.regionality.models.region.domain.attributes.sort')
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'regionId' => [['regionId'], 'integer'],
            'siteId' => [['siteId'], 'string', 'length' => 2],
            'active' => [['active'], 'boolean'],
            'activeDefault' => [['active'], 'default', 'value' => 1],
            'default' => [['default'], 'boolean'],
            'defaultDefault' => [['default'], 'default', 'value' => 0],
            'value' => [['value'], 'string', 'max' => 255],
            'sort' => [['sort'], 'integer'],
            'sortDefault' => [['sort'], 'default', 'value' => 500],
            'required' => [['regionId', 'siteId', 'active', 'default', 'value', 'sort'], 'required'],
            'unique' => [['value'], 'unique', 'message' => Loc::getMessage('intec.regionality.models.region.domain.errors.exists')]
        ];
    }

    /**
     * Реляция. Возвращает регион, к которому относится значение.
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