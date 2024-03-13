<?php
namespace intec\seo\models;

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use intec\core\db\ActiveRecord;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

Loc::loadMessages(__FILE__);

/**
 * Модель настроек сайта.
 * Class SiteSettings
 * @property string $siteId Идентификатор.
 * @property integer $filterIndexingDisabled Отключить индексацию на всех страницах фильтра.
 * @property integer $filterPaginationPart Часть адреса постраничной навигации фильтра.
 * @property integer $filterPaginationText Текст постраничной навигации фильтра.
 * @property integer $filterCanonicalUse Использовать канонический Url в фильтре.
 * @property integer $filterUrlQueryClean Очищать параметры запроса в Url, относящиеся к фильтру.
 * @property integer $filterVisitsEnabled Регистрировать визиты пользователей на страницах фильтра.
 * @property integer $filterVisitsReferrers Регистрировать только визиты пользователей, пришедших со следующих сайтов.
 * @property string $filterPages Страницы с фильтром.
 * @property integer $filterClearRedirectUse Использовать перенаправление на стандартную страницу при очистке фильтра.
 * @package intec\seo\models
 * @author apocalypsisdimon@gmail.com
 */
class SiteSettings extends ActiveRecord
{
    /**
     * @var array $cache
     */
    protected static $cache = [];

    /**
     * @var SiteSettings|null|false
     */
    protected static $_current = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seo_sites_settings';
    }

    /**
     * Возвращает настройки для указанного сайта.
     * @param string|null $site Сайт.
     * @return SiteSettings|null
     */
    public static function get($site = null)
    {
        if (empty($site))
            $site = Context::getCurrent()->getSite();

        if (empty($site))
            return null;

        $settings = static::findOne($site);

        if (empty($settings)) {
            $settings = new SiteSettings();
            $settings->loadDefaultValues();
            $settings->validate(['filterVisitsReferrers']);
            $settings->siteId = $site;
        }

        return $settings;
    }

    /**
     * Возвращает настройки текущего сайта.
     * @param boolean $reset Сбросить кеш.
     * @return SiteSettings|null
     */
    public static function getCurrent($reset = false)
    {
        if (static::$_current === false || $reset)
            static::$_current = static::get();

        return static::$_current;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'siteId' => Loc::getMessage('intec.seo.models.siteSettings.attributes.siteId'),
            'filterIndexingDisabled' => Loc::getMessage('intec.seo.models.siteSettings.attributes.filterIndexingDisabled'),
            'filterPaginationPart' => Loc::getMessage('intec.seo.models.siteSettings.attributes.filterPaginationPart'),
            'filterPaginationText' => Loc::getMessage('intec.seo.models.siteSettings.attributes.filterPaginationText'),
            'filterCanonicalUse' => Loc::getMessage('intec.seo.models.siteSettings.attributes.filterCanonicalUse'),
            'filterUrlQueryClean' => Loc::getMessage('intec.seo.models.siteSettings.attributes.filterUrlQueryClean'),
            'filterVisitsEnabled' => Loc::getMessage('intec.seo.models.siteSettings.attributes.filterVisitsEnabled'),
            'filterVisitsReferrers' => Loc::getMessage('intec.seo.models.siteSettings.attributes.filterVisitsReferrers'),
            'filterPages' => Loc::getMessage('intec.seo.models.siteSettings.attributes.filterPages'),
            'filterClearRedirectUse' => Loc::getMessage('intec.seo.models.siteSettings.attributes.filterClearRedirectUse')
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'siteId' => [['siteId'], 'string', 'length' => 2],
            'filterIndexingDisabled' => [['filterIndexingDisabled'], 'boolean'],
            'filterIndexingDisabledDefault' => [['filterIndexingDisabled'], 'default', 'value' => 0],
            'filterPaginationPart' => [['filterPaginationPart'], 'string', 'max' => 255],
            'filterPaginationText' => [['filterPaginationText'], 'string', 'max' => 255],
            'filterCanonicalUse' => [['filterCanonicalUse'], 'boolean'],
            'filterCanonicalUseDefault' => [['filterCanonicalUse'], 'default', 'value' => 1],
            'filterUrlQueryClean' => [['filterUrlQueryClean'], 'boolean'],
            'filterUrlQueryCleanDefault' => [['filterUrlQueryClean'], 'default', 'value' => 1],
            'filterVisitsEnabled' => [['filterVisitsEnabled'], 'boolean'],
            'filterVisitsEnabledDefault' => [['filterVisitsEnabled'], 'default', 'value' => 1],
            'filterVisitsReferrers' => [['filterVisitsReferrers'], 'string'],
            'filterVisitsReferrersDefault' => [['filterVisitsReferrers'], 'default', 'value' => 'yandex.ru'."\r\n".'google.ru'."\r\n".'google.com'."\r\n".'www.yahoo.com'."\r\n".'www.rambler.ru'],
            'filterPages' => [['filterPages'], 'string'],
            'filterClearRedirectUse' => [['filterClearRedirectUse'], 'boolean'],
            'filterClearRedirectUseDefault' => [['filterClearRedirectUse'], 'default', 'value' => 1],
            'required' => [[
                'siteId',
                'filterIndexingDisabled',
                'filterCanonicalUse',
                'filterUrlQueryClean',
                'filterVisitsEnabled',
                'filterClearRedirectUse'
            ], 'required']
        ];
    }

    /**
     * Возвращает строковое значение атрибута в виде массива, где разделитель строк - перенос строки.
     * @param string $name Имя атрибута.
     * @param boolean $trim Применять trim к строке.
     * @return array
     */
    protected function getStringAttributeAsArray($name, $trim = true)
    {
        $result = [];

        $values = $this->getAttribute($name);
        $values = StringHelper::replace($values, [
            "\r" => ''
        ]);

        $values = explode("\n", $values);

        foreach ($values as $value) {
            if ($trim)
                $value = trim($value);

            if (!empty($value) || Type::isNumeric($value))
                $result[] = $value;
        }

        return $result;
    }

    /**
     * Возвращает реферальные сайты визитов в виде массива.
     * @return array
     */
    public function getFilterVisitsReferrers()
    {
        return $this->getStringAttributeAsArray('filterVisitsReferrers');
    }

    /**
     * Возвращает страницы фильтра в виде массива.
     * @return array
     */
    public function getFilterPages()
    {
        return $this->getStringAttributeAsArray('filterPages');
    }
}