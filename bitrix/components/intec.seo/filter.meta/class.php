<?php

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\Loader;
use Bitrix\Iblock\Template\Engine as TemplateEngine;
use Bitrix\Iblock\Template\Entity\Section as TemplateSection;
use Bitrix\Highloadblock\HighloadBlockTable;
use intec\Core;
use intec\core\base\condition\DataProviderResult;
use intec\core\base\condition\providers\ClosureDataProvider;
use intec\core\base\condition\modifiers\ClosureResultModifier;
use intec\core\base\conditions\GroupCondition;
use intec\core\bitrix\conditions\CatalogPriceCondition;
use intec\core\bitrix\conditions\IBlockPropertyCondition;
use intec\core\bitrix\conditions\IBlockSectionCondition;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;
use intec\core\helpers\StringHelper;
use intec\core\io\Path;
use intec\seo\filter\conditions\CatalogPriceFilteredMaximalCondition;
use intec\seo\filter\conditions\CatalogPriceFilteredMinimalCondition;
use intec\seo\filter\conditions\CatalogPriceMaximalCondition;
use intec\seo\filter\conditions\CatalogPriceMinimalCondition;
use intec\seo\filter\conditions\IBlockPropertyMaximalCondition;
use intec\seo\filter\conditions\IBlockPropertyMinimalCondition;
use intec\seo\filter\tags\OfferPropertyTag;
use intec\seo\filter\tags\PriceTag;
use intec\seo\filter\tags\PropertyTag;
use intec\seo\models\filter\Condition;
use intec\seo\models\filter\Url;
use intec\seo\models\SiteSettings;

class IntecSeoFilterMetaComponent extends CBitrixComponent
{
    /**
     * Текущие условия.
     * @var array
     */
    protected static $_conditions = [];
    /**
     * Текущие примененные условия.
     * @var array
     */
    protected static $_appliedConditions = [];

    /**
     * Возвращает текущие уловия.
     * @return array
     */
    public static function getConditions()
    {
        return static::$_conditions;
    }

    /**
     * Возвращает текущие примененные условия.
     * @return array
     */
    public static function getAppliedConditions()
    {
        return static::$_appliedConditions;
    }

    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams = ArrayHelper::merge([
            'IBLOCK_ID' => null,
            'SECTION_ID' => null,
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

        if ($arParams['CACHE_TIME'] < 0)
            $arParams['CACHE_TIME'] = 0;

        return $arParams;
    }

    /**
     * @inheritdoc
     */
    public function executeComponent()
    {
        static::$_conditions = [];
        static::$_appliedConditions = [];

        global $APPLICATION;

        if (
            !Loader::includeModule('iblock') ||
            !Loader::includeModule('intec.seo')
        ) return null;

        $arParams = $this->arParams;
        $oSettings = SiteSettings::getCurrent();

        if (empty($oSettings))
            return null;

        $this->arResult = [
            'IBLOCK' => null,
            'SECTION' => null
        ];

        if (empty($arParams['IBLOCK_ID']) || empty($arParams['SECTION_ID']))
            return null;

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

            $this->endResultCache();
        }

        $arResult = $this->arResult;
        $arResult['PROPERTIES'] = [];
        $arResult['CONDITIONS'] = [];
        $arResult['INDEXING'] = null;
        $arResult['CANONICAL'] = false;
        $arResult['META'] = [
            'title' => null,
            'keywords' => null,
            'description' => null,
            'pageTitle' => null,
            'breadcrumbName' => null,
            'breadcrumbLink' => null,
            'descriptionTop' => null,
            'descriptionBottom' => null,
            'descriptionAdditional' => null
        ];

        if (empty($arResult['SECTION']))
            return null;

        /** Подключение модуля каталога */
        $bCatalogUse = Loader::includeModule('catalog');

        $arSku = false;

        if ($bCatalogUse)
            $arSku = CCatalogSku::GetInfoByProductIBlock($arResult['IBLOCK']['ID']);

        $arFilter = $APPLICATION->IncludeComponent(
            'intec.seo:filter.loader',
            '.default',
            [],
            $this,
            ['HIDE_ICONS' => 'Y']
        );

        if (empty($arFilter))
            return null;

        if ($oSettings->filterIndexingDisabled)
            $arResult['INDEXING'] = false;

        $arPagination = [
            'PAGE' => Core::$app->request->get('PAGEN_1'),
            'PART' => null,
            'TEXT' => null
        ];

        if (Type::isNumeric($arPagination['PAGE'])) {
            $arPagination['PART'] = $oSettings->filterPaginationPart;
            $arPagination['TEXT'] = $oSettings->filterPaginationText;

            if (!empty($arPagination['PART'])) {
                $arPagination['PART'] = StringHelper::replace($arPagination['PART'], [
                    '%NUMBER%' => $arPagination['PAGE']
                ]);
            } else {
                $arPagination['PART'] = null;
            }

            if (!empty($arPagination['TEXT']) || Type::isNumeric($arPagination['TEXT'])) {
                $arPagination['TEXT'] = StringHelper::replace($arPagination['TEXT'], [
                    '%NUMBER%' => $arPagination['PAGE']
                ]);
            } else {
                $arPagination['TEXT'] = null;
            }
        } else {
            $arPagination['PAGE'] = null;
        }

        $arMacros = [
            'SEO_FILTER_PAGINATION_PAGE_NUMBER' => $arPagination['PAGE'],
            'SEO_FILTER_PAGINATION_TEXT' => $arPagination['TEXT']
        ];

        $oCache = Cache::createInstance();

        if ($oCache->startDataCache($arParams['CACHE_TIME'], $arResult['IBLOCK']['ID'])) {
            $arResult['PROPERTIES'] = Arrays::fromDBResult(CIBlockProperty::GetList([
                'SORT' => 'ASC'
            ], [
                'IBLOCK_ID' => $arResult['IBLOCK']['ID']
            ]));

            if ($arSku !== false)
                $arResult['PROPERTIES']->addRange(Arrays::fromDBResult(CIBlockProperty::GetList([
                    'SORT' => 'ASC'
                ], [
                    'IBLOCK_ID' => $arSku['IBLOCK_ID']
                ])));

            $arResult['PROPERTIES'] = $arResult['PROPERTIES']->each(function ($iIndex, &$arProperty) {
                if ($arProperty['PROPERTY_TYPE'] === 'L' && empty($arProperty['USER_TYPE']) && !Type::isNumeric($arProperty['USER_TYPE'])) {
                    $arProperty['VALUES'] = Arrays::fromDBResult(CIBlockPropertyEnum::GetList([
                        'SORT' => 'ASC'
                    ], [
                        'PROPERTY_ID' => $arProperty['ID']
                    ]))->indexBy('ID')->asArray();
                } else if ($arProperty['PROPERTY_TYPE'] === 'S' && $arProperty['USER_TYPE'] === 'directory') {
                    $arProperty['VALUES'] = [];

                    try {
                        $oEntity = null;
                        $oBlock = HighloadBlockTable::getList([
                            'filter' => [
                                'TABLE_NAME' => $arProperty['USER_TYPE_SETTINGS']['TABLE_NAME']
                            ]
                        ])->fetch();

                        if (!empty($oBlock))
                            $oEntity = HighloadBlockTable::compileEntity($oBlock);

                        if (!empty($oEntity)) {
                            $oQuery = new Query($oEntity);
                            $oQuery->setSelect(['*']);
                            $oResult = $oQuery->exec();

                            while ($arValue = $oResult->fetch())
                                if (!empty($arValue['UF_XML_ID']))
                                    $arProperty['VALUES'][$arValue['UF_XML_ID']] = $arValue;
                        }
                    } catch (\Exception $oException) {}
                }
            })->indexBy('ID')->asArray();

            $oCache->endDataCache([
                'PROPERTIES' => $arResult['PROPERTIES']
            ]);
        } else {
            $arVariables = $oCache->getVars();
            $arResult['PROPERTIES'] = $arVariables['PROPERTIES'];

            unset($arVariables);
        }

        $arSections = [];
        $arElements = [];

        $oUrl = Url::getCurrent();
        $arConditions = Condition::find()
            ->where([
                'active' => 1,
                'iBlockId' => $arResult['IBLOCK']['ID']
            ])
            ->forSections([null, $arResult['SECTION']['ID']])
            ->forSites([SITE_ID])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        $arStrict = [];
        $arConditionStrict = [];

        /** Составление карты строгости */
        foreach ($arFilter['ITEMS'] as $mId => $arProperty) {
            if (isset($arProperty['PRICE']) && $arProperty['PRICE'])
                $mId = 'P_'.$arProperty['ID'];

            if (!Type::isArray($arStrict[$mId]))
                $arStrict[$mId] = [];

            foreach ($arProperty['VALUES'] as $sKey => $arValue) {
                if (isset($arProperty['PRICE']) && $arProperty['PRICE']) {
                    $arStrict[$mId][$sKey] = isset($arValue['HTML_VALUE']);
                } else if (isset($arProperty['DISPLAY_TYPE'])) {
                    if (
                        $arProperty['DISPLAY_TYPE'] === 'A' ||
                        $arProperty['DISPLAY_TYPE'] === 'B'
                    ) {
                        $arStrict[$mId][$sKey] = isset($arValue['HTML_VALUE']);
                    } else if (
                        $arProperty['DISPLAY_TYPE'] !== 'U'
                    ) {
                        $arStrict[$mId][$arValue['VALUE']] = isset($arValue['CHECKED']) && $arValue['CHECKED'];
                    }
                }
            }
        }

        /** Преобразователь условий, подставляет текстовые значения списков, справочников и привязок заместо идентификаторов */
        $fConverter = function ($oCondition) use (&$fConverter, &$arResult, &$arSections, &$arElements) {
            if ($oCondition instanceof GroupCondition) {
                $oConditions = $oCondition->getConditions();

                foreach ($oConditions as $oChild)
                    $fConverter($oChild);
            } else if ($oCondition instanceof IBlockPropertyCondition) {
                if ($oCondition->value !== null) {
                    $arProperty = ArrayHelper::getValue($arResult['PROPERTIES'], $oCondition->id);
                    
                    if (!empty($arProperty)) {
                        if ($arProperty['PROPERTY_TYPE'] === 'L' && empty($arProperty['USER_TYPE']) && !Type::isNumeric($arProperty['USER_TYPE'])) {
                            $arValue = ArrayHelper::getValue($arProperty['VALUES'], $oCondition->value);

                            if (!empty($arValue)) {
                                $oCondition->value = $arValue['VALUE'];
                            } else {
                                $oCondition->value = false;
                            }
                        } else if ($arProperty['PROPERTY_TYPE'] === 'S' && $arProperty['USER_TYPE'] === 'directory') {
                            $arValue = ArrayHelper::getValue($arProperty['VALUES'], $oCondition->value);

                            if (!empty($arValue)) {
                                $oCondition->value = !empty($arValue['UF_NAME']) || Type::isInteger($arValue['UF_NAME']) ? $arValue['UF_NAME'] : $arValue['UF_XML_ID'];
                            } else {
                                $oCondition->value = false;
                            }
                        } else if ($arProperty['PROPERTY_TYPE'] === 'G') {
                            if (!ArrayHelper::keyExists($oCondition->value, $arSections))
                                $arSections[$oCondition->value] = CIBlockSection::GetList([], [
                                    'ID' => $oCondition->value
                                ], false, [], [
                                    'nPageSize' => 1
                                ])->Fetch();

                            $arValue = $arSections[$oCondition->value];

                            if (!empty($arValue)) {
                                $oCondition->value = $arValue['NAME'];
                            } else {
                                $oCondition->value = false;
                            }
                        } else if ($arProperty['PROPERTY_TYPE'] === 'E') {
                            if (!ArrayHelper::keyExists($oCondition->value, $arElements))
                                $arElements[$oCondition->value] = CIBlockElement::GetList([], [
                                    'ID' => $oCondition->value
                                ], false, [
                                    'nPageSize' => 1
                                ])->Fetch();

                            $arValue = $arElements[$oCondition->value];

                            if (!empty($arValue)) {
                                $oCondition->value = $arValue['NAME'];
                            } else {
                                $oCondition->value = false;
                            }
                        }
                    }
                }
            }
        };

        /** Провайдер данных для условий */
        $oProvider = new ClosureDataProvider(function ($oCondition) use (&$arResult, &$arFilter) {
            /** @var ClosureDataProvider $this */
            if ($oCondition instanceof IBlockSectionCondition) {
                return new DataProviderResult($arResult['SECTION']['ID']);
            } else if ($oCondition instanceof IBlockPropertyCondition) {
                if ($oCondition->value === false)
                    return new DataProviderResult(null, false);

                $arProperty = ArrayHelper::getValue($arFilter, ['ITEMS', $oCondition->id]);

                if (!empty($arProperty) && isset($arProperty['DISPLAY_TYPE'])) {
                    if (
                        $arProperty['DISPLAY_TYPE'] === 'A' ||
                        $arProperty['DISPLAY_TYPE'] === 'B'
                    ) {
                        /** Если тип свойства числа */
                        /** Получаем минимальное установленное значение */
                        $fMinimum = ArrayHelper::getValue($arProperty, ['VALUES', 'MIN', 'HTML_VALUE']);
                        /** Получаем максимальное установленное значение */
                        $fMaximum = ArrayHelper::getValue($arProperty, ['VALUES', 'MAX', 'HTML_VALUE']);

                        /** Если минимальное значение установлено, преобразуем его в float */
                        if ($fMinimum !== null)
                            $fMinimum = Type::toFloat($fMinimum);

                        /** Если максимальное значение установлено, преобразуем его в float */
                        if ($fMaximum !== null)
                            $fMaximum = Type::toFloat($fMaximum);

                        /** Если минимальное либо максимальное значения установлены */
                        if ($fMinimum !== null || $fMaximum !== null) {
                            if ($oCondition instanceof IBlockPropertyMinimalCondition) {
                                if ($fMinimum !== null)
                                    return new DataProviderResult($fMinimum);
                            } else if ($oCondition instanceof IBlockPropertyMaximalCondition) {
                                if ($fMaximum !== null)
                                    return new DataProviderResult($fMaximum);
                            } else {
                                if (
                                    $oCondition->getOperator() === IBlockPropertyCondition::OPERATOR_LESS ||
                                    $oCondition->getOperator() === IBlockPropertyCondition::OPERATOR_LESS_OR_EQUAL
                                ) {
                                    /** Если сравнение "меньше" или "меньше или равно", то начинаем с максимальной границы */
                                    if ($fMaximum !== null)
                                        return new DataProviderResult($fMaximum);

                                    if ($fMinimum !== null)
                                        return new DataProviderResult($fMinimum);
                                } else if (
                                    $oCondition->getOperator() === IBlockPropertyCondition::OPERATOR_MORE ||
                                    $oCondition->getOperator() === IBlockPropertyCondition::OPERATOR_MORE_OR_EQUAL
                                ) {
                                    /** Если сравнение "больше" или "больше или равно", то начинаем с минимальной границы */
                                    if ($fMinimum !== null)
                                        return new DataProviderResult($fMinimum);

                                    if ($fMaximum !== null)
                                        return new DataProviderResult($fMaximum);
                                }
                            }
                        }
                    } else if (
                        $arProperty['DISPLAY_TYPE'] !== 'U'
                    ) {
                        /** Если тип свойства любой, кроме календаря и чисел */
                        $arValues = [];

                        /** Собираем установленые значения фильтра */
                        if (isset($arProperty['VALUES']))
                            foreach ($arProperty['VALUES'] as $arValue) {
                                if (isset($arValue['CHECKED']) && $arValue['CHECKED'])
                                    $arValues[] = $arValue['VALUE'];
                            }

                        /** Если хоть одно значение установлено, отправляем результат */
                        if (!empty($arValues))
                            return new DataProviderResult($arValues);
                    }
                }

                return new DataProviderResult(null, false);
            } else if ($oCondition instanceof CatalogPriceCondition) {
                $arPrice = null;

                foreach ($arFilter['ITEMS'] as $arPrice) {
                    if (isset($arPrice['PRICE']) && $arPrice['PRICE'] && $arPrice['ID'] == $oCondition->id)
                        break;

                    $arPrice = null;
                }

                if (!empty($arPrice)) {
                    $fValue = null;

                    if ($oCondition instanceof CatalogPriceMinimalCondition) {
                        $fValue = ArrayHelper::getValue($arPrice, ['VALUES', 'MIN', 'VALUE']);
                    } else if ($oCondition instanceof CatalogPriceMaximalCondition) {
                        $fValue = ArrayHelper::getValue($arPrice, ['VALUES', 'MAX', 'VALUE']);
                    } else if ($oCondition instanceof CatalogPriceFilteredMinimalCondition) {
                        $fValue = ArrayHelper::getValue($arPrice, ['VALUES', 'MIN', 'HTML_VALUE']);
                    } else if ($oCondition instanceof CatalogPriceFilteredMaximalCondition) {
                        $fValue = ArrayHelper::getValue($arPrice, ['VALUES', 'MAX', 'HTML_VALUE']);
                    }

                    if ($fValue !== null) {
                        $fValue = Type::toFloat($fValue);

                        return new DataProviderResult($fValue);
                    }
                }

                return new DataProviderResult(null, false);
            }

            return new DataProviderResult(null);
        });

        /** Модификатор результата условий */
        $oModifier = new ClosureResultModifier(function ($oCondition, $oData, $bResult) use (&$arFilter, &$arConditionStrict) {
            /** @var DataProviderResult $oData */

            if ($oCondition instanceof IBlockPropertyCondition) {
                $arProperty = ArrayHelper::getValue($arFilter, ['ITEMS', $oCondition->id]);

                if (!empty($arProperty) && isset($arProperty['DISPLAY_TYPE'])) {
                    /** Если значение условия null (т.е. принимает любое значение для свойства) */
                    if (!empty($oData)) {
                        if ($oCondition->value !== null) {
                            /** Устанавливаем строгость для условий с установленным значением */
                            if (isset($arConditionStrict[$oCondition->id]) && $oData->getIsValid()) {
                                if (
                                    $arProperty['DISPLAY_TYPE'] === 'A' ||
                                    $arProperty['DISPLAY_TYPE'] === 'B'
                                ) {
                                    /** Убираем строгость в зависимости от типа условия */
                                    if ($oCondition instanceof IBlockPropertyMinimalCondition) {
                                        $arConditionStrict[$oCondition->id]['MIN'] = false;
                                    } else if ($oCondition instanceof IBlockPropertyMaximalCondition) {
                                        $arConditionStrict[$oCondition->id]['MAX'] = false;
                                    } else {
                                        $arConditionStrict[$oCondition->id]['MIN'] = false;
                                        $arConditionStrict[$oCondition->id]['MAX'] = false;
                                    }
                                } else if (
                                    $arProperty['DISPLAY_TYPE'] !== 'U'
                                ) {
                                    if ($oCondition->operator === IBlockPropertyCondition::OPERATOR_EQUAL) {
                                        /** Если оператор "равно", то убираем строгость с установленного значения свойства */
                                        $arConditionStrict[$oCondition->id][$oCondition->value] = false;
                                    } else if ($oCondition->operator === IBlockPropertyCondition::OPERATOR_NOT_EQUAL) {
                                        /** Если оператор "не равно", то убираем строгость со всех значений свойства */
                                        foreach ($arConditionStrict[$oCondition->id] as $sValue => $bValue) {
                                            if ($oCondition->value != $sValue)
                                                $arConditionStrict[$oCondition->id][$sValue] = false;
                                        }
                                    } else if ($oCondition->operator === IBlockPropertyCondition::OPERATOR_CONTAIN) {
                                        /** Если оператор "содержит", то убираем строгость со всех значений свойства, которые содержат значение */
                                        foreach ($arConditionStrict[$oCondition->id] as $sValue => $bValue) {
                                            if (StringHelper::position($oCondition->value, $sValue, 0, true, false, Encoding::getDefault()) !== false)
                                                $arConditionStrict[$oCondition->id][$sValue] = false;
                                        }
                                    } else if ($oCondition->operator === IBlockPropertyCondition::OPERATOR_NOT_CONTAIN) {
                                        /** Если оператор "не содержит", то убираем строгость со всех значений свойства, которые не содержат значение */
                                        foreach ($arConditionStrict[$oCondition->id] as $sValue => $bValue) {
                                            if (StringHelper::position($oCondition->value, $sValue, 0, true, false, Encoding::getDefault()) === false)
                                                $arConditionStrict[$oCondition->id][$sValue] = false;
                                        }
                                    }
                                }
                            }
                        } else {
                            $bResult = false;

                            if (
                                $arProperty['DISPLAY_TYPE'] === 'A' ||
                                $arProperty['DISPLAY_TYPE'] === 'B'
                            ) {
                                if ($oData->getIsValid()) {
                                    /** Получаем минимальное установленное значение */
                                    $fMinimum = ArrayHelper::getValue($arProperty, ['VALUES', 'MIN', 'HTML_VALUE']);
                                    /** Получаем максимальное установленное значение */
                                    $fMaximum = ArrayHelper::getValue($arProperty, ['VALUES', 'MAX', 'HTML_VALUE']);

                                    /** Если минимальное значение установлено, преобразуем его в float */
                                    if ($fMinimum !== null)
                                        $fMinimum = Type::toFloat($fMinimum);

                                    /** Если максимальное значение установлено, преобразуем его в float */
                                    if ($fMaximum !== null)
                                        $fMaximum = Type::toFloat($fMaximum);

                                    if ($oCondition instanceof IBlockPropertyMinimalCondition) {
                                        /** Если тип условия по минимальному значению числового фильтра */
                                        $bResult = $fMinimum !== null;

                                        if (isset($arConditionStrict[$oCondition->id]))
                                            $arConditionStrict[$oCondition->id]['MIN'] = false;
                                    } else if ($oCondition instanceof IBlockPropertyMaximalCondition) {
                                        /** Если тип условия по максимальному значению числового фильтра */
                                        $bResult = $fMaximum !== null;

                                        if (isset($arConditionStrict[$oCondition->id]))
                                            $arConditionStrict[$oCondition->id]['MAX'] = false;
                                    } else {
                                        if (
                                            $oCondition->getOperator() === IBlockPropertyCondition::OPERATOR_LESS ||
                                            $oCondition->getOperator() === IBlockPropertyCondition::OPERATOR_LESS_OR_EQUAL
                                        ) {
                                            /** Если сравнение "меньше" или "меньше или равно", то проверяем, установлена ли максимальная граница */
                                            $bResult = $fMaximum !== null;
                                        } else if (
                                            $oCondition->getOperator() === IBlockPropertyCondition::OPERATOR_MORE ||
                                            $oCondition->getOperator() === IBlockPropertyCondition::OPERATOR_MORE_OR_EQUAL
                                        ) {
                                            /** Если сравнение "больше" или "больше или равно", то проверяем, установлена ли минимальная граница */
                                            $bResult = $fMinimum !== null;
                                        }

                                        if (isset($arConditionStrict[$oCondition->id])) {
                                            /** Если хоть одно значение установлено, убираем строгость */
                                            $arConditionStrict[$oCondition->id]['MIN'] = false;
                                            $arConditionStrict[$oCondition->id]['MAX'] = false;
                                        }
                                    }
                                }
                            } else if (
                                $arProperty['DISPLAY_TYPE'] !== 'U'
                            ) {
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

                                /** Убираем строгость со всех значений свойства */
                                foreach ($arConditionStrict[$oCondition->id] as $sValue => $bValue)
                                    $arConditionStrict[$oCondition->id][$sValue] = false;
                            }
                        }
                    }
                } else {
                    /** Если свойства не существует */
                    $bResult = false;
                }
            } else if ($oCondition instanceof CatalogPriceCondition) {
                $arPrice = null;

                foreach ($arFilter['ITEMS'] as $arPrice) {
                    if (isset($arPrice['PRICE']) && $arPrice['PRICE'] && $arPrice['ID'] == $oCondition->id)
                        break;

                    $arPrice = null;
                }

                if (!empty($oData)) {
                    /** Убираем строгость для цены */
                    if (isset($arConditionStrict['P_'.$oCondition->id]) && $oData->getIsValid()) {
                        if (
                            $oCondition instanceof CatalogPriceMinimalCondition ||
                            $oCondition instanceof CatalogPriceFilteredMinimalCondition
                        ) {
                            $arConditionStrict['P_'.$oCondition->id]['MIN'] = false;
                        } else if (
                            $oCondition instanceof CatalogPriceMaximalCondition ||
                            $oCondition instanceof CatalogPriceFilteredMaximalCondition
                        ) {
                            $arConditionStrict['P_'.$oCondition->id]['MAX'] = false;
                        }
                    }

                    /** Если значение условия null (т.е. принимает любое значение для цены) */
                    if ($oCondition->value === null) {
                        $bResult = false;

                        if ($oCondition->getOperator() === CatalogPriceCondition::OPERATOR_EQUAL) {
                            /** Если граница цены равна любому значению */
                            $bResult = $oData->getIsValid();
                        } else if ($oCondition->getOperator() === CatalogPriceCondition::OPERATOR_NOT_EQUAL) {
                            /** Если граница цены равна не одному значению */
                            $bResult = !$oData->getIsValid();
                        }
                    }
                }
            }

            return $bResult;
        });

        /** @var Condition $oCondition */
        foreach ($arConditions as $oCondition) {
            $bFulfilled = false;

            if (!empty($oUrl) && $oUrl->conditionId === $oCondition->id) {
                $bFulfilled = true;
            } else {
                $arConditionStrict = $arStrict;
                $oRules = $oCondition->getRules();
                $fConverter($oRules);
                $bFulfilled = $oRules->getIsFulfilled($oProvider, $oModifier);

                /** Если условие выполнено и является строгим, проверяем карту строгости */
                if ($bFulfilled && $oCondition->strict) {
                    /** Идем по свойствам карты */
                    foreach ($arConditionStrict as $mId => $arValues) {
                        /** Идем по значениям свойства карты */
                        foreach ($arValues as $sKey => $bValue)
                            /** Если значнеие не использовалось, значит строгость некорректна и условие не выполнено */
                            if ($bValue) {
                                $bFulfilled = false;
                                break;
                            }

                        if (!$bFulfilled)
                            break;
                    }
                }
            }

            if ($bFulfilled)
                $arResult['CONDITIONS'][] = $oCondition;
        }

        unset($bFulfilled);

        /**
         * @var Condition $oCondition
         */
        foreach ($arResult['CONDITIONS'] as $oCondition) {
            $bApply = false;
            $arResult['INDEXING'] = Type::toBoolean($oCondition->indexing);

            if (!empty($oCondition->metaTitle) || Type::isNumeric($oCondition->metaTitle)) {
                $arResult['META']['title'] = $oCondition->metaTitle;
                $bApply = true;
            }

            if (!empty($oCondition->metaKeywords) || Type::isNumeric($oCondition->metaKeywords)) {
                $arResult['META']['keywords'] = $oCondition->metaKeywords;
                $bApply = true;
            }

            if (!empty($oCondition->metaDescription) || Type::isNumeric($oCondition->metaDescription)) {
                $arResult['META']['description'] = $oCondition->metaDescription;
                $bApply = true;
            }

            if (!empty($oCondition->metaPageTitle) || Type::isNumeric($oCondition->metaPageTitle)) {
                $arResult['META']['pageTitle'] = $oCondition->metaPageTitle;
                $bApply = true;
            }

            if (!empty($oCondition->metaBreadcrumbName) || Type::isNumeric($oCondition->metaBreadcrumbName)) {
                $arResult['META']['breadcrumbName'] = $oCondition->metaBreadcrumbName;
                $bApply = true;
            }

            if (!empty($oCondition->metaDescriptionTop) || Type::isNumeric($oCondition->metaDescriptionTop)) {
                $arResult['META']['descriptionTop'] = $oCondition->metaDescriptionTop;
                $bApply = true;
            }

            if (!empty($oCondition->metaDescriptionBottom) || Type::isNumeric($oCondition->metaDescriptionBottom)) {
                $arResult['META']['descriptionBottom'] = $oCondition->metaDescriptionBottom;
                $bApply = true;
            }

            if (!empty($oCondition->metaDescriptionAdditional) || Type::isNumeric($oCondition->metaDescriptionAdditional)) {
                $arResult['META']['descriptionAdditional'] = $oCondition->metaDescriptionAdditional;
                $bApply = true;
            }

            if ($bApply) {
                $arResult['CANONICAL'] = true;
                static::$_appliedConditions[$oCondition->id] = $oCondition;
            }

            static::$_conditions[$oCondition->id] = $oCondition;
        }

        $oEntity = new TemplateSection($arResult['SECTION']['ID']);

        if ($arResult['INDEXING'] === true) {
            $APPLICATION->SetPageProperty('robots', 'index, follow');
        } else if ($arResult['INDEXING'] === false) {
            $APPLICATION->SetPageProperty('robots', 'noindex, nofollow');
        }

        /** Устанавливаем обработчик для замены свойств мета-информации */
        PropertyTag::setHandler(function ($arParameters) use (&$arResult, &$arFilter) {
            $arValues = [];

            foreach ($arFilter['ITEMS'] as $arItem) {
                $sCode = $arItem['CODE'];

                if (empty($sCode) && !Type::isNumeric($sCode))
                    $sCode = $arItem['ID'];

                if (!ArrayHelper::isIn($sCode, $arParameters))
                    continue;

                if (!isset($arItem['IBLOCK_ID']) || $arItem['IBLOCK_ID'] != $arResult['IBLOCK']['ID'])
                    continue;

                foreach ($arItem['VALUES'] as $arValue) {
                    if (!isset($arValue['CHECKED']) || !$arValue['CHECKED'])
                        continue;

                    $arValues[] = $arValue['VALUE'];
                }
            }

            return $arValues;
        });

        /** Устанавливаем обработчик для замены свойств торговых предложений мета-информации */
        OfferPropertyTag::setHandler(function ($arParameters) use (&$arFilter, &$arSku) {
            $arValues = [];

            if ($arSku === false)
                return $arValues;

            foreach ($arFilter['ITEMS'] as $arItem) {
                $sCode = $arItem['CODE'];

                if (empty($sCode) && !Type::isNumeric($sCode))
                    $sCode = $arItem['ID'];

                if (!ArrayHelper::isIn($sCode, $arParameters))
                    continue;

                if (!isset($arItem['IBLOCK_ID']) || $arItem['IBLOCK_ID'] != $arSku['IBLOCK_ID'])
                    continue;

                foreach ($arItem['VALUES'] as $arValue) {
                    if (!isset($arValue['CHECKED']) || !$arValue['CHECKED'])
                        continue;

                    $arValues[] = $arValue['VALUE'];
                }
            }

            return $arValues;
        });

        /** Устанавливаем обработчик для замены цен мета-информации */
        PriceTag::setHandler(function ($sType, $sValueType) use (&$arFilter) {
            $sValue = '';
            $arItem = ArrayHelper::getValue($arFilter, ['ITEMS', $sType]);

            if (!empty($arItem)) {
                if ($sValueType === 'default.minimal') {
                    $sValue = ArrayHelper::getValue($arItem, ['VALUES', 'MIN', 'VALUE']);
                } else if ($sValueType === 'default.maximal') {
                    $sValue = ArrayHelper::getValue($arItem, ['VALUES', 'MAX', 'VALUE']);
                } else if ($sValueType === 'filtered.minimal') {
                    $sValue = ArrayHelper::getValue($arItem, ['VALUES', 'MIN', 'HTML_VALUE']);

                    if (empty($sValue) && !Type::isNumeric($sValue))
                        $sValue = ArrayHelper::getValue($arItem, ['VALUES', 'MIN', 'VALUE']);
                } else if ($sValueType === 'filtered.maximal') {
                    $sValue = ArrayHelper::getValue($arItem, ['VALUES', 'MAX', 'HTML_VALUE']);

                    if (empty($sValue) && !Type::isNumeric($sValue))
                        $sValue = ArrayHelper::getValue($arItem, ['VALUES', 'MAX', 'VALUE']);
                }
            }

            return Type::toString($sValue);
        });

        foreach ($arResult['META'] as $sKey => $mValue) {
            if ($mValue !== null) {
                $mValue = StringHelper::replaceMacros($mValue, $arMacros);
                $mValue = TemplateEngine::process($oEntity, $mValue);
            }

            $arResult['META'][$sKey] = $mValue;
        }

        /** Убираем обработчик для замены свойств мета-информации */
        PropertyTag::setHandler(null);
        /** Убираем обработчик для замены свойств торговых предложений мета-информации */
        OfferPropertyTag::setHandler(null);
        /** Убираем обработчик для замены цен мета-информации */
        PriceTag::setHandler(null);

        if ($arResult['CANONICAL'])
            $arResult['CANONICAL'] = $oSettings->filterCanonicalUse;

        if ($arResult['CANONICAL']) {
            $oCanonicalUrl = null;

            if (!empty($oUrl)) {
                $oCanonicalUrl = new \intec\core\net\Url($oUrl->target);
                $oCanonicalUrl->getQuery()->removeAll();

                if ($arPagination['PART'])
                    $oCanonicalUrl->setPathString($oCanonicalUrl->getPathString().$arPagination['PART']);
            } else {
                $oCanonicalUrl = new \intec\core\net\Url($APPLICATION->GetCurPage(false));
            }

            $oCanonicalUrl->setScheme(Core::$app->request->getIsSecureConnection() ? 'https' : 'http');
            $oCanonicalUrl->setHost(Core::$app->request->getHostName());

            $arResult['CANONICAL'] = $oCanonicalUrl->build();

            unset($oCanonicalUrl);
        } else {
            $arResult['CANONICAL'] = null;
        }

        if (!empty($arResult['META']['title']) || Type::isNumeric($arResult['META']['title']))
            $APPLICATION->SetPageProperty('title', $arResult['META']['title']);

        if (!empty($arResult['META']['keywords']) || Type::isNumeric($arResult['META']['keywords']))
            $APPLICATION->SetPageProperty('keywords', $arResult['META']['keywords']);

        if (!empty($arResult['META']['description']) || Type::isNumeric($arResult['META']['description']))
            $APPLICATION->SetPageProperty('description', $arResult['META']['description']);

        if (!empty($arResult['META']['pageTitle']) || Type::isNumeric($arResult['META']['pageTitle']))
            $APPLICATION->SetTitle($arResult['META']['pageTitle']);

        if (!empty($arResult['META']['breadcrumbName']) || Type::isNumeric($arResult['META']['breadcrumbName']))
            $arResult['META']['breadcrumbLink'] = Core::$app->request->getUrl();

        if (!empty($arResult['CANONICAL']))
            $APPLICATION->SetPageProperty('canonical', $arResult['CANONICAL']);

        $this->arResult = $arResult;

        unset($arResult);

        if ($oSettings->filterVisitsEnabled)
            if (!empty(static::$_appliedConditions))
                $APPLICATION->AddHeadString('<script type="text/javascript">
    window.addEventListener(\'load\', function () {
        var request = new XMLHttpRequest();
    
        request.open(\'POST\', \'/bitrix/components/intec.seo/filter.meta/statistics.php\');
        request.setRequestHeader(\'Content-Type\', \'application/x-www-form-urlencoded\');
        request.send(\'site=\' + encodeURIComponent('.JavaScript::toObject(SITE_ID).') + \'&referrer=\' + encodeURIComponent(document.referrer) + \'&page=\' + encodeURIComponent(window.location.href));
    });
</script>');

        $this->includeComponentTemplate();

        return $this->arResult;
    }
}