<?php

namespace intec\core\bitrix\iblock;

use CIBlock;
use CIBlockSection;
use Bitrix\Main\Loader;
use intec\core\helpers\Type;

/**
 * Class SectionsQuery
 * @property boolean $useQuantity Использовать подсчет количества.
 * @package intec\core\bitrix\iblock
 * @deprecated
 */
class SectionsQuery extends Query
{
    use LimitQueryTrait;
    use SectionsQueryTrait;

    /**
     * Использовать подсчет количества.
     * @var boolean
     */
    protected $_useQuantity = false;

    /**
     * Запускает запрос и возвращает результат в виде списка разделов.
     * @return Sections
     */
    public function execute()
    {
        $result = new Sections();

        if (!Loader::includeModule('iblock'))
            return $result;

        $id = $this->getIBlockId();
        $type = $this->getIBlockType();
        $filter = $this->getFilter();
        $select = $this->getSelect();
        $sort = $this->getSort();
        $sectionsId = $this->getIBlockSectionsId();
        $sectionsCode = $this->getIBlockSectionsCode();
        $navigation = [];
        $limit = $this->getLimit();
        $offset = $this->getOffset();

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
        $databaseResult->SetUrlTemplates(null, $this->getIBlockSectionUrlTemplate(), $this->getIBlockRootUrlTemplate());

        while ($section = $databaseResult->GetNext(false, false)) {
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

            $result->set($section['ID'], $section);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getIsGlobal()
    {
        return parent::getIsGlobal() &&
            $this->_iBlockSectionsId === null &&
            $this->_iBlockSectionsCode === null;
    }

    /**
     * Возвращает состояние подсчета количества.
     * @return boolean
     */
    public function getUseQuantity()
    {
        return $this->_useQuantity;
    }

    /**
     * Устанавливает шаблоны URL инфоблока.
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
     * Устанавливает состояние подсчета количества.
     * @param boolean $value
     * @return $this
     */
    public function setUseQuantity($value)
    {
        $this->_useQuantity = Type::toBoolean($value);

        return $this;
    }
}
