<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('iblock') || !Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);

include(__DIR__.'/parts/detail.php');
include(__DIR__.'/parts/menu.php');

$arMenu['SHOW'] = $arMenu['SHOW'] && $arParams['DETAIL_MENU_SHOW'] === 'Y';

?>

<div class="ns-bitrix c-news c-news-help-1 p-detail">
    <div class="news-wrapper intec-content intec-content-visible">
        <div class="news-wrapper-2 intec-content-wrapper">
            <?php if ($arMenu['SHOW']) { ?>
                <div class="news-content-left intec-content-left">
                    <div class="news-menu">
                        <?php $APPLICATION->IncludeComponent(
                            'bitrix:menu',
                            $arMenu['TEMPLATE'],
                            $arMenu['PARAMETERS'],
                            $component
                        ) ?>
                    </div>
                </div>
                <div class="news-content-right intec-content-right">
                    <div class="news-content-right-wrapper intec-content-right-wrapper" data-role="content">
            <?php } ?>
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:news.detail',
                    $arDetail['TEMPLATE'],
                    $arDetail['PARAMETERS'],
                    $component
                );?>
            <?php if ($arMenu['SHOW']) { ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
