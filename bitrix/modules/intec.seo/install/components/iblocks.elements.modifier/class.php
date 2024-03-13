<?php

use Bitrix\Main\Loader;
use Bitrix\Iblock\Template\Engine as TemplateEngine;
use Bitrix\Iblock\Template\Entity\Element as TemplateElement;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;
use intec\seo\models\iblocks\elements\names\Template;

class IntecSeoIblocksElementsModifierComponent extends CBitrixComponent
{
    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams = ArrayHelper::merge([
            'IBLOCK_ID' => null,
            'SECTION_ID' => null,
            'SECTION_CODE' => null,
            'ITEMS' => null,
            'ENCODE' => 'Y',
            'PAGE_USE' => 'N',
            'PAGE_COUNT' => null,
            'PAGE_SIZE' => null,
            'PAGE_NUMBER' => null,
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000
        ], $arParams);

        $arParams['IBLOCK_ID'] = Type::toInteger($arParams['IBLOCK_ID']);
        $arParams['SECTION_ID'] = Type::toInteger($arParams['SECTION_ID']);
        $arParams['CACHE_TIME'] = Type::toInteger($arParams['CACHE_TIME']);

        if ($arParams['IBLOCK_ID'] < 1)
            $arParams['IBLOCK_ID'] = null;

        if ($arParams['SECTION_ID'] < 1)
            $arParams['SECTION_ID'] = null;

        if (empty($arParams['PAGE_COUNT']) || empty($arParams['PAGE_SIZE']) || empty($arParams['PAGE_NUMBER']))
            $arParams['PAGE_USE'] = 'N';

        if ($arParams['CACHE_TIME'] < 0)
            $arParams['CACHE_TIME'] = 0;

        return $arParams;
    }

    public function executeComponent()
    {
        if (
            !Loader::includeModule('iblock') ||
            !Loader::includeModule('intec.seo')
        ) return null;

        $arParams = $this->arParams;
        $this->arResult = [
            'IBLOCK' => null,
            'SECTION' => null,
            'QUANTITY' => null,
            'ITEMS' => []
        ];

        if (
            empty($arParams['IBLOCK_ID']) ||
            empty($arParams['SECTION_ID']) &&
            empty($arParams['SECTION_CODE'])
        ) return null;

        if ($this->startResultCache()) {
            $this->arResult['IBLOCK'] = CIBlock::GetList([], [
                'ID' => $arParams['IBLOCK_ID']
            ])->Fetch();

            if (!empty($this->arResult['IBLOCK'])) {
                if (!empty($arParams['SECTION_ID'])) {
                    $this->arResult['SECTION'] = CIBlockSection::GetList([], [
                        'IBLOCK_ID' => $this->arResult['IBLOCK']['ID'],
                        'ID' => $arParams['SECTION_ID']
                    ])->Fetch();
                } else {
                    $this->arResult['SECTION'] = CIBlockSection::GetList([], [
                        'IBLOCK_ID' => $this->arResult['IBLOCK']['ID'],
                        'CODE' => $arParams['SECTION_CODE']
                    ])->Fetch();
                }
            }

            $this->endResultCache();
        }

        $arResult = $this->arResult;
        $arResult['TEMPLATE'] = null;

        if (empty($arResult['SECTION']))
            return null;

        $arResult['TEMPLATE'] = Template::find()
            ->with(['sections', 'sites'])
            ->where([
                'active' => 1,
                'iBlockId' => $arResult['IBLOCK']['ID']
            ])
            ->forSections(!empty($arResult['SECTION']['ID']) ? [null, $arResult['SECTION']['ID']] : [null])
            ->forSites([SITE_ID])
            ->orderBy(['sort' => SORT_DESC])
            ->limit(1)
            ->one();

        if (empty($arResult['TEMPLATE']))
            return null;

        $arResult['QUANTITY'] = $arResult['TEMPLATE']->getAttribute('quantity');

        if (empty($arResult['QUANTITY']))
            $arResult['QUANTITY'] = 0;

        $arNames = $arResult['TEMPLATE']->getValue();

        if (Type::isArray($arParams['ITEMS']) && !empty($arNames)) {
            if ($arParams['PAGE_USE'] === 'Y') {
                $iIterations = $arParams['PAGE_SIZE'] * ($arParams['PAGE_NUMBER'] - 1);
                $iNamesCount = count($arNames);

                if ($iIterations > 0) {
                    if ($iIterations > $iNamesCount)
                        $iIterations = $iIterations % $iNamesCount;

                    for ($iIteration = 0; $iIteration < $iIterations; $iIteration++) {
                        if (next($arNames) === false)
                            reset($arNames);
                    }
                }
            }

            $iCounter = 0;

            foreach ($arParams['ITEMS'] as $mKey => $arItem) {
                if ($arResult['QUANTITY'] == 0) {
                    $oEntity = new TemplateElement($arItem['ID']);
                    $sName = current($arNames);
                    $sName = TemplateEngine::process($oEntity, $sName);

                    if ($arParams['ENCODE'] === 'Y')
                        $sName = Html::encode($sName);

                    $arItem['NAME'] = $sName;
                    $arResult['ITEMS'][$mKey] = $arItem;

                    if (next($arNames) === false)
                        reset($arNames);
                } else {
                    if ($iCounter < $arResult['QUANTITY']) {
                        $oEntity = new TemplateElement($arItem['ID']);
                        $sName = current($arNames);
                        $sName = TemplateEngine::process($oEntity, $sName);

                        if ($arParams['ENCODE'] === 'Y')
                            $sName = Html::encode($sName);

                        $arItem['NAME'] = $sName;
                        $arResult['ITEMS'][$mKey] = $arItem;

                        if (next($arNames) === false)
                            reset($arNames);

                    } else {
                        $arResult['ITEMS'][$mKey] = $arItem;
                    }

                    $iCounter++;
                }
            }
        } else {
            $arResult['ITEMS'] = $arParams['ITEMS'];
        }

        $this->arResult = $arResult;

        unset($arResult);

        $this->includeComponentTemplate();

        return $this->arResult;
    }
}