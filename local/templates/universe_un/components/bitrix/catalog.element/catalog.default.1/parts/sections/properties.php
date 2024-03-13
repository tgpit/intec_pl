<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Type;

/**
 * @var array $arResult
 */

?>
<div class="catalog-element-section-properties" data-role="offers.properties.detail">
    <?php if ($arVisual['PROPERTIES']['DETAIL']['PRODUCT']['SHOW'] && !$arVisual['PROPERTIES']['DETAIL']['OFFERS']['SHOW']) { ?>
        <?php foreach ($arResult['DISPLAY_PROPERTIES'] as $arProperty) { ?>
            <div class="catalog-element-section-property">
                <div class="intec-grid intec-grid-nowrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-10">
                    <div class="intec-grid-item-2">
                        <div class="catalog-element-section-property-name">
                            <?= $arProperty['NAME'] ?>
                        </div>
                    </div>
                    <div class="intec-grid-item-2">
                        <div class="catalog-element-section-property-value">
                            <?= !Type::isArray($arProperty['DISPLAY_VALUE']) ?
                                $arProperty['DISPLAY_VALUE'] :
                                implode(', ', $arProperty['DISPLAY_VALUE'])
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
    <?php if ($arVisual['PROPERTIES']['DETAIL']['OFFERS']['SHOW']) { ?>
        <?php foreach ($arResult['OFFERS_PROPERTIES'] as $sKey => $arOffer) { ?>
            <div class="catalog-element-properties-offer-container" data-offer="<?= $sKey ?>">
                <?php foreach ($arOffer as $arProperty) { ?>
                    <div class="catalog-element-section-property">
                        <div class="intec-grid intec-grid-nowrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-10">
                            <div class="intec-grid-item-2">
                                <div class="catalog-element-section-property-name">
                                    <?= $arProperty['NAME'] ?>
                                </div>
                            </div>
                            <div class="intec-grid-item-2">
                                <div class="catalog-element-section-property-value">
                                    <?= !Type::isArray($arProperty['VALUE']) ?
                                        $arProperty['VALUE'] :
                                        implode(', ', $arProperty['VALUE'])
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>
    <?php unset($arProperty) ?>
</div>