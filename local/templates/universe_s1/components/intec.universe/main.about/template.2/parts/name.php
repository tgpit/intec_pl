<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 */

?>
<?php return function () use (&$arResult) { ?>
    <div class="widget-name">
        <?= $arResult['ITEM']['NAME'] ?>
    </div>
<?php } ?>