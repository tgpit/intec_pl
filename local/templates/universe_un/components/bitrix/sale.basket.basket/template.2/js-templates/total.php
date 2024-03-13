<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

?>
<script id="basket-total-template" type="text/html">
    <?= Html::beginTag('div', [
        'class' => 'basket-total-container',
        'data-entity' => 'basket-checkout-aligner'
    ]) ?>
    <div class="basket-total-wrapper intec-grid intec-grid-1024-wrap">
        <div class="basket-total-info intec-grid-item intec-grid-item-1024-1">
            {{#WEIGHT_FORMATED}}
                <div class="basket-total-info-item">
                    <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_TOTAL_WEIGHT').'<span>{{{WEIGHT_FORMATED}}}</span>' ?>
                </div>
            {{/WEIGHT_FORMATED}}
            {{#VOLUME_FORMATTED}}
                <div class="basket-total-info-item">
                    <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_TOTAL_VOLUME').'<span>{{{VOLUME_FORMATTED}}}</span>' ?>
                </div>
            {{/VOLUME_FORMATTED}}
            <?php if ($arParams['HIDE_COUPON'] !== 'Y') { ?>
                <div class="basket-coupon" data-print="false">
                    <?php include(__DIR__.'/total/coupon.field.php') ?>
                    <?php include(__DIR__.'/total/coupon.message.php') ?>
                </div>
            <?php } ?>
            <div class="basket-total-print-wrap">
                <?= Html::beginTag('div', [
                    'class' => [
                        'basket-total-print',
                        'intec-ui' => [
                            '',
                            'control-button'
                        ]
                    ],
                    'data-role' => 'print'
                ]) ?>
                    <span class="basket-total-print-icon intec-ui-part-icon">
                        <?= FileHelper::getFileData(__DIR__.'/../svg/panel.button.print.svg') ?>
                    </span>
                    <span class="basket-total-print-text intec-ui-part-icon">
                        <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_PRINT_TEXT') ?>
                    </span>
                <?= Html::endTag('div') ?>
            </div>
        </div>
            <?= Html::beginTag('div', [
                'class' => [
                    'basket-price-wrap',
                    'intec-grid' => [
                        '',
                        'a-h-end',
                        'a-h-1024-start',
                        '768-wrap',
                        'item-auto',
                        'item-1024-1'
                    ]
                ]
            ]) ?>
                <?php include(__DIR__.'/total/total.price.php') ?>
                <?php include(__DIR__.'/total/total.order.php') ?>
            <?= Html::endTag('div') ?>
    </div>
    <?= Html::endTag('div') ?>
</script>