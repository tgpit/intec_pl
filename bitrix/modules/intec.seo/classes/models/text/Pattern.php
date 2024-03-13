<?php
namespace intec\seo\models\text;

use Bitrix\Main\Localization\Loc;
use intec\core\db\ActiveRecord;

Loc::loadMessages(__FILE__);

/**
 * Модель текстового шаблона.
 * Class Pattern
 * @property integer $id Идентификатор.
 * @property string $code Код.
 * @property integer $active Активность.
 * @property string $name Наименование.
 * @property string $value Значение.
 * @property integer $sort Сортировка.
 * @package intec\seo\models\text
 * @author apocalypsisdimon@gmail.com
 */
class Pattern extends ActiveRecord
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
        return 'seo_texts_patterns';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Loc::getMessage('intec.seo.models.text.pattern.attributes.id'),
            'code' => Loc::getMessage('intec.seo.models.text.pattern.attributes.code'),
            'active' => Loc::getMessage('intec.seo.models.text.pattern.attributes.active'),
            'name' => Loc::getMessage('intec.seo.models.text.pattern.attributes.name'),
            'value' => Loc::getMessage('intec.seo.models.text.pattern.attributes.value'),
            'sort' => Loc::getMessage('intec.seo.models.text.pattern.attributes.sort')
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'code' => [['code'], 'string', 'max' => 255],
            'codeMatch' => [['code'], 'match', 'pattern' => '/^[A-Za-z0-9_ -]*$/'],
            'active' => [['active'], 'boolean'],
            'activeDefault' => [['active'], 'default', 'value' => 1],
            'name' => [['name'], 'string', 'max' => 255],
            'value' => [['value'], 'string'],
            'sort' => [['sort'], 'integer'],
            'sortDefault' => [['sort'], 'default', 'value' => 500],
            'unique' => [['code'], 'unique', 'targetAttribute' => ['code']],
            'required' => [['code', 'name', 'sort'], 'required']
        ];
    }
}