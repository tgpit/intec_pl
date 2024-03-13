<?php

use Bitrix\Main\Loader;
use intec\core\bitrix\components\IBlockElements;
use intec\core\bitrix\iblock\ElementsQuery;
use intec\core\bitrix\iblock\SectionsQuery;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

class IntecMainStoriesComponent extends CBitrixComponent
{
    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams)
    {
        if (!Loader::includeModule('iblock'))
            return [];

        if (!Loader::includeModule('intec.core'))
            return [];

        if (!Type::isArray($arParams))
            $arParams = [];

        $arParams = ArrayHelper::merge([
            'IBLOCK_TYPE' => null,
            'IBLOCK_ID' => null,
            'SECTIONS_MODE' => 'id',
            'SECTIONS' => [],
            'ELEMENTS_COUNT' => 0,
            'ELEMENT_ITEMS_COUNT' => 0,
            'HEADER_TEXT' => null,
            '~HEADER_TEXT' => null,
            'HEADER_SHOW' => 'N',
            'HEADER_POSITION' => null,
            'DESCRIPTION_TEXT' => null,
            '~DESCRIPTION_TEXT' => null,
            'DESCRIPTION_SHOW' => 'N',
            'DESCRIPTION_POSITION' => null,
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
            'FILTER' => []
        ], $arParams);

        $arParams['SECTIONS_MODE'] = ArrayHelper::fromRange(['id', 'code'], $arParams['SECTIONS_MODE']);

        if (Type::isArray($arParams['SECTIONS']))
            $arParams['SECTIONS'] = array_filter($arParams['SECTIONS']);
        else
            $arParams['SECTIONS'] = [];

        $arParams['ELEMENTS_COUNT'] = Type::toInteger($arParams['ELEMENTS_COUNT']);
        $arParams['ELEMENT_ITEMS_COUNT'] = Type::toInteger($arParams['ELEMENT_ITEMS_COUNT']);

        if ($arParams['ELEMENTS_COUNT'] < 0)
            $arParams['ELEMENTS_COUNT'] = 0;

        if ($arParams['ELEMENT_ITEMS_COUNT'] < 0)
            $arParams['ELEMENT_ITEMS_COUNT'] = 0;

        if (empty($arParams['SORT_BY']))
            $arParams['SORT_BY'] = 'SORT';

        $arParams['ORDER_BY'] = ArrayHelper::fromRange(['ASC', 'DESC'], $arParams['ORDER_BY']);

        if (Type::isArray($arParams['FILTER']))
            $arParams['FILTER'] = array_filter($arParams['FILTER']);
        else
            $arParams['FILTER'] = [];

        return $arParams;
    }

    /**
     * @inheritdoc
     */
    public function executeComponent()
    {
        if (empty($this->arParams['IBLOCK_ID']))
            return;

        global $USER;

        if ($this->startResultCache(false, $USER->GetGroups())) {
            $this->arResult = [
                'BLOCKS' => [
                    'HEADER' => [
                        'SHOW' => $this->arParams['HEADER_SHOW'] === 'Y',
                        'TEXT' => $this->arParams['~HEADER_TEXT'],
                        'POSITION' => ArrayHelper::fromRange([
                            'left',
                            'center',
                            'right'
                        ], $this->arParams['HEADER_POSITION'])
                    ],
                    'DESCRIPTION' => [
                        'SHOW' => $this->arParams['DESCRIPTION_SHOW'] === 'Y',
                        'TEXT' => $this->arParams['~DESCRIPTION_TEXT'],
                        'POSITION' => ArrayHelper::fromRange([
                            'left',
                            'center',
                            'right'
                        ], $this->arParams['DESCRIPTION_POSITION'])
                    ]
                ],
                'SECTIONS' => [],
                'ITEMS' => []
            ];

            if (empty($arResult['BLOCKS']['HEADER']['TEXT']))
                $arResult['BLOCKS']['HEADER']['SHOW'] = false;

            if (empty($arResult['BLOCKS']['DESCRIPTION']['TEXT']))
                $arResult['BLOCKS']['DESCRIPTION']['SHOW'] = false;

            $querySections = new SectionsQuery();
            $queryElements = new ElementsQuery();
            $sections = [];

            $querySections->setIBlockType($this->arParams['IBLOCK_TYPE']);
            $querySections->setIBlockId($this->arParams['IBLOCK_ID']);
            $querySections->setLimit($this->arParams['ELEMENTS_COUNT']);
            $querySections->setSort([
                $this->arParams['SORT_BY'] => $this->arParams['ORDER_BY']
            ]);

            $queryElements->setIBlockType($this->arParams['IBLOCK_TYPE']);
            $queryElements->setIBlockId($this->arParams['IBLOCK_ID']);
            $queryElements->setSort([
                $this->arParams['SORT_BY'] => $this->arParams['ORDER_BY']
            ]);

            if ($this->arParams['SECTIONS_MODE'] === 'code')
                $querySections->setIBlockSectionsCode($this->arParams['SECTIONS']);
            else
                $querySections->setIBlockSectionsId($this->arParams['SECTIONS']);

            $querySections->setFilter([
                'GLOBAL_ACTIVE' => 'Y',
                'ACTIVE' => 'Y'
            ]);

            if (!empty($this->arParams['FILTER']))
                $querySections->extendFilter($this->arParams['FILTER']);

            $queryElements->setFilter([
                'ACTIVE' => 'Y',
                'ACTIVE_DATE' => 'Y',
                'CHECK_PERMISSIONS' => 'Y',
                'MIN_PERMISSION' => 'R'
            ]);

            $arSections = $querySections->execute();

            if (!$arSections->isEmpty()) {
                $arSections->each(function ($key, $value) use (&$sections, &$files) {
                    $sections[] = $value['ID'];
                })->handleFiles();

                $queryElements->setIBlockSectionsId($sections);

                $arItems = $queryElements->execute();

                if (!$arItems->isEmpty()) {
                    $arItems->handleFiles();

                    $this->arResult['SECTIONS'] = $arSections->asArray();
                    $this->arResult['ITEMS'] = $arItems->asArray();

                    unset($arSections, $arItems);

                    foreach ($this->arResult['SECTIONS'] as $sectionKey => &$section) {
                        $section['ITEMS'] = [];

                        $count = 0;

                        foreach ($this->arResult['ITEMS'] as $itemKey => &$item) {
                            if ($item['IBLOCK_SECTION_ID'] === $section['ID']) {
                                $count++;

                                if ($this->arParams['ELEMENT_ITEMS_COUNT'] > 0 && $count > $this->arParams['ELEMENT_ITEMS_COUNT']) {
                                    unset($this->arResult['ITEMS'][$itemKey]);
                                    continue;
                                }

                                $section['ITEMS'][$item['ID']] = &$item;
                            }
                        }

                        if (empty($section['ITEMS']))
                            unset($this->arResult['SECTIONS'][$sectionKey]);

                        unset($itemKey, $item);
                    }

                    unset($sectionKey, $section, $count);
                }

                unset($arItems);
            }

            unset($querySections, $queryElements, $sections, $arSections);

            $this->includeComponentTemplate();
        }

        return null;
    }
}