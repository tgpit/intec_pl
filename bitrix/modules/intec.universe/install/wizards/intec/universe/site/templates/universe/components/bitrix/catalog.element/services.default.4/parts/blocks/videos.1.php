<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arBlock
 */

?>
<div class="catalog-element-videos">
	<?php $APPLICATION->IncludeComponent(
		'intec.universe:main.videos',
        $arBlock['TEMPLATE'],
		[
			'IBLOCK_TYPE' => $arBlock['IBLOCK']['TYPE'],
			'IBLOCK_ID' => $arBlock['IBLOCK']['ID'],
			'FILTER' => [
				'ID' => $arBlock['IBLOCK']['ELEMENTS']
			],
			'ELEMENTS_COUNT' => '',
			'PICTURE_SOURCES' => [
				'service',
				'preview',
				'detail',
			],
			'PROPERTY_URL' => $arBlock['IBLOCK']['PROPERTIES']['LINK'],
			'HEADER_SHOW' => 'Y',
			'HEADER_POSITION' => $arBlock['HEADER']['POSITION'],
			'HEADER' => $arBlock['HEADER']['VALUE'],
			'DESCRIPTION_SHOW' => 'Y',
			'DESCRIPTION' => $arBlock['DESCRIPTION']['VALUE'],
            'DESCRIPTION_POSITION' => $arBlock['DESCRIPTION']['POSITION'],
			'SLIDER_USE' => 'Y',
			'SLIDER_NAV' => 'Y',
			'FOOTER_SHOW' => 'N',
			'CACHE_TYPE' => 'N',
			'SORT_BY' => 'sort',
			'ORDER_BY' => 'asc',
            'SETTINGS_USE' => 'N',
            'LAZYLOAD_USE' => $arResult['LAZYLOAD']['USE'] ? 'Y' : 'N',
            'COLUMNS' => '4',
            'NAME_SHOW' => 'Y',
            'LIST_VIEW' => 'big',
		],
		$component
	) ?>
</div>