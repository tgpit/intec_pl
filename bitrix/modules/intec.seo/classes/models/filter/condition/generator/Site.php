<?php
namespace intec\seo\models\filter\condition\generator;

use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\seo\models\filter\condition\Generator;

/**
 * Модель привязки генератора условий фильтра к сайту.
 * Class Site
 * @property integer $generatorId Условие.
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
        return 'seo_filter_conditions_generators_sites';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'generatorId' => [['generatorId'], 'integer'],
            'siteId' => [['siteId'], 'string', 'length' => 2],
            'required' => [[
                'generatorId',
                'siteId'
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