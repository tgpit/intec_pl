<?php

use Bitrix\Main\Loader;
use intec\core\bitrix\iblock\Elements;
use intec\core\bitrix\iblock\ElementsQuery;
use intec\core\bitrix\iblock\SectionsQuery;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

class IntecMainImagesComponent extends CBitrixComponent
{
    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams) {
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
            'SECTIONS_COUNT' => 0,
            'SECTION_ELEMENTS_COUNT' => 0,
            'ELEMENTS_COUNT' => 0,
            'HEADER_SHOW' => 'N',
            'HEADER_TEXT' => null,
            '~HEADER_TEXT' => null,
            'HEADER_POSITION' => 'center',
            'DESCRIPTION_SHOW' => 'N',
            'DESCRIPTION_TEXT' => null,
            '~DESCRIPTION_TEXT' => null,
            'DESCRIPTION_POSITION' => 'center',
            'LIST_PAGE_URL' => null,
            'SECTION_URL' => null,
            'DETAIL_URL' => null,
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
            'FILTER' => [],
        ], $arParams);

        $arParams['SECTIONS_MODE'] = ArrayHelper::fromRange(['id', 'code'], $arParams['SECTIONS_MODE']);

        if (!Type::isArray($arParams['SECTIONS']))
            $arParams['SECTIONS'] = [];
        else
            $arParams['SECTIONS'] = array_filter($arParams['SECTIONS']);

        $arParams['SECTIONS_COUNT'] = Type::toInteger($arParams['SECTIONS_COUNT']);

        if ($arParams['SECTIONS_COUNT'] < 0)
            $arParams['SECTIONS_COUNT'] = 0;

        $arParams['SECTION_ELEMENTS_COUNT'] = Type::toInteger($arParams['SECTION_ELEMENTS_COUNT']);

        if ($arParams['SECTION_ELEMENTS_COUNT'] < 0)
            $arParams['SECTION_ELEMENTS_COUNT'] = 0;

        $arParams['ELEMENTS_COUNT'] = Type::toInteger($arParams['ELEMENTS_COUNT']);

        if ($arParams['ELEMENTS_COUNT'] < 0)
            $arParams['ELEMENTS_COUNT'] = 0;

        $arParams['HEADER_POSITION'] = ArrayHelper::fromRange(['center', 'left', 'right'], $arParams['HEADER_POSITION']);
        $arParams['DESCRIPTION_POSITION'] = ArrayHelper::fromRange(['center', 'left', 'right'], $arParams['DESCRIPTION_POSITION']);

        if (empty($arParams['SORT_BY']))
            $arParams['SORT_BY'] = 'SORT';

        $arParams['ORDER_BY'] = ArrayHelper::fromRange(['ASC', 'DESC'], $arParams['ORDER_BY']);

        if (!Type::isArray($arParams['FILTER']))
            $arParams['FILTER'] = [];
        else
            $arParams['FILTER'] = array_filter($arParams['FILTER']);

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
                        'SHOW' => $this->arParams['HEADER_SHOW'] === 'Y' && !empty($this->arParams['~HEADER_TEXT']),
                        'TEXT' => $this->arParams['~HEADER_TEXT'],
                        'POSITION' => $this->arParams['HEADER_POSITION']
                    ],
                    'DESCRIPTION' => [
                        'SHOW' => $this->arParams['DESCRIPTION_SHOW'] === 'Y' && !empty($this->arParams['~DESCRIPTION_TEXT']),
                        'TEXT' => $this->arParams['~DESCRIPTION_TEXT'],
                        'POSITION' => $this->arParams['DESCRIPTION_POSITION']
                    ]
                ],
                'SECTIONS' => [],
                'ITEMS' => []
            ];

            $sectionsQuery = new SectionsQuery();
            $itemsQuery = new ElementsQuery();

            $sectionsQuery->setIBlockType($this->arParams['IBLOCK_TYPE'])
                ->setIBlockId($this->arParams['IBLOCK_ID'])
                ->setLimit($this->arParams['SECTIONS_COUNT'])
                ->setSort(['SORT' => 'ASC'])
                ->setFilter([
                    'GLOBAL_ACTIVE' => 'Y',
                    'ACTIVE' => 'Y'
                ]);

            $itemsQuery->setIBlockType($this->arParams['IBLOCK_TYPE'])
                ->setIBlockId($this->arParams['IBLOCK_ID'])
                ->setLimit($this->arParams['ELEMENTS_COUNT'])
                ->setSort([$this->arParams['SORT_BY'] => $this->arParams['ORDER_BY']])
                ->setFilter([
                    'ACTIVE' => 'Y',
                    'ACTIVE_DATE' => 'Y',
                    'CHECK_PERMISSIONS' => 'Y',
                    'MIN_PERMISSION' => 'R'
                ]);

            if (!empty($this->arParams['SECTIONS'])) {
                if ($this->arParams['SECTIONS_MODE'] === 'code') {
                    $sectionsQuery->setIBlockSectionsCode($this->arParams['SECTIONS']);
                    $itemsQuery->setIBlockSectionsCode($this->arParams['SECTIONS']);
                } else {
                    $sectionsQuery->setIBlockSectionsId($this->arParams['SECTIONS']);
                    $itemsQuery->setIBlockSectionsId($this->arParams['SECTIONS']);
                }
            }

            if (!empty($this->arParams['FILTER']))
                $itemsQuery->extendFilter($this->arParams['FILTER']);

            $sections = $sectionsQuery->execute();
            $items = $itemsQuery->execute();

            unset($sectionsQuery, $itemsQuery);

            if (!$sections->isEmpty())
                $this->arResult['SECTIONS'] = $sections->handleFiles()->asArray();

            if (!$items->isEmpty())
                $this->arResult['ITEMS'] = $items->handleFiles(Elements::HANDLE_FILES_MODE_INTERNAL)->asArray();

            unset($sections, $items);

            if (!empty($this->arResult['SECTIONS']) && !empty($this->arResult['ITEMS'])) {
                foreach ($this->arResult['SECTIONS'] as $sectionKey => &$section) {
                    $section['ITEMS'] = [];

                    $count = 0;

                    foreach ($this->arResult['ITEMS'] as $itemKey => &$item) {
                        if ($item['IBLOCK_SECTION_ID'] === $section['ID']) {
                            $count++;

                            if ($this->arParams['SECTION_ELEMENTS_COUNT'] > 0 && $count > $this->arParams['SECTION_ELEMENTS_COUNT']) {
                                unset($this->arResult['ITEMS'][$itemKey]);
                                continue;
                            }

                            $section['ITEMS'][$item['ID']] = &$item;
                        }
                    }

                    unset($itemKey, $item);
                }

                unset($sectionKey, $section, $count);
            }

            $this->includeComponentTemplate();
        }

        return null;
    }
}