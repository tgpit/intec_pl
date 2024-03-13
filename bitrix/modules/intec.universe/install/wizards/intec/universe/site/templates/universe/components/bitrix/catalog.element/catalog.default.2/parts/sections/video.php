<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<div class="catalog-element-section-videos" data-print="false">
    <?php $APPLICATION->IncludeComponent(
        'intec.universe:main.videos',
        'template.1', [
            'IBLOCK_TYPE' => $arParams['VIDEO_IBLOCK_TYPE'],
            'IBLOCK_ID' => $arParams['VIDEO_IBLOCK_ID'],
            'PROPERTY_URL' => $arParams['VIDEO_PROPERTY_URL'],
            'FILTER' => [
                'ID' => $arResult['VIDEO']
            ],
            'PICTURE_SERVICE_QUALITY' => 'sddefault',
            'SLIDER_USE' => 'N',
            'HEADER_SHOW' => 'N',
            'DESCRIPTION_SHOW' => 'N',
            'FOOTER_SHOW' => 'N',
            'CONTENT_POSITION' => 'left',
            'COLUMNS' => !(
                $arVisual['VIEW']['VALUE'] === 'narrow' &&
                $arVisual['GALLERY']['SHOW']
            ) ? 3 : 1
        ],
        $component
    ) ?>
</div>