<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

return function (&$parameters, &$item = []) {
    $result = [];
    $prefix = 'SECTION_TIMER_';
    $prefixLength = StringHelper::length($prefix);
    $excluded = [
        'SHOW',
        'TEMPLATE',
        'IBLOCK_TYPE',
        'IBLOCK_ID',
        'SETTINGS_USE',
        'LAZYLOAD_USE',
        'ELEMENT_ID_INTRODUCE',
        'ELEMENT_ID'
    ];

    foreach ($parameters as $key => $value) {
        if (!StringHelper::startsWith($key, $prefix))
            continue;

        $key = StringHelper::cut($key, $prefixLength);

        if (!ArrayHelper::isIn($key, $excluded))
            $result[$key] = $value;
    }

    $quantityShow = !empty($item['OFFERS']) ? 'N' : $result['TIMER_QUANTITY_SHOW'];

    return [
        'component' => 'intec.universe:product.timer',
        'template' => !empty($parameters['TIMER_TEMPLATE']) ? $parameters['TIMER_TEMPLATE'] : '.default',
        'parameters' => ArrayHelper::merge($result, [
            'IBLOCK_TYPE' => $parameters['IBLOCK_TYPE'],
            'IBLOCK_ID' => $parameters['IBLOCK_ID'],
            'SETTINGS_USE' => $parameters['SETTINGS_USE'],
            'LAZYLOAD_USE' => $parameters['LAZYLOAD_USE'],
            'ELEMENT_ID_INTRODUCE' => 'Y',
            'ELEMENT_ID' => $item['ID'],
            'TIMER_QUANTITY_SHOW' => $quantityShow,
            'RANDOMIZE_ID' => 'Y'
        ])
    ];
};