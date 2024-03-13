<?php
namespace intec\seo\models\iblocks\metadata;

use CUserTypeEntity;
use CIBlockProperty;
use CCatalogSku;
use CCatalogGroup;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\StoreTable;
use intec\Core;
use intec\core\base\conditions\GroupCondition;
use intec\core\collections\Arrays;
use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\core\db\ActiveRecords;
use intec\core\helpers\Type;
use intec\core\helpers\StringHelper;
use intec\regionality\models\Region;
use intec\seo\models\iblocks\metadata\template\Section;
use intec\seo\models\iblocks\metadata\template\Site;

Loc::loadMessages(__FILE__);

/**
 * Модель шаблона метаинформации.
 * Class Condition
 * @property integer $id Идентификатор.
 * @property string $code Код.
 * @property integer $active Активность.
 * @property string $name Наименование.
 * @property integer $iBlockId Инфоблок.
 * @property array $rules Правила.
 * @property string $sectionMetaTitle Заголовок meta раздела.
 * @property string $sectionMetaKeywords Ключевые слова meta раздела.
 * @property string $sectionMetaDescription Описание meta раздела.
 * @property string $sectionMetaPageTitle Заголовок страницы раздела.
 * @property string $sectionMetaPicturePreviewAlt Alt картинки предпросмотра раздела.
 * @property string $sectionMetaPicturePreviewTitle Title картинки предпросмотра раздела.
 * @property string $sectionMetaPictureDetailAlt Alt детальной картинки раздела.
 * @property string $sectionMetaPictureDetailTitle Title детальной картинки раздела.
 * @property string $elementMetaTitle Заголовок meta элемента.
 * @property string $elementMetaKeywords Ключевые слова meta элемента.
 * @property string $elementMetaDescription Описание meta элемента.
 * @property string $elementMetaPageTitle Заголовок страницы элемента.
 * @property string $elementMetaPicturePreviewAlt Alt картинки предпросмотра элемента.
 * @property string $elementMetaPicturePreviewTitle Title картинки предпросмотра элемента.
 * @property string $elementMetaPictureDetailAlt Alt детальной картинки элемента.
 * @property string $elementMetaPictureDetailTitle Title детальной картинки элемента.
 * @property integer $sort Сортировка.
 * @package intec\seo\models\iblocks\metadata
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
        return 'seo_iblocks_metadata_templates';
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
            'code' => [['code'], 'string', 'max' => 255],
            'active' => [['active'], 'boolean'],
            'activeDefault' => [['active'], 'default', 'value' => 1],
            'name' => [['name'], 'string', 'max' => 255],
            'iBlockId' => [['iBlockId'], 'integer'],
            'rules' => [['rules'], 'string'],
            'sectionMetaTitle' => [['sectionMetaTitle'], 'string'],
            'sectionMetaKeywords' => [['sectionMetaKeywords'], 'string'],
            'sectionMetaDescription' => [['sectionMetaDescription'], 'string'],
            'sectionMetaPageTitle' => [['sectionMetaPageTitle'], 'string'],
            'sectionMetaPicturePreviewAlt' => [['sectionMetaPicturePreviewAlt'], 'string'],
            'sectionMetaPicturePreviewTitle' => [['sectionMetaPicturePreviewTitle'], 'string'],
            'sectionMetaPictureDetailAlt' => [['sectionMetaPictureDetailAlt'], 'string'],
            'sectionMetaPictureDetailTitle' => [['sectionMetaPictureDetailTitle'], 'string'],
            'elementMetaTitle' => [['elementMetaTitle'], 'string'],
            'elementMetaKeywords' => [['elementMetaKeywords'], 'string'],
            'elementMetaDescription' => [['elementMetaDescription'], 'string'],
            'elementMetaPageTitle' => [['elementMetaPageTitle'], 'string'],
            'elementMetaPicturePreviewAlt' => [['elementMetaPicturePreviewAlt'], 'string'],
            'elementMetaPicturePreviewTitle' => [['elementMetaPicturePreviewTitle'], 'string'],
            'elementMetaPictureDetailAlt' => [['elementMetaPictureDetailAlt'], 'string'],
            'elementMetaPictureDetailTitle' => [['elementMetaPictureDetailTitle'], 'string'],
            'sort' => [['sort'], 'integer'],
            'sortDefault' => [['sort'], 'default', 'value' => 500],
            'unique' => [['code'], 'unique', 'targetAttribute' => ['code']],
            'required' => [['code', 'active'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.id'),
            'code' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.code'),
            'active' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.active'),
            'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.name'),
            'iBlockId' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.iBlockId'),
            'rules' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.rules'),
            'sectionMetaTitle' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.sectionMetaTitle'),
            'sectionMetaKeywords' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.sectionMetaKeywords'),
            'sectionMetaDescription' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.sectionMetaDescription'),
            'sectionMetaPageTitle' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.sectionMetaPageTitle'),
            'sectionMetaPicturePreviewAlt' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.sectionMetaPicturePreviewAlt'),
            'sectionMetaPicturePreviewTitle' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.sectionMetaPicturePreviewTitle'),
            'sectionMetaPictureDetailAlt' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.sectionMetaPictureDetailAlt'),
            'sectionMetaPictureDetailTitle' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.sectionMetaPictureDetailTitle'),
            'elementMetaTitle' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.elementMetaTitle'),
            'elementMetaKeywords' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.elementMetaKeywords'),
            'elementMetaDescription' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.elementMetaDescription'),
            'elementMetaPageTitle' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.elementMetaPageTitle'),
            'elementMetaPicturePreviewAlt' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.elementMetaPicturePreviewAlt'),
            'elementMetaPicturePreviewTitle' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.elementMetaPicturePreviewTitle'),
            'elementMetaPictureDetailAlt' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.elementMetaPictureDetailAlt'),
            'elementMetaPictureDetailTitle' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.elementMetaPictureDetailTitle'),
            'sort' => Loc::getMessage('intec.seo.models.iblock.metadata.template.attributes.sort')
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
     * Возвращает макросы для раздела.
     * @return array
     */
    public function getSectionMacros()
    {
        $result = [];
        $result[] = [
            'code' => 'this',
            'type' => 'group',
            'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.sectionMacros.this.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.sectionMacros.this.items.name'),
                'value' => '{=this.Name}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.sectionMacros.this.items.code'),
                'value' => '{=this.Code}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.sectionMacros.this.items.previewText'),
                'value' => '{=this.PreviewText}'
            ]]
        ];

        $result[] = [
            'code' => 'parent',
            'type' => 'group',
            'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.sectionMacros.parent.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.sectionMacros.parent.items.name'),
                'value' => '{=parent.Name}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.sectionMacros.parent.items.code'),
                'value' => '{=parent.Code}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.sectionMacros.parent.items.previewText'),
                'value' => '{=parent.PreviewText}'
            ]]
        ];

        $result[] = [
            'code' => 'iblock',
            'type' => 'group',
            'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.sectionMacros.iblock.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.sectionMacros.iblock.items.name'),
                'value' => '{=iblock.Name}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.sectionMacros.iblock.items.code'),
                'value' => '{=iblock.Code}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.sectionMacros.iblock.items.previewText'),
                'value' => '{=iblock.PreviewText}'
            ]]
        ];

        if (Loader::includeModule('catalog')) {
            $stores = Arrays::from(StoreTable::getList()->fetchAll());

            if (!$stores->isEmpty()) {
                $part = [
                    'code' => 'stores',
                    'type' => 'group',
                    'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.sectionMacros.stores.name'),
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

        if (Loader::includeModule('intec.regionality')) {
            $part = [
                'code' => 'regionality',
                'type' => 'group',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.sectionMacros.regionality.name'),
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
            'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.sectionMacros.misc.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.sectionMacros.misc.items.sections'),
                'value' => '{=concat this.sections.name " / "}'
            ]]
        ];

        $result[] = $part;

        unset($part);

        $fields = Arrays::fromDBResult(CUserTypeEntity::GetList(['SORT' => 'ASC']));

        if (!$fields->isEmpty()) {
            $part = [
                'code' => 'fields',
                'type' => 'group',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.sectionMacros.fields.name'),
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

        return $result;
    }

    /**
     * Возвращает макросы для элемента.
     * @return array
     */
    public function getElementMacros()
    {
        $result = [];
        $result[] = [
            'code' => 'this',
            'type' => 'group',
            'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.this.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.this.items.name'),
                'value' => '{=this.Name}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.this.items.code'),
                'value' => '{=this.Code}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.this.items.previewText'),
                'value' => '{=this.PreviewText}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.this.items.detailText'),
                'value' => '{=this.DetailText}'
            ]]
        ];

        $result[] = [
            'code' => 'parent',
            'type' => 'group',
            'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.parent.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.parent.items.name'),
                'value' => '{=parent.Name}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.parent.items.code'),
                'value' => '{=parent.Code}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.parent.items.previewText'),
                'value' => '{=parent.PreviewText}'
            ]]
        ];

        $result[] = [
            'code' => 'iblock',
            'type' => 'group',
            'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.iblock.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.iblock.items.name'),
                'value' => '{=iblock.Name}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.iblock.items.code'),
                'value' => '{=iblock.Code}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.iblock.items.previewText'),
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
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.properties.name'),
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
                    'value' => '{=concat {=this.property.'.$code.'} ", "}'
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
                            'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.skuProperties.name'),
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
                                'value' => '{=concat {=distinct this.catalog.sku.property.'.$code.'} ", "}'
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
                            'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.prices.name'),
                            'items' => []
                        ];

                        foreach ($prices as $price) {
                            $part['items'][] = [
                                'code' => $price['NAME'],
                                'type' => 'macro',
                                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.prices.items.price.name', [
                                    '#price#' => !empty($price['NAME_LANG']) ? $price['NAME_LANG'] : $price['NAME']
                                ]),
                                'value' => '{=this.catalog.price.'.$price['NAME'].'}'
                            ];
                        }

                        if (!empty($part['items']))
                            $result[] = $part;

                        $part = [
                            'code' => 'skuPrices',
                            'type' => 'group',
                            'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.skuPrices.name'),
                            'items' => []
                        ];

                        foreach ($prices as $price) {
                            $part['items'][] = [
                                'code' => $price['NAME'],
                                'type' => 'group',
                                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.skuPrices.items.price.name', [
                                    '#price#' => !empty($price['NAME_LANG']) ? $price['NAME_LANG'] : $price['NAME']
                                ]),
                                'items' => [[
                                    'type' => 'macro',
                                    'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.skuPrices.items.price.items.minimal'),
                                    'value' => '{=min this.catalog.sku.price.'.$price['NAME'].'}'
                                ], [
                                    'type' => 'macro',
                                    'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.skuPrices.items.price.items.maximal'),
                                    'value' => '{=max this.catalog.sku.price.'.$price['NAME'].'}'
                                ]]
                            ];
                        }

                        if (!empty($part['items']))
                            $result[] = $part;

                        unset($part);
                    }
                }
            }
        }

        if (Loader::includeModule('catalog')) {
            $stores = Arrays::from(StoreTable::getList()->fetchAll());

            if (!$stores->isEmpty()) {
                $part = [
                    'code' => 'stores',
                    'type' => 'group',
                    'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.stores.name'),
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

        if (Loader::includeModule('intec.regionality')) {
            $part = [
                'code' => 'regionality',
                'type' => 'group',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.regionality.name'),
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
            'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.misc.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.misc.items.sections'),
                'value' => '{=concat this.sections.name " / "}'
            ]]
        ];

        if (Loader::includeModule('catalog')) {
            $part['items'][] = [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.misc.items.stores'),
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
                'name' => Loc::getMessage('intec.seo.models.iblock.metadata.template.elementMacros.fields.name'),
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

        return $result;
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