<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

/**
 * @var array $arCurrentValues
 */

$arParameters = array();
$arParameters['CACHE_TIME'] = array();

$arComponentParameters = array(
    'GROUPS' => array(
        'FILTER_SETTINGS' => array(
            'NAME' => GetMessage('C_WIDGET_GROUP_FILTER'),
            'SORT' => 200
        ),
        'LIST_SETTINGS' => array(
            'NAME' => GetMessage('C_WIDGET_GROUP_LIST'),
            'SORT' => 200
        )
    ),
    'PARAMETERS' => $arParameters
);