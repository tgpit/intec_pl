<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arVisual
 */

$arDescription = [
    'SHOW' => false,
    'VALUE' => null,
    'OFFERS' => []
];

$sMod = strtoupper($arVisual['DESCRIPTION']['MODE'].'_TEXT');

if (!empty($arResult[$sMod]))
    $arDescription['VALUE'] = $arResult[$sMod];

$arDescription['SHOW'] = !empty($arDescription['VALUE']);

if ($arVisual['OFFERS']['DESCRIPTION']['SHOW']) {
    foreach ($arResult['OFFERS'] as $arOffer) {
        $sDescription = $arOffer[$sMod];

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


unset($sMod);
?>

<?php if (!empty($arDescription['OFFERS'])) { ?>
    <?php foreach ($arDescription['OFFERS'] as $sKey => $sDescription) { ?>
        <div class="catalog-element-section-content-wrapper" data-offer="<?= $sKey ?>">
            <?= $sDescription ?>
        </div>
    <?php } ?>
<?php } else { ?>
    <div class="catalog-element-section-content-wrapper">
        <?= $arDescription['VALUE'] ?>
    </div>
<?php } ?>