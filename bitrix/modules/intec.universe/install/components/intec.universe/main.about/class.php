<?php

use intec\core\bitrix\components\IBlockElements;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

class IntecMainAboutComponent extends IBlockElements
{
    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams) {
        if (!Type::isArray($arParams))
            $arParams = [];

        $arParams = ArrayHelper::merge([
            'IBLOCK_TYPE' => null,
            'IBLOCK_ID' => null,
            'SECTIONS_MODE' => 'id',
            'SECTION' => [],
            'ELEMENTS_MODE' => 'id',
            'ELEMENT' => null,
            'PICTURE_SOURCES' => [],
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
            'FILTER' => []
        ], $arParams);

        if (!Type::isArray($arParams['SECTION']))
            $arParams['SECTION'] = [];

        if (!Type::isArray($arParams['PICTURE_SOURCES']))
            $arParams['PICTURE_SOURCES'] = [];

        if (!Type::isArray($arParams['FILTER']))
            $arParams['FILTER'] = [];

        return $arParams;
    }

    /**
     * @inheritdoc
     */
    public function executeComponent() {
        global $USER;

        if ($this->startResultCache(false, $USER->GetGroups())) {
            $arParams = $this->arParams;
            $arResult = [
                'ITEM' => [],
                'PICTURE' => []
            ];

            $arParams['SECTION'] = array_filter($arParams['SECTION']);
            $arParams['PICTURE_SOURCES'] = array_filter($arParams['PICTURE_SOURCES']);

            $arQuery = [
                'FILTER' => ArrayHelper::merge([
                    'IBLOCK_LID' => $this->getSiteId(),
                    'ACTIVE' => 'Y',
                    'ACTIVE_DATE' => 'Y',
                    'CHECK_PERMISSIONS' => 'Y',
                    'MIN_PERMISSION' => 'R'
                ], $arParams['FILTER']),
                'SORT' => [
                    $arParams['SORT_BY'] => $arParams['ORDER_BY']
                ]
            ];

            if ($arParams['SECTIONS_MODE'] === 'code')
                $this->setSectionsCode($arParams['SECTION']);
            else
                $this->setSectionsId($arParams['SECTION']);

            if ($arParams['ELEMENTS_MODE'] === 'code')
                $this->setElementsCode($arParams['ELEMENT']);
            else
                $this->setElementsId($arParams['ELEMENT']);

            $arItem = $this->getElements($arQuery['SORT'], $arQuery['FILTER'], 1);

            if (!empty($arItem)) {
                $arResult['ITEM'] = ArrayHelper::getFirstValue($arItem);

                if (!empty($arParams['PICTURE_SOURCES'])) {
                    if (
                        Type::isArray($arResult['ITEM']['DETAIL_PICTURE']) &&
                        ArrayHelper::isIn('detail', $arParams['PICTURE_SOURCES']) &&
                        !empty($arResult['ITEM']['DETAIL_PICTURE'])
                    )
                        $arResult['PICTURE'] = $arResult['ITEM']['DETAIL_PICTURE'];
                    else if (
                        Type::isArray($arResult['ITEM']['PREVIEW_PICTURE']) &&
                        ArrayHelper::isIn('preview', $arParams['PICTURE_SOURCES']) &&
                        !empty($arResult['ITEM']['PREVIEW_PICTURE'])
                    )
                        $arResult['PICTURE'] = $arResult['ITEM']['PREVIEW_PICTURE'];
                }
            }

            unset($arItem);

            $this->arResult = $arResult;

            unset($arParams, $arResult);

            $this->includeComponentTemplate();
        }

        return null;
    }
}