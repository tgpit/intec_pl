<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;

?>
{{#DELAYED}}
<div class="basket-alert-wrap" data-print="false">
    <div class="basket-alert">
        <span class="basket-alert-text">
            <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_ITEM_DELAYED_MESSAGE') ?>
        </span>
        <div class="basket-alert-interactive intec-cl-text" data-entity="basket-item-remove-delayed">
            <span class="basket-alert-icon">
                <?= FileHelper::getFileData(__DIR__.'/../../svg/alert.delayed.svg') ?>
            </span>
            <span class="basket-alert-interactive-text">
                <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_ITEM_DELAYED_REMOVE') ?>
            </span>
        </div>
    </div>
</div>
{{/DELAYED}}