<?php

namespace intec\core\bitrix\component\parameters;

use Bitrix\Main\Loader;
use intec\core\base\Collection;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

if (!Loader::includeModule('iblock'))
    return;

/**
 * Class Properties
 * @package intec\core\bitrix\component\parameters
 * @deprecated
 */
class Properties extends Collection
{
    const MODE_BOTH = 0;
    const MODE_SINGLE = 1;
    const MODE_MULTIPLE = 2;

    /**
     * Создает коллекцию свойств инфоблока
     * @param array $filter
     * @param array $sort
     * @param string $indexBy
     * @return Properties
     */
    public static function getProperties($filter, $sort = ['SORT' => 'ASC'], $indexBy = 'CODE')
    {
        if (empty($filter) || !Type::isArray($filter)) {
            return new static([]);
        }

        $filter = ArrayHelper::merge([
            'ACTIVE' => 'Y'
        ], $filter);

        if (empty($sort) || !Type::isArray($sort)) {
            $sort = ['SORT' => 'ASC'];
        }

        if (empty($indexBy) || !Type::isString($indexBy)) {
            $indexBy = 'CODE';
        }

        $items = [];
        $result = \CIBlockProperty::GetList($sort, $filter);

        while ($item = $result->GetNext()) {
            $items[$item[$indexBy]] = $item;
        }

        return new static($items);
    }

    /**
     * Выбирает соотвествующие параметрам свойства из колеккции
     * @param string $propertyType
     * @param string $listType
     * @param null|bool|string $userType
     * @param int $mode
     * @return array
     */
    public function getCustom($propertyType, $listType, $userType = null, $mode = self::MODE_BOTH)
    {
        if ($this->isEmpty() || empty($propertyType)) {
            return [];
        }

        return $this->asArray(function ($key, $value) use (&$mode, &$propertyType, &$listType, &$userType) {
            if ($value['PROPERTY_TYPE'] === $propertyType && $value['LIST_TYPE'] === $listType) {
                if ($userType !== false && $value['USER_TYPE'] !== $userType) {
                    return ['skip' => true];
                }

                if ($mode !== self::MODE_BOTH) {
                    if (
                        ($mode === self::MODE_MULTIPLE && $value['MULTIPLE'] !== 'Y') ||
                        ($mode === self::MODE_SINGLE && $value['MULTIPLE'] !== 'N')
                    ) {
                        return ['skip' => true];
                    }
                }

                return [
                    'key' => $key,
                    'value' => '['.$key.'] '.$value['NAME']
                ];
            }

            return ['skip' => true];
        });
    }

    /**
     * Возвращает массив свойств относящихся к текстовым свойствам
     * @param int $mode
     * @return array
     */
    public function getString($mode = self::MODE_BOTH)
    {
        return $this->getCustom('S', 'L', false, $mode);
    }

    /**
     * Возвращает массив свойств типа "Число"
     * @param int $mode
     * @return array
     */
    public function getNumber($mode = self::MODE_BOTH)
    {
        return $this->getCustom('N', 'L', null, $mode);
    }

    /**
     * Возвращает массив свойств типа "Файл"
     * @param int $mode
     * @return array
     */
    public function getFile($mode = self::MODE_BOTH)
    {
        return $this->getCustom('F', 'L', null, $mode);
    }

    /**
     * Возвращает массив свойств типа "Список" внешний вид "список"
     * @param int $mode
     * @return array
     */
    public function getList($mode = self::MODE_BOTH)
    {
        return $this->getCustom('L', 'L', null, $mode);
    }

    /**
     * Возвращает массив свойств типа "Список" внешний вид "флажки"
     * @param int $mode
     * @return array
     */
    public function getCheckbox($mode = self::MODE_BOTH)
    {
        return $this->getCustom('L', 'C', null, $mode);
    }

    /**
     * Возвращает массив свойств типа "Привязка к элементам"
     * @param int $mode
     * @return array
     */
    public function getElement($mode = self::MODE_BOTH)
    {
        return $this->getCustom('E', 'L', null, $mode);
    }

    /**
     * Возвращает массив свойств типа "Привязка к разделам"
     * @param int $mode
     * @return array
     */
    public function getSection($mode = self::MODE_BOTH)
    {
        return $this->getCustom('G', 'L', null, $mode);
    }

    /**
     * Возвращает массив свойств типа "HTML/Текст"
     * @param int $mode
     * @return array
     */
    public function getHtml($mode = self::MODE_BOTH)
    {
        return $this->getCustom('S', 'L', 'HTML', $mode);
    }

    /**
     * Возвращает массив свойств типа "Видео"
     * @param int $mode
     * @return array
     */
    public function getVideo($mode = self::MODE_BOTH)
    {
        return $this->getCustom('S', 'L', 'video', $mode);
    }

    /**
     * Возвращает массив свойств типа "Дата"
     * @param int $mode
     * @return array
     */
    public function getDate($mode = self::MODE_BOTH)
    {
        return $this->getCustom('S', 'L', 'Date', $mode);
    }

    /**
     * Возвращает массив свойств типа "Дата/Время"
     * @param int $mode
     * @return array
     */
    public function getDateTime($mode = self::MODE_BOTH)
    {
        return $this->getCustom('S', 'L', 'DateTime', $mode);
    }

    /**
     * Возвращает массив свойств типа "Деньги"
     * @param int $mode
     * @return array
     */
    public function getMoney($mode = self::MODE_BOTH)
    {
        return $this->getCustom('S', 'L', 'Money', $mode);
    }

    /**
     * Возвращает массив свойств типа "Привязка к Яндекс.Карте"
     * @param int $mode
     * @return array
     */
    public function getYandexMap($mode = self::MODE_BOTH)
    {
        return $this->getCustom('S', 'L', 'map_yandex', $mode);
    }

    /**
     * Возвращает массив свойств типа "Привязка к Google Maps"
     * @param int $mode
     * @return array
     */
    public function getGoogleMap($mode = self::MODE_BOTH)
    {
        return $this->getCustom('S', 'L', 'map_google', $mode);
    }

    /**
     * Возвращает массив свойств типа "Привязка к пользователю"
     * @param int $mode
     * @return array
     */
    public function getUser($mode = self::MODE_BOTH)
    {
        return $this->getCustom('S', 'L', 'UserID', $mode);
    }

    /**
     * Возвращает массив свойств типа "Привязка к разделам с автозаполнением"
     * @param int $mode
     * @return array
     */
    public function getSectionAuto($mode = self::MODE_BOTH)
    {
        return $this->getCustom('G', 'L', 'SectionAuto', $mode);
    }

    /**
     * Возвращает массив свойств типа "Привязка к файлу (на сервере)"
     * @param int $mode
     * @return array
     */
    public function getFileMan($mode = self::MODE_BOTH)
    {
        return $this->getCustom('S', 'L', 'FileMan', $mode);
    }

    /**
     * Возвращает массив свойств типа "Привязка к элементам в виде списка"
     * @param int $mode
     * @return array
     */
    public function getEList($mode = self::MODE_BOTH)
    {
        return $this->getCustom('E', 'L', 'EList', $mode);
    }

    /**
     * Возвращает массив свойств типа "Привязка к элементам по XML_ID"
     * @param int $mode
     * @return array
     */
    public function getElementXmlID($mode = self::MODE_BOTH)
    {
        return $this->getCustom('S', 'L', 'ElementXmlID', $mode);
    }

    /**
     * Возвращает массив свойств типа "Справочник"
     * @param int $mode
     * @return array
     */
    public function getEAutocomplete($mode = self::MODE_BOTH)
    {
        return $this->getCustom('S', 'L', 'directory', $mode);
    }

    /**
     * Возвращает массив свойств типа "Счетчик"
     * @param int $mode
     * @return array
     */
    public function getSequence($mode = self::MODE_BOTH)
    {
        return $this->getCustom('N', 'L', 'Sequence', $mode);
    }
}