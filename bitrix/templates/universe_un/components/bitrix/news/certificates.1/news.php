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

include(__DIR__.'/parts/list.php');

$arVisual = $arResult['VISUAL'];

?>

<div class="ns-bitrix c-news c-news-certificates-1 p-list">
    <div class="news-wrapper intec-content intec-content-visible">
        <div class="news-wrapper-2 intec-content-wrapper">
            <?php if ($arVisual['DESCRIPTION']['SHOW'] && $arVisual['DESCRIPTION']['POSITION'] === 'top') { ?>
                <div class="intec-ui-m-b-30">
                    <?= $arVisual['DESCRIPTION']['VALUE'] ?>
                </div>
            <?php } ?>
            <?php $APPLICATION->IncludeComponent(
                'bitrix:news.list',
                $arElements['TEMPLATE'],
                $arElements['PARAMETERS'],
                $component
            ); ?>
            <?php if ($arVisual['DESCRIPTION']['SHOW'] && $arVisual['DESCRIPTION']['POSITION'] === 'bottom') { ?>
                <div class="intec-ui-m-t-30">
                    <?= $arVisual['DESCRIPTION']['VALUE'] ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
