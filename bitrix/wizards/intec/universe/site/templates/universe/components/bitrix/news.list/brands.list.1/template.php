<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 */

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
$arLazyLoad = $arResult['LAZYLOAD'];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-news-list',
        'c-news-list-brands-list-1'
    ],
    'data' => [
        'wide' => $arVisual['WIDE'] ? 'true' : 'false',
        'view' => $arVisual['VIEW'],
        'columns' => $arVisual['COLUMNS'],
        'borders' => $arVisual['BORDERS']['SHOW'] ? 'true' : 'false'
    ]
]) ?>
    <?php if (!empty($arResult['ITEMS'])) { ?>
        <?php if ($arVisual['NAVIGATION']['SHOW']['TOP']) { ?>
            <div data-pagination-num="<?= $arResult['NAVIGATION']['NUMBER'] ?>">
                <!-- pagination-container -->
                <?= $arResult['NAV_STRING'] ?>
                <!-- pagination-container -->
            </div>
        <?php } ?>
        <?php
            if ($arVisual['VIEW'] === 'tiles.1') {
                include(__DIR__ . '/parts/tiles.1.php');
            } else if ($arVisual['VIEW'] === 'tiles.2') {
                include(__DIR__ . '/parts/tiles.2.php');
            } else {
                include(__DIR__ . '/parts/list.1.php');
            }
        ?>
        <?php if ($arVisual['NAVIGATION']['SHOW']['BOTTOM']) { ?>
            <div data-pagination-num="<?= $arResult['NAVIGATION']['NUMBER'] ?>">
                <!-- pagination-container -->
                <?= $arResult['NAV_STRING'] ?>
                <!-- pagination-container -->
            </div>
        <?php } ?>
    <?php } ?>
<?= Html::endTag('div') ?>