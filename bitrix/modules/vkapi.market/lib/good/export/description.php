<?php

namespace VKapi\Market\Good\Export;

use Bitrix\Main\Localization\Loc;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * ����� ��� ���������� �������� ������
 * Class Description
 * 
 * @package VKapi\Market\Good\Export;
 */
class Description
{
    /**
     * @var \VKapi\Market\Export\Item|null
     */
    protected $oExportItem = null;
    /**
     * ������� ������ ��� ��
     */
    const EOL = "\n";
    public function __construct(\VKapi\Market\Export\Item $exportItem)
    {
        $this->oExportItem = $exportItem;
    }
    /**
     * @return \VKapi\Market\Manager
     */
    public function manager()
    {
        return \VKapi\Market\Manager::getInstance();
    }
    /**
     * @return \VKapi\Market\Export\Item
     */
    public function exportItem()
    {
        return $this->oExportItem;
    }
    /**
     * ������� \r\n �� \n
     * @param $text
     * @return string|string[]|null
     */
    public function replaceEOL($text)
    {
        return preg_replace("/\r\n/", "\n", $text);
    }
    /**
     * �������� ����������� {BR}
     * @param $text
     * @return string
     */
    public function removeBrPlaceholder($text)
    {
        return preg_replace('/{BR}/m', self::EOL, $text);
    }
    /**
     * �������� ����������� [....] � ��������
     * @param string $text - ����� � ������� ����� ����� ��� ��������
     * @return string
     */
    public function removeEmptyBlock($text)
    {
        // �������� ������ ����������� ���� [��������� {DEL}] ----
        if (preg_match_all('/(\\[[^\\]]+\\]*)/m', $text, $match)) {
            $match = array_unique($match[1]);
            foreach ($match as $i => $block) {
                // ���� � ����� ���� ����������� ���� {DEL}, ������ �������� ����� ���� �� �������, ������ ����� ���� �������
                if (strpos($block, '{DEL}') !== false) {
                    // ������ ����������� [��������� {DEL}]\n ������� ����������� ������� ������
                    $text = str_replace($block, '__DEL__', $text);
                } elseif (preg_match('/\\{BR\\}\\s*\\]/', $block, $mbr)) {
                    $text = str_replace($block, trim(preg_replace('/\\{BR\\}\\s*\\]/', "{BR}", $block), '[]'), $text);
                } else {
                    $text = str_replace($block, trim($block, '[]'), $text);
                }
            }
        }
        // �������� ���������� ����������� ���� {DEL} ----------
        $text = preg_replace("~(__DEL__[\n]*)~", '', $text);
        $text = str_replace('{DEL}', '', $text);
        return $text;
    }
    /**
     * �������� ��������� �������� � ������������� ��������
     * @param $text
     * @return string
     */
    public function removeDoubleSpace($text)
    {
        $text = str_replace('&nbsp;', ' ', $text);
        $text = preg_replace('/([\\x20]+)/', ' ', $text);
        return $text;
    }
    /**
     * ���������� �������� ������ ��������
     * 
     * @param array $arProductData
     * @return string
     */
    public function getProductText($arProductData)
    {
        $description = $this->exportItem()->getProductTemplate();
        $description = $this->replaceEOL($description);
        if (preg_match_all('/(\\{[^}]+\\})/m', $description, $match)) {
            $arPlaceholders = array_unique($match[1]);
            unset($match);
            [$exportDataTmp, $description, $arProductData, $arPlaceholders] = $this->manager()->sendEvent(\VKapi\Market\Manager::EVENT_ON_BEFORE_PRODUCT_DESCRIPTION, array('arExportData' => $this->exportItem()->getData(), 'template' => $description, 'arData' => $arProductData, 'arPlaceholders' => $arPlaceholders), true);
            unset($exportDataTmp);
            foreach ($arPlaceholders as $placeholder) {
                $code = trim($placeholder, '{}');
                if ($code == 'BR') {
                    // ��������� � �����
                } elseif ($code == 'EMPTY') {
                    // ����� ������
                    $description = str_replace($placeholder, PHP_EOL . PHP_EOL, $description);
                } else {
                    // ���� ��� ������, �� ����������� {DEL}, ����� ������� ����������� ���� [���������: {�������� ���}]
                    // ���� ������������ �����������
                    if (array_key_exists($code, $arProductData) && strlen(trim($arProductData[$code]))) {
                        $description = str_replace($placeholder, trim($arProductData[$code]), $description);
                    } else {
                        // ������ �� ����������� ��� ��������
                        $description = str_replace($placeholder, '{DEL}', $description);
                    }
                }
            }
        }
        $description = $this->removeEmptyBlock($description);
        $description = $this->removeBrPlaceholder($description);
        $description = $this->removeDoubleSpace($description);
        // �������� ������� ��������� �� �����
        $description = trim($description);
        return $description;
    }
    /**
     * ���������� �������� ������ c ��������� �������������
     * 
     * @param array $arProductData - ������ �� ��������� ������
     * @param array $arOfferList - ������ � ������� ������
     * @return string
     */
    public function getOffersText($arProductData, $arOfferList)
    {
        $description = '';
        if ($this->exportItem()->isEnabledOfferCombine() && !$this->exportItem()->isEnabledExtendedGoods()) {
            $description .= $this->getOfferContentBefore($arProductData, $arOfferList);
            foreach ($arOfferList as $arOffer) {
                $description .= $this->getOfferContent($arProductData, $arOffer);
            }
            $description .= $this->getOfferContentAfter($arProductData, $arOfferList);
        } else {
            $description .= $this->getOfferContent($arProductData, reset($arOfferList));
        }
        // ������� ������ �����
        $description = $this->removeEmptyBlock($description);
        $description = $this->removeBrPlaceholder($description);
        $description = $this->removeDoubleSpace($description);
        // �������� ������� ��������� �� �����
        $description = trim($description);
        return $description;
    }
    /**
     * ���������� �������� ������ c ��������� �������������
     * 
     * @param array $arProductData - ������ �� ��������� ������
     * @param array $arOffer - ������ �� �����
     * @return string
     */
    public function getOfferContent($arProductData, $arOffer)
    {
        $description = $this->exportItem()->getOfferTemplate();
        $description = $this->replaceEOL($description);
        // ���� ��� �����x, �� ����������� {DEL},����� ������� ����������� ���� [���������: {�������� ���}]
        if (preg_match_all('/(\\{[^}]+\\})/m', $description, $match)) {
            $arPlaceholders = array_unique($match[1]);
            unset($match);
            [$exportDataTmp, $description, $arProductData, $arPlaceholders, $arOffer] = $this->manager()->sendEvent(\VKapi\Market\Manager::EVENT_ON_BEFORE_OFFER_DESCRIPTION, array('arExportData' => $this->exportItem()->getData(), 'template' => $description, 'arData' => $arProductData, 'arPlaceholders' => $arPlaceholders, 'arOffer' => $arOffer), true);
            unset($exportDataTmp);
            // ������� ������������
            foreach ($arPlaceholders as $placeholder) {
                $code = trim($placeholder, '{}');
                if ($placeholder == '{BR}') {
                    // ��������� � �����
                } elseif ($placeholder == '{EMPTY}') {
                    // ����� ������
                    $description = str_replace($placeholder, self::EOL . self::EOL, $description);
                } else {
                    if (isset($arOffer[$code]) && strlen(trim($arOffer[$code]))) {
                        $description = str_replace($placeholder, trim($arOffer[$code]), $description);
                    } elseif (isset($arProductData[$code]) && strlen(trim($arProductData[$code]))) {
                        $description = str_replace($placeholder, trim($arProductData[$code]), $description);
                    } else {
                        $description = str_replace($placeholder, '{DEL}', $description);
                    }
                }
            }
        }
        return $description;
    }
    /**
     * ���������� �������� ������ c ��������� �������������
     * 
     * @param array $arProductData - ������ �� ��������� ������
     * @param array $arOfferList - ������ � ������� ������
     * @return string
     */
    public function getOfferContentBefore($arProductData, $arOfferList)
    {
        $description = $this->exportItem()->getOfferTemplateBefore();
        $description = $this->replaceEOL($description);
        // ���� ��� �����x, �� ����������� {DEL},����� ������� ����������� ���� [���������: {�������� ���}]
        if (preg_match_all('/(\\{[^}]+\\})/m', $description, $match)) {
            $arPlaceholders = array_unique($match[1]);
            unset($match);
            [$exportDataTmp, $description, $arProductData, $arPlaceholders, $arOfferList] = $this->manager()->sendEvent(\VKapi\Market\Manager::EVENT_ON_BEFORE_OFFER_DESCRIPTION_BEFORE, array('arExportData' => $this->exportItem()->getData(), 'template' => $description, 'arData' => $arProductData, 'arPlaceholders' => $arPlaceholders, 'arOfferList' => $arOfferList), true);
            unset($exportDataTmp);
            // ������� ������������
            foreach ($arPlaceholders as $placeholder) {
                $code = trim($placeholder, '{}');
                if ($placeholder == '{BR}') {
                    // ��������� � �����
                } elseif ($placeholder == '{EMPTY}') {
                    // ����� ������
                    $description = str_replace($placeholder, self::EOL . self::EOL, $description);
                } else {
                    if (isset($arProductData[$code]) && strlen(trim($arProductData[$code]))) {
                        $description = str_replace($placeholder, trim($arProductData[$code]), $description);
                    } else {
                        $description = str_replace($placeholder, '{DEL}', $description);
                    }
                }
            }
        }
        return $description;
    }
    /**
     * ���������� ��������� �������� ������ c ��������� �������������
     * 
     * @param array $arProductData - ������ �� ��������� ������
     * @param array $arOfferList - ������ � ������� ������
     * @return string
     */
    public function getOfferContentAfter($arProductData, $arOfferList)
    {
        $description = $this->exportItem()->getOfferTemplateAfter();
        $description = $this->replaceEOL($description);
        // ���� ��� �����x, �� ����������� {DEL},����� ������� ����������� ���� [���������: {�������� ���}]
        if (preg_match_all('/(\\{[^}]+\\})/m', $description, $match)) {
            $arPlaceholders = array_unique($match[1]);
            unset($match);
            [$exportDataTmp, $description, $arProductData, $arPlaceholders, $arOfferList] = $this->manager()->sendEvent(\VKapi\Market\Manager::EVENT_ON_BEFORE_OFFER_DESCRIPTION_AFTER, array('arExportData' => $this->exportItem()->getData(), 'template' => $description, 'arData' => $arProductData, 'arPlaceholders' => $arPlaceholders, 'arOfferList' => $arOfferList), true);
            unset($exportDataTmp);
            // ������� ������������
            foreach ($arPlaceholders as $placeholder) {
                $code = trim($placeholder, '{}');
                if ($placeholder == '{BR}') {
                    // ��������� � �����
                } elseif ($placeholder == '{EMPTY}') {
                    // ����� ������
                    $description = str_replace($placeholder, self::EOL . self::EOL, $description);
                } else {
                    if (isset($arProductData[$code]) && strlen(trim($arProductData[$code]))) {
                        $description = str_replace($placeholder, trim($arProductData[$code]), $description);
                    } else {
                        $description = str_replace($placeholder, '{DEL}', $description);
                    }
                }
            }
        }
        return $description;
    }
}
?>