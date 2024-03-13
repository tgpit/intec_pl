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
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\seo\models\iblocks\metadata\Template;

class IntecSeoIblocksMetadataLoaderComponent extends CBitrixComponent
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
            'TYPE' => null,
            'MODE' => null,
            'METADATA_SET' => 'Y',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000
        ], $arParams);

        $arParams['IBLOCK_ID'] = Type::toInteger($arParams['IBLOCK_ID']);
        $arParams['CACHE_TIME'] = Type::toInteger($arParams['CACHE_TIME']);

        if ($arParams['IBLOCK_ID'] < 1)
            $arParams['IBLOCK_ID'] = null;

        $arParams['TYPE'] = ArrayHelper::fromRange(['section', 'element'], $arParams['TYPE']);
        $arParams['MODE'] = ArrayHelper::fromRange(['single', 'multiple'], $arParams['MODE']);

        if ($arParams['MODE'] !== 'single')
            $arParams['METADATA_SET'] = 'N';

        if ($arParams['CACHE_TIME'] < 0)
            $arParams['CACHE_TIME'] = 0;

        return $arParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        if (
            !Loader::includeModule('iblock') ||
            !Loader::includeModule('intec.seo')
        ) return null;

        $arParams = $this->arParams;
        $this->arResult = [
            'IBLOCK' => null,
            'SECTIONS' => [],
            'ELEMENTS' => []
        ];

        if (
            empty($arParams['IBLOCK_ID']) ||
            $arParams['TYPE'] === 'section' && empty($arParams['SECTION_ID']) ||
            $arParams['TYPE'] === 'element' && empty($arParams['ELEMENT_ID'])
        ) return null;

        if ($this->startResultCache()) {
            $this->arResult['IBLOCK'] = CIBlock::GetList([], [
                'ID' => $arParams['IBLOCK_ID']
            ])->Fetch();

            if (!empty($this->arResult['IBLOCK'])) {
                if ($arParams['TYPE'] === 'section') {
                    $rsSections = CIBlockSection::GetList([], [
                        'IBLOCK_ID' => $this->arResult['IBLOCK']['ID'],
                        'ID' => $arParams['SECTION_ID']
                    ]);

                    while ($arSection = $rsSections->Fetch()) {
                        $this->arResult['SECTIONS'][$arSection['ID']] = $arSection;

                        if ($arParams['MODE'] === 'single')
                            break;
                    }

                    unset($rsSections, $arSection);
                } else {
                    $rsElements = CIBlockElement::GetList([], [
                        'IBLOCK_ID' => $this->arResult['IBLOCK']['ID'],
                        'ID' => $arParams['ELEMENT_ID']
                    ]);

                    while ($rsElement = $rsElements->GetNextElement()) {
                        $arElement = $rsElement->GetFields();
                        $arElement['PROPERTIES'] = [];
                        $arElementProperties = $rsElement->GetProperties();

                        foreach ($arElementProperties as $arProperty)
                            $arElement['PROPERTIES'][$arProperty['ID']] = $arProperty;

                        $this->arResult['ELEMENTS'][$arElement['ID']] = $arElement;

                        if ($arParams['MODE'] === 'single')
                            break;
                    }

                    unset($rsElements, $rsElement, $arElement, $arElementProperties);
                }
            }

            $this->endResultCache();
        }

        $arResult = $this->arResult;

        if (
            $arParams['TYPE'] === 'section' && empty($arResult['SECTIONS']) ||
            $arParams['TYPE'] === 'element' && empty($arResult['ELEMENTS'])
        ) return null;

        $arSectionsId = [null];

        if ($arParams['TYPE'] === 'section') {
            foreach ($arResult['SECTIONS'] as &$arSection)
                $arSectionsId[] = $arSection['ID'];

            unset($arSection);
        } else {
            foreach ($arResult['ELEMENTS'] as &$arElement)
                if (!empty($arElement['IBLOCK_SECTION_ID']))
                    $arSectionsId[] = $arElement['IBLOCK_SECTION_ID'];

            unset($arElement);
        }

        $arTemplates = Template::find()
            ->with(['sections', 'sites'])
            ->where([
                'active' => 1,
                'iBlockId' => $arResult['IBLOCK']['ID']
            ])
            ->forSections($arSectionsId)
            ->forSites([SITE_ID])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        $arItem = null;

        /** Провайдер данных для условий */
        $oProvider = new ClosureDataProvider(function ($oCondition) use (&$arResult, &$arParams, &$arItem) {
            /** @var ClosureDataProvider $this */
            if ($oCondition instanceof IBlockSectionCondition) {
                if ($arParams['TYPE'] === 'section') {
                    return new DataProviderResult($arItem['ID']);
                } else {
                    return new DataProviderResult($arItem['IBLOCK_SECTION_ID']);
                }
            } else if ($oCondition instanceof IBlockPropertyCondition) {
                if ($arParams['TYPE'] !== 'element')
                    return new DataProviderResult(null);

                if ($oCondition->value === false)
                    return new DataProviderResult(null, false);

                $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [$oCondition->id]);

                if (!empty($arProperty)) {
                    $mValue = null;

                    if ($arProperty['PROPERTY_TYPE'] === 'N') {
                        if (Type::isNumeric($arProperty['VALUE']))
                            $mValue = Type::toFloat($arProperty['VALUE']);
                    } else {
                        if (!empty($arProperty['VALUE_ENUM_ID'])) {
                            $mValue = $arProperty['VALUE_ENUM_ID'];
                        } else {
                            $mValue = $arProperty['VALUE'];
                        }
                    }

                    if (empty($mValue) && !Type::isNumeric($mValue))
                        $mValue = null;

                    return new DataProviderResult($mValue);
                }

                return new DataProviderResult(null, false);
            }

            return new DataProviderResult(null);
        });

        /** Модификатор результата условий */
        $oModifier = new ClosureResultModifier(function ($oCondition, $oData, $bResult) use (&$arResult, &$arParams, &$arItem) {
            /** @var DataProviderResult $oData */

            if ($oCondition instanceof IBlockPropertyCondition) {
                /** Если не элемент, то условие должно выполниться */
                if ($arParams['TYPE'] !== 'element')
                    return true;

                $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [$oCondition->id]);

                if (!empty($arProperty)) {
                    /** Если значение условия null (т.е. принимает любое значение для свойства) */
                    if (!empty($oData)) {
                        if ($oCondition->value === null) {
                            if (
                                $oCondition->getOperator() === IBlockPropertyCondition::OPERATOR_EQUAL ||
                                $oCondition->getOperator() === IBlockPropertyCondition::OPERATOR_CONTAIN
                            ) {
                                /** Если любое значение для свойства фильтра установлено */
                                $bResult = $oData->getIsValid();
                            } else if (
                                $oCondition->getOperator() === IBlockPropertyCondition::OPERATOR_NOT_EQUAL ||
                                $oCondition->getOperator() === IBlockPropertyCondition::OPERATOR_NOT_CONTAIN
                            ) {
                                /** Если никакое значение для свойства фильтра не установлено */
                                $bResult = !$oData->getIsValid();
                            }
                        }
                    }
                } else {
                    /** Если свойства не существует */
                    $bResult = false;
                }
            }

            return $bResult;
        });

        $arItems = null;

        if ($arParams['TYPE'] === 'section') {
            $arItems = &$arResult['SECTIONS'];
        } else {
            $arItems = &$arResult['ELEMENTS'];
        }

        foreach ($arItems as &$arItem) {
            $arItem['META'] = [
                'title' => null,
                'keywords' => null,
                'description' => null,
                'pageTitle' => null,
                'picturePreviewAlt' => null,
                'picturePreviewTitle' => null,
                'pictureDetailAlt' => null,
                'pictureDetailTitle' => null
            ];

            foreach ($arTemplates as $oTemplate) {
                /**
                 * @var Template $oTemplate
                 */

                $oRules = $oTemplate->getRules();

                if ($oRules->getIsFulfilled($oProvider, $oModifier))
                    $arItem['TEMPLATE'] = $oTemplate;
            }

            if (!empty($arItem['TEMPLATE'])) {
                $oEntity = null;
                $oTemplate = $arItem['TEMPLATE'];

                if ($arParams['TYPE'] === 'section') {
                    $arItem['META']['title'] = $oTemplate->sectionMetaTitle;
                    $arItem['META']['keywords'] = $oTemplate->sectionMetaKeywords;
                    $arItem['META']['description'] = $oTemplate->sectionMetaDescription;
                    $arItem['META']['pageTitle'] = $oTemplate->sectionMetaPageTitle;
                    $arItem['META']['picturePreviewAlt'] = $oTemplate->sectionMetaPicturePreviewAlt;
                    $arItem['META']['picturePreviewTitle'] = $oTemplate->sectionMetaPicturePreviewTitle;
                    $arItem['META']['pictureDetailAlt'] = $oTemplate->sectionMetaPictureDetailAlt;
                    $arItem['META']['pictureDetailTitle'] = $oTemplate->sectionMetaPictureDetailTitle;

                    $oEntity = new TemplateSection($arItem['ID']);
                } else {
                    $arItem['META']['title'] = $oTemplate->elementMetaTitle;
                    $arItem['META']['keywords'] = $oTemplate->elementMetaKeywords;
                    $arItem['META']['description'] = $oTemplate->elementMetaDescription;
                    $arItem['META']['pageTitle'] = $oTemplate->elementMetaPageTitle;
                    $arItem['META']['picturePreviewAlt'] = $oTemplate->elementMetaPicturePreviewAlt;
                    $arItem['META']['picturePreviewTitle'] = $oTemplate->elementMetaPicturePreviewTitle;
                    $arItem['META']['pictureDetailAlt'] = $oTemplate->elementMetaPictureDetailAlt;
                    $arItem['META']['pictureDetailTitle'] = $oTemplate->elementMetaPictureDetailTitle;

                    $oEntity = new TemplateElement($arItem['ID']);
                }

                foreach ($arItem['META'] as $sKey => $mValue) {
                    if ($mValue !== null)
                        $mValue = TemplateEngine::process($oEntity, $mValue);

                    $arItem['META'][$sKey] = $mValue;
                }

                unset($oEntity);
            }
        }

        unset($arItem);

        if ($arParams['MODE'] === 'single') {
            $arItem = reset($arItems);
            $arResult['TEMPLATE'] = $arItem['TEMPLATE'];
            $arResult['META'] = $arItem['META'];
        }

        unset($arItems, $arItem, $arTemplates, $oTemplate);

        if ($arParams['METADATA_SET'] === 'Y') {
            if (!empty($arResult['META']['title']) || Type::isNumeric($arResult['META']['title']))
                $APPLICATION->SetPageProperty('title', $arResult['META']['title']);

            if (!empty($arResult['META']['keywords']) || Type::isNumeric($arResult['META']['keywords']))
                $APPLICATION->SetPageProperty('keywords', $arResult['META']['keywords']);

            if (!empty($arResult['META']['description']) || Type::isNumeric($arResult['META']['description']))
                $APPLICATION->SetPageProperty('description', $arResult['META']['description']);

            if (!empty($arResult['META']['pageTitle']) || Type::isNumeric($arResult['META']['pageTitle']))
                $APPLICATION->SetTitle($arResult['META']['pageTitle']);
        }

        $this->arResult = $arResult;

        unset($arResult);

        $this->includeComponentTemplate();

        return $this->arResult;
    }
}