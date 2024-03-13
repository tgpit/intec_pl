<?php

namespace intec\core\bitrix\iblock;

use CIBlock;
use CIBlockElement;
use Bitrix\Main\Loader;
use intec\core\helpers\Type;

/**
 * Class ElementsQuery
 * @package intec\core\bitrix\iblock
 * @deprecated
 */
class ElementsQuery extends Query
{
    use ElementsQueryTrait;
    use GroupQueryTrait;
    use LimitQueryTrait;
    use SectionsQueryTrait;

    /**
     * Получать элементы вместе со свойствами.
     * @var boolean
     */
    protected $_withProperties = true;

    /**
     * Автоматическое преобразовывать свойства HTML/Text
     * @var boolean
     */
    protected $_textToHtmlAuto = true;

    /**
     * Получать сырые значения
     * @var boolean
     */
    protected $_useTilda = false;

    /**
     * Запускает запрос и возвращает результат в виде списка элементов.
     * @return Elements
     */
    public function execute()
    {
        $result = new Elements();

        if (!Loader::includeModule('iblock'))
            return $result;

        $id = $this->getIBlockId();
        $type = $this->getIBlockType();
        $filter = $this->getFilter();
        $select = $this->getSelect();
        $group = $this->getGroup();
        $sort = $this->getSort();
        $sectionsId = $this->getIBlockSectionsId();
        $sectionsCode = $this->getIBlockSectionsCode();
        $elementsId = $this->getIBlockElementsId();
        $elementsCode = $this->getIBlockElementsCode();
        $navigation = [];
        $limit = $this->getLimit();
        $offset = $this->getOffset();
        $withProperties = $this->getWithProperties();

        if ($filter === null)
            $filter = [];

        if ($sort === null)
            $sort = [];

        if ($select === null)
            $select = [];

        if ($group === null)
            $group = false;

        if ($limit !== null)
            $navigation['nPageSize'] = $limit;

        if ($offset !== null)
            $navigation['iNumPage'] = $offset;

        if ($type !== null)
            $filter['IBLOCK_TYPE'] = $type;

        if ($id !== null)
            $filter['IBLOCK_ID'] = $id;

        if ($sectionsId !== null)
            $filter['SECTION_ID'] = $sectionsId;

        if ($sectionsCode !== null)
            $filter['SECTION_CODE'] = $sectionsCode;

        if ($elementsId !== null)
            $filter['ID'] = $elementsId;

        if ($elementsCode !== null)
            $filter['CODE'] = $elementsCode;

        if (empty($navigation))
            $navigation = false;

        $databaseResult = CIBlockElement::GetList($sort, $filter, $group, $navigation, $select);
        $databaseResult->SetUrlTemplates($this->getIBlockElementUrlTemplate(), $this->getIBlockSectionUrlTemplate(), $this->getIBlockRootUrlTemplate());

        while ($elementResult = $databaseResult->GetNextElement($this->getTextToHtmlAuto(), $this->getUseTilda())) {
            $element = $elementResult->GetFields();

            if ($withProperties)
                $element['PROPERTIES'] = $elementResult->GetProperties();

            $buttons = CIBlock::GetPanelButtons(
                $element['IBLOCK_ID'],
                $element['ID'],
                $element['SECTION_ID'], [
                    'SECTION_BUTTONS' => false,
                    'SESSID' => false,
                    'CATALOG' => true
                ]
            );

            $element['EDIT_LINK'] = $buttons['edit']['edit_element']['ACTION_URL'];
            $element['DELETE_LINK'] = $buttons['edit']['delete_element']['ACTION_URL'];

            $result->set($element['ID'], $element);
        }

        return $result;
    }

    /**
     * Признак получения элементов вместе с их свойствами.
     * @return boolean
     */
    public function getWithProperties()
    {
        return $this->_withProperties;
    }

    /**
     * Признак автоматического преобразования свойств HTML/Text
     * @return boolean
     */
    public function getTextToHtmlAuto()
    {
        return $this->_textToHtmlAuto;
    }

    /**
     * Признак получения сырых значений
     * @return boolean
     */
    public function getUseTilda()
    {
        return $this->_useTilda;
    }

    /**
     * @inheritdoc
     */
    public function getIsGlobal()
    {
        return parent::getIsGlobal() &&
            $this->_iBlockSectionsId === null &&
            $this->_iBlockSectionsCode === null &&
            $this->_iBlockElementsId === null &&
            $this->_iBlockSectionsCode === null;
    }

    /**
     * Устанавливает шаблоны URL инфоблока.
     * @param string|null $root
     * @param string|null $section
     * @param string|null $element
     * @return $this
     */
    public function setIBlockUrlTemplates($root = null, $section = null, $element = null)
    {
        $this->setIBlockRootUrlTemplate($root);
        $this->setIBlockSectionUrlTemplate($section);
        $this->setIBlockElementUrlTemplate($element);

        return $this;
    }

    /**
     * Устанавливает признак получения элементов вместе с их свойствами.
     * @param boolean $value
     * @return static
     */
    public function setWithProperties($value)
    {
        $this->_withProperties = Type::toBoolean($value);

        return $this;
    }

    /**
     * Устанавливает признак автоматического преобразования свойств HTML/Text
     * @param boolean $value
     * @return static
     */
    public function setTextToHtmlAuto($value)
    {
        $this->_textToHtmlAuto = Type::toBoolean($value);

        return $this;
    }

    /**
     * Устанавливает признак получения сырых значений
     * @param boolean $value
     * @return static
     */
    public function setUseTilda($value)
    {
        $this->_useTilda = Type::toBoolean($value);

        return $this;
    }
}