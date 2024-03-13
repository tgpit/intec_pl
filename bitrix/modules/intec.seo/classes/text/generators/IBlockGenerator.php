<?php
namespace intec\seo\text\generators;

use CIBlock;
use Bitrix\Main\Loader;
use intec\core\base\Exception;
use intec\core\helpers\Type;
use intec\seo\text\Generator;

/**
 * Класс генератора текста с поддержкой инфоблока.
 * Class IBlockGenerator
 * @property integer|null $iBlock
 * @property array|null $data
 * @package intec\seo\text\generators
 */
class IBlockGenerator extends Generator
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
    public function getIBlock()
    {
        return $this->_data !== null && isset($this->_data['ID']) ? $this->_data['ID'] : null;
    }

    /**
     * Устанавливает идентификатор привязанного инфоблока.
     * @param integer|null $value
     * @return $this
     */
    public function setIBlock($value)
    {
        $this->_data = null;

        if ($value !== null && Loader::includeModule('iblock')) {
            $data = CIBlock::GetByID($value)->GetNext(true, false);

            if (!empty($data))
                $this->_data = [
                    'ID' => $data['ID'],
                    'CODE' => $data['CODE'],
                    'NAME' => $data['NAME'],
                    'DESCRIPTION' => $data['DESCRIPTION'],
                    'SECTIONS_NAME' => $data['SECTIONS_NAME'],
                    'SECTION_NAME' => $data['SECTION_NAME'],
                    'ELEMENTS_NAME' => $data['ELEMENTS_NAME'],
                    'ELEMENT_NAME' => $data['ELEMENT_NAME']
                ];
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
            throw new Exception('IBlock not set.');

        foreach ($this->_data as $key => $value)
            $macros[$key] = $value;

        return parent::process($text, $macros);
    }
}