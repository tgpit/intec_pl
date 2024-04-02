<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arResult
 * @var array $arVisual
 */

$arDescription = [
    'SHOW' => false,
    'VALUE' => null,
    'OFFERS' => []
];

if (!empty($arResult['DETAIL_TEXT']))
    $arDescription['VALUE'] = $arResult['DETAIL_TEXT'];
else if ($arVisual['DESCRIPTION']['DETAIL']['FROM_PREVIEW'] && !empty($arResult['PREVIEW_TEXT']))
    $arDescription['VALUE'] = $arResult['PREVIEW_TEXT'];

$arDescription['SHOW'] = !empty($arDescription['VALUE']);

if ($arVisual['OFFERS']['DESCRIPTION']['SHOW']) {
    foreach ($arResult['OFFERS'] as $arOffer) {
        $sDescription = $arOffer['DETAIL_TEXT'];

        if (empty($sDescription))
            $sDescription = $arDescription['VALUE'];

        if (!empty($sDescription)) {
            $arDescription['OFFERS'][$arOffer['ID']] = $sDescription;

            if (!$arDescription['SHOW'])
                $arDescription['SHOW'] = true;
        }
    }

    unset($sDescription);
}

if (!$arDescription['SHOW'])
    return;

if (empty($arVisual['DESCRIPTION']['DETAIL']['NAME']))
    $arVisual['DESCRIPTION']['DETAIL']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_ADDITIONAL_DESCRIPTION');

?>
<div class="catalog-element-description catalog-element-additional-block">
    <div class="catalog-element-description-wrapper">
        <div class="catalog-element-additional-block-name">
            <?= $arVisual['DESCRIPTION']['DETAIL']['NAME'] ?>
        </div>
        <?php if (!empty($arDescription['OFFERS'])) { ?>
            <?php foreach ($arDescription['OFFERS'] as $sKey => $sDescription) { ?>
                <div class="catalog-element-additional-block-content-text" data-offer="<?= $sKey ?>">
                    <?= $sDescription ?>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="catalog-element-additional-block-content-text">
                <?= $arDescription['VALUE'] ?>
            </div>
        <?php } ?>
    </div>
</div>
<?php unset($arDescription, $sKey, $sDescription) ?>