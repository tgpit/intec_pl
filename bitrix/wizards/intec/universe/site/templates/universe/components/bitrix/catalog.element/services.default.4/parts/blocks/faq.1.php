<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arBlock
 */

?>
<div class="catalog-element-faq">
	<?php $APPLICATION->IncludeComponent(
		'intec.universe:main.faq',
		'template.1',
		[
			'IBLOCK_TYPE' => $arBlock['IBLOCK']['TYPE'],
			'IBLOCK_ID' => $arBlock['IBLOCK']['ID'],
			'SECTIONS' => [],
			'FILTER' => [
				'ID' => $arBlock['IBLOCK']['ELEMENTS']
			],
			'ELEMENTS_COUNT' => '',
			'PROPERTY_EXPANDED' => '',
			'HEADER_SHOW' => 'Y',
			'HEADER_POSITION' => $arBlock['HEADER']['POSITION'],
			'HEADER_TEXT' => $arBlock['HEADER']['VALUE'],
			'DESCRIPTION_SHOW' => 'N',
			'FOOTER_SHOW' => 'N',
			'CACHE_TYPE' => 'A',
			'CACHE_TIME' => '0',
			'SORT_BY' => 'SORT',
			'ORDER_BY' => 'ASC',
			'HIDE' => 'Y',
			'SETTINGS_USE' => 'N',
			'LAZYLOAD_USE' => $arResult['LAZYLOAD']['USE'] ? 'Y' : 'N'
		],
		$component
	) ?>
</div>