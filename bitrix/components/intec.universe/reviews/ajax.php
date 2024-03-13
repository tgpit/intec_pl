<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Security\Sign\BadSignatureException;
use Bitrix\Main\Security\Sign\Signer;
use Bitrix\Main\Web\PostDecodeFilter;
use intec\core\helpers\Encoding;

/**
 * @global CMain $APPLICATION
 */

define('STOP_STATISTICS', true);
define('NOT_CHECK_PERMISSIONS', true);

$siteId = !empty($_REQUEST['siteId']) && is_string($_REQUEST['siteId']) ? $_REQUEST['siteId'] : '';
$siteId = substr(preg_replace('/[^a-z0-9_]/i', '', $siteId), 0, 2);

if (!empty($siteId) && is_string($siteId))
    define('SITE_ID', $siteId);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$_POST = Encoding::convert(
    $_POST,
    Encoding::getDefault(),
    Encoding::UTF8
);

$request = Application::getInstance()
    ->getContext()
    ->getRequest();

$request->addFilter(new PostDecodeFilter());

$oSigner = new Signer();

try {
    $template = $oSigner->unsign(
        $request->get('template') ?: '',
        'intec.reviews'
    );

    $parameters = base64_decode(
        $oSigner->unsign(
            $request->get('parameters') ?: '',
            'intec.reviews'
        )
    ) ?: '';
} catch (BadSignatureException $e) {
    die();
}

$template = base64_decode($template);
$parameters = unserialize($parameters);
$parent = false;

if (!empty($parameters['PARENT_NAME'])) {
    $parent = new CBitrixComponent();
    $parent->initComponent($parameters['PARENT_NAME'], $parameters['PARENT_TEMPLATE_NAME']);
    $parent->initComponentTemplate($parameters['PARENT_TEMPLATE_PAGE']);
}

$APPLICATION->IncludeComponent(
    'intec.universe:reviews',
    $template,
    $parameters,
    $parent
);