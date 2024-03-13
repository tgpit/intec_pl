<?php
namespace intec\seo\models\filter\condition\generator;

use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\seo\models\filter\condition\Generator;

/**
 * Модель привязки генератора условий фильтра к разделу.
 * Class Section
 * @property integer $generatorId Генератор.
 * @property integer $iBlockSectionId Раздел.
 * @package intec\seo\models\filter\condition\generator
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
        return 'seo_filter_conditions_generators_sections';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'generatorId' => [['generatorId'], 'integer'],
            'iBlockSectionId' => [['iBlockSectionId'], 'integer'],
            'required' => [[
                'generatorId',
                'iBlockSectionId'
            ], 'required']
        ];
    }

    /**
     * Реляция. Возвращает привязанный генератор условий.
     * @param boolean $result Возвращать результат.
     * @return Generator|ActiveQuery|null
     */
    public function getGenerator($result = false)
    {
        return $this->relation(
            'generator',
            $this->hasOne(Generator::className(), ['id' => 'generatorId']),
            $result
        );
    }
}