<?php
namespace intec\seo\text\generators;

use CIBlock;
use CIBlockSection;
use Bitrix\Main\Loader;
use intec\core\base\Exception;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\seo\text\Generator;

/**
 * Класс генератора текста с поддержкой раздела инфоблока.
 * Class IBlockSectionGenerator
 * @property integer|null $iBlock
 * @property array|null $data
 * @package intec\seo\text\generators
 */
class IBlockSectionGenerator extends Generator
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
    public function getSection()
    {
        return $this->_data !== null && isset($this->_data['ID']) ? $this->_data['ID'] : null;
    }

    /**
     * Устанавливает идентификатор привязанного инфоблока.
     * @param integer|null $value
     * @return $this
     */
    public function setSection($value)
    {
        $this->_data = null;

        if ($value !== null && Loader::includeModule('iblock')) {
            $data = CIBlockSection::GetList([], ['ID' => $value], false, [
                'IBLOCK_ID'
            ])->GetNext(true, false);

            if (!empty($data)) {
                $data = CIBlockSection::GetList([], ['ID' => $value, 'IBLOCK_ID' => $data['IBLOCK_ID']], false, [
                    'ID',
                    'CODE',
                    'NAME',
                    'DESCRIPTION',
                    'UF_*',
                    'IBLOCK_ID'
                ])->GetNext(true, false);

                $this->_data = [
                    'ID' => $data['ID'],
                    'CODE' => $data['CODE'],
                    'NAME' => $data['NAME'],
                    'DESCRIPTION' => $data['DESCRIPTION'],
                    'IBLOCK' => null,
                    'PROPERTIES' => []
                ];

                foreach ($data as $key => $value) {
                    if (!StringHelper::startsWith($key, 'UF_'))
                        continue;

                    $key = StringHelper::cut($key, 3);
                    $this->_data['PROPERTIES'][$key] = $value;
                }

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

                unset($this->_data['IBLOCK_ID']);
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
            if ($key === 'IBLOCK' || $key === 'PROPERTIES')
                continue;

            $macros[$key] = $value;
        }

        if ($this->_data['IBLOCK'] !== null)
            foreach ($this->_data['IBLOCK'] as $key => $value)
                $macros['IBLOCK_'.$key] = $value;

        foreach ($this->_data['PROPERTIES'] as $key => $value)
            $macros['PROPERTY_'.$key] = Type::isArray($value) ? implode(', ', $value) : $value;

        return parent::process($text, $macros);
    }
}