<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arBlock
 */

?>
<div class="catalog-element-icons">
    <?php $APPLICATION->IncludeComponent(
        'intec.universe:main.advantages',
        'template.10',
        ArrayHelper::merge($arBlock['PARAMETERS'], [
            'IBLOCK_TYPE' => $arBlock['IBLOCK']['TYPE'],
            'IBLOCK_ID' => $arBlock['IBLOCK']['ID'],
            'FILTER' => [
                'ID' => $arBlock['IBLOCK']['ELEMENTS']
            ],
            'SECTIONS_MODE' => 'id',
            'SECTIONS' => [
                0 => "",
                1 => "",
            ],
            'ELEMENTS_COUNT' => '',
            'PICTURE_SHOW' => 'Y',
            'PREVIEW_SHOW' => 'Y',
            'COLUMNS' => '5',
            'SETTINGS_USE' => 'N',
            'LAZYLOAD_USE' => $arResult['LAZYLOAD']['USE'] ? 'Y' : 'N',
            'HEADER_SHOW' => 'N',
            'DESCRIPTION_SHOW' => 'N',
            'VIEW' => 'icon',
            'CACHE_TYPE' => 'N',
            'SVG_USE' => $arBlock['IBLOCK']['SVG_USE'],
            'SVG_PROPERTY' => $arBlock['IBLOCK']['SVG_PROPERTY']
        ]),
        $component
    ) ?>
</div>
