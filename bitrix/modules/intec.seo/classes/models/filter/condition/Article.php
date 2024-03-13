<?php
namespace intec\seo\models\filter\condition;

use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\seo\models\filter\Condition;

/**
 * Модель привязки шаблона наименований элементов к разделу.
 * Class Section
 * @property integer $templateId Шаблон.
 * @property integer $iBlockSectionId Раздел.
 * @package intec\seo\models\iblocks\metadata\template
 * @author apocalypsisdimon@gmail.com
 */
class Article extends ActiveRecord
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
        return 'seo_filter_conditions_articles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'conditionId' => [['conditionId'], 'integer'],
            'iBlockElementId' => [['iBlockElementId'], 'integer'],
            'required' => [[
                'conditionId',
                'iBlockElementId'
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