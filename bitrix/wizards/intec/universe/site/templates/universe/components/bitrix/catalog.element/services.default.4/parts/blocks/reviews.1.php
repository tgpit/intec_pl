<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arBlock
 */

?>
<!--noindex-->
<div class="catalog-element-reviews">
    <?php $APPLICATION->IncludeComponent(
        'intec.universe:main.reviews',
        'template.16',
        array(
            'IBLOCK_TYPE' => $arBlock['IBLOCK']['TYPE'],
            'IBLOCK_ID' => $arBlock['IBLOCK']['ID'],
            'FILTER' => [
                'ID' => $arBlock['IBLOCK']['ELEMENTS']
            ],
            'ELEMENTS_COUNT' => '',
            'SECTIONS_MODE' => 'id',
            'SECTIONS' => [
                0 => '',
                1 => '',
            ],
            'SETTINGS_USE' => 'N',
            'LAZYLOAD_USE' => $arResult['LAZYLOAD']['USE'] ? 'Y' : 'N',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => $arBlock['HEADER']['POSITION'],
            'HEADER_TEXT' => $arBlock['HEADER']['VALUE'],
            'DESCRIPTION_SHOW' => 'N',
            'RATING_SHOW' => 'Y',
            'PROPERTY_RATING' => $arBlock['IBLOCK']['PROPERTIES']['RATING'],
            'RATING_MAX' => '5',
            'LINK_USE' => 'Y',
            'LINK_BLANK' => 'Y',
            'SLIDER_LOOP' => 'N',
            'SLIDER_AUTO_USE' => 'N',
            'ACTIVE_DATE_SHOW' => 'Y',
            'LIST_PAGE_URL' => $arBlock['PAGE'],
            'SECTION_URL' => '',
            'DETAIL_URL' => '',
            'CACHE_TYPE' => 'N',
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
            'ACTIVE_DATE_FORMAT' => 'd.m.Y',
            'BUTTON_ALL_SHOW' => $arBlock['BUTTON']['SHOW'] ? 'Y' : 'N',
            'BUTTON_ALL_TEXT' => $arBlock['BUTTON']['TEXT']
        ),
        $component
    );?>
</div>
<!--/noindex-->
