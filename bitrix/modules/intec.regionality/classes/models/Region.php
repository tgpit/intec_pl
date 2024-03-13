<?php
namespace intec\regionality\models;

use CCatalogGroup;
use CCatalogStore;
use CStartShopPrice;
use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\core\db\ActiveRecords;
use intec\core\base\Collection;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\regionality\models\region\Store;
use intec\regionality\Module;
use intec\regionality\models\region\Domain;
use intec\regionality\models\region\PriceType;
use intec\regionality\models\region\Site;
use intec\regionality\models\region\Value;
use intec\regionality\services\locator\Service as Locator;
use intec\regionality\tools\Domain as DomainTools;

Loc::loadMessages(__FILE__);

/**
 * Модель региона.
 * Class Region
 * @property integer $id Идентификатор.
 * @property string $code Код.
 * @property integer $active Активность.
 * @property integer $default По умолчанию.
 * @property string $name Наименование.
 * @property string $description Описание.
 * @property integer $sort Сортировка.
 * @package intec\regionality\models
 * @author apocalypsisdimon@gmail.com
 */
class Region extends ActiveRecord
{
    /**
     * Код сущности.
     */
    const ENTITY = 'REGIONALITY_REGION';

    /**
     * Префикс.
     */
    const PREFIX = Module::VARIABLE.'_'.self::VARIABLE.'_';
    /**
     * Префикс свойства: Системный.
     */
    const PROPERTY_PREFIX_SYSTEM = 'UF_';
    /**
     * Префикс свойства: Макрос.
     */
    const PROPERTY_PREFIX_FIELD = 'PROPERTIES_';

    /**
     * Системная переменная региона.
     */
    const VARIABLE = 'REGION';

    /**
     * @var array $cache
     */
    protected static $cache = [];

    /**
     * @var Region|null|false
     */
    protected static $_default = false;

    /**
     * @var Region|null|false
     */
    protected static $_current = false;

    /**
     * @var Arrays
     */
    protected static $_properties;

    /**
     * @inheritdoc
     * @return RegionQuery
     */
    public static function find()
    {
        return Core::createObject(RegionQuery::className(), [get_called_class()]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'regionality_regions';
    }

    /**
     * Возвращает регион сессии.
     * @return Region|null
     */
    public static function getSessional()
    {
        if (empty($_SESSION[Module::VARIABLE]) || !Type::isArray($_SESSION[Module::VARIABLE]))
            return null;

        if (empty($_SESSION[Module::VARIABLE][static::VARIABLE]) || !Type::isArray($_SESSION[Module::VARIABLE][static::VARIABLE]))
            return null;

        if (empty($_SESSION[Module::VARIABLE][static::VARIABLE]['ID']))
            return null;

        return static::findOne($_SESSION[Module::VARIABLE][static::VARIABLE]['ID']);
    }

    /**
     * Устанавливает регион сесии.
     * @param Region|null $region
     */
    public static function setSessional($region)
    {
        static::$_current = false;

        $fields = null;

        if ($region instanceof static)
            $fields = $region->getFieldsStructure();

        if (!isset($_SESSION[Module::VARIABLE]) || !Type::isArray($_SESSION[Module::VARIABLE]))
            $_SESSION[Module::VARIABLE] = [];

        $_SESSION[Module::VARIABLE][self::VARIABLE] = $fields;
    }

    /**
     * Возвращает установленный регион, сессионный регион, регион по умолчанию или `null`.
     * @return Region|null
     */
    public static function getCurrent()
    {
        $current = static::$_current;

        if (empty($current))
            $current = static::getSessional();

        if (empty($current))
            $current = static::getDefault();

        return $current;
    }

    /**
     * Устанавливает текущий регион.
     * @param Region|null $region
     */
    public static function setCurrent($region)
    {
        if ($region instanceof static) {
            static::setSessional($region);
            static::$_current = $region;
        } else {
            static::$_current = null;
        }
    }

    /**
     * Проверяет, был ли установлен регион вручную.
     * @return boolean
     */
    public static function isCurrentSet()
    {
        return static::$_current !== false;
    }

    /**
     * Восстанавливает регион из Cookie для текущего сайта.
     * @return boolean
     */
    public static function restore()
    {
        $key = Module::VARIABLE.'_'.Region::VARIABLE.'_CURRENT';
        $region = static::getRemembered();

        if (empty($region))
            return false;

        if (isset($_COOKIE[$key]) && $_COOKIE[$key] === 'Y') {
            Region::setCurrent($region);
        } else {
            Region::setSessional($region);
        }

        return true;
    }

    /**
     * Производит действия с Cookie для текущего сайта.
     * @param Region|null $region
     * @param boolean $domainsUse
     * @param integer $time
     * @param boolean $reset
     * @return boolean
     */
    protected static function memory($region, $domainsUse = true, $time = 3600, $reset = false)
    {
        $site = Context::getCurrent()->getSite();
        $time = Type::toInteger($time);

        if (empty($site))
            return false;

        if ($time < 0)
            $time = 0;

        $domain = '';

        if ($domainsUse) {
            if (!empty($region)) {
                $domain = $region->resolveDomain($site, true);
            } else {
                $siteSettings = SiteSettings::get($site);
                $domain = $siteSettings->getDomain(true);
                $reset = true;
            }

            $domain = DomainTools::getRoot($domain);

            if (empty($domain))
                return false;

            $currentDomain = Core::$app->request->getHostName();
            $currentDomain = DomainTools::getRoot($currentDomain);

            if ($domain !== $currentDomain)
                return false;
        }

        setcookie(
            Module::VARIABLE.'_'.Region::VARIABLE.'_ID',
            !$reset ? $region->id : null,
            $time !== 0 ? time() + $time : 0,
            '/',
            $domain
        );

        setcookie(
            Module::VARIABLE.'_'.Region::VARIABLE.'_CURRENT',
            !$reset ? (Region::isCurrentSet() ? 'Y' : 'N') : null,
            $time !== 0 ? time() + $time : 0,
            '/',
            $domain
        );

        $_COOKIE[Module::VARIABLE.'_'.Region::VARIABLE.'_ID'] = !$reset ? $region->id : null;
        $_COOKIE[Module::VARIABLE.'_'.Region::VARIABLE.'_CURRENT'] = !$reset ? (Region::isCurrentSet() ? 'Y' : 'N') : null;

        return true;
    }

    /**
     * Возвращает хранимый в Cookie регион.
     * @return Region|null
     */
    public static function getRemembered()
    {
        $key = Module::VARIABLE.'_'.Region::VARIABLE.'_ID';
        $region = null;

        if (isset($_COOKIE[$key]))
            $region = $_COOKIE[$key];

        if (empty($region))
            return null;

        return Region::findOne($region);
    }

    /**
     * Запоминает регион в Cookie текущего сайта.
     * @param Region $region
     * @param boolean $domainsUse
     * @param integer $time
     * @return boolean
     */
    public static function remember($region, $domainsUse = true, $time = 3600)
    {
        static::reset($domainsUse);
        return static::memory($region, $domainsUse, $time, false);
    }

    /**
     * Сбрасывает регион в Cookie для текущего сайта.
     * @param boolean $domainsUse
     * @param integer $time
     */
    public static function reset($domainsUse = true, $time = 3600)
    {
        $region = static::getRemembered();

        if (!empty($region))
            static::memory($region, $domainsUse, $time, true);
    }

    /**
     * Возвращает регион по умолчанию.
     * @param string|null $site Текущий сайт.
     * @return Region|null
     */
    public static function getDefault($site = null)
    {
        if (empty($site))
            $site = Context::getCurrent()->getSite();

        if (empty($site))
            return null;

        /** @var SiteSettings $site */
        $siteSettings = SiteSettings::findOne($site);

        if (empty($siteSettings))
            return null;

        /** @var Region $region */
        $region = $siteSettings->getRegion(true);

        if (!empty($region) && $region->isForSites($site))
            return $region;

        return null;
    }

    /**
     * Возвращает поля региона.
     * Ключ в данном случае содержит код поля, а значение - наименование.
     * @param string|null|false $prefix Добавлять префикс.
     * @param boolean $withProperties Возвращать со свойствами.
     * @return array
     */
    public static function getFields($prefix = null, $withProperties = true)
    {
        if ($prefix === null) {
            $prefix = self::PREFIX;
        } else if ($prefix === false) {
            $prefix = null;
        }

        $result = [
            $prefix.'ID' => Loc::getMessage('intec.regionality.models.region.fields.ID'),
            $prefix.'CODE' => Loc::getMessage('intec.regionality.models.region.fields.CODE'),
            $prefix.'NAME' => Loc::getMessage('intec.regionality.models.region.fields.NAME'),
            $prefix.'DESCRIPTION' => Loc::getMessage('intec.regionality.models.region.fields.DESCRIPTION'),
            $prefix.'SORT' => Loc::getMessage('intec.regionality.models.region.fields.SORT')
        ];

        if ($withProperties) {
            $properties = static::getProperties();

            foreach ($properties as $property)
                $result[$prefix.self::PROPERTY_PREFIX_FIELD.$property['FIELD_CODE']] = $property['LABEL'];
        }

        return $result;
    }

    /**
     * Возвращает свойства региона.
     * @param boolean $reset Сбросить кеш.
     * @return Arrays
     */
    public static function getProperties($reset = false)
    {
        global $USER_FIELD_MANAGER;

        if (static::$_properties === null || $reset) {
            $properties = $USER_FIELD_MANAGER->GetUserFields(static::ENTITY, 0, LANGUAGE_ID);

            foreach ($properties as &$property) {
                $property['FIELD_CODE'] = StringHelper::cut(
                    $property['FIELD_NAME'],
                    StringHelper::length(self::PROPERTY_PREFIX_SYSTEM)
                );

                $property['LABEL'] = $property['FIELD_CODE'];

                if (!empty($property['EDIT_FORM_LABEL']))
                    $property['LABEL'] = $property['EDIT_FORM_LABEL'];
            }

            static::$_properties = Arrays::from($properties);
            static::$_properties->reindex();

            unset($property);
            unset($properties);
        }

        return static::$_properties;
    }

    /**
     * Разрешает регион подомену.
     * @param string|boolean|null $domain Домен.
     * @param string|boolean|null $site Сайт, к которому привязан домен.
     * @return Region|null
     */
    public static function resolveByDomain($domain = null, $site = null)
    {
        /** @var Region $result */
        $result = null;

        if ($domain === null || $domain === true)
            $domain = Core::$app->request->getHostName();

        if (empty($domain))
            return $result;

        if ($site === null || $site === true) {
            $site = Context::getCurrent()->getSite();
        } else if ($site === false) {
            $site = null;
        }

        /** @var Domain $domain */
        $domain = Domain::find()
            ->where([
                'active' => 1,
                'value' => $domain
            ]);

        if (!empty($site))
            $domain->andWhere(['siteId' => $site]);

        $domain = $domain->one();

        if (!empty($domain)) {
            $result = $domain->getRegion()
                ->with([
                    'domains',
                    'values'
                ])
                ->one();

            if (!empty($result) && $result->active) {
                return $result;
            } else {
                $result = null;
            }
        }

        return $result;
    }

    /**
     * Разрешает регион по IP адресу.
     * @param string|boolean|null $address IP адрес.
     * @param array|null $extensions Расширения сервиса.
     * @return Region|null
     */
    public static function resolveByAddress($address = null, $extensions = null)
    {
        /** @var Region $result */
        $result = null;

        if ($address === null || $address === true)
            $address = Core::$app->request->getUserIP();

        if (empty($address))
            return $result;

        $locator = Locator::getInstance();
        $result = $locator->resolve($address, $extensions);

        if (!empty($result)) {
            $result = static::find()
                ->where(['and',
                    ['=', 'active', 1],
                    ['or',
                        ['=', 'code', $result],
                        ['=', 'name', $result]
                    ]
                ])
                ->with([
                    'domains',
                    'values'
                ])
                ->one();

            if (!empty($result))
                return $result;
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->default == 1) {
                $this->active = 1;

                /** @var static[] $regions */
                $regions = static::find()->where([
                    'default' => 1
                ])->all();

                /** @var static $region */
                foreach ($regions as $region) {
                    if ($region->id == $this->id)
                        continue;

                    $region->default = 0;
                    $region->save();
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

        $domains = $this->getDomains(true);
        $pricesTypes = $this->getPricesTypes(true);
        $stores = $this->getStores(true);
        $sites = $this->getSites(true);
        $values = $this->getValues(true);

        foreach ($domains as $domain)
            $domain->delete();

        foreach ($pricesTypes as $priceType)
            $priceType->delete();

        foreach ($stores as $store)
            $store->delete();

        foreach ($sites as $site)
            $site->delete();

        foreach ($values as $value)
            $value->delete();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Loc::getMessage('intec.regionality.models.region.attributes.id'),
            'code' => Loc::getMessage('intec.regionality.models.region.attributes.code'),
            'active' => Loc::getMessage('intec.regionality.models.region.attributes.active'),
            'name' => Loc::getMessage('intec.regionality.models.region.attributes.name'),
            'description' => Loc::getMessage('intec.regionality.models.region.attributes.description'),
            'sort' => Loc::getMessage('intec.regionality.models.region.attributes.sort')
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'code' => [['code'], 'string', 'max' => 255],
            'codeMatch' => [['code'], 'match', 'pattern' => '/^[A-Za-z0-9_`\' -]*$/'],
            'active' => [['active'], 'boolean'],
            'activeDefault' => [['active'], 'default', 'value' => 1],
            'name' => [['name'], 'string', 'max' => 255],
            'description' => [['description'], 'string'],
            'sort' => [['sort'], 'integer'],
            'sortDefault' => [['sort'], 'default', 'value' => 500],
            'unique' => [['code'], 'unique', 'targetAttribute' => ['code']],
            'required' => [['code', 'active', 'name', 'sort'], 'required']
        ];
    }

    /**
     * Возвращает значения полей.
     * @param string|boolean|null $site Идентификатор сайта.
     * @param string|boolean|null $prefix Добавлять префикс.
     * @param boolean $withProperties Возвращать вместе со свойствами.
     * @param boolean $render Отрисовывать свойство.
     * @param boolean $raw Сохранять оригинальное значение свойства.
     * @return Collection
     */
    public function getFieldsValues($site = null, $prefix = null, $withProperties = true, $render = false, $raw = false)
    {
        if ($prefix === null || $prefix === true) {
            $prefix = self::PREFIX;
        } else if ($prefix === false) {
            $prefix = null;
        }

        $result = new Collection();
        $result->setRange([
            $prefix.'ID' => $this->id,
            $prefix.'CODE' => $this->code,
            $prefix.'NAME' => $this->name,
            $prefix.'DESCRIPTION' => $this->description,
            $prefix.'SORT' => $this->sort
        ]);

        if ($withProperties)
            $result->setRange($this->getPropertiesValues(
                $site,
                $prefix.self::PROPERTY_PREFIX_FIELD,
                $render,
                $raw
            ));

        return $result;
    }

    /**
     * Возвращает структурированный массив со значениями полей и свойствами.
     * @param string|boolean|null $site Идентификатор сайта.
     * @param boolean $withRelations Возвращать вместе с зависимостями.
     * @param boolean $withProperties Возвращать вместе со свойствами.
     * @return null|array
     */
    public function getFieldsStructure($site = null, $withRelations = true, $withProperties = true)
    {
        $result = $this
            ->getFieldsValues($site, false, false)
            ->asArray();

        if ($withRelations) {
            $result['PRICES'] = [
                'ID' => [],
                'CODE' => []
            ];

            $result['STORES'] = [
                'ID' => [],
                'CODE' => []
            ];

            if (Loader::includeModule('catalog')) {
                $regionPricesTypes = $this->getPricesTypes(true)->indexBy('priceTypeId');
                $regionStores = $this->getStores(true)->indexBy('storeId');

                $pricesTypes = Arrays::fromDBResult(CCatalogGroup::GetList([
                    'SORT' => 'ASC'
                ], [
                    'ACTIVE' => 'Y'
                ]));

                $stores = Arrays::fromDBResult(CCatalogStore::GetList([
                    'SORT' => 'ASC'
                ], [
                    'ACTIVE' => 'Y',
                    'ISSUING_CENTER' => 'Y'
                ]));

                foreach ($pricesTypes as $priceType) {
                    if (!$regionPricesTypes->exists($priceType['ID']))
                        continue;

                    $result['PRICES']['ID'][] = $priceType['ID'];
                    $result['PRICES']['CODE'][] = $priceType['NAME'];
                }

                foreach ($stores as $store) {
                    if (!$regionStores->exists($store['ID']))
                        continue;

                    $result['STORES']['ID'][] = $store['ID'];

                    if (!empty($store['CODE']))
                        $result['STORES']['CODE'][] = $store['CODE'];
                }
            } else if (Loader::includeModule('intec.startshop')) {
                $regionPricesTypes = $this->getPricesTypes(true)->indexBy('priceTypeId');
                $pricesTypes = Arrays::fromDBResult(CStartShopPrice::GetList([
                    'SORT' => 'ASC'
                ], [
                    'ACTIVE' => 'Y'
                ]));

                foreach ($pricesTypes as $priceType) {
                    if (!$regionPricesTypes->exists($priceType['ID']))
                        continue;

                    $result['PRICES']['ID'][] = $priceType['ID'];
                    $result['PRICES']['CODE'][] = $priceType['CODE'];
                }
            }
        }

        if ($withProperties) {
            $result['PROPERTIES'] = $this
                ->getPropertiesValues(null, false)
                ->asArray(function ($key, $value) {
                    return [
                        'key' => $key,
                        'value' => [
                            'DISPLAY' => null,
                            'RAW' => $value
                        ]
                    ];
                });

            $properties = $this
                ->getPropertiesValues(null, false, true);

            foreach ($properties as $key => $value)
                $result['PROPERTIES'][$key]['DISPLAY'] = $value;
        }

        return $result;
    }

    /**
     * Возвращает пары (ключ свойства - значение).
     * @param string|boolean|null $site Идентификатор сайта.
     * @param string|boolean|null $prefix Добавлять префикс ключу свойства.
     * @param boolean $render Отрисовывать свойство.
     * @param boolean $raw Сохранять оригинальное значение свойства.
     * @return Collection
     */
    public function getPropertiesValues($site = null, $prefix = null, $render = false, $raw = false)
    {
        global $USER_FIELD_MANAGER;

        $result = new Collection();
        $context = Context::getCurrent();
        $properties = static::getProperties();

        if ($site === null || $site === true) {
            $site = $context->getSite();
        } else if ($site === false) {
            $site = null;
        }

        if ($prefix === null || $prefix === true) {
            $prefix = self::PROPERTY_PREFIX_FIELD;
        } else if ($prefix === false) {
            $prefix = null;
        }

        /** @var Value[] $values */
        $values = $this->getValues(true);
        $valuesCommon = new Collection();
        $valuesSite = new Collection();

        foreach ($values as $value) {
            if ($value->siteId === null) {
                $valuesCommon->set($value->propertyCode, $value);
            } else if (!empty($site) && $value->siteId == $site) {
                $valuesSite->set($value->propertyCode, $value);
            }
        }

        foreach ($properties as $property) {
            $code = $property['FIELD_CODE'];
            /** @var Value $value */
            $value = null;

            if ($valuesSite->exists($code)) {
                $value = $valuesSite->get($code);
            } else if ($valuesCommon->exists($code)) {
                $value = $valuesCommon->get($code);
            }

            if (!empty($value)) {
                $value = $value->getNormalizedValue();
            } else {
                $value = null;
            }

            if ($render && !empty($USER_FIELD_MANAGER)) {
                if ($raw)
                    $result->set($prefix.$code.'_RAW', $value);

                $property['VALUE'] = $value;
                $value = $USER_FIELD_MANAGER->GetPublicView($property);

                unset($property['VALUE']);

                $result->set($prefix.$code.($raw ? '_DISPLAY' : null), $value);
            } else {
                $result->set($prefix.$code, $value);
            }

        }

        return $result;
    }

    /**
     * Проверяет, относится ли регион к одному из указанных сайтов.
     * @param string|array $sites Идентификаторы сайтов.
     * @param boolean $all Относится ко всем сайтам в списке.
     * @return boolean
     */
    public function isForSites($sites, $all = false)
    {
        $result = false;

        if (empty($sites))
            return $result;

        if (!Type::isArray($sites))
            $sites = [$sites];

        $regionSites = $this->getSites(true);

        if ($all) {
            $result = true;

            foreach ($regionSites as $regionSite)
                if (!ArrayHelper::isIn($regionSite->siteId, $sites)) {
                    $result = false;
                    break;
                }
        } else {
            foreach ($regionSites as $regionSite)
                if (ArrayHelper::isIn($regionSite->siteId, $sites)) {
                    $result = true;
                    break;
                }
        }

        return $result;
    }

    /**
     * Определяет доменное имя для региона.
     * @param string|boolean|null $site Сайт для определяния.
     * @param boolean $strict Определять домен в любом случае.
     * @return string|null
     */
    public function resolveDomain($site = null, $strict = false)
    {
        if ($site === null || $site === true) {
            $site = Context::getCurrent()->getSite();
        } else if ($site === false) {
            $site = null;
        }

        /** Получаем все домены региона */
        $domains = $this->getDomains(true);
        $domains->sortBy('sort', SORT_ASC);
        $domain = null;

        /** Если у региона есть домены */
        if (!$domains->isEmpty()) {
            /** Ищем домен по умолчанию */
            foreach ($domains as $domain) {
                if (!empty($site))
                    if ($domain->siteId != $site) {
                        $domain = null;
                        continue;
                    }

                /** @var Domain $domain */
                if ($domain->default) {
                    $domain = $domain->value;
                    break;
                }

                $domain = null;
            }

            /** Если домен по умолчанию не найден, ищем активный домен */
            if (empty($domain))
                foreach ($domains as $domain) {
                    if (!empty($site))
                        if ($domain->siteId != $site) {
                            $domain = null;
                            continue;
                        }

                    /** @var Domain $domain */
                    if ($domain->active) {
                        $domain = $domain->value;
                        break;
                    }

                    $domain = null;
                }
        }

        /** Если домен не определен и сайт определен */
        if (empty($domain) && !empty($site)) {
            /** Получаем настройки сайта */
            $siteSettings = SiteSettings::get($site);

            if (!empty($siteSettings)) {
                $domain = $siteSettings->getDomain($strict);

                if (empty($domain))
                    $domain = null;
            }
        }

        return $domain;
    }

    /**
     * Реляция. Возвращает домены региона.
     * @param boolean $result Возвращать результат.
     * @param boolean $collection Возвращать результат как коллекцию.
     * @return ActiveRecords|Domain[]|ActiveQuery
     */
    public function getDomains($result = false, $collection = true)
    {
        return $this->relation(
            'domains',
            $this->hasMany(Domain::className(), ['regionId' => 'id']),
            $result,
            $collection
        );
    }

    /**
     * Реляция. Возвращает типы цен региона.
     * @param boolean $result Возвращать результат.
     * @param boolean $collection Возвращать результат как коллекцию.
     * @return ActiveRecords|PriceType[]|ActiveQuery
     */
    public function getPricesTypes($result = false, $collection = true)
    {
        return $this->relation(
            'pricesTypes',
            $this->hasMany(PriceType::className(), ['regionId' => 'id']),
            $result,
            $collection
        );
    }

    /**
     * Реляция. Возвращает типы цен региона.
     * @param boolean $result Возвращать результат.
     * @param boolean $collection Возвращать результат как коллекцию.
     * @return ActiveRecords|Store[]|ActiveQuery
     */
    public function getStores($result = false, $collection = true)
    {
        return $this->relation(
            'stores',
            $this->hasMany(Store::className(), ['regionId' => 'id']),
            $result,
            $collection
        );
    }

    /**
     * Реляция. Возвращает сайты региона.
     * @param boolean $result Возвращать результат.
     * @param boolean $collection Возвращать результат как коллекцию.
     * @return ActiveRecords|Site[]|ActiveQuery
     */
    public function getSites($result = false, $collection = true)
    {
        return $this->relation(
            'sites',
            $this->hasMany(Site::className(), ['regionId' => 'id']),
            $result,
            $collection
        );
    }

    /**
     * Реляция. Возвращает значения свойств региона.
     * @param bool $result Возвращать результат.
     * @param bool $collection Возвращать результат как коллекцию.
     * @return ActiveRecords|Value[]|ActiveQuery
     */
    public function getValues($result = false, $collection = true)
    {
        return $this->relation(
            'values',
            $this->hasMany(Value::className(), ['regionId' => 'id']),
            $result,
            $collection
        );
    }
}