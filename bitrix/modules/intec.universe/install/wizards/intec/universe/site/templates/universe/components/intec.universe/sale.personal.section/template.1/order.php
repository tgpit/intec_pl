<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

global $APPLICATION, $USER;

$this->setFrameMode(false);

$arFilter = $arParams['FILTER'];

if (!is_array($arFilter))
    $arFilter = [];

if ($USER->IsAuthorized()) {
    $arFilter['USER'] = $USER->GetID();
} else {
    $arFilter['USER'] = null;
}

$iOrderId = !empty($arResult['SEF']) ? $arResult['SEF']['VARIABLES']['ORDER_ID'] : $_REQUEST['orderId'];

?>
<div class="ns-intec c-sale-personal-section c-sale-personal-section-template-1 p-orders">
    <div class="sale-personal-section-wrapper intec-content">
        <div class="sale-personal-section-wrapper intec-content-wrapper">
            <?php $APPLICATION->IncludeComponent(
                'intec:startshop.orders.detail',
                '.default', [
                    'FILTER' => $arFilter,
                    'LIST_PAGE_URL' => $arResult['PAGES']['orders']['LINK'],
                    'ORDER_ID' => $iOrderId,
                    'CURRENCY' => $arParams['CURRENCY'],
                    "404_SET_STATUS" => $arParams['SET_STATUS_404'],
                    "404_REDIRECT" => $arParams['SHOW_404'],
                    "404_PAGE" => $arParams['FILE_404']
                ],
                $component
            ) ?>
        </div>
    </div>
</div>
