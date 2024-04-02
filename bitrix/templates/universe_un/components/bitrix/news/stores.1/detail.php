<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;

/**
 * @var array $arParams
 * @var array $arResult
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @global CDatabase $DB
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $templateFile
 * @var string $templateFolder
 * @var string $componentPath
 * @var CBitrixComponent $component
 */

$this->setFrameMode(true);
$arElement = null;

if (Loader::includeModule('catalog')) {
    $sElementPrefix = substr($arResult['VARIABLES']['ELEMENT_ID'], 0, 1);

    $arResult['VARIABLES']['ELEMENT_ID'] = preg_replace("/[^0-9]/", '', $arResult['VARIABLES']['ELEMENT_ID']);

    if ($sElementPrefix == 's') {
        $arElement = CCatalogStore::GetList(
            array('ID' => 'ASC'),
            array('ID' => $arResult['VARIABLES']['ELEMENT_ID']),
            false,
            false,
            array("ID")
        )->Fetch();
    }
}
?>

<div class="ns-bitrix c-news c-news-stores-1 p-detail">
    <div class="news-content">
        <?php if ($arElement) {
            include(__DIR__.'/parts/detail.store.php');

            if ($arDetail['SHOW']) {
                $APPLICATION->IncludeComponent(
                    'bitrix:catalog.store.detail',
                    $arDetail['TEMPLATE'],
                    $arDetail['PARAMETERS'],
                    $component
                );
            }
        } else {
            include(__DIR__.'/parts/detail.contact.php');

            if ($arDetail['SHOW']) {
                $iElementId = $APPLICATION->IncludeComponent(
                    'bitrix:news.detail',
                    $arDetail['TEMPLATE'],
                    $arDetail['PARAMETERS'],
                    $component
                );
            }
        } ?>
    </div>
</div>