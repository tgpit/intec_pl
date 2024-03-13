<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>

<div class="basket-item-image intec-image-effect">
    <?php if (!$arResult['QUICK_VIEW']['USE']) { ?>
        {{#DETAIL_PAGE_URL}}
        <?= Html::beginTag('a', [
            'class' => 'basket-item-image-link',
            'href' => '{{DETAIL_PAGE_URL}}'
        ]) ?>
        {{/DETAIL_PAGE_URL}}
    <? } else { ?>
        <?php include(__DIR__.'/../../svg/quick.view.button.icon.svg'); ?>
    <?php } ?>
        <?= Html::img('{{{IMAGE_URL}}}{{^IMAGE_URL}}'.SITE_TEMPLATE_PATH.'/images/picture.missing.png{{/IMAGE_URL}}') ?>
    <?php if (!$arResult['QUICK_VIEW']['USE']) { ?>
        {{#DETAIL_PAGE_URL}}
        <?= Html::endTag('a') ?>
        {{/DETAIL_PAGE_URL}}
    <?php } ?>
</div>