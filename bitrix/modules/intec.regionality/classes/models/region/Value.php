<?php
namespace intec\regionality\models\region;

use Bitrix\Main\Localization\Loc;
use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\core\helpers\Type;
use intec\regionality\models\Region;

Loc::loadMessages(__FILE__);

/**
 * Модель значения свойства региона.
 * Class Value
 * @property string $propertyCode Код свойства.
 * @property integer $regionId Идентификатор региона.
 * @property string $siteId Идентификатор сайта.
 * @property string $value Значение.
 * @property boolean $isEmpty Значение пустое. Только для чтения.
 * @property string $normalizedValue Нормализованное значение. Только для чтения.
 * @package intec\regionality\models\region
 * @author apocalypsisdimon@gmail.com
 */
class Value extends ActiveRecord
{
    /** @var array $cache */
    protected static $cache = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'regionality_regions_values';
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();

        $this->normalize();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->normalize();

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'value' => [
                'class' => 'intec\core\behaviors\FieldObject',
                'attribute' => 'value'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return [
            'propertyCode',
            'regionId',
            'siteId'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'propertyCode' => [['propertyCode'], 'string', 'max' => 255],
            'regionId' => [['regionId'], 'integer'],
            'siteId' => [['siteId'], 'string', 'max' => 2],
            'value' => [['value'], 'string'],
            'required' => [['propertyCode', 'regionId'], 'required'],
            'unique' => [['propertyCode', 'regionId', 'siteId'], 'unique', 'targetAttribute' => ['propertyCode', 'regionId', 'siteId']]
        ];
    }

    /**
     * Возвращает значение, указывающее на пустоту значения.
     * @return boolean
     */
    public function getIsEmpty()
    {
        $result = true;
        $values = $this->value;

        if (Type::isArray($values)) {
            foreach ($values as $value)
                if (!empty($value) || Type::isNumeric($value)) {
                    $result = false;
                    break;
                }
        } else {
            $result = empty($values) && !Type::isNumeric($values);
        }

        return $result;
    }

    /**
     * Возвращает нормализованное значение.
     * @return mixed
     */
    public function getNormalizedValue()
    {
        $result = null;
        $values = $this->value;

        if (!$this->getIsEmpty()) {
            if (Type::isArray($values)) {
                $result = [];

                foreach ($values as $value)
                    if (!empty($value) || Type::isNumeric($value))
                        $result[] = $value;

                if (empty($result))
                    $result = null;
            } else {
                $result = $values;
            }
        }

        return $result;
    }

    /**
     * Операция. Нормализует значение.
     */
    public function normalize()
    {
        $this->value = $this->getNormalizedValue();
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