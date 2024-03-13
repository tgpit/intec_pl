<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

/**
 * @var array $arTickets
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

?>
<div class="sale-personal-section-claims">
    <div class="sale-personal-section-claims-header">
        <div class="sale-personal-section-claims-title">
            <?= Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CLAIMS_TITLE') ?>
        </div>
    </div>
    <div class="sale-personal-section-claims-wrap">
        <div class="sale-personal-section-claims-items">
            <?php if (!empty($arTickets)) { ?>
                <div class="sale-personal-section-claims-item sale-personal-section-claims-item-header">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-h-8">
                        <div class="sale-personal-section-claims-text sale-personal-section-claims-text-header intec-grid-item" data-code="id">
                            <?= Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CLAIMS_ID') ?>
                        </div>
                        <div class="sale-personal-section-claims-text sale-personal-section-claims-text-header intec-grid-item" data-code="theme">
                            <?= Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CLAIMS_THEME') ?>
                        </div>
                        <div class="sale-personal-section-claims-text sale-personal-section-claims-text-header intec-grid-item" data-code="date">
                            <?= Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CLAIMS_DATE') ?>
                        </div>
                    </div>
                </div>
                <?php foreach ($arTickets as $arTicket) { ?>
                    <div class="sale-personal-section-claims-item">
                        <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-h-8">
                            <div class="sale-personal-section-claims-text intec-grid-item intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start" data-code="id">
                                <div class="sale-personal-section-claims-text-header intec-grid-item-1 intec-grid-item-450-2">
                                    <?= Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CLAIMS_ID') ?>
                                </div>
                                <div class="intec-grid-item-1 intec-grid-item-450-2">
                                    <?= Html::tag(empty($arTicket['TICKET_EDIT_URL']) ? 'div' : 'a', $arTicket['ID'], [
                                        'class' => 'intec-cl-text',
                                        'href' => !empty($arTicket['TICKET_EDIT_URL']) ? $arTicket['TICKET_EDIT_URL'] : null
                                    ]) ?>
                                    <div style="color: #808080;"><?= $arTicket['TIMESTAMP_X'] ?></div>
                                </div>
                            </div>
                            <div class="sale-personal-section-claims-text intec-grid-item intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start" data-code="theme">
                                <div class="sale-personal-section-claims-text-header intec-grid-item-1 intec-grid-item-450-2">
                                    <?= Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CLAIMS_THEME') ?>
                                </div>
                                <div class="intec-grid-item-1 intec-grid-item-450-2">
                                    <?= $arTicket['TITLE'] ?>
                                </div>
                            </div>
                            <div class="sale-personal-section-claims-text intec-grid-item intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start" data-code="date">
                                <div class="sale-personal-section-claims-text-header intec-grid-item-1 intec-grid-item-450-2">
                                    <?= Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CLAIMS_DATE') ?>
                                </div>
                                <div class="intec-grid-item-1 intec-grid-item-450-2">
                                    <?= $arTicket['TIMESTAMP_X'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="sale-personal-section-claims-empty">
                    <?= Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CLAIMS_EMPTY') ?>
                </div>
            <?php } ?>
        </div>
        <div class="sale-personal-section-claims-buttons">
            <div class="intec-grid intec-grid-wrap intec-grid-a-h-end intec-grid-a-h-450-start intec-grid-a-v-center intec-grid-i-4">
                <?php if (!empty($arResult['ALL_TICKET_URL'])) { ?>
                    <div class="intec-grid-item-auto">
                        <?= Html::tag('a', Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CLAIMS_BUTTON_ALL'), [
                            'class' => [
                                'sale-personal-section-claims-button',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'mod-transparent',
                                    'mod-round-2',
                                    'scheme-current'
                                ]
                            ],
                            'href' => $arResult['ALL_TICKET_URL']
                        ]) ?>
                    </div>
                <?php } ?>
                <?php if (!empty($arResult['ADD_TICKET_URL'])) { ?>
                    <div class="intec-grid-item-auto">
                        <?= Html::tag('a', Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CLAIMS_BUTTON_NEW'), [
                            'class' => [
                                'sale-personal-section-claims-button',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'mod-transparent',
                                    'mod-round-2',
                                    'scheme-current'
                                ]
                            ],
                            'href' => $arResult['ADD_TICKET_URL']
                        ]) ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>