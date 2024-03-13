<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?= Html::beginTag('div', [
    'class' => Html::cssClassFromArray([
        'widget-items' => true,
        'owl-carousel' => $bSlideUse,
        'one-item' => !$bSlideUse
    ], true),
    'data-role' => 'items'
]) ?>

<?php foreach ($arResult['ITEMS'] as $arItem) {

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

    ?>
    <?= Html::beginTag('div', [
        'id' => $sAreaId,
        'class' => 'widget-item',
        'data' => [
            'id' => $arItem['ID'],
            'role' => 'item',
            'data' => $sData,
            'expanded' => 'false',
            'available' => $arItem['CAN_BUY'] ? 'true' : 'false',
            'properties' => !empty($arSkuProps) ? Json::encode($arSkuProps, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true) : ''
        ]
    ]) ?>
        <div class="widget-item-base">
            <div class="widget-item-mobile-first-wrapper">
                <?php $vButtons($arItem) ?>
                <div class="widget-item-image-container">
                    <?php $vImage($arItem) ?>
                    <!--noindex-->
                    <?php if ($arVisual['MARKS']['SHOW']) { ?>
                        <div class="widget-item-marks">
                            <?php $APPLICATION->includeComponent(
                                'intec.universe:main.markers',
                                $arVisual['MARKS']['TEMPLATE'],
                                [
                                    'HIT' => $arItem['MARKS']['HIT'] ? 'Y' : 'N',
                                    'NEW' => $arItem['MARKS']['NEW'] ? 'Y' : 'N',
                                    'RECOMMEND' => $arItem['MARKS']['RECOMMEND'] ? 'Y' : 'N',
                                    'SHARE' => $arItem['MARKS']['SHARE'] ? 'Y' : 'N',
                                    'ORIENTATION' => $arVisual['MARKS']['ORIENTATION']
                                ],
                                $component,
                                ['HIDE_ICONS' => 'Y']
                            ) ?>
                        </div>
                    <?php } ?>
                    <!--/noindex-->
                </div>
            </div>

            <div class="widget-item-mobile-second-wrapper">
                <!--noindex-->
                <?php if ($arVisual['VOTE']['SHOW']) { ?>
                    <div class="widget-item-vote">
                        <?php $APPLICATION->IncludeComponent(
                            'bitrix:iblock.vote',
                            'template.3',
                            [
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
                                    4 => '5',
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
                    <?= Html::tag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', $arItem['NAME'], [
                        'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $sLink : null,
                        'class' => [
                            'intec-cl-text-hover',
                        ],
                        'data' => [
                            'role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
                        ]
                    ]) ?>
                </div>
                <!--noindex-->
                <?php if ($arVisual['QUANTITY']['SHOW'] || $arVisual['ARTICLE']['SHOW']) { ?>
                    <div class="widget-item-information intec-grid intec-grid-wrap intec-grid-i-4">
                        <?php if ($arVisual['QUANTITY']['SHOW']) { ?>
                            <div class="widget-item-quantity-wrap intec-grid-item-auto">
                                <?php $vQuantity($arItem) ?>
                            </div>
                        <?php } ?>
                        <?php if ($arVisual['ARTICLE']['SHOW']) { ?>
                            <div class="widget-item-article intec-grid-item-auto" data-role="article">
                                <?= Html::tag('span', Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_ARTICLE_NAME'), [
                                    'class' => 'widget-item-article-name'
                                ]) ?>
                                <?= Html::tag('span', $arItem['DATA']['ARTICLE']['VALUE'], [
                                    'class' => 'widget-item-article-value',
                                    'data-role' => 'article.value'
                                ]) ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <!--/noindex-->
                <?php $vPrice($arItem) ?>

                <a class="widget-item-mobile-button intec-ui-mod-round-2 intec-ui intec-ui-control-button intec-ui-mod-transparent intec-ui-scheme-current" href="<?= $arItem['DETAIL_PAGE_URL'] ?>">
                    <?= Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_DETAIL_BUTTON') ?>
                </a>

                <?php
                    $sDateParam = null;

                    $arParams['MODE'] = ArrayHelper::fromRange(['period', 'day'], $arParams['MODE']);

                    if ($arParams['MODE'] === 'period') {
                        if (!empty($arParams['PROPERTY_SHOW_END'])) {
                            $sDateParam = $arParams['PROPERTY_SHOW_END'];
                        }
                    } else {
                        if (!empty($arParams['PROPERTY_SHOW_END_TIME'])) {
                            $sDateParam = $arParams['PROPERTY_SHOW_END_TIME'];
                        }
                    }

                    $sEndTime = CIBlockElement::GetProperty(
                        $arResult['IBLOCK_ID'],
                        $arItem['ID'],
                        ['SORT' => 'ASC'],
                        ['CODE' => $sDateParam]
                    )->GetNext();

                    $sEndTime = $sEndTime['VALUE'];
                ?>

                <?php if ($arBlocks['PRODUCT_DAY_TIMER']['SHOW']) { ?>
                    <div class="widget-item-product-day-timer" data-role="product.day.timer">
                        <?php $APPLICATION->IncludeComponent(
                            'intec.universe:timer',
                            'template.1',
                            [
                                'DATE_END' => $sEndTime,
                                'TIME_ZERO_HIDE' => 'N',
                                'SHOW_SECONDS' => 'Y'
                            ],
                            $component,
                            ['HIDE_ICONS' => 'Y']
                        ) ?>
                    </div>
                <?php } ?>
                </div>
            <!--noindex-->
            </div>

        <div class="widget-item-advanced">
            <?php if ($arItem['ACTION'] !== 'none') { ?>
                <div class="widget-item-purchase-container">
                    <div class="widget-item-purchase-container-wrapper intec-grid intec-grid-a-v-center">
                        <?php if ($arVisual['COUNTER']['SHOW'] && $arItem['ACTION'] === 'buy' && empty($arItem['OFFERS'])) {
                            $vCounter($bSlideUse);
                        } ?>
                        <div class="widget-item-purchase intec-grid-item intec-grid-item-shrink-1">
                            <?php $vPurchase($arItem) ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <!--/noindex-->
    <?= Html::endTag('div') ?>
    <?php $iItemsCurrent++; ?>
<?php } ?>
<?= Html::endTag('div') ?>
