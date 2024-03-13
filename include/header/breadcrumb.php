<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @global CMain $APPLICATION
 */

$GLOBALS['BreadCrumbIBlockType'] = 'catalogs';
$GLOBALS['BreadCrumbIBlockId'] = '58';

?>
<?php $APPLICATION->IncludeComponent(
    'bitrix:breadcrumb',
    '',
    array(),
    false
); ?>