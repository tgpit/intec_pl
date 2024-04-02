<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arSvg
 * @var bool $bOffers
 */

?>
<?php $vStores = function (&$arStore) use (&$arResult, &$arVisual, &$vQuantity) { ?>
    <?= Html::beginTag('div', [
        'class' => 'store-amount-list-item',
        'data-role' => 'store.list.item',
        'data-active' => 'false',
        'data-store-id' => $arStore['ID']
    ]) ?>
    <div class="store-amount-list-item-name">
        <?= $arStore['TITLE'] ?>
    </div>
    <div class="store-amount-list-item-quantity">
        <?php $vQuantity($arStore) ?>
    </div>
    <?= Html::endTag('div') ?>
<?php } ?>

<?php
    $vStores($arStore);
    unset($vStores);
?>