<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

$sSectionName = $arVisual['INFORMATION']['SHIPMENT']['NAME'];

if ($sSectionName)
    $sSectionName = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_ADDITIONAL_SHIPMENT');

?>
<div class="catalog-element-payment catalog-element-additional-block">
    <div class="catalog-element-additional-block-name">
        <?= $sSectionName ?>
    </div>
    <div class="catalog-element-additional-block-content-text">
        <?php $APPLICATION->IncludeComponent(
            'bitrix:main.include',
            '', [
                'AREA_FILE_SHOW' => 'file',
                'PATH' => $arVisual['INFORMATION']['SHIPMENT']['PATH'],
                'EDIT_TEMPLATE' => ''
            ],
            $component
        ) ?>
    </div>
</div>
<?php unset($sSectionName) ?>