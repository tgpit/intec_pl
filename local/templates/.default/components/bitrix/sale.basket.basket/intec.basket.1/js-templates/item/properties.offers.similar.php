<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

?>
{{#HAS_SIMILAR_ITEMS}}
<div class="basket-alert-wrap">
    <?= Html::beginTag('div', [
        'class' => [
            'basket-alert'
        ],
        'data-entity' => 'basket-item-sku-notification'
    ]) ?>
    {{#USE_FILTER}}
    <div class="basket-alert-interactive intec-cl-text" data-entity="basket-item-show-similar-link">
                <span class="basket-alert-interactive-text">
                    <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_OFFERS_SIMILAR_PART_1') ?>
                </span>
    </div>
    <span class="basket-alert-text">
                <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_OFFERS_SIMILAR_PART_2').' {{SIMILAR_ITEMS_QUANTITY}} {{MEASURE_TEXT}}.' ?>
            </span>
    {{/USE_FILTER}}
    <div class="basket-alert-interactive intec-cl-text" data-entity="basket-item-merge-sku-link">
            <span class="basket-alert-icon">
                <?= FileHelper::getFileData(__DIR__.'/../../svg/alert.combine.svg') ?>
            </span>
        <span class="basket-alert-interactive-text">
                <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_OFFERS_SIMILAR_PART_3') ?>
            </span>
    </div>
    <?= Html::endTag('div') ?>
</div>
{{/HAS_SIMILAR_ITEMS}}