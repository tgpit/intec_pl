<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

$arExt = [];

if($arParams['POPUP'])
	$arExt[] = 'window';

CUtil::InitJSCore($arExt);

$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/js/socialservices/css/ss.css');
$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/js/socialservices/ss.js');
