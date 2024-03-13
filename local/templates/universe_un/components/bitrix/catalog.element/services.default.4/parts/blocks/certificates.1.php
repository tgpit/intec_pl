<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arBlock
 */

?>
<div class="catalog-element-certificates">
    <?php $APPLICATION->IncludeComponent(
        'intec.universe:main.certificates',
        'template.2',
        array(
            'IBLOCK_TYPE' => $arBlock['IBLOCK']['TYPE'],
            'IBLOCK_ID' => $arBlock['IBLOCK']['ID'],
            'FILTER' => [
                'ID' => $arBlock['IBLOCK']['ELEMENTS']
            ],
            'ELEMENTS_COUNT' => '',
            'SETTINGS_USE' => 'N',
            'LAZYLOAD_USE' => 'N',
            'HEADER_SHOW' => 'Y',
            'DESCRIPTION_SHOW' => 'N',
            'LINE_COUNT' => '4',
            'ALIGNMENT' => 'center',
            'NAME_SHOW' => 'Y',
            'FOOTER_SHOW' => 'N',
            'LIST_PAGE_URL' => '',
            'CACHE_TYPE' => 'N',
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
            'COLUMNS' => '5',
            'WIDE' => 'N',
            'INDENT_USE' => 'N',
            'TABS_USE' => 'N',
            'SLIDER_USE' => 'Y',
            'SLIDER_NAV' => 'Y',
            'SLIDER_DOTS' => 'N',
            'SLIDER_LOOP' => 'N',
            'SLIDER_AUTO_USE' => 'N',
            'HEADER_POSITION' => $arBlock['HEADER']['POSITION'],
            'HEADER_TEXT' => $arBlock['HEADER']['VALUE'],
            'SLIDER_CENTER' => 'N'
        ),
        $component
    ); ?>
</div>