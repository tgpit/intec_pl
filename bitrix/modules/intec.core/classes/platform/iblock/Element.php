<?php
namespace intec\core\platform\iblock;

use Bitrix\Iblock\PropertyTable;
use intec\core\base\ArrayModel;
use intec\core\helpers\Type;
use intec\core\platform\main\FileQuery;
use intec\core\platform\main\Files;

/**
 * Класс, представляющий элемент инфоблока.
 * Class Element
 * @package intec\core\platform\iblock
 * @author apocalypsisdimon@gmail.com
 */
class Element extends ArrayModel
{
    /**
     * Коллекция свойств элемента инфоблока.
     * @var ElementProperties
     */
    protected $_properties;

    /**
     * Конструктор.
     * Element constructor.
     * @param array $fields
     * @param array $properties
     * @param array $config
     */
    public function __construct(array $fields = [], array $properties = [], array $config = [])
    {
        $this->_properties = new ElementProperties($properties);

        parent::__construct($fields, $config);
    }

    /**
     * Возвращает код.
     * @return string
     */
    public function getCode()
    {
        return $this->_fields['CODE'];
    }

    /**
     * Возвращает Url детальной страницы.
     * @return string
     */
    public function getDetailPageUrl()
    {
        return $this->_fields['DETAIL_PAGE_URL'];
    }

    /**
     * Возвращает файлы из полей и свойств элемента.
     * @param array $fields Поля.
     * @param array|true|null $properties Свойства.
     * @return Files
     */
    public function getFiles($fields = ['PREVIEW_PICTURE', 'DETAIL_PICTURE'], $properties = [])
    {
        $query = new FileQuery();

        foreach ($fields as $field) {
            $value = $this->_fields->get($field);

            if (!Type::isEmpty($value)) {
                if (Type::isArray($value)) {
                    $query->add($value['ID']);
                } else {
                    $query->add($value);
                }
            }
        }

        if ($properties === true) {
            foreach ($this->_properties as $property) {
                /** @var ElementProperty $property */
                if ($property->getType() !== PropertyTable::TYPE_FILE)
                    continue;

                if ($property->getIsMultiple()) {
                    $values = $property->getValue();

                    foreach ($values as $value) {
                        if (!Type::isEmpty($value))
                            $query->add($value);
                    }
                } else {
                    $value = $property->getValue();

                    if (!Type::isEmpty($value))
                        $query->add($value);
                }
            }
        } else if (Type::isArray($properties)) {
            foreach ($properties as $property) {
                /** @var ElementProperty $property */
                $property = $this->_properties->get($property);

                if (empty($property) || $property->getType() !== PropertyTable::TYPE_FILE)
                    continue;

                if ($property->getIsMultiple()) {
                    $values = $property->getValue();

                    foreach ($values as $value) {
                        if (!Type::isEmpty($value))
                            $query->add($value);
                    }
                } else {
                    $value = $property->getValue();

                    if (!Type::isEmpty($value))
                        $query->add($value);
                }
            }
        }

        return $query->execute();
    }

    /**
     * Возвращает идентификатор инфоблока.
     * @return integer
     */
    public function getIblockId()
    {
        return Type::toInteger($this->_fields['IBLOCK_ID']);
    }

    /**
     * Возвращает идентификатор.
     * @return integer
     */
    public function getId()
    {
        return Type::toInteger($this->_fields['ID']);
    }

    /**
     * Возвращает значение, указывающее на активность элемента.
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->_fields['ACTIVE'] === 'Y';
    }

    /**
     * Возвращает Url страницы списка.
     * @return string
     */
    public function getListPageUrl()
    {
        return $this->_fields['LIST_PAGE_URL'];
    }

    /**
     * Возвращает наименование.
     * @return string
     */
    public function getName()
    {
        return $this->_fields['NAME'];
    }

    /**
     * Возвращает коллекцию свойств элемента инфоблока.
     * @return ElementProperties
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * Возвращает идентификатор раздела.
     * @return integer|null
     */
    public function getSectionId()
    {
        $result = $this->_fields['IBLOCK_SECTION_ID'];

        if (!Type::isEmpty($result)) {
            $result = Type::toInteger($result);
        } else {
            $result = null;
        }

        return $result;
    }
}
