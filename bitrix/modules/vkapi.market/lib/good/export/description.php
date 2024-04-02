<?php

namespace VKapi\Market\Good\Export;

use Bitrix\Main\Localization\Loc;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * Класс для подготовки описания товара
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
     * Перевод строки для вк
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
     * Заменит \r\n на \n
     * @param $text
     * @return string|string[]|null
     */
    public function replaceEOL($text)
    {
        return preg_replace("/\r\n/", "\n", $text);
    }
    /**
     * Удаление конструкций {BR}
     * @param $text
     * @return string
     */
    public function removeBrPlaceholder($text)
    {
        return preg_replace('/{BR}/m', self::EOL, $text);
    }
    /**
     * Удаление конструкций [....] в шаблонах
     * @param string $text - текст в котором изщем блоки для удаления
     * @return string
     */
    public function removeEmptyBlock($text)
    {
        // удаление лишних конструкций вида [Заголовок {DEL}] ----
        if (preg_match_all('/(\\[[^\\]]+\\]*)/m', $text, $match)) {
            $match = array_unique($match[1]);
            foreach ($match as $i => $block) {
                // если в блоке есть конструкция вида {DEL}, значит значение пусто либо не найдено, значит такой блок удаляем
                if (strpos($block, '{DEL}') !== false) {
                    // удалит конструкцию [Заголовок {DEL}]\n включая последующей перенос строки
                    $text = str_replace($block, '__DEL__', $text);
                } elseif (preg_match('/\\{BR\\}\\s*\\]/', $block, $mbr)) {
                    $text = str_replace($block, trim(preg_replace('/\\{BR\\}\\s*\\]/', "{BR}", $block), '[]'), $text);
                } else {
                    $text = str_replace($block, trim($block, '[]'), $text);
                }
            }
        }
        // удаление оставшихся конструкций вида {DEL} ----------
        $text = preg_replace("~(__DEL__[\n]*)~", '', $text);
        $text = str_replace('{DEL}', '', $text);
        return $text;
    }
    /**
     * Удаление повторных пробелов и непереносимых пробелов
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
     * Возвращает описание товара простого
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
                    // обработка в конце
                } elseif ($code == 'EMPTY') {
                    // путая строка
                    $description = str_replace($placeholder, PHP_EOL . PHP_EOL, $description);
                } else {
                    // если нет данных, то подставляем {DEL}, чтобы удалить конструкции вида [Заголовок: {значение нет}]
                    // если существующая конструкция
                    if (array_key_exists($code, $arProductData) && strlen(trim($arProductData[$code]))) {
                        $description = str_replace($placeholder, trim($arProductData[$code]), $description);
                    } else {
                        // замена на плэйсхолдер для удаления
                        $description = str_replace($placeholder, '{DEL}', $description);
                    }
                }
            }
        }
        $description = $this->removeEmptyBlock($description);
        $description = $this->removeBrPlaceholder($description);
        $description = $this->removeDoubleSpace($description);
        // обрезаем пробелы прееновсы по краям
        $description = trim($description);
        return $description;
    }
    /**
     * Возвращает описание товара c торговыми предложениями
     * 
     * @param array $arProductData - данные по основному товару
     * @param array $arOfferList - Массив с данными оферов
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
        // удаляем пустые блоки
        $description = $this->removeEmptyBlock($description);
        $description = $this->removeBrPlaceholder($description);
        $description = $this->removeDoubleSpace($description);
        // обрезаем пробелы прееновсы по краям
        $description = trim($description);
        return $description;
    }
    /**
     * Возвращает описание товара c торговыми предложениями
     * 
     * @param array $arProductData - данные по основному товару
     * @param array $arOffer - данные по оферу
     * @return string
     */
    public function getOfferContent($arProductData, $arOffer)
    {
        $description = $this->exportItem()->getOfferTemplate();
        $description = $this->replaceEOL($description);
        // если нет данныx, то подставляем {DEL},чтобы удалить конструкции вида [Заголовок: {значение нет}]
        if (preg_match_all('/(\\{[^}]+\\})/m', $description, $match)) {
            $arPlaceholders = array_unique($match[1]);
            unset($match);
            [$exportDataTmp, $description, $arProductData, $arPlaceholders, $arOffer] = $this->manager()->sendEvent(\VKapi\Market\Manager::EVENT_ON_BEFORE_OFFER_DESCRIPTION, array('arExportData' => $this->exportItem()->getData(), 'template' => $description, 'arData' => $arProductData, 'arPlaceholders' => $arPlaceholders, 'arOffer' => $arOffer), true);
            unset($exportDataTmp);
            // обходим плэйсхолдеры
            foreach ($arPlaceholders as $placeholder) {
                $code = trim($placeholder, '{}');
                if ($placeholder == '{BR}') {
                    // обработка в конце
                } elseif ($placeholder == '{EMPTY}') {
                    // путая строка
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
     * Возвращает описание товара c торговыми предложениями
     * 
     * @param array $arProductData - данные по основному товару
     * @param array $arOfferList - Массив с данными оферов
     * @return string
     */
    public function getOfferContentBefore($arProductData, $arOfferList)
    {
        $description = $this->exportItem()->getOfferTemplateBefore();
        $description = $this->replaceEOL($description);
        // если нет данныx, то подставляем {DEL},чтобы удалить конструкции вида [Заголовок: {значение нет}]
        if (preg_match_all('/(\\{[^}]+\\})/m', $description, $match)) {
            $arPlaceholders = array_unique($match[1]);
            unset($match);
            [$exportDataTmp, $description, $arProductData, $arPlaceholders, $arOfferList] = $this->manager()->sendEvent(\VKapi\Market\Manager::EVENT_ON_BEFORE_OFFER_DESCRIPTION_BEFORE, array('arExportData' => $this->exportItem()->getData(), 'template' => $description, 'arData' => $arProductData, 'arPlaceholders' => $arPlaceholders, 'arOfferList' => $arOfferList), true);
            unset($exportDataTmp);
            // обходим плэйсхолдеры
            foreach ($arPlaceholders as $placeholder) {
                $code = trim($placeholder, '{}');
                if ($placeholder == '{BR}') {
                    // обработка в конце
                } elseif ($placeholder == '{EMPTY}') {
                    // путая строка
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
     * Возвращает окончание описания товара c торговыми предложениями
     * 
     * @param array $arProductData - данные по основному товару
     * @param array $arOfferList - Массив с данными оферов
     * @return string
     */
    public function getOfferContentAfter($arProductData, $arOfferList)
    {
        $description = $this->exportItem()->getOfferTemplateAfter();
        $description = $this->replaceEOL($description);
        // если нет данныx, то подставляем {DEL},чтобы удалить конструкции вида [Заголовок: {значение нет}]
        if (preg_match_all('/(\\{[^}]+\\})/m', $description, $match)) {
            $arPlaceholders = array_unique($match[1]);
            unset($match);
            [$exportDataTmp, $description, $arProductData, $arPlaceholders, $arOfferList] = $this->manager()->sendEvent(\VKapi\Market\Manager::EVENT_ON_BEFORE_OFFER_DESCRIPTION_AFTER, array('arExportData' => $this->exportItem()->getData(), 'template' => $description, 'arData' => $arProductData, 'arPlaceholders' => $arPlaceholders, 'arOfferList' => $arOfferList), true);
            unset($exportDataTmp);
            // обходим плэйсхолдеры
            foreach ($arPlaceholders as $placeholder) {
                $code = trim($placeholder, '{}');
                if ($placeholder == '{BR}') {
                    // обработка в конце
                } elseif ($placeholder == '{EMPTY}') {
                    // путая строка
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