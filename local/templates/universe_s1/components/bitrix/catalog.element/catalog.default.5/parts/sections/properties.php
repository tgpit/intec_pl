<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Type;

/**
 * @var array $arResult
 */

$iPropertyIndex = 0;

?>
<div class="catalog-element-properties-detail">
    <?php foreach ($arResult['DISPLAY_PROPERTIES'] as $arProperty) {
        $iPropertyIndex++;
    ?>
        <div class="catalog-element-properties-detail-item" data-code="<?= ($iPropertyIndex % 2 == 0) ? 'even' : 'odd' ?>">
            <div class="intec-grid intec-grid-a-v-center intec-grid-i-4 intec-grid-500-wrap">
                <div class="intec-grid-item-2 intec-grid-item-500-1">
                    <div class="catalog-element-properties-detail-item-name">
                        <?= $arProperty['NAME'] ?>
                    </div>
                </div>
                <div class="intec-grid-item-2 intec-grid-item-500-1">
                    <div class="catalog-element-properties-detail-item-value">
                        <?php if (Type::isArray($arProperty['DISPLAY_VALUE'])) { ?>
                            <?= implode(', ', $arProperty['DISPLAY_VALUE']) ?>
                        <?php } else { ?>
                            <?= $arProperty['DISPLAY_VALUE'] ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ($arVisual['OFFERS']['PROPERTIES']['SHOW'] && !empty($arResult['FIELDS']['OFFERS'])) { ?>
        <?php foreach ($arResult['FIELDS']['OFFERS'] as $sKey => $arOffer) {
            $iPropertyOfferIndex = $iPropertyIndex;
        ?>
            <div class="catalog-element-properties-detail-offer-container" data-offer="<?= $sKey ?>" data-role="offers.properties">
                <?php foreach ($arOffer as $arProperty) {
                    $iPropertyOfferIndex++;
                ?>
                    <div class="catalog-element-properties-detail-item" data-code="<?= ($iPropertyOfferIndex % 2 == 0) ? 'even' : 'odd' ?>">
                        <div class="intec-grid intec-grid-a-v-center intec-grid-i-4 intec-grid-500-wrap">
                            <div class="intec-grid-item-2 intec-grid-item-500-1">
                                <div class="catalog-element-properties-detail-item-name">
                                    <?= $arProperty['NAME'] ?>
                                </div>
                            </div>
                            <div class="intec-grid-item-2 intec-grid-item-500-1">
                                <div class="catalog-element-properties-detail-item-value">
                                    <?php if (Type::isArray($arProperty['VALUE'])) { ?>
                                        <?= implode(', ', $arProperty['VALUE']) ?>
                                    <?php } else { ?>
                                        <?= $arProperty['VALUE'] ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>
</div>
<?php unset($arProperty, $sKey, $arOffer) ?>