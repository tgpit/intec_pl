<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<?php return function (&$arSkuExtended) use (&$arVisual) { ?>
    <?php foreach ($arSkuExtended as $sKey => $arProperty) {

        if (empty($arProperty))
            continue;

    ?>
        <?= Html::beginTag('div', [
            'class' => [
                'widget-item-offers-property-extended',
                'intec-grid' => [
                    '',
                    'a-v-center'
                ]
            ],
            'data' => [
                'role' => 'item.property',
                'property' => $arProperty['code'],
                'type' => $arProperty['type'],
                'side' => strtolower($sKey),
                'visible' => 'false'
            ]
        ]) ?>
            <div class="widget-item-offers-property-extended-values intec-grid-item">
                <?php foreach ($arProperty['values'] as $arValue) { ?>
                    <?= Html::beginTag('div', [
                        'class' => 'widget-item-offers-property-extended-value',
                        'data' => [
                            'role' => 'item.property.value',
                            'state' => 'hidden',
                            'value' => $arValue['id']
                        ]
                    ]) ?>
                        <?php if ($arProperty['type'] === 'picture' && !empty($arValue['picture'])) { ?>
                            <?= Html::tag('div', '', [
                                'class' => [
                                    'widget-item-offers-property-extended-value-image',
                                    'intec-cl-border-hover'
                                ],
                                'data-role' => 'item.property.value.image',
                                'style' => [
                                    'background-image' => 'url(\''.$arValue['picture'].'\')'
                                ]
                            ]) ?>
                            <div class="widget-item-offers-property-extended-value-overlay"></div>
                        <?php } else { ?>
                            <div class="widget-item-offers-property-extended-value-text">
                                <?= $arValue['name'] ?>
                            </div>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
<?php } ?>
