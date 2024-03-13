<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arVisual
 */

?>
<?php $vSku = function ($arSku) use (&$arVisual) { ?>
    <div class="widget-item-offers-properties">
        <?php foreach ($arSku as $arProperty) { ?>
            <?= Html::beginTag('div', [
                'class' => 'widget-item-offers-property',
                'data' => [
                    'role' => 'item.property',
                    'property' => $arProperty['code'],
                    'type' => $arProperty['type'],
                    'visible' => 'false'
                ]
            ]) ?>
                <div class="widget-item-offers-property-header intec-grid intec-grid-a-v-center intec-grid-wrap intec-grid-i-4">
                    <div class="widget-item-offers-property-title intec-grid-item-auto">
                        <?= $arProperty['name'] ?>
                    </div>
                    <div class="widget-item-offers-property-value-selected intec-grid-item-auto" data-role="item.property.value.selected">
                    </div>
                </div>
                <div class="intec-grid intec-grid-wrap intec-grid-a-v-center">
                    <div class="intec-grid-item-auto intec-grid-item-shrink-1 widget-item-offers-property-values" data-role="offers.values">
                        <?php
                            $sPropCounterFirst = 0;
                            $sPropCounterSecond = 0;
                        ?>
                        <?php foreach ($arProperty['values'] as $arValue) { ?>
                            <?php
                                if ($sPropCounterFirst < 4) {
                                    $sPropCounterFirst++;
                                }
                            ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'widget-item-offers-property-value',
                                    'intec-cl-border-hover'
                                ],
                                'data' => [
                                    'role' => 'item.property.value',
                                    'state' => 'hidden',
                                    'value' => $arValue['id'],
                                    'hidden-more' => count($arProperty['values']) > 4 && $sPropCounterSecond > 0 ? 'true' : 'false',
                                    'prop-visibility' =>  count($arProperty['values']) > 4 && $sPropCounterSecond > 0 ? 'false' : 'true'
                                ]
                            ]) ?>
                                <?php if ($arProperty['type'] === 'picture' && !empty($arValue['picture'])) { ?>
                                    <?= Html::beginTag('div', [
                                        'class' => 'widget-item-offers-property-value-image',
                                        'data' => [
                                            'role' => 'item.property.value.image',
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $arValue['picture'] : null
                                        ],
                                        'style' => [
                                            'background-image' => $arVisual['LAZYLOAD']['USE'] ? null : 'url(\''.$arValue['picture'].'\')'
                                        ],
                                        'title' => $arValue['name']
                                    ]) ?>
                                        <div data-role="item.property.value.name">
                                            <?= $arValue['name'] ?>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                <?php } else { ?>
                                    <div class="widget-item-offers-property-value-text" data-role="item.property.value.name">
                                        <?= $arValue['name'] ?>
                                    </div>
                                <?php } ?>
                            <?= Html::endTag('div') ?>
                            <?php
                                if ($sPropCounterFirst >= 4) {
                                    $sPropCounterSecond++;
                                }
                            ?>
                        <?php } ?>
                    </div>
                    <?php if (count($arProperty['values']) > 4) {
                        $sPropCounterSecond--;
                    ?>
                        <div class="intec-grid-item-auto intec-grid-item-shrink-1 intec-cl-text-hover intec-cl-border-hover widget-item-offers-property-more" data-role="show.more.offers.props" data-visibility="true">
                            <?= Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_OFFERS_PROPERTY_MORE').$sPropCounterSecond ?>
                        </div>
                    <?php } ?>
                </div>
            <?= Html::endTag('div') ?>
        <?php } ?>
    </div>
<?php } ?>