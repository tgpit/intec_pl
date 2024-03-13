<?php
namespace intec\regionality\models;

use CSite;
use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

Loc::loadMessages(__FILE__);

/**
 * Модель сайта.
 * Class SiteSettings
 * @property string $siteId Идентификатор.
 * @property integer $regionId Идентификатор региона сайта по умолчанию.
 * @property integer $regionLocationResolve Определять местоположение в соотвествии с регионом.
 * @property integer $regionRememberTime Время хранения региона в Cookie.
 * @property integer $regionResolveOrder Порядок определения региона.
 * @property integer $regionResolveIgnoreUse Использовать игнорирование определения региона.
 * @property string $regionResolveIgnoreUserAgents Игнорирование определения региона для следующих UserAgent'ов.
 * @property integer $domain Домен сайта по умолчанию.
 * @property integer $domainsUse Использовать несколько доменов.
 * @property integer $domainsLinkingUse Использовать привязку региона к домену.
 * @property integer $domainsLinkingReset Сбрасывать установленный регион при смене региона из-за домена.
 * @property integer $domainsRedirectUse Использовать перенаправление на домен по умолчанию.
 * @package intec\regionality\models
 * @author apocalypsisdimon@gmail.com
 */
class SiteSettings extends ActiveRecord
{
    /**
     * Порядок определения домена: Сначало по домену.
     */
    const REGION_RESOLVE_ORDER_DOMAIN = 0;
    /**
     * Порядок определения домена: Сначало по IP адресу.
     */
    const REGION_RESOLVE_ORDER_ADDRESS = 1;

    /**
     * UserAgent'ы, которые игнорируются по умолчанию.
     */
    const REGION_RESOLVE_IGNORE_USER_AGENTS = [
        'Googlebot',
        'Slurp',
        'Yahoo! Slurp',
        'MSNBot',
        'Teoma',
        'Scooter',
        'ia_archiver',
        'Lycos',
        'Yandex',
        'StackRambler',
        'Mail.Ru',
        'Aport',
        'WebAlta'
    ];

    /**
     * Возвращает порядок определения региона.
     * @return array
     */
    public static function getRegionResolveOrders()
    {
        return [
            static::REGION_RESOLVE_ORDER_DOMAIN => Loc::getMessage('intec.regionality.models.siteSettings.regionResolveOrders.domain'),
            static::REGION_RESOLVE_ORDER_ADDRESS => Loc::getMessage('intec.regionality.models.siteSettings.regionResolveOrders.address')
        ];
    }

    /**
     * Возвращает значения порядка определения региона.
     * @return array
     */
    public static function getRegionResolveOrdersValues()
    {
        $values = static::getRegionResolveOrders();
        return ArrayHelper::getKeys($values);
    }

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
        return 'regionality_sites_settings';
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
            $settings->validate(['regionResolveIgnoreUserAgents']);
            $settings->siteId = $site;
        }

        return $settings;
    }

    /**
     * Возвращает текущий сайт.
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
            'siteId' => Loc::getMessage('intec.regionality.models.siteSettings.attributes.siteId'),
            'regionId' => Loc::getMessage('intec.regionality.models.siteSettings.attributes.regionId'),
            'regionLocationResolve' => Loc::getMessage('intec.regionality.models.siteSettings.attributes.regionLocationResolve'),
            'regionRememberTime' => Loc::getMessage('intec.regionality.models.siteSettings.attributes.regionRememberTime'),
            'regionResolveOrder' => Loc::getMessage('intec.regionality.models.siteSettings.attributes.regionResolveOrder'),
            'regionResolveIgnoreUse' => Loc::getMessage('intec.regionality.models.siteSettings.attributes.regionResolveIgnoreUse'),
            'regionResolveIgnoreUserAgents' => Loc::getMessage('intec.regionality.models.siteSettings.attributes.regionResolveIgnoreUserAgents'),
            'domain' => Loc::getMessage('intec.regionality.models.siteSettings.attributes.domain'),
            'domainsUse' => Loc::getMessage('intec.regionality.models.siteSettings.attributes.domainsUse'),
            'domainsLinkingUse' => Loc::getMessage('intec.regionality.models.siteSettings.attributes.domainsLinkingUse'),
            'domainsLinkingReset' => Loc::getMessage('intec.regionality.models.siteSettings.attributes.domainsLinkingReset'),
            'domainsRedirectUse' => Loc::getMessage('intec.regionality.models.siteSettings.attributes.domainsRedirectUse')
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $regionResolveOrders = static::getRegionResolveOrdersValues();
        $regionResolveIgnoreUserAgents = implode("\r\n", self::REGION_RESOLVE_IGNORE_USER_AGENTS);

        return [
            'siteId' => [['siteId'], 'string', 'length' => 2],
            'regionId' => [['regionId'], 'integer'],
            'regionLocationResolve' => [['regionLocationResolve'], 'boolean'],
            'regionLocationResolveDefault' => [['regionLocationResolve'], 'default', 'value' => 1],
            'regionRememberTime' => [['regionRememberTime'], 'integer', 'min' => 0],
            'regionRememberTimeDefault' => [['regionRememberTime'], 'default', 'value' => 3600],
            'regionResolveOrder' => [['regionResolveOrder'], 'integer'],
            'regionResolveOrderDefault' => [['regionResolveOrder'], 'default', 'value' => static::REGION_RESOLVE_ORDER_DOMAIN],
            'regionResolveOrderIn' => [['regionResolveOrder'], 'in', 'range' => $regionResolveOrders],
            'regionResolveIgnoreUse' => [['regionResolveIgnoreUse'], 'boolean'],
            'regionResolveIgnoreUserAgents' => [['regionResolveIgnoreUserAgents'], 'string'],
            'regionResolveIgnoreUserAgentsDefault' => [['regionResolveIgnoreUserAgents'], 'default', 'value' => $regionResolveIgnoreUserAgents],
            'domain' => [['domain'], 'string', 'max' => 255],
            'domainsUse' => [['domainsUse'], 'boolean'],
            'domainsUseDefault' => [['domainsUse'], 'default', 'value' => 0],
            'domainsLinkingUse' => [['domainsLinkingUse'], 'boolean'],
            'domainsLinkingUseDefault' => [['domainsLinkingUse'], 'default', 'value' => 1],
            'domainsLinkingReset' => [['domainsLinkingReset'], 'boolean'],
            'domainsLinkingResetDefault' => [['domainsLinkingReset'], 'default', 'value' => 0],
            'domainsRedirectUse' => [['domainsRedirectUse'], 'boolean'],
            'domainsRedirectUseDefault' => [['domainsRedirectUse'], 'default', 'value' => 1],
            'required' => [[
                'siteId',
                'regionLocationResolve',
                'regionRememberTime',
                'regionResolveOrder',
                'domainsUse',
                'domainsLinkingUse',
                'domainsLinkingReset',
                'domainsRedirectUse'
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
     * Возвращает список UserAgent'ов, которые игнорируются при определении региона.
     * @return array
     */
    public function getRegionResolveIgnoreUserAgents()
    {
        return $this->getStringAttributeAsArray('regionResolveIgnoreUserAgents');
    }

    /**
     * Получает домен для сайта.
     * @param boolean $strict Получить домен в любом случае.
     * @return string|null
     */
    public function getDomain($strict = false)
    {
        $context = Context::getCurrent();
        $server = $context->getServer();
        $domain = null;

        if (!empty($this->domain))
            $domain = $this->domain;

        $site = CSite::GetByID($this->siteId)->Fetch();

        if (empty($domain) && !empty($site)) {
            /** Если домен не определен, берем домен из настроек сайта для сервера */
            if (empty($domain) && !empty($site['SERVER_NAME']))
                $domain = $site['SERVER_NAME'];

            /** Если домен не определен, производим поиск доменов в настройках сайта */
            if (empty($domain) && !empty($site['DOMAINS'])) {
                $domains = StringHelper::replace($site['DOMAINS'], ["\r" => '']);
                $domains = explode("\n", $domains);

                foreach ($domains as $domain) {
                    if (!empty($domain))
                        break;

                    $domain = null;
                }
            }
        }

        if (empty($domain) && $strict)
            $domain = $server->getServerName();

        return $domain;
    }

    /**
     * Реляция. Возвращает домен сайта.
     * @param boolean $result Возвращать результат.
     * @return ActiveRecord|Region|ActiveQuery
     */
    public function getRegion($result = false)
    {
        return $this->relation(
            'region',
            $this->hasOne(Region::className(), ['id' => 'regionId']),
            $result
        );
    }

    /**
     * Реляция. Возвращает активные расширения сервиса определения региона по IP адресу.
     * @param boolean $result Возвращать результат.
     * @param boolean $collection Возвращать результат как коллекцию.
     * @return ActiveRecord|Region|ActiveQuery
     */
    public function getLocatorExtensions($result = false, $collection = true)
    {
        return $this->relation(
            'locatorExtensions',
            $this->hasMany(SiteSettingsLocatorExtension::className(), ['siteId' => 'siteId']),
            $result,
            $collection
        );
    }
}