<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

?>
<div class="support-ticket-edit-form" data-role="form">
    <div class="support-ticket-edit-form-title">
        <div class="support-ticket-edit-form-title-header">
            <?= empty($arResult["TICKET"]['ID']) ? Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_TITLE_TICKET') : Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_TITLE_ANSWER') ?>
        </div>
    </div>
    <div class="support-ticket-edit-form-wrap">
        <form name="support_edit" method="post" action="<?=$arResult["REAL_FILE_PATH"]?>" enctype="multipart/form-data">
            <?= bitrix_sessid_post() ?>
            <input type="hidden" name="set_default" value="Y" />
            <input type="hidden" name="ID" value="<?= empty($arResult["TICKET"]['ID']) ? 0 : $arResult["TICKET"]["ID"] ?>" />
            <input type="hidden" name="lang" value="<?= LANG ?>" />
            <input type="hidden" name="edit" value="1" />
            <input type="hidden" value="Y" name="apply" />
            <?php if (empty($arResult["TICKET"]['ID'])) { ?>
                <div class="support-ticket-edit-form-field">
                    <label class="support-ticket-edit-form-field-title" for="TICKET_TITLE">
                        <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_FIELD_TITLE') ?>
                    </label>
                    <input class="support-ticket-edit-form-field-inputtext intec-ui intec-ui-control-input" type="text" id="TICKET_TITLE" name="TITLE" value="<?= Html::encode($_REQUEST["TITLE"]) ?>" size="48" maxlength="255" />
                </div>
            <?php } ?>
            <div class="support-ticket-edit-form-field">
                <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-h-8">
                    <div class="intec-grid-item-auto">
                        <label class="support-ticket-edit-form-field-title" for="TICKET_MESSAGE">
                            <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_FIELD_MESSAGE') ?>
                        </label>
                    </div>
                    <div class="intec-grid-item-auto">
                        <?= Html::input('button', 'B', Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_BOLD'), [
                            'class' => [
                                'support-ticket-edit-form-button-message',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'scheme-current',
                                    'mod-round-2'
                                ]
                            ],
                            'accesskey' => 'b',
                            'data' => [
                                'role' => 'insertTag',
                                'type' => 'B'
                            ],
                            'id' => 'B',
                            'title' => Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_BOLD_TITLE')
                        ]) ?>
                    </div>
                    <div class="intec-grid-item-auto">
                        <?= Html::input('button', 'I', Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_ITALIC'), [
                            'class' => [
                                'support-ticket-edit-form-button-message',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'scheme-current',
                                    'mod-round-2'
                                ]
                            ],
                            'accesskey' => 'i',
                            'data' => [
                                'role' => 'insertTag',
                                'type' => 'I'
                            ],
                            'id' => 'I',
                            'title' => Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_ITALIC_TITLE')
                        ]) ?>
                    </div>
                    <div class="intec-grid-item-auto">
                        <?= Html::input('button', 'U', Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_UNDERLINE'), [
                            'class' => [
                                'support-ticket-edit-form-button-message',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'scheme-current',
                                    'mod-round-2'
                                ]
                            ],
                            'accesskey' => 'u',
                            'data' => [
                                'role' => 'insertTag',
                                'type' => 'U'
                            ],
                            'id' => 'U',
                            'title' => Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_UNDERLINE_TITLE')
                        ]) ?>
                    </div>
                    <div class="intec-grid-item-auto">
                        <?= Html::input('button', 'QUOTE', Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_QUOTE'), [
                            'class' => [
                                'support-ticket-edit-form-button-message',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'scheme-current',
                                    'mod-round-2'
                                ]
                            ],
                            'accesskey' => 'q',
                            'data' => [
                                'role' => 'insertTag',
                                'type' => 'QUOTE'
                            ],
                            'id' => 'QUOTE',
                            'title' => Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_QUOTE_TITLE')
                        ]) ?>
                    </div>
                    <div class="intec-grid-item-auto">
                        <?= Html::input('button', 'CODE', Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_CODE'), [
                            'class' => [
                                'support-ticket-edit-form-button-message',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'scheme-current',
                                    'mod-round-2'
                                ]
                            ],
                            'accesskey' => 'c',
                            'data' => [
                                'role' => 'insertTag',
                                'type' => 'CODE'
                            ],
                            'id' => 'CODE',
                            'title' => Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_CODE_TITLE')
                        ]) ?>
                    </div>
                    <?php if (LANG == "ru") { ?>
                        <div class="intec-grid-item-auto">
                            <?= Html::input('button', 'TRANSLIT', Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_TRANSLIT'), [
                                'class' => [
                                    'support-ticket-edit-form-button-message',
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'scheme-current',
                                        'mod-round-2'
                                    ]
                                ],
                                'accesskey' => 't',
                                'data' => [
                                    'role' => 'translit',
                                    'type' => 'TRANSLIT'
                                ],
                                'id' => 'TRANSLIT',
                                'title' => Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_TRANSLIT_TITLE')
                            ]) ?>
                        </div>
                    <?php } ?>
                </div>
                <textarea class="support-ticket-edit-form-field-textarea intec-ui intec-ui-control-input" id="TICKET_MESSAGE" name="MESSAGE" id="MESSAGE" rows="20" cols="5" wrap="virtual"><?= Html::encode($_REQUEST["MESSAGE"]) ?></textarea>
            </div>
            <div class="support-ticket-edit-form-field">
                <label class="support-ticket-edit-form-field-title" for="CRITICALITY_ID">
                    <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_FIELD_CRITICALITY') ?>
                </label>
                <?php if (empty($arResult["TICKET"]['ID']) || strlen($arResult["ERROR_MESSAGE"]) > 0 ) {
                    if (strlen($arResult["DICTIONARY"]["CRITICALITY_DEFAULT"]) > 0 && strlen($arResult["ERROR_MESSAGE"]) <= 0) {
                        $criticality = $arResult["DICTIONARY"]["CRITICALITY_DEFAULT"];
                    } else {
                        $criticality = Html::encode($_REQUEST["CRITICALITY_ID"]);
                    }
                } else {
                    $criticality = $arResult["TICKET"]["CRITICALITY_ID"];
                } ?>
                <select class="support-ticket-edit-form-field-select" name="CRITICALITY_ID" id="CRITICALITY_ID">
                    <option value=""></option>
                    <?php foreach ($arResult["DICTIONARY"]["CRITICALITY"] as $value => $option) { ?>
                        <option value="<?= $value ?>" <?= $criticality == $value ? 'selected="selected"' : '' ?>>
                            <?= $option ?>
                        </option>
                    <?php } ?>
                </select>
                <?php unset($criticality) ?>
            </div>
            <div class="support-ticket-edit-form-field">
                <label class="support-ticket-edit-form-field-title" for="CATEGORY_ID">
                    <?= empty($arResult["TICKET"]['ID']) ? Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_FIELD_CATEGORY') : Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_FIELD_MARK') ?>
                </label>
                <?php
                if (empty($arResult["TICKET"]['ID'])) { ?>
                    <?php if (strlen($arResult["DICTIONARY"]["CATEGORY_DEFAULT"]) > 0 && strlen($arResult["ERROR_MESSAGE"]) <= 0) {
                        $category = $arResult["DICTIONARY"]["CATEGORY_DEFAULT"];
                    } else {
                        $category = Html::encode($_REQUEST["CATEGORY_ID"]);
                    } ?>
                    <select class="support-ticket-edit-form-field-select" name="CATEGORY_ID" id="CATEGORY_ID">
                        <option value=""></option>
                        <?php foreach ($arResult["DICTIONARY"]["CATEGORY"] as $value => $option) { ?>
                            <option value="<?= $value ?>" <?= $category == $value ? 'selected="selected"' : '' ?>>
                                <?= $option ?>
                            </option>
                        <?php } ?>
                    </select>
                    <?php unset($category) ?>
                <?php } else { ?>
                    <?php $mark = (strlen($arResult["ERROR_MESSAGE"]) > 0 ? Html::encode($_REQUEST["MARK_ID"]) : $arResult["TICKET"]["MARK_ID"]) ?>
                    <select class="support-ticket-edit-form-field-select" name="MARK_ID" id="MARK_ID">
                        <option value=""></option>
                        <?php foreach ($arResult["DICTIONARY"]["MARK"] as $value => $option) { ?>
                            <option value="<?= $value ?>" <?= $mark == $value ? 'selected="selected"' : '' ?>>
                                <?= $option ?>
                            </option>
                        <?php } ?>
                    </select>
                    <?php unset($mark) ?>
                <?php } ?>
            </div>
            <?php if ($arParams['SHOW_COUPON_FIELD'] == 'Y' && $arParams['ID'] <= 0) { ?>
                <div class="support-ticket-edit-form-field">
                    <label class="support-ticket-edit-form-field-title" for="COUPON">
                        <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_FIELD_COUPON') ?>
                    </label>
                    <input class="support-ticket-edit-form-field-inputtext intec-ui intec-ui-control-input" type="text" id="COUPON" name="COUPON" value="<?= Html::encode($_REQUEST["COUPON"]) ?>" size="48" maxlength="255" />
                </div>
            <?php } ?>
            <?php global $USER_FIELD_MANAGER ?>
            <?php
            if(isset($arParams["SET_SHOW_USER_FIELD_T"])) {
                foreach($arParams["SET_SHOW_USER_FIELD_T"] as $sPropertyCode => $arProperty) {
                    $arProperty["ALL"]["VALUE"] = $arParams[$sPropertyCode];
                    ?>
                    <div class="support-ticket-edit-form-field">
                        <div class="support-ticket-edit-form-field-title">
                            <?= Html::encode($arProperty["NAME_F"]) ?>
                        </div>
                        <div class="support-ticket-edit-form-field-system">
                            <?php $APPLICATION->IncludeComponent(
                                'bitrix:system.field.edit',
                                $arProperty['ALL']['USER_TYPE_ID'],
                                [
                                    'bVarsFromForm' => true,
                                    'arUserField' => $arProperty['ALL'],
                                ],
                                null,
                                ['HIDE_ICONS' => 'Y']
                            ) ?>
                        </div>
                    </div>
                <?php }
                unset($sPropertyCode, $arProperty);
            } ?>
            <div class="support-ticket-edit-form-field">
                <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-8">
                    <div class="intec-grid-item-auto">
                        <div class="support-ticket-edit-form-field-inputfile">
                            <input name="FILE" id="FILE" type="file" multiple="multiple" data-role="upload" />
                            <label for="FILE" class="intec-ui intec-ui-control-button intec-ui-mod-transparent intec-ui-mod-round-2 intec-ui-scheme-current">
                                <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_FILE') ?>
                            </label>
                        </div>
                    </div>
                    <div class="intec-grid-item-auto">
                        <div class="support-ticket-edit-form-field-filesize">
                            <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_FIELD_FILE_SIZE', [
                                '#SIZE#' => $arResult["OPTIONS"]["MAX_FILESIZE"]
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (strlen($arResult['TICKET']['DATE_CLOSE']) <= 0 && !empty($arResult['TICKET']['ID'])) { ?>
                <div class="support-ticket-edit-form-field">
                    <label class="intec-ui intec-ui-control-switch intec-ui-scheme-current intec-ui-size-4">
                        <input class="support-ticket-edit-form-field-checkbox" type="checkbox" name="CLOSE" id="CLOSE" value="Y" <?= $arResult['TICKET']['CLOSE'] == 'Y' ? 'checked="checked"' : '' ?> />
                        <span class="intec-ui-part-selector"></span>
                        <span class="intec-ui-part-content support-ticket-edit-form-button-close-info">
                            <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_CLOSE') ?>
                        </span>
                    </label>
                </div>
            <?php } ?>
            <div class="support-ticket-edit-form-buttons">
                <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-6">
                    <div class="intec-grid-item-auto">
                        <?= Html::submitInput(Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_SAVE'), [
                            'class' => [
                                'support-ticket-edit-form-button',
                                'intec-ui' => [
                                    '',
                                    'control-button'
                                ],
                                'intec-cl' => [
                                    'background-hover',
                                    'border-hover'
                                ]
                            ],
                            'name' => 'save'
                        ]) ?>
                    </div>
                    <div class="intec-grid-item-auto">
                        <?= Html::submitInput(Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_APPLY'), [
                            'class' => [
                                'support-ticket-edit-form-button',
                                'intec-ui' => [
                                    '',
                                    'control-button'
                                ],
                                'intec-cl' => [
                                    'background-hover',
                                    'border-hover'
                                ]
                            ],
                            'name' => 'apply'
                        ]) ?>
                    </div>
                    <div class="intec-grid-item-auto">
                        <?= Html::resetInput(Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_FORM_BUTTON_RESET'), [
                            'class' => [
                                'support-ticket-edit-form-button',
                                'intec-ui' => [
                                    '',
                                    'control-button'
                                ],
                                'intec-cl' => [
                                    'background-hover',
                                    'border-hover'
                                ]
                            ]
                        ]) ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>