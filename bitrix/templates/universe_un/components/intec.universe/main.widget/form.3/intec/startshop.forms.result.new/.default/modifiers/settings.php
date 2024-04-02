<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\template\Properties;

/**
 * @var array $arParams
 */

if (defined('EDITOR'))
    return;

$arParams['WEB_FORM_CONSENT_SHOW'] = Properties::get('base-consent') ? 'Y' : 'N';