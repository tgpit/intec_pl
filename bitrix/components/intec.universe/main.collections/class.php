<?php

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Web\Json;
use intec\core\bitrix\Component;
use intec\core\bitrix\iblock\Elements;
use intec\core\bitrix\iblock\ElementsQuery;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

class IntecCollectionsComponent extends CBitrixComponent
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
            'HEADER_BLOCK_SHOW' => 'N',
            'HEADER_BLOCK_TEXT' => null,
            'HEADER_BLOCK_POSITION' => 'center',
            'DESCRIPTION_BLOCK_SHOW' => 'N',
            'DESCRIPTION_BLOCK_TEXT' => null,
            'DESCRIPTION_BLOCK_POSITION' => 'center',
            'LIST_PAGE_URL' => null,
            'SECTION_URL' => null,
            'DETAIL_URL' => null,
            'NAVIGATION_USE' => 'N',
            'NAVIGATION_ID' => 'collections',
            'NAVIGATION_MODE' => 'standard',
            'NAVIGATION_ALL' => 'N',
            'NAVIGATION_TEMPLATE' => '.default',
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

        if ($arParams['ELEMENTS_COUNT'] < 0)
            $arParams['ELEMENTS_COUNT'] = 0;

        $arParams['HEADER_BLOCK_POSITION'] = ArrayHelper::fromRange(['center', 'left', 'right'], $arParams['HEADER_BLOCK_POSITION']);
        $arParams['DESCRIPTION_BLOCK_POSITION'] = ArrayHelper::fromRange(['center', 'left', 'right'], $arParams['DESCRIPTION_BLOCK_POSITION']);

        if (Type::isArray($arParams['FILTER']))
            $arParams['FILTER'] = array_filter($arParams['FILTER']);
        else
            $arParams['FILTER'] = [];

        if (empty($arParams['SORT_BY']))
            $arParams['SORT_BY'] = 'SORT';

        $arParams['ORDER_BY'] = ArrayHelper::fromRange(['ASC', 'DESC'], $arParams['ORDER_BY']);

        return $arParams;
    }

    /**
     * @inheritdoc
     */
    function executeComponent()
    {
        if (empty($this->arParams['IBLOCK_ID']))
            return null;

        $this->arResult = [
            'BLOCKS' => [
                'HEADER' => [
                    'SHOW' => $this->arParams['HEADER_BLOCK_SHOW'] === 'Y' && !empty($this->arParams['~HEADER_BLOCK_TEXT']),
                    'TEXT' => $this->arParams['~HEADER_BLOCK_TEXT'],
                    'POSITION' => $this->arParams['HEADER_BLOCK_POSITION']
                ],
                'DESCRIPTION' => [
                    'SHOW' => $this->arParams['DESCRIPTION_BLOCK_SHOW'] === 'Y' && !empty($this->arParams['~DESCRIPTION_BLOCK_TEXT']),
                    'TEXT' => $this->arParams['~DESCRIPTION_BLOCK_TEXT'],
                    'POSITION' => $this->arParams['DESCRIPTION_BLOCK_POSITION']
                ]
            ],
            'ITEMS' => [],
            'NAVIGATION' => [],
            'CACHE_ID' => null
        ];

        $this->arResult['NAVIGATION'] = $this->setNavigation();
        $this->arResult['CACHE_ID'] = Component::getUniqueId($this).'-'.$this->setCacheId($this->arResult['NAVIGATION']);

        if ($this->startResultCache(false, $this->arResult['CACHE_ID'])) {
            $itemsQuery = new ElementsQuery();

            $itemsQuery->setIBlockType($this->arParams['IBLOCK_TYPE'])
                ->setIBlockId($this->arParams['IBLOCK_ID'])
                ->setLimit($this->arParams['ELEMENTS_COUNT'])
                ->setSort([$this->arParams['SORT_BY'] => $this->arParams['ORDER_BY']])
                ->setIBlockUrlTemplates(
                    $this->arParams['LIST_PAGE_URL'],
                    $this->arParams['SECTION_URL'],
                    $this->arParams['DETAIL_URL']
                )->setFilter([
                    'ACTIVE' => 'Y',
                    'ACTIVE_DATE' => 'Y',
                    'CHECK_PERMISSIONS' => 'Y',
                    'MIN_PERMISSION' => 'R'
                ]);

            if (!empty($this->arParams['SECTIONS'])) {
                if ($this->arParams['SECTIONS_MODE'] === 'code')
                    $itemsQuery->setIBlockSectionsCode($this->arParams['SECTIONS']);
                else
                    $itemsQuery->setIBlockSectionsId($this->arParams['SECTIONS']);
            }

            if ($this->arParams['ELEMENTS_COUNT'] > 0)
                $itemsQuery->setOffset($this->arResult['NAVIGATION']['PAGE']['CURRENT']);

            if (!empty($this->arParams['FILTER']))
                $itemsQuery->extendFilter($this->arParams['FILTER']);

            $items = $itemsQuery->execute();

            unset($itemsQuery);

            if (!$items->isEmpty())
                $this->arResult['ITEMS'] = $items->handleFiles(Elements::HANDLE_FILES_MODE_INTERNAL)->asArray();

            unset($items);

            $this->includeComponentTemplate();
        }

        return null;
    }

    private function setNavigation()
    {
        global $APPLICATION;

        $arNavigation = [
            'USE' => $this->arParams['NAVIGATION_USE'] === 'Y',
            'ID' => $this->arParams['NAVIGATION_ID'],
            'MODE' => ArrayHelper::fromRange(['standard', 'ajax'], $this->arParams['NAVIGATION_MODE']),
            'ALL' => $this->arParams['NAVIGATION_ALL'] === 'Y',
            'TEMPLATE' => $this->arParams['NAVIGATION_TEMPLATE'],
            'PAGE' => [
                'SIZE' => $this->arParams['ELEMENTS_COUNT'],
                'CURRENT' => 1,
                'COUNT' => 1
            ],
            'ITEMS' => 0,
            'PRINT' => null
        ];

        if ($arNavigation['USE'] && empty($arNavigation['ID']))
            $arNavigation['USE'] = false;

        if ($arNavigation['MODE'] === 'ajax' && !StringHelper::startsWith($arNavigation['TEMPLATE'], 'lazy.'))
            $arNavigation['USE'] = false;

        if ($arNavigation['MODE'] !== 'standard' && $arNavigation['ALL'])
            $arNavigation['ALL'] = false;

        if ($arNavigation['PAGE']['SIZE'] < 1)
            $arNavigation['USE'] = false;

        if ($arNavigation['USE']) {
            $navigation = new PageNavigation($arNavigation['ID']);

            $navigation->setPageSize($arNavigation['PAGE']['SIZE'])
                ->allowAllRecords($arNavigation['ALL'])
                ->initFromUri();

            $filter = [
                '=IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                '=ACTIVE' => 'Y'
            ];

            $this->arParams['SECTIONS'] = array_filter($this->arParams['SECTIONS']);

            if (!empty($this->arParams['SECTIONS']))
                $filter['=IBLOCK_SECTION_ID'] = $this->arParams['SECTIONS'];

            if (Type::isArray($this->arParams['FILTER']) && !empty($this->arParams['FILTER']))
                $filter = ArrayHelper::merge($filter, array_filter($this->arParams['FILTER']));

            $list = ElementTable::getList([
                'filter' => $filter,
                'select' => ['ID'],
                'count_total' => true,
                'offset' => $navigation->getOffset(),
                'limit' => $navigation->getLimit()
            ]);

            $navigation->setRecordCount($list->getCount());

            if ($navigation->getCurrentPage() > $navigation->getPageCount())
                $navigation->setCurrentPage($navigation->getPageCount());

            $arNavigation['PAGE']['CURRENT'] = $navigation->getCurrentPage();
            $arNavigation['PAGE']['COUNT'] = $navigation->getPageCount();

            if ($navigation->allRecordsShown())
                $arNavigation['PAGE']['SIZE'] = $navigation->getRecordCount();
            else
                $arNavigation['PAGE']['SIZE'] = $navigation->getPageSize();

            $arNavigation['ITEMS'] = $navigation->getRecordCount();

            if ($navigation->getPageCount() < 2 && !$navigation->allRecordsShown())
                $arNavigation['USE'] = false;

            if ($arNavigation['PAGE']['COUNT'] > 1 || $navigation->allRecordsShown()) {
                ob_start();

                $APPLICATION->IncludeComponent(
                    'bitrix:main.pagenavigation',
                        $arNavigation['TEMPLATE'], [
                        'NAV_OBJECT' => $navigation,
                        'SEF_MODE' => 'N'
                    ],
                    $this
                );

                $arNavigation['PRINT'] = ob_get_contents();

                ob_end_clean();
            }

            unset($navigation, $filter, $list);
        }

        return $arNavigation;
    }

    private function setCacheId($data) {
        global $USER;

        if (!Type::isString($data))
            $data = serialize($data);

        return md5($data.serialize($USER->GetGroups()));
    }

    public static function sendJsonAnswer($result = [])
    {
        global $APPLICATION;

        if (!empty($result))
            $result['JS'] = Asset::getInstance()->getJs();

        $APPLICATION->RestartBuffer();

        echo Json::encode($result);

        \CMain::FinalActions();

        die();
    }
}