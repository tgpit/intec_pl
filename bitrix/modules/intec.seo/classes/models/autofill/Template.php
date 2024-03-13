<?php
namespace intec\seo\models\autofill;

use CUserTypeEntity;
use CIBlockProperty;
use CCatalogSku;
use CCatalogGroup;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\StoreTable;
use intec\Core;
use intec\core\collections\Arrays;
use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\core\db\ActiveRecords;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\regionality\models\Region;
use intec\seo\models\autofill\template\Section;
use intec\seo\models\autofill\template\FillingSection;
use intec\seo\models\autofill\template\Element;
use intec\seo\models\autofill\template\Site;

Loc::loadMessages(__FILE__);

/**
 * Модель шаблона наименований элементов.
 * Class Condition
 * @property integer $id Идентификатор.
 * @property string $code Код.
 * @property integer $active Активность.
 * @property string $name Наименование.
 * @property integer $iBlockId Инфоблок.
 * @property string $value Значение.
 * @property boolean $random В случайном порядке.
 * @property boolean $self Использовать элементы раздела.
 * @property integer $quantity Количество.
 * @property integer $sort Сортировка.
 * @package intec\seo\models\iblocks\elements\names
 * @author apocalypsisdimon@gmail.com
 */
class Template extends ActiveRecord
{
    /**
     * @var array $cache
     */
    protected static $cache = [];

    /**
     * @inheritdoc
     * @return TemplateQuery
     */
    public static function find()
    {
        return Core::createObject(TemplateQuery::className(), [get_called_class()]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seo_autofill_templates';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'code' => [['code'], 'string', 'max' => 255],
            'active' => [['active'], 'boolean'],
            'activeDefault' => [['active'], 'default', 'value' => 1],
            'name' => [['name'], 'string', 'max' => 255],
            'iBlockId' => [['iBlockId'], 'integer'],
            'self' => [['self'], 'boolean'],
            'selfDefault' => [['self'], 'default', 'value' => 1],
            'random' => [['random'], 'boolean'],
            'randomDefault' => [['random'], 'default', 'value' => 1],
            'quantity' => [['quantity'], 'integer'],
            'sort' => [['sort'], 'integer'],
            'sortDefault' => [['sort'], 'default', 'value' => 500],
            'unique' => [['code'], 'unique', 'targetAttribute' => ['code']],
            'required' => [['code', 'active', 'name', 'self', 'random', 'quantity', 'sort'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Loc::getMessage('intec.seo.models.autofill.template.attributes.id'),
            'code' => Loc::getMessage('intec.seo.models.autofill.template.attributes.code'),
            'active' => Loc::getMessage('intec.seo.models.autofill.template.attributes.active'),
            'name' => Loc::getMessage('intec.seo.models.autofill.template.attributes.name'),
            'iBlockId' => Loc::getMessage('intec.seo.models.autofill.template.attributes.iBlockId'),
            'self' => Loc::getMessage('intec.seo.models.autofill.template.attributes.self'),
            'random' => Loc::getMessage('intec.seo.models.autofill.template.attributes.random'),
            'quantity' => Loc::getMessage('intec.seo.models.autofill.template.attributes.quantity'),
            'sort' => Loc::getMessage('intec.seo.models.autofill.template.attributes.sort')
        ];
    }

    /**
     * Реляция. Возвращает привязанные разделы.
     * @param boolean $result Возвращать результат.
     * @param boolean $collection Возвращать как коллекцию.
     * @return Section[]|ActiveRecords|ActiveQuery|null
     */
    public function getSections($result = false, $collection = true)
    {
        return $this->relation(
            'sections',
            $this->hasMany(Section::className(), ['templateId' => 'id']),
            $result,
            $collection
        );
    }

    /**
     * Реляция. Возвращает привязанные разделы.
     * @param boolean $result Возвращать результат.
     * @param boolean $collection Возвращать как коллекцию.
     * @return Section[]|ActiveRecords|ActiveQuery|null
     */
    public function getFillingSections($result = false, $collection = true)
    {
        return $this->relation(
            'fillingSections',
            $this->hasMany(FillingSection::className(), ['templateId' => 'id']),
            $result,
            $collection
        );
    }

    /**
     * Реляция. Возвращает привязанные разделы.
     * @param boolean $result Возвращать результат.
     * @param boolean $collection Возвращать как коллекцию.
     * @return Section[]|ActiveRecords|ActiveQuery|null
     */
    public function getElements($result = false, $collection = true)
    {
        return $this->relation(
            'elements',
            $this->hasMany(Element::className(), ['templateId' => 'id']),
            $result,
            $collection
        );
    }


        /**
     * Реляция. Возвращает привязанные сайты.
     * @param boolean $result Возвращать результат.
     * @param boolean $collection Возвращать как коллекцию.
     * @return Site[]|ActiveRecords|ActiveQuery|null
     */
    public function getSites($result = false, $collection = true)
    {
        return $this->relation(
            'sites',
            $this->hasMany(Site::className(), ['templateId' => 'id']),
            $result,
            $collection
        );
    }
}