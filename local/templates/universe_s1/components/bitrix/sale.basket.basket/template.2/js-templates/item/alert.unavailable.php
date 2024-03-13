<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;

?>
{{#NOT_AVAILABLE}}
<div class="basket-alert-wrap" data-print="false">
    <div class="basket-alert">
            <span class="basket-alert-text">
                <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_ITEM_UNAVAILABLE') ?>
            </span>
        <div class="basket-alert-interactive intec-cl-text" data-entity="basket-item-delete" data-item-action="delete">
            <span class="basket-alert-icon">
                <?= FileHelper::getFileData(__DIR__.'/../../svg/alert.remove.svg') ?>
            </span>
            <span class="basket-alert-interactive-text">
                <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_ITEM_REMOVE') ?>
            </span>
        </div>
    </div>
</div>
{{/NOT_AVAILABLE}}