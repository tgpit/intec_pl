<?php
namespace intec\measures\models;

use intec\core\db\ActiveRecord;
use intec\measures\Module;

/**
 * Модель коэффициента конвертации единицы измерения товара.
 * Class ConversionRatio
 * @property integer $productId Идентификатор товара.
 * @property integer $measureId Единица измерения.
 * @property integer $active Активность конверсии.
 * @property integer $measureToId Единица измерения, в которую происходит конвертирование.
 * @property double $value Значение коэффициента конвертирования.
 * @package intec\measures\models
 * @author apocalypsisdimon@gmail.com
 */
class ConversionRatio extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'measures_conversions_ratios';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'productId' => [['productId'], 'integer'],
            'measureId' => [['measureId'], 'integer'],
            'active' => [['active'], 'boolean'],
            'activeDefault' => [['active'], 'default', 'value' => 1],
            'value' => [['value'], 'double'],
            'valueBounds' => [['value'], 'compare', 'compareValue' => 0, 'operator' => '>', 'type' => 'number'],
            'valueDefault' => [['value'], 'default', 'value' => 1],
            'unique' => [['productId', 'measureId'], 'unique', 'targetAttribute' => ['productId', 'measureId']],
            'required' => [['productId', 'measureId', 'active', 'value'], 'required']
        ];
    }

    /**
     * Возвращает единицу измерения конвертации.
     * @return array|null
     */
    public function getMeasure()
    {
        $measures = Module::getMeasures();

        return isset($measures[$this->measureId]) ? $measures[$this->measureId] : null;
    }
}