<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\bitrix\iblock\Elements;
use intec\core\bitrix\iblock\ElementsQuery;
use intec\core\bitrix\iblock\Sections;
use intec\core\bitrix\iblock\SectionsQuery;
use intec\core\collections\Scalars;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

$arResult['SECTIONS'] = [];

if (!empty($arResult['CATEGORIES'])) {
    $arSections = new Scalars();
    $arElements = new Scalars();

    foreach ($arResult['CATEGORIES'] as &$arCategory)
        foreach ($arCategory['ITEMS'] as &$arItem) {
            $arItem['ELEMENT'] = null;
            $arItem['SECTION'] = null;

            if ($arItem['MODULE_ID'] != 'iblock')
                continue;

            $iElementId = ArrayHelper::getValue($arItem, 'ITEM_ID');
            $iElementId = Type::toInteger($iElementId);

            if (empty($iElementId))
                continue;

            $arElements->addUnique($iElementId);
        }

    unset($arItem, $arCategory);

    if (!$arElements->isEmpty()) {
        $arElements = new ElementsQuery([
            'filter' => [
                'ID' => $arElements->asArray()
            ]
        ]);

        $arElements = $arElements
            ->execute()
            ->handleFiles()
            ->indexBy('ID');

        foreach ($arElements as $arElement) {
            if (!empty($arElement['IBLOCK_SECTION_ID']))
                $arSections->addUnique($arElement['IBLOCK_SECTION_ID']);
        }

        if (!$arSections->isEmpty()) {
            $arSections = new SectionsQuery([
                'filter' => [
                    'ID' => $arSections->asArray()
                ]
            ]);

            $arSections = $arSections
                ->execute()
                ->handleFiles()
                ->indexBy('ID');
        }
    }

    if ($arElements->isEmpty())
        $arElements = new Elements();

    if ($arSections->isEmpty())
        $arSections = new Sections();

    if (!$arElements->isEmpty()) {
        foreach ($arResult['CATEGORIES'] as &$arCategory)
            foreach ($arCategory['ITEMS'] as &$arItem) {
                if ($arItem['MODULE_ID'] != 'iblock')
                    continue;

                $arElement = ArrayHelper::getValue($arItem, 'ITEM_ID');
                $arElement = Type::toInteger($arElement);

                if (empty($arElement))
                    continue;

                $arElement = $arElements->get($arElement);

                if (empty($arElement))
                    continue;

                $arItem['ELEMENT'] = $arElement;

                if (!empty($arElement['IBLOCK_SECTION_ID']))
                    $arItem['SECTION'] = $arSections->get($arElement['IBLOCK_SECTION_ID']);
            }

        unset($arItem, $arCategory);
    }

    $arResult['SECTIONS'] = $arSections->asArray();

    unset($arElements, $arSections);
}
