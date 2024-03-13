<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<?php return function (&$arData) use (&$APPLICATION, &$component) { ?>
    <?php $APPLICATION->IncludeComponent(
        'intec.universe:system.video.tag',
        '.default', [
            'FILES_MP4' => !empty($arData['VIDEO']['FILES']['MP4']) ? $arData['VIDEO']['FILES']['MP4']['SRC'] : null,
            'FILES_WEBM' => !empty($arData['VIDEO']['FILES']['WEBM']) ? $arData['VIDEO']['FILES']['WEBM']['SRC'] : null,
            'FILES_OGV' => !empty($arData['VIDEO']['FILES']['OGV']) ? $arData['VIDEO']['FILES']['OGV']['SRC'] : null,
            'CACHE_TYPE' => 'N'
        ],
        $component,
        ['HIDE_ICONS' => 'Y']
    ) ?>
<?php } ?>