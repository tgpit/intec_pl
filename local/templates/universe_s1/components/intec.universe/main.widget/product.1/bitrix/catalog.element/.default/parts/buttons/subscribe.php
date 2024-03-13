<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var string $sTemplateId
 * @var CMain $APPLICATION
 */

$APPLICATION->IncludeComponent(
    'bitrix:catalog.product.subscribe',
    '.default', [
        'PRODUCT_ID' => $arResult['ID'],
        'BUTTON_ID' => $sTemplateId.'-subscribe-'.$arResult['ID'],
        'BUTTON_CLASS' => Html::cssClassFromArray([
            'catalog-element-button-buy',
            'intec-ui' => [
                '',
                'control-button',
                'scheme-current'
            ]
        ])
    ]
);