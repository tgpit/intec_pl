<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Type;

/**
 * @var array $arResult
 */

$sParity = (count($arResult['DISPLAY_PROPERTIES']) + 1) % 2 == 0 ? 'even' : 'odd';

?>
<div class="catalog-element-section-properties" data-role="offers.properties.detail">
    <?php foreach ($arResult['DISPLAY_PROPERTIES'] as $arProperty) { ?>
        <div class="catalog-element-section-property">
            <div class="catalog-element-section-property-name">
                <?= $arProperty['NAME'] ?>
            </div>
            <div class="catalog-element-section-property-value">
                <?= !Type::isArray($arProperty['DISPLAY_VALUE']) ?
                    $arProperty['DISPLAY_VALUE'] :
                    implode(', ', $arProperty['DISPLAY_VALUE'])
                ?>
            </div>
            <div class="intec-ui-clearfix"></div>
        </div>
    <?php } ?>
    <?php if ($arVisual['OFFERS']['PROPERTIES']['SHOW'] && !empty($arResult['OFFERS_PROPERTIES'])) { ?>
        <?php foreach ($arResult['OFFERS_PROPERTIES'] as $sKey => $arOffer) { ?>
            <div class="catalog-element-properties-offer-container" data-parity="<?= $sParity ?>" data-offer="<?= $sKey ?>">
                <?php foreach ($arOffer as $arProperty) { ?>
                    <div class="catalog-element-section-property">
                        <div class="catalog-element-section-property-name">
                            <?= $arProperty['NAME'] ?>
                        </div>
                        <div class="catalog-element-section-property-value">
                            <?= !Type::isArray($arProperty['VALUE']) ?
                                $arProperty['VALUE'] :
                                implode(', ', $arProperty['VALUE'])
                            ?>
                        </div>
                        <div class="intec-ui-clearfix"></div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>
    <?php unset($arProperty) ?>
</div>