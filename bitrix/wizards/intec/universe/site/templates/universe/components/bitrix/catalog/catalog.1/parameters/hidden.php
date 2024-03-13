<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\core\io\Path;

$arParameters = [
    'GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT',
    'GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT',
];

$oPath = Path::from(__DIR__.'/../settings/empty/control.js')->toRelative()->asAbsolute();
$sPath = $oPath->getValue('/');

foreach ($arParameters as $sParameter) {
    $arTemplateParameters[$sParameter] = [
        'HIDDEN' => 'Y',
        'TYPE' => 'CUSTOM',
        'JS_FILE' => $sPath,
        'JS_EVENT' => 'initControlEmpty'
    ];
}