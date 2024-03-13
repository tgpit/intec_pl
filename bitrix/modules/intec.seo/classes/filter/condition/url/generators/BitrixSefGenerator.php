<?php
namespace intec\seo\filter\condition\url\generators;

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\seo\filter\condition\FilterHelper;
use intec\seo\filter\condition\url\Generator;
use intec\core\net\Url;

/**
 * Класс, представляющий генератор Url адресов из свойств на основе ЧПУ платформы.
 * Class BitrixSefGenerator
 * @package intec\seo\filter\url\generators
 * @author apocalypsisdimon@gmail.com
 */
class BitrixSefGenerator extends Generator
{
    /**
     * @inheritdoc
     */
    public function getSourceUrl($objects, $iblock, $section)
    {
        $template = $this->sourceTemplate;
        $macros = $this->getMacros($iblock, $section);

        $macros['SMART_FILTER_PATH'] = [];

        foreach ($objects as $object) {
            if (
                $object['TYPE'] === FilterHelper::FILTER_PROPERTY_TYPE_RANGE ||
                $object['TYPE'] === FilterHelper::FILTER_PROPERTY_TYPE_LIST
            ) {
                $key = $object['CODE'];

                if (empty($key))
                    $key = $object['ID'];

                $key = $this->encodeUrlPart(StringHelper::toLowerCase($key, Encoding::getDefault()));

                if ($object['TYPE'] === FilterHelper::FILTER_PROPERTY_TYPE_RANGE) {
                    if (isset($object['VALUES']['minimal']) && isset($object['VALUES']['maximal'])) {
                        if ($object['VALUES']['minimal']['TEXT'] > $object['VALUES']['maximal']['TEXT'])
                            return null;

                        $macros['SMART_FILTER_PATH'][] = $key.'-from-'.$this->encodeUrlPart($object['VALUES']['minimal']['URL']).'-to-'.$this->encodeUrlPart($object['VALUES']['maximal']['URL']);
                    } else if (isset($object['VALUES']['minimal'])) {
                        $macros['SMART_FILTER_PATH'][] = $key.'-from-'.$this->encodeUrlPart($object['VALUES']['minimal']['URL']);
                    } else {
                        $macros['SMART_FILTER_PATH'][] = $key.'-to-'.$this->encodeUrlPart($object['VALUES']['maximal']['URL']);
                    }
                } else {
                    $values = [];

                    foreach ($object['VALUES'] as $value)
                        $values[] = $this->encodeUrlPart($value['URL']);

                    $macros['SMART_FILTER_PATH'][] = $key.'-is-'.implode('-or-', $values);
                }
            } else if ($object['TYPE'] === FilterHelper::FILTER_PROPERTY_TYPE_PRICE) {
                $key = $object['NAME'];
                $key = $this->encodeUrlPart(StringHelper::toLowerCase($key, Encoding::getDefault()));

                if (isset($object['VALUES']['minimal']) && isset($object['VALUES']['maximal'])) {
                    $macros['SMART_FILTER_PATH'][] = 'price-'.$key.'-from-'.$this->encodeUrlPart($object['VALUES']['minimal']).'-to-'.$this->encodeUrlPart($object['VALUES']['maximal']);
                } else if (isset($object['VALUES']['minimal'])) {
                    $macros['SMART_FILTER_PATH'][] = 'price-'.$key.'-from-'.$this->encodeUrlPart($object['VALUES']['minimal']);
                } else {
                    $macros['SMART_FILTER_PATH'][] = 'price-'.$key.'-to-'.$this->encodeUrlPart($object['VALUES']['maximal']);
                }
            }
        }

        $macros['SMART_FILTER_PATH'] = implode('/', $macros['SMART_FILTER_PATH']);
        $url = StringHelper::replaceMacros($template, $macros);

        if (empty($url) && !Type::isNumeric($url))
            $url = null;

        return $url;
    }
}