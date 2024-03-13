<?php

use Bitrix\Main\Loader;
use Bitrix\Iblock\Template\Engine as TemplateEngine;
use Bitrix\Iblock\Template\Entity\Section as TemplateSection;
use Bitrix\Iblock\Template\Entity\Element as TemplateElement;
use intec\core\base\condition\DataProviderResult;
use intec\core\base\condition\modifiers\ClosureResultModifier;
use intec\core\base\condition\providers\ClosureDataProvider;
use intec\core\bitrix\conditions\IBlockPropertyCondition;
use intec\core\bitrix\conditions\IBlockSectionCondition;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\seo\models\iblocks\metadata\Template;
use intec\seo\models\autofill\Template as AutofillTemplate;
use intec\seo\models\filter\Condition;

class IntecSeoIblocksSectionExtendFilterComponent extends CBitrixComponent
{
    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams = ArrayHelper::merge([
            'IBLOCK_ID' => null,
            'SECTION_ID' => null,
            'SECTIONS_ID' => null,
            'FILTER_NAME' => null,
            'CURRENT_URL' => null,
            'MODE' => 'parent',
            'INCLUDE_SUBSECTIONS' => 'Y',
            'HAS_COUNT' => null,
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

        if (Type::isArray($arParams['SECTIONS_ID'])) {
            $arParams['SECTIONS_ID'] = array_filter($arParams['SECTIONS_ID']);

            if (empty($arParams['SECTIONS_ID']))
                $arParams['SECTIONS_ID'] = null;
        } else {
            $arParams['SECTIONS_ID'] = null;
        }

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
            'SECTIONS' => [],
            'FILTER' => null
        ];

        if (
            empty($arParams['IBLOCK_ID']) ||
            empty($arParams['SECTION_ID'])
        ) return null;

        if ($this->startResultCache()) {
            $this->arResult['IBLOCK'] = CIBlock::GetList([], [
                'ID' => $arParams['IBLOCK_ID']
            ])->Fetch();

            if (!empty($this->arResult['IBLOCK'])) {
                $this->arResult['SECTION'] = CIBlockSection::GetList([], [
                    'IBLOCK_ID' => $this->arResult['IBLOCK']['ID'],
                    'ID' => $arParams['SECTION_ID']
                ])->Fetch();
            }

            $arResult = $this->arResult;
            $arIds = [];
            $bUseCondition = false;
            $bRelationElements = false;
            $bUseTemplateCount = false;
            $bRandom = false;
            $iPageNumber = 0;
            $iElementSum = 0;
            $sCurrentUrl = $this->arParams['CURRENT_URL'];
            $iCount = 0;

            if (empty($arResult['SECTION'])) {
                $this->abortResultCache();
                return null;
            }

            if (!empty($sCurrentUrl)) {
                $arConditions = Condition::find()
                    ->with(['autofillSections', 'sections', 'url', 'sites'])
                    ->where([
                        'active' => 1,
                        'iBlockId' => $arResult['IBLOCK']['ID']
                    ])
                    ->forSections([null, $arResult['SECTION']['ID']])
                    ->forSites([SITE_ID])
                    ->orderBy(['sort' => SORT_ASC])
                    ->one();
            } else {
                $arConditions = null;
            }

            if (!empty($arConditions && !empty($sCurrentUrl))) {
                $arConditionsRecordUrls = $arConditions->getRelatedRecord('url');

                if (!empty($arConditionsRecordUrls)) {
                    foreach ($arConditionsRecordUrls as $key => $arConditionsRecordUrl) {
                        $arConditionsUrl = $arConditionsRecordUrl->getAttribute('source');

                        if ($arConditionsUrl == $sCurrentUrl) {
                            $iPageNumber = $key;
                            $bUseCondition = true;
                            break;
                        }

                        $iElementSum = $iElementSum + $arConditionsRecordUrl->getAttribute('iBlockElementsCount');
                    }
                }
            }

            if ($bUseCondition) {
                $arConditionsRecordAutofillSections = $arConditions->getRelatedRecord('autofillSections');

                if (!empty($arConditionsRecordAutofillSections)) {
                    foreach ($arConditionsRecordAutofillSections as $arConditionsRecordAutofillSectionItem) {
                        $iSectionId = $arConditionsRecordAutofillSectionItem->getAttribute('iBlockSectionId');

                        if ($iSectionId == $arResult['SECTION']['ID'])
                            continue;

                        $arResult['SECTIONS'][] = $iSectionId;
                    }
                } else {
                    $bUseCondition = false;
                }
            }

            if (!$bUseCondition) {
                $arTemplate = AutofillTemplate::find()
                    ->with(['sections', 'fillingSections', 'elements', 'sites'])
                    ->where([
                        'active' => 1,
                        'iBlockId' => $arResult['IBLOCK']['ID']
                    ])
                    ->forSections(!empty($arResult['SECTION']['ID']) ? [null, $arResult['SECTION']['ID']] : [null])
                    ->forSites([SITE_ID])
                    ->orderBy(['sort' => SORT_DESC])
                    ->limit(1)
                    ->one();

                if (empty($arTemplate)) {
                    $arTemplate = AutofillTemplate::find()
                        ->with(['sections', 'fillingSections', 'elements', 'sites'])
                        ->where([
                            'active' => 1,
                            'iBlockId' => $arResult['IBLOCK']['ID']
                        ])
                        ->forSections(0)
                        ->forSites([SITE_ID])
                        ->orderBy(['sort' => SORT_DESC])
                        ->limit(1)
                        ->one();
                }
            }

            if (!$bUseCondition && empty($arTemplate)) {
                $this->abortResultCache();
                return null;
            }

            if (!empty($GLOBALS['arCatalogSectionsExtendingFilterMainItems'])) {

                $arMainItems = [
                    'COUNT' => count($GLOBALS['arCatalogSectionsExtendingFilterMainItems']),
                    'ID' => $GLOBALS['arCatalogSectionsExtendingFilterMainItems']
                ];

                $this->arParams['HAS_COUNT'] = $arMainItems['COUNT'];
                $iElementSum = $arMainItems['COUNT'];
            }

            if ($bUseCondition) {
                $iCountTemplate = $arConditions->getAttribute('autofillQuantity');
                $bSelf = $arConditions->getAttribute('autofillSelf') == 1 ? true : false;
            } else {
                $iCountTemplate = $arTemplate->getAttribute('quantity');
                $bSelf = $arTemplate->getAttribute('self') == 1 ? true : false;
                $bRandom = $arTemplate->getAttribute('random') == 1 ? true : false;
            }

            if (!empty($iCountTemplate))
                $iCount = $iCountTemplate;

            if ($bSelf) {
                $iCount = $iCount - $this->arParams['HAS_COUNT'];
            }

            if (!$bUseCondition) {
                $arRelationRecord = $arTemplate->getRelatedRecord('elements');

                if (empty($arRelationRecord)) {
                    $arRelationRecord = $arTemplate->getRelatedRecord('fillingSections');

                    if (!empty($arRelationRecord)) {
                        foreach ($arRelationRecord as $arRelationItem) {
                            $iSectionId = $arRelationItem->iBlockSectionId;

                            if ($iSectionId == $arResult['SECTION']['ID'])
                                continue;

                            $arResult['SECTIONS'][] = $iSectionId;
                        }
                    }
                    $bUseTemplateCount = true;
                } else {
                    $bRelationElements = true;
                    $arSelectedElements = [];

                    foreach ($arRelationRecord as $arRelationItem) {
                        $iElementId = $arRelationItem->getAttribute('iBlockElementId');

                        $arSelectedElements[] = $iElementId;
                    }
                }
            }

            if (!empty($arResult['SECTIONS']) || $bRelationElements) {
                if ($iCount > 0) {
                    $iStartItemPosition = 0;

                    if ($bSelf) {
                        $iCountTemplate = $iCountTemplate - $iElementSum;
                    }

                    if ($bUseCondition) {
                        $iStartItemPosition = $iPageNumber * $iCountTemplate;
                    }

                    if ($bRelationElements) {
                        $arElements = Arrays::fromDBResult(CIBlockElement::GetList([
                            'SORT' => 'ASC'
                        ], [
                            'ID' => $arSelectedElements,
                            'ACTIVE' => 'Y',
                            'ACTIVE_DATE' => 'Y',
                            'CATALOG_AVAILABLE' => 'Y',
                        ], false, false, ['ID']))->asArray();
                        unset ($arSelectedElements);
                    } else {
                        $arElements = Arrays::fromDBResult(CIBlockElement::GetList([
                            'SORT' => 'ASC'
                        ], [
                            'IBLOCK_ID' => $arResult['IBLOCK']['ID'],
                            'SECTION_ID' => $arResult['SECTIONS'],
                            'ACTIVE' => 'Y',
                            'ACTIVE_DATE' => 'Y',
                            'CATALOG_AVAILABLE' => 'Y',
                            'INCLUDE_SUBSECTIONS' => $arParams['INCLUDE_SUBSECTIONS'] === 'Y' ? 'Y' : 'N'
                        ], false, false, ['ID']))->asArray();
                    }

                    if ($bRandom) {
                        shuffle($arElements);
                    }

                    $i = 0;
                    if ($bUseCondition) {
                        while ($i <= $iStartItemPosition + $iCountTemplate) {
                            if ($i > $iStartItemPosition) {
                                $arIds[] = current($arElements)['ID'];
                            }

                            if (next($arElements) === false)
                                reset($arElements);

                            $i++;
                        }
                    } else {
                        foreach ($arElements as $arElement) {
                            $arIds[] = $arElement['ID'];
                            $i++;
                            if ($i >= $iCountTemplate)
                                break;
                        }
                    }
                }
            }

            if ($iCount <= 0 || $bUseTemplateCount) {
                $iCount = $iCountTemplate;
            } else if ($iCount < $this->arParams['HAS_COUNT']) {
                $iCount = $this->arParams['HAS_COUNT'];
            }

            if ($bSelf) {
                if (!empty($arMainItems)) {
                    $arSelfItemsIds = $arMainItems['ID'];
                } else {
                    $arSelfItemsIds = Arrays::fromDBResult(CIBlockElement::GetList([
                        'SORT' => 'ASC'
                    ], [
                        'IBLOCK_ID' => $arResult['IBLOCK']['ID'],
                        'SECTION_ID' => $arResult['SECTION']['ID'],
                        'ACTIVE' => 'Y',
                    ], false, array(), array('ID')))->asArray();

                    foreach ($arSelfItemsIds as &$arItemId) {
                        $arItemId = $arItemId['ID'];
                    }
                }

                $arSelfItemsIds = array_reverse($arSelfItemsIds);

                $i = $iCount;
                foreach ($arSelfItemsIds as $arItemId) {
                    if ($i <= 0)
                        break;

                    $arIds[] = $arItemId;
                    $i--;
                }

                unset($i);
            }

            if (!empty($arIds))
                $arResult['FILTER'] = [
                    'ID' => $arIds
                ];

            $this->arResult = $arResult;
            $this->endResultCache();
        }

        if (!empty($this->arResult['FILTER']) && (!empty($arParams['FILTER_NAME'] || Type::isNumeric($arParams['FILTER_NAME'])))) {
            if (!Type::isArray($GLOBALS[$arParams['FILTER_NAME']]))
                $GLOBALS[$arParams['FILTER_NAME']] = [];

            $GLOBALS[$arParams['FILTER_NAME']] = ArrayHelper::merge(
                $GLOBALS[$arParams['FILTER_NAME']],
                $this->arResult['FILTER']
            );
        }

        return $this->arResult;
    }
}