<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arCurrentValues
 */

$templatePrefix = 'reviews.';
$templatePrefixLength = StringHelper::length($templatePrefix);

$templates = Arrays::from(CComponentUtil::GetTemplatesList('bitrix:news.list', $siteTemplate))
    ->indexBy('NAME')
    ->asArray(function ($key, $value) use (&$templatePrefix, &$templatePrefixLength) {
        if (!StringHelper::startsWith($value['NAME'], $templatePrefix))
            return['skip' => true];

        $name = StringHelper::cut($value['NAME'], $templatePrefixLength);

        return [
            'key' => $name,
            'value' => '['.$value['TEMPLATE'].'] '.$name
        ];
    });

$arTemplateParameters['LIST_TEMPLATE'] = [
    'PARENT' => 'LIST_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_REVIEWS_1_LIST_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $templates,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (empty($arCurrentValues['LIST_TEMPLATE']) || !ArrayHelper::keyExists($arCurrentValues['LIST_TEMPLATE'], $templates))
    return;

$commonParameters = [
    'DATE_FORMAT',
    'SETTINGS_USE',
    'LAZYLOAD_USE',
    'VIDEO_IBLOCK_TYPE',
    'VIDEO_IBLOCK_ID',
    'STAFF_IBLOCK_TYPE',
    'STAFF_IBLOCK_ID',
    'PROPERTY_INFORMATION',
    'PROPERTY_RATING',
    'PROPERTY_VIDEO',
    'VIDEO_PROPERTY_URL',
    'PROPERTY_PICTURES',
    'PROPERTY_FILES',
    'PROPERTY_STAFF',
    'STAFF_PROPERTY_POSITION'
];

$arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
    'bitrix:news.list',
    $templatePrefix.$arCurrentValues['LIST_TEMPLATE'],
    $siteTemplate,
    $arCurrentValues,
    'LIST_',
    function ($key, &$parameter) use (&$commonParameters) {
        if (ArrayHelper::isIn($key, $commonParameters))
            return false;

        $parameter['PARENT'] = 'LIST_SETTINGS';
        $parameter['NAME'] = Loc::getMessage('C_NEWS_STAFF_1_LIST').' '.$parameter['NAME'];

        return true;
    },
    Component::PARAMETERS_MODE_TEMPLATE
));