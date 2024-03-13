<?php

namespace intec\importexport\models\excel;

use CIBlock;
use CIBlockElement;
use Bitrix\Main\Loader;
use intec\core\helpers\Type;
use intec\core\bitrix\iblock\Elements;
use intec\core\bitrix\iblock\ElementsQuery;
use intec\core\helpers\StringHelper;
use intec\core\helpers\ArrayHelper;

/**
 * Class ElementsQuery
 * @package intec\core\bitrix\iblock
 * @deprecated
 */
class ElementsQueryCustom extends ElementsQuery
{
    private $selectProperties = [];
    private $optimization = false;

    public function getSelectProperties() {
        return $this->selectProperties;
    }
    public function setSelectProperties($properties, $cutPrefix = false, $prefix = 'PROPERTY_') {
        if ($cutPrefix) {
            $unPrefixProperties = [];

            foreach ($properties as $property) {
                if (StringHelper::startsWith($property, $prefix)) {
                    $unPrefixProperties[] = StringHelper::cut($property, StringHelper::length($prefix));
                }
            }

            $this->selectProperties = $unPrefixProperties;
        } else {
            $this->selectProperties = $properties;
        }
    }
    public function setIsOptimization($value) {
        if (Type::toBoolean($value) && $value)
            $this->optimization = true;
        else
            $this->optimization = false;
    }

    public function execute()
    {
        $result = new Elements();

        if (!Loader::includeModule('iblock'))
            return $result;

        $id = $this->getIBlockId();
        $type = $this->getIBlockType();
        $filter = $this->getFilter();
        $select = $this->getSelect();
        $selectProperties = $this->getSelectProperties();
        $optimization = $this->optimization;
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

            if ($withProperties) {
                if (empty($selectProperties) && !$optimization) {
                    $element['PROPERTIES'] = $elementResult->GetProperties();
                } else {
                    if (!empty($selectProperties)) {
                        foreach ($selectProperties as $selectProperty) {
                            $element['PROPERTIES'][$selectProperty] = $elementResult->GetProperty($selectProperty);
                        }
                    }
                }

            }

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

}