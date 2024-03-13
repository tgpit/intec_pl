<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponent $component
 */


?>
<div class="widget-menu-mobile-items" data-role="services-menu-mobile">
    <?php foreach ($arResult['SECTIONS'] as $arItem) { ?>
        <div class="widget-menu-mobile-wrapper">
            <?= Html::beginTag('div', [
                'class' => [
                    'widget-menu-item',
                    'intec' => [
                        'grid-item',
                        'cl-text-hover',
                        'cl-border-hover'
                    ]
                ],
                'data' => [
                    'role' => 'menu-item',
                    'menu-id' => $arItem['ID'],
                ]
            ]) ?>
            <div class="widget-menu-item-name">
                <?= $arItem['NAME'] ?>
            </div>
            <?= Html::endTag('div') ?>
        </div>
    <?php } ?>
</div>