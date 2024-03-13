<?php

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Web\Json;
use intec\core\bitrix\Component;
use intec\core\bitrix\components\IBlockElements;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

class IntecMainNewsComponent extends IBlockElements
{
    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams)
    {
        if (!Type::isArray($arParams))
            $arParams = [];

        $arParams = ArrayHelper::merge([
            'IBLOCK_TYPE' => null,
            'IBLOCK_ID' => null,
            'ELEMENTS_COUNT' => null,
            'FILTER' => null,
            'HEADER_BLOCK_TEXT' => null,
            '~HEADER_BLOCK_TEXT' => null,
            'HEADER_BLOCK_SHOW' => 'N',
            'HEADER_BLOCK_POSITION' => null,
            'DESCRIPTION_BLOCK_TEXT' => null,
            '~DESCRIPTION_BLOCK_TEXT' => null,
            'DESCRIPTION_BLOCK_SHOW' => 'N',
            'DESCRIPTION_BLOCK_POSITION' => null,
            'DATE_SHOW' => 'N',
            'DATE_FORMAT' => null,
            'LIST_PAGE_URL' => null,
            'SECTION_URL' => null,
            'DETAIL_URL' => null,
            'NAVIGATION_USE' => 'N',
            'NAVIGATION_ID' => 'news',
            'NAVIGATION_MODE' => 'standard',
            'NAVIGATION_ALL' => 'N',
            'NAVIGATION_TEMPLATE' => '.default',
            'SORT_BY' => 'sort',
            'ORDER_BY' => 'asc'
        ], $arParams);

        return $arParams;
    }

    /**
     * @inheritdoc
     */
    function executeComponent()
    {
        global $USER;

        if (!Loader::includeModule('iblock'))
            return;

        $arParams = $this->arParams;
        $arResult = [
            'BLOCKS' => [
                'HEADER' => [
                    'SHOW' => $arParams['HEADER_BLOCK_SHOW'] === 'Y',
                    'TEXT' => $arParams['~HEADER_BLOCK_TEXT'],
                    'POSITION' => ArrayHelper::fromRange([
                        'left',
                        'center',
                        'right'
                    ], $arParams['HEADER_BLOCK_POSITION'])
                ],
                'DESCRIPTION' => [
                    'SHOW' => $arParams['DESCRIPTION_BLOCK_SHOW'] === 'Y',
                    'TEXT' => $arParams['~DESCRIPTION_BLOCK_TEXT'],
                    'POSITION' => ArrayHelper::fromRange([
                        'left',
                        'center',
                        'right'
                    ], $arParams['DESCRIPTION_BLOCK_POSITION'])
                ]
            ],
            'VISUAL' => [
                'DATE' => [
                    'SHOW' => $arParams['DATE_SHOW'] === 'Y',
                    'FORMAT' => $arParams['DATE_FORMAT']
                ]
            ],
            'ITEMS' => [],
            'NAVIGATION' => [],
            'CACHE_ID' => null
        ];

        $arResult['NAVIGATION'] = $this->setNavigation();
        $arResult['CACHE_ID'] = Component::getUniqueId($this).'-'.$this->setCacheId($arResult['NAVIGATION']);

        if ($this->startResultCache(false, $arResult['CACHE_ID'], $USER->GetGroups())) {
            if (empty($arResult['BLOCKS']['HEADER']['TEXT']))
                $arResult['BLOCKS']['HEADER']['SHOW'] = false;

            if (empty($arResult['BLOCKS']['DESCRIPTION']['TEXT']))
                $arResult['BLOCKS']['DESCRIPTION']['SHOW'] = false;

            $this->setIBlockType($arParams['IBLOCK_TYPE']);
            $this->setIBlockId($arParams['IBLOCK_ID']);

            $arIBlock = $this->getIBlock();

            if (!empty($arIBlock) && $arIBlock['ACTIVE'] === 'Y') {
                $this->setUrlTemplates(
                    $arParams['LIST_PAGE_URL'],
                    $arParams['SECTION_URL'],
                    $arParams['DETAIL_URL']
                );

                $arSort = [];
                $arFilter = $arParams['FILTER'];

                if (!empty($arParams['SORT_BY']) && !empty($arParams['ORDER_BY']))
                    $arSort = [$arParams['SORT_BY'] => $arParams['ORDER_BY']];

                if (!Type::isArray($arFilter))
                    $arFilter = [];

                $arFilter = ArrayHelper::merge([
                    'IBLOCK_LID' => $this->getSiteId(),
                    'ACTIVE' => 'Y',
                    'ACTIVE_DATE' => 'Y',
                    'CHECK_PERMISSIONS' => 'Y',
                    'MIN_PERMISSION' => 'R'
                ], $arFilter);

                $arResult['ITEMS'] = $this->getElements(
                    $arSort,
                    $arFilter,
                    $arResult['NAVIGATION']['PAGE']['SIZE'],
                    $arResult['NAVIGATION']['PAGE']['CURRENT']
                );

                unset($arSort, $arFilter);

                foreach ($arResult['ITEMS'] as &$arItem) {
                    $arItem['DATE_CREATE_FORMATTED'] = null;
                    $arItem['DATE_ACTIVE_FROM_FORMATTED'] = null;
                    $arItem['DATE_ACTIVE_TO_FORMATTED'] = null;

                    if (!empty($arResult['VISUAL']['DATE']['FORMAT'])) {
                        if (!empty($arItem['DATE_CREATE']))
                            $arItem['DATE_CREATE_FORMATTED'] = CIBlockFormatProperties::DateFormat(
                                $arResult['VISUAL']['DATE']['FORMAT'],
                                MakeTimeStamp(
                                    $arItem['DATE_CREATE'],
                                    CSite::GetDateFormat()
                                )
                            );

                        if (!empty($arItem['DATE_ACTIVE_FROM']))
                            $arItem['DATE_ACTIVE_FROM_FORMATTED'] = CIBlockFormatProperties::DateFormat(
                                $arResult['VISUAL']['DATE']['FORMAT'],
                                MakeTimeStamp(
                                    $arItem['DATE_ACTIVE_FROM'],
                                    CSite::GetDateFormat()
                                )
                            );

                        if (!empty($arItem['DATE_ACTIVE_TO']))
                            $arItem['DATE_ACTIVE_TO_FORMATTED'] = CIBlockFormatProperties::DateFormat(
                                $arResult['VISUAL']['DATE']['FORMAT'],
                                MakeTimeStamp(
                                    $arItem['DATE_ACTIVE_TO'],
                                    CSite::GetDateFormat()
                                )
                            );
                    } else {
                        if (!empty($arItem['DATE_CREATE']))
                            $arItem['DATE_CREATE_FORMATTED'] = $arItem['DATE_CREATE'];

                        if (!empty($arItem['DATE_ACTIVE_FROM']))
                            $arItem['DATE_ACTIVE_FROM_FORMATTED'] = $arItem['DATE_ACTIVE_FROM'];

                        if (!empty($arItem['DATE_ACTIVE_TO']))
                            $arItem['DATE_ACTIVE_TO_FORMATTED'] = $arItem['DATE_ACTIVE_TO'];
                    }
                }

                unset($arItem);
            }

            $this->arResult = $arResult;

            unset($arParams, $arResult, $arIBlock);

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
        if (!Type::isString($data))
            $data = serialize($data);

        return md5($data);
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