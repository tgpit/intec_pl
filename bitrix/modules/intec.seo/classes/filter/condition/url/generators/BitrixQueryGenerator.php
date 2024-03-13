<?php
namespace intec\seo\filter\condition\url\generators;

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\net\Url;
use intec\seo\filter\condition\FilterHelper;
use intec\seo\filter\condition\url\Generator;

/**
 * Класс, представляющий генератор Url адресов из свойств на основе параметров запроса.
 * Class BitrixSefGenerator
 * @package intec\seo\filter\url\generators
 * @author apocalypsisdimon@gmail.com
 */
class BitrixQueryGenerator extends Generator
{
    /**
     * @inheritdoc
     */
    public function getSourceUrl($objects, $iblock, $section)
    {
        $template = $this->sourceTemplate;
        $macros = $this->getMacros($iblock, $section);

        $macros['SMART_FILTER_PATH'] = [];
        $macros['SMART_FILTER_PATH'][] = 'set_filter=y';

        foreach ($objects as $object) {
            if (
                $object['TYPE'] === FilterHelper::FILTER_PROPERTY_TYPE_RANGE ||
                $object['TYPE'] === FilterHelper::FILTER_PROPERTY_TYPE_LIST
            ) {
                $key = $this->encodeUrlPart(StringHelper::toLowerCase($object['ID'], Encoding::getDefault()));

                if ($object['TYPE'] === FilterHelper::FILTER_PROPERTY_TYPE_RANGE) {
                    if (isset($object['VALUES']['minimal']) && isset($object['VALUES']['maximal']))
                        if ($object['VALUES']['minimal']['TEXT'] > $object['VALUES']['maximal']['TEXT'])
                            return null;

                    if (isset($object['VALUES']['minimal']))
                        $macros['SMART_FILTER_PATH'][] = 'arrFilter_'.$key.'_MIN='.$this->encodeUrlPart($object['VALUES']['minimal']['URL']);

                    if (isset($object['VALUES']['maximal']))
                        $macros['SMART_FILTER_PATH'][] = 'arrFilter_'.$key.'_MAX='.$this->encodeUrlPart($object['VALUES']['maximal']['URL']);
                } else {
                    foreach ($object['VALUES'] as $value)
                        $macros['SMART_FILTER_PATH'][] = 'arrFilter_'.$key.'_'.$value['HASH'].'=Y';
                }
            }
        }

        foreach ($objects as $object) {
            if ($object['TYPE'] === FilterHelper::FILTER_PROPERTY_TYPE_PRICE) {
                $key = 'P'.$object['ID'];

                if (isset($object['VALUES']['minimal']) && isset($object['VALUES']['maximal']))
                    if ($object['VALUES']['minimal'] > $object['VALUES']['maximal'])
                        return null;

                if (isset($object['VALUES']['minimal']))
                    $macros['SMART_FILTER_PATH'][] = 'arrFilter_'.$key.'_MIN='.$this->encodeUrlPart($object['VALUES']['minimal']);

                if (isset($object['VALUES']['maximal']))
                    $macros['SMART_FILTER_PATH'][] = 'arrFilter_'.$key.'_MAX='.$this->encodeUrlPart($object['VALUES']['maximal']);
            }
        }

        $macros['SMART_FILTER_PATH'] = implode('&', $macros['SMART_FILTER_PATH']);
        $url = StringHelper::replaceMacros($template, $macros);

        if (empty($url) && !Type::isNumeric($url))
            $url = null;

        return $url;
    }
}