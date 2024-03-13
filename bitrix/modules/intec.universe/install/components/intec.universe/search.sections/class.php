<?php

use intec\Core;
use intec\core\bitrix\components\IBlockSections;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\core\net\Url;

class IntecSearchSectionsComponent extends IBlockSections
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
            'ELEMENTS_ID' => null,
            'ELEMENTS_COUNT' => null,
            'SECTION_ID_VARIABLE' => null,
            'FILTER' => null,
            'LIST_PAGE_URL' => null,
            'SECTION_URL' => null,
            'QUANTITY_SHOW' => 'N',
            'SORT_BY' => 'sort',
            'ORDER_BY' => 'asc'
        ], $arParams);

        if (empty($arParams['SECTION_ID_VARIABLE']))
            $arParams['SECTION_ID_VARIABLE'] = 'section';

        return $arParams;
    }

    /**
     * @inheritdoc
     */
    public function executeComponent()
    {
        global $USER;

        $arParams = $this->arParams;
        $oRequest = Core::$app->request;
        $sSection = $oRequest->get($arParams['SECTION_ID_VARIABLE']);

        if ($this->startResultCache(false, $USER->GetGroups())) {
            $arResult = [
                'SECTIONS' => [],
                'VISUAL' => [
                    'QUANTITY' => [
                        'SHOW' => $arParams['QUANTITY_SHOW'] === 'Y'
                    ]
                ]
            ];

            $arSectionsCount = [];
            $arSectionsId = [];

            $dbSections = CIBlockElement::GetElementGroups($arParams['ELEMENTS_ID'], false);

            while($arSection = $dbSections->Fetch()) {
                if (!ArrayHelper::keyExists(
                    $arSection['ID'],
                    $arSectionsCount
                )) {
                    $arSectionsCount[$arSection['ID']] = [
                        'ID' => $arSection['ID'],
                        'ITEMS' => []
                    ];
                }

                if (!ArrayHelper::isIn(
                    $arSection['IBLOCK_ELEMENT_ID'],
                    $arSectionsCount[$arSection['ID']]['ITEMS']
                )) $arSectionsCount[$arSection['ID']]['ITEMS'][] = $arSection['IBLOCK_ELEMENT_ID'];

                $arSectionsId[] = $arSection['ID'];
            }

            unset($arSection);

            $this->setIBlockType($arParams['IBLOCK_TYPE']);
            $this->setIBlockId($arParams['IBLOCK_ID']);

            $arIBlock = $this->getIBlock();

            if (!empty($arIBlock) && $arIBlock['ACTIVE'] === 'Y') {
                $this->setSectionsId($arSectionsId);

                $arSort = [];
                $arFilter = $arParams['FILTER'];

                if (!empty($arParams['SORT_BY']) && !empty($arParams['ORDER_BY']))
                    $arSort = [$arParams['SORT_BY'] => $arParams['ORDER_BY']];

                if (!Type::isArray($arFilter))
                    $arFilter = [];

                $arFilter = ArrayHelper::merge([
                    'GLOBAL_ACTIVE' => 'Y',
                    'CNT_ACTIVE' => 'Y',
                    'CHECK_PERMISSIONS' => 'Y',
                    'MIN_PERMISSION' => 'R'
                ], $arFilter);

                if (empty($this->getSectionsId()))
                    $arFilter['SECTION_ID'] = false;

                $arSections = $this->getSections(
                    $arSort,
                    $arFilter,
                    $arParams['ELEMENTS_COUNT'],
                    null,
                    false
                );

                $sUrl = $oRequest->getUrl();

                foreach ($arSections as &$arSection) {
                    $oUrl = new Url($sUrl);
                    $oUrl->getQuery()->set($arParams['SECTION_ID_VARIABLE'], $arSection['ID']);
                    $arSection['URL'] = $oUrl->build();
                    $arSection['CURRENT'] = 'N';
                    $arSection['ELEMENTS_COUNT'] = 0;

                    if (!empty($arSectionsCount[$arSection['ID']]['ITEMS']))
                        $arSection['ELEMENTS_COUNT'] = count($arSectionsCount[$arSection['ID']]['ITEMS']);
                }

                $arResult['SECTIONS'] = $arSections;

                unset($arSections);
            }

            $this->arResult = $arResult;

            unset($arSection);
            unset($arSectionsId);
            unset($arParams);
            unset($arResult);

            $this->endResultCache();
        }

        foreach ($this->arResult['SECTIONS'] as &$arSection) {
            if ($arSection['ID'] === $sSection) {
                $arSection['CURRENT'] = 'Y';
                break;
            }
        }

        unset($arSection);

        $this->includeComponentTemplate();

        return $this->arResult['SECTIONS'];
    }
}