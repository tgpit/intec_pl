<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php $vMeasure = function (&$arItem) use (&$arVisual) { ?>
    <?php $fRender = function (&$arItem, $bOffer = false) { ?>
        <?php if ($arItem['DATA']['OFFER'] && !$bOffer)
            return;
        ?>
        <?= Html::beginTag('span', [
            'data-offer' => $bOffer ? $arItem['ID'] : 'false'
        ]) ?>
            <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_LIST_1_MEASURE_RATIO', [
                '#QUANTITY_RATIO#' => !empty($arItem['CATALOG_MEASURE_RATIO']) ? $arItem['CATALOG_MEASURE_RATIO'] : '1',
                '#MEASURE#' => $arItem['CATALOG_MEASURE_NAME']
            ]) ?>
        <?= Html::endTag('span') ?>
    <?php } ?>
    <?php

        $fRender($arItem);

        if ($arVisual['OFFERS']['USE'] && $arItem['DATA']['OFFER'])
            foreach ($arItem['OFFERS'] as &$arOffer) {
                $fRender($arOffer, true);

                unset($arOffer);
            }

    ?>
<?php } ?>
