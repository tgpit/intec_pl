<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Context;

/**
 * @var IntecMainNewsComponent $component
 */

global $APPLICATION;

$request = Context::getCurrent()->getRequest();

if ($component->arParams['AJAX_UPDATE'] === 'Y' && $request->isAjaxRequest() && !defined('EDITOR')) {
    $content = ob_get_contents();

    ob_end_clean();

    list(, $items) = explode('<!--items-->', $content);

    unset($content);

    $component::sendJsonAnswer([
        'items' => $items
    ]);
}