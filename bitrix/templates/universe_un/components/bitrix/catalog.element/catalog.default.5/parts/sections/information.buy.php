<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<div class="catalog-element-sections-content-text">
    <?php $APPLICATION->IncludeComponent(
        'bitrix:main.include',
        '', [
            'AREA_FILE_SHOW' => 'file',
            'PATH' => $arVisual['INFORMATION']['BUY']['PATH'],
            'EDIT_TEMPLATE' => ''
        ],
        $component
    ) ?>
</div>