<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arBlock
 */

?>
<div class="catalog-element-stages-2" data-background-use="<?= $arBlock['BACKGROUND_USE'] ? 'true' : 'false' ?>">
    <?php $APPLICATION->IncludeComponent(
        "intec.universe:main.stages",
        "template.5",
        array(
            'IBLOCK_TYPE' => $arBlock['IBLOCK']['TYPE'],
            'IBLOCK_ID' => $arBlock['IBLOCK']['ID'],
            'FILTER' => [
                'ID' => $arBlock['IBLOCK']['ELEMENTS']
            ],
            'SECTIONS' => [
                0 => '',
                1 => '',
            ],
            'ELEMENTS_COUNT' => '',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => $arBlock['HEADER']['POSITION'],
            'HEADER' => $arBlock['HEADER']['VALUE'],
            'DESCRIPTION_SHOW' => 'Y',
            'DESCRIPTION_POSITION' => 'center',
            'DESCRIPTION' => '',
            'COUNT_SHOW' => 'Y',
            'ELEMENT_DESCRIPTION_SHOW' => 'Y',
            'CACHE_TYPE' => 'N',
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
            'LINE_COUNT' => '4',
            'ELEMENT_SHOW_DESCRIPTION' => 'Y',
            'PROPERTY_TIME' => '',
            'PROPERTY_TEXT_SOURCE' => 'preview',
            'ELEMENT_NAME_SIZE' => 'big',
            'BY_SECTION' => 'N',
            'VIEW' => $arBlock['VIEW'],
            'COLUMNS' => '4'
        ),
        $component
    ); ?>
</div>