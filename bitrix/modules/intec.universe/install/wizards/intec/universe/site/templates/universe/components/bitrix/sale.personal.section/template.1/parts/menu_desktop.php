<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

?>

<?php $fDraw = function ($arItem) use (&$fDraw, &$arParams, &$arResult) { ?>
    <?php
    $arItems = $arItem['ITEMS'];

    include(__DIR__.'/default.php');
    ?>
<?php } ?>

<div class="sale-personal-section-links-wrap" data-role="links">
    <div class="sale-personal-section-links intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-stretch" data-role="items">
        <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
            <?php
                $bActive = $arItem['ACTIVE'];

                $sUrl = $bActive ? null : Html::encode($arItem['PATH']);
                $sTag = $bActive ? 'div' : 'a';
            ?>
            <div class="sale-personal-section-link-item intec-grid-item-auto" data-role="item">
                <?= Html::beginTag($sTag, [
                    'class' => Html::cssClassFromArray([
                        'sale-personal-section-link' => true,
                        'intec-grid' => [
                            '' => true,
                            'nowrap' => true,
                            'a-h-center' => true,
                            'a-v-center' => true
                        ],
                        'intec-cl' => [
                            'background-hover' => !$bActive,
                            'border-hover' => !$bActive,
                            'background' => $bActive,
                            'border' => $bActive
                        ]
                    ], true),
                    'href' => $sUrl,
                    'data' => [
                        'active' => $bActive ? 'true' : 'false'
                    ]
                ]) ?>
                    <?php if ($arResult['VISUAL']['SHOW_ICON']) { ?>
                        <div class="sale-personal-section-link-icon intec-grid-item-auto">
                            <?= $arItem['ICON'] ?>
                        </div>
                    <?php } ?>
                    <div class="sale-personal-section-link-text intec-grid-item-auto">
                        <?= Html::encode($arItem['NAME']) ?>
                    </div>
                <?= Html::endTag($sTag) ?>
            </div>
        <?php } ?>
        <?php unset($arItem) ?>
        <div class="sale-personal-section-link-item intec-grid-item-auto" data-role="more">
            <?= Html::beginTag('div', [
                'class' => [
                    'sale-personal-section-link',
                    'intec-grid' => [
                        '',
                        'nowrap',
                        'a-h-center',
                        'a-v-center'
                    ],
                    'intec-cl' => [
                        'background-hover',
                        'border-hover'
                    ]
                ]
            ]) ?>
            <div class="sale-personal-section-link-text intec-grid-item-auto">
                <?= Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_MENU_ITEM_MORE_LINKS') ?>
            </div>
            <?= Html::endTag('div') ?>
            <?php $fDraw($arResult) ?>
        </div>
    </div>
</div>
