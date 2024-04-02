<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Loader;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arIBlock = $arResult['IBLOCK'];

include(__DIR__.'/parts/menu.php');
include(__DIR__.'/parts/detail.php');

$arMenu['SHOW'] = $arMenu['SHOW'] && $arParams['DETAIL_MENU_SHOW'] === 'Y';
$arFilter = [
    'SHOW' => $arParams['DETAIL_FILTER_USE'] === 'Y' && $arProducts['SHOW'],
    'TYPE' => ArrayHelper::fromRange([
        'horizontal',
        'vertical'
    ], $arParams['DETAIL_FILTER_TYPE'])
];

$arColumns = [
    'SHOW' => $arMenu['SHOW'] || ($arFilter['SHOW'] && $arFilter['TYPE'] === 'vertical')
];

if ($arColumns['SHOW']) {
    $arDetail['PARAMETERS']['WIDE'] = 'N';
}

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-news',
        'c-news-brands-1',
        'p-detail'
    ]
]) ?>
    <div class="news-wrapper intec-content intec-content-visible">
        <div class="news-wrapper-2 intec-content-wrapper">
            <?= Html::beginTag('div', [
                'class' => 'news-content',
                'data' => [
                    'role' => !$arColumns['SHOW'] ? 'content' : null
                ]
            ]) ?>
                <?php if ($arColumns['SHOW']) { ?>
                    <div class="news-content-left intec-content-left">
                        <?php $APPLICATION->ShowViewContent('news_detail_brands_filter') ?>
                        <?php if ($arMenu['SHOW']) { ?>
                            <div class="news-menu">
                                <?php $APPLICATION->IncludeComponent(
                                    'bitrix:menu',
                                    $arMenu['TEMPLATE'],
                                    $arMenu['PARAMETERS'],
                                    $component
                                ) ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="news-content-right intec-content-right">
                        <div class="news-content-right-wrapper intec-content-right-wrapper" data-role="content">
                <?php } ?>
                    <?php if ($arDetail['SHOW']) { ?>
                        <?php $APPLICATION->IncludeComponent(
                            'bitrix:news.detail',
                            $arDetail['TEMPLATE'],
                            $arDetail['PARAMETERS'],
                            $component
                        ); ?>
                    <?php } ?>
                <?php if ($arColumns['SHOW']) { ?>
                    </div>
                </div>
                <?php } ?>
            <?= Html::endTag('div') ?>
        </div>
    </div>
<?= Html::endTag('div') ?>


