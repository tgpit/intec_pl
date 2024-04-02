<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

global $APPLICATION, $USER;

$this->setFrameMode(false);

if ($USER->IsAuthorized()) {

    $arFilter = ArrayHelper::merge($arParams['FILTER'], [
        "USER" => $USER->GetID()
    ]);
    ?>
    <div class="ns-intec c-sale-personal-section c-sale-personal-section-template-1 p-orders">
        <div class="sale-personal-section-wrapper intec-content">
            <div class="sale-personal-section-wrapper intec-content-wrapper">
                <?php $APPLICATION->IncludeComponent(
                    'intec:startshop.orders.list',
                    '.default', [
                        "FILTER" => $arFilter,
                        'CURRENCY' => $arParams['CURRENCY'],
                        'DETAIL_PAGE_URL' => StringHelper::replace($arResult['SEF']['FOLDER'].$arResult['SEF']['TEMPLATES']['order'], [
                            '%23ORDER_ID%23' => '#ID#',
                            '#ORDER_ID#' => '#ID#'
                        ])
                    ],
                    $component
                ) ?>
            </div>
        </div>
    </div>
<?php
} else {
$APPLICATION->ShowAuthForm(null);
}
