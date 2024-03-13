<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arBlock
 */

?>
<div class="catalog-element-advantages">
    <?php $APPLICATION->IncludeComponent(
        "intec.universe:main.advantages",
        "template.36",
        array(
            'IBLOCK_TYPE' => $arBlock['IBLOCK']['TYPE'],
            'IBLOCK_ID' => $arBlock['IBLOCK']['ID'],
            'FILTER' => [
                'ID' => $arBlock['IBLOCK']['ELEMENTS']
            ],
            'SECTIONS_MODE' => 'id',
            'SECTIONS' => array(
                0 => '',
                1 => '',
            ),
            'ELEMENTS_COUNT' => '',
            'SETTINGS_USE' => 'N',
            'LAZYLOAD_USE' => $arResult['LAZYLOAD']['USE'] ? 'Y' : 'N',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => $arBlock['HEADER']['POSITION'],
            'HEADER' => $arBlock['HEADER']['VALUE'],
            'DESCRIPTION_SHOW' => 'N',
            'PICTURE_SHOW' => 'Y',
            'PREVIEW_SHOW' => 'Y',
            'CACHE_TYPE' => 'N',
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ),
        $component
    );?>
</div>
