<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 */

$sLinkStatus = 'none';

if (!empty($arResult['SECTIONS']['PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES']) > $arVisual['PROPERTIES']['PREVIEW']['COUNT'])
    $sLinkStatus = 'main';

$arShowAllOffers = [];
?>

<div class="catalog-element-properties" data-print="false">
    <?php if ($arVisual['PROPERTIES']['PREVIEW']['SHOW']) { ?>
        <div class="intec-grid intec-grid-wrap intec-grid-i-h-10">
            <?php $iPropertyNumber = 1 ?>
            <?php foreach ($arResult['DISPLAY_PROPERTIES'] as $arProperty) {

                if ($iPropertyNumber > $arVisual['PROPERTIES']['PREVIEW']['COUNT'])
                    break;

                if (!empty($arProperty['USER_TYPE']))
                    continue;
            ?>
                <div class="intec-grid-item-2 intec-grid-item-400-1">
                    <div class="catalog-element-property">
                        <div class="catalog-element-property-name">
                            <?= $arProperty['NAME'] ?>
                        </div>
                        <div class="catalog-element-property-value">
                            <?= !Type::isArray($arProperty['DISPLAY_VALUE']) ?
                                $arProperty['DISPLAY_VALUE'] :
                                implode(', ', $arProperty['DISPLAY_VALUE'])
                            ?>
                        </div>
                    </div>
                </div>
                <?php $iPropertyNumber++ ?>
            <?php } ?>
            <?php unset($iPropertyNumber, $arProperty) ?>
        </div>
    <?php } ?>

    <?php if ($arVisual['OFFERS']['PROPERTIES']['SHOW'] &&
                !empty($arResult['OFFERS_PROPERTIES']) &&
                $arResult['SKU_VIEW'] != 'list') {

            foreach ($arResult['OFFERS_PROPERTIES'] as $sKey => $arOffer) {
                $iPropertyNumber = 1;
            ?>
                <div class="catalog-element-properties-offer-container" data-offer="<?= $sKey ?>" data-role="offers.properties">
                    <div class="intec-grid intec-grid-wrap intec-grid-i-h-10">
                        <?php foreach ($arOffer as $arProperty) {

                            if (!empty($arVisual['OFFERS']['PROPERTIES']['COUNT']) &&
                                $arVisual['OFFERS']['PROPERTIES']['COUNT'] > 0) {
                                if ($iPropertyNumber > $arVisual['OFFERS']['PROPERTIES']['COUNT']) {
                                    if ($sLinkStatus === 'none')
                                        $sLinkStatus = 'offers';

                                    $arShowAllOffers[] = $sKey;

                                    break;
                                }
                            }
                        ?>
                            <div class="intec-grid-item-2 intec-grid-item-400-1">
                                <div class="catalog-element-property">
                                    <div class="catalog-element-property-name">
                                        <?= $arProperty['NAME'] ?>
                                    </div>
                                    <div class="catalog-element-property-value">
                                        <?= !Type::isArray($arProperty['VALUE']) ?
                                            $arProperty['VALUE'] :
                                            implode(', ', $arProperty['VALUE'])
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php $iPropertyNumber++; ?>
                        <?php } ?>
                    </div>
                </div>
            <?php unset($iPropertyNumber) ?>
        <?php } ?>
    <?php } ?>
    <?php if ($sLinkStatus !== 'none') { ?>
        <a class="catalog-element-properties-all" data-print="false" data-role="show.all" data-status="<?= $sLinkStatus ?>">
            <span class="catalog-element-properties-all-text">
                <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_PROPERTIES_ALL') ?>
            </span>
            <span class="catalog-element-properties-all-icon">
                <i class="far fa-chevron-right"></i>
            </span>
        </a>
    <?php } ?>
</div>