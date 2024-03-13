<?php
namespace intec\importexport\models\excel\import;

use Bitrix\Main\Loader;
use Bitrix\Main\Text\Encoding;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\collections\Arrays;

class Properties
{
    private $delimiter = ';';
    private $iBlockProperties = [];
    private $importProperties = [];
    private $elementProperties = [];

    private $settings = [
        'global' => [
            'overwriteProperties' => true, //true - перезаписывать свойства при обновлении, false - не пререзаписывать
            //'overwriteIfEmpty' => true, // true - если новое значение не установленно,то удалять старое, false - то оставлять старое
        ],
        'list' => [
            'loadValue' => 'VALUE', //VALUE, XML_ID
            'indexField' => 'VALUE', //VALUE, XML_ID
            'dontCreateNew' => false, //true - не создовать новые свойства списка, false - создовать новые свойства списка
            'newXmlIdRandom' => false, //true - True - генерирует случайный xml_id длиной в 24 символа, false – создает xml_id транслитерацией, если не получиться то генерирует случайный id.
        ],
        'files' => [
            'nonExistentFile' => true, //true - добавить id файла даже если его не существует, false - не добавлять id файла если его не существует
        ],
        'elements' => [
            'identificationField' => 'ID', //поле для проверки существование элемента (работает только еслии в свойстве выбан инфоблок)
            'nonExistent' => true, //true - добавлять элемент если его не существует, false - не добавлять элеммнет если его не сущетвует
        ],
        'sections' => [
            'identificationField' => 'ID', //поле для проверки существование раздела (работает только еслии в свойстве выбан инфоблок)
            'nonExistent' => true, //true - добавлять раздел если его не существует, false - не добавлять раздел если его не сущетвует /*fix this later*/
        ],
        'users' => [
            //'addNonUserId' => true, //true - доболяет ID несуществующих пользователей, false - не добовляет ID если пользователя несуществует
        ],
        'forum' => [
            //'addNonForumId' => true, //true - доболяет ID несуществующего форума, false - не добовляет ID если форума несуществует
        ]
    ];

    public function __construct($delimiter = null, $importProperties = null, $iBlockProperties = null, $iBlockId = null, $elementProperties = null)
    {
        if (!empty($delimiter))
            $this->setDelimiter($delimiter);

        if (!empty($importProperties))
            $this->setImportProperties($importProperties);

        if (!empty($iBlockProperties) && empty($iBlockId))
            $this->setIBlockProperties($iBlockProperties);
        elseif (!empty($iBlockId))
            $this->setPropertiesFromIBlock($iBlockId);

        if (!empty($elementProperties))
            $this->setElementProperties($elementProperties);
    }

    public function getSettings ()
    {
        return $this->settings;
    }

    public function setSettings ($data)
    {
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $this->setSetting($key, $value);
            }
        }
    }

    public function setSetting ($setting, $data)
    {
        if (!empty($data)) {
            $this->settings[$setting] = $data;
        }
    }

    public function getDelimiter ()
    {
        return $this->delimiter;
    }

    public function setDelimiter ($data)
    {
        if (!empty($data))
            $this->delimiter = $data;
    }

    public function getIBlockProperties ()
    {
        return $this->iBlockProperties;
    }

    public function setIBlockProperties ($data)
    {
        if (!empty($data))
            $this->iBlockProperties = $data;
    }

    public function getImportProperties ()
    {
        return $this->importProperties;
    }

    public function setImportProperties ($data)
    {
        if (!empty($data))
            $this->importProperties = $data;
    }

    public function getElementProperties ()
    {
        return $this->elementProperties;
    }

    public function setElementProperties ($data)
    {
        if (!empty($data))
            $this->elementProperties = $data;
    }

    public function setPropertiesFromIBlock ($iBlockId = null)
    {
        if (empty($iBlockId))
            return false;

        if (!Loader::includeModule('iblock'))
            return false;

        $properties = Arrays::fromDBResult(\CIBlockProperty::GetList([], ['ACTIVE' => 'Y', 'IBLOCK_ID' => $iBlockId]))->asArray();
        $reindexProperties = [];

        foreach ($properties as $key => $property) {
            if (empty($property['CODE'])) {
                $reindexProperties[$property['ID']] = $property;
            } else {
                $reindexProperties[$property['CODE']] = $property;
            }
        }

        $properties = $reindexProperties;

        $this->setIBlockProperties($properties);

        return true;
    }

    public function getProperties ($properties = [])
    {
        if (!empty($properties))
            $this->setImportProperties($properties);

        /* nenf test this later*/
        if (empty($this->delimiter))
            return $this->importProperties;

        $result = [];

        foreach ($this->iBlockProperties as $key => $iBlockProperty) {
            $result[$key] = $this->getProperty($key);
        }

        return $result;
    }

    private function getProperty ($key)
    {
        if (empty($key))
            return false;

        $result = [];
        $value = Encoding::convertEncodingToCurrent($this->importProperties[$key]);
        $description = $this->getDescription($key);
        $propertyType = $this->iBlockProperties[$key]['PROPERTY_TYPE'];
        $propertyUserType = $this->iBlockProperties[$key]['USER_TYPE'];

        if ($propertyType === 'L') {
            if (empty($propertyUserType))
                $result = $this->listProperty($key, $value, $description);
        } elseif ($propertyType === 'S') {
            if (empty($propertyUserType))
                $result = $this->stringProperty($key, $value, $description);
            elseif ($propertyUserType === 'HTML')
                $result = $this->htmlProperty($key, $value, $description);
            elseif ($propertyUserType === 'video')
                $result = $this->videoProperty($key, $value, $description);
            elseif ($propertyUserType === 'Date')
                $result = $this->dateProperty($key, $value, $description);
            elseif ($propertyUserType === 'DateTime')
                $result = $this->dateTimeProperty($key, $value, $description);
            elseif ($propertyUserType === 'Money')
                $result = $this->moneyProperty($key, $value, $description);
            elseif ($propertyUserType === 'map_yandex')
                $result = $this->yandexProperty($key, $value, $description);
            elseif ($propertyUserType === 'map_google')
                $result = $this->googleProperty($key, $value, $description);
            elseif ($propertyUserType === 'UserID')
                $result = $this->toUserProperty($key, $value, $description);
            elseif ($propertyUserType === 'TopicID')
                $result = $this->forumProperty($key, $value, $description);
            elseif ($propertyUserType === 'FileMan')
                $result = $this->fileServerProperty($key, $value, $description);
            elseif ($propertyUserType === 'ElementXmlID')
                $result = $this->elementXmlProperty($key, $value, $description);
            elseif ($propertyUserType === 'directory')
                $result = $this->highloadProperty($key, $value, $description);
        } elseif ($propertyType === 'F') {
            if (empty($propertyUserType))
                $result = $this->fileProperty($key, $value, $description);
        } elseif ($propertyType === 'G') {
            if (empty($propertyUserType))
                $result = $this->sectionProperty($key, $value, $description);
            elseif ($propertyUserType === 'SectionAuto')
                $result = $this->sectionAutoProperty($key, $value, $description);
        } elseif ($propertyType === 'E') {
            if (empty($propertyUserType))
                $result = $this->elementProperty($key, $value, $description);
            elseif ($propertyUserType === 'SKU')
                $result = $this->skuProperty($key, $value, $description);
            elseif ($propertyUserType === 'EList')
                $result = $this->elementListProperty($key, $value, $description);
            elseif ($propertyUserType === 'EAutocomplete')
                $result = $this->elementAutoProperty($key, $value, $description);
        } elseif ($propertyType === 'N') {
            if (empty($propertyUserType))
                $result = $this->numberProperty($key, $value, $description);
            elseif ($propertyUserType === 'Sequence')
                $result = $this->counterProperty($key, $value, $description);
        }

        /*if ($this->importProperties[$key . '_DESCRIPTION']) {
            $description = $this->importProperties[$key . '_DESCRIPTION'];
        }*/

        $result = $this->mergeDescription($result, $description);

        if ((!empty($result) && $result !== false) || ($result !== 0))
            return $result;
        else
            return [];
    }

    private function getDescription ($key)
    {
        if (empty($key))
            return [];

        $value = $this->importProperties[$key . '_DESCRIPTION'];
        $description = $this->elementProperties[$key]['DESCRIPTION'];

        if (empty($value)) {
            if (!empty($description))
                return $description;
        }

        $result = [];

        if ($this->multiplyCheck($key)) {
            $explodeValues = explode($this->delimiter, $value);

            foreach ($explodeValues as $explodeValue) {
                $result[] = $explodeValue;
            }
        } else {
            return $result[] = $value;
        }

        if (!$this->settings['global']['overwriteProperties']) {
            $elementProperties = $this->elementProperties[$key]['VALUE'];

            if (!empty($elementProperties))
                $result = array_merge($result, $elementProperties);

            $result = array_unique($result);
        }

        if (!empty($result) && $result !== false)
            return $result;
        else
            return [];
    }

    private function mergeDescription ($value, $description)
    {
        if (empty($value) || empty($description))
            return $value;

        $result = [];

        if (Type::isArray($value)) {
            foreach ($value as $key => $item) {
                $result[$key] = [
                    'VALUE' => $item,
                    'DESCRIPTION' => $description[$key]
                ];
            }
        } else {
            $result[] = [
                'VALUE' => $value,
                'DESCRIPTION' => $description
            ];
        }

        return $result;
    }



    private function createListEnum ($propertyId, $value)
    {
        if (empty($propertyId) || empty($value))
            return false;

        $fields = [
            'PROPERTY_ID' => $propertyId
        ];

        if ($this->settings['list']['loadValue'] === 'VALUE') {
            $params = [
                'max_len' => 100,
                'change_case' => 'L',
                'replace_space' => '_',
                'replace_other' => '_',
                'delete_repeat_replace' => true,
                'safe_chars' => ''
            ];

            $fields['VALUE'] = $value;
            $newXmlId = \CUtil::translit($value, 'ru', $params);

            if (empty($newXmlId) || $this->settings['list']['newXmlIdRandom'])
                $newXmlId = \Bitrix\Main\Security\Random::getString(24);

            $fields['XML_ID'] = $newXmlId;
            $fields['EXTERNAL_ID'] = $newXmlId;
        } elseif ($this->settings['list']['loadValue'] === 'XML_ID') {
            $fields['VALUE'] = $value;
            $fields['XML_ID'] = $value;
            $fields['EXTERNAL_ID'] = $value;
        }

        $enum = new \CIBlockPropertyEnum;

        $property =  Arrays::fromDBResult(\CIBlockPropertyEnum::GetList(
            [],
            ['PROPERTY_ID' => $fields['PROPERTY_ID'], 'XML_ID' => $fields['XML_ID']]
        ))->asArray();

        if (!empty($property)) {
            $fields['XML_ID'] = \Bitrix\Main\Security\Random::getString(24);
            $fields['EXTERNAL_ID'] = $fields['XML_ID'];
        }

        return $enum->Add($fields);
    }

    private function multiplyCheck ($property)
    {
        return $this->iBlockProperties[$property]['MULTIPLE'] === 'Y';
    }

    private function filesExistCheck ($fileIds, $onlyId = true)
    {
        if (empty($fileIds))
            return [];

        $result = Arrays::fromDBResult(\CFile::GetList([], ['@ID' => $fileIds]))->indexBy('ID')->asArray();

        if ($onlyId) {
            $subResult = [];

            foreach ($result as $item) {
                $subResult[] = $item['ID'];
            }

            $result = $subResult;
        }

        return $result;
    }

    private function filesExistCheckBool ($fileIds, $onlyExist = true)
    {
        if (empty($fileIds))
            return false;

        $subResult = [];
        $result = Arrays::fromDBResult(\CFile::GetList([], ['@ID' => $fileIds]))->indexBy('ID')->asArray();

        foreach ($fileIds as $item) {
            if ($onlyExist) {
                if (!empty($result[$item]))
                    $subResult[$item] = true;
            } else {
                $subResult[$item] = !empty($result[$item]);
            }
        }

        $result = $subResult;

        return $result;
    }

    private function elementExistCheck ($key, $values, $byXmlId = false)
    {
        if (empty($values) || empty($key) /*|| !CModule::IncludeModule('iblock')*/)
            return $values;

        if ($byXmlId)
            $field = 'XML_ID';
        else
            $field = $this->settings['elements']['identificationField'];

        $result = [];

        $filter = [
            $field => $values
        ];

        if (!empty($this->elementProperties[$key]['LINK_IBLOCK_ID']))
            $filter['IBLOCK_ID'] = $this->elementProperties[$key]['LINK_IBLOCK_ID'];

        $select = [
            'IBLOCK_ID',
            'ID',
            'NAME',
            $field
        ];
        $select = array_unique($select);

        $items = Arrays::fromDBResult(
            \CIBlockElement::GetList(
                ["SORT"=>"ASC"],
                $filter,
                false,
                false,
                $select
            )
        )->indexBy('ID')->asArray();

        if (!empty($items)) {
            foreach ($items as $item) {
                $result[] = $item['ID'];
            }
        }

        return $result;
    }

    /* so bad. fix this.*/
    private function sectionExistCheck ($key, $values)
    {
        if (empty($values) || empty($key) /*|| !CModule::IncludeModule('iblock')*/)
            return $values;

        $field = $this->settings['sections']['identificationField'];
        $result = [];

        $filter = [
            $field => $values
        ];

        if (!empty($this->elementProperties[$key]['LINK_IBLOCK_ID']))
            $filter['IBLOCK_ID'] = $this->elementProperties[$key]['LINK_IBLOCK_ID'];

        $select = [
            'IBLOCK_ID',
            'ID',
            'NAME',
            $field
        ];
        $select = array_unique($select);

        if (Type::isArray($values)) {
            foreach ($values as $value) {
                $filter = [
                    $field => $value
                ];
                $item = ArrayHelper::getFirstValue(Arrays::fromDBResult(
                    \CIBlockSection::GetList(
                        ["SORT"=>"ASC"],
                        $filter,
                        false,
                        false,
                        $select
                    )
                )->indexBy('ID')->asArray());

                if (!empty($item))
                    $result[] = $item['ID'];
            }
        } else {
            $items = Arrays::fromDBResult(
                \CIBlockSection::GetList(
                    ["SORT"=>"ASC"],
                    $filter,
                    false,
                    false,
                    $select
                )
            )->indexBy('ID')->asArray();
        }

        return $result;
    }

    private function listProperty ($key, $value, $description = null)
    {
        if (empty($key))
            return [];

        if (empty($value)) {
            if (!empty($this->elementProperties[$key]))
                return $this->elementProperties[$key]['VALUE_ENUM_ID'];
        }

        $result = [];
        $maxLength = 99; // about 99. if more then errors
        $allValues = Arrays::fromDBResult(\CIBlockProperty::GetPropertyEnum($this->iBlockProperties[$key]['ID'], [], []))->asArray();
        $isMultiply = $this->multiplyCheck($key);

        if ($isMultiply) {
            $explodeValues = explode($this->delimiter, $value);

            foreach ($explodeValues as $explodeValue) {
                $hasProperty = false;

                if (StringHelper::length($explodeValue) > $maxLength)
                    $explodeValue = StringHelper::cut($explodeValue,0, $maxLength);

                foreach ($allValues as $allValue) {
                    if ($allValue[$this->settings['list']['indexField']] == $explodeValue) {
                        $result[] = $allValue['ID'];
                        $hasProperty = true;
                        break;
                    }
                }

                if (!$this->settings['list']['dontCreateNew'] && !$hasProperty) {
                    $result[] = $this->createListEnum($this->iBlockProperties[$key]['ID'], $explodeValue);
                }
            }
        } else {
            $hasProperty = false;

            if (StringHelper::length($value) > $maxLength)
                $value = StringHelper::cut($value,0, $maxLength);

            foreach ($allValues as $allValue) {
                if ($allValue[$this->settings['list']['indexField']] == $value) {
                    $result[] = $allValue['ID'];
                    $hasProperty = true;
                    break;
                }
            }

            if (!$this->settings['list']['dontCreateNew'] && !$hasProperty) {
                $result[] = $this->createListEnum($this->iBlockProperties[$key]['ID'], $value);
            }
        }

        $elementProperties = $this->elementProperties[$key]['VALUE_XML_ID'];

        if (!empty($elementProperties) && $isMultiply) {
            $result = array_merge($result, $elementProperties);
            $result = array_unique($result);
        }

        return $result;
    }

    private function stringProperty ($key, $value, $description = null)
    {
        if (empty($key))
            return [];

        if (empty($value)) {
            if (!empty($this->elementProperties[$key]['VALUE']))
                return $this->elementProperties[$key]['VALUE'];
        }

        $result = [];

        if ($this->multiplyCheck($key)) {
            $explodeValues = explode($this->delimiter, $value);

            foreach ($explodeValues as $explodeValue) {
                $result[] = $explodeValue;
            }
        } else {
            return $result[] = $value;
        }

        if (!$this->settings['global']['overwriteProperties']) {
            $elementProperties = $this->elementProperties[$key]['VALUE'];

            if (!empty($elementProperties))
                $result = array_merge($result, $elementProperties);

            $result = array_unique($result);
        }

        return $result;
    }

    private function numberProperty ($key, $value, $description = null)
    {
        if (empty($key))
            return [];

        if (!$this->multiplyCheck($key) && (is_scalar($value) && strlen($value)))
            $value = Type::toFloat($value);

        if (empty($value) && !(is_scalar($value) && strlen($value))) {
            if (!empty($this->elementProperties[$key]['VALUE']))
                return $this->elementProperties[$key]['VALUE'];
        }

        $result = [];

        if ($this->multiplyCheck($key)) {
            $explodeValues = explode($this->delimiter, $value);

            foreach ($explodeValues as $explodeValue) {
                $formatted = Type::toFloat($explodeValue);

                if (!empty($formatted))
                    $result[] = Type::toFloat($explodeValue);
            }

        } else {
            return $result[] = $value;
        }

        if (!$this->settings['global']['overwriteProperties']) {
            $elementProperties = $this->elementProperties[$key]['VALUE'];

            if (!empty($elementProperties))
                $result = array_merge($result, $elementProperties);

            $result = array_unique($result);
        }

        return $result;
    }

    private function fileProperty ($key, $value, $description = null)
    {
        if (empty($key))
            return [];

        if (!ArrayHelper::keyExists($key, $this->importProperties))
            return [];

        $result = [];
        $deleteOldFile = true;

        if ($this->multiplyCheck($key)) {
            $explodeValues = explode($this->delimiter, $value);

            if (!$this->settings['files']['nonExistentFile']) {
                $result = $this->filesExistCheck($value);
            } else {
                foreach ($explodeValues as $explodeValue) {
                    $result[] = \CFile::MakeFileArray($explodeValue);
                }
            }
        } else {
            if (!$this->settings['files']['nonExistentFile']) {
                $result = $this->filesExistCheck($value);
                $result = ArrayHelper::getFirstValue($result);
            } else {
                $result = \CFile::MakeFileArray($value);
            }
        }

        if ($deleteOldFile) {
            foreach ($this->elementProperties[$key]['PROPERTY_VALUE_ID'] as $id) {
                $result[$id] = ["del"=>"Y"];
            }
        }

        return $result;
    }

    private function elementProperty ($key, $value, $description = null, $byXmlId = false)
    {
        if (empty($key))
            return [];

        if (empty($value)) {
            if (!empty($this->elementProperties[$key]['VALUE']))
                return $this->elementProperties[$key]['VALUE'];
        }

        $result = [];

        if ($this->multiplyCheck($key)) {
            $explodeValues = explode($this->delimiter, $value);

            if ($this->settings['elements']['nonExistent'])
                $result = $explodeValues;
            else
                $result = $this->elementExistCheck($key, $explodeValues, $byXmlId);
        } else {
            if ($this->settings['elements']['nonExistent'])
                $result = $value;
            else
                $result = ArrayHelper::getFirstValue($this->elementExistCheck($key, $value, $byXmlId));
        }

        if (!empty($result))
            return $result;
        else
            return $this->elementProperties[$key]['VALUE'];
    }

    private function sectionProperty ($key, $value, $description = null)
    {
        if (empty($key))
            return [];

        if (empty($value)) {
            if (!empty($this->elementProperties[$key]['VALUE']))
                return $this->elementProperties[$key]['VALUE'];
        }

        $result = [];

        if ($this->multiplyCheck($key)) {
            $explodeValues = explode($this->delimiter, $value);

            if ($this->settings['sections']['nonExistent'])
                $result = $explodeValues;
            else
                $result = $this->sectionExistCheck($key, $explodeValues);
        } else {
            if ($this->settings['sections']['nonExistent'])
                $result = $value;
            else
                $result = ArrayHelper::getFirstValue($this->sectionExistCheck($key, $value));
        }

        if (!empty($result))
            return $result;
        else
            return $this->elementProperties[$key]['VALUE'];
    }

    private function htmlProperty ($key, $value, $description = null)
    {
        if (empty($key))
            return [];

        if (empty($value)) {
            if (!empty($this->elementProperties[$key]['VALUE']))
                return ['VALUE' => $this->elementProperties[$key]['~VALUE']];
        }

        $result = [];

        if ($this->multiplyCheck($key)) {
            $explodeValues = explode($this->delimiter, $value);

            foreach ($explodeValues as $explodeValue) {
                $result[] = [
                    'TEXT' => html_entity_decode($explodeValue),
                    'TYPE' => 'HTML'
                ];
            }
        } else {
            return $result[] = [
                'VALUE' => [
                    'TEXT' => html_entity_decode($value),
                    'TYPE' => 'HTML'
                ]
            ];
        }

        $elementProperties = $this->elementProperties[$key]['VALUE'];

        if (!empty($elementProperties))
            $result = array_merge($result, $elementProperties);

        if (!empty($result)) {
            $unique = ['HTML' => [], 'TEXT' => []];

            foreach ($result as $key => $item) {
                if ($item['TYPE'] === 'HTML')
                    $unique['HTML'][] = html_entity_decode($item['TEXT']);
                else
                    $unique['TEXT'][] = html_entity_decode($item['TEXT']);
            }

            $result = [];

            if (!empty($unique['HTML'])) {
                $unique['HTML'] = array_unique($unique['HTML']);

                foreach ($unique['HTML'] as $item) {
                    $result[] = ['TEXT' => $item, 'TYPE' => 'HTML'];
                }
            }

            if (!empty($unique['TEXT'])) {
                $unique['TEXT'] = array_unique($unique['TEXT']);

                foreach ($unique['TEXT'] as $item) {
                    $result[] = ['TEXT' => $item, 'TYPE' => 'TEXT'];
                }
            }
        }

        return $result;
    }

    private function videoProperty ($key, $value, $description = null)
    {
        if (empty($key))
            return [];

        if (empty($value)) {
            if (!empty($this->elementProperties[$key]['VALUE']))
                return $this->elementProperties[$key]['VALUE'];
        }

        $result = [];

        if ($this->multiplyCheck($key)) {
            $explodeValues = explode($this->delimiter, $value);

            foreach ($explodeValues as $explodeValue) {
                $result[] = [
                    'path' => $explodeValue,
                    'width' => 400,
                    'height' => 300,
                    'title' => '',
                    'duration' => '',
                    'author' => '',
                    'date' => '',
                    'desc' => ''
                ];
            }

        } else {
            $result = [[
                'path' => $value,
                'width' => 400,
                'height' => 300,
                'title' => '',
                'duration' => '',
                'author' => '',
                'date' => '',
                'desc' => ''
            ]];
        }

        if (!empty($result))
            return $result;
        else
            return $this->elementProperties[$key]['VALUE'];
    }

    private function dateProperty ($key, $value, $description = null)
    {
        return $this->datePropertyCompilation($key, $value);
    }

    private function dateTimeProperty ($key, $value, $description = null)
    {
        return $this->datePropertyCompilation($key, $value);
    }

    private function datePropertyCompilation ($key, $value)
    {
        if (empty($key))
            return [];

        if (empty($value)) {
            if (!empty($this->elementProperties[$key]['VALUE']))
                return $this->elementProperties[$key]['VALUE'];
        }

        $result = [];

        if ($this->multiplyCheck($key)) {
            $explodeValues = explode($this->delimiter, $value);

            foreach ($explodeValues as $explodeValue) {
                $result[] = $explodeValue;
            }

        } else {
            return $result[] = $value;
        }

        if (!$this->settings['global']['overwriteProperties']) {
            $elementProperties = $this->elementProperties[$key]['VALUE'];

            if (!empty($elementProperties))
                $result = array_merge($result, $elementProperties);

            $result = array_unique($result);
        }

        return $result;
    }

    private function moneyProperty ($key, $value, $description = null)
    {
        if (empty($key))
            return [];

        if (empty($value)) {
            if (!empty($this->elementProperties[$key]['VALUE']))
                return $this->elementProperties[$key]['VALUE'];
        }

        $result = [];

        if ($this->multiplyCheck($key)) {
            $explodeValues = explode($this->delimiter, $value);

            foreach ($explodeValues as $explodeValue) {
                $result[] = $explodeValue;
            }

        } else {
            return $result[] = $value;
        }

        if (!$this->settings['global']['overwriteProperties']) {
            $elementProperties = $this->elementProperties[$key]['VALUE'];

            if (!empty($elementProperties))
                $result = array_merge($result, $elementProperties);

            $result = array_unique($result);
        }

        return $result;
    }

    private function yandexProperty ($key, $value, $description = null)
    {
        return $this->mapProperty($key, $value);
    }

    private function googleProperty ($key, $value, $description = null)
    {
        return $this->mapProperty($key, $value);
    }

    private function mapProperty ($key, $value)
    {
        if (empty($key))
            return [];

        if (empty($value)) {
            if (!empty($this->elementProperties[$key]['VALUE']))
                return $this->elementProperties[$key]['VALUE'];
        }

        $result = [];

        if ($this->multiplyCheck($key)) {
            $explodeValues = explode($this->delimiter, $value);

            foreach ($explodeValues as $explodeValue) {
                $result[] = $explodeValue;
            }

        } else {
            return $result[] = $value;
        }

        if (!$this->settings['global']['overwriteProperties']) {
            $elementProperties = $this->elementProperties[$key]['VALUE'];

            if (!empty($elementProperties))
                $result = array_merge($result, $elementProperties);

            $result = array_unique($result);
        }

        return $result;
    }

    private function toUserProperty ($key, $value, $description = null)
    {
        if (empty($key))
            return [];

        if (empty($value)) {
            if (!empty($this->elementProperties[$key]['VALUE']))
                return $this->elementProperties[$key]['VALUE'];
        }

        $result = [];


        if ($this->multiplyCheck($key)) {
            $explodeValues = explode($this->delimiter, $value);

            foreach ($explodeValues as $explodeValue) {
                $result[] = $explodeValue;
            }

        } else {
            return $result[] = $value;
        }

        if (!$this->settings['global']['overwriteProperties']) {
            $elementProperties = $this->elementProperties[$key]['VALUE'];

            if (!empty($elementProperties))
                $result = array_merge($result, $elementProperties);

            $result = array_unique($result);
        }

        return $result;
    }

    private function sectionAutoProperty ($key, $value, $description = null)
    {
        return $this->sectionProperty($key, $value);
    }

    private function forumProperty ($key, $value, $description = null)
    {
        if (empty($key))
            return [];

        if (empty($value)) {
            if (!empty($this->elementProperties[$key]['VALUE']))
                return $this->elementProperties[$key]['VALUE'];
        }

        $result = [];

        if ($this->multiplyCheck($key)) {
            $explodeValues = explode($this->delimiter, $value);

            foreach ($explodeValues as $explodeValue) {
                $result[] = $explodeValue;
            }

        } else {
            return $result[] = $value;
        }

        if (!$this->settings['global']['overwriteProperties']) {
            $elementProperties = $this->elementProperties[$key]['VALUE'];

            if (!empty($elementProperties))
                $result = array_merge($result, $elementProperties);

            $result = array_unique($result);
        }

        return $result;
    }

    private function skuProperty ($key, $value, $description = null)
    {
        return $this->elementProperty($key, $value);
    }

    private function fileServerProperty ($key, $value, $description = null)
    {
        if (empty($key))
            return [];

        if (empty($value)) {
            if (!empty($this->elementProperties[$key]['VALUE']))
                return $this->elementProperties[$key]['VALUE'];
        }

        $result = [];

        if ($this->multiplyCheck($key)) {
            $explodeValues = explode($this->delimiter, $value);

            foreach ($explodeValues as $explodeValue) {
                $result[] = $explodeValue;
            }

        } else {
            return $result[] = $value;
        }

        if (!$this->settings['global']['overwriteProperties']) {
            $elementProperties = $this->elementProperties[$key]['VALUE'];

            if (!empty($elementProperties))
                $result = array_merge($result, $elementProperties);

            $result = array_unique($result);
        }

        return $result;
    }

    private function elementListProperty ($key, $value, $description = null)
    {
        return $this->elementProperty($key, $value);
    }

    private function elementXmlProperty ($key, $value, $description = null)
    {
        return $this->elementProperty($key, $value, true);
    }

    private function elementAutoProperty ($key, $value, $description = null)
    {
        return $this->elementProperty($key, $value);
    }

    private function highloadProperty ($key, $value, $description = null)
    {
        if (empty($key))
            return [];

        if (empty($value)) {
            if (!empty($this->elementProperties[$key]['VALUE']))
                return $this->elementProperties[$key]['VALUE'];
        }

        $result = [];

        if ($this->multiplyCheck($key)) {
            $explodeValues = explode($this->delimiter, $value);

            foreach ($explodeValues as $explodeValue) {
                $result[] = $explodeValue;
            }

        } else {
            return $result[] = $value;
        }

        if (!$this->settings['global']['overwriteProperties']) {
            $elementProperties = $this->elementProperties[$key]['VALUE'];

            if (!empty($elementProperties))
                $result = array_merge($result, $elementProperties);

            $result = array_unique($result);
        }

        return $result;
    }

    private function counterProperty ($key, $value, $description = null)
    {
        if (empty($key))
            return [];

        if (empty($value)) {
            if (!empty($this->elementProperties[$key]['VALUE']))
                return $this->elementProperties[$key]['VALUE'];
        }

        $result = [];

        if ($this->multiplyCheck($key)) {
            $explodeValues = explode($this->delimiter, $value);

            foreach ($explodeValues as $explodeValue) {
                $result[] = $explodeValue;
            }

        } else {
            return $result[] = $value;
        }

        if (!$this->settings['global']['overwriteProperties']) {
            $elementProperties = $this->elementProperties[$key]['VALUE'];

            if (!empty($elementProperties))
                $result = array_merge($result, $elementProperties);

            $result = array_unique($result);
        }

        return $result;
    }

    private function descriptionProperty ($key, $description)
    {
        if (empty($key) || empty($description))
            return false;


    }
}