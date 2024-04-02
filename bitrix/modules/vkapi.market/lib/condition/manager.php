<?php

namespace VKapi\Market\Condition;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * �������� ����� ��� ������ � ���������
 * Class Manager
 * 
 * @package VKapi\Market\Condition
 */
class Manager
{
    /**
     * ���������� ������ ��� �����
     * 
     * @var string
     */
    protected $rand = '';
    /**
     * ID dom ��������
     * 
     * @var string|null
     */
    private $containerId = null;
    /**
     * @var string �������� ����� � ���������
     */
    private $prefix = 'rule';
    /**
     * @var array ������� ����������� �������
     */
    private $arConditions = [];
    /**
     * �������� ��� ��� ������ ����� � ���������, ���������� �������, �������� ���������� � ��.
     * Manager constructor.
     * 
     * @param null $containerId - ID dom ��������
     * @param null $prefix - �������� ����� � ���������
     */
    public function __construct($conteinerId = null, $prefix = null)
    {
        if (!is_null($conteinerId)) {
            $this->containerId = $conteinerId;
        }
        if (!is_null($prefix)) {
            $this->prefix = $prefix;
        }
    }
    public function addCondition(\VKapi\Market\Condition\Base $condition)
    {
        $this->arConditions[$condition->getType()] = $condition;
    }
    /**
     * ���������� ������ ���������� �������
     * 
     * @return \VKapi\Market\Condition\Base[]
     */
    public function getConditions()
    {
        return $this->arConditions;
    }
    /**
     * ������ ��������������������� ������ �������
     * 
     * @param $type
     * @return \VKapi\Market\Condition\Base|null
     */
    public function getConditionByType($type)
    {
        if (isset($this->arConditions[$type])) {
            return $this->arConditions[$type];
        }
        return null;
    }
    /**
     * ����� ����������� ����� � ���������
     * 
     * @param bool $conditionValues - �������������� ������ � ��������� ����� �������� �������
     * @throws \Bitrix\Main\ArgumentException
     */
    public function show($conditionValues = [])
    {
        \CUtil::InitJSCore('jquery2');
        // ���� ��������� �� ����� ����, �� ��������� ���
        if (is_null($this->containerId)) {
            $this->containerId = 'vkapi-market-condition__area-' . randString('10');
            echo '<div class="vkapi-market-condition__area" id="' . $this->containerId . '"></div>';
        }
        if (!is_array($conditionValues)) {
            $conditionValues = [];
        }
        $arData = ['container' => $this->containerId, 'prefix' => $this->prefix, 'conditions' => $this->getJsConditionsParams(), 'values' => []];
        // ��������� ������������� ��� �������� ������� --------
        if ($conditionValues) {
            $conditionValues = $this->getPrepiredValuesPreview($conditionValues);
        }
        $arData['values'] = $conditionValues;
        ?>
        <script type="text/javascript">
            var params = <?php 
        echo \Bitrix\Main\Web\Json::encode($arData);
        ?>;

            window.VKApiMarketConditionsJs = window.VKApiMarketConditionsJs || {};
            window.VKApiMarketConditionsJs['<?php 
        echo $this->containerId;
        ?>'] = new VKapiMarketConditions(params);
        </script>
        <?php 
    }
    /**
     * C��������� � ������ ������ ������� ������� ������� � ���������� ����� ��� ��������
     * @return array
     */
    public function getJsConditionsParams()
    {
        $arReturn = [];
        // ��������� ������ ������� ������� �������
        foreach ($this->getConditions() as $conditionItem) {
            /**
             * @var \VKapi\Market\Condition\Base $conditionItem
             */
            $arReturn = array_merge($arReturn, $conditionItem->getJsData());
        }
        return $arReturn;
    }
    /**
     * ������ ���������� ������ �� ��������, ��� ������������ ���������� � ���� ������ ��� �� ���� self::show
     * 
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public function parse()
    {
        $arReturn = [];
        $app = \Bitrix\Main\Application::getInstance();
        $req = $app->getContext()->getRequest();
        if ($rules = $req->getPost($this->prefix)) {
            $arRuleIndexToIndex = [];
            foreach ($rules as $ruleIndex => $rule) {
                $indexPath = explode('__', $ruleIndex);
                // ���������� ������, ���������� ---
                if (count($indexPath) <= 0) {
                    continue;
                }
                // ���������� ��� �������, � �������� ��� ��������������������� ������ --
                [$type, $id] = explode(':', $rule['conditionType'], 2);
                $oCondition = $this->getConditionByType($type);
                if (is_null($oCondition)) {
                    continue;
                }
                // ������� �������� --
                if ($parseResult = $oCondition->parse($id, array_diff_key($rule, array_flip(['conditionType'])))) {
                    if (count($indexPath) == 1) {
                        $arReturn[] = $parseResult;
                        $index = count($arReturn) - 1;
                        $arRuleIndexToIndex[$ruleIndex] =& $arReturn[$index];
                    } else {
                        $key = implode('__', array_slice($indexPath, 0, -1));
                        $arRuleIndexToIndex[$key]['childs'][] = $parseResult;
                        $index = count($arRuleIndexToIndex[$key]['childs']) - 1;
                        $arRuleIndexToIndex[$ruleIndex] =& $arRuleIndexToIndex[$key]['childs'][$index];
                    }
                }
            }
            unset($arRuleIndexToIndex, $rules, $ruleIndex, $rule, $indexPath, $index, $key);
        }
        return $arReturn;
    }
    /**
     * ������ ������ �������� � �������������� ��������������, �������� �������� �������� ����������
     * 
     * @param $arConditionValues
     * @return mixed
     */
    public function getPrepiredValuesPreview($arConditionValues)
    {
        if (is_array($arConditionValues)) {
            foreach ($arConditionValues as $valueIndex => $value) {
                if (!isset($value['type'])) {
                    continue;
                }
                $arConditionValues[$valueIndex] = $this->getPrepiredValuePreviewForChild($value);
            }
        }
        return $arConditionValues;
    }
    /**
     * ���������� ������������� �������� ��� ���������� �������
     * 
     * @param $item
     * @return mixed
     */
    protected function getPrepiredValuePreviewForChild($item)
    {
        // ��������
        $oCondition = $this->getConditionByType($item['type']);
        if (!is_null($oCondition)) {
            $item['values'] = $oCondition->getPrepiredValuePreview($item);
        }
        // �������
        if (!empty($item['childs'])) {
            foreach ($item['childs'] as $childIndex => $child) {
                $item['childs'][$childIndex] = $this->getPrepiredValuePreviewForChild($child);
            }
        }
        return $item;
    }
    /**
     * ��������� ������ � ���������
     * 
     * @param $arConditions
     * @return string
     * @internal
     */
    public function getEval($arConditions)
    {
        if (!empty($arResult = \VKapi\Market\Condition\Base::getEvalForChilds($arConditions))) {
            return reset($arResult);
        } else {
            return false;
        }
    }
    /**
     * ��������������� �������� ������������ ������ ��������
     * 
     * @param $arConditions - �������� �������
     * @param $arItem - �������������� ������ �����
     * @return bool|mixed
     */
    public function isMatchCondition($arConditions, $arItem)
    {
        $conditionEval = $this->getEval($arConditions);
        if ($conditionEval) {
            return eval(" return (" . trim($conditionEval) . "); ");
        }
        return true;
    }
    /**
     * ������ �������������� ������ ��������� ������, ��� ��������� �������� �������
     * 
     * @param $arElement - ����� ����� ��������, �� ���������� � ���� PROPERTIES
     * @param bool $bOffer - ���� ���������� �������� �����������
     * @return array
     */
    public function getPreparedElementFields($arElement, $bOffer = false)
    {
        $arReturn = [];
        $arElement['DATE_ACTIVE_FROM'] = $this->getPreparedDateFormatForEval($arElement['DATE_ACTIVE_FROM']);
        $arElement['DATE_ACTIVE_TO'] = $this->getPreparedDateFormatForEval($arElement['DATE_ACTIVE_TO']);
        $arElement['DATE_CREATE'] = $this->getPreparedDateFormatForEval($arElement['DATE_CREATE']);
        $arElement['TIMESTAMP_X'] = $this->getPreparedDateFormatForEval($arElement['TIMESTAMP_X']);
        // ���������� �� ���� ----------------------------
        $arElement['ACTIVE_DATE'] = 'Y';
        $curTime = time();
        if ($arElement['DATE_ACTIVE_FROM'] && $arElement['DATE_ACTIVE_TO']) {
            if ($arElement['DATE_ACTIVE_FROM'] > $curTime && $arElement['DATE_ACTIVE_TO'] < $curTime) {
                $arElement['ACTIVE_DATE'] = 'N';
            }
        } elseif ($arElement['DATE_ACTIVE_FROM'] && $arElement['DATE_ACTIVE_FROM'] > $curTime) {
            $arElement['ACTIVE_DATE'] = 'N';
        } elseif ($arElement['DATE_ACTIVE_TO'] && $arElement['DATE_ACTIVE_TO'] < $curTime) {
            $arElement['ACTIVE_DATE'] = 'N';
        }
        if (isset($arElement['PREVIEW_TEXT'])) {
            $arElement['PREVIEW_TEXT'] = $this->htmlToText($arElement['PREVIEW_TEXT']);
        }
        if (isset($arElement['DETAIL_TEXT'])) {
            $arElement['DETAIL_TEXT'] = $this->htmlToText($arElement['DETAIL_TEXT']);
        }
        foreach ($arElement as $fieldName => $fieldValue) {
            if (substr($fieldName, 0, 1) == '~') {
                continue;
            }
            if (in_array($fieldName, ['PROPERTIES'])) {
                continue;
            }
            // $arReturn[$fieldName] = self::toLowerCase($fieldValue);
            $arReturn[$fieldName] = $fieldValue;
            if (substr($fieldName, 0, 8) === 'CATALOG_') {
                continue;
            }
            $arReturn[$fieldName . '_' . $arElement['IBLOCK_ID']] = $arReturn[$fieldName];
            if (!$bOffer) {
                // $arReturn['PARENT_' . $fieldName] = self::toLowerCase($fieldValue);
                $arReturn['PARENT_' . $fieldName] = $fieldValue;
            }
        }
        foreach ($arElement['PROPERTIES'] as $prop) {
            switch ($prop['PROPERTY_TYPE']) {
                case 'L':
                    // $arReturn['PROPERTY_' . $prop['ID']] = (array)self::toLowerCase($prop['VALUE_ENUM_ID']);
                    $arReturn['PROPERTY_' . $prop['ID']] = (array) $prop['VALUE_ENUM_ID'];
                    break;
                default:
                    // $arReturn['PROPERTY_' . $prop['ID']] = (array)self::toLowerCase($prop['VALUE']);
                    $arReturn['PROPERTY_' . $prop['ID']] = (array) $prop['VALUE'];
            }
        }
        return $arReturn;
    }
    /**
     * �������� � ������� �������� ������ ��� �������� ������
     * 
     * @param array|string $data
     * @return array|string
     */
    public static function toLowerCase($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::toLowerCase($value);
            }
        } elseif (self::isUtf()) {
            $data = mb_strtolower($data);
        } else {
            $data = strtolower($data);
        }
        return $data;
    }
    /**
     * �������� ���� � �����, ��� ���������, � Unix-������� (timestamp)
     * 
     * @param $dateString - ����, �������� 20.09.2017 15:56:51
     * @return int - ������ 1505912211 ��� 0
     */
    protected function getPreparedDateFormatForEval($dateString)
    {
        $result = 0;
        if ($arr = \ParseDateTime($dateString)) {
            return mktime($arr["HH"], $arr["MI"], $arr["SS"], $arr["MM"], $arr["DD"], $arr["YYYY"]);
        }
        return $result;
    }
    /**
     * ������ ���� ��� �����
     * 
     * @param $text
     * @return false|int|string|string[]
     */
    protected function htmlToText($text)
    {
        // ������� html � text
        $text = htmlspecialcharsBack(\HTMLToTxt($text, "", [], false));
        // ������ &nbsp;
        $text = preg_replace('/(&[a-z]+;)/', ' ', $text);
        return $text;
    }
    /**
     * ���������� �������������� ����� ��� �������� ������� �� id ������
     * 
     * @param array $arElementId - ����� ��������������� ���� ��� ������� ����� ����������� � ��������
     * @param bool $bOffer - ������� �� ID �������� �����������
     * @param array $arPriceUserGroupIds - ������ ������������� ��� ������� ������
     * @param string|null $siteId - ���� ��� �������� ������������� ������
     * @return array
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getPreparedElementFieldsById(array $arElementId, $bOffer = false, $arPriceUserGroupIds = [], $siteId = null)
    {
        $arReturn = [];
        $arElementId = array_map('intval', $arElementId);
        $arElementId = array_diff($arElementId, [0]);
        if (!\Bitrix\Main\Loader::includeModule('iblock') || empty($arElementId)) {
            return $arReturn;
        }
        $dbrElement = \CIBlockElement::GetList([], ['ID' => $arElementId], false, false, ['ID', 'IBLOCK_ID', 'CODE', 'XML_ID', 'EXTERNAL_ID', 'NAME', 'ACTIVE', 'SORT', 'PREVIEW_PICTURE', 'PREVIEW_TEXT', 'DETAIL_PICTURE', 'DETAIL_TEXT', 'TAGS', 'CREATED_BY', 'MODIFIED_BY', 'DATE_ACTIVE_FROM', 'DATE_ACTIVE_TO', 'DATE_CREATE', 'TIMESTAMP_X']);
        while ($obElement = $dbrElement->GetNextElement(true, false)) {
            $arElement = $obElement->getFields();
            $arElement['PROPERTIES'] = $obElement->getProperties();
            unset($arElement['IBLOCK_SECTION_ID']);
            $arElement['IBLOCK_SECTION_ID'] = [];
            // ���� �������� ---
            $arElement = array_merge($arElement, \VKapi\Market\Condition\CatalogField::getElementDefaultValues());
            // ���������� ������ ---
            $arElement = $this->getPreparedElementFields($arElement, $bOffer);
            // ������� �������� �� �������� �����������
            $arElement['IS_OFFER'] = $bOffer ? 'Y' : 'N';
            $arReturn[$arElement['ID']] = $arElement;
        }
        // ������� �������� ��������
        $arSectionParentList = $this->getSectionParentList();
        // ������� ---------
        if (!empty($arReturn)) {
            $dbrSection = \Bitrix\Iblock\SectionElementTable::getList(['filter' => ['IBLOCK_ELEMENT_ID' => array_keys($arReturn)]]);
            while ($arSection = $dbrSection->fetch()) {
                $arSectionIdList = [$arSection['IBLOCK_SECTION_ID']];
                if (isset($arSectionParentList[$arSection['IBLOCK_SECTION_ID']])) {
                    $arSectionIdList = [...$arSectionIdList, ...(array) $arSectionParentList[$arSection['IBLOCK_SECTION_ID']]];
                }
                $arReturn[$arSection['IBLOCK_ELEMENT_ID']]['IBLOCK_SECTION_ID'] = [...$arReturn[$arSection['IBLOCK_ELEMENT_ID']]['IBLOCK_SECTION_ID'], ...$arSectionIdList];
                if (!$bOffer) {
                    $arReturn[$arSection['IBLOCK_ELEMENT_ID']]['PARENT_IBLOCK_SECTION_ID'] = [...$arReturn[$arSection['IBLOCK_ELEMENT_ID']]['PARENT_IBLOCK_SECTION_ID'], ...$arSectionIdList];
                }
            }
            // ������� ���������
            foreach ($arReturn as $elementId => &$arElement) {
                $arElement['IBLOCK_SECTION_ID'] = array_values(array_unique($arElement['IBLOCK_SECTION_ID']));
                $arElement['IBLOCK_SECTION_ID_' . $arElement['IBLOCK_ID']] = $arElement['IBLOCK_SECTION_ID'];
                if (!$bOffer) {
                    $arElement['PARENT_IBLOCK_SECTION_ID'] = array_values(array_unique($arElement['PARENT_IBLOCK_SECTION_ID']));
                    $arElement['PARENT_IBLOCK_SECTION_ID_' . $arElement['IBLOCK_ID']] = $arElement['PARENT_IBLOCK_SECTION_ID'];
                }
            }
            unset($elementId, $arElement);
        }
        // �������
        if (self::isInstalledCatalogModule() && count($arReturn)) {
            $dbrProductQuantity = \Bitrix\Catalog\Model\Product::getList(['filter' => ['ID' => array_keys($arReturn)], 'select' => ['ID', 'QUANTITY', 'WEIGHT', 'AVAILABLE']]);
            while ($arProduct = $dbrProductQuantity->fetch()) {
                $arReturn[$arProduct['ID']]['CATALOG_WEIGHT'] = (int) $arProduct['WEIGHT'];
                $arReturn[$arProduct['ID']]['CATALOG_QUANTITY'] = (int) $arProduct['QUANTITY'];
                $arReturn[$arProduct['ID']]['CATALOG_AVAILABLE'] = $arProduct['AVAILABLE'] === 'Y' ? 'Y' : 'N';
            }
            // �� ������� ---------
            if (class_exists('\\CCatalogStoreProduct')) {
                $dbrStoreProduct = \Bitrix\Catalog\StoreProductTable::getList(['filter' => ['=PRODUCT_ID' => array_keys($arReturn)], 'select' => ['PRODUCT_ID', 'STORE_ID', 'AMOUNT']]);
                while ($arStoreProduct = $dbrStoreProduct->fetch()) {
                    $arReturn[$arStoreProduct['PRODUCT_ID']]['CATALOG_STORE_' . $arStoreProduct['STORE_ID']] = intval($arStoreProduct['AMOUNT']);
                }
            }
            // ���� ---------------
            $oCatalogDiscount = new \CCatalogDiscount();
            $oCatalogProduct = new \CCatalogProduct();
            $dbrPrice = \CPrice::GetList([], ["PRODUCT_ID" => array_keys($arReturn)], false, false, ['ID', 'PRODUCT_ID', 'CURRENCY', 'CATALOG_GROUP_ID', 'PRICE']);
            while ($arPrice = $dbrPrice->Fetch()) {
                // ������������ ������ -----
                $arDiscounts = $oCatalogDiscount->GetDiscountByPrice($arPrice["ID"], $arPriceUserGroupIds, "N", $siteId ?? false);
                $discountPrice = (float) $oCatalogProduct->CountPriceWithDiscount($arPrice["PRICE"], $arPrice["CURRENCY"], $arDiscounts);
                // $arReturn[$arPrice['PRODUCT_ID']]['CATALOG_PRICE_' . $arPrice['CATALOG_GROUP_ID']] = preg_replace('/(\.[0]+)$/', '', $arPrice['PRICE']);
                $arReturn[$arPrice['PRODUCT_ID']]['CATALOG_PRICE_' . $arPrice['CATALOG_GROUP_ID']] = (float) $arPrice['PRICE'];
                $arReturn[$arPrice['PRODUCT_ID']]['CATALOG_DISCOUNT_PERCENT_' . $arPrice['CATALOG_GROUP_ID']] = 0;
                $arReturn[$arPrice['PRODUCT_ID']]['CATALOG_DISCOUNT_PRICE_' . $arPrice['CATALOG_GROUP_ID']] = 0;
                if ($discountPrice && $discountPrice < (float) $arPrice['PRICE']) {
                    $arReturn[$arPrice['PRODUCT_ID']]['CATALOG_PRICE_' . $arPrice['CATALOG_GROUP_ID']] = $discountPrice;
                    $arReturn[$arPrice['PRODUCT_ID']]['CATALOG_DISCOUNT_PERCENT_' . $arPrice['CATALOG_GROUP_ID']] = round((float) ($arPrice['PRICE'] - $discountPrice) * 100 / $arPrice['PRICE']);
                    $arReturn[$arPrice['PRODUCT_ID']]['CATALOG_DISCOUNT_PRICE_' . $arPrice['CATALOG_GROUP_ID']] = round($arPrice['PRICE'] - $discountPrice);
                }
                // $arReturn[$arPrice['PRODUCT_ID']]['CATALOG_GROUP_' . $arPrice['CATALOG_GROUP_ID']] = preg_replace(
                // '/(\.[0]+)$/',
                // '',
                // $arPrice['PRICE']
                // );
                $arReturn[$arPrice['PRODUCT_ID']]['CATALOG_GROUP_' . $arPrice['CATALOG_GROUP_ID']] = (float) $arPrice['PRICE'];
            }
            unset($arDiscounts);
        }
        unset($obElement, $dbrElement, $arElementId, $arElement);
        return $arReturn;
    }
    /**
     * ������ ������ ��������������� ������������ �������� ��� ������� �������
     * 
     * @return mixed
     * @throws \Bitrix\Main\LoaderException
     */
    public function getSectionParentList()
    {
        static $arTreeList;
        if (!isset($arTreeList) && self::isInstalledIblockModule()) {
            $dbrSection = \CIBlockSection::GetList(['LEFT_MARGIN' => 'ASC'], [], false, ['ID', 'IBLOCK_SECTION_ID']);
            while ($ar = $dbrSection->fetch()) {
                $arTreeList[$ar['ID']] = [];
                if (!!$ar['IBLOCK_SECTION_ID'] && array_key_exists($ar['IBLOCK_SECTION_ID'], $arTreeList)) {
                    $arTreeList[$ar['ID']] = array_merge([$ar['IBLOCK_SECTION_ID']], $arTreeList[$ar['IBLOCK_SECTION_ID']]);
                }
            }
        }
        return $arTreeList;
    }
    /**
     * ������ ������ ��������������� ������������ �������� ��� ������� �������
     * 
     * @return mixed
     * @throws \Bitrix\Main\LoaderException
     */
    public function getSectionChildsList()
    {
        static $arTreeList;
        if (!isset($arTreeList) && self::isInstalledIblockModule()) {
            $arItem2Parent = [];
            $dbrSection = \CIBlockSection::GetList(['LEFT_MARGIN' => 'ASC'], [], false, ['ID', 'IBLOCK_SECTION_ID']);
            while ($ar = $dbrSection->fetch()) {
                $id = (int) $ar['ID'];
                $arItem2Parent[$id] = (int) $ar['IBLOCK_SECTION_ID'];
                $arTreeList[$id] = [];
                $this->appendToAllParent($id, $arItem2Parent[$id], $arItem2Parent, $arTreeList);
            }
        }
        return $arTreeList;
    }
    /**
     * ��������� ������� ������� �������� �� �����
     * @param $id
     * @param $parentId
     * @param $arItem2Parent
     * @param $arTreeList
     * @return void
     */
    public function appendToAllParent($id, $parentId, &$arItem2Parent, &$arTreeList)
    {
        if ($parentId) {
            // ������� ��������
            $arTreeList[$parentId][] = $id;
            $this->appendToAllParent($id, $arItem2Parent[$parentId], $arItem2Parent, $arTreeList);
        }
    }
    /**
     * ������ ������������� �������� ������� � ���� �������������� ���� ��� ��������
     * @param $sectionId
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public function getSectionListWithChilds($sectionId)
    {
        $arChilds = $this->getSectionChildsList();
        if (!empty($arChilds[$sectionId])) {
            return array_merge([$sectionId], $arChilds[$sectionId]);
        }
        return [$sectionId];
    }
    /**
     * ���������� �� ������ ��������
     * 
     * @return bool
     * @throws \Bitrix\Main\LoaderException
     */
    public static function isInstalledCatalogModule()
    {
        static $bInstalled;
        if (!isset($bInstalled)) {
            $bInstalled = \Bitrix\Main\Loader::includeModule('catalog');
        }
        return $bInstalled;
    }
    /**
     * ���������� �� ������ ����������
     * 
     * @return bool
     * @throws \Bitrix\Main\LoaderException
     */
    public static function isInstalledIblockModule()
    {
        static $bInstalled;
        if (!isset($bInstalled)) {
            $bInstalled = \Bitrix\Main\Loader::includeModule('highloadblock');
        }
        return $bInstalled;
    }
    /**
     * ���������� �� ������ ����������
     * 
     * @return bool
     * @throws \Bitrix\Main\LoaderException
     */
    public static function isInstalledHighloadBlockModule()
    {
        static $bInstalled;
        if (!isset($bInstalled)) {
            $bInstalled = \Bitrix\Main\Loader::includeModule('iblock');
        }
        return $bInstalled;
    }
    /**
     * ��������, �������� �� ������� ���� � ��������� utf-8
     * 
     * @return bool
     */
    public static function isUtf()
    {
        static $flag;
        if (!isset($flag)) {
            $flag = defined('BX_UTF') && BX_UTF === true;
        }
        return $flag;
    }
    /**
     * @param $arConditions
     * @param $iblockId
     * @return \Bitrix\Main\ORM\Query\Filter\ConditionTree
     * @throws \Bitrix\Main\ArgumentException
     */
    public function parseBaseFilter($arConditions, $iblockId)
    {
        $iblockId = (int) $iblockId;
        $filter = \Bitrix\Main\ORM\Query\Query::filter();
        if (empty($arConditions[0])) {
            return $filter;
        }
        $first = $arConditions[0];
        if ($first['id'] !== 'DEFAULT' || $first['type'] !== 'VKapi\\Market\\Condition\\Group') {
            return $filter;
        }
        $filter = \Bitrix\Main\ORM\Query\Query::filter();
        $filter->logic($first['values']['aggregator'] === 'and' ? \Bitrix\Main\ORM\Query\Filter\ConditionTree::LOGIC_AND : \Bitrix\Main\ORM\Query\Filter\ConditionTree::LOGIC_OR);
        $aggregatorAnd = $first['values']['aggregator'] === 'and';
        $aggregatorInverse = $first['values']['type'] === 'false';
        if ($aggregatorInverse || !$aggregatorAnd) {
            return $filter;
        }
        $inSection = [];
        $notInSection = [];
        foreach ($first['childs'] as $child) {
            switch ($child['id']) {
                // ACTIVE_28
                case 'ACTIVE_' . $iblockId:
                    if ($child['type'] !== 'VKapi\\Market\\Condition\\IblockElementField') {
                        break;
                    }
                    if (in_array($child['values']['condition'], ['EQUAL', 'STRICT_EQUAL'])) {
                        $filter->where('ACTIVE', '=', $child['values']['value']);
                    } else {
                        $filter->where('ACTIVE', '!=', $child['values']['value']);
                    }
                    break;
                case 'IBLOCK_SECTION_ID_' . $iblockId:
                    if ($child['type'] !== 'VKapi\\Market\\Condition\\IblockElementField') {
                        break;
                    }
                    if ($child['values']['condition'] === 'EQUAL') {
                        $inSection = array_merge($inSection, $this->getSectionListWithChilds($child['values']['value']));
                    } else {
                        $notInSection = array_merge($notInSection, $this->getSectionListWithChilds($child['values']['value']));
                    }
                    break;
                case 'DEFAULT':
                    if ($child['type'] !== 'VKapi\\Market\\Condition\\Group') {
                        break;
                    }
                    $subFilter = \Bitrix\Main\ORM\Query\Query::filter();
                    $subFilter->logic($child['values']['aggregator'] === 'and' ? \Bitrix\Main\ORM\Query\Filter\ConditionTree::LOGIC_AND : \Bitrix\Main\ORM\Query\Filter\ConditionTree::LOGIC_OR);
                    $subInSection = [];
                    $subNotInSection = [];
                    foreach ($child['childs'] as $subChild) {
                        switch ($subChild['id']) {
                            case 'IBLOCK_SECTION_ID_' . $iblockId:
                                if ($subChild['type'] !== 'VKapi\\Market\\Condition\\IblockElementField') {
                                    break;
                                }
                                if ($subChild['values']['condition'] === 'EQUAL') {
                                    $subInSection = array_merge($subInSection, $this->getSectionListWithChilds($subChild['values']['value']));
                                } else {
                                    $subNotInSection = array_merge($subNotInSection, $this->getSectionListWithChilds($subChild['values']['value']));
                                }
                                break;
                        }
                    }
                    if (count($subInSection)) {
                        $subFilter->whereIn('ELEMENT_SECTION.IBLOCK_SECTION_ID', $subInSection);
                    }
                    if (count($subNotInSection)) {
                        $subFilter->whereNotIn('ELEMENT_SECTION.IBLOCK_SECTION_ID', $subNotInSection);
                    }
                    if (count($subInSection) || count($subNotInSection)) {
                        $filter->where($subFilter);
                    }
            }
        }
        if (count($inSection)) {
            $filter->whereIn('ELEMENT_SECTION.IBLOCK_SECTION_ID', $inSection);
        }
        if (count($notInSection)) {
            $filter->whereNotIn('ELEMENT_SECTION.IBLOCK_SECTION_ID', $notInSection);
        }
        return $filter;
    }
}
?>