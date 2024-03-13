<?php

use Bitrix\Main\Loader;
use Bitrix\Iblock\Template\Engine as TemplateEngine;
use Bitrix\Iblock\Template\Entity\Section as TemplateSection;
use intec\Core;
use intec\core\base\writers\ClosureWriter;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\net\Url;
use intec\seo\filter\condition\FilterHelper;
use intec\seo\filter\tags\OfferPropertyTag;
use intec\seo\filter\tags\PropertyTag;
use intec\seo\models\filter\Condition;
use intec\seo\models\filter\condition\Section;
use intec\seo\models\filter\ConditionQuery;
use intec\seo\models\filter\Url as FilterUrl;

class IntecSeoFilterTagsComponent extends CBitrixComponent
{
    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams = ArrayHelper::merge([
            'IBLOCK_ID' => null,
            'SECTION_ID' => null,
            'INCLUDE_SUBSECTIONS' => 'A',
            'QUANTITY' => 0,
            'SORT' => 'none',
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

        $arParams['INCLUDE_SUBSECTIONS'] = ArrayHelper::fromRange(['A', 'Y', 'N'], $arParams['INCLUDE_SUBSECTIONS']);

        if ($arParams['CACHE_TIME'] < 0)
            $arParams['CACHE_TIME'] = 0;

        return $arParams;
    }

    /**
     * @inheritdoc
     */
    public function executeComponent()
    {
        if (
            !Loader::includeModule('iblock') ||
            !Loader::includeModule('intec.seo')
        ) return null;

        $arParams = $this->arParams;
        $arResult = [
            'STRICT' => false
        ];

        CBitrixComponent::includeComponentClass('intec.seo:filter.meta');

        /** Получаем примененные условия к текущему разделу */
        /** @var Condition[] $arAppliedConditions */
        $arAppliedConditions = IntecSeoFilterMetaComponent::getAppliedConditions();

        foreach ($arAppliedConditions as $condition)
            if ($condition->tagRelinkingStrict)
                $arResult['STRICT'] = true;

        if (empty($arParams['IBLOCK_ID']) || empty($arParams['SECTION_ID']))
            return null;

        if ($this->startResultCache(false, serialize([$arResult['STRICT']]))) {
            $arResult['IBLOCK'] = CIBlock::GetList([], [
                'ID' => $arParams['IBLOCK_ID']
            ])->Fetch();

            $arResult['SECTION'] = null;
            $arResult['SECTIONS'] = [];

            if (!empty($arResult['IBLOCK'])) {
                /** Получаем разделы для просчета тегов */
                $arResult['SECTION'] = CIBlockSection::GetList([], [
                    'IBLOCK_ID' => $arResult['IBLOCK']['ID'],
                    'ID' => $arParams['SECTION_ID']
                ])->Fetch();

                if (!empty($arResult['SECTION'])) {
                    $arResult['SECTIONS'][$arResult['SECTION']['ID']] = $arResult['SECTION'];

                    if ($arParams['INCLUDE_SUBSECTIONS'] === 'A' || $arParams['INCLUDE_SUBSECTIONS'] === 'Y') {
                        $rsSections = [
                            'IBLOCK_ID' => $arResult['IBLOCK']['ID'],
                            '>LEFT_MARGIN' => $arResult['SECTION']['LEFT_MARGIN'],
                            '<RIGHT_MARGIN' => $arResult['SECTION']['RIGHT_MARGIN'],
                            '>DEPTH_LEVEL' => $arResult['SECTION']['DEPTH_LEVEL']
                        ];

                        if ($this->arParams['INCLUDE_SUBSECTIONS'] === 'A')
                            $rsSections['GLOBAL_ACTIVE'] = 'Y';

                        $rsSections = CIBlockSection::GetList([], $rsSections);

                        while ($arSection = $rsSections->Fetch())
                            $arResult['SECTIONS'][$arSection['ID']] = $arSection;

                        unset($rsSections, $arSection);
                    }
                }
            }

            if (empty($arResult['SECTIONS'])) {
                $this->endResultCache();
                return null;
            }

            /** Подключение модуля каталога */
            $bCatalogUse = Loader::includeModule('catalog');
            $arSku = false;

            if ($bCatalogUse)
                $arSku = CCatalogSku::GetInfoByProductIBlock($arResult['IBLOCK']['ID']);

            $arSectionsId = ArrayHelper::getKeys($arResult['SECTIONS']);

            /** Получаем условия для просчета тегов */
            $arResult['CONDITIONS'] = [];
            $arConditions = Condition::find()
                ->with(['sections'])
                ->where([
                    'active' => 1,
                    'iBlockId' => $arResult['IBLOCK']['ID']
                ])
                ->forSections(array_merge([null], $arSectionsId))
                ->forSites([SITE_ID])
                ->orderBy(['sort' => SORT_ASC])
                ->all();

            /** @var Condition $oCondition */
            foreach ($arConditions as $oCondition) {
                if (!$arResult['STRICT']) {
                    if (
                        $oCondition->tagMode === Condition::TAG_MODE_SELF ||
                        $oCondition->tagMode === Condition::TAG_MODE_RECURSIVE
                    ) {
                        $arConditionSections = $oCondition->getSections(true);
                        $arConditionSectionsId = [];

                        /** Если условие для всех разделов, устанавливаем ему текущие разделы */
                        if ($arConditionSections->isEmpty()) {
                            $arConditionSectionsId = $arSectionsId;
                        } else {
                            /** Иначе только пересекающиеся с текущими разделами */
                            /** @var Section $oConditionSection */
                            foreach ($arConditionSections as $oConditionSection)
                                $arConditionSectionsId[] = $oConditionSection->iBlockSectionId;

                            $arConditionSectionsId = array_intersect($arConditionSectionsId, $arSectionsId);
                        }

                        /** Если режим тегов - текущий раздел, то устанавливаем свойству текущий раздел, иначе если условие не привязано к текущему разделу - не добавляем его в список условий */
                        if ($oCondition->tagMode === Condition::TAG_MODE_SELF)
                            if (ArrayHelper::isIn($arResult['SECTION']['ID'], $arConditionSectionsId)) {
                                $arConditionSectionsId = [$arResult['SECTION']['ID']];
                            } else {
                                continue;
                            }

                        $arConditionSections = [];

                        /** Устанавливаем новые разделы для условия */
                        foreach ($arConditionSectionsId as $iConditionSectionId) {
                            $oConditionSection = new Section();
                            $oConditionSection->conditionId = $oCondition->id;
                            $oConditionSection->iBlockSectionId = $iConditionSectionId;

                            $arConditionSections[] = $oConditionSection;
                        }

                        /** Записываем новые разделы в модель */
                        $oCondition->populateRelation('sections', $arConditionSections);

                        /** Добавляем условие в список, если условие еще не существует */
                        if (!isset($arResult['CONDITIONS'][$oCondition->id]))
                            $arResult['CONDITIONS'][$oCondition->id] = $oCondition;
                    } else if (
                        $oCondition->tagMode === Condition::TAG_MODE_SECTIONS ||
                        $oCondition->tagMode === Condition::TAG_MODE_ALL
                    ) {
                        /** Добавляем условие в список, если условие еще не существует */
                        if (!isset($arResult['CONDITIONS'][$oCondition->id]))
                            $arResult['CONDITIONS'][$oCondition->id] = $oCondition;
                    }
                }

                if (
                    $oCondition->tagMode === Condition::TAG_MODE_RELINKING ||
                    $oCondition->tagMode === Condition::TAG_MODE_ALL
                ) {
                    /** Выбираем все условия перелинковки */
                    /** @var ConditionQuery $arRelinkingConditions */
                    $arRelinkingConditions = $oCondition->getTagRelinkingConditions();
                    $arRelinkingConditions = $arRelinkingConditions
                        ->where([
                            'active' => 1
                        ])
                        ->forSites([SITE_ID])
                        ->orderBy(['sort' => SORT_ASC])
                        ->all();

                    /** Добавляем в общий список, заменяя другие условия */
                    /** @var Condition $oRelinkingCondition */
                    foreach ($arRelinkingConditions as $oRelinkingCondition)
                        $arResult['CONDITIONS'][$oRelinkingCondition->id] = $oRelinkingCondition;
                }
            }

            unset($arRelinkingConditions, $oRelinkingCondition, $arConditions, $arConditionSections, $arConditionSectionsId);

            /** @var Condition $oCondition */
            $oCondition = null;
            $arItemCombination = null;
            $arItems = [];

            $oWriter = new ClosureWriter(function ($oUrl, $arCombination, $arIBlock, $arSection) use (&$oCondition, &$arItemCombination, &$arItems) {
                /** @var FilterUrl $oUrl */
                $arFilter = FilterHelper::getFilterFromCombination($arCombination, $arIBlock, $arSection);
                $arItemCombination = $arCombination;

                if (empty($arFilter))
                    return false;

                $arFilter['ACTIVE'] = 'Y';
                $arFilter['ACTIVE_DATE'] = 'Y';

                if (isset($arFilter['ID'])) {
                    $arFilter['ID']->arFilter['ACTIVE'] = 'Y';
                    $arFilter['ID']->arFilter['ACTIVE_DATE'] = 'Y';
                }

                $arFilter['INCLUDE_SUBSECTIONS'] = $oCondition->recursive ? 'Y' : 'N';

                $iCount = CIBlockElement::GetList([
                    'SORT' => 'ASC'
                ], $arFilter, false, [
                    'nPageSize' => 1
                ]);

                $iCount = $iCount->SelectedRowsCount();

                if ($iCount < 1)
                    return false;

                $oEntity = new TemplateSection($arSection['ID']);
                $sName = $value = StringHelper::replaceMacros($oCondition->tagName, [
                    'SEO_FILTER_PAGINATION_PAGE_NUMBER' => '',
                    'SEO_FILTER_PAGINATION_TEXT' => ''
                ]);

                $sName = TemplateEngine::process($oEntity, $sName);

                if (empty($sName) && !Type::isNumeric($sName))
                    return false;

                $arItems[] = [
                    'NAME' => $sName,
                    'ACTIVE' => false,
                    'TARGET' => false,
                    'URL' => [
                        'SOURCE' => $oUrl->source,
                        'TARGET' => $oUrl->target
                    ]
                ];

                return true;
            }, false);

            $arResult['ITEMS'] = [];
            $arConditionsId = [];

            /** Устанавливаем обработчик свойств мета-информации */
            PropertyTag::setHandler(function ($arParameters) use (&$arResult, &$arItemCombination) {
                $arValues = [];

                foreach ($arItemCombination as $arProperty) {
                    $sCode = $arProperty['CODE'];

                    if (empty($sCode) && !Type::isNumeric($sCode))
                        $sCode = $arProperty['ID'];

                    if (!ArrayHelper::isIn($sCode, $arParameters))
                        continue;

                    if ($arProperty['IBLOCK_ID'] != $arResult['IBLOCK']['ID'])
                        continue;

                    $arValues[] = $arProperty['VALUE']['TEXT'];
                }

                return $arValues;
            });

            /** Устанавливаем обработчик свойств торговых предложений мета-информации */
            OfferPropertyTag::setHandler(function ($arParameters) use (&$arResult, &$arItemCombination, &$arSku) {
                $arValues = [];

                if ($arSku === false)
                    return $arValues;

                foreach ($arItemCombination as $arProperty) {
                    $sCode = $arProperty['CODE'];

                    if (empty($sCode) && !Type::isNumeric($sCode))
                        $sCode = $arProperty['ID'];

                    if (!ArrayHelper::isIn($sCode, $arParameters))
                        continue;

                    if ($arProperty['IBLOCK_ID'] != $arSku['IBLOCK_ID'])
                        continue;

                    $arValues[] = $arProperty['VALUE']['TEXT'];
                }

                return $arValues;
            });

            foreach ($arResult['CONDITIONS'] as $oCondition) {
                if (empty($oCondition->tagName) && !Type::isNumeric($oCondition->tagName))
                    continue;

                $arConditionsId[] = $oCondition->id;
                $oCondition->generateUrl(null, $oWriter);
            }

            /** Убираем обработчик свойств мета-информации */
            PropertyTag::setHandler(null);
            /** Убираем обработчик свойств торговых предложений мета-информации */
            OfferPropertyTag::setHandler(null);

            $arTargets = [];

            /** Собираем существующие целевые адреса */
            foreach ($arItems as $arItem)
                $arTargets[] = $arItem['URL']['TARGET'];

            if (!empty($arTargets)) {
                $arTargets = array_unique($arTargets);
                $arTargets = FilterUrl::find()->where([
                    //'active' => 1,
                    'conditionId' => $arConditionsId,
                    'target' => $arTargets
                ])->indexBy('target')->all();

                foreach ($arItems as $arItem) {
                    /** @var FilterUrl $oTarget */
                    $oTarget = $arTargets->get($arItem['URL']['TARGET']);

                    if (!empty($oTarget) && $oTarget->getAttribute('active') == 1)
                        $arItem['TARGET'] = true;

                    if (empty($oTarget))
                        $arItem['TARGET'] = false;

                    /** Получаем кол-во товаров */
                    if (!empty($oTarget))
                        $arItem['COUNT'] = $oTarget->getAttribute('iBlockElementsCount');

                    if (!empty($oTarget))
                        $arItem['SORT'] = $oTarget->getAttribute('sort');

                    $arResult['ITEMS'][] = $arItem;
                }
            }

            $this->arResult = $arResult;
            $this->endResultCache();
        }

        unset($arResult);

        /** Устанавливаем текущую страницу с параметрами */
        $oUrl = new Url(Core::$app->request->getUrl());
        $sUrl = $oUrl->build();

        /** Устанавливаем текущую страницу без параметров */
        $oUrl->getQuery()->removeAll();
        $sPage = $oUrl->build();

        /** Получаем текущий перенаправленный адрес */
        $oUrl = FilterUrl::getCurrent();

        foreach ($this->arResult['ITEMS'] as &$arItem) {
            $arParts = explode('?', $arItem['URL']['SOURCE']);

            /** Если у адреса есть параметры, то сравниваем со страницей с параметрами */
            if (isset($arParts[1]) && (!empty($arParts[1]) || Type::isNumeric($arParts[1]))) {
                $arItem['ACTIVE'] = $arItem['URL']['SOURCE'] === $sUrl;
            } else {
                /** Иначе сравниваем со страницей без параметров */
                $arItem['ACTIVE'] = $arItem['URL']['SOURCE'] === $sPage;
            }

            /** Если неактивен и есть целевой адрес, и текущий перенаправленный адрес не пустой */
            if (!$arItem['ACTIVE'] && $arItem['TARGET'] && $oUrl !== null)
                $arItem['ACTIVE'] = $arItem['URL']['TARGET'] === $oUrl->target;
        }

        /** Если указанно ограничение по кол-ву то оставляем нужное кол-во */
        if (!empty($arParams['QUANTITY']) && $arParams['QUANTITY'] > 0) {
            $this->arResult['ITEMS'] = array_slice($this->arResult['ITEMS'], 0, $arParams['QUANTITY']);
        }

        /** Сортировка тегов */
        if ($arParams['SORT'] !== 'none') {
            switch ($arParams['SORT']) {
                case 'name':
                    sort($this->arResult['ITEMS']);
                    break;
                case 'nameDesc':
                    rsort($this->arResult['ITEMS']);
                    break;
                case 'count':
                    usort($this->arResult['ITEMS'], function($a, $b){
                        return ($a['COUNT'] - $b['COUNT']);
                    });
                    break;
                case 'countDesc':
                    usort($this->arResult['ITEMS'], function($a, $b){
                        return -($a['COUNT'] - $b['COUNT']);
                    });
                    break;
                case 'sorting':
                    usort($this->arResult['ITEMS'], function($a, $b){
                        return ($a['SORT'] - $b['SORT']);
                    });
                    break;
                case 'sortingDesc':
                    usort($this->arResult['ITEMS'], function($a, $b){
                        return -($a['SORT'] - $b['SORT']);
                    });
                    break;
            }
        }

        $this->includeComponentTemplate();

        return $this->arResult;
    }
}