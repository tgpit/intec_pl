<?php
namespace intec\seo\models\filter\condition;

use CSite;
use CUserTypeEntity;
use CIBlockSection;
use CIBlockProperty;
use CCatalogGroup;
use CCatalogSku;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\StoreTable;
use intec\core\base\conditions\GroupCondition;
use intec\core\bitrix\conditions\IBlockPropertyCondition;
use intec\core\collections\Arrays;
use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\core\db\ActiveRecords;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\regionality\models\Region;
use intec\seo\models\filter\Condition;
use intec\seo\models\filter\condition\generator\Block;
use intec\seo\models\filter\condition\generator\block\Property;
use intec\seo\models\filter\condition\generator\Blocks;
use intec\seo\models\filter\condition\generator\Section;
use intec\seo\models\filter\condition\generator\Site;
use intec\seo\models\filter\condition\Section as ConditionSection;
use intec\seo\models\filter\condition\Site as ConditionSite;

Loc::loadMessages(__FILE__);

/**
 * Модель генератора условий.
 * Class Generator
 * @property integer $id Идентификатор.
 * @property string $name Наименование.
 * @property string $iBlockId Инфоблок.
 * @property string $operator Оператор.
 * @property array $blocks Блоки.
 * @property string $conditionName Название генерируемых условий.
 * @property integer $conditionActive Активность генерируемых условий.
 * @property integer $conditionSearchable Участие в поиске генерируемых условий.
 * @property integer $conditionIndexing Индексация генерируемых условий.
 * @property integer $conditionStrict Строгость генерируемых условий.
 * @property integer $conditionRecursive Рекурсивность генерируемых условий.
 * @property float $conditionPriority Приоритет генерируемых условий.
 * @property string $conditionFrequency Частота генерируемых условий.
 * @property string $conditionMetaTitle Meta заголовок генерируемых условий.
 * @property string $conditionMetaKeywords Meta ключевые слова генерируемых условий.
 * @property string $conditionMetaDescription Meta описание генерируемых условий.
 * @property string $conditionMetaSearchTitle Заголовок страницы генерируемых условий.
 * @property string $conditionMetaPageTitle Заголовок страницы генерируемых условий.
 * @property string $conditionMetaBreadcrumbName Название в хлебных крошках генерируемых условий.
 * @property string $conditionMetaDescriptionTop Верхнее описание генерируемых условий.
 * @property string $conditionMetaDescriptionBottom Нижнее описание генерируемых условий.
 * @property string $conditionMetaDescriptionAdditional Дополнительное описание генерируемых условий.
 * @property string $conditionTagName Наименование тега генерируемых условий.
 * @property string $conditionTagMode Режим тегов генерируемых условий.
 * @property integer $conditionTagRelinkingStrict Строгая перелинковка тегов генерируемых условий.
 * @property integer $conditionUrlActive Активность Url условий.
 * @property string $conditionUrlName Шаблон наименования Url условий.
 * @property string $conditionUrlSource Шаблон исходного адреса Url условий.
 * @property string $conditionUrlTarget Шаблон целевого адреса Url условий.
 * @property string $conditionUrlGenerator Генератор Url условий.
 * @property integer $sort Сортировка.
 * @package intec\seo\models\filter\condition
 * @author apocalypsisdimon@gmail.com
 */
class Generator extends ActiveRecord
{
    /**
     * Логический оператор: и.
     */
    const OPERATOR_AND = 'and';
    /**
     * Логический оператор: или.
     */
    const OPERATOR_OR = 'or';

    /**
     * Возвращает список логических операторов.
     * @return array
     */
    public static function getOperators()
    {
        return [
            self::OPERATOR_AND,
            self::OPERATOR_OR
        ];
    }

    /**
     * @var array $cache
     */
    protected static $cache = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seo_filter_conditions_generators';
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
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'blocks' => [
                'class' => 'intec\core\behaviors\FieldArray',
                'attribute' => 'blocks'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'name' => [['name'], 'string', 'max' => 255],
            'iBlockId' => [['iBlockId'], 'integer'],
            'operator' => [['operator'], 'string'],
            'operatorRange' => [['operator'], 'in', 'range' => self::getOperators()],
            'operatorDefault' => [['operator'], 'default', 'value' => self::OPERATOR_AND],
            'blocks' => [['blocks'], 'string'],
            'conditionActive' => [['conditionActive'], 'boolean'],
            'conditionActiveDefault' => [['conditionActive'], 'default', 'value' => 1],
            'conditionName' => [['conditionName'], 'string', 'max' => 255],
            'conditionSearchable' => [['conditionSearchable'], 'boolean'],
            'conditionSearchableDefault' => [['conditionSearchable'], 'default', 'value' => 1],
            'conditionIndexing' => [['conditionIndexing'], 'boolean'],
            'conditionIndexingDefault' => [['conditionIndexing'], 'default', 'value' => 1],
            'conditionStrict' => [['conditionStrict'], 'boolean'],
            'conditionStrictDefault' => [['conditionStrict'], 'default', 'value' => 0],
            'conditionRecursive' => [['conditionRecursive'], 'boolean'],
            'conditionRecursiveDefault' => [['conditionRecursive'], 'default', 'value' => 1],
            'conditionPriority' => [['conditionPriority'], 'double', 'min' => 0, 'max' => 1],
            'conditionPriorityDefault' => [['conditionPriority'], 'default', 'value' => 0.0],
            'conditionFrequency' => [['conditionFrequency'], 'string'],
            'conditionFrequencyRange' => [['conditionFrequency'], 'in', 'range' => Condition::getFrequenciesValues()],
            'conditionFrequencyDefault' => [['conditionFrequency'], 'default', 'value' => Condition::FREQUENCY_ALWAYS],
            'conditionMetaTitle' => ['conditionMetaTitle', 'string'],
            'conditionMetaKeywords' => ['conditionMetaKeywords', 'string'],
            'conditionMetaDescription' => ['conditionMetaDescription', 'string'],
            'conditionMetaSearchTitle' => ['conditionMetaSearchTitle', 'string'],
            'conditionMetaPageTitle' => ['conditionMetaPageTitle', 'string'],
            'conditionMetaBreadcrumbName' => ['conditionMetaBreadcrumbName', 'string'],
            'conditionMetaDescriptionTop' => ['conditionMetaDescriptionTop', 'string'],
            'conditionMetaDescriptionBottom' => ['conditionMetaDescriptionBottom', 'string'],
            'conditionMetaDescriptionAdditional' => ['conditionMetaDescriptionAdditional', 'string'],
            'conditionTagName' => [['conditionTagName'], 'string'],
            'conditionTagMode' => [['conditionTagMode'], 'string', 'max' => 255],
            'conditionTagModeRange' => [['conditionTagMode'], 'in', 'range' => Condition::getTagModesValues()],
            'conditionTagModeDefault' => [['conditionTagMode'], 'default', 'value' => Condition::TAG_MODE_SELF],
            'conditionTagRelinkingStrict' => [['conditionTagRelinkingStrict'], 'boolean'],
            'conditionTagRelinkingStrictDefault' => [['conditionTagRelinkingStrict'], 'default', 'value' => 0],
            'conditionUrlActive' => [['conditionUrlActive'], 'boolean'],
            'conditionUrlActiveDefault' => [['conditionUrlActive'], 'default', 'value' => 0],
            'conditionUrlName' => [['conditionUrlName'], 'string', 'max' => 255],
            'conditionUrlSource' => [['conditionUrlSource'], 'string'],
            'conditionUrlTarget' => [['conditionUrlTarget'], 'string'],
            'conditionUrlGenerator' => [['conditionUrlGenerator'], 'string'],
            'sort' => [['sort'], 'integer'],
            'sortDefault' => [['sort'], 'default', 'value' => 500],
            'required' => [[
                'name',
                'operator',
                'conditionActive',
                'conditionName',
                'conditionSearchable',
                'conditionIndexing',
                'conditionStrict',
                'conditionRecursive',
                'conditionPriority',
                'conditionFrequency',
                'conditionTagMode',
                'conditionTagRelinkingStrict',
                'conditionUrlActive',
                'sort'
            ], 'required']
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.id'),
            'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.name'),
            'iBlockId' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.iBlockId'),
            'blocks' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.blocks'),
            'conditionActive' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionActive'),
            'conditionName' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionName'),
            'conditionSearchable' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionSearchable'),
            'conditionIndexing' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionIndexing'),
            'conditionStrict' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionStrict'),
            'conditionRecursive' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionRecursive'),
            'conditionPriority' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionPriority'),
            'conditionFrequency' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionFrequency'),
            'conditionMetaTitle' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionMetaTitle'),
            'conditionMetaKeywords' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionMetaKeywords'),
            'conditionMetaDescription' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionMetaDescription'),
            'conditionMetaSearchTitle' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionMetaSearchTitle'),
            'conditionMetaPageTitle' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionMetaPageTitle'),
            'conditionMetaBreadcrumbName' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionMetaBreadcrumbName'),
            'conditionMetaDescriptionTop' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionMetaDescriptionTop'),
            'conditionMetaDescriptionBottom' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionMetaDescriptionBottom'),
            'conditionMetaDescriptionAdditional' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionMetaDescriptionAdditional'),
            'conditionTagName' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionTagName'),
            'conditionTagMode' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionTagMode'),
            'conditionTagRelinkingStrict' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionTagRelinkingStrict'),
            'conditionUrlActive' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionUrlActive'),
            'conditionUrlName' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionUrlName'),
            'conditionUrlSource' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionUrlSource'),
            'conditionUrlTarget' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionUrlTarget'),
            'conditionUrlGenerator' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.conditionUrlGenerator'),
            'sort' => Loc::getMessage('intec.seo.models.filter.condition.generator.attributes.sort')
        ];
    }

    /**
     * Устанавливает блоки из объекта.
     * @param Blocks|Block[] $value
     * @return static
     */
    public function setBlocks($value)
    {
        $this->blocks = Blocks::from($value)->export();

        return $this;
    }

    /**
     * Возвращает блоки в виде объекта.
     * @return Blocks
     */
    public function getBlocks()
    {
        return Blocks::create($this->blocks);
    }

    /**
     * Возвращает макросы.
     * @return array
     */
    public function getMacros()
    {
        $result = [];
        $blocks = $this->getBlocks();

        if (!$blocks->isEmpty()) {
            $part = [
                'code' => 'combinations',
                'type' => 'group',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.combinations.name'),
                'items' => []
            ];

            for ($index = 1; $index <= $blocks->getCount(); $index++) {
                $part['items'][] = [
                    'code' => $index,
                    'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.combinations.items.block.name', [
                        '#block#' => $index
                    ]),
                    'type' => 'group',
                    'items' => [[
                        'type' => 'macro',
                        'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.combinations.items.block.items.propertyName'),
                        'value' => '{#PROPERTY_NAME#%'.$index.'%default}'
                    ], [
                        'type' => 'macro',
                        'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.combinations.items.block.items.propertyValue'),
                        'value' => '{#PROPERTY_VALUE#%'.$index.'%concat,}'
                    ]]
                ];
            }

            $result[] = $part;

            unset($part);
        }

        $result[] = [
            'code' => 'this',
            'type' => 'group',
            'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.this.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.this.items.name'),
                'value' => '{=this.Name}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.this.items.code'),
                'value' => '{=this.Code}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.this.items.previewText'),
                'value' => '{=this.PreviewText}'
            ]]
        ];

        $result[] = [
            'code' => 'parent',
            'type' => 'group',
            'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.parent.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.parent.items.name'),
                'value' => '{=parent.Name}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.parent.items.code'),
                'value' => '{=parent.Code}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.parent.items.previewText'),
                'value' => '{=parent.PreviewText}'
            ]]
        ];

        $result[] = [
            'code' => 'iblock',
            'type' => 'group',
            'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.iblock.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.iblock.items.name'),
                'value' => '{=iblock.Name}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.iblock.items.code'),
                'value' => '{=iblock.Code}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.iblock.items.previewText'),
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
                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.properties.name'),
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
                            'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.skuProperties.name'),
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
                            'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.prices.name'),
                            'items' => []
                        ];

                        foreach ($prices as $price) {
                            $part['items'][] = [
                                'code' => $price['NAME'],
                                'type' => 'group',
                                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.prices.items.price.name', [
                                    '#price#' => !empty($price['NAME_LANG']) ? $price['NAME_LANG'] : $price['NAME']
                                ]),
                                'items' => [[
                                    'type' => 'macro',
                                    'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.prices.items.price.items.minimal'),
                                    'value' => '{=filterPrice "'.$price['NAME'].'" "default.minimal"}'
                                ], [
                                    'type' => 'macro',
                                    'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.prices.items.price.items.maximal'),
                                    'value' => '{=filterPrice "'.$price['NAME'].'" "default.maximal"}'
                                ], [
                                    'type' => 'macro',
                                    'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.prices.items.price.items.minimalFiltered'),
                                    'value' => '{=filterPrice "'.$price['NAME'].'" "filtered.minimal"}'
                                ], [
                                    'type' => 'macro',
                                    'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.prices.items.price.items.maximalFiltered'),
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
                            'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.stores.name'),
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
                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.regionality.name'),
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
            'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.misc.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.misc.items.sections'),
                'value' => '{=concat this.sections.name " / "}'
            ]]
        ];

        if (Loader::includeModule('catalog')) {
            $part['items'][] = [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.misc.items.stores'),
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
                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.fields.name'),
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
            'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.pagination.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.pagination.items.pageNumber'),
                'value' => '#SEO_FILTER_PAGINATION_NUMBER#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.filter.condition.generator.macros.pagination.items.text'),
                'value' => '#SEO_FILTER_PAGINATION_TEXT#'
            ]]
        ];

        return $result;
    }

    /**
     * Генерирует условия.
     */
    public function generate()
    {
        if (empty($this->iBlockId) || (empty($this->conditionName) && !Type::isNumeric($this->conditionName)))
            return false;

        /** Определяем тип каталога (default - стандартный встроенный) */
        $catalog = null;

        if (Loader::includeModule('catalog'))
            $catalog = 'default';

        $temporary = new Blocks();
        $blocks = $this->getBlocks();

        /** Просчитываем валидные блоки с заполненными свойствами */
        /** @var Block $block */
        foreach ($blocks as $block) {
            $invalid = false;
            $properties = $block->getProperties();

            if ($properties->isEmpty()) {
                $invalid = true;
            } else {
                /** @var Property $property */
                foreach ($properties as $property)
                    if (empty($property->id)) {
                        $invalid = true;
                        break;
                    }
            }

            if (!$invalid)
                $temporary->add($block);
        }

        $blocks = $temporary;

        unset($temporary);

        /** Если валидных блоков нет, выходим с ошибкой */
        if ($blocks->isEmpty())
            return false;

        /** Получаем комбинации */
        $combinations = $blocks->getCombinations();

        /** Получаем список сайтов генератора */
        $sites = $this->getSites(true)->asArray(function ($index, $site) {
            return ['value' => $site->siteId];
        });

        /** Получаем сайты системы, которые указаны в генераторе */
        if (!empty($sites)) {
            $sites = Arrays::fromDBResult(CSite::GetList($sort = 'sort', $order = 'asc'))->where(function ($index, $site) use ($sites) {
                return ArrayHelper::isIn($site['ID'], $sites);
            })->indexBy('ID');
        } else {
            $sites = Arrays::from([]);
        }

        /** Получаем список разделов генератора */
        $sections = $this->getSections(true)->asArray(function ($index, $section) {
            return ['value' => $section->iBlockSectionId];
        });

        /** Получаем разделы системы, которые указаны в генераторе */
        if (!empty($sections)) {
            $sections = Arrays::fromDBResult(CIBlockSection::GetList([
                'SORT' => 'ASC'
            ], [
                'IBLOCK_ID' => $this->iBlockId,
                'ID' => $sections
            ]))->indexBy('ID');
        } else {
            $sections = Arrays::from([]);
        }

        /** Получаем свойства системы по инфоблоку */
        $properties = Arrays::fromDBResult(CIBlockProperty::GetList([
            'SORT' => 'ASC'
        ], [
            'IBLOCK_ID' => $this->iBlockId
        ]));

        if ($catalog === 'default') {
            /** Получаем свойства торговых предложений для встроенного каталога */
            $sku = CCatalogSku::GetInfoByProductIBlock($this->iBlockId);

            if ($sku !== false) {
                $properties->addRange(Arrays::fromDBResult(CIBlockProperty::GetList([
                    'SORT' => 'ASC'
                ], [
                    'IBLOCK_ID' => $sku['IBLOCK_ID']
                ])));
            }
        }

        /** Индексируем свойства */
        $properties->indexBy('ID');

        /** Устанавливаем тип для макроса */
        $properties->each(function ($id, &$property) use ($catalog) {
            $property['MACROS_TYPE'] = 'ProductProperty';

            if ($catalog === 'default') {
                $sku = CCatalogSku::GetInfoByOfferIBlock($property['IBLOCK_ID']);

                if ($sku !== false)
                    $property['MACROS_TYPE'] = 'OfferProperty';
            }
        });

        /** Подготавливаем макрос */
        $macros = [
            'SITES_ID' => [],
            'SITES_NAME' => [],
            'SECTIONS_ID' => [],
            'SECTIONS_NAME' => [],
            'PROPERTIES_ID' => [],
            'PROPERTIES_NAME' => []
        ];

        /** Устанавливаем макросы сайтов */
        foreach ($sites as $site) {
            $macros['SITES_ID'][] = $site['ID'];
            $macros['SITES_NAME'][] = !empty($site['SITE_NAME']) ? $site['SITE_NAME'] : $site['NAME'];
        }

        /** Устанавливаем макросы разделов */
        foreach ($sections as $section) {
            $macros['SECTIONS_ID'][] = $section['ID'];
            $macros['SECTIONS_NAME'][] = $section['NAME'];
        }

        /** Устанавливаем макросы свойств */
        $properties->each(function ($index, $property) use (&$blocks, &$macros) {
            $result = false;

            /** @var Block $block */
            foreach ($blocks as $block) {
                $blockProperties = $block->getProperties();

                /** @var Property $blockProperty */
                foreach ($blockProperties as $blockProperty) {
                    if ($blockProperty->id == $property['ID']) {
                        $result = true;
                        break;
                    }
                }

                if ($result)
                    break;
            }

            if ($result) {
                $macros['PROPERTIES_ID'][] = $property['ID'];
                $macros['PROPERTIES_NAME'][] = $property['NAME'];
            }
        });

        foreach ($macros as $index => $macro)
            $macros[$index] = implode(', ', $macro);

        /** Генерируем условия */
        foreach ($combinations as $combination) {
            $conditions = [];

            /** Генерируем правила условия */
            foreach ($combination as $property)
                $conditions[] = new IBlockPropertyCondition([
                    'operator' => IBlockPropertyCondition::OPERATOR_EQUAL,
                    'id' => $property->id,
                    'value' => null
                ]);

            $condition = $this->createCondition();
            $condition->name = StringHelper::replaceMacros($this->conditionName, $macros);

            if (StringHelper::length($condition->name) > 255)
                $condition->name = StringHelper::cut($condition->name, 0, 255);

            $condition->iBlockId = $this->iBlockId;
            $condition->setRules(new GroupCondition([
                'operator' => $this->operator,
                'conditions' => $conditions
            ]));

            $attributes = $condition->getAttributes();

            /** Заменяем макросы в атрибутах условия */
            foreach ($attributes as $key => $value) {
                if (!StringHelper::startsWith($key, 'meta') && $key !== 'tagName')
                    continue;

                $patterns = [];
                $matches = [];

                if (preg_match_all('/{#PROPERTY_NAME#%(\d+)%(default|lower|upper)}/', $value, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $pattern = $match[0];
                        $block = $match[1] - 1;
                        $modifier = $match[2];
                        $result = null;

                        if (ArrayHelper::keyExists($pattern, $patterns))
                            continue;

                        if (isset($combination[$block])) {
                            $property = $properties->get($combination[$block]->id);

                            if (!empty($property)) {
                                $result = $property['NAME'];

                                if ($modifier === 'lower') {
                                    $result = StringHelper::toLowerCase($result, Encoding::getDefault());
                                } else if ($modifier === 'upper') {
                                    $result = StringHelper::toUpperCase($result, Encoding::getDefault());
                                }
                            }
                        }

                        $patterns[$pattern] = $result;
                    }
                }

                $matches = [];

                if (preg_match_all('/{#PROPERTY_VALUE#%(\d+)%(concat[,\/]|lower|upper)}/', $value, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $pattern = $match[0];
                        $block = $match[1] - 1;
                        $modifier = $match[2];
                        $delimiter = null;
                        $result = null;

                        if (ArrayHelper::keyExists($pattern, $patterns))
                            continue;

                        if (StringHelper::startsWith($modifier, 'concat')) {
                            $delimiter = StringHelper::cut($modifier, -1, null, Encoding::getDefault());
                            $modifier = StringHelper::cut($modifier, 0, -1, Encoding::getDefault());
                        }

                        if (isset($combination[$block])) {
                            $property = $properties->get($combination[$block]->id);

                            if (!empty($property)) {
                                $code = $property['CODE'];

                                if (empty($code) && !Type::isNumeric($code))
                                    $code = $property['ID'];

                                $result = '{='.$modifier.' {='.$property['MACROS_TYPE'].' "'.$code.'"}'.(!empty($delimiter) ? ' "'.$delimiter.' "' : null).'}';
                            }

                        }

                        $patterns[$pattern] = $result;
                    }
                }

                $value = StringHelper::replace($value, $patterns);
                $condition->setAttribute($key, $value);
            }

            /** Сохраняем условие */
            if ($condition->save()) {
                /** Устанавливаем сайты условию */
                foreach ($sites as $site) {
                    $conditionSite = new ConditionSite();
                    $conditionSite->conditionId = $condition->id;
                    $conditionSite->siteId = $site['ID'];
                    $conditionSite->save();
                }

                /** Устанавливаем разделы условию */
                foreach ($sections as $section) {
                    $conditionSection = new ConditionSection();
                    $conditionSection->conditionId = $condition->id;
                    $conditionSection->iBlockSectionId = $section['ID'];
                    $conditionSection->save();
                }
            }
        }

        return true;
    }

    /**
     * Создает условие с наследованием параметров.
     * @return Condition
     */
    public function createCondition()
    {
        $condition = new Condition();
        $condition->active = $this->conditionActive;
        $condition->searchable = $this->conditionSearchable;
        $condition->indexing = $this->conditionIndexing;
        $condition->strict = $this->conditionStrict;
        $condition->recursive = $this->conditionRecursive;
        $condition->priority = $this->conditionPriority;
        $condition->frequency = $this->conditionFrequency;
        $condition->metaTitle = $this->conditionMetaTitle;
        $condition->metaKeywords = $this->conditionMetaKeywords;
        $condition->metaDescription = $this->conditionMetaDescription;
        $condition->metaSearchTitle = $this->conditionMetaSearchTitle;
        $condition->metaPageTitle = $this->conditionMetaPageTitle;
        $condition->metaBreadcrumbName = $this->conditionMetaBreadcrumbName;
        $condition->metaDescriptionTop = $this->conditionMetaDescriptionTop;
        $condition->metaDescriptionBottom = $this->conditionMetaDescriptionBottom;
        $condition->metaDescriptionAdditional = $this->conditionMetaDescriptionAdditional;
        $condition->tagName = $this->conditionTagName;
        $condition->tagMode = $this->conditionTagMode;
        $condition->tagRelinkingStrict = $this->conditionTagRelinkingStrict;
        $condition->urlActive = $this->conditionUrlActive;
        $condition->urlName = $this->conditionUrlName;
        $condition->urlSource = $this->conditionUrlSource;
        $condition->urlTarget = $this->conditionUrlTarget;
        $condition->urlGenerator = $this->conditionUrlGenerator;

        return $condition;
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
            $this->hasMany(Section::className(), ['generatorId' => 'id']),
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
            $this->hasMany(Site::className(), ['generatorId' => 'id']),
            $result,
            $collection
        );
    }
}