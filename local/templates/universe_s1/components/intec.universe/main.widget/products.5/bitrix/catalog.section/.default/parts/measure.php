<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

?>
<?php return function (&$arItem) use (&$arVisual) { ?>
    <?php $fRender = function (&$arItem, $bOffer = false) {

        if ($arItem['VISUAL']['OFFER'] && !$bOffer)
            return;

    ?>
        <?= Html::tag('span', Loc::getMessage('C_WIDGET_PRODUCTS_5_QUANTITY_RATIO', [
            '#QUANTITY_RATIO#' => !empty($arItem['CATALOG_MEASURE_RATIO']) ? $arItem['CATALOG_MEASURE_RATIO'] : '1',
            '#MEASURE#' => $arItem['CATALOG_MEASURE_NAME']
        ]), [
            'data-offer' => $bOffer ? $arItem['ID'] : 'false'
        ]) ?>
    <?php } ?>
    <?php

        $fRender($arItem);

        if ($arVisual['OFFERS']['USE'] && $arItem['VISUAL']['OFFER']) {
            foreach ($arItem['OFFERS'] as &$arOffer)
                $fRender($arOffer, true);

            unset($arOffer);
        }

    ?>
<?php } ?>