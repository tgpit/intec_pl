<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var string $sTemplateId
 * @var array $arVisual
 */

?>
<div class="catalog-element-properties">
    <?php if ($arVisual['PROPERTIES']['PREVIEW']['PRODUCT']['SHOW']) { ?>
        <div class="intec-grid intec-grid-wrap intec-grid-i-h-25 intec-grid-i-v-5">
            <?php $iPropertyNumber = 1 ?>
            <?php foreach ($arResult['DISPLAY_PROPERTIES'] as $arProperty) {
                if ($iPropertyNumber > $arVisual['PROPERTIES']['PREVIEW']['PRODUCT']['COUNT'])
                    break;

                if (!empty($arProperty['USER_TYPE']))
                    continue;
            ?>
                <div class="intec-grid-item intec-grid-item-2 intec-grid-item-1000-1 catalog-element-property">
                    <span class="catalog-element-property-name">
                        <?= $arProperty['NAME'] ?> -
                    </span>
                    <span class="catalog-element-property-value">
                        <?= !Type::isArray($arProperty['DISPLAY_VALUE']) ?
                            $arProperty['DISPLAY_VALUE'] :
                            implode(', ', $arProperty['DISPLAY_VALUE'])
                        ?>;
                    </span>
                </div>
                <?php $iPropertyNumber++ ?>
            <?php } ?>
            <?php unset($iPropertyNumber, $arProperty) ?>
        </div>
    <?php } ?>
    <?php if ($arVisual['PROPERTIES']['PREVIEW']['OFFERS']['SHOW']) { ?>
        <?php foreach ($arResult['OFFERS_PROPERTIES'] as $sKey => $arOffer) { ?>
            <?php $iPropertyNumber = 1 ?>
            <div class="catalog-element-properties-offer-container" data-offer="<?= $sKey ?>" data-role="offers.properties">
                <div class="intec-grid intec-grid-wrap intec-grid-i-h-25 intec-grid-i-v-10">
                    <?php foreach ($arOffer as $arProperty) {
                        if ($iPropertyNumber > $arVisual['PROPERTIES']['PREVIEW']['OFFERS']['COUNT'])
                            break;
                    ?>
                        <div class="intec-grid-item intec-grid-item-2 intec-grid-item-1000-1 catalog-element-property">
                            <span class="catalog-element-property-name">
                                <?= $arProperty['NAME'] ?> -
                            </span>
                            <span class="catalog-element-property-value">
                                <?= !Type::isArray($arProperty['VALUE']) ?
                                    $arProperty['VALUE'] :
                                    implode(', ', $arProperty['VALUE'])
                                ?>;
                            </span>
                        </div>
                        <?php $iPropertyNumber++; ?>
                    <?php } ?>
                </div>
            </div>
            <?php unset($iPropertyNumber) ?>
        <?php } ?>
    <?php } ?>
    <?php if ($arVisual['PROPERTIES']['DETAIL']['PRODUCT']['SHOW'] || $arVisual['PROPERTIES']['DETAIL']['OFFERS']['SHOW']) { ?>
        <div class="catalog-element-properties-all intec-cl-text intec-cl-text-light-hover" data-role="show.all">
            <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_1_PROPERTIES_ALL') ?>
        </div>
    <?php } ?>
</div>