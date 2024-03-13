<?php

use intec\core\bitrix\components\IBlockElements;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

class IntecMainPanelComponent extends IBlockElements
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
            'ELEMENTS' => [],
            'ORDER' => '',
            'FILTER' => [],
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ], $arParams);

        return $arParams;
    }

    /**
     * @inheritdoc
     */
    public function executeComponent()
    {
        global $USER;

        if ($this->startResultCache(false, $USER->GetGroups())) {
            $arResult = [
                'VISUAL' => [],
                'ITEMS' => []
            ];

            $this->setIBlockType($this->arParams['IBLOCK_TYPE']);
            $this->setIBlockId($this->arParams['IBLOCK_ID']);

            if ($this->getMode() === 'code') {
                $this->setElementsCode($this->arParams['ELEMENTS']);
            } else {
                $this->setElementsId($this->arParams['ELEMENTS']);
            }

            $iBlock = $this->getIBlock();

            if (!empty($iBlock) && $iBlock['ACTIVE'] === 'Y') {
                $items = $this->getElements(
                    $this->getSort(),
                    $this->getFilter(),
                    $this->getPageElementsCount()
                );

                foreach (explode(',', $this->arParams['ELEMENTS_ORDER']) as $item) {
                    if (ArrayHelper::keyExists($item, $items)) {
                        $arResult['ITEMS'][$item] = $items[$item];
                        unset($items[$item]);
                    }
                }

                unset($item);

                if (!empty($items))
                    $arResult['ITEMS'] = ArrayHelper::merge($arResult['ITEMS'], $items);

                $this->arResult = $arResult;

                unset($arResult, $items);
            }
        }

        $this->includeComponentTemplate();

        return null;
    }

    /**
     * Возвращает фильтр для выборки элементов с учетом пользовательского фильтра
     * @return array|mixed
     */
    public function getFilter()
    {
        $filter = [
            'IBLOCK_LID' => $this->getSiteId(),
            'ACTIVE' => 'Y',
            'ACTIVE_DATE' => 'Y',
            'CHECK_PERMISSIONS' => 'Y',
            'MIN_PERMISSION' => 'R'
        ];

        if (!empty($this->arParams['FILTER']) && Type::isArray($this->arParams['FILTER']))
            $filter = ArrayHelper::merge(
                $filter,
                $this->arParams['FILTER']
            );

        return $filter;
    }

    /**
     * Возвращает режим выбора элементов
     * @return mixed|null
     */
    public function getMode()
    {
        return ArrayHelper::fromRange(['id', 'code'], $this->arParams['MODE']);
    }

    /**
     * Возвращает корректное количество элементов на странице
     * 0 - выводит все элементы
     * @return int
     */
    public function getPageElementsCount()
    {
        $count = Type::toInteger($this->arParams['ELEMENTS_COUNT']);

        if ($count < 0)
            return 0;

        return $count;
    }

    /**
     * Возвращает массив для сортировки
     * @return array
     */
    public function getSort()
    {
        $sort = [];

        if (!empty($this->arParams['SORT_BY']) && !empty($this->arParams['ORDER_BY']))
            $sort[$this->arParams['SORT_BY']] = $this->arParams['ORDER_BY'];

        return $sort;
    }
}