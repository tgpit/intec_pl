<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Context;

/**
 * @var array $arResult
 * @var IntecReviewsNewComponent $component
 */

global $APPLICATION;

$request = Context::getCurrent()->getRequest();

if ($component->arParams['AJAX_UPDATE'] === 'Y' && $request->isAjaxRequest() && !defined('EDITOR')) {
    $content = ob_get_contents();

    ob_end_clean();

    list(, $form) = explode('<!--form-->', $content);

    if ($arResult['FORM']['STATUS'] === 'added')
        list(, $items) = explode('<!--items-all-->', $content);
    else
        list(, $items) = explode('<!--items-->', $content);

    unset($content);

    $component::sendJson([
        'status' => $arResult['FORM']['STATUS'],
        'form' => $form,
        'items' => $items
    ]);
}