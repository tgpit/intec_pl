<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Type;

return function (&$parameters, $templateId, $name = null) {
    $fields = [];

    if (!empty($parameters['FORM_PROPERTY_PRODUCT']))
        $fields[$parameters['FORM_PROPERTY_PRODUCT']] = $name;

    if (!empty($parameters['FORM_TITLE']))
        $title = $parameters['FORM_TITLE'];
    else
        $title = Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_ORDER_TITLE_DEFAULT');

    return [
        'id' => Type::toInteger($parameters['FORM_ID']),
        'template' => !empty($parameters['FORM_TEMPLATE']) ? $parameters['FORM_TEMPLATE'] : '.default',
        'parameters' => [
            'AJAX_OPTION_ADDITIONAL' => $templateId.'-product-order',
            'CONSENT_URL' => $parameters['CONSENT_URL']
        ],
        'fields' => $fields,
        'settings' => [
            'title' => $title
        ]
    ];
};