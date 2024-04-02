<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;

?>
{{#DELAYED}}
<div class="intec-grid basket-alert-wrap">
    <div class="intec-grid-item-auto intec-grid intec-grid-nowrap intec-grid-500-wrap basket-alert">
        <span class="intec-grid-item-auto basket-alert-text">
            <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_DELAYED_MESSAGE') ?>
        </span>
        <div class="intec-grid-item-auto basket-alert-interactive intec-cl-text" data-entity="basket-item-remove-delayed">
            <span class="basket-alert-icon">
                <?= FileHelper::getFileData(__DIR__.'/../../svg/alert.delayed.svg') ?>
            </span>
            <span class="basket-alert-interactive-text">
                <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_DELAYED_REMOVE') ?>
            </span>
        </div>
    </div>
</div>
{{/DELAYED}}