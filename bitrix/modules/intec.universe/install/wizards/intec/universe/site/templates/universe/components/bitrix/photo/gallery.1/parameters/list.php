<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 * @var string $siteTemplate
 */

$prefix = 'LIST_';
$template = 'gallery.';
$templateLength = StringHelper::length($template);

$templates = Arrays::from(CComponentUtil::GetTemplatesList('bitrix:photo.section'))
    ->asArray(function ($key, $value) use (&$template, &$templateLength) {
        if (!StringHelper::startsWith($value['NAME'], $template))
            return ['skip' => true];

        $value['NAME'] = StringHelper::cut($value['NAME'], $templateLength);

        return [
            'key' => $value['NAME'],
            'value' => $value['NAME']
        ];
    });

$arTemplateParameters['LIST_TEMPLATE'] = [
    'PARENT' => 'LIST_SETTINGS',
    'NAME' => Loc::getMessage('C_PHOTO_GALLERY_1_LIST_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $templates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['LIST_TEMPLATE']) && ArrayHelper::keyExists($arCurrentValues['LIST_TEMPLATE'], $templates)) {
    $excluded = [
        'SETTINGS_USE',
        'LAZYLOAD_USE'
    ];

    $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
        'bitrix:photo.section',
        $template.$arCurrentValues['LIST_TEMPLATE'],
        $siteTemplate,
        $arCurrentValues,
        $prefix,
        function ($key, &$parameter) use (&$excluded) {
            if (ArrayHelper::isIn($key, $excluded) || $parameter['HIDDEN'] === 'Y')
                return false;

            $parameter['PARENT'] = 'LIST_SETTINGS';
            $parameter['NAME'] = Loc::getMessage('C_PHOTO_GALLERY_1_LIST_CAPTION').' '.$parameter['NAME'];

            return true;
        },
        Component::PARAMETERS_MODE_TEMPLATE
    ));

    unset($excluded);
}

unset($prefix, $template, $templateLength, $templates);