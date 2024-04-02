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

?>

<div class="ns-bitrix c-news c-news-help-1 p-list">
    <div class="news-wrapper intec-content intec-content-visible">
        <div class="news-wrapper-2 intec-content-wrapper">
            <?php $APPLICATION->IncludeComponent(
                'bitrix:news.list',
                $arElements['TEMPLATE'],
                $arElements['PARAMETERS'],
                $component
            ); ?>
        </div>
    </div>
</div>
