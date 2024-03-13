<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

?>
<div class="ns-intec-seo c-filter-tags c-filter-tags-default">
    <div class="filter-tags-items">
        <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
            <?= Html::beginTag('div', [
                'class' => 'filter-tags-item',
                'data' => [
                    'active' => $arItem['ACTIVE'] ? 'true' : 'false'
                ]
            ]) ?>
                <?= Html::tag($arItem['ACTIVE'] ? 'div' : 'a', Html::encode($arItem['NAME']), [
                    'href' => !$arItem['ACTIVE'] ? Html::encode($arItem['TARGET'] ? $arItem['URL']['TARGET'] : $arItem['URL']['SOURCE']) : null,
                    'class' => 'filter-tags-item-name'
                ]) ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
    </div>
</div>
