<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arVisual
 * @var array $arSkuExtended
 */

?>
<?php return function ($arSku) use (&$arVisual, &$arSkuExtended) { ?>
    <div class="widget-item-offers-properties">
        <?php foreach ($arSku as $arProperty) {

            $bExtended = false;

            if ($arVisual['OFFERS']['VIEW'] === 'extended' && !empty($arSkuExtended))
                foreach ($arSkuExtended as $arPropertyExtended)
                    if ($arProperty['code'] === $arPropertyExtended['code'])
                        $bExtended = true;

        ?>
            <?= Html::beginTag('div', [
                'class' => 'widget-item-offers-property',
                'data' => [
                    'role' => 'item.property',
                    'property' => $arProperty['code'],
                    'type' => $arProperty['type'],
                    'extended' => $bExtended ? 'true' : 'false',
                    'visible' => 'false'
                ]
            ]) ?>
                <div class="widget-item-offers-property-title" data-align="<?= $arVisual['OFFERS']['ALIGN'] ?>">
                    <?= $arProperty['name'] ?>
                </div>
                <div class="widget-item-offers-property-values" data-align="<?= $arVisual['OFFERS']['ALIGN'] ?>">
                    <?php foreach ($arProperty['values'] as $arValue) { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'widget-item-offers-property-value',
                                'intec-cl-border-hover'
                            ],
                            'data' => [
                                'role' => 'item.property.value',
                                'state' => 'hidden',
                                'value' => $arValue['id']
                            ]
                        ]) ?>
                            <?php if ($arProperty['type'] === 'picture' && !empty($arValue['picture'])) { ?>
                                <?= Html::beginTag('div', [
                                    'class' => 'widget-item-offers-property-value-image',
                                    'data-role' => 'item.property.value.image',
                                    'style' => [
                                        'background-image' => 'url('.$arValue['picture'].')'
                                    ]
                                ]) ?>
                                    <i class="far fa-check"></i>
                                <?= Html::endTag('div') ?>
                            <?php } else { ?>
                                <div class="widget-item-offers-property-value-text">
                                    <?= $arValue['name'] ?>
                                </div>
                            <?php } ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                </div>
            <?= Html::endTag('div') ?>
        <?php } ?>
    </div>
<?php } ?>