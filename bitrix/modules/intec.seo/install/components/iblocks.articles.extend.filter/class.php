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
use intec\seo\models\articles\Template;
use intec\seo\models\filter\Condition;

class IntecSeoIblocksArticlesExtendFilterComponent extends CBitrixComponent
{
    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams = ArrayHelper::merge([
            'IBLOCK_ID' => null,
            'SECTION_ID' => null,
            'ELEMENT_ID' => null,
            'SECTIONS_ID' => null,
            'FILTER_NAME' => null,
            'CURRENT_URL' => null,
            'FILTER_MODE' => 'single', /* for add iblocks in filter (single - one iblock)  (many - few iblocks) */
            'QUANTITY' => 5,
            'INCLUDE_SUBSECTIONS' => 'Y',
            'HAS_COUNT' => null,
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000
        ], $arParams);

        $arParams['IBLOCK_ID'] = Type::toInteger($arParams['IBLOCK_ID']);
        $arParams['SECTION_ID'] = Type::toInteger($arParams['SECTION_ID']);
        $arParams['ELEMENT_ID'] = Type::toInteger($arParams['ELEMENT_ID']);;
        $arParams['QUANTITY'] = Type::toInteger($arParams['QUANTITY']);
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

        if ($arParams['COUNT'] < 1)
            $arParams['COUNT'] = 1;

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

        $sFilterMode = ArrayHelper::fromRange(['single','many'], $arParams['FILTER_MODE']);

        $this->arResult = [
            'IBLOCK' => null,
            'SECTION' => null,
            'FILTER_MODE_SINGLE' => $sFilterMode == 'manu' ? false : true,
            'ELEMENT_ID' => !empty($this->arParams['ELEMENT_ID']) ? $arParams['ELEMENT_ID'] : null,
            'FILTER' => null
        ];

        if (
            empty($arParams['IBLOCK_ID']) ||
            empty($arParams['SECTION_ID'])
        ) return null;

        if ($this->startResultCache()) {
            $iIblockId = $arParams['IBLOCK_ID'];
            $iSectionId = $arParams['SECTION_ID'];

            $arResult = $this->arResult;
            $sCurrentUrl = $this->arParams['CURRENT_URL'];
            $iQuantity = $this->arParams['QUANTITY'];
            $bUseCondition = false;
            $iPageNumber = 0;

            if (empty($iSectionId)) {
                $this->abortResultCache();
                return null;
            }

            $arUrlParts = explode('?', $sCurrentUrl, 2);
            $sCurrentUrl = $arUrlParts[0];

            if (!empty($sCurrentUrl)) {
                $arConditions = Condition::find()
                    ->with(['articles', 'sections', 'url', 'sites'])
                    ->where([
                        'active' => 1,
                        'iBlockId' => $iIblockId
                    ])
                    ->forSections([null, $iSectionId])
                    ->forSites([SITE_ID])
                    ->orderBy(['sort' => SORT_ASC])
                    ->one();
            } else {
                $arConditions = null;
            }

            if (!empty($arConditions) && !empty($sCurrentUrl)) {
                $arConditionsRecordUrls = $arConditions->getRelatedRecord('url');

                if (!empty($arConditionsRecordUrls)) {
                    foreach ($arConditionsRecordUrls as $key => $arConditionsRecordUrl) {
                        $arConditionsUrl = $arConditionsRecordUrl->getAttribute('source');
                        $arConditionsUrlActive = $arConditionsRecordUrl->getAttribute('active');

                        if ($arConditionsUrl == $sCurrentUrl && $arConditionsUrlActive === 1) {
                            $iPageNumber = $key;
                            $bUseCondition = true;
                            break;
                        }
                    }
                }
            }

            if (!$bUseCondition) {
                if (empty($arResult['ELEMENT_ID'])) {
                    $arTemplate = Template::find()
                        ->with(['sections', 'elements', 'articles', 'sites'])
                        ->where([
                            'active' => 1,
                            'iBlockId' => $iIblockId
                        ])
                        ->forSections(!empty($iSectionId) ? [null, $iSectionId] : [null])
                        ->forSites([SITE_ID])
                        ->orderBy(['sort' => SORT_DESC])
                        ->limit(1)
                        ->one();
                } else {
                    $arTemplate = Template::find()
                        ->with(['sections', 'elements', 'articles', 'sites'])
                        ->where([
                            'active' => 1,
                            'iBlockId' => $iIblockId
                        ])
                        ->forElements(!empty($arResult['ELEMENT_ID']) ? [$arResult['ELEMENT_ID']] : [null])
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

            if ($bUseCondition) {
                $arArticlesRelated = $arConditions->getRelatedRecord('articles');
            }
            else {
                $arArticlesRelated = $arTemplate->getRelatedRecord('articles');

                if (empty($arResult['ELEMENT_ID'])) {
                    $arSectionsRecords = $arTemplate->getRelatedRecord('sections');
                    $bIsCurrentSection = false;

                    foreach ($arSectionsRecords as $arSectionsRecord) {
                        if ($arSectionsRecord->getAttribute('iBlockSectionId') == $iSectionId) {
                            $bIsCurrentSection = true;
                            break;
                        }
                    }

                    if (!$bIsCurrentSection) {
                        $this->abortResultCache();
                        return null;
                    }
                }
            }

            if (!empty($arArticlesRelated)) {
                $arAllArticlesId = [];

                foreach ($arArticlesRelated as $arArticlesRelatedItem) {
                    $arAllArticlesId[] = $arArticlesRelatedItem->iBlockElementId;
                }
            } else {
                $this->abortResultCache();
                return null;
            }

            $iStartItemPosition = 0;

            if ($iPageNumber > 0) {
                $iStartItemPosition = $iPageNumber * $iQuantity;
            }

            $i = 0;

            while ($i <= $iStartItemPosition + $iQuantity) {
                if ($i > $iStartItemPosition) {
                    $arArticlesId[] = current($arAllArticlesId);
                }

                if (next($arAllArticlesId) === false)
                    reset($arAllArticlesId);

                $i++;
            }

            if (!empty($arArticlesId)) {
                $arResult['FILTER'] = [
                    'ID' => $arArticlesId
                ];
            } else {
                $this->abortResultCache();
                return null;
            }

            $arIblockId = [];

            if ($sFilterMode == 'many') {
                $arIblockElement = Arrays::fromDBResult(CIBlockElement::GetList([], [
                    'ID' => $arArticlesId
                ], false, [], ['IBLOCK_ID']))->asArray(function ($index, $item) {
                    return [
                        'value' => $item['IBLOCK_ID']
                    ];
                });

                $bIblockRepeat = false;
                $arIblockElement = Arrays::fromDBResult(CIBlock::GetList([], [
                    'ID' => $arIblockElement
                ], false, [], [
                    'ID'
                ]))->asArray();

                foreach ($arIblockElement as $item) {
                    if (!empty($arIblockId)) {
                        foreach ($arIblockId as $iblockItem) {
                            if ($iblockItem == $item['ID']) {
                                $bIblockRepeat = true;
                            }
                        }
                    }

                    if ($bIblockRepeat)
                        $bIblockRepeat = false;
                    else
                        $arIblockId[] = $item['ID'];
                }
            } else {
                $arIblockElement = CIBlockElement::GetList([], [
                    'ID' => array_shift($arArticlesId)
                ])->Fetch();

                $arIblockId = CIBlock::GetList([], [
                    'ID' => $arIblockElement['IBLOCK_ID']
                ])->Fetch();

                $arIblockId = $arIblockId['ID'];
            }

            $arResult['FILTER']['IBLOCK_ID'] = $arIblockId;

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