<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use intec\core\bitrix\Component;

/**
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 * @var array $arResult
 * @var array $arParams
 */

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

if ($arVisual['PRODUCTS']['SHOW']) {
    include(__DIR__.'/parts/sort.php');
    include(__DIR__.'/parts/products.php');
}

if ($arVisual['SHARES']['SHOW'])
    include(__DIR__.'/parts/shares.php');

$sPictureUrl = null;

if (!empty($arVisual['BANNER']['PICTURE']))
    $sPictureUrl = 'url(\''.($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arVisual['BANNER']['PICTURE']).'\')';

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-news-detail',
        'c-news-detail-collections-detail-1'
    ]
]) ?>
    <?= Html::beginTag('div', [
        'class' => 'news-detail-banner',
        'data' => [
            'theme' => $arVisual['BANNER']['THEME'],
            'has-image' => !empty($sPictureUrl) ? 'true' : 'false',
            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
            'original' => $arVisual['LAZYLOAD']['USE'] ? $arVisual['BANNER']['PICTURE'] : null
        ],
        'style' => [
            'background-image' => !empty($sPictureUrl) ? $sPictureUrl : null,
        ]
    ]) ?>
        <div class="news-detail-banner-fade"></div>
        <div class="news-detail-banner-body intec-content intec-content-visible">
            <div class="intec-content-wrapper">
                <?= Html::beginTag('div', [
                    'class' => [
                        'intec-grid' => [
                            '',
                            'a-v-center'
                        ]
                    ]
                ]) ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'intec-template-title' => empty($sPictureUrl),
                            'intec-grid-item-2' => true,
                            'intec-grid-item-500-1' => true
                        ], true)
                    ]) ?>
                        <h1 class="news-detail-banner-name">
                            <?= $arResult['NAME'] ?>
                        </h1>
                        <?php if (!empty($arVisual['BANNER']['TEXT'])) { ?>
                            <div class="news-detail-banner-text">
                                <?= $arVisual['BANNER']['TEXT'] ?>
                            </div>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    <?= Html::endTag('div')?>
    <?php if (!empty($arResult['PREVIEW_TEXT'])) { ?>
        <div class="news-detail-line-block news-detail-description">
            <div class="intec-content intec-content-visible">
                <div class="intec-content-wrapper">
                    <?= strip_tags($arResult['PREVIEW_TEXT']) ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ($arVisual['PRODUCTS']['SHOW']) { ?>
        <div class="news-detail-line-block news-detail-products">
            <div class="intec-content intec-content-visible">
                <div class="intec-content-wrapper">
                    <?php if ($arVisual['PRODUCTS']['HEADER']['SHOW']) { ?>
                        <div class="intec-template-part intec-template-part-title">
                            <?= $arVisual['PRODUCTS']['HEADER']['VALUE'] ?>
                        </div>
                    <?php } ?>
                    <?php include(__DIR__.'/parts/panel.php') ?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.section',
                        $arProducts['TEMPLATE'],
                        $arProducts['PARAMETERS'],
                        $component
                    ) ?>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php if ($arVisual['DETAIL']['SHOW']) { ?>
        <div class="news-detail-line-block news-detail-additional-text-wrapper">
            <div class="intec-content intec-content-visible">
                <div class="intec-content-wrapper">
                    <?php if (!empty($arVisual['DETAIL']['HEADER'])) { ?>
                        <div class="intec-template-part intec-template-part-title">
                            <?= $arVisual['DETAIL']['HEADER'] ?>
                        </div>
                    <?php } if (!empty($arResult['DETAIL_TEXT'])) { ?>
                        <div class="news-detail-additional-text">
                            <?= $arResult['DETAIL_TEXT'] ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ($arVisual['SHARES']['SHOW']) { ?>
        <div class="news-detail-line-block news-detail-shares">
            <div class="intec-content intec-content-visible">
                <div class="intec-content-wrapper">
                    <?php $APPLICATION->IncludeComponent(
                        'intec.universe:main.shares',
                        $arShares['TEMPLATE'],
                        $arShares['PARAMETERS'],
                        $component
                    ) ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ($arVisual['LINK']['BACK']['SHOW']) { ?>
        <div class="news-detail-line-block news-detail-return">
            <div class="intec-content intec-content-visible">
                <div class="intec-content-wrapper">
                    <a class="intec-cl-text-hover" href="<?= $arResult['LIST_PAGE_URL'] ?>">
                        <i class="far fa-chevron-left"></i>
                        <?= $arVisual['LINK']['BACK']['VALUE'] ?>
                    </a>
                </div>
            </div>
        </div>
    <?php } ?>
<?= Html::endTag('div') ?>