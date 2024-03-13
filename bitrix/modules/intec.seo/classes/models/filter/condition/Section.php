<?php
namespace intec\seo\models\filter\condition;

use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\seo\models\filter\Condition;

/**
 * Модель привязки условия фильтра к разделу.
 * Class Section
 * @property integer $conditionId Условие.
 * @property integer $iBlockSectionId Раздел.
 * @package intec\seo\models\filter\condition
 * @author apocalypsisdimon@gmail.com
 */
class Section extends ActiveRecord
{
    /**
     * @var array $cache
     */
    protected static $cache = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seo_filter_conditions_sections';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'conditionId' => [['conditionId'], 'integer'],
            'iBlockSectionId' => [['iBlockSectionId'], 'integer'],
            'required' => [[
                'conditionId',
                'iBlockSectionId'
            ], 'required']
        ];
    }

    /**
     * Реляция. Возвращает привязанное условие.
     * @param boolean $result Возвращать результат.
     * @return Condition|ActiveQuery|null
     */
    public function getCondition($result = false)
    {
        return $this->relation(
            'condition',
            $this->hasOne(Condition::className(), ['id' => 'conditionId']),
            $result
        );
    }
}