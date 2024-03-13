<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\template\Properties;

if (defined('EDITOR'))
    return;

$arParams['CLAIMS_USE'] = Properties::get('profile-claims-use') ? 'Y' : 'N';
$arParams['PROFILE_ADD_USE'] = Properties::get('profile-add-use') ? 'Y' : 'N';
$arParams['CRM_SHOW_PAGE'] = Properties::get('profile-crm-use') ? 'Y' : 'N';
$arParams['PRODUCT_VIEWED_SHOW_PAGE'] = Properties::get('profile-viewed-product-use') ? 'Y' : 'N';
