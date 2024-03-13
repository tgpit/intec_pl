<?php
namespace intec\seo\models\filter;

use CUtil;
use CUserTypeEntity;
use CIBlock;
use CIBlockSection;
use CIBlockElement;
use CIBlockProperty;
use CCatalogGroup;
use CCatalogSku;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\StoreTable;
use intec\Core;
use intec\core\base\conditions\GroupCondition;
use intec\core\base\WriterInterface;
use intec\core\base\writers\ClosureWriter;
use intec\core\collections\Arrays;
use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\core\db\ActiveRecords;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\regionality\models\Region;
use intec\seo\filter\condition\FilterHelper;
use intec\seo\filter\condition\url\Generator;
use intec\seo\filter\condition\url\generators\BitrixQueryGenerator;
use intec\seo\filter\condition\url\generators\BitrixSefGenerator;
use intec\seo\models\filter\condition\TagRelinkingCondition;
use intec\seo\models\filter\condition\Section;
use intec\seo\models\filter\condition\Article;
use intec\seo\models\filter\condition\AutofillSection;
use intec\seo\models\filter\condition\Site;

Loc::loadMessages(__FILE__);

/**
 * Модель условия фильтра.
 * Class Condition
 * @property integer $id Идентификатор.
 * @property integer $active Активность.
 * @property string $name Наименование.
 * @property integer $searchable Учавствует в поиске.
 * @property integer $indexing Индексируется.
 * @property integer $strict Строгое.
 * @property integer $recursive Рекурсивное.
 * @property float $priority Приоритет.
 * @property string $frequency Частота изменения.
 * @property string $iBlockId Инфоблок.
 * @property array $rules Правила.
 * @property string $metaTitle Заголовок meta.
 * @property string $metaKeywords Ключевые слова meta.
 * @property string $metaDescription Описание meta.
 * @property string $metaSearchTitle Заголовок в поиске.
 * @property string $metaPageTitle Заголовок страницы.
 * @property string $metaBreadcrumbName Наименование в хлебных крошках.
 * @property string $metaDescriptionTop Верхнее описание.
 * @property string $metaDescriptionBottom Нижнее описание.
 * @property string $metaDescriptionAdditional Дополнительное описание.
 * @property string $tagName Наименование тега.
 * @property string $tagMode Режим тегов.
 * @property integer $tagRelinkingStrict Строгая перелинковка тегов.
 * @property integer $urlActive Активность Url.
 * @property string $urlName Шаблон наименования Url.
 * @property string $urlSource Шаблон исходного адреса Url.
 * @property string $urlTarget Шаблон целевого адреса Url.
 * @property string $urlGenerator Генератор Url.
 * @property integer $sort Сортировка.
 * @package intec\seo\models\filter
 * @author apocalypsisdimon@gmail.com
 */
class Condition extends ActiveRecord
{
    /**
     * Частота изменения: Всегда.
     */
    const FREQUENCY_ALWAYS = 'always';
    /**
     * Частота изменения: Раз в час.
     */
    const FREQUENCY_HOURLY = 'hourly';
    /**
     * Частота изменения: Раз в день.
     */
    const FREQUENCY_DAILY = 'daily';
    /**
     * Частота изменения: Раз в неделю.
     */
    const FREQUENCY_WEEKLY = 'weekly';
    /**
     * Частота изменения: Раз в месяц.
     */
    const FREQUENCY_MONTHLY = 'monthly';
    /**
     * Частота изменения: Раз в год.
     */
    const FREQUENCY_YEARLY = 'yearly';
    /**
     * Частота изменения: Никогда.
     */
    const FREQUENCY_NEVER = 'never';

    /**
     * Возвращает частоту изменения.
     * @return array
     */
    public static function getFrequencies()
    {
        return [
            static::FREQUENCY_ALWAYS => Loc::getMessage('intec.seo.models.filter.condition.frequency.always'),
            static::FREQUENCY_HOURLY => Loc::getMessage('intec.seo.models.filter.condition.frequency.hourly'),
            static::FREQUENCY_DAILY => Loc::getMessage('intec.seo.models.filter.condition.frequency.daily'),
            static::FREQUENCY_WEEKLY => Loc::getMessage('intec.seo.models.filter.condition.frequency.weekly'),
            static::FREQUENCY_MONTHLY => Loc::getMessage('intec.seo.models.filter.condition.frequency.monthly'),
            static::FREQUENCY_YEARLY => Loc::getMessage('intec.seo.models.filter.condition.frequency.yearly'),
            static::FREQUENCY_NEVER => Loc::getMessage('intec.seo.models.filter.condition.frequency.never')
        ];
    }

    /**
     * Возвращает значения частоты изменения.
     * @return array
     */
    public static function getFrequenciesValues()
    {
        $values = static::getFrequencies();
        $values = ArrayHelper::getKeys($values);

        return $values;
    }

    /**
     * Режим тегов: Только раздел.
     */
    const TAG_MODE_SELF = 'self';
    /**
     * Режим тегов: Раздел и подразделы.
     */
    const TAG_MODE_RECURSIVE = 'recursive';
    /**
     * Режим тегов: Все разделы условия.
     */
    const TAG_MODE_SECTIONS = 'sections';
    /**
     * Режим тегов: Привязанные условия.
     */
    const TAG_MODE_RELINKING = 'relinking';
    /**
     * Режим тегов: Все.
     */
    const TAG_MODE_ALL = 'all';

    /**
     * Возвращает режимы тегов.
     * @return array
     */
    public static function getTagModes()
    {
        return [
            static::TAG_MODE_SELF => Loc::getMessage('intec.seo.models.filter.condition.tagMode.self'),
            static::TAG_MODE_RECURSIVE => Loc::getMessage('intec.seo.models.filter.condition.tagMode.recursive'),
            static::TAG_MODE_SECTIONS => Loc::getMessage('intec.seo.models.filter.condition.tagMode.sections'),
            static::TAG_MODE_RELINKING => Loc::getMessage('intec.seo.models.filter.condition.tagMode.relinking'),
            static::TAG_MODE_ALL => Loc::getMessage('intec.seo.models.filter.condition.tagMode.all')
        ];
    }

    /**
     * Возвращает значения режимов тегов.
     * @return array
     */
    public static function getTagModesValues()
    {
        $values = static::getTagModes();
        $values = ArrayHelper::getKeys($values);

        return $values;
    }

    /**
     * Возвращает список генераторов.
     * @return array
     */
    public static function getUrlGenerators()
    {
        return [
            BitrixQueryGenerator::className() => Loc::getMessage('intec.seo.models.filter.condition.generators.bitrixQuery'),
            BitrixSefGenerator::className() => Loc::getMessage('intec.seo.models.filter.condition.generators.bitrixSef')
        ];
    }

    /**
     * Возвращает значения списка генераторов.
     * @return array
     */
    public static function getUrlGeneratorsValues()
    {
        $values = static::getUrlGenerators();
        $values = ArrayHelper::getKeys($values);

        return $values;
    }

    /**
     * @var array $cache
     */
    protected static $cache = [];

    /**
     * @inheritdoc
     * @return ConditionQuery
     */
    public static function find()
    {
        return Core::createObject(ConditionQuery::className(), [get_called_class()]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seo_filter_conditions';
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->urlGenerator = ArrayHelper::fromRange(static::getUrlGeneratorsValues(), $this->urlGenerator, false, false);

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

        $sites = $this->getSites(true);
        $sections = $this->getSections(true);

        foreach ($sites as $site)
            $site->delete();

        foreach ($sections as $section)
            $section->delete();

        $sections = $this->getAutofillSections(true);

        foreach ($sections as $section)
            $section->delete();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'rules' => [
                'class' => 'intec\core\behaviors\FieldArray',
                'attribute' => 'rules'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'active' => [['active'], 'boolean'],
            'activeDefault' => [['active'], 'default', 'value' => 1],
            'name' => [['name'], 'string', 'max' => 255],
            'searchable' => [['searchable'], 'boolean'],
            'searchableDefault' => [['searchable'], 'default', 'value' => 1],
            'indexing' => [['indexing'], 'boolean'],
            'indexingDefault' => [['indexing'], 'default', 'value' => 1],
            'strict' => [['strict'], 'boolean'],
            'strictDefault' => [['strict'], 'default', 'value' => 0],
            'recursive' => [['recursive'], 'boolean'],
            'recursiveDefault' => [['recursive'], 'default', 'value' => 1],
            'priority' => [['priority'], 'double', 'min' => 0, 'max' => 1],
            'priorityDefault' => [['priority'], 'default', 'value' => 0.0],
            'frequency' => [['frequency'], 'string'],
            'frequencyRange' => [['frequency'], 'in', 'range' => static::getFrequenciesValues()],
            'frequencyDefault' => [['frequency'], 'default', 'value' => static::FREQUENCY_ALWAYS],
            'iBlockId' => [['iBlockId'], 'integer'],
            'rules' => [['rules'], 'string'],
            'metaTitle' => [['metaTitle'], 'string'],
            'metaKeywords' => [['metaKeywords'], 'string'],
            'metaDescription' => [['metaDescription'], 'string'],
            'metaSearchTitle' => [['metaSearchTitle'], 'string'],
            'metaPageTitle' => [['metaPageTitle'], 'string'],
            'metaBreadcrumbName' => [['metaBreadcrumbName'], 'string'],
            'metaDescriptionTop' => [['metaDescriptionTop'], 'string'],
            'metaDescriptionBottom' => [['metaDescriptionBottom'], 'string'],
            'metaDescriptionAdditional' => [['metaDescriptionAdditional'], 'string'],
            'tagName' => [['tagName'], 'string'],
            'tagMode' => [['tagMode'], 'string', 'max' => 255],
            'tagModeRange' => [['tagMode'], 'in', 'range' => static::getTagModesValues()],
            'tagModeDefault' => [['tagMode'], 'default', 'value' => static::TAG_MODE_SELF],
            'tagRelinkingStrict' => [['tagRelinkingStrict'], 'boolean'],
            'tagRelinkingStrictDefault' => [['tagRelinkingStrict'], 'default', 'value' => 0],
            'urlActive' => [['urlActive'], 'boolean'],
            'urlActiveDefault' => [['urlActive'], 'default', 'value' => 1],
            'urlName' => [['urlName'], 'string', 'max' => 255],
            'urlSource' => [['urlSource'], 'string'],
            'urlTarget' => [['urlTarget'], 'string'],
            'urlGenerator' => [['urlGenerator'], 'string'],
            'autofillIBlockId' => [['autofillIBlockId'], 'integer'],
            'autofillSelf' => [['autofillSelf'], 'boolean'],
            'autofillSelfDefault' => [['autofillSelf'], 'default', 'value' => 1],
            'autofillQuantity' => [['autofillQuantity'], 'integer'],
            'sort' => [['sort'], 'integer'],
            'sortDefault' => [['sort'], 'default', 'value' => 500],
            'required' => [[
                'active',
                'name',
                'searchable',
                'indexing',
                'strict',
                'recursive',
                'priority',
                'frequency',
                'tagMode',
                'tagRelinkingStrict',
                'urlActive',
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
            'id' => Loc::getMessage('intec.seo.models.filter.condition.attributes.id'),
            'active' => Loc::getMessage('intec.seo.models.filter.condition.attributes.active'),
            'name' => Loc::getMessage('intec.seo.models.filter.condition.attributes.name'),
            'searchable' => Loc::getMessage('intec.seo.models.filter.condition.attributes.searchable'),
            'indexing' => Loc::getMessage('intec.seo.models.filter.condition.attributes.indexing'),
            'strict' => Loc::getMessage('intec.seo.models.filter.condition.attributes.strict'),
            'recursive' => Loc::getMessage('intec.seo.models.filter.condition.attributes.recursive'),
            'priority' => Loc::getMessage('intec.seo.models.filter.condition.attributes.priority'),
            'frequency' => Loc::getMessage('intec.seo.models.filter.condition.attributes.frequency'),
            'iBlockId' => Loc::getMessage('intec.seo.models.filter.condition.attributes.iBlockId'),
            'rules' => Loc::getMessage('intec.seo.models.filter.condition.attributes.rules'),
            'metaTitle' => Loc::getMessage('intec.seo.models.filter.condition.attributes.metaTitle'),
            'metaKeywords' => Loc::getMessage('intec.seo.models.filter.condition.attributes.metaKeywords'),
            'metaDescription' => Loc::getMessage('intec.seo.models.filter.condition.attributes.metaDescription'),
            'metaSearchTitle' => Loc::getMessage('intec.seo.models.filter.condition.attributes.metaSearchTitle'),
            'metaPageTitle' => Loc::getMessage('intec.seo.models.filter.condition.attributes.metaPageTitle'),
            'metaBreadcrumbName' => Loc::getMessage('intec.seo.models.filter.condition.attributes.metaBreadcrumbName'),
            'metaDescriptionTop' => Loc::getMessage('intec.seo.models.filter.condition.attributes.metaDescriptionTop'),
            'metaDescriptionBottom' => Loc::getMessage('intec.seo.models.filter.condition.attributes.metaDescriptionBottom'),
            'metaDescriptionAdditional' => Loc::getMessage('intec.seo.models.filter.condition.attributes.metaDescriptionAdditional'),
            'tagName' => Loc::getMessage('intec.seo.models.filter.condition.attributes.tagName'),
            'tagMode' => Loc::getMessage('intec.seo.models.filter.condition.attributes.tagMode'),
            'tagRelinkingStrict' => Loc::getMessage('intec.seo.models.filter.condition.attributes.tagRelinkingStrict'),
            'urlActive' => Loc::getMessage('intec.seo.models.filter.condition.attributes.urlActive'),
            'urlName' => Loc::getMessage('intec.seo.models.filter.condition.attributes.urlName'),
            'urlSource' => Loc::getMessage('intec.seo.models.filter.condition.attributes.urlSource'),
            'urlTarget' => Loc::getMessage('intec.seo.models.filter.condition.attributes.urlTarget'),
            'urlGenerator' => Loc::getMessage('intec.seo.models.filter.condition.attributes.urlGenerator'),
            'autofillIBlockId' => Loc::getMessage('intec.seo.models.filter.condition.attributes.autofillIblockId'),
            'autofillSelf' => Loc::getMessage('intec.seo.models.filter.condition.attributes.autofillSelf'),
            'autofillQuantity' => Loc::getMessage('intec.seo.models.filter.condition.attributes.autofillQuantity'),
            'sort' => Loc::getMessage('intec.seo.models.filter.condition.attributes.sort')
        ];
    }

    /**
     * Устанавливает правила из объекта.
     * @param GroupCondition $value
     * @return static
     */
    public function setRules($value)
    {
        if (!($value instanceof GroupCondition))
            $value = new GroupCondition();

        $this->rules = $value->export();

        return $this;
    }

    /**
     * Возвращает набор правил в виде объекта.
     * @return GroupCondition|null
     */
    public function getRules()
    {
        $rules = GroupCondition::create($this->rules);

        if (empty($rules))
            $rules = new GroupCondition();

        return $rules;
    }

    /**
     * Возвращает макросы.
     * @return array
     */
    public function getMacros()
    {
        $result = [];
        $result[] = [
            'code' => 'this',
            'type' => 'group',
            'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.this.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.this.items.name'),
                'value' => '{=this.Name}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.this.items.code'),
                'value' => '{=this.Code}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.this.items.previewText'),
                'value' => '{=this.PreviewText}'
            ]]
        ];

        $result[] = [
            'code' => 'parent',
            'type' => 'group',
            'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.parent.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.parent.items.name'),
                'value' => '{=parent.Name}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.parent.items.code'),
                'value' => '{=parent.Code}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.parent.items.previewText'),
                'value' => '{=parent.PreviewText}'
            ]]
        ];

        $result[] = [
            'code' => 'iblock',
            'type' => 'group',
            'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.iblock.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.iblock.items.name'),
                'value' => '{=iblock.Name}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.iblock.items.code'),
                'value' => '{=iblock.Code}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.iblock.items.previewText'),
                'value' => '{=iblock.PreviewText}'
            ]]
        ];

        if (!empty($this->iBlockId)) {
            $properties = Arrays::fromDBResult(CIBlockProperty::GetList([
                'SORT' => 'ASC'
            ], [
                'IBLOCK_ID' => $this->iBlockId
            ]));

            $part = [
                'code' => 'properties',
                'type' => 'group',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.properties.name'),
                'items' => []
            ];

            foreach ($properties as $property) {
                if ($property['PROPERTY_TYPE'] === 'F')
                    continue;

                $code = $property['CODE'];

                if (empty($code) && !Type::isNumeric($code))
                    $code = $property['ID'];

                $part['items'][] = [
                    'type' => 'macro',
                    'name' => $property['NAME'],
                    'value' => '{=concat {=filterProperty "'.$code.'"} ", "}'
                ];
            }

            if (!empty($part['items']))
                $result[] = $part;

            unset($part);

            if (Loader::includeModule('catalog')) {
                $sku = CCatalogSku::GetInfoByIBlock($this->iBlockId);

                if ($sku !== false) {
                    if ($sku['IBLOCK_ID'] != $this->iBlockId) {
                        $skuProperties = Arrays::fromDBResult(CIBlockProperty::GetList([
                            'SORT' => 'ASC'
                        ], [
                            'IBLOCK_ID' => $sku['IBLOCK_ID']
                        ]));

                        $part = [
                            'code' => 'skuProperties',
                            'type' => 'group',
                            'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.skuProperties.name'),
                            'items' => []
                        ];

                        foreach ($skuProperties as $skuProperty) {
                            if ($skuProperty['PROPERTY_TYPE'] === 'F')
                                continue;

                            $code = $skuProperty['CODE'];

                            if (empty($code) && !Type::isNumeric($code))
                                $code = $skuProperty['ID'];

                            $part['items'][] = [
                                'type' => 'macro',
                                'name' => $skuProperty['NAME'],
                                'value' => '{=concat {=filterOfferProperty "'.$code.'"} ", "}'
                            ];
                        }

                        if (!empty($part['items']))
                            $result[] = $part;

                        unset($part);
                    }

                    $prices = Arrays::fromDBResult(CCatalogGroup::GetList());

                    if (!$prices->isEmpty()) {
                        $part = [
                            'code' => 'prices',
                            'type' => 'group',
                            'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.prices.name'),
                            'items' => []
                        ];

                        foreach ($prices as $price) {
                            $part['items'][] = [
                                'code' => $price['NAME'],
                                'type' => 'group',
                                'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.prices.items.price.name', [
                                    '#price#' => !empty($price['NAME_LANG']) ? $price['NAME_LANG'] : $price['NAME']
                                ]),
                                'items' => [[
                                    'type' => 'macro',
                                    'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.prices.items.price.items.minimal'),
                                    'value' => '{=filterPrice "'.$price['NAME'].'" "default.minimal"}'
                                ], [
                                    'type' => 'macro',
                                    'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.prices.items.price.items.maximal'),
                                    'value' => '{=filterPrice "'.$price['NAME'].'" "default.maximal"}'
                                ], [
                                    'type' => 'macro',
                                    'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.prices.items.price.items.minimalFiltered'),
                                    'value' => '{=filterPrice "'.$price['NAME'].'" "filtered.minimal"}'
                                ], [
                                    'type' => 'macro',
                                    'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.prices.items.price.items.maximalFiltered'),
                                    'value' => '{=filterPrice "'.$price['NAME'].'" "filtered.maximal"}'
                                ]]
                            ];
                        }

                        if (!empty($part['items']))
                            $result[] = $part;

                        unset($part);
                    }

                    $stores = Arrays::from(StoreTable::getList()->fetchAll());

                    if (!$stores->isEmpty()) {
                        $part = [
                            'code' => 'stores',
                            'type' => 'group',
                            'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.stores.name'),
                            'items' => []
                        ];

                        foreach ($stores as $store) {
                            $part['items'][] = [
                                'type' => 'macro',
                                'name' => !empty($store['TITLE']) ? $store['TITLE'] : $store['ADDRESS'],
                                'value' => '{=catalog.store.'.$store['ID'].'.name}'
                            ];
                        }

                        $result[] = $part;

                        unset($part);
                    }
                }
            }
        }

        if (Loader::includeModule('intec.regionality')) {
            $part = [
                'code' => 'regionality',
                'type' => 'group',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.regionality.name'),
                'items' => []
            ];

            $fields = Region::getFields();

            foreach ($fields as $key => $name) {
                if (StringHelper::position('PROPERTIES', $key)) {
                    $part['items'][] = [
                        'type' => 'macro',
                        'name' => $name,
                        'value' => '#'.$key.'_DISPLAY#'
                    ];
                    $part['items'][] = [
                        'type' => 'macro',
                        'name' => $name . ' (2)',
                        'value' => '#'.$key.'_RAW#'
                    ];
                } else {
                    $part['items'][] = [
                        'type' => 'macro',
                        'name' => $name,
                        'value' => '#'.$key.'_DISPLAY#'
                    ];
                }
            }

            $result[] = $part;

            unset($part);
        }

        $part = [
            'code' => 'misc',
            'type' => 'group',
            'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.misc.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.misc.items.sections'),
                'value' => '{=concat this.sections.name " / "}'
            ]]
        ];

        if (Loader::includeModule('catalog')) {
            $part['items'][] = [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.misc.items.stores'),
                'value' => '{=concat catalog.store ", "}'
            ];
        }

        $result[] = $part;

        unset($part);

        $fields = Arrays::fromDBResult(CUserTypeEntity::GetList(['SORT' => 'ASC']));

        if (!$fields->isEmpty()) {
            $part = [
                'code' => 'fields',
                'type' => 'group',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.fields.name'),
                'items' => []
            ];

            foreach ($fields as $field) {
                $part['items'][] = [
                    'type' => 'macro',
                    'name' => '['.$field['ID'].']['.$field['ENTITY_ID'].'] '.$field['FIELD_NAME'],
                    'value' => '#'.$field['FIELD_NAME'].'#'
                ];
            }

            $result[] = $part;

            unset ($part);
        }

        $result[] = [
            'code' => 'pagination',
            'type' => 'group',
            'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.pagination.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.pagination.items.pageNumber'),
                'value' => '#SEO_FILTER_PAGINATION_PAGE_NUMBER#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.macros.pagination.items.text'),
                'value' => '#SEO_FILTER_PAGINATION_TEXT#'
            ]]
        ];

        return $result;
    }

    /**
     * Возвращает генератор для условия.
     * @return Generator|null
     */
    public function getUrlGenerator()
    {
        $result = null;
        $class = $this->urlGenerator;

        if (empty($class))
            return $result;

        if (!is_subclass_of($class, Generator::className()))
            return $result;

        /** @var Generator $result */
        $result = new $class();
        $result->sourceTemplate = $this->urlSource;
        $result->targetTemplate = $this->urlTarget;
        $result->transliterationUse = true;

        return $result;
    }

    /**
     * Создает наименование для адреса из комбинации и раздела.
     * @param array $combination
     * @param array $section
     * @return string|null
     */
    public function generateUrlName($combination, $iblock, $section)
    {
        $result = $this->urlName;

        if (empty($result) && !Type::isNumeric($result) || empty($combination)|| empty($iblock) || empty($section))
            return null;

        $macros = [
            'IBLOCK_ID' => $iblock['ID'],
            'IBLOCK_CODE' => $iblock['CODE'],
            'IBLOCK_TYPE_ID' => $iblock['IBLOCK_TYPE_ID'],
            'IBLOCK_NAME' => $iblock['NAME'],
            'IBLOCK_EXTERNAL_ID' => $iblock['EXTERNAL_ID'],
            'SECTION_ID' => $section['ID'],
            'SECTION_CODE' => $section['CODE'],
            'SECTION_CODE_PATH' => $section['CODE_PATH'],
            'SECTION_NAME' => $section['NAME'],
            'SECTION_EXTERNAL_ID' => $section['EXTERNAL_ID'],
            'PROPERTIES_ID' => [],
            'PROPERTIES_CODE' => [],
            'PROPERTIES_NAME' => [],
            'PROPERTIES_COMBINATION' => []
        ];

        $properties = FilterHelper::getFilterObjects($combination, true);

        foreach ($properties as $property) {
            if (
                $property['TYPE'] === FilterHelper::FILTER_PROPERTY_TYPE_RANGE ||
                $property['TYPE'] === FilterHelper::FILTER_PROPERTY_TYPE_LIST
            ) {
                $macros['PROPERTIES_ID'][] = $property['ID'];

                if (!empty($property['CODE']) || Type::isNumeric($property['CODE']))
                    $property['PROPERTIES_CODE'][] = $property['CODE'];

                if ($property['TYPE'] === FilterHelper::FILTER_PROPERTY_TYPE_RANGE) {
                    if (isset($property['VALUES']['minimal']) && isset($property['VALUES']['maximal'])) {
                        $macros['PROPERTIES_COMBINATION'][] = $property['NAME'].' ('.Loc::getMessage('intec.seo.models.filter.condition.attributes.urlName.macros.range', [
                            '#minimal#' => $property['VALUES']['minimal']['TEXT'],
                            '#maximal#' => $property['VALUES']['maximal']['TEXT']
                        ]).')';
                    } else if (isset($property['VALUES']['MINIMAL'])) {
                        $macros['PROPERTIES_COMBINATION'][] = $property['NAME'].' ('.Loc::getMessage('intec.seo.models.filter.condition.attributes.urlName.macros.range.from', [
                            '#minimal#' => $property['VALUES']['minimal']['TEXT']
                        ]).')';
                    } else {
                        $macros['PROPERTIES_COMBINATION'][] = $property['NAME'].' ('.Loc::getMessage('intec.seo.models.filter.condition.attributes.urlName.macros.range.to', [
                            '#maximal#' => $property['VALUES']['maximal']['TEXT']
                        ]).')';
                    }
                } else {
                    $values = [];

                    foreach ($property['VALUES'] as $value)
                        $values[] = $value['TEXT'];

                    $macros['PROPERTIES_COMBINATION'][] = $property['NAME'].' ('.implode(', ', $values).')';
                }
            }
        }

        $macros['PROPERTIES_ID'] = implode(', ', $macros['PROPERTIES_ID']);
        $macros['PROPERTIES_CODE'] = implode(', ', $macros['PROPERTIES_CODE']);
        $macros['PROPERTIES_NAME'] = implode(', ', $macros['PROPERTIES_NAME']);
        $macros['PROPERTIES_COMBINATION'] = implode(', ', $macros['PROPERTIES_COMBINATION']);
        $result = StringHelper::replaceMacros($result, $macros);

        return $result;
    }

    /**
     * Генерирует адреса на основе правил.
     * @param Generator $generator
     * @param WriterInterface|null $writer
     */
    public function generateUrl($generator = null, $writer = null)
    {
        if (empty($this->iBlockId))
            return;

        if (empty($generator))
            $generator = $this->getUrlGenerator();

        if (!($generator instanceof Generator))
            return;

        /** Получаем инфоблок */
        $iblock = CIBlock::GetList([], [
            'ID' => $this->iBlockId
        ])->Fetch();

        if (empty($iblock))
            return;

        /** Получаем разделы */
        $sections = $this->getSections(true)->asArray(function ($index, $section) {
            return [
                'value' => $section->iBlockSectionId
            ];
        });

        $filter = [
            'IBLOCK_ID' => $iblock['ID'],
            'ACTIVE' => 'Y'
        ];

        if (!empty($sections))
            $filter['ID'] = $sections;

        $sections = Arrays::fromDBResult(CIBlockSection::GetList([
            'SORT' => 'ASC'
        ], $filter))->asArray();

        if ($writer === null) {
            $index = 0;
            $writer = new ClosureWriter(function ($url, $combination, $iblock, $section) use (&$index) {
                /** @var Url $url */
                $filter = FilterHelper::getFilterFromCombination($combination, $iblock, $section);

                if (empty($filter))
                    return false;

                $filter['ACTIVE'] = 'Y';
                $filter['ACTIVE_DATE'] = 'Y';

                if (isset($filter['ID'])) {
                    $filter['ID']->arFilter['ACTIVE'] = 'Y';
                    $filter['ID']->arFilter['ACTIVE_DATE'] = 'Y';
                }

                $filter['INCLUDE_SUBSECTIONS'] = $this->recursive ? 'Y' : 'N';

                $count = CIBlockElement::GetList([
                    'SORT' => 'ASC'
                ], $filter);

                $count = $count->SelectedRowsCount();

                if ($count < 1)
                    return false;

                $url->conditionId = $this->id;
                $url->active = $this->urlActive;
                $url->name = $this->generateUrlName($combination, $iblock, $section);

                $propertiesName = null;

                foreach ($combination as $combinationItem) {
                    if (empty($propertiesName)) {
                        $propertiesName = $combinationItem['VALUE']['TEXT'];
                    } else {
                        $propertiesName = $propertiesName . ' ' . $combinationItem['VALUE']['TEXT'];
                    }
                }

                $propertiesName = CUtil::translit($propertiesName, 'ru', [
                    'max_len' => 100000,
                    'change_case' => false,
                    'replace_space' => ', ',
                    'replace_other' => ', ',
                    'delete_repeat_replace' => true
                ]);

                $propertiesName = StringHelper::toLowerCase($propertiesName);

                if (empty($url->name) && !Type::isNumeric($url->name)) {
                    $url->name = $this->name . ($index > 0 ? ' (' . $index . ')' : null);
                    $url->name = $url->name . ' (' . $propertiesName . ')';
                }

                if (StringHelper::length($url->name) > 255)
                    $url->name = StringHelper::cut($url->name, 0, 255);

                $url->iBlockElementsCount = $count;

                if ($url->save()) {
                    $index++;
                    return true;
                }

                return false;
            }, false);
        }

        $generator->generateBatchByCondition($this->getRules(), $writer, $iblock, $sections, $this->recursive);
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
            $this->hasMany(Section::className(), ['conditionId' => 'id']),
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
            $this->hasMany(Article::className(), ['conditionId' => 'id']),
            $result,
            $collection
        );
    }

    /**
     * Реляция. Возвращает привязанные разделы для заполнения.
     * @param boolean $result Возвращать результат.
     * @param boolean $collection Возвращать как коллекцию.
     * @return Section[]|ActiveRecords|ActiveQuery|null
     */
    public function getAutofillSections($result = false, $collection = true)
    {
        return $this->relation(
            'autofillSections',
            $this->hasMany(AutofillSection::className(), ['conditionId' => 'id']),
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
            $this->hasMany(Site::className(), ['conditionId' => 'id']),
            $result,
            $collection
        );
    }

    /**
     * Реляция. Возвращает условия перелинковки.
     * @param boolean $result Возвращать результат.
     * @param boolean $collection Возвращать как коллекцию.
     * @return static[]|ActiveRecords|ActiveQuery|null
     */
    public function getTagRelinkingConditions($result = false, $collection = true)
    {
        return $this->relation(
            'tagRelinkingConditions',
            $this->hasMany(static::className(), ['id' => 'relinkingConditionId'])->viaTable(TagRelinkingCondition::tableName(), [
                'conditionId' => 'id'
            ]),
            $result,
            $collection
        );
    }

    /**
     * Реляция. Возвращает привязки условия перелинковки.
     * @param boolean $result Возвращать результат.
     * @param boolean $collection Возвращать как коллекцию.
     * @return TagRelinkingCondition[]|ActiveRecords|ActiveQuery|null
     */
    public function getTagRelinkingConditionsLinks($result = false, $collection = true)
    {
        return $this->relation(
            'tagRelinkingConditionsLinks',
            $this->hasMany(TagRelinkingCondition::className(), ['conditionId' => 'id']),
            $result,
            $collection
        );
    }

    /**
     * Реляция. Возвращает привязанные адреса.
     * @param boolean $result Возвращать результат.
     * @param boolean $collection Возвращать как коллекцию.
     * @return Url[]|ActiveRecords|ActiveQuery|null
     */
    public function getUrl($result = false, $collection = true)
    {
        return $this->relation(
            'url',
            $this->hasMany(Url::className(), ['conditionId' => 'id']),
            $result,
            $collection
        );
    }
}