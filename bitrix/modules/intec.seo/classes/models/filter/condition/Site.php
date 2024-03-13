<?php
namespace intec\seo\models\filter\condition;

use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\seo\models\filter\Condition;

/**
 * Модель привязки условия фильтра к сайту.
 * Class Site
 * @property integer $conditionId Условие.
 * @property string $siteId Сайт.
 * @package intec\seo\models\filter\condition
 * @author apocalypsisdimon@gmail.com
 */
class Site extends ActiveRecord
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
        return 'seo_filter_conditions_sites';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'conditionId' => [['conditionId'], 'integer'],
            'siteId' => [['siteId'], 'string', 'length' => 2],
            'required' => [[
                'conditionId',
                'siteId'
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