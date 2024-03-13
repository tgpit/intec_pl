<?php
namespace intec\seo\models\filter;

use DateTime;
use SimpleXMLElement;
use CSite;
use CIBlockElement;
use Bitrix\Main\Localization\Loc;
use intec\core\base\writers\ClosureWriter;
use intec\core\helpers\ArrayHelper;
use intec\seo\filter\condition\FilterHelper;
use intec\core\db\ActiveRecord;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Type;
use intec\core\io\Path;
use intec\core\net\Url;
use intec\seo\models\filter\Url as FilterUrl;

Loc::loadMessages(__FILE__);

/** TODO: Добавить поддержку выбора условий для генерации карты сайта */

/**
 * Модель карты сайта.
 * Class Sitemap
 * @property integer $id Идентификатор.
 * @property string $siteId Сайт.
 * @property string $active Активность.
 * @property string $name Наименование.
 * @property string $scheme Схема.
 * @property string $domain Домен.
 * @property string $sourceFile Исходный файл.
 * @property string $targetFile Результирующий файл.
 * @property string $configured Работа только с настроенными адресами.
 * @property integer $sort Сортировка.
 * @package intec\seo\models\filter
 * @author apocalypsisdimon@gmail.com
 */
class Sitemap extends ActiveRecord
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
        return 'seo_filter_sitemaps';
    }

    /**
     * Схема: http.
     */
    const SCHEME_HTTP = 'http';
    /**
     * Схема: https.
     */
    const SCHEME_HTTPS = 'https';

    /**
     * Возвращает доступные схемы.
     * @return array
     */
    public static function getSchemes()
    {
        return [
            self::SCHEME_HTTP => 'http',
            self::SCHEME_HTTPS => 'https'
        ];
    }

    /**
     * Возвращает доступные значения схемы.
     * @return array
     */
    public static function getSchemesValues()
    {
        $values = static::getSchemes();
        $values = ArrayHelper::getKeys($values);

        return $values;
    }

    /**
     * Кешированный сайт.
     * @var boolean
     */
    protected $_site = false;

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            /** Запрет на изменение сайта привязки */
            if (!$this->getIsNewRecord())
                $this->setAttribute('siteId', $this->getOldAttribute('siteId'));

            /** Если целевой файл был изменен */
            if ($this->isAttributeChanged('targetFile')) {
                $pathOld = $this->getTargetFile(false, true);
                $pathNew = $this->getTargetFile();

                if (!empty($pathOld) && FileHelper::isFile($pathOld)) {
                    if (!empty($pathNew))
                        FileHelper::setFileData($pathNew, FileHelper::getFileData($pathOld));

                    unlink($pathOld);
                }
            }

            /** Если 1 из атрибутов изменен, нужно обновление активности */
            if (
                $this->isAttributeChanged('scheme') ||
                $this->isAttributeChanged('domain') ||
                $this->isAttributeChanged('sourceFile') ||
                $this->isAttributeChanged('targetFile')
            ) {
                /** Перенос активности в новый файл карты сайта если это возможно */
                if ($this->getActive(true)) {
                    $this->setActive(false, true);
                    $this->setActive(true);
                }
            }

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

        $this->setActive(false);
        $this->setActive(false, true);
        $this->removeFile();
        $this->removeFile(true);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $schemes = static::getSchemesValues();

        return [
            'siteId' => [['siteId'], 'string', 'length' => 2],
            'name' => [['name'], 'string', 'max' => 255],
            'scheme' => [['scheme'], 'string', 'max' => 255],
            'schemeRange' => [['scheme'], 'in', 'range' => $schemes],
            'schemeDefault' => [['scheme'], 'default', 'value' => self::SCHEME_HTTP],
            'domain' => [['domain'], 'string', 'max' => 255],
            'sourceFile' => [['sourceFile'], 'string', 'max' => 255],
            'sourceFileDefault' => [['sourceFile'], 'default', 'value' => 'sitemap.xml'],
            'targetFile' => [['targetFile'], 'string', 'max' => 255],
            'targetFileDefault' => [['targetFile'], 'default', 'value' => 'sitemap_seo.xml'],
            'configured' => [['configured'], 'boolean'],
            'configuredDefault' => [['configured'], 'default', 'value' => 1],
            'sort' => [['sort'], 'integer'],
            'sortDefault' => [['sort'], 'default', 'value' => 500],
            'required' => [[
                'siteId',
                'name',
                'scheme',
                'domain',
                'sourceFile',
                'targetFile',
                'configured',
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
            'id' => Loc::getMessage('intec.seo.models.filter.sitemap.attributes.id'),
            'siteId' => Loc::getMessage('intec.seo.models.filter.sitemap.attributes.siteId'),
            'name' => Loc::getMessage('intec.seo.models.filter.sitemap.attributes.name'),
            'scheme' => Loc::getMessage('intec.seo.models.filter.sitemap.attributes.scheme'),
            'domain' => Loc::getMessage('intec.seo.models.filter.sitemap.attributes.domain'),
            'sourceFile' => Loc::getMessage('intec.seo.models.filter.sitemap.attributes.sourceFile'),
            'targetFile' => Loc::getMessage('intec.seo.models.filter.sitemap.attributes.targetFile'),
            'configured' => Loc::getMessage('intec.seo.models.filter.sitemap.attributes.configured'),
            'sort' => Loc::getMessage('intec.seo.models.filter.sitemap.attributes.sort')
        ];
    }

    /**
     * Возвращает сайт к которой привязана карта сайта.
     * @param boolean $reset Сбросить кеш.
     * @return array|null
     */
    public function getSite($reset = false)
    {
        if (empty($this->siteId))
            return null;

        if ($this->_site === false || $reset) {
            $this->_site = CSite::GetByID($this->siteId)->Fetch();

            if (empty($this->_site))
                $this->_site = null;
        }

        return $this->_site;
    }

    /**
     * Устанавливает активность.
     * @param boolean $value
     * @param boolean $old
     * @param boolean $reset
     * @return boolean
     */
    public function setActive($value, $old = false, $reset = false)
    {
        $value = Type::toBoolean($value);
        $path = $this->getSourceFile(false, $old, $reset);
        $url = $this->getTargetUrl($old);
        $content = FileHelper::getFileData($path);

        libxml_use_internal_errors(true);

        $xml = simplexml_load_string($content);

        if ($xml !== false) {
            $position = 0;
            $included = false;

            if ($xml->getName() !== 'sitemapindex')
                return false;

            foreach ($xml as $node) {
                if ($node->getName() !== 'sitemap')
                    continue;

                if ((string)$node->loc[0] === $url) {
                    $included = true;
                    break;
                }

                $position++;
            }

            if ($value) {
                if ($included) {
                    return true;
                } else {
                    $node = $xml->addChild('sitemap');
                    $node->addChild('loc', $url);
                    $node->addChild('lastmod', (new DateTime())->format('Y-m-d\TH:i:sP'));
                }
            } else {
                if ($included) {
                    unset($xml->sitemap[$position]);
                } else {
                    return true;
                }
            }

            FileHelper::setFileData($path, $xml->asXML());

            return true;
        }

        return false;
    }

    /**
     * Возвращает активность.
     * @param boolean $old
     * @param boolean $reset
     * @return boolean
     */
    public function getActive($old = false, $reset = false)
    {
        $path = $this->getSourceFile(false, $old, $reset);
        $url = $this->getTargetUrl($old);

        if (empty($path) || empty($url) || !FileHelper::isFile($path))
            return false;

        $content = FileHelper::getFileData($path);

        libxml_use_internal_errors(true);

        $xml = simplexml_load_string($content);

        if ($xml !== false) {
            if ($xml->getName() !== 'sitemapindex')
                return false;

            foreach ($xml as $node)
                if ($node->getName() === 'sitemap')
                    if ((string)$node->loc[0] === $url)
                        return true;
        }

        return false;
    }

    /**
     * Возвращает состояние существования файла.
     * @param boolean $old
     * @param boolean $reset
     * @return boolean
     */
    public function getIsFileExists($old = false, $reset = false)
    {
        $path = $this->getTargetFile(false, $old, $reset);

        if (empty($path))
            return false;

        return FileHelper::isFile($path);
    }

    /**
     * Возвращает путь до исходного файла.
     * @param boolean $relative
     * @param boolean $old
     * @param boolean $reset
     * @return null|string
     */
    public function getSourceFile($relative = false, $old = false, $reset = false)
    {
        $site = null;
        $path = null;

        if ($old) {
            $path = $this->getOldAttribute('sourceFile');
        } else {
            $path = $this->getAttribute('sourceFile');
        }

        if (empty($path) && !Type::isNumeric($path))
            return null;

        if ($relative)
            return Path::normalize($path, true);

        $site = $this->getSite($reset);

        if (empty($site))
            return null;

        return Path::normalize($site['ABS_DOC_ROOT'].'/'.$path);
    }

    /**
     * Возвращает Url адрес до исходного файла.
     * @param boolean $old
     * @return null|string
     */
    public function getSourceUrl($old = false)
    {
        $scheme = null;
        $domain = null;
        $path = null;

        if ($old) {
            $scheme = $this->getOldAttribute('scheme');
            $domain = $this->getOldAttribute('domain');
            $path = $this->getOldAttribute('sourceFile');
        } else {
            $scheme = $this->getAttribute('scheme');
            $domain = $this->getAttribute('domain');
            $path = $this->getAttribute('sourceFile');
        }

        if (empty($scheme) || empty($domain) || empty($path) && !Type::isNumeric($path))
            return null;

        $path = Path::normalize($path, true, '/');

        $url = new Url();
        $url->setScheme($scheme);
        $url->setHost($domain);
        $url->setPathString($path);

        return $url->build();
    }

    /**
     * Возвращает путь до целевого файла.
     * @param bool $relative
     * @param bool $old
     * @param bool $reset
     * @return null|string
     */
    public function getTargetFile($relative = false, $old = false, $reset = false)
    {
        $site = null;
        $path = null;

        if ($old) {
            $path = $this->getOldAttribute('targetFile');
        } else {
            $path = $this->getAttribute('targetFile');
        }

        if (empty($path) && !Type::isNumeric($path))
            return null;

        if ($relative)
            return Path::normalize($path, true);

        $site = $this->getSite($reset);

        if (empty($site))
            return null;

        return Path::normalize($site['ABS_DOC_ROOT'].'/'.$path);
    }

    /**
     * Возвращает Url адрес до целевого файла.
     * @param boolean $old
     * @return null|string
     */
    public function getTargetUrl($old = false)
    {
        $scheme = null;
        $domain = null;
        $path = null;

        if ($old) {
            $scheme = $this->getOldAttribute('scheme');
            $domain = $this->getOldAttribute('domain');
            $path = $this->getOldAttribute('targetFile');
        } else {
            $scheme = $this->getAttribute('scheme');
            $domain = $this->getAttribute('domain');
            $path = $this->getAttribute('targetFile');
        }

        if (empty($scheme) || empty($domain) || empty($path) && !Type::isNumeric($path))
            return null;

        $path = Path::normalize($path, true, '/');

        $url = new Url();
        $url->setScheme($scheme);
        $url->setHost($domain);
        $url->setPathString($path);

        return $url->build();
    }

    /**
     * Удаляет файл карты сайта.
     * @param boolean $old
     * @param boolean $reset
     * @return boolean
     */
    public function removeFile($old = false, $reset = false)
    {
        $path = $this->getTargetFile(false ,$old, $reset);

        if (empty($path))
            return false;

        if (FileHelper::isFile($path))
            return unlink($path);

        return true;
    }

    /**
     * Генерирует файл карты сайта.
     * @param boolean $old
     * @param boolean $reset Сбросить кеш.
     * @return boolean
     */
    public function generateFile($old = false, $reset = false)
    {
        $path = $this->getTargetFile(false, $old, $reset);

        if (empty($path))
            return false;

        $content = $this->generateContent();

        if (empty($content))
            return false;

        FileHelper::setFileData($path, $content);

        return true;
    }

    /**
     * Генерирует контент карты сайта.
     * @return string|null
     */
    public function generateContent()
    {
        if (
            empty($this->scheme) ||
            empty($this->domain)
        ) return null;

        $data = $this->generateData();

        if (empty($data))
            return null;

        libxml_use_internal_errors(true);

        $date = new DateTime();
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

        foreach ($data as $item) {
            $url = new Url($item['url']);
            $url->setScheme($this->scheme);
            $url->setUser(null);
            $url->setPassword(null);
            $url->setHost($this->domain);
            $url->setPort(null);
            $url->setFragment(null);

            $node = $xml->addChild('url');
            $node->addChild('loc', $url->build());
            $node->addChild('lastmod', $date->format('Y-m-d\TH:i:sP'));
            $node->addChild('changefreq', $item['frequency']);
            $node->addChild('priority', $item['priority']);
        }

        return $xml->asXML();
    }

    /**
     * Генерирует данные для карты сайта.
     * @return array
     */
    public function generateData()
    {
        $result = [];

        if (empty($this->siteId))
            return $result;

        /** @var Condition[] $conditions */
        $conditions = Condition::find()
            ->where([
                'active' => 1
            ])->forSites([$this->siteId])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        /** @var Condition $condition */
        $condition = null;
        /** @var FilterUrl[] $urls */
        $urls = [];

        $writer = new ClosureWriter(function ($url, $combination, $iblock, $section) use (&$condition, &$urls) {
            /** @var FilterUrl $url */
            $filter = FilterHelper::getFilterFromCombination($combination, $iblock, $section);

            if (empty($filter))
                return false;

            $filter['ACTIVE'] = 'Y';
            $filter['ACTIVE_DATE'] = 'Y';

            if (isset($filter['ID'])) {
                $filter['ID']->arFilter['ACTIVE'] = 'Y';
                $filter['ID']->arFilter['ACTIVE_DATE'] = 'Y';
            }

            $filter['INCLUDE_SUBSECTIONS'] = $condition->recursive ? 'Y' : 'N';

            $count = CIBlockElement::GetList([
                'SORT' => 'ASC'
            ], $filter, false, [
                'nPageSize' => 1
            ]);

            $count = $count->SelectedRowsCount();

            if ($count < 1)
                return false;

            $url->populateRelation('condition', $condition);
            $urls[] = $url;

            return true;
        }, false);

        $conditionsId = [];

        foreach ($conditions as $condition) {
            $conditionsId[] = $condition->id;
            $condition->generateUrl(null, $writer);
        }

        $sources = [];

        /** Собираем существующие целевые адреса */
        foreach ($urls as $url)
            $sources[] = $url->source;

        if (!empty($sources)) {
            $sources = array_unique($sources);
            $sources = FilterUrl::find()->where([
                'active' => 1,
                'conditionId' => $conditionsId,
                'source' => $sources
            ])->indexBy('source')->all();

            foreach ($urls as $url) {
                /** @var FilterUrl $configured */
                $configured = $sources->get($url->source);
                $part = null;

                if (!empty($configured)) {
                    if ($configured->mapping)
                        $part = ['url' => $configured->target];
                } else if (!$this->configured) {
                    $part = ['url' => $url->source];
                }

                if (!empty($part)) {
                    $condition = $url->getCondition(true);
                    $part['frequency'] = $condition->frequency;
                    $part['priority'] = $condition->priority;
                    $result[] = $part;
                }
            }
        }

        return $result;
    }
}