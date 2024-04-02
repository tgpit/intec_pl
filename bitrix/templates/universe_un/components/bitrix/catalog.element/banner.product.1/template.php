<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;
use intec\core\helpers\FileHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

Loc::loadMessages(__FILE__);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];

include(__DIR__.'/parts/data.php');
include(__DIR__.'/parts/quantity.php');
include(__DIR__.'/parts/price.range.php');

$arPrice = null;
$bOffers = !empty($arResult['OFFERS']);

if (!empty($arResult['ITEM_PRICES']))
    $arPrice = ArrayHelper::getFirstValue($arResult['ITEM_PRICES']);

$arSvg = [
    'DISCOUNT' => FileHelper::getFileData(__DIR__.'/svg/discount.icon.svg'),
    'BASKET' => FileHelper::getFileData(__DIR__.'/svg/basket.svg'),
    'DELAY' => FileHelper::getFileData(__DIR__.'/svg/delay.svg'),
    'COMPARE' => FileHelper::getFileData(__DIR__.'/svg/compare.svg')
];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-element',
        'c-catalog-element-banner-product-1'
    ],
    'data' => [
        'data' => Json::encode($arData, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true),
        'properties' => Json::encode($arResult['SKU_PROPS'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true),
        'role' => 'banner.product'
    ]
]) ?>
    <?php if (!$arVisual['WIDE']) { ?>
        <div class="intec-content intec-content-visible">
            <div class="intec-content-wrapper">
    <?php } ?>
                <div class="catalog-element-information">
                    <?php if ($arVisual['MARKS']['SHOW']) { ?>
                        <div class="catalog-element-information-part" data-code="marks">
                            <?php include(__DIR__.'/parts/marks.php') ?>
                        </div>
                    <?php } ?>
                    <div class="catalog-element-information-part" data-code="name">
                        <?= Html::tag('a', $arResult['NAME'], [
                            'class' => 'catalog-element-name',
                            'href' => $arResult['DETAIL_PAGE_URL']
                        ]) ?>
                    </div>
                    <?php if ($arVisual['VOTE']['SHOW'] || $arVisual['QUANTITY']['SHOW']) { ?>
                        <div class="catalog-element-information-part" data-code="vote.quantity">
                            <div class="intec-grid intec-grid-nowrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-8">
                                <?php if ($arVisual['VOTE']['SHOW']) { ?>
                                    <div class="intec-grid-item-auto">
                                        <?php include(__DIR__ . '/parts/vote.php') ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['QUANTITY']['SHOW']) { ?>
                                    <div class="intec-grid-item-auto">
                                        <?php $vQuantity($arResult); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($arVisual['TIMER']['SHOW']) { ?>
                        <div class="catalog-element-information-part" data-code="timer">
                            <?php include(__DIR__ . '/parts/timer.php') ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($arPrice)) { ?>
                        <div class="catalog-element-information-part" data-code="price">
                            <?php include(__DIR__.'/parts/price.php'); ?>
                        </div>
                    <?php } ?>
                    <?php include(__DIR__ . '/parts/buttons.php') ?>
                </div>
    <?php if (!$arVisual['WIDE']) { ?>
            </div>
        </div>
    <?php } ?>
    <?php include(__DIR__.'/parts/microdata.php') ?>
    <?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>