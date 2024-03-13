<?php
namespace intec\seo\models\articles;

use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\core\db\ActiveRecords;
use intec\seo\models\articles\template\Article;
use intec\seo\models\articles\template\Element;
use intec\seo\models\articles\template\Section;
use intec\seo\models\articles\template\SectionsForElements;
use intec\seo\models\articles\template\Site;

Loc::loadMessages(__FILE__);

/**
 * Модель шаблона наименований элементов.
 * Class Condition
 * @property integer $id Идентификатор.
 * @property string $code Код.
 * @property integer $active Активность.
 * @property string $name Наименование.
 * @property integer $iBlockId Инфоблок.
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
        return 'seo_articles_templates';
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
            'sort' => [['sort'], 'integer'],
            'sortDefault' => [['sort'], 'default', 'value' => 500],
            'unique' => [['code'], 'unique', 'targetAttribute' => ['code']],
            'required' => [['code', 'active', 'name', 'sort'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Loc::getMessage('intec.seo.models.articles.template.attributes.id'),
            'code' => Loc::getMessage('intec.seo.models.articles.template.attributes.code'),
            'active' => Loc::getMessage('intec.seo.models.articles.template.attributes.active'),
            'name' => Loc::getMessage('intec.seo.models.articles.template.attributes.name'),
            'iBlockId' => Loc::getMessage('intec.seo.models.articles.template.attributes.iBlockId'),
            'sort' => Loc::getMessage('intec.seo.models.articles.template.attributes.sort')
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
    public function getSectionsForElements($result = false, $collection = true)
    {
        return $this->relation(
            'sectionsForElements',
            $this->hasMany(SectionsForElements::className(), ['templateId' => 'id']),
            $result,
            $collection
        );
    }


    /**
     * Реляция. Возвращает привязанные элементы.
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
     * Реляция. Возвращает привязанные статьи.
     * @param boolean $result Возвращать результат.
     * @param boolean $collection Возвращать как коллекцию.
     * @return Section[]|ActiveRecords|ActiveQuery|null
     */
    public function getArticles($result = false, $collection = true)
    {
        return $this->relation(
            'articles',
            $this->hasMany(Article::className(), ['templateId' => 'id']),
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