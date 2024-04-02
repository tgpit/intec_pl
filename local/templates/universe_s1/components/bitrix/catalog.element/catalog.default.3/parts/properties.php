<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Type;

/**
 * @var array $arResult
 */

?>
<?php if (!empty($arResult['DISPLAY_PROPERTIES'])) { ?>
    <?php foreach ($arResult['DISPLAY_PROPERTIES'] as $arProperty) { ?>
        <div class="catalog-element-property">
            <span class="catalog-element-property-name">
                <?= $arProperty['NAME'] . ':' ?>
            </span>
            <span class="catalog-element-property-value">
                <?= !Type::isArray($arProperty['DISPLAY_VALUE']) ?
                    $arProperty['DISPLAY_VALUE'] :
                    implode(', ', $arProperty['DISPLAY_VALUE'])
                ?>
            </span>
        </div>
    <?php } ?>
<?php } ?>
<?php if ($arVisual['OFFERS']['PROPERTIES']['SHOW'] && !empty($arResult['OFFERS_PROPERTIES'])) { ?>
    <?php foreach ($arResult['OFFERS_PROPERTIES'] as $sKey => $arOffer) { ?>
        <?php $iCounter = 0 ?>
        <div class="catalog-element-properties-offer-container" data-offer="<?= $sKey ?>" data-role="offers.properties">
            <?php foreach ($arOffer as $arProperty) {

                $iCounter++;

                if ($iCounter > $arVisual['OFFERS']['PROPERTIES']['COUNT'] && $arVisual['OFFERS']['PROPERTIES']['COUNT'] > 0)
                    break;

                ?>
                <div class="catalog-element-property">
                    <span class="catalog-element-property-name">
                        <?= $arProperty['NAME'] ?>
                    </span>
                    <span class="catalog-element-property-value">
                        <?= !Type::isArray($arProperty['VALUE']) ?
                            $arProperty['VALUE'] :
                            implode(', ', $arProperty['VALUE'])
                        ?>
                    </span>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <?php unset($iCounter) ?>
<?php } ?>
<?php unset($arProperty) ?>
