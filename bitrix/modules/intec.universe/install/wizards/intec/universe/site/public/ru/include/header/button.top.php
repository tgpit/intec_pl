<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @global CMain $APPLICATION
 */

?>
<?php $APPLICATION->IncludeComponent(
    "intec.universe:main.widget",
    "navigation.button.top",
    array(
        "RADIUS" => "",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "3600000"
    ),
    false
) ?>
