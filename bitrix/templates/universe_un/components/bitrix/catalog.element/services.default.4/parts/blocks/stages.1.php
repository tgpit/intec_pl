<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arBlock
 */

?>
<div class="catalog-element-stages-1-wrap intec-content">
    <div class="catalog-element-stages-1-wrap-2 intec-content-wrapper">
        <div class="catalog-element-stages-1">
        <?php $APPLICATION->IncludeComponent(
            'intec.universe:main.stages',
            'template.4',
            [
                'IBLOCK_TYPE' => $arBlock['IBLOCK']['TYPE'],
                'IBLOCK_ID' => $arBlock['IBLOCK']['ID'],
                'FILTER' => [
                    'ID' => $arBlock['IBLOCK']['ELEMENTS']
                ],
                'ELEMENTS_COUNT' => '',
                'PROPERTY_TIME' => '',
                'PROPERTY_TEXT_SOURCE' => 'preview',
                'HEADER_SHOW' => 'Y',
                'HEADER_POSITION' => $arBlock['HEADER']['POSITION'],
                'HEADER' => $arBlock['HEADER']['VALUE'],
                'CACHE_TYPE' => 'N',
                'SORT_BY' => 'SORT',
                'ORDER_BY' => 'ASC'
            ],
            $component
        ) ?>
        </div>
    </div>
</div>