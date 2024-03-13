<?php
namespace intec\core\platform\iblock;

use intec\core\base\ArrayModel;
use intec\core\helpers\Type;
use intec\core\platform\main\FileQuery;
use intec\core\platform\main\Files;

/**
 * Класс, представляющий раздел инфоблока.
 * Class Section
 * @package intec\core\platform\iblock
 * @author apocalypsisdimon@gmail.com
 */
class Section extends ArrayModel
{
    /**
     * Возвращает код.
     * @return mixed
     */
    public function getCode()
    {
        return $this->_fields['CODE'];
    }

    /**
     * Возвращает уровень вложенности раздела.
     * @return integer
     */
    public function getDepthLevel()
    {
        return Type::toInteger($this->_fields['DEPTH_LEVEL']);
    }

    /**
     * Возвращает файлы из полей.
     * @param array $fields
     * @return Files
     */
    public function getFiles($fields = ['PICTURE', 'DETAIL_PICTURE'])
    {
        $query = new FileQuery();

        foreach ($fields as $field) {
            $value = $this->_fields->get($field);

            if (!Type::isEmpty($value)) {
                if (Type::isArray($value)) {
                    if (isset($value['ID'])) {
                        $query->add($value['ID']);
                    } else {
                        foreach ($value as $part)
                            $query->add($part);
                    }
                } else {
                    $query->add($value);
                }
            }
        }

        return $query->execute();
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
     * Возвращает значение, указывающее на активность раздела.
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->_fields['ACTIVE'] === 'Y';
    }

    /**
     * Возвращает левый отступ раздела в дереве.
     * @return integer
     */
    public function getLeftMargin()
    {
        return Type::toInteger($this->_fields['LEFT_MARGIN']);
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
     * Возвращает правый отступ раздела в дереве.
     * @return integer
     */
    public function getRightMargin()
    {
        return Type::toInteger($this->_fields['RIGHT_MARGIN']);
    }

    /**
     * Возвращает идентификатор раздела.
     * @return integer
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

    /**
     * Возвращает Url адрес раздела.
     * @return string
     */
    public function getUrl()
    {
        return $this->_fields['SECTION_PAGE_URL'];
    }
}
