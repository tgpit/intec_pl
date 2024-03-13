<?php
require(dirname(__FILE__).'/../../../../lib/PHPExcel/PHPExcel.php');


use Bitrix\Main\Loader;
use Bitrix\Main\Text\Encoding;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\StringHelper;
use intec\core\helpers\ArrayHelper;
use intec\importexport\models\excel\import\Template;
use intec\core\helpers\Json;
use intec\core\base\InvalidParamException;
use intec\importexport\models\excel\IBlockHelper;
use intec\importexport\models\excel\IBlockSelections;
use intec\importexport\models\excel\ConditionHelper;
use intec\importexport\models\excel\import\Properties;
use intec\importexport\models\excel\import\ImportHelper;
use intec\importexport\models\excel\Price;
use intec\core\collections\Arrays;


class Import
{
    /**
     * Содержит статистику.
     * @var array
     */
    private $statistic = [
        'added' => 0,
        'updated' => 0,
        'deleted' => 0,
        'sectionCreate' => 0,
        'sectionUpdated' => 0,
        'sectionDeleted' => 0,
        'totalOffers' => 0,
        'offersAdded' => 0,
        'offersUpdated' => 0,
        'offersDeleted' => 0,
        'errors' => 0,
        'totalRows' => 0,
        'startTime' => 0,
        'leadTime' => 0,
        'finished' => false,
        'offersIds' => [],
        'elementIds' => [],
        'sectionIds' => [
            'created' => [],
            'updated' => [],
        ]
    ];

    private $userId;

    /**
     * номер текущей строки
     * @var string|number
     */
    private $currentNumberRow;

    /**
     * Содержит основные настройки таблицы(цвет, шрифт, описание и тд.).
     * @var array
     */
    private $settingsData = [

    ];

    /**
     * Содержит настройки строк.
     * Служит для определения какие строки добовлять а какие нет.
     * @var array
     */
    private $rowSettingsData = [];

    /**
     * Cодержит данные для выборки из инфоблока(IBLOCK_ID, количество и тд.).
     * @var array
     */
    private $baseData = [];

    /**
     * Содержит основные данные(Первая строка таблицы, какие свойства выводить, в каком порядке, оформление и тд).
     * @var array
     */
    private $tableData = [];

    /**
     * Содержит настройки полей инфоблока.
     * @var array
     */
    private $iblockFields = [];

    private $fileData = [];

    private $objPhpExcel;


    /**
     * Массив ‘errors’ должен содержать массив вида
     * 'message' => Loc::getMessage(),
     * 'systemMessage' => $e->getMessage(), - если есть
     * 'exception' => $e - если есть
     * @var array
     */
    private $error = [
        'is' => false,
        'critical' => false,
        'errors' => []
    ];

    public function __construct($baseData = [], $tableData = [], $settings = [], $rowSettings = [])
    {
        global $USER;
        $this->userId = $USER->GetID();

        if (empty($baseData) || empty($tableData))
            return;

        $this->setBaseData($baseData);
        $this->setTableData($tableData);
        $this->setSettings($settings);
        $this->setRowSettings($settings);
    }

    public function init() {

    }

    public function getStatistic ()
    {
        return $this->statistic;
    }

    private function setSettings ($data)
    {
        if (!empty($data))
            $this->settingsData = $data;
    }

    public function getSettings ()
    {
        return $this->settingsData;
    }

    private function setRowSettings ($data)
    {
        if (!empty($data))
            $this->rowSettingsData = $data;
    }

    public function getRowSettings ()
    {
        return $this->rowSettingsData;
    }

    /**
     * Массив ‘errors’ должен содержать массив вида
     * 'message' => Loc::getMessage(),
     * 'systemMessage' => $e->getMessage(), - если есть
     * 'exception' => $e - если есть
     * @var array
     */
    private function setError ($data, $critical = false)
    {
        if (!empty($data)) {
            $this->error['is'] = true;
            $data = ArrayHelper::merge([
                'message' => null,
                'systemMessage' => null,
                'exception' => null
            ], $data);

            if ($critical)
                $this->error['critical'] = true;

            if (!empty($this->currentNumberRow))
                $this->error['errors'][$this->currentNumberRow] = $data;
            else
                $this->error['errors'][] = $data;

            $this->statistic['errors']++;
        }
    }

    private function unsetErrors ($index = null)
    {
        if (empty($index))
            unset($this->error['errors']);
        else
            unset($this->error['errors'][$index]);
    }

    public function getErrors ()
    {
        return $this->error;
    }

    private function setBaseData ($data)
    {
        if (!empty($data))
            $this->baseData = $data;
    }

    public function getBaseData ()
    {
        return $this->baseData;
    }

    private function setTableData ($data)
    {
        if (!empty($data))
            $this->tableData = $data;
    }

    public function getTableData ()
    {
        return $this->tableData;
    }

    public function addElements ($data)
    {
        if (empty($this->baseData) || empty($this->tableData))
            return null;

        $settings = $this->settingsData['settings'];
        $hasSettings = false;
        if (!empty($settings)) {
            foreach ($settings as $setting) {
                if (!empty($setting)) {
                    $hasSettings = true;
                    break;
                }
            }
        }

        $newData = [];

        foreach ($data as $dataKey => $dataItem) {
            if ($hasSettings) {
                $subRes = null;
                foreach ($settings as $settingKey => $setting) {
                    $subRes[$settingKey] = ConditionHelper::getComputedImportValue($dataItem, $settingKey, $setting, $this->baseData['delimiter']);
                }

                $dataItem = $subRes;
            }

            foreach ($dataItem as $key => $item) {
                foreach ($this->tableData['data'][$key] as $dataGroup) {
                    if (empty($dataGroup) || $dataGroup == 'false')
                        continue;

                    $newData[$dataKey][$dataGroup] = $item;
                }
            }
        }

        $data = $newData;
        unset($newData, $dataKey, $dataItem, $key, $item);

        foreach ($data as $key => $item) {
            $this->currentNumberRow = $key + 1;
            $this->addElement($item);
            $this->statistic['totalRows']++;
        }
    }

    public function addElement ($data)
    {
        if (empty($data))
            return null;

        $createElements = $this->baseData['dontCreateNewElements'] ? false : true;
        $updateElements = $this->baseData['dontUpdateExistElements'] ? false : true;
        $createOffers = $this->baseData['dontCreateNewOffers'] ? false : true;
        $deactivateNew = $this->baseData['newDeactivate'] ? true : false;
        $deactivateZeroQuantity = $this->baseData['zeroQuantityDeactivate'] ? true : false;
        $deactivateZeroPrice = $this->baseData['zeroPriceDeactivate'] ? true : false;
        $activate = $this->baseData['activate'] ? true : false;

        $fields = [
            'MODIFIED_BY' => $this->userId,
            'IBLOCK_ID' => $this->baseData['iblock']
        ];

        $filter = [];
        $fieldsProperties = [];
        $fieldsSeo = [];
        $hasOffer = false;
        $offersParentId = null;
        $fieldsOffers = null;
        $hasName = false;

        foreach ($data as $key => $value) {
            if (!empty($key)) {
                if (StringHelper::startsWith($key, 'PROPERTY_')) {
                    $code = StringHelper::cut($key, StringHelper::length('PROPERTY_'));
                    $fieldsProperties[$code] = $value;
                } elseif (StringHelper::startsWith($key, 'EMPTY_PROPERTY_')) {
                    $code = StringHelper::cut($key, StringHelper::length('EMPTY_PROPERTY_'));
                    $fieldsProperties[$code] = $value;
                } elseif (StringHelper::startsWith($key, 'SEO_PROPERTY_')) {
                    $code = StringHelper::cut($key, StringHelper::length('SEO_PROPERTY_'));
                    $fieldsSeo[$code] = $value;
                } elseif (StringHelper::startsWith($key, 'OFFERS_')) {
                    $code = StringHelper::cut($key, StringHelper::length('OFFERS_'));
                    $fieldsOffers[$code] = $value;

                    if ($createOffers)
                        $hasOffer = true;

                } elseif (StringHelper::startsWith($key, 'EMPTY_OFFERS_')) {
                    $code = StringHelper::cut($key, StringHelper::length('EMPTY_OFFERS_'));
                    $fieldsOffers['EMPTY_' . $code] = $value;
                } elseif (StringHelper::startsWith($key, 'CATALOG_PROPERTY_STORE_AMOUNT_')) {
                    $code = StringHelper::cut($key, StringHelper::length('CATALOG_PROPERTY_STORE_AMOUNT_'));
                    $fields['CATALOG_AMOUNT'][$code] = $value;
                } else {
                    if ($key === 'PREVIEW_PICTURE' || $key === 'DETAIL_PICTURE') {
                        $value = explode($this->baseData['delimiter'], $value);
                        $value = ArrayHelper::getFirstValue($value);
                        $value = trim($value);
                        $value = CFile::MakeFileArray($value);

                        if (StringHelper::startsWith($value['type'], 'image/'))
                            $fields[$key] = $value;

                    } elseif ($key === 'PREVIEW_TEXT_TYPE_TEXT') { /*change it to dynamic*/
                        $fields['PREVIEW_TEXT'] = $value;
                        $fields['PREVIEW_TEXT_TYPE'] = 'text';
                    } elseif ($key === 'PREVIEW_TEXT_TYPE_HTML') {
                        $fields['PREVIEW_TEXT'] = $value;
                        $fields['PREVIEW_TEXT_TYPE'] = 'html';
                    } elseif ($key === 'DETAIL_TEXT_TYPE_TEXT') {
                        $fields['DETAIL_TEXT'] = $value;
                        $fields['DETAIL_TEXT_TYPE'] = 'text';
                    } elseif ($key === 'DETAIL_TEXT_TYPE_HTML') {
                        $fields['DETAIL_TEXT'] = $value;
                        $fields['DETAIL_TEXT_TYPE'] = 'html';
                    } else {
                        $fields[$key] = $value;
                    }
                }
            }
        }

        if (!empty($fields['NAME'])) {
            $hasName = true;

            if (empty($fields['CODE'])) {
                if (!empty($this->baseData['autoGenerateCode']) || $this->iblockFields['CODE']['IS_REQUIRED'] === 'Y') {
                    $params = [
                        'max_len' => 100,
                        'change_case' => 'L',
                        'replace_space' => '_',
                        'replace_other' => '_',
                        'delete_repeat_replace' => true,
                        'safe_chars' => ''
                    ];

                    $fields['CODE'] = CUtil::translit(trim($fields['NAME']), 'ru', $params);
                }
            }
        }

        foreach ($this->baseData['identificationElements'] as $item) {
            if (StringHelper::startsWith($item, 'PROPERTY_')) {
                $code = StringHelper::cut($item, StringHelper::length('PROPERTY_'));

                if (ArrayHelper::keyExists($code, $fieldsProperties)) {
                    $type = Arrays::fromDBResult(\CIBlockProperty::GetList([], ['ACTIVE' => 'Y', 'IBLOCK_ID' => $this->baseData['iblock'], 'CODE' => $code]))->asArray();
                    $type = ArrayHelper::getFirstValue($type);

                    if (!empty($fieldsProperties[$code])) {
                        if ($type['PROPERTY_TYPE'] === 'L') {
                            $filter[$item . '_VALUE'] = $fieldsProperties[$code];
                        } else {
                            $filter[$item] = $fieldsProperties[$code];
                        }
                    }

                    unset($type);
                }

                unset($code);
            }

            if (!empty($fields[$item]))
                $filter[$item] = $fields[$item];
        }

        $sectionId = $this->createSections($data);

        if (empty($sectionId))
            $sectionId = $this->createSectionsLevel0($data);

        if (!empty($sectionId))
            $fields['IBLOCK_SECTION_ID'] = $sectionId;

        $elements = null;

        /** @todo Сделать фильтр и по выбранному разделу */
        if (!empty($filter))
            $elements = IBlockSelections::getElements($this->baseData['iblock'], ['ID' => 'ASC'], $filter, ['IBLOCK_ID', 'LID', 'ID', 'NAME', 'QUANTITY', 'IBLOCK_SECTION_ID'], 0, 0, true);

        if (Loader::includeModule('catalog')) {
            foreach ($elements as &$element) {
                $offersParentId[] = $element['ID'];
                $price = Arrays::fromDBResult(CPrice::GetList([],["PRODUCT_ID" => $element['ID']]))->asArray();
                $element['PRICE'] = $price;
            }
        }

        /*need tests*/
        if (!empty($fieldsProperties)) {
            $el = ArrayHelper::getFirstValue($elements);
            $properties = new Properties($this->baseData['delimiter'], $fieldsProperties, [], $this->baseData['iblock'], $el['PROPERTIES']);
            $properties = $properties->getProperties();
            $fields['PROPERTY_VALUES'] = $properties;

            unset($properties, $el);
        }

        if (!empty($fieldsSeo)) {
            $fields['IPROPERTY_TEMPLATES'] = $fieldsSeo;
        }


        if ($activate && empty($fields['ACTIVE']) ) {
            $fields['ACTIVE'] = 'Y';
        }

        if (!empty($elements)) {
            if ($updateElements)
                $this->updateElements($elements, $fields, $deactivateZeroQuantity, $deactivateZeroPrice);

            if ($hasOffer)
                $this->addOffers($fieldsOffers, $offersParentId);
        } else {
            if ($createElements) {
                if (!$hasName) {
                    $this->setError(['message' => Loc::getMessage('importexport.import.error.name')]);
                    return ;
                }

                if ($deactivateNew)
                    $fields['ACTIVE'] = 'N';

                if (!IBlockHelper::hasValue('PRICE', $fields, true) && $deactivateZeroPrice)
                    $fields['ACTIVE'] = 'N';

                if (!IBlockHelper::hasValue('QUANTITY', $fields, true) && $deactivateZeroQuantity)
                    $fields['ACTIVE'] = 'N';

                $element = new CIBlockElement;
                $result = $element->Add(
                    $fields,
                    false,
                    false,
                    false
                );

                if (!empty($result) && Loader::includeModule('catalog')) {
                    if (!CCatalogProduct::GetByID($result))
                        CCatalogProduct::Add(['ID' => $result]);

                    if (!empty($fields['CATALOG_AMOUNT'])) {
                        foreach ($fields['CATALOG_AMOUNT'] as $key => $storeAmount) {
                            CCatalogStoreProduct::Add([
                                'PRODUCT_ID' => $result,
                                'STORE_ID' => $key,
                                'AMOUNT' => $storeAmount
                            ]);
                        }
                    }

                    $price = new Price();
                    $price->setRoundMod($this->baseData['priceRoundMod']);
                    $price->setCurrency($this->baseData['defaultCurrency']);
                    $price->prepareFields($result, $fields);
                    $price->updatePrice($result, []);
                    $price->updateCatalogProperties($result);
                    $price->updateRatio($result);
                    $price->updateVatProperties($result);
                }

                if ($hasOffer)
                    $this->addOffers($fieldsOffers, $result);

                if ($result) {
                    $this->statistic['added']++;
                    $_SESSION['INTEC_IMPORT']['elementsIds'][] = $result;
                    //$this->statistic['elementIds'][] = $result;
                } else {
                    $this->setError(['message' => $element->LAST_ERROR]);
                }
            }
        }
    }

    public function addOffers ($data, $parentId)
    {
        if (empty($data))
            return null;

        $iblockId = CCatalog::GetByID($this->baseData['iblock']);

        if (!$iblockId) {
            $iblockId = CCatalogSKU::GetInfoByProductIBlock($this->baseData['iblock']);
            $iblockId = $iblockId['IBLOCK_ID'];
        } else {
            $iblockId = $iblockId['OFFERS_IBLOCK_ID'];
        }

        $offerDelimiter = '|';
        $offers = [];
        $newOffersList = [];

        foreach ($data as $key => $item) {
            $offers[$key] = explode($offerDelimiter, $item);
        }

        foreach ($offers as $key => $offer) {
            foreach ($offer as $index => $item) {
                if (!empty($item))
                    $newOffersList[$index][$key] = $item;
            }
        }

        $offers = $newOffersList;

        foreach ($offers as $offer) {
            $this->addOffer($offer, $iblockId, $parentId);
            $this->statistic['totalOffers']++;
        }
    }

    public function addOffer ($data, $iblockId, $parentId)
    {
        if (empty($data) || empty($iblockId))
            return null;

        $deactivateNew = $this->baseData['newDeactivate'] ? true : false;
        $deactivateZeroQuantity = $this->baseData['zeroQuantityDeactivate'] ? true : false;
        $deactivateZeroPrice = $this->baseData['zeroPriceDeactivate'] ? true : false;
        $activate = $this->baseData['activate'] ? true : false;


        $fields = [
            'MODIFIED_BY' => $this->userId,
            'IBLOCK_ID' => $iblockId
        ];

        $filter = [];
        $fieldsProperties = [];
        $fieldsSeo = [];

        foreach ($data as $key => $value) {
            if (!empty($key)) {
                if (StringHelper::startsWith($key, 'PROPERTY_')) {
                    $code = StringHelper::cut($key, StringHelper::length('PROPERTY_'));
                    $fieldsProperties[$code] = $value;
                } elseif (StringHelper::startsWith($key, 'EMPTY_PROPERTY_')) {
                    $code = StringHelper::cut($key, StringHelper::length('EMPTY_PROPERTY_'));
                    $fieldsProperties[$code] = $value;
                } elseif (StringHelper::startsWith($key, 'SEO_PROPERTY_')) {
                    $code = StringHelper::cut($key, StringHelper::length('SEO_PROPERTY_'));
                    $fieldsSeo[$code] = $value;
                } else {
                    $fields[$key] = $value;
                }
            }
        }

        foreach ($this->baseData['identificationOffers'] as $item) {
            if (StringHelper::startsWith($item, 'OFFERS_')) {
                $item = StringHelper::cut($item, StringHelper::length('OFFERS_'));
                $filter[$item] = $fields[$item];
            }
        }

        if (!empty($parentId))
            $filter['PROPERTY_CML2_LINK'] = $parentId;

        $elements = IBlockSelections::getElements($iblockId, ['ID' => 'ASC'], $filter, ['IBLOCK_ID', 'LID', 'ID', 'NAME', 'QUANTITY'], 0, 0, true);

        foreach ($elements as &$element) {
            $price = Arrays::fromDBResult(CPrice::GetList([],["PRODUCT_ID" => $element['ID']]))->asArray();
            $element['PRICE'] = $price;
        }

        if (count($elements) <= 1) {
            $properties = [];
            if (!empty($parentId))
                $properties['CML2_LINK'] = $parentId;

            if (!empty($fieldsProperties)) {
                $el = ArrayHelper::getFirstValue($elements);
                $properties = new Properties($this->baseData['delimiter'], $fieldsProperties, [], $iblockId, $el['PROPERTIES']);
                $properties = $properties->getProperties();

                unset($properties, $el);
            }

            if (!empty($properties))
                $fields['PROPERTY_VALUES'] = $properties;

            if (!empty($fieldsSeo)) {
                $fields['IPROPERTY_TEMPLATES'] = $fieldsSeo;
            }
        }

        if ($activate && empty($fields['ACTIVE']) ) {
            $fields['ACTIVE'] = 'Y';
        }

        if (!empty($elements)) {
            $this->updateElements($elements, $fields, $deactivateZeroQuantity, $deactivateZeroPrice, true);
        } else {

            if ($deactivateNew)
                $fields['ACTIVE'] = 'N';

            if (!IBlockHelper::hasValue('PRICE', $fields, true) && $deactivateZeroPrice)
                $fields['ACTIVE'] = 'N';

            if (!IBlockHelper::hasValue('QUANTITY', $fields, true) && $deactivateZeroQuantity)
                $fields['ACTIVE'] = 'N';

            $element = new CIBlockElement;
            $result = $element->Add(
                $fields,
                false,
                false,
                false
            );

            if (!empty($result)) {
                if (!CCatalogProduct::GetByID($result))
                    CCatalogProduct::Add(['ID' => $result]);

                $price = new Price();
                $price->setRoundMod($this->baseData['priceRoundMod']);
                $price->setCurrency($this->baseData['defaultCurrency']);
                $price->prepareFields($result, $fields);
                $price->updatePrice($result, []);
                $price->updateCatalogProperties($result);
                $price->updateRatio($result);
                $price->updateVatProperties($result);
            }

            if ($result) {
                $this->statistic['offersAdded']++;
                $_SESSION['INTEC_IMPORT']['offersIds'][] = $result;
                //$this->statistic['offersIds'][] = $result;
                $_SESSION['INTEC_IMPORT']['offersIds'] = array_unique($_SESSION['INTEC_IMPORT']['offersIds']);
                //$this->statistic['offersIds'] = array_unique($this->statistic['offersIds']);
            }
            else {
                $this->setError(['message' => $element->LAST_ERROR]);
            }
        }
    }

    public function createSections ($data)
    {
        if (empty($data))
            return null;

        $prefix = 'SECTION_PROPERTY_';

        $parentId = false;
        $dontCreateNew = $this->baseData['sectionDontCreateNew'] ? true : false;
        $priorityPath = false;
        $sectionsFromPath = [];
        $sectionsFromPathIndex = 'NAME';
        $filterOnDepthLevel = true; //take into account nesting level

        if (ArrayHelper::keyExists('SECTION_PATH', $data) && !empty($data['SECTION_PATH'])) {
            $sections = explode('/', $data['SECTION_PATH']);

            foreach ($sections as $key => $section) {
                $sectionsFromPath[$key + 1] = [
                    'IBLOCK_ID' => $this->baseData['iblock'],
                    $sectionsFromPathIndex => trim($section)
                ];
            }

            unset($sections);
        }

        for ($i = 1; $i <= $this->baseData['level']; $i++) {
            $levelPrefix = $prefix . 'LEVEL_' . $i . '_';
            $hasSection = false;
            $hasSectionFromPath = !empty($sectionsFromPath[$i][$sectionsFromPathIndex]);

            $filter = [
                'ID' => $data[$levelPrefix . 'ID'],
                'IBLOCK_ID' => $this->baseData['iblock'],
                'NAME' => $data[$levelPrefix . 'NAME'],
                'CODE' => $data[$levelPrefix . 'CODE'],
                'XML_ID' => $data[$levelPrefix . 'XML_ID'],
                'ACTIVE' => $data[$levelPrefix . 'ACTIVE'],
                'SORT' => $data[$levelPrefix . 'SORT'],
                'DESCRIPTION' => $data[$levelPrefix . 'DESCRIPTION'],
                'SECTION_PAGE_URL' => $data[$levelPrefix . 'SECTION_PAGE_URL'],
                'PAGE_TITLE' => $data[$levelPrefix . 'PAGE_TITLE'],
            ];

            if ($filterOnDepthLevel) //take into account nesting level
                $filter['DEPTH_LEVEL'] = $i;

            if (!empty($parentId)) {
                if ($hasSectionFromPath) {
                    $sectionsFromPath[$i]['IBLOCK_SECTION_ID'] = $parentId;
                    $sectionsFromPath[$i]['SECTION_ID'] = $parentId;
                }

                $filter['IBLOCK_SECTION_ID'] = $parentId;
                $filter['SECTION_ID'] = $parentId;
            }

            $hasFilterField = false;

            foreach ($filter as $key => $value) {
                if (!empty($value) && ($key !== 'IBLOCK_ID' &&$key !== 'SECTION_ID' && $key !== 'IBLOCK_SECTION_ID' && $key !== 'DEPTH_LEVEL')) {
                    $hasFilterField = true;
                    break;
                }
            }

            if ($hasFilterField && $priorityPath && $hasSectionFromPath)
                $filter = $sectionsFromPath[$i];

            if (!$hasFilterField && $hasSectionFromPath) {
                $filter = $sectionsFromPath[$i];
                $filter['DEPTH_LEVEL'] = $i;
                $hasFilterField = true;
            }

            if ($hasFilterField)
                $hasSection = $this->checkSection($filter, $data, $levelPrefix);
            else
                break;

            if (empty($hasSection)) {
                if (!$dontCreateNew) {
                    $filter['IPROPERTY_TEMPLATES'] = [
                        'SECTION_META_TITLE' => $data[$levelPrefix . 'META_TITLE'],
                        'SECTION_META_KEYWORDS' => $data[$levelPrefix . 'META_KEYWORDS'],
                        'SECTION_META_DESCRIPTION' => $data[$levelPrefix . 'META_DESCRIPTION'],
                    ];

                    $picture = $data[$levelPrefix . 'PICTURE'];
                    $detailPicture = $data[$levelPrefix . 'DETAIL_PICTURE'];

                    if (!empty($picture)) {
                        if (!\intec\core\helpers\Type::isArray($picture)) {
                            $filter['PICTURE'] = CFile::MakeFileArray($picture);
                        } else {
                            $picture = ArrayHelper::getFirstValue($picture);
                        }
                    }

                    if (!empty($detailPicture)) {
                        if (!\intec\core\helpers\Type::isArray($detailPicture)) {
                            $filter['DETAIL_PICTURE'] = CFile::MakeFileArray($detailPicture);
                        } else {
                            $detailPicture = ArrayHelper::getFirstValue($detailPicture);
                        }
                    }
                    unset($picture, $detailPicture);

                    $parentId = $this->createSection($filter);
                }
            } else {
                $parentId = $hasSection;
                $this->statistic['section']['updated'][] = $parentId;
            }
        }

        return $parentId;
    }

    public function createSection ($filter)
    {
        if (!empty($filter['NAME']) && empty($filter['CODE'])) {
            $params = [
                'max_len' => 100,
                'change_case' => 'L',
                'replace_space' => '_',
                'replace_other' => '_',
                'delete_repeat_replace' => true,
                'safe_chars' => ''
            ];
            $filter['CODE'] = CUtil::translit($filter['NAME'], 'ru', $params);

            if (empty($filter['CODE']))
                $filter['CODE'] = $newXmlId = \Bitrix\Main\Security\Random::getString(24);
        }

        $bs = new CIBlockSection;
        $result = $bs->Add($filter);

        $this->statistic['sectionCreate']++;
        $this->statistic['section']['created'][] = $result;

        if (!$result)
            $this->setError(['message' => Loc::getMessage('importexport.import.error.create.section') . ' ' . $bs->LAST_ERROR]);

        return $result;
    }

    public function createSectionsLevel0 ($data)
    {
        if (empty($data))
            return null;

        $levelPrefix = 'SECTION_PROPERTY_LEVEL_0_';
        $dontCreateNew = $this->baseData['sectionDontCreateNew'] ? true : false;
        $sectionsFromPath = [];
        $sectionsFromPathIndex = 'NAME';
        $hasFilterField = false;

        if (ArrayHelper::keyExists('SECTION_PATH', $data) && !empty($data['SECTION_PATH'])) {
            $sections = explode('/', $data['SECTION_PATH']);

            foreach ($sections as $key => $section) {
                $sectionsFromPath[$key + 1] = [
                    'IBLOCK_ID' => $this->baseData['iblock'],
                    $sectionsFromPathIndex => trim($section)
                ];
            }

            unset($sections);
        }

        $filter = [
            'ID' => $data[$levelPrefix . 'ID'],
            'IBLOCK_ID' => $this->baseData['iblock'],
            'NAME' => $data[$levelPrefix . 'NAME'],
            'CODE' => $data[$levelPrefix . 'CODE'],
            'XML_ID' => $data[$levelPrefix . 'XML_ID'],
            'ACTIVE' => $data[$levelPrefix . 'ACTIVE'],
            'SORT' => $data[$levelPrefix . 'SORT'],
            'DESCRIPTION' => $data[$levelPrefix . 'DESCRIPTION'],
            'SECTION_PAGE_URL' => $data[$levelPrefix . 'SECTION_PAGE_URL'],
            'PAGE_TITLE' => $data[$levelPrefix . 'PAGE_TITLE'],
        ];

        foreach ($filter as $key => $value) {
            if (!empty($value) && ($key !== 'IBLOCK_ID' &&$key !== 'SECTION_ID' && $key !== 'IBLOCK_SECTION_ID' && $key !== 'DEPTH_LEVEL')) {
                $hasFilterField = true;
                break;
            }
        }

        if (!$hasFilterField)
            return null;

        $hasSection = $this->checkSection($filter, $data, $levelPrefix);

        if (empty($hasSection)) {
            if (!$dontCreateNew) {
                $filter['IPROPERTY_TEMPLATES'] = [
                    'SECTION_META_TITLE' => $data[$levelPrefix . 'META_TITLE'],
                    'SECTION_META_KEYWORDS' => $data[$levelPrefix . 'META_KEYWORDS'],
                    'SECTION_META_DESCRIPTION' => $data[$levelPrefix . 'META_DESCRIPTION'],
                ];

                $picture = $data[$levelPrefix . 'PICTURE'];
                $detailPicture = $data[$levelPrefix . 'DETAIL_PICTURE'];

                if (!empty($picture)) {
                    if (!\intec\core\helpers\Type::isArray($picture)) {
                        $filter['PICTURE'] = CFile::MakeFileArray($picture);
                    } else {
                        $picture = ArrayHelper::getFirstValue($picture);
                    }
                }

                if (!empty($detailPicture)) {
                    if (!\intec\core\helpers\Type::isArray($detailPicture)) {
                        $filter['DETAIL_PICTURE'] = CFile::MakeFileArray($detailPicture);
                    } else {
                        $detailPicture = ArrayHelper::getFirstValue($detailPicture);
                    }
                }
                unset($picture, $detailPicture);

                $parentId = $this->createSection($filter);
            }
        } else {
            $parentId = $hasSection;
            $this->statistic['section']['updated'][] = $parentId;
            return $parentId;
        }

        return null;
    }

    /**
     * Проверяет существование раздела и обновляет его поля.
     * В зависимости от настроек возвращает id раздела или результат выборки.
     * @param array $filter фильтр для выборки раздел.
     * @param array $fields поля для обновения раздела.
     * @param string|null $levelPrefix уровень вложенности.
     * @return array|number|false
     */
    public function checkSection ($filter, $fields = [], $levelPrefix = null)
    {
        if (empty($filter))
            return false;

        $result = Arrays::fromDBResult(CIBlockSection::GetList([], $filter, false, [], false))->asArray();

        if (empty($result))
            return false;

        $result = ArrayHelper::getFirstValue($result);

        if (!empty($fields) && !empty($levelPrefix))
            $this->updateSection($result, $fields, $levelPrefix);

        return $result['ID'];
    }

    /** @todo подумать */
    public function updateSection ($section, $fields, $levelPrefix)
    {
        if (empty($section) || empty($fields) || empty($levelPrefix))
            return false;

        $newFields = [
            'IBLOCK_ID' => $this->baseData['iblock'],
            'NAME' => $fields[$levelPrefix . 'NAME'],
            'CODE' => $fields[$levelPrefix . 'CODE'],
            'XML_ID' => $fields[$levelPrefix . 'XML_ID'],
            'ACTIVE' => $fields[$levelPrefix . 'ACTIVE'],
            'SORT' => $fields[$levelPrefix . 'SORT'],
            'DESCRIPTION' => $fields[$levelPrefix . 'DESCRIPTION'],
            'SECTION_PAGE_URL' => $fields[$levelPrefix . 'SECTION_PAGE_URL'],
            'PAGE_TITLE' => $fields[$levelPrefix . 'PAGE_TITLE'],
            'IPROPERTY_TEMPLATES' => [
                'SECTION_META_TITLE' => $fields[$levelPrefix . 'META_TITLE'],
                'SECTION_META_KEYWORDS' => $fields[$levelPrefix . 'META_KEYWORDS'],
                'SECTION_META_DESCRIPTION' => $fields[$levelPrefix . 'META_DESCRIPTION'],
            ]
        ];

        if (!empty($fields[$levelPrefix . 'PICTURE'])) {
            if (!$this->checkPicture($section['PICTURE'], $fields[$levelPrefix . 'PICTURE'])) {
                $newFields['PICTURE'] = CFile::MakeFileArray($fields[$levelPrefix . 'PICTURE']);
            }
        }
        if (!empty($fields[$levelPrefix . 'DETAIL_PICTURE'])) {
            if (!$this->checkPicture($section['DETAIL_PICTURE'], $fields[$levelPrefix . 'DETAIL_PICTURE'])) {
                $newFields['DETAIL_PICTURE'] = CFile::MakeFileArray($fields[$levelPrefix . 'DETAIL_PICTURE']);
            }
        }

        $bs = new CIBlockSection;
        $result = $bs->update($section['ID'], $newFields);

        return $result;
    }

    public function checkPicture ($id, $path, $delimiter = '/')
    {
        if (empty($path) || empty($id))
            return false;

        $oldFile = CFile::GetFileArray($id);
        $oldFileName = $oldFile['ORIGINAL_NAME'];
        $newFileName = explode($delimiter, $path);
        $newFileName = $newFileName[count($newFileName) - 1];

        return $oldFileName === $newFileName;
    }

    public function updateElements ($elements = [], $fields = [], $deactivateZeroQuantity = false, $deactivateZeroPrice = false, $isOffer = false)
    {
        if (empty($elements) || empty($fields))
            return null;

        foreach ($elements as $element) {
            $newElement = new CIBlockElement;

            if (Loader::includeModule('catalog')) {
                if (!CCatalogProduct::GetByID($element['ID']))
                    CCatalogProduct::Add(['ID' => $element['ID']]);

                $price = new Price();
                $price->setRoundMod($this->baseData['priceRoundMod']);
                $price->setCurrency($this->baseData['defaultCurrency']);
                $price->prepareFields($element['ID'], $fields);
                $price->updatePrice($element['ID'], $element['PRICE']);
                $price->updateCatalogProperties($element['ID']);
                $price->updateRatio($element['ID']);
                $price->updateVatProperties($element['ID']);

                $statistic = $price->getStatistic();
            }

            if (!$statistic['hasPrice'] && $deactivateZeroPrice) {
                $fields['ACTIVE'] = 'N';
            }

            if (!$statistic['hasQuantity'] && $deactivateZeroQuantity) {
                $fields['ACTIVE'] = 'N';
            }

            $result = $newElement->Update($element['ID'], $fields);

            if ($result) {
                if (!empty($fields['CATALOG_AMOUNT'])) {
                    foreach ($fields['CATALOG_AMOUNT'] as $key => $storeAmount) {
                        CCatalogStoreProduct::Add([
                            'PRODUCT_ID' => $element['ID'],
                            'STORE_ID' => $key,
                            'AMOUNT' => $storeAmount
                        ]);
                    }
                }

                if (!$isOffer) {
                    $this->statistic['updated']++;
                    $_SESSION['INTEC_IMPORT']['elementsIds'][] = $element['ID'];
                    //$this->statistic['elementIds'][] = $element['ID'];
                } else {
                    $this->statistic['offersUpdated']++;
                    $_SESSION['INTEC_IMPORT']['offersIds'][] = $element['ID'];
                    //$this->statistic['offersIds'][] = $element['ID'];
                }
            } else {
                $this->setError(['message' => $newElement->LAST_ERROR]);
            }
        }
    }

    public function deleteElements ($data)
    {
        if (empty($this->baseData) || empty($this->tableData))
            return null;

        $settings = $this->settingsData['settings'];
        $hasSettings = false;
        if (!empty($settings)) {
            foreach ($settings as $setting) {
                if (!empty($setting)) {
                    $hasSettings = true;
                    break;
                }
            }
        }

        $newData = [];

        foreach ($data as $dataKey => $dataItem) {
            if ($hasSettings) {
                $subRes = null;
                foreach ($settings as $settingKey => $setting) {
                    $subRes[$settingKey] = ConditionHelper::getComputedImportValue($dataItem, $settingKey, $setting, $this->baseData['delimiter']);
                }

                $dataItem = $subRes;
            }

            foreach ($dataItem as $key => $item) {
                foreach ($this->tableData['data'][$key] as $dataGroup) {
                    if (empty($dataGroup) || $dataGroup == 'false')
                        continue;

                    $newData[$dataKey][$dataGroup] = $item;
                }
            }

        }

        $data = $newData;
        unset($newData, $dataKey, $dataItem, $key, $item);

        foreach ($data as $item) {
            $this->deleteElement($item);
            $this->statistic['totalRows']++;
        }
    }

    public function deleteElement ($data)
    {
        if (empty($data))
            return null;

        $fields = [
            'IBLOCK_ID' => $this->baseData['iblock']
        ];

        $filter = [];
        $fieldsProperties = [];
        $fieldsSeo = [];
        $hasOffer = false;
        $offersParentId = null;
        $fieldsOffers = null;
        $hasName = false;

        foreach ($data as $key => $value) {
            if (!empty($key)) {
                if (StringHelper::startsWith($key, 'PROPERTY_')) {
                    $code = StringHelper::cut($key, StringHelper::length('PROPERTY_'));
                    $fieldsProperties[$code] = $value;
                } elseif (StringHelper::startsWith($key, 'EMPTY_PROPERTY_')) {
                    $code = StringHelper::cut($key, StringHelper::length('EMPTY_PROPERTY_'));
                    $fieldsProperties[$code] = $value;
                } elseif (StringHelper::startsWith($key, 'SEO_PROPERTY_')) {
                    $code = StringHelper::cut($key, StringHelper::length('SEO_PROPERTY_'));
                    $fieldsSeo[$code] = $value;
                } elseif (StringHelper::startsWith($key, 'OFFERS_')) {
                    $code = StringHelper::cut($key, StringHelper::length('OFFERS_'));
                    $fieldsOffers[$code] = $value;

                } elseif (StringHelper::startsWith($key, 'EMPTY_OFFERS_')) {
                    $code = StringHelper::cut($key, StringHelper::length('EMPTY_OFFERS_'));
                    $fieldsOffers['EMPTY_' . $code] = $value;
                } elseif (StringHelper::startsWith($key, 'CATALOG_PROPERTY_STORE_AMOUNT_')) {
                    $code = StringHelper::cut($key, StringHelper::length('CATALOG_PROPERTY_STORE_AMOUNT_'));
                    $fields['CATALOG_AMOUNT'][$code] = $value;
                } else {
                    if ($key === 'PREVIEW_PICTURE' || $key === 'DETAIL_PICTURE') {
                        $value = explode($this->baseData['delimiter'], $value);
                        $value = ArrayHelper::getFirstValue($value);
                        $value = trim($value);
                        $value = CFile::MakeFileArray($value);

                        if (StringHelper::startsWith($value['type'], 'image/'))
                            $fields[$key] = $value;

                    } elseif ($key === 'PREVIEW_TEXT_TYPE_TEXT') {
                        $fields['PREVIEW_TEXT'] = $value;
                        $fields['PREVIEW_TEXT_TYPE'] = 'text';
                    } elseif ($key === 'PREVIEW_TEXT_TYPE_HTML') {
                        $fields['PREVIEW_TEXT'] = $value;
                        $fields['PREVIEW_TEXT_TYPE'] = 'html';
                    } elseif ($key === 'DETAIL_TEXT_TYPE_TEXT') {
                        $fields['DETAIL_TEXT'] = $value;
                        $fields['DETAIL_TEXT_TYPE'] = 'text';
                    } elseif ($key === 'DETAIL_TEXT_TYPE_HTML') {
                        $fields['DETAIL_TEXT'] = $value;
                        $fields['DETAIL_TEXT_TYPE'] = 'html';
                    } else {
                        $fields[$key] = $value;
                    }
                }
            }
        }

        foreach ($this->baseData['identificationElements'] as $item) {
            if (StringHelper::startsWith($item, 'PROPERTY_')) {
                $code = StringHelper::cut($item, StringHelper::length('PROPERTY_'));

                if (ArrayHelper::keyExists($code, $fieldsProperties)) {
                    $type = Arrays::fromDBResult(\CIBlockProperty::GetList([], ['ACTIVE' => 'Y', 'IBLOCK_ID' => $this->baseData['iblock'], 'CODE' => $code]))->asArray();
                    $type = ArrayHelper::getFirstValue($type);

                    if (!empty($fieldsProperties[$code])) {
                        if ($type['PROPERTY_TYPE'] === 'L') {
                            $filter[$item . '_VALUE'] = $fieldsProperties[$code];
                        } else {
                            $filter[$item] = $fieldsProperties[$code];
                        }
                    }

                    unset($type);
                }

                unset($code);
            }

            if (!empty($fields[$item]))
                $filter[$item] = $fields[$item];
        }


        $elements = null;

        if (!empty($filter))
            $elements = IBlockSelections::getElements($this->baseData['iblock'], ['ID' => 'ASC'], $filter, ['ID', 'NAME'], 0, 0, false);

        if (!empty($elements)) {
            foreach ($elements as $element) {
                CIBlockElement::Delete($element['ID']);
                $this->statistic['deleted']++;
                $_SESSION['INTEC_IMPORT']['elementsIds'][] = $element['ID'];
                //$this->statistic['elementIds'][] = $element['ID'];
            }
        }
    }

    public function import ()
    {
        $data = $this->getImportData();

        $data = ArrayHelper::getFirstValue($data);
        $sectionNoneUpdate = $this->baseData['sectionNoneUpdate'] ? true : false;
        $activateNonEmpty = $this->baseData['sectionActivateNoneEmpty'] ? true : false;
        $deactivateEmpty = $this->baseData['sectionDeactivateEmpty'] ? true : false;
        $nonActive = $this->baseData['sectionDeactivateEmptyActive'] ? true : false;
        $deleteEmpty = $this->baseData['sectionDeleteEmpty'] ? true : false;

        if (empty($data)) {
            $this->statistic['finished'] = true;
            $_SESSION['INTEC_IMPORT']['elementsIds'] = array_unique($_SESSION['INTEC_IMPORT']['elementsIds']);

            return null;
        }

        if (!empty($this->baseData['iblock'])) {
            $iblockFields = \CIBlock::GetArrayByID($this->baseData['iblock']);
            $this->iblockFields = $iblockFields['FIELDS'];
            unset($iblockFields);
        }

        $data = $this->getSelectedRows($data, 1, $this->baseData['importInStep']);

        $hasIdentification = false;

        foreach ($this->baseData['identificationElements'] as $identification) {
            $hasIdentification = IBlockHelper::hasValue($identification, $this->tableData['data'], true);

            if (!$hasIdentification)
                break;
        }

        if ($hasIdentification) {
            $oldSections = [];

            if ($sectionNoneUpdate) {
                $oldSections = Arrays::fromDBResult(CIBlockSection::GetList(
                    [],
                    ['IBLOCK_ID' => $this->baseData['iblock']],
                    ['ELEMENT_SUBSECTIONS' => 'Y'],
                    ['ID', 'ELEMENT_CNT']
                ))->asArray(function ($index, $sectionResult) {
                    return ['value' => $sectionResult['ID']];
                });
            }

            if ($this->baseData['deleteMode'])
                $this->deleteElements($data);
            else
                $this->addElements($data);

            if ($activateNonEmpty || $deactivateEmpty || $deleteEmpty) {
                $sections = IBlockHelper::getSectionExceptionIds($this->baseData['iblock'], $oldSections);

                if (!empty($sections))
                    ImportHelper::actionOnSection($this->baseData['iblock'], $sections, $activateNonEmpty, $deactivateEmpty, $deleteEmpty, $nonActive);
            }
        } else {
            $this->statistic['finished'] = true;
            $this->setError(['message' => Loc::getMessage('importexport.import.error.template.identification.fields.not.selected')], true);
            return null;
        }

    }

    public function getImportData ()
    {
        if ($this->rowSettingsData['all'] == 'false' && empty($this->rowSettingsData['selected']))
            return null;

        /*$startRow = 1;

        if ($startRow > $this->rowSettingsData['count'] && $this->rowSettingsData['all'] == 'false')
            return null;*/

        if (!empty($this->baseData['fileId'])) {
            $fileData = CFile::GetFileArray($this->baseData['fileId']);
            $fileType = substr(strrchr($fileData['ORIGINAL_NAME'], "."), 1);
            $filePath = $fileData['SRC'];
        } elseif (!empty($this->baseData['file'])) {
            $fileType = substr(strrchr($this->baseData['file'], "."), 1);
            $filePath = $this->baseData['file'];
        }


        if ($fileType === 'xlsx') {
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        } elseif ($fileType === 'xls') {
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
        } elseif ($fileType === 'csv') {
            $encode = 'UTF-8';
            $encodeFilePath = $_SERVER["DOCUMENT_ROOT"].$filePath;

            if(file_exists($encodeFilePath)) {
                $handle = fopen($encodeFilePath, "r");
                $contents = fread($handle, 260000);
                fclose($handle);

                if(!(CUtil::DetectUTF8($contents))) {
                    if(!function_exists('iconv') || iconv('CP1251', 'CP1251', $contents) == $contents)
                        $encode = 'CP1251';
                }
            }

            $objReader = PHPExcel_IOFactory::createReader('CSV')
                ->setInputEncoding($encode)->setDelimiter($this->baseData['csvDelimiter'])
                ->setEnclosure(self::getCsvEnclosure())
                ->setSheetIndex(0);
        } else {
            return null;
        }

        $filePath = ImportHelper::convertFilePath($_SERVER['DOCUMENT_ROOT'] . $filePath);

        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filePath);
        $sheet = $objPHPExcel->getActiveSheet();
        $maxCell = $sheet->getHighestRowAndColumn();
        $data = $sheet->rangeToArray('A'. 1 .':' . $maxCell['column'] . $maxCell['row']);

        unset($sheet, $objPHPExcel, $objReader);


        foreach ($data as &$row) {
            foreach ($row as &$value) {
                $value = Encoding::convertEncodingToCurrent($value);
            }
        }


        return ['data' => $data];
    }

    public function importByCron ($id)
    {
        $return = null;
        $template = Template::findOne($id);
        $isBase = Loader::includeModule('sale');

        if (empty($template))
            return null;

        if (empty($id)) {
            $this->setError(['message' => Loc::getMessage('importexport.import.error.template.id')], true);
            return null;
        }

        $template = Template::findOne($id);

        if (empty($this->baseData)) {
            try {
                $this->setBaseData(Json::decode($template->getAttribute('params')));

                if (empty($this->baseData['fileId']) && empty($this->baseData['file'])) {
                    $this->error['is'] = true;
                    $this->error['errors'][] = [
                        'message' => Loc::getMessage('intec.importexport.export.error.non.file')
                    ];
                }

                if (empty($this->baseData['iblock'])) {
                    $this->error['is'] = true;
                    $this->error['errors'][] = [
                        'message' => Loc::getMessage('intec.importexport.export.error.non.iblock')
                    ];
                }

                if (empty($this->baseData['identificationElements'])) {
                    $this->error['is'] = true;
                    $this->error['errors'][] = [
                        'message' => Loc::getMessage('importexport.import.error.non.identification')
                    ];
                }

                if ($isBase && !empty($this->baseData['iblock'])) {
                    $iblockId = CCatalog::GetByID($this->baseData['iblock']);

                    if (!$iblockId) {
                        $iblockId = CCatalogSKU::GetInfoByProductIBlock($this->baseData['iblock']);
                        $iblockId = $iblockId['IBLOCK_ID'];
                    } else {
                        $iblockId = $iblockId['OFFERS_IBLOCK_ID'];
                    }

                    if (!empty($iblockId) && empty($this->baseData['identificationOffers'])) {
                        $this->error['is'] = true;
                        $this->error['errors'][] = [
                            'message' => Loc::getMessage('importexport.import.error.non.identification.offers')
                        ];
                    }
                }


            } catch (InvalidParamException $exception) {
                return null;
            }
        }

        if (empty($this->tableData)) {
            try {
                $this->setTableData(Json::decode($template->getAttribute('tableParams')));
            } catch (InvalidParamException $exception) {
                return null;
            }
        }

        if (empty($this->settingsData)) {
            try {
                $this->setSettings(Json::decode($template->getAttribute('columnSettings'), true, true));

            } catch (InvalidParamException $exception) {
                return null;
            }
        }

        if (empty($this->rowSettingsData)) {
            try {
                $this->setRowSettings(Json::decode($template->getAttribute('rowSettings')));
            } catch (InvalidParamException $exception) {
                return null;
            }
        }

        if (!$this->error['is']) {
            unset($_SESSION['INTEC_IMPORT']);

            $return = $this->import();
        }

        if (!empty($return)) {
            $return = [
                'resultMessage' => Loc::getMessage('importexport.import.error.import'),
                'error' => $this->error
            ];
        }

        return $return;
    }

    public function importByIdOnStep ($id = null, $step = 1, $prevStatistic = null)
    {
        $data = $this->getImportFileData($id, $step, false, true);

        $data = ArrayHelper::getFirstValue($data);
        $sectionNoneUpdate = $this->baseData['sectionNoneUpdate'] ? true : false;
        $activateNonEmpty = $this->baseData['sectionActivateNoneEmpty'] ? true : false;
        $deactivateEmpty = $this->baseData['sectionDeactivateEmpty'] ? true : false;
        $nonActive = $this->baseData['sectionDeactivateEmptyActive'] ? true : false;
        $deleteEmpty = $this->baseData['sectionDeleteEmpty'] ? true : false;

        if (!empty($this->baseData['iblock'])) {
            $iblockFields = \CIBlock::GetArrayByID($this->baseData['iblock']);
            $this->iblockFields = $iblockFields['FIELDS'];
            unset($iblockFields);
        }

        if (empty($data)) {
            //if (!empty($prevStatistic))
            $this->statistic['finished'] = true;
            $_SESSION['INTEC_IMPORT']['elementsIds'] = array_unique($_SESSION['INTEC_IMPORT']['elementsIds']);

            return null;
        }

        $data = $this->getSelectedRows($data, $step, $this->baseData['importInStep']);

        if ($step <= 1 && !empty($_SESSION['INTEC_IMPORT'])) {
            unset($_SESSION['INTEC_IMPORT']);
        }

        /*fix this later*/
        /*if ($step <= 1) {
            $firstRow = ArrayHelper::shift($data);
        }*/

        $hasIdentification = false;

        foreach ($this->baseData['identificationElements'] as $identification) {
            $hasIdentification = IBlockHelper::hasValue($identification, $this->tableData['data'], true);

            if (!$hasIdentification)
                break;
        }

        if ($hasIdentification) {
            $oldSections = [];

            if ($sectionNoneUpdate) {
                $oldSections = Arrays::fromDBResult(CIBlockSection::GetList(
                    [],
                    ['IBLOCK_ID' => $this->baseData['iblock']],
                    ['ELEMENT_SUBSECTIONS' => 'Y'],
                    ['ID', 'ELEMENT_CNT']
                ))->asArray(function ($index, $sectionResult) {
                    return ['value' => $sectionResult['ID']];
                });
            }

            if ($this->baseData['deleteMode'])
                $this->deleteElements($data);
            else
                $this->addElements($data);

            if ($activateNonEmpty || $deactivateEmpty || $deleteEmpty) {
                $sections = IBlockHelper::getSectionExceptionIds($this->baseData['iblock'], $oldSections);

                if (!empty($sections))
                    ImportHelper::actionOnSection($this->baseData['iblock'], $sections, $activateNonEmpty, $deactivateEmpty, $deleteEmpty, $nonActive);
            }
        } else {
            $this->statistic['finished'] = true;
            $this->setError(['message' => Loc::getMessage('importexport.import.error.template.identification.fields.not.selected')], true);
            return null;
        }
    }


    private function setTemplateAndGetData ($id)
    {
        if (empty($id)) {
            $this->setError(['message' => Loc::getMessage('importexport.import.error.template.id')], true);
            return null;
        }

        $template = Template::findOne($id);

        if (empty($template)) {
            $this->setError(['message' => StringHelper::replaceMacros(Loc::getMessage('importexport.import.error.template.not.found'), ['TEMPLATE_ID' => $id])], true);
            return null;
        }

        try {
            $parameters = Json::decode($template->getAttribute('params'));
        } catch (InvalidParamException $exception) {
            $parameters = [];
        }

        try {
            $tableParameters = Json::decode($template->getAttribute('tableParams'));
        } catch (InvalidParamException $exception) {
            $tableParameters = [];
        }

        try {
            $settings = Json::decode($template->getAttribute('columnSettings'), true, true);
        } catch (InvalidParamException $exception) {
            $settings = [];
        }

        try {
            $rowSettings = Json::decode($template->getAttribute('rowSettings'));
        } catch (InvalidParamException $exception) {
            $rowSettings = [];
        }

        $this->setBaseData($parameters);
        $this->setTableData($tableParameters);
        $this->setSettings($settings);
        $this->setRowSettings($rowSettings);

        return true;
    }


    /*v2 */
    public function getImportCount ($id = null)
    {
        if (empty($id))
            return null;


        if (!$this->setTemplateAndGetData($id))
            return null;

        if (empty($this->baseData) )
            return null;

        if (!empty($this->baseData['fileId'])) {
            $fileData = CFile::GetFileArray($this->baseData['fileId']);
            $fileType = substr(strrchr($fileData['ORIGINAL_NAME'], "."), 1);
            $filePath = $fileData['SRC'];
        } elseif (!empty($this->baseData['file'])) {
            $fileType = substr(strrchr($this->baseData['file'], "."), 1);
            $filePath = $this->baseData['file'];
        }

        if ($fileType === 'xlsx')
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        elseif ($fileType === 'xls')
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
        elseif ($fileType === 'csv')
            $objReader = PHPExcel_IOFactory::createReader('CSV')->setDelimiter($this->baseData['csvDelimiter'])
                ->setEnclosure(self::getCsvEnclosure())
                ->setSheetIndex(0);
        else
            return null;


        $result = $objReader->listWorksheetInfo($_SERVER['DOCUMENT_ROOT'] . $filePath);
        $result = ArrayHelper::getFirstValue($result);

        $result['end'] = true;
        $result['count'] = $result['totalRows'];

        return $result;
    }

    /*v1 by steps. Slow version */
    public function getImportCountByStep ($id, $step, $count = null, $defaultChunkSize = 30000)
    {
        if (empty($id))
            return null;

        if (!$this->setTemplateAndGetData($id))
            return null;

        if (empty($this->baseData) )
            return null;

        if (!empty($this->baseData['fileId'])) {
            $fileData = CFile::GetFileArray($this->baseData['fileId']);
            $fileType = substr(strrchr($fileData['ORIGINAL_NAME'], "."), 1);
            $filePath = $fileData['SRC'];
        } elseif (!empty($this->baseData['file'])) {
            $fileType = substr(strrchr($this->baseData['file'], "."), 1);
            $filePath = $this->baseData['file'];
        }

        if ($fileType === 'xlsx')
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        elseif ($fileType === 'xls')
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
        elseif ($fileType === 'csv')
            $objReader = PHPExcel_IOFactory::createReader('CSV')->setDelimiter($this->baseData['csvDelimiter'])
                ->setEnclosure(self::getCsvEnclosure())
                ->setSheetIndex(0);
        else
            return null;


        $chunkSize = $defaultChunkSize;

        $startRow = 1;

        if ($step > 1)
            $startRow = ($chunkSize * ($step - 1)) + 1;

        $chunkFilter = new PHPExcel_ChunkReadFilter();

        $chunkFilter->setRows($startRow, $chunkSize);
        $objReader->setReadFilter($chunkFilter);
        $objReader->setReadDataOnly(true);

        $objPHPExcel = $objReader->load($_SERVER['DOCUMENT_ROOT'] . $filePath);
        $sheet = $objPHPExcel->getActiveSheet();
        $maxRows = $sheet->getHighestRow();

        $result = [
            'end' => false,
            'count' => 0
        ];

        if (empty($count) && $maxRows < $chunkSize) {
            $result['end'] = true;
            $result['count'] = $maxRows;
        } else {
            if ($maxRows < $count) {
                $result['end'] = true;
                $result['count'] = $count;
            } else {
                $result['count'] = $maxRows;
            }
        }

        return $result;
    }


    private function getCsvEnclosure ()
    {
        $enclosure = '"';

        switch ($this->baseData['csvEnclosure']) {
            case 'dquote':
                $enclosure = '"';
                break;
            case 'quote':
                $enclosure = '\'';
                break;
            case 'none':
                $enclosure = '';
                break;
        }

        return $enclosure;
    }

    public function getImportFileData ($id = null, $step = 0, $firstRow = false, $checkRowSettings = false, $isTable = false)
    {
        if (!$this->setTemplateAndGetData($id))
            return null;

        if (empty($this->baseData))
            return null;

        if ($checkRowSettings && $this->rowSettingsData['all'] == 'false' && empty($this->rowSettingsData['selected']))
            return null;

        if ($isTable)
            $chunkCount = $this->baseData['step2ShowCount'];
        else
            $chunkCount = $this->baseData['importInStep'];

        if ($firstRow)
            $chunkSize = 1;
        else
            $chunkSize = $chunkCount;

        $startRow = 1;

        if ($step > 1)
            $startRow = ($chunkSize * ($step - 1)) + 1;

        if ($checkRowSettings && $startRow > $this->rowSettingsData['count'] && $this->rowSettingsData['all'] == 'false')
            return null;

        if (!empty($this->baseData['fileId'])) {
            $fileData = CFile::GetFileArray($this->baseData['fileId']);
            $fileType = substr(strrchr($fileData['ORIGINAL_NAME'], "."), 1);
            $filePath = $fileData['SRC'];
        } elseif (!empty($this->baseData['file'])) {
            $fileType = substr(strrchr($this->baseData['file'], "."), 1);
            $filePath = $this->baseData['file'];
        }


        if ($fileType === 'xlsx') {
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        } elseif ($fileType === 'xls') {
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
        } elseif ($fileType === 'csv') {
            $encode = 'UTF-8';
            $encodeFilePath = $_SERVER["DOCUMENT_ROOT"].$filePath;

            if(file_exists($encodeFilePath)) {
                $handle = fopen($encodeFilePath, "r");
                $contents = fread($handle, 260000);
                fclose($handle);

                if(!(CUtil::DetectUTF8($contents))) {
                    if(!function_exists('iconv') || iconv('CP1251', 'CP1251', $contents) == $contents)
                        $encode = 'CP1251';
                }
            }

            $objReader = PHPExcel_IOFactory::createReader('CSV')
                ->setInputEncoding($encode)->setDelimiter($this->baseData['csvDelimiter'])
                ->setEnclosure(self::getCsvEnclosure())
                ->setSheetIndex(0);
        } else {
            return null;
        }


        $chunkFilter = new PHPExcel_ChunkReadFilter();

        $filePath = ImportHelper::convertFilePath($_SERVER['DOCUMENT_ROOT'] . $filePath);

        $chunkFilter->setRows($startRow,$chunkSize);
        $objReader->setReadFilter($chunkFilter);
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filePath);
        $sheet = $objPHPExcel->getActiveSheet();
        $maxCell = $sheet->getHighestRowAndColumn();
        $data = $sheet->rangeToArray('A'. $startRow .':' . $maxCell['column'] . $maxCell['row']);

        unset($sheet, $objPHPExcel, $objReader);


        foreach ($data as &$row) {
            foreach ($row as &$value) {
                $value = Encoding::convertEncodingToCurrent($value);
            }
        }


        return ['data' => $data];
    }

    public function getSelectedRows ($rows, $step, $countInStep, $firstRow = false)
    {
        if (empty($rows) || empty($step))
            return $rows;

        $startRow = ($countInStep * ($step - 1));

        $rowSettings = $this->rowSettingsData;
        $result = [];
        $isEnd = false;
        $counter = -1;

        if ($startRow >= $rowSettings['count']) {
            $isEnd = true;

            if ($rowSettings['all'] == 'true')
                return $rows;
            else
                return [];
        }

        for ($i = $startRow - 1; $i < ($startRow + $countInStep); $i++) {

            if (!empty($rowSettings['selected'][$i])) {
                if (!empty($rows[$counter]))
                    $result[$i] = $rows[$counter];
            }

            if ($i >= $rowSettings['count'] && $rowSettings['all'] !== 'false') {
                if (!empty($rows[$counter]))
                    $result[$i] = $rows[$counter];
            }

            $counter++;
        }

        return $result;
    }
}