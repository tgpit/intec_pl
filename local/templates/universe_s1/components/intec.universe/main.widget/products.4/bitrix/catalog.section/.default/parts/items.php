<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\Json;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 * @var Closure $dData
 * @var Closure $vBigImage
 * @var Closure $vButtons
 * @var Closure $vCounter
 * @var Closure $vImage
 * @var Closure $vPrice
 * @var Closure $vPurchase
 * @var Closure $vQuantity
 * @var Closure $vSku
 */

$iColumns = $arVisual['COLUMNS']['DESKTOP'];

if (!empty($arBanner))
    if ($iColumns <= 3)
        $iColumns = 4;

$iItemsCount = null;
$iItemsCurrent = 0;

if ($arVisual['LINES'] !== null)
    $iItemsCount = $iColumns * $arVisual['LINES'];

if (!empty($arBanner))
    if ($iItemsCount >= 4) {
        $iItemsCount = $iItemsCount - 2;
    } else {
        $iItemsCount--;
    }

?>
<?= Html::beginTag('div', [
    'class' => Html::cssClassFromArray([
        'widget-items' => true,
        'intec-grid' => [
            '' => true,
            'wrap' => true,
            'a-v-stretch' => true,
            'a-h-start' => true,
            'i-10' => $arVisual['INDENTS']['USE']
        ]
    ], true)
]) ?>
<?php if (!empty($arBanner)) {

    $sId = $sTemplateId.'_'.$arBanner['ID'];
    $sAreaId = $this->GetEditAreaId($sId);
    $this->AddEditAction($sId, $arBanner['EDIT_LINK']);
    $this->AddDeleteAction($sId, $arBanner['DELETE_LINK']);

    $sData = Json::encode($dData($arBanner), JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true);
    $sLink = Html::decode($arBanner['DETAIL_PAGE_URL']);

?>
    <?= Html::beginTag('div', [
        'id' => $sAreaId,
        'class' => Html::cssClassFromArray([
            'widget-item' => true,
            'intec-grid-item' => [
                'auto' => $arVisual['COLUMNS']['DESKTOP'] >= 5,
                '2' => $arVisual['COLUMNS']['DESKTOP'] < 5,
                '1200-auto' => true,
                '950-1' => true
            ]
        ],  true),
        'data' => [
            'id' => $arBanner['ID'],
            'role' => 'item',
            'data' => $sData,
            'expanded' => 'false',
            'available' => $arBanner['CAN_BUY'] ? 'true' : 'false',
            'first-item' => 'true',
            'first-item-theme' => $arBanner['BANNER']['THEME'],
            'columns' => $arVisual['COLUMNS']['DESKTOP']
        ]
    ]) ?>
    <div class="widget-item-wrapper">
        <div class="widget-item-first">
            <div class="widget-item-image-container">
                <?= Html::tag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', null, [
                    'class' => 'widget-item-big-link',
                    'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $sLink : null,
                    'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
                ]) ?>
                <?php $vBigImage($arBanner) ?>
                <?php if ($arBanner['VISUAL']['MARKS']['SHOW']) { ?>
                    <!--noindex-->
                    <div class="widget-item-marks">
                        <?php $APPLICATION->includeComponent(
                            'intec.universe:main.markers',
                            'template.2', [
                            'HIT' => $arBanner['VISUAL']['MARKS']['VALUES']['HIT'] ? 'Y' : 'N',
                            'NEW' => $arBanner['VISUAL']['MARKS']['VALUES']['NEW'] ? 'Y' : 'N',
                            'RECOMMEND' => $arBanner['VISUAL']['MARKS']['VALUES']['RECOMMEND'] ? 'Y' : 'N',
                            'SHARE' => $arBanner['VISUAL']['MARKS']['VALUES']['SHARE'] ? 'Y' : 'N',
                            'ORIENTATION' => $arVisual['MARKS']['ORIENTATION']
                        ],
                            $component,
                            ['HIDE_ICONS' => 'Y']
                        ) ?>
                    </div>
                    <!--/noindex-->
                <?php } ?>
                <div class="widget-item-info">
                    <?php if ($arVisual['VOTE']['SHOW']) { ?>
                        <div class="widget-item-vote">
                            <?php $APPLICATION->IncludeComponent(
                                'bitrix:iblock.vote',
                                'template.1', [
                                    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                    'ELEMENT_ID' => $arBanner['ID'],
                                    'ELEMENT_CODE' => $arBanner['CODE'],
                                    'MAX_VOTE' => '5',
                                    'VOTE_NAMES' => [
                                        0 => '1',
                                        1 => '2',
                                        2 => '3',
                                        3 => '4',
                                        4 => '5'
                                    ],
                                    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                    'CACHE_TIME' => $arParams['CACHE_TIME'],
                                    'DISPLAY_AS_RATING' => $arVisual['VOTE']['MODE'] === 'rating' ? 'rating' : 'vote_avg',
                                    'SHOW_RATING' => 'N'
                                ],
                                $component,
                                ['HIDE_ICONS' => 'Y']
                            ) ?>
                        </div>
                    <?php } ?>
                    <!--/noindex-->
                    <div class="widget-item-name">
                        <div>
                            <?= $arBanner['NAME'] ?>
                        </div>
                    </div>
                    <?php if ($arBanner['VISUAL']['QUANTITY']['SHOW'] || $arBanner['VISUAL']['ARTICLE']['SHOW']) { ?>
                        <!--noindex-->
                        <div class="widget-item-information">
                            <div class="intec-grid intec-grid-wrap intec-grid-i-h-8 intec-grid-i-v-2">
                                <?php if ($arBanner['VISUAL']['QUANTITY']['SHOW']) { ?>
                                    <div class="widget-item-quantity-wrap intec-grid-item-auto">
                                        <?php $vQuantity($arBanner) ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arBanner['VISUAL']['ARTICLE']['SHOW']) { ?>
                                    <?= Html::beginTag('div', [
                                        'class' => 'widget-item-article-wrap intec-grid-item-auto',
                                        'data' => [
                                            'role' => 'article',
                                            'show' => !empty($arBanner['DATA']['ARTICLE']['VALUE']) ? 'true' : 'false'
                                        ]
                                    ]) ?>
                                    <div class="widget-item-article">
                                        <?= Html::tag('span', $arBanner['DATA']['ARTICLE']['NAME'], [
                                            'class' => 'widget-item-article-name'
                                        ]) ?>
                                        <?= Html::tag('span', $arBanner['DATA']['ARTICLE']['VALUE'], [
                                            'class' => 'widget-item-article-value',
                                            'data-role' => 'article.value'
                                        ]) ?>
                                    </div>
                                    <?= Html::endTag('div') ?>
                                <?php } ?>
                            </div>
                        </div>
                        <!--/noindex-->
                    <?php } ?>
                    <?php if ($arBanner['VISUAL']['PRICE']['SHOW'])
                        $vPrice($arBanner);
                    ?>
                    <?php if ($arBanner['VISUAL']['TIMER']['SHOW']) { ?>
                        <div class="widget-item-section-timer">
                            <?php include(__DIR__ . '/banner.timer.php'); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?= Html::endTag('div') ?>
<?php } ?>
<?php foreach ($arItems as $arItem) {

    if ($iItemsCount !== null)
        if ($iItemsCurrent >= $iItemsCount)
            break;

    $sId = $sTemplateId.'_'.$arItem['ID'];
    $sAreaId = $this->GetEditAreaId($sId);
    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

    $sData = Json::encode($dData($arItem), JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true);
    $sLink = Html::decode($arItem['DETAIL_PAGE_URL']);

    $arSkuProps = [];

    if (!empty($arResult['SKU_PROPS']))
        $arSkuProps = $arResult['SKU_PROPS'];
    else if (!empty($arItem['SKU_PROPS']))
        $arSkuProps = $arItem['SKU_PROPS'];

    $bRecalculation = false;

    if (
        $bBase &&
        $arVisual['PRICE']['RECALCULATION'] &&
        $arItem['VISUAL']['COUNTER']['SHOW'] &&
        $arItem['VISUAL']['ACTION'] === 'buy'
    )
        $bRecalculation = true;

?>
    <?= Html::beginTag('div', [
        'id' => $sAreaId,
        'class' => Html::cssClassFromArray([
            'widget-item' => true,
            'intec-grid-item' => [
                $iColumns => true,
                '500-1' => ($iColumns <= 5) && $arVisual['COLUMNS']['MOBILE'] == 1,
                '500-2' => ($iColumns <= 5) && $arVisual['COLUMNS']['MOBILE'] == 2,
                '700-2' => $iColumns > 2,
                '950-3' => $iColumns > 2,
                '1200-3' => $iColumns > 3
            ]
        ],  true),
        'data' => [
            'id' => $arItem['ID'],
            'role' => 'item',
            'data' => $sData,
            'expanded' => 'false',
            'available' => $arItem['CAN_BUY'] ? 'true' : 'false',
            'recalculation' => $bRecalculation ? 'true' : 'false',
            'timer-column-size' => $arVisual['COLUMNS']['DESKTOP'],
            'timer-adaptation' => $arVisual['TIMER']['SHOW'] ? 'true' : 'false'
        ]
    ]) ?>
        <div class="widget-item-wrapper" data-borders-style="<?= $arVisual['BORDERS']['STYLE'] ?>">
            <div class="widget-item-base">
                <?php if (
                    $arItem['VISUAL']['DELAY']['USE'] ||
                    $arItem['VISUAL']['COMPARE']['USE'] ||
                    $arItem['VISUAL']['ORDER_FAST']['USE'] ||
                    $arResult['QUICK_VIEW']['USE']
                )
                    $vButtons($arItem);
                ?>
                <div class="widget-item-image-container">
                    <?php $vImage($arItem) ?>
                    <?php if ($arItem['VISUAL']['MARKS']['SHOW']) { ?>
                        <!--noindex-->
                        <div class="widget-item-marks">
                            <?php $APPLICATION->includeComponent(
                                'intec.universe:main.markers',
                                'template.2', [
                                    'HIT' => $arItem['VISUAL']['MARKS']['VALUES']['HIT'] ? 'Y' : 'N',
                                    'NEW' => $arItem['VISUAL']['MARKS']['VALUES']['NEW'] ? 'Y' : 'N',
                                    'RECOMMEND' => $arItem['VISUAL']['MARKS']['VALUES']['RECOMMEND'] ? 'Y' : 'N',
                                    'SHARE' => $arItem['VISUAL']['MARKS']['VALUES']['SHARE'] ? 'Y' : 'N',
                                    'ORIENTATION' => $arVisual['MARKS']['ORIENTATION']
                                ],
                                $component,
                                ['HIDE_ICONS' => 'Y']
                            ) ?>
                        </div>
                        <!--/noindex-->
                    <?php } ?>
                </div>
                <?php if ($arVisual['VOTE']['SHOW']) { ?>
                    <!--noindex-->
                    <div class="widget-item-vote">
                        <?php $APPLICATION->IncludeComponent(
                            'bitrix:iblock.vote',
                            'template.1', [
                                'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                                'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                'ELEMENT_ID' => $arItem['ID'],
                                'ELEMENT_CODE' => $arItem['CODE'],
                                'MAX_VOTE' => '5',
                                'VOTE_NAMES' => [
                                    0 => '1',
                                    1 => '2',
                                    2 => '3',
                                    3 => '4',
                                    4 => '5'
                                ],
                                'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                'CACHE_TIME' => $arParams['CACHE_TIME'],
                                'DISPLAY_AS_RATING' => $arVisual['VOTE']['MODE'] === 'rating' ? 'rating' : 'vote_avg',
                                'SHOW_RATING' => 'N'
                            ],
                            $component,
                            ['HIDE_ICONS' => 'Y']
                        ) ?>
                    </div>
                    <!--/noindex-->
                <?php } ?>
                <div class="widget-item-name">
                    <?= Html::tag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', $arItem['NAME'], [
                        'class' => 'intec-cl-text-hover',
                        'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $sLink : null,
                        'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : 'offer.link'
                    ]) ?>
                </div>
                <?php if ($arItem['VISUAL']['QUANTITY']['SHOW'] || $arItem['VISUAL']['ARTICLE']['SHOW']) { ?>
                    <!--noindex-->
                    <div class="widget-item-information">
                        <div class="intec-grid intec-grid-wrap intec-grid-i-h-8 intec-grid-i-v-2">
                            <?php if ($arItem['VISUAL']['QUANTITY']['SHOW']) { ?>
                                <div class="widget-item-quantity-wrap intec-grid-item-auto">
                                    <?php $vQuantity($arItem) ?>
                                </div>
                            <?php } ?>
                            <?php if ($arItem['VISUAL']['ARTICLE']['SHOW']) { ?>
                                <?= Html::beginTag('div', [
                                    'class' => 'widget-item-article-wrap intec-grid-item-auto',
                                    'data' => [
                                        'role' => 'article',
                                        'show' => !empty($arItem['DATA']['ARTICLE']['VALUE']) ? 'true' : 'false'
                                    ]
                                ]) ?>
                                    <div class="widget-item-article">
                                        <span class="widget-item-article-name">
                                            <?= $arItem['DATA']['ARTICLE']['NAME'] ?>
                                        </span>
                                        <span class="widget-item-article-value" data-role="article.value">
                                            <?= $arItem['DATA']['ARTICLE']['VALUE'] ?>
                                        </span>
                                    </div>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        </div>
                    </div>
                    <!--/noindex-->
                <?php } ?>
                <?php if ($arVisual['OFFERS']['USE'] && $arItem['VISUAL']['OFFER'] && !empty($arSkuProps)) { ?>
                    <!--noindex-->
                    <?php $vSku($arSkuProps) ?>
                    <!--/noindex-->
                <?php } ?>
                <?php if ($arItem['VISUAL']['PRICE']['SHOW'])
                    $vPrice($arItem);
                ?>
                <?php if ($arItem['VISUAL']['TIMER']['SHOW']) { ?>
                    <div class="widget-item-section-timer">
                        <?php include(__DIR__ . '/timer.php'); ?>
                    </div>
                <?php } ?>
            </div>
            <?php if ($arItem['ACTION'] !== 'none') { ?>
                <!--noindex-->
                <div class="widget-item-advanced">
                    <div class="widget-item-purchase-container">
                        <div class="widget-item-purchase-container-wrapper intec-grid intec-grid-a-v-center">
                            <?php if ($arItem['VISUAL']['COUNTER']['SHOW'])
                                $vCounter();
                            ?>
                            <div class="widget-item-purchase intec-grid-item intec-grid-item-shrink-1">
                                <div class="widget-item-purchase-desktop">
                                    <?php $vPurchase($arItem) ?>
                                </div>
                                <div class="widget-item-purchase-mobile">
                                    <?php $vPurchase($arItem, true) ?>
                                </div>
                            </div>
                        </div>
                        <?php if ($bRecalculation) { ?>
                            <div class="widget-item-summary hidden" data-role="item.summary">
                                <?= Loc::getMessage('C_WIDGET_PRODUCTS_4_TITLE_SUMMARY') ?>
                                <span data-role="item.summary.price"></span>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <!--/noindex-->
            <?php } ?>
        </div>
    <?= Html::endTag('div') ?>
    <?php $iItemsCurrent++; ?>
    <?php $bFirstItem = false; ?>
<?php } ?>
<?= Html::endTag('div') ?>
