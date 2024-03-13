<?php
namespace intec\seo\text\generators;

use CIBlock;
use CIBlockSection;
use CIBlockElement;
use Bitrix\Main\Loader;
use intec\core\base\Exception;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\seo\text\Generator;

/**
 * Класс генератора текста с поддержкой элемента инфоблока.
 * Class IBlockElementGenerator
 * @property integer|null $iBlock
 * @property array|null $data
 * @package intec\seo\text\generators
 */
class IBlockElementGenerator extends Generator
{
    /**
     * Значения инфоблока.
     * @var array
     */
    protected $_data;


    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * Возвращает идентификатор привязанного инфоблока.
     * @return integer|null
     */
    public function getElement()
    {
        return $this->_data !== null && isset($this->_data['ID']) ? $this->_data['ID'] : null;
    }

    /**
     * Устанавливает идентификатор привязанного инфоблока.
     * @param integer|null $value
     * @return $this
     */
    public function setElement($value)
    {
        $this->_data = null;

        if ($value !== null && Loader::includeModule('iblock')) {
            $data = CIBlockElement::GetList([], ['ID' => $value], false, false, [
                'IBLOCK_ID'
            ])->GetNext(true, false);

            if (!empty($data)) {
                $data = CIBlockElement::GetList([], ['ID' => $value, 'IBLOCK_ID' => $data['IBLOCK_ID']], false, false, [
                    'ID',
                    'CODE',
                    'NAME',
                    'PREVIEW_TEXT',
                    'DETAIL_TEXT',
                    'IBLOCK_ID',
                    'IBLOCK_SECTION_ID'
                ])->GetNextElement(true, false);

                $properties = $data->GetProperties();
                $data = $data->GetFields();

                $data['DESCRIPTION_PREVIEW'] = $data['PREVIEW_TEXT'];
                $data['DESCRIPTION_DETAIL'] = $data['DETAIL_TEXT'];

                unset($data['PREVIEW_TEXT']);
                unset($data['PREVIEW_TEXT_TYPE']);
                unset($data['DETAIL_TEXT']);
                unset($data['DETAIL_TEXT_TYPE']);

                $data['IBLOCK'] = null;
                $data['SECTION'] = null;
                $data['PROPERTIES'] = [];

                foreach ($properties as $property) {
                    if (empty($property['CODE']) && !Type::isNumeric($property['CODE']))
                        continue;

                    $data['PROPERTIES'][$property['CODE']] = Type::isArray($property['VALUE']) ? implode(', ', $property['VALUE']) : $property['VALUE'];
                }

                $this->_data = $data;

                $data = CIBlock::GetByID($this->_data['IBLOCK_ID'])->GetNext(true, false);

                if (!empty($data))
                    $this->_data['IBLOCK'] = [
                        'ID' => $data['ID'],
                        'CODE' => $data['CODE'],
                        'NAME' => $data['NAME'],
                        'DESCRIPTION' => $data['DESCRIPTION'],
                        'SECTIONS_NAME' => $data['SECTIONS_NAME'],
                        'SECTION_NAME' => $data['SECTION_NAME'],
                        'ELEMENTS_NAME' => $data['ELEMENTS_NAME'],
                        'ELEMENT_NAME' => $data['ELEMENT_NAME']
                    ];

                if (!empty($this->_data['IBLOCK_SECTION_ID'])) {
                    $data = CIBlockSection::GetList([], [
                        'ID' => $this->_data['IBLOCK_SECTION_ID'],
                        'IBLOCK_ID' => $this->_data['IBLOCK_ID']
                    ], false, [
                        'ID',
                        'CODE',
                        'NAME',
                        'DESCRIPTION',
                        'UF_*'
                    ])->GetNext(true, false);

                    if (!empty($data)) {
                        $this->_data['SECTION'] = [
                            'ID' => $data['ID'],
                            'CODE' => $data['CODE'],
                            'NAME' => $data['NAME'],
                            'DESCRIPTION' => $data['DESCRIPTION'],
                            'PROPERTIES' => []
                        ];

                        foreach ($data as $key => $value) {
                            if (!StringHelper::startsWith($key, 'UF_'))
                                continue;

                            $key = StringHelper::cut($key, 3);
                            $this->_data['SECTION']['PROPERTIES'][$key] = $value;
                        }
                    }
                }

                unset($this->_data['IBLOCK_ID']);
                unset($this->_data['IBLOCK_SECTION_ID']);
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function process($text, $macros = [])
    {
        if (!Type::isArray($macros))
            $macros = [];

        if ($this->_data === null)
            throw new Exception('IBlock section not set.');

        foreach ($this->_data as $key => $value) {
            if ($key === 'IBLOCK' || $key === 'SECTION' || $key === 'PROPERTIES')
                continue;

            $macros[$key] = $value;
        }

        if ($this->_data['IBLOCK'] !== null)
            foreach ($this->_data['IBLOCK'] as $key => $value)
                $macros['IBLOCK_'.$key] = $value;

        if ($this->_data['SECTION'] !== null) {
            foreach ($this->_data['SECTION'] as $key => $value) {
                if ($key === 'PROPERTIES')
                    continue;

                $macros['SECTION_'.$key] = $value;
            }

            foreach ($this->_data['SECTION']['PROPERTIES'] as $key => $value)
                $macros['SECTION_PROPERTY_'.$key] = Type::isArray($value) ? implode(', ', $value) : $value;
        }

        foreach ($this->_data['PROPERTIES'] as $key => $value)
            $macros['PROPERTY_'.$key] = Type::isArray($value) ? implode(', ', $value) : $value;

        return parent::process($text, $macros);
    }
}