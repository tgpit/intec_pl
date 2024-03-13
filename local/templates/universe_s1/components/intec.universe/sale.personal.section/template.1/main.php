<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;

?>
<div class="ns-intec c-sale-personal-section c-sale-personal-section-template-1 p-main">
    <div class="sale-personal-section-wrapper intec-content">
        <div class="sale-personal-section-wrapper intec-content-wrapper">
            <div class="sale-personal-section-items intec-grid intec-grid-wrap intec-grid-a-v-stretch intec-grid-i-8">
                <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
                    <div class="sale-personal-section-item intec-grid-item-3 intec-grid-item-768-2 intec-grid-item-550-1">
                        <?= Html::beginTag('a', [
                            'class' => [
                                'sale-personal-section-item-wrapper',
                                'intec-cl-background'
                            ],
                            'href' => $arItem['LINK'],
                            'data' => [
                                'code' => $arItem['CODE']
                            ]
                        ]) ?>
                            <span class="sale-personal-section-item-icon">
                                <?= Html::tag('i', null, [
                                    'class' => [
                                        'fa',
                                        'fa-'.$arItem['ICON']
                                    ]
                                ]) ?>
                            </span>
                            <span class="sale-personal-section-item-title">
                                <?= Html::encode($arItem['NAME']) ?>
                            </span>
                        <?= Html::endTag('a') ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
