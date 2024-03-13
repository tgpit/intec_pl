<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arBlock
 */

?>
<!--noindex-->
<div class="catalog-element-brands">
	<?php $APPLICATION->IncludeComponent(
		'intec.universe:main.brands',
		'template.4',
		[
			'IBLOCK_TYPE' => $arBlock['IBLOCK']['TYPE'],
			'IBLOCK_ID' => $arBlock['IBLOCK']['ID'],
			'FILTER' => [
				'ID' => $arBlock['IBLOCK']['ELEMENTS']
			],
			'ELEMENTS_COUNT' => '',
			'MODE' => 'N',
			'SECTIONS' => [],
			'POSITION_SHOW' => 'Y',
			'PROPERTY_POSITION' => $arBlock['IBLOCK']['PROPERTIES']['POSITION'],
			'HEADER_SHOW' => 'Y',
			'HEADER_POSITION' => $arBlock['HEADER']['POSITION'],
			'HEADER_TEXT' => $arBlock['HEADER']['VALUE'],
            'DESCRIPTION_SHOW' => 'Y',
            'DESCRIPTION_POSITION' => 'left',
            'DESCRIPTION_TEXT' => $arBlock['DESCRIPTION'],
			'LINK_USE' => 'N',
			'SLIDER_LOOP' => 'N',
			'SLIDER_AUTO_USE' => 'N',
			'FOOTER_SHOW' => 'Y',
			'LIST_PAGE_URL' => $arBlock['PAGE'],
			'SECTION_URL' => '',
			'DETAIL_URL' => '',
			'CACHE_TYPE' => 'N',
			'SORT_BY' => 'SORT',
			'ORDER_BY' => 'ASC',
			'FOOTER_POSITION' => 'center',
			'SHOW_ALL_BUTTON_SHOW' => $arBlock['BUTTON']['SHOW'] ? 'Y' : 'N',
			'SHOW_ALL_BUTTON_TEXT' => $arBlock['BUTTON']['TEXT'],
            'SHOW_ALL_BUTTON_POSITION' => "left",
            'SHOW_ALL_BUTTON_DISPLAY' => "none",
            'SHOW_ALL_BUTTON_BORDER' => "rectangular",
            'SETTINGS_USE' => 'N',
            'LAZYLOAD_USE' => $arResult['LAZYLOAD']['USE'] ? 'Y' : 'N',

            "LINE_COUNT" => "4",
            "ALIGNMENT" => "center",
            "EFFECT" => "none",
            "TRANSPARENCY" => "0",
            "COLUMNS" => "4",
            "GRAYSCALE" => "N",
            "EFFECT_PRIMARY" => "shadow",
            "EFFECT_SECONDARY" => "grayscale",
            "BORDER_SHOW" => "N"
		],
		$component
	) ?>
</div>
<!--/noindex-->
