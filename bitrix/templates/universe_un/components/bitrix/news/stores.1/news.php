<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);

?>
<div class="ns-bitrix c-news c-news-stores-1 p-news">
    <div class="news-content">
        <?php if (Loader::includeModule('catalog')) {

            $sPathToElement = StringHelper::replace($arResult['URL_TEMPLATES']['detail'], ['#ELEMENT_ID#' => 's#store_id#']);
            $sPathToElement = $arResult['FOLDER'].$sPathToElement;

            include(__DIR__.'/parts/list.stores.php');

            if ($arList['SHOW']) {
                $APPLICATION->IncludeComponent(
                    'bitrix:catalog.store.list',
                    $arList['TEMPLATE'],
                    $arList['PARAMETERS'],
                    $component
                );
            }
        } else {

            include(__DIR__.'/parts/list.contacts.php');

            if ($arList['SHOW']) {
                $APPLICATION->IncludeComponent(
                    'bitrix:news.list',
                    $arList['TEMPLATE'],
                    $arList['PARAMETERS'],
                    $component
                );
            }

        } ?>
    </div>
</div>
