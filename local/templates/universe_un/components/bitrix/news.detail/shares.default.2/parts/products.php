<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Html;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<div class="news-detail-products widget">
    <div class="news-detail-products-wrapper intec-content intec-content-visible">
        <div class="news-detail-products-wrapper-2 intec-content-wrapper">
            <?php if (!empty($arResult['BLOCKS']['PRODUCTS']['HEADER'])) { ?>
                <div class="news-detail-products-header widget-header">
                    <div class="intec-grid intec-grid-a-v-center intec-grid-i-4 intec-grid-wrap">
                        <div class="intec-grid-item intec-grid-item-600-1">
                            <?= Html::tag('div', $arResult['BLOCKS']['PRODUCTS']['HEADER']['VALUE'], [
                                'class' => Html::cssClassFromArray([
                                    'widget-title' => true,
                                    'align-' . $arResult['BLOCKS']['PRODUCTS']['HEADER']['POSITION'] => true,
                                    'margin' => $arResult['BLOCKS']['PRODUCTS']['PRODUCTS_USE_LIST_URL'] && !empty($arParams['PRODUCTS_LIST_URL'])
                                ], true)
                            ]) ?>
                        </div>
                        <?php if ($arResult['BLOCKS']['PRODUCTS']['PRODUCTS_USE_LIST_URL'] && !empty($arParams['PRODUCTS_LIST_URL'])) { ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'intec-grid-item-auto',
                                    'intec-grid-item-600-1',
                                    'news-detail-products-link-wrapper'
                                ],
                                'data-position' => $arResult['BLOCKS']['PRODUCTS']['PRODUCTS_LIST_URL_POSITION']
                            ]) ?>
                                <a href="<?= $arResult['BLOCKS']['PRODUCTS']['PRODUCTS_LIST_URL'] ?>" class="news-detail-products-link">
                                    <?= Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_PRODUCTS_LIST_URL_TEXT') ?>
                                </a>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <div class="news-detail-products-content widget-content">
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:catalog.section',
                    $arResult['BLOCKS']['PRODUCTS']['TEMPLATE'],
                    $arResult['BLOCKS']['PRODUCTS']['PARAMETERS'],
                    $component
                ) ?>
            </div>
        </div>
    </div>
</div>
