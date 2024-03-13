<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\template\Properties;

/**
 * @var array $arParams
 */

if (!defined('EDITOR')) {
    $arSettings = [
        'LIST' => [
            'TEMPLATE' => Properties::get('sections-jobs-template')
        ]
    ];

    switch ($arSettings['LIST']['TEMPLATE']) {
        case 'list.1': {
            $arParams['LIST_DETAIL_PAGE_USE'] = 'Y';
            break;
        }
        case 'list.2': {
            $arParams['LIST_DETAIL_PAGE_USE'] = 'N';
            break;
        }
    }

    if (Properties::get('template-images-lazyload-use')) {
        $arParams['LIST_LAZYLOAD_USE'] = 'Y';
        $arParams['DETAIL_LAZYLOAD_USE'] = 'Y';
    }
}