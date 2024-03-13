<?php
namespace intec\seo\models\filter\condition;

use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\seo\models\filter\Condition;

/**
 * Модель связи условия с условием перелинковки тегов.
 * Class TagRelinkingCondition
 * @property integer $conditionId Условие.
 * @property integer $relinkingConditionId Условие перелинковки.
 * @package intec\seo\models\filter\condition
 * @author apocalypsisdimon@gmail.com
 */
class TagRelinkingCondition extends ActiveRecord
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
        return 'seo_filter_conditions_tags_relinking_conditions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'conditionId' => [['conditionId'], 'integer'],
            'relinkingConditionId' => [['relinkingConditionId'], 'integer'],
            'required' => [[
                'conditionId',
                'relinkingConditionId'
            ], 'required']
        ];
    }

    /**
     * Реляция. Возвращает условие.
     * @param boolean $result Возвращать результат.
     * @return ActiveQuery|Condition|null
     */
    public function getCondition($result = false)
    {
        return $this->relation(
            'condition',
            $this->hasOne(Condition::className(), ['id' => 'conditionId']),
            $result
        );
    }

    /**
     * Реляция. Возвращает условие перелинковки.
     * @param boolean $result Возвращать результат.
     * @return ActiveQuery|Condition|null
     */
    public function getRelinkingCondition($result = false)
    {
        return $this->relation(
            'condition',
            $this->hasOne(Condition::className(), ['id' => 'relinkingConditionId']),
            $result
        );
    }
}