<?php
namespace intec\seo\models\iblocks\elements\names;

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
use intec\seo\models\iblocks\elements\names\template\Section;
use intec\seo\models\iblocks\elements\names\template\Site;

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
        return 'seo_iblocks_elements_names_templates';
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
            'value' => [['value'], 'string'],
            'quantity' => [['quantity'], 'integer'],
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
            'id' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.attributes.id'),
            'code' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.attributes.code'),
            'active' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.attributes.active'),
            'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.attributes.name'),
            'iBlockId' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.attributes.iBlockId'),
            'value' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.attributes.value'),
            'quantity' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.attributes.quantity'),
            'sort' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.attributes.sort')
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
     * Возвращает макросы.
     * @return array
     */
    public function getMacros()
    {
        $result = [];
        $result[] = [
            'code' => 'this',
            'type' => 'group',
            'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.this.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.this.items.name'),
                'value' => '{=this.Name}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.this.items.code'),
                'value' => '{=this.Code}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.this.items.previewText'),
                'value' => '{=this.PreviewText}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.this.items.detailText'),
                'value' => '{=this.DetailText}'
            ]]
        ];

        $result[] = [
            'code' => 'parent',
            'type' => 'group',
            'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.parent.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.parent.items.name'),
                'value' => '{=parent.Name}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.parent.items.code'),
                'value' => '{=parent.Code}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.parent.items.previewText'),
                'value' => '{=parent.PreviewText}'
            ]]
        ];

        $result[] = [
            'code' => 'iblock',
            'type' => 'group',
            'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.iblock.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.iblock.items.name'),
                'value' => '{=iblock.Name}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.iblock.items.code'),
                'value' => '{=iblock.Code}'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.iblock.items.previewText'),
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
                'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.properties.name'),
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
                            'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.skuProperties.name'),
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
                            'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.prices.name'),
                            'items' => []
                        ];

                        foreach ($prices as $price) {
                            $part['items'][] = [
                                'code' => $price['NAME'],
                                'type' => 'macro',
                                'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.prices.items.price.name', [
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
                            'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.skuPrices.name'),
                            'items' => []
                        ];

                        foreach ($prices as $price) {
                            $part['items'][] = [
                                'code' => $price['NAME'],
                                'type' => 'group',
                                'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.skuPrices.items.price.name', [
                                    '#price#' => !empty($price['NAME_LANG']) ? $price['NAME_LANG'] : $price['NAME']
                                ]),
                                'items' => [[
                                    'type' => 'macro',
                                    'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.skuPrices.items.price.items.minimal'),
                                    'value' => '{=min this.catalog.sku.price.'.$price['NAME'].'}'
                                ], [
                                    'type' => 'macro',
                                    'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.skuPrices.items.price.items.maximal'),
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
                    'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.stores.name'),
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
                'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.regionality.name'),
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
            'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.misc.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.misc.items.sections'),
                'value' => '{=concat this.sections.name " / "}'
            ]]
        ];

        if (Loader::includeModule('catalog')) {
            $part['items'][] = [
                'type' => 'macro',
                'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.misc.items.stores'),
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
                'name' => Loc::getMessage('intec.seo.models.iblock.elements.names.template.macros.fields.name'),
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
     * Возвращает значение в виде массива.
     * @return array
     */
    public function getValue()
    {
        return $this->getStringAttributeAsArray('value');
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