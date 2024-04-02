<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arResult
 */

?>
<div class="catalog-element-article">
    <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_ARTICLE', [
        '#ARTICLE#' => $arResult['DATA']['ARTICLE']['VALUE']
    ]) ?>
</div>
