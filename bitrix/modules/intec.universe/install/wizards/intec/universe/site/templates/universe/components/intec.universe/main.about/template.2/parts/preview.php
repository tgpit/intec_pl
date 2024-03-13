<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php return function () use (&$arResult, &$arVisual) { ?>
    <?php if ($arVisual['PREVIEW']['SHOW']) { ?>
        <div class="widget-preview">
            <?= $arResult['ITEM']['PREVIEW_TEXT'] ?>
        </div>
    <?php } ?>
<?php } ?>