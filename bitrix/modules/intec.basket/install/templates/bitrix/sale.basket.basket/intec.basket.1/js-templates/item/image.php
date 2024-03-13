<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arParams
 */

$arPosition = [
    'BADGES' => !empty($arParams['LABEL_PROP_POSITION']) ? explode('-', $arParams['LABEL_PROP_POSITION']) : ['top', 'left'],
    'PERCENT' => !empty($arParams['DISCOUNT_PERCENT_POSITION']) ? explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) : ['top', 'right']
];

?>
<div class="intec-basket-preview">
    <div class="intec-basket-preview-picture">
        {{#DETAIL_PAGE_URL}}
            <a class="intec-basket-preview-url intec-basket-picture" href="{{DETAIL_PAGE_URL}}" target="_blank">
        {{/DETAIL_PAGE_URL}}
        <img src="{{{IMAGE_URL}}}{{^IMAGE_URL}}'<?= SITE_TEMPLATE_PATH.'/images/picture.missing.png' ?>{{/IMAGE_URL}}" alt="{{NAME}}" title="{{NAME}}" loading="lazy">
        {{#DETAIL_PAGE_URL}}
            </a>
        {{/DETAIL_PAGE_URL}}
    </div>
    {{#SHOW_LABEL}}
        <div class="intec-basket-badges" data-mobile-hidden="true" data-x="<?= $arPosition['BADGES'][1] ?>" data-y="<?= $arPosition['BADGES'][0] ?>">
            {{#LABEL_VALUES}}
                <div class="intec-basket-badges-item">
                    <div class="intec-basket-badges-item-content">
                        {{NAME}}
                    </div>
                </div>
            {{/LABEL_VALUES}}
        </div>
    {{/SHOW_LABEL}}
    <?php if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y') { ?>
        {{#DISCOUNT_PRICE_PERCENT}}
            <div class="intec-basket-badges" data-x="<?= $arPosition['PERCENT'][1] ?>" data-y="<?= $arPosition['PERCENT'][0] ?>">
                <div class="intec-basket-badges-item">
                    <div class="intec-basket-badges-item-content">
                        -{{DISCOUNT_PRICE_PERCENT_FORMATED}}
                    </div>
                </div>
            </div>
        {{/DISCOUNT_PRICE_PERCENT}}
    <?php } ?>
</div>
<?php unset($arPosition) ?>