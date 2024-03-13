<?php

namespace intec\core\platform\iblock;

use Bitrix\Main\Loader;
use CIBlock;
use CIBlockElement;
use intec\core\helpers\Type;

/**
 * Класс для выборки элементов
 * @package intec\core\platform\iblock
 * @author imber228@gmail.com
 */
class ElementsQuery extends Query
{
    use QueryElementsTrait;
    use QueryGroupTrait;
    use QuerySectionsTrait;

    /**
     * Активность получения пользовательских свойств элемента
     * @var bool
     */
    protected $_withProperties = true;

    /**
     * Запускает запрос и возвращает его результат
     * @return Elements|mixed
     */
    public function execute()
    {
        $result = new Elements();

        if (!Loader::includeModule('iblock'))
            return $result;

        $converter = new ElementArrayConverter();
        $id = $this->getIBlockId();
        $type = $this->getIBlockType();
        $filter = $this->getFilter();
        $select = $this->getSelect();
        $group = $this->getGroup();
        $sort = $this->getSort();
        $sectionsId = $this->getSectionsId();
        $sectionsCode = $this->getsectionsCode();
        $elementsId = $this->getElementsId();
        $elementsCode = $this->getElementsCode();
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

        if (empty($group) && Type::isArray($group))
            return $databaseResult;

        $databaseResult->SetUrlTemplates(
            $this->getIBlockElementUrlTemplate(),
            $this->getIBlockSectionUrlTemplate(),
            $this->getIBlockRootUrlTemplate()
        );

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

            $element = $converter->convert($element);

            $result->set($element->getId(), $element);
        }

        return $result;
    }

    /**
     * Возвращает активность получения пользовательских свойств элемента
     * @return bool
     */
    public function getWithProperties()
    {
        return $this->_withProperties;
    }

    /**
     * @inheritdoc
     */
    public function getIsGlobal()
    {
        return parent::getIsGlobal() &&
            $this->_sectionsId === null &&
            $this->_sectionsCode === null &&
            $this->_elementsId === null &&
            $this->_elementsCode === null;
    }

    /**
     * Устанавливает шаблоны URL для корня, раздела и элемента инфоблока
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
     * Устанавливает активность получения пользовательских свойств элемента
     * @param bool $value
     * @return $this
     */
    public function setWithProperties($value)
    {
        $this->_withProperties = Type::toBoolean($value);

        return $this;
    }
}