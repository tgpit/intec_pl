<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php return function () use (&$arResult, &$arVisual) { ?>
    <?php if ($arVisual['TITLE']['SHOW']) { ?>
        <div class="widget-title">
            <?= $arResult['TITLE'] ?>
        </div>
    <?php } ?>
<?php } ?>