<?php
namespace intec\seo\models\filter;

use Bitrix\Main\Web\HttpClient;
use DateTime;
use DateTimeZone;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\core\db\ActiveRecords;
use intec\core\helpers\Html;
use intec\core\helpers\Type;
use intec\seo\models\filter\url\Scan;

Loc::loadMessages(__FILE__);

/**
 * Модель ссылки фильтра.
 * Class Url
 * @property integer $id Идентификатор.
 * @property integer $conditionId Условие.
 * @property integer $active Активность.
 * @property string $name Наименование.
 * @property string $source Исходный адрес.
 * @property string $target Целевой адрес.
 * @property integer $iBlockId Инфоблок.
 * @property integer $iBlockSectionId Раздел инфоблока.
 * @property integer $iBlockElementsCount Количество элементов.
 * @property string $dateCreate Дата создания.
 * @property string $dateChange Дата изменения.
 * @property integer $mapping В карте сайта.
 * @property string $metaTitle Заголовок meta.
 * @property string $metaKeywords Ключевые слова meta.
 * @property string $metaDescription Описание meta.
 * @property string $metaPageTitle Заголовок страницы.
 * @property string $metaBreadcrumbName Наименование в хлебных крошках.
 * @property integer $sort Сортировка.
 * @package intec\seo\models\filter
 * @author apocalypsisdimon@gmail.com
 */
class Url extends ActiveRecord
{
    protected static $_current;

    /**
     * @var array $cache
     */
    protected static $cache = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seo_filter_url';
    }

    /**
     * Устанавливает текущий Url.
     * @param static $url
     */
    public static function setCurrent($url)
    {
        static::$_current = null;

        if ($url instanceof static)
            static::$_current = $url;
    }

    /**
     * Возвращает текущий Url.
     * @return static
     */
    public static function getCurrent()
    {
        return static::$_current;
    }

    const RESOLVE_MODE_SOURCE = 0b1;
    const RESOLVE_MODE_TARGET = 0b10;
    const RESOLVE_MODE_BOTH = self::RESOLVE_MODE_SOURCE | self::RESOLVE_MODE_TARGET;

    /**
     * Разрешает адрес по Url.
     * @param string $url Url.
     * @param integer $mode Режим разрешения.
     * @return static|null
     */
    public static function resolveByUrl($url = null, $mode = self::RESOLVE_MODE_BOTH)
    {
        $result = null;

        if ($url === null)
            $url = Core::$app->request->getUrl();

        $url = new \intec\core\net\Url($url);
        $url->setScheme(null);
        $url->setUser(null);
        $url->setPassword(null);
        $url->setHost(null);
        $url->setPort(null);
        $url->setFragment(null);

        if ($mode & self::RESOLVE_MODE_SOURCE) {
            $result = static::find()->where(['and',
                ['=', 'active', 1],
                ['or',
                    ['=', 'source', $url->getPathString()],
                    ['=', 'source', $url->build()]
                ]
            ])->one();
        }

        if (empty($result) && ($mode & self::RESOLVE_MODE_TARGET)) {
            $result = static::find()->where(['and',
                ['=', 'active', 1],
                ['or',
                    ['=', 'target', $url->getPathString()],
                    ['=', 'target', $url->build()]
                ]
            ])->one();
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $date = new DateTime('now', new DateTimeZone('UTC'));

            if (empty($this->dateCreate))
                $this->dateCreate = $date->format('Y-m-d H:i:s');

            $this->dateChange = $date->format('Y-m-d H:i:s');

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();

        $scans = $this->getScans(true);

        foreach ($scans as $scan)
            $scan->delete();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'conditionId' => [['conditionId'], 'integer'],
            'active' => [['active'], 'boolean'],
            'activeDefault' => [['active'], 'default', 'value' => 1],
            'name' => [['name'], 'string', 'max' => 255],
            'source' => [['source'], 'string'],
            'target' => [['target'], 'string'],
            'iBlockId' => [['iBlockId'], 'integer'],
            'iBlockSectionId' => [['iBlockSectionId'], 'integer'],
            'iBlockElementsCount' => [['iBlockElementsCount'], 'integer'],
            'dateCreate' => [['dateCreate'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            'dateChange' => [['dateChange'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            'mapping' => [['mapping'], 'boolean'],
            'mappingDefault' => [['mapping'], 'default', 'value' => 1],
            'metaTitle' => [['metaTitle'], 'string'],
            'metaKeywords' => [['metaKeywords'], 'string'],
            'metaDescription' => [['metaDescription'], 'string'],
            'metaPageTitle' => [['metaPageTitle'], 'string'],
            'metaBreadcrumbName' => [['metaBreadcrumbName'], 'string'],
            'sort' => [['sort'], 'integer'],
            'sortDefault' => [['sort'], 'default', 'value' => 500],
            'required' => [[
                'active',
                'name',
                'source',
                'target',
                'mapping',
                'sort'
            ], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Loc::getMessage('intec.seo.models.filter.url.attributes.id'),
            'conditionId' => Loc::getMessage('intec.seo.models.filter.url.attributes.conditionId'),
            'active' => Loc::getMessage('intec.seo.models.filter.url.attributes.active'),
            'name' => Loc::getMessage('intec.seo.models.filter.url.attributes.name'),
            'source' => Loc::getMessage('intec.seo.models.filter.url.attributes.source'),
            'target' => Loc::getMessage('intec.seo.models.filter.url.attributes.target'),
            'iBlockId' => Loc::getMessage('intec.seo.models.filter.url.attributes.iBlockId'),
            'iBlockSectionId' => Loc::getMessage('intec.seo.models.filter.url.attributes.iBlockSectionId'),
            'iBlockElementsCount' => Loc::getMessage('intec.seo.models.filter.url.attributes.iBlockElementsCount'),
            'dateCreate' => Loc::getMessage('intec.seo.models.filter.url.attributes.dateCreate'),
            'dateChange' => Loc::getMessage('intec.seo.models.filter.url.attributes.dateChange'),
            'mapping' => Loc::getMessage('intec.seo.models.filter.url.attributes.mapping'),
            'metaTitle' => Loc::getMessage('intec.seo.models.filter.url.attributes.metaTitle'),
            'metaKeywords' => Loc::getMessage('intec.seo.models.filter.url.attributes.metaKeywords'),
            'metaDescription' => Loc::getMessage('intec.seo.models.filter.url.attributes.metaDescription'),
            'metaPageTitle' => Loc::getMessage('intec.seo.models.filter.url.attributes.metaPageTitle'),
            'metaBreadcrumbName' => Loc::getMessage('intec.seo.models.filter.url.attributes.metaBreadcrumbName'),
            'sort' => Loc::getMessage('intec.seo.models.filter.url.attributes.sort')
        ];
    }

    /**
     * Возвращает результат сканирования.
     * @param string|null $scheme Схема.
     * @param string|null $domain Домен.
     * @return Scan|null
     */
    public function scan($scheme = null, $host = null)
    {
        if ($this->getIsNewRecord())
            return null;

        if ($scheme === null)
            $scheme = Core::$app->request->getIsSecureConnection() ? 'https' : 'http';

        if ($host === null)
            $host = Core::$app->request->getServerName();

        $url = new \intec\core\net\Url();
        $url->setScheme($scheme);
        $url->setHost($host);
        $url->setPathString($this->source);
        $url = $url->build();

        $scan = new Scan();
        $scan->urlId = $this->id;
        $scan->date = (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s');

        $client = new HttpClient();
        $client->setRedirect(true, 1);

        if ($client->get($url)) {
            $scan->status = $client->getStatus();
            $content = $client->getResult();
            $matches = [];

            if (preg_match('/<title>(.*?)<\/title>/is', $content, $matches))
                $scan->metaTitle = Html::decode($matches[1]);

            if (preg_match('/<meta\s+name="keywords"\s+content="([^"]+)"/is', $content, $matches))
                $scan->metaKeywords = Html::decode($matches[1]);

            if (preg_match('/<meta\s+name="description"\s+content="([^"]+)"/is', $content, $matches))
                $scan->metaDescription = Html::decode($matches[1]);

            if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $content, $matches))
                $scan->metaPageTitle = Html::decode($matches[1]);
        }

        return $scan;
    }

    /**
     * Возвращает список связанных атрибутов.
     * @return array
     */
    public function relatedAttributes()
    {
        return [
            'metaTitle',
            'metaKeywords',
            'metaDescription',
            'metaPageTitle',
            'metaBreadcrumbName'
        ];
    }

    /**
     * Проверяет существование связанного атрибута.
     * @param string $name
     * @return boolean
     */
    public function hasRelatedAttribute($name)
    {
        return $this->hasAttribute($name) && in_array($name, $this->relatedAttributes(), true);
    }

    /**
     * Возвращает значение связанного с условием атрибута.
     * @param string $name Название атрибута.
     * @return mixed|null
     */
    public function getRelatedAttribute($name)
    {
        $value = null;

        if (!$this->hasRelatedAttribute($name))
            return $value;

        $value = $this->getAttribute($name);

        if (empty($value) && !Type::isNumeric($value)) {
            $condition = $this->getCondition(true);

            if (!empty($condition))
                $value = $condition->getAttribute($name);
        }

        return $value;
    }

    /**
     * Возвращает значение заголовка meta.
     * @param boolean $related Связанный.
     * @return string|null
     */
    public function getMetaTitle($related = true)
    {
        if ($related)
            return $this->getRelatedAttribute('metaTitle');

        return $this->getAttribute('metaTitle');
    }

    /**
     * Возвращает значение ключевых слов meta.
     * @param boolean $related Связанный.
     * @return string|null
     */
    public function getMetaKeywords($related = true)
    {
        if ($related)
            return $this->getRelatedAttribute('metaKeywords');

        return $this->getAttribute('metaKeywords');
    }

    /**
     * Возвращает значение описания meta.
     * @param boolean $related Связанный.
     * @return string|null
     */
    public function getMetaDescription($related = true)
    {
        if ($related)
            return $this->getRelatedAttribute('metaDescription');

        return $this->getAttribute('metaDescription');
    }

    /**
     * Возвращает значение заголовок сайта.
     * @param boolean $related Связанный.
     * @return string|null
     */
    public function getMetaPageTitle($related = true)
    {
        if ($related)
            return $this->getRelatedAttribute('metaPageTitle');

        return $this->getAttribute('metaPageTitle');
    }

    /**
     * Возвращает значение наименования в хлебных крошках.
     * @param boolean $related Связанный.
     * @return string|null
     */
    public function getMetaBreadcrumbName($related = true)
    {
        if ($related)
            return $this->getRelatedAttribute('metaBreadcrumbName');

        return $this->getAttribute('metaBreadcrumbName');
    }

    /**
     * Реляция. Возвращает условие.
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

    /**
     * Реляция. Возвращает результаты сканирования.
     * @param boolean $result Возвращать результат.
     * @param boolean $collection Возвращать как коллекцию.
     * @return Scan[]|ActiveRecords|ActiveQuery|null
     */
    public function getScans($result = false, $collection = true)
    {
        return $this->relation(
            'scans',
            $this->hasMany(Scan::className(), ['urlId' => 'id']),
            $result,
            $collection
        );
    }
}