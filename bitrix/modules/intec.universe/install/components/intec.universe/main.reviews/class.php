<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;
use intec\core\bitrix\components\IBlockElements;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

class IntecMainReviewsComponent extends IBlockElements
{
    private $users = [];

    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams) {
        if (!Type::isArray($arParams))
            $arParams = [];

        $arParams = ArrayHelper::merge([
            'IBLOCK_TYPE' => null,
            'IBLOCK_ID' => null,
            'ELEMENTS_COUNT' => null,
            'SECTIONS_MODE' => 'id',
            'SECTIONS' => [],
            'FILTER' => null,
            'HEADER_SHOW' => 'N',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => null,
            '~HEADER_TEXT' => null,
            'DESCRIPTION_SHOW' => 'N',
            'DESCRIPTION_POSITION' => 'center',
            'DESCRIPTION_TEXT' => null,
            '~DESCRIPTION_TEXT' => null,
            'LIST_PAGE_URL' => null,
            'SECTION_URL' => null,
            'DETAIL_URL' => null,
            'SORT_BY' => 'sort',
            'ORDER_BY' => 'asc'
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
            $arParams = $this->arParams;
            $arResult = [
                'BLOCKS' => [
                    'HEADER' => [
                        'SHOW' => $arParams['HEADER_SHOW'] === 'Y',
                        'TEXT' => $arParams['~HEADER_TEXT'],
                        'POSITION' => ArrayHelper::fromRange(['left', 'center', 'right'], $arParams['HEADER_POSITION'])
                    ],
                    'DESCRIPTION' => [
                        'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y',
                        'TEXT' => $arParams['~DESCRIPTION_TEXT'],
                        'POSITION' => ArrayHelper::fromRange(['left', 'center', 'right'], $arParams['DESCRIPTION_POSITION'])
                    ]
                ],
                'ITEMS' => []
            ];

            if (empty($arResult['BLOCKS']['HEADER']['TEXT']))
                $arResult['BLOCKS']['HEADER']['SHOW'] = false;

            if (empty($arResult['BLOCKS']['DESCRIPTION']['TEXT']))
                $arResult['BLOCKS']['DESCRIPTION']['SHOW'] = false;

            $this->setIBlockType($arParams['IBLOCK_TYPE']);
            $this->setIBlockId($arParams['IBLOCK_ID']);

            $arIBlock = $this->getIBlock();

            if (!empty($arIBlock) && $arIBlock['ACTIVE'] === 'Y') {
                $arSort = [];
                $arFilter = $arParams['FILTER'];

                if ($arParams['SECTIONS_MODE'] === 'code')
                    $this->setSectionsCode($arParams['SECTIONS']);
                else
                    $this->setSectionsId($arParams['SECTIONS']);

                $this->setUrlTemplates(
                    $arParams['LIST_PAGE_URL'],
                    $arParams['SECTION_URL'],
                    $arParams['DETAIL_URL']
                );

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

                $arResult['ITEMS'] = $this->getElements($arSort, $arFilter, $arParams['ELEMENTS_COUNT']);

                foreach ($arResult['ITEMS'] as &$arItem) {
                    if (StringHelper::startsWith($arItem['NAME'], Loc::getMessage('C_MAIN_REVIEWS_COMPONENT_USER_NAME_UNREGISTERED', [
                        '{{ID}}' => null
                    ]))) {
                        $arItem['NAME'] = Loc::getMessage('C_MAIN_REVIEWS_COMPONENT_USER_NAME_UNREGISTERED', [
                            '{{ID}}' => $arItem['ID']
                        ]);

                        continue;
                    }

                    $this->users[] = $arItem['NAME'];
                }

                unset($arItem);

                if (!empty($this->users)) {
                    $arUsers = $this->getUsers();

                    if (!empty($arUsers)) {
                        foreach ($arResult['ITEMS'] as &$arItem) {
                            if (ArrayHelper::keyExists($arItem['NAME'], $arUsers))
                                $arItem['NAME'] = $arUsers[$arItem['NAME']];
                        }

                        unset($arItem);
                    }
                }
            }

            $this->arResult = $arResult;

            unset($arResult, $arParams, $arIBlock, $arSort, $arFilter);

            $this->includeComponentTemplate();
        }

        return null;
    }

    /**
     * Получает массив пользователей вида Логин => Имя
     * @return array
     */
    private function getUsers() {
        if (!empty($this->users)) {
            return Arrays::from(
                UserTable::getList([
                    'select' => [
                        'LOGIN',
                        'NAME',
                        'LAST_NAME',
                        'SECOND_NAME'
                    ],
                    'filter' => [
                        'LOGIN' => $this->users
                    ]
                ])->fetchAll()
            )->indexBy('LOGIN')->each(function ($key, &$value) {
                $name = [];

                if (!empty($value['NAME']))
                    $name[] = $value['NAME'];

                if (!empty($value['LAST_NAME']))
                    $name[] = $value['LAST_NAME'];

                if (!empty($name))
                    $value['FULL_NAME'] = implode(' ', $name);
                else
                    $value['FULL_NAME'] = Loc::getMessage('C_MAIN_REVIEWS_COMPONENT_USER_NAME_EMPTY');
            })->asArray(function ($key, $value) {
                return [
                    'key' => $key,
                    'value' => $value['FULL_NAME']
                ];
            });
        } else
            return [];
    }
}