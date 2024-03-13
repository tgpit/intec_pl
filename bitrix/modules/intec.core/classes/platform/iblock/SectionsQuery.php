<?php

namespace intec\core\platform\iblock;

use Bitrix\Main\Loader;
use CIBlock;
use CIBlockSection;
use intec\core\helpers\Type;

/**
 * Класс для выборки разделов инфоблока
 * @package intec\core\platform\iblock
 * @author imber228@gmail.com
 */
class SectionsQuery extends Query
{
    use QuerySectionsTrait;

    /**
     * Активность подсчета количества
     * @var bool
     */
    protected $_useQuantity = false;

    /**
     * Запускает запрос и возвращает его результат
     * @return Sections|mixed
     */
    public function execute()
    {
        $result = new Sections();

        if (!Loader::includeModule('iblock'))
            return $result;

        $converter = new SectionArrayConverter();
        $filter = $this->getFilter();
        $id = $this->getIBlockId();
        $limit = $this->getLimit();
        $navigation = [];
        $offset = $this->getOffset();
        $sectionsCode = $this->getSectionsCode();
        $sectionsId = $this->getSectionsId();
        $select = $this->getSelect();
        $sort = $this->getSort();
        $type = $this->getIBlockType();

        if ($filter === null)
            $filter = [];

        if ($sort === null)
            $sort = [];

        if ($select === null)
            $select = [];

        if ($limit !== null)
            $navigation['nPageSize'] = $limit;

        if ($offset !== null)
            $navigation['iNumPage'] = $offset;

        if ($type !== null)
            $filter['IBLOCK_TYPE'] = $type;

        if ($id !== null)
            $filter['IBLOCK_ID'] = $id;

        if ($sectionsId !== null)
            $filter['ID'] = $sectionsId;

        if ($sectionsCode !== null)
            $filter['CODE'] = $sectionsCode;

        if (empty($navigation))
            $navigation = false;

        $databaseResult = CIBlockSection::GetList($sort, $filter, $this->getUseQuantity(), $select, $navigation);
        $databaseResult->SetUrlTemplates(
            null,
            $this->getIBlockSectionUrlTemplate(),
            $this->getIBlockRootUrlTemplate()
        );

        while ($section = $databaseResult->GetNext($this->getTextToHtmlAuto(), $this->getUseTilda())) {
            $buttons = CIBlock::GetPanelButtons(
                $section['IBLOCK_ID'],
                0,
                $section['ID'], [
                    'SECTION_BUTTONS' => true,
                    'SESSID' => false,
                    'CATALOG' => true
                ]
            );

            $section['EDIT_LINK'] = $buttons['edit']['edit_section']['ACTION_URL'];
            $section['DELETE_LINK'] = $buttons['edit']['delete_section']['ACTION_URL'];

            $section = $converter->convert($section);

            $result->set($section->getId(), $section);
        }

        return $result;
    }

    /**
     * Возвращает активность подсчета количества
     * @return bool
     */
    public function getUseQuantity()
    {
        return $this->_useQuantity;
    }

    /**
     * Устанавливает шаблоны URL корня и раздела инфоблока
     * @param string|null $root
     * @param string|null $section
     * @return $this
     */
    public function setIBlockUrlTemplates($root = null, $section = null)
    {
        $this->setIBlockRootUrlTemplate($root);
        $this->setIBlockSectionUrlTemplate($section);

        return $this;
    }

    /**
     * Устанавливает активность подсчета количества
     * @param $value
     * @return $this
     */
    public function setUseQuantity($value)
    {
        $this->_useQuantity = Type::toBoolean($value);

        return $this;
    }
}