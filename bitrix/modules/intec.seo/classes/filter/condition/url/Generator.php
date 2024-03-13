<?php
namespace intec\seo\filter\condition\url;

use CUtil;
use CIBlockSection;
use intec\core\base\Component;
use intec\core\base\Condition;
use intec\core\base\condition\DataProviderResult;
use intec\core\base\condition\providers\ClosureDataProvider;
use intec\core\base\conditions\GroupCondition;
use intec\core\base\InvalidParamException;
use intec\core\base\WriterInterface;
use intec\core\bitrix\conditions\IBlockSectionCondition;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\net\Url;
use intec\seo\filter\condition\CombinationsHelper;
use intec\seo\filter\condition\FilterHelper;
use intec\seo\models\filter\Url as FilterUrl;

/**
 * Класс, представляющий базу для генератора Url адресов из свойств.
 * Class Generator
 * @package intec\seo\filter\url
 * @author apocalypsisdimon@gmail.com
 */
abstract class Generator extends Component
{
    /**
     * Макросы.
     * @var array|null
     */
    public $macros = [];
    /**
     * Исходный шаблон адреса.
     * @var string|null
     */
    public $sourceTemplate;
    /**
     * Целевой шаблон адреса.
     * @var string|null
     */
    public $targetTemplate;
    /**
     * Производить транслитерацию Url частей.
     * @var boolean
     */
    public $transliterationUse;
    /**
     * На какой символ заменять пробел при транслитерации.
     * @var string
     */
    public $transliterationSpaceCharacter = '_';
    /**
     * На какой символ заменять другие символы при транслитерации.
     * @var string
     */
    public $transliterationOtherCharacter = '_';

    /**
     * Возвращает базовые макросы.
     * @param array $iblock
     * @param array $section
     * @return array
     */
    public function getMacros($iblock, $section)
    {
        return ArrayHelper::merge([
            'IBLOCK_ID' => $iblock['ID'],
            'IBLOCK_CODE' => $iblock['CODE'],
            'IBLOCK_TYPE_ID' => $iblock['IBLOCK_TYPE_ID'],
            'IBLOCK_EXTERNAL_ID' => $iblock['EXTERNAL_ID'],
            'ID' => $section['ID'],
            'CODE' => $section['CODE'],
            'SECTION_ID' => $section['ID'],
            'SECTION_CODE' => $section['CODE'],
            'SECTION_CODE_PATH' => $section['CODE_PATH'],
            'EXTERNAL_ID' => $section['EXTERNAL_ID']
        ], $this->macros);
    }

    /**
     * Преобразует часть Url адреса в соответствии с настройками генератора.
     * @param string $url Часть адреса.
     * @return string
     */
    protected function prepareUrlPart($url)
    {
        if ($this->transliterationUse)
            $url = CUtil::translit($url, 'ru', [
                'max_len' => 100000,
                'change_case' => false,
                'replace_space' => $this->transliterationSpaceCharacter,
                'replace_other' => $this->transliterationOtherCharacter,
                'delete_repeat_replace' => true
            ]);

        return $this->encodeUrlPart($url);
    }

    /**
     * Кодирует часть Url адреса.
     * @param string $url Часть адреса.
     * @return string
     */
    protected function encodeUrlPart($url)
    {
        $url = Encoding::convert($url, Encoding::UTF8, Encoding::getDefault());
        $url = Url::encode($url, true);

        return $url;
    }

    /**
     * Возвращает исходный Url адрес для комбинации свойств.
     * @param array $objects
     * @param array $iblock
     * @param array $section
     * @return mixed
     */
    public abstract function getSourceUrl($objects, $iblock, $section);

    /**
     * Возвращает целевой Url адрес из комбинации свойств.
     * @param array $objects
     * @param array $iblock
     * @param array $section
     * @return null
     */
    public function getTargetUrl($objects, $iblock, $section)
    {
        $template = $this->targetTemplate;
        $macros = $this->getMacros($iblock, $section);
        $matches = [];

        if (preg_match_all('/{([^{}]+):([^{}]*):([^{}]*)}/', $template, $matches, PREG_SET_ORDER)) {
            $patterns = [];

            foreach ($matches as $match) {
                $replacing = [];

                if (empty($match[2]) && !Type::isNumeric($match[2]))
                    $match[2] = '/';

                if (empty($match[3]) && !Type::isNumeric($match[3]))
                    $match[3] = '-';

                foreach ($objects as $object) {
                    if ($object['TYPE'] === FilterHelper::FILTER_PROPERTY_TYPE_LIST) {
                        $values = [
                            'PROPERTY_ID' => $object['ID'],
                            'PROPERTY_CODE' => $object['CODE'],
                            'PROPERTY_VALUE' => []
                        ];

                        foreach ($object['VALUES'] as $value) {
                            $values['PROPERTY_VALUE'][] = StringHelper::toLowerCase($this->prepareUrlPart($value['TEXT'])); /* было $value['URL'] */
                        }

                        $values['PROPERTY_VALUE'] = implode($match[3], $values['PROPERTY_VALUE']);
                        $values['PROPERTY_ID'] = $this->encodeUrlPart(StringHelper::toLowerCase($values['PROPERTY_ID'], Encoding::getDefault()));

                        if (!empty($values['PROPERTY_CODE']) || Type::isNumeric($values['PROPERTY_CODE']))
                            $values['PROPERTY_CODE'] = $this->encodeUrlPart(StringHelper::toLowerCase($values['PROPERTY_CODE'], Encoding::getDefault()));

                        $replacing[] = StringHelper::replaceMacros($match[1], $values);
                    }
                }

                $replacing = implode($match[2], $replacing);
                $patterns[$match[0]] = $replacing;
            }

            $template = StringHelper::replace($template, $patterns);
        }

        $macros['RANGES'] = [];
        $macros['PRICES'] = [];

        foreach ($objects as $object) {
            if ($object['TYPE'] === FilterHelper::FILTER_PROPERTY_TYPE_RANGE) {
                $key = $object['CODE'];

                if (empty($key))
                    $key = $object['ID'];

                $key = $this->encodeUrlPart(StringHelper::toLowerCase($key, Encoding::getDefault()));

                if (isset($object['VALUES']['minimal']) && isset($object['VALUES']['maximal'])) {
                    if ($object['VALUES']['minimal']['TEXT'] > $object['VALUES']['maximal']['TEXT'])
                        return null;

                    $macros['RANGES'][] = $key.'-from-'.$this->prepareUrlPart($object['VALUES']['minimal']['URL']).'-to-'.$this->prepareUrlPart($object['VALUES']['maximal']['URL']);
                } else if (isset($object['VALUES']['minimal'])) {
                    $macros['RANGES'][] = $key.'-from-'.$this->prepareUrlPart($object['VALUES']['minimal']['URL']);
                } else {
                    $macros['RANGES'][] = $key.'-to-'.$this->prepareUrlPart($object['VALUES']['maximal']['URL']);
                }
            } else if ($object['TYPE'] === FilterHelper::FILTER_PROPERTY_TYPE_PRICE) {
                $key = $object['NAME'];
                $key = $this->encodeUrlPart(StringHelper::toLowerCase($key, Encoding::getDefault()));

                if (isset($object['VALUES']['minimal']) && isset($object['VALUES']['maximal'])) {
                    $macros['PRICES'][] = 'price-'.$key.'-from-'.$this->encodeUrlPart($object['VALUES']['minimal']).'-to-'.$this->encodeUrlPart($object['VALUES']['maximal']);
                } else if (isset($object['VALUES']['minimal'])) {
                    $macros['PRICES'][] = 'price-'.$key.'-from-'.$this->encodeUrlPart($object['VALUES']['minimal']);
                } else {
                    $macros['PRICES'][] = 'price-'.$key.'-to-'.$this->encodeUrlPart($object['VALUES']['maximal']);
                }
            }
        }

        $macros['RANGES'] = implode('/', $macros['RANGES']);
        $macros['PRICES'] = implode('/', $macros['PRICES']);
        $url = StringHelper::replaceMacros($template, $macros);

        if (empty($url) && !Type::isNumeric($url))
            $url = null;

        return $url;
    }

    /**
     * Генерирует адрес из комбинации.
     * @param array $combination
     * @param array $iblock
     * @param array $section
     * @return FilterUrl|null
     */
    public function generate($combination, $iblock, $section)
    {
        $properties = FilterHelper::getFilterObjects($combination, true);

        /** Если подходящих объектов не найдено, то уходим */
        if (empty($properties) || empty($iblock) || empty($section))
            return null;

        if (empty($section['CODE_PATH']))
            $section['CODE_PATH'] = CIBlockSection::getSectionCodePath($section['ID']);

        /** Создаем новый экземпляр Url */
        $url = new FilterUrl();
        /** Генерируем исходный адрес */
        $url->source = $this->getSourceUrl($properties, $iblock, $section);

        /** Если исходный адрес пуст, уходим */
        if ($url->source === null)
            return null;

        /** Генерируем целевой адрес */
        $url->target = $this->getTargetUrl($properties, $iblock, $section);

        /** Если целевой адрес пуст, уходим */
        if ($url->target === null)
            return null;

        return $url;
    }

    /**
     * Генерирует несколько адресов из комбинаций.
     * @param array $combinations
     * @param WriterInterface $writer
     * @param array $iblock
     * @param array $sections
     * @param boolean $recursive
     * @throws InvalidParamException
     */
    public function generateBatchByCombinations($combinations, $writer, $iblock, $sections = [], $recursive = true)
    {
        if (
            empty($combinations) ||
            empty($iblock) ||
            empty($sections)
        ) return;

        if (!($writer instanceof WriterInterface))
            throw new InvalidParamException('Writer is not implements WriterInterface');

        /** Если комбинации не нормализованы, т.е. есть пустые значения у условий, нормализуем их (заполнить значениями или если нет значений - удалить комбинацию) */
        if (!CombinationsHelper::combinationsIsNormalized($combinations))
            $combinations = CombinationsHelper::normalizeCombinations($combinations, $iblock, $sections, $recursive);

        /** Получаем комбинации фильтра из обычных комбинаций (генерация значений фильтра с хешами) */
        $combinations = FilterHelper::getCombinations($combinations, true);

        if (empty($combinations))
            return;

        /** Идем по разделам */
        foreach ($sections as $section) {
            /** Устанавливаем путь из разделов */
            $section['CODE_PATH'] = CIBlockSection::getSectionCodePath($section['ID']);

            /** Указываем провайдер для проверки текущего раздела */
            $provider = new ClosureDataProvider(function ($condition) use (&$section) {
                if ($condition instanceof IBlockSectionCondition) {
                    return new DataProviderResult($section['ID']);
                }

                return new DataProviderResult(null);
            });

            /** Идем по комбинациям */
            foreach ($combinations as $combination) {
                $valid = true;

                /** Комбинация для генерации (без условий по разделам) */
                $generationCombination = [];

                /** Проверка принадлежности комбинации к разделу */
                foreach ($combination as $object)
                    if ($object['CONDITION'] instanceof IBlockSectionCondition) {
                        if (!$object['CONDITION']->getIsFulfilled($provider)) {
                            $valid = false;
                            break;
                        }
                    } else {
                        $generationCombination[] = $object;
                    }

                /** Если не принадлежит к разделу */
                if (!$valid)
                    continue;

                /** Генерируем Url */
                $url = $this->generate($generationCombination, $iblock, $section);

                /** Если не пустой, отправляем в механизм записи */
                if (!empty($url))
                    $writer->write($url, $generationCombination, $iblock, $section);
            }
        }
    }

    /**
     * Генерирует несколько адресов из условия.
     * @param Condition $condition
     * @param WriterInterface $writer
     * @param array $iblock
     * @param array $sections
     * @param boolean $recursive
     * @throws InvalidParamException
     */
    public function generateBatchByCondition($condition, $writer, $iblock, $sections = [], $recursive = true)
    {
        $combinations = [];

        /** Преобразуем в список комбинаций */
        if ($condition instanceof GroupCondition) {
            $combinations = $condition->getCombinations();
        } else {
            $combinations[] = [
                $condition
            ];
        }

        /** Вызываем генерацию из комбинации */
        $this->generateBatchByCombinations($combinations, $writer, $iblock, $sections, $recursive);
    }
}