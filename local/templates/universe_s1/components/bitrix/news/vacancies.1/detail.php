<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
    <?php

use Bitrix\Main\Loader;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use Bitrix\Main\Localization\Loc;
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

$arParams = ArrayHelper::merge([
    'DETAIL_MENU_SHOW' => 'Y'
], $arParams);

$arIBlock = $arResult['IBLOCK'];
$arSection = $arResult['SECTION'];

include(__DIR__.'/parts/menu.php');
include(__DIR__.'/parts/detail.php');

$arMenu['SHOW'] = $arMenu['SHOW'] && $arParams['DETAIL_MENU_SHOW'] === 'Y';

if ($arMenu['SHOW']) {
    $arDetail['PARAMETERS']['WIDE'] = 'N';
}

?>
<div class="ns-bitrix c-news c-news-vacancies-1 p-detail">
    <?php if ($arMenu['SHOW']) { ?>
    <div class="news-wrapper intec-content intec-content-visible">
        <div class="news-wrapper-2 intec-content-wrapper">
            <div class="news-content">
                <div class="news-content-left intec-content-left">
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
                    <div class="news-content-right-wrapper intec-content-right-wrapper">
                        <?php } ?>
                        <?php if ($arDetail['SHOW']) { ?>
                            <?php $APPLICATION->IncludeComponent(
                                "bitrix:news.detail",
                                $arDetail['TEMPLATE'],
                                $arDetail['PARAMETERS'],
                                false
                            ); ?>
                        <?php } ?>
                        <?php if ($arMenu['SHOW']) { ?>
                    </div>
                </div>
                <div class="intec-ui-clear"></div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
