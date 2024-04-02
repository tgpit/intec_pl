<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var Closure $vQuantity(&$arStore)
 */

?>
<div class="store-amount-list-content scrollbar-outer" data-scroll>
    <?php foreach ($arResult['STORES'] as $arStore) {
        if ($arStore['AMOUNT_STATUS'] === 'empty') {
            if ($arVisual['SHOW_EMPTY_STORE']) {
                include(__DIR__.'/stores.php');
            }
        } else {
            include(__DIR__.'/stores.php');
        }
    } ?>
</div>
