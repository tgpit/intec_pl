<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arLazyLoad
 * @var CBitrixComponentTemplate $this
 */

Loc::loadMessages(__FILE__);

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arDescriptionProperties = ArrayHelper::getValue($arParams, 'DESCRIPTION_PROPERTIES');
$bDescriptionFull = $arParams['DESCRIPTION_FULL'] === 'Y';

$sImage = null;

if (!empty($arResult['DETAIL_PICTURE'])) {
    $sImage = $arResult['DETAIL_PICTURE'];
}

if (!empty($sImage)) {
    $sImage = $sImage['SRC'];
} else {
    $sImage = null;
}

$arForms = [];

if ($arParams['DISPLAY_FORM_ORDER'] == 'Y' && !empty($arParams['FORM_ORDER'])) {
    $arForms['ORDER'] = [
        'id' => $arParams['FORM_ORDER'],
        'template' => '.default',
        'parameters' => [
            'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM_ORDER',
            'CONSENT_URL' => $arParams['CONSENT_URL']
        ],
        'settings' => [
            'title' => Loc::getMessage('N_PROJECTS_N_D_DEFAULT_BUTTON_ORDER')
        ],
        'fields' => []
    ];

    if (!empty($arParams['PROPERTY_FORM_ORDER_PROJECT']))
        $arForms['ORDER']['fields'][$arParams['PROPERTY_FORM_ORDER_PROJECT']] = $arResult['NAME'];
}

if ($arParams['DISPLAY_FORM_ASK'] == 'Y' && !empty($arParams['FORM_ASK'])) {
    $arForms['ASK'] = [
        'id' => $arParams['FORM_ASK'],
        'template' => '.default',
        'parameters' => [
            'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM_ASK',
            'CONSENT_URL' => $arParams['CONSENT_URL']
        ],
        'settings' => [
            'title' => Loc::getMessage('N_PROJECTS_N_D_DEFAULT_BUTTON_ASK')
        ]
    ];
}

$arLazyLoad = $arResult['LAZYLOAD'];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-news-detail',
        'c-news-detail-projects-default-2'
    ]
]) ?>
    <div class="news-detail-content">
        <?php include(__DIR__.'/parts/header.php') ?>
        <?php include(__DIR__.'/parts/tabs.php') ?>
        <?php
            if (!empty($arResult['SERVICES'])) {
                include(__DIR__.'/parts/services.php');
            }
        ?>
    </div>
<?= Html::endTag('div') ?>
<?php include(__DIR__.'/parts/script.php') ?>
