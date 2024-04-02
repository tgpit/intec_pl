<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 */

?>

<!--noindex-->
<div class="sale-personal-section-links-wrap-mobile" data-role="mobile-links">
    <?= Html::beginTag('div', [
        'class' => [
            'sale-personal-section-links-button-mobile',
            'intec-grid' => [
                '',
                'nowrap',
                'a-h-between',
                'a-v-canter'
            ]
        ],
        'data' => [
            'action' => 'menu.open',
            'state' => 'false'
        ]
    ]) ?>
        <div class="intec-grid-item-auto">
            <?= Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CHAIN_MAIN') ?>
        </div>
        <div class="intec-grid-item-auto">
            <i class="glyph-icon-menu-icon_2" data-code="icon-open"></i>
            <i class="glyph-icon-cancel" data-code="icon-close"></i>
        </div>
    <?= Html::endTag('div') ?>
    <div class="sale-personal-section-links-mobile" data-role="content" style="display: none;">
        <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
            <div class="sale-personal-section-link-item-mobile">
                <?= Html::tag('a', Html::encode($arItem['NAME']), [
                    'class' => Html::cssClassFromArray([
                        'sale-personal-section-link-mobile' => true,
                        'intec-cl' => [
                            'text' => $arItem['ACTIVE'],
                            'text-light-hover' => true
                        ]
                    ], true),
                    'href' => Html::encode($arItem['PATH'])
                ]); ?>
            </div>
        <?php } ?>
    </div>
</div>
<!--/noindex-->
