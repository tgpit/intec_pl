<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

?>
<div class="catalog-section-panel" data-role="section.panel">
    <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
        <div class="intec-grid-item">
            <div class="catalog-section-panel-checkbox">
                <?= Html::beginTag('label', [
                    'class' => [
                        'intec-ui' => [
                            '',
                            'control-checkbox',
                            'scheme-current',
                            'size-2'
                        ]
                    ]
                ]) ?>
                    <?= Html::checkbox('', false, [
                        'value' => null,
                        'data-role' => 'panel.checkbox'
                    ]) ?>
                    <span class="intec-ui-part-selector"></span>
                    <span class="intec-ui-part-content">
                        <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_LIST_1_PANEL_SELECT_ALL') ?>
                    </span>
                <?= Html::endTag('label') ?>
            </div>
        </div>
        <div class="intec-grid-item-auto">
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-section-panel-button',
                    'intec-ui' => [
                        '',
                        'control-basket-button'
                    ],
                    'intec-ui-control-button',
                    'intec-ui-scheme-current'
                ],
                'data' => [
                    'role' => 'panel.add',
                    'basket-state' => 'disabled',
                ]
            ]) ?>
                <div class="intec-ui-part-icon">
                    <i class="glyph-icon-cart"></i>
                </div>
                <div class="intec-ui-part-content">
                    <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_8_BUTTON_ADD') ?>
                    (<span data-role="panel.add.number">0</span>)
                </div>
                <span class="intec-ui-part-effect intec-ui-part-effect-bounce">
                    <span class="intec-ui-part-effect-wrapper">
                        <i></i><i></i><i></i>
                    </span>
                </span>
            <?= Html::endTag('div') ?>
        </div>
    </div>
</div>