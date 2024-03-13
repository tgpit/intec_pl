<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

return function (&$parameters, $id = null, $quantity = null) {
    $result = [];
    $prefix = 'ORDER_FAST_';
    $prefixLength = StringHelper::length($prefix);
    $excluded = [
        'USE',
        'TEMPLATE',
        'SETTINGS_USE',
        'LAZYLOAD_USE',
        'PROPERTY_ARTICLE',
        'OFFERS_PROPERTY_ARTICLE'
    ];

    foreach ($parameters as $key => $value) {
        if (!StringHelper::startsWith($key, $prefix))
            continue;

        $key = StringHelper::cut($key, $prefixLength);

        if (!ArrayHelper::isIn($key, $excluded))
            $result[$key] = $value;
    }

    return [
        'component' => 'intec.universe:sale.order.fast',
        'template' => !empty($parameters['ORDER_FAST_TEMPLATE']) ? $parameters['ORDER_FAST_TEMPLATE'] : '.default',
        'parameters' => ArrayHelper::merge($result, [
            'SETTINGS_USE' => $parameters['SETTINGS_USE'],
            'LAZYLOAD_USE' => $parameters['LAZYLOAD_USE'],
            'PRODUCT' => $id,
            'QUANTITY' => $quantity,
            'CONSENT_URL' => $parameters['CONSENT_URL'],
            'PROPERTY_ARTICLE' => $parameters['PROPERTY_ARTICLE']
        ]),
        'settings' => [
            'parameters' => [
                'width' => null
            ]
        ]
    ];
};