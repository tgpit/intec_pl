<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

if (!Loader::includeModule('intec.core'))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this, true));
global $USER;

?>
<div class="ns-intec-universe c-profile-add c-profile-add-template-1" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <?php if (!empty($arResult['ERRORS'])) { ?>
                <div class="form-result-new-message intec-ui intec-ui-control-alert intec-ui-scheme-red intec-ui-m-b-20">
                    <?php ShowError($arResult['ERROR_MESSAGE']) ?>
                </div>
            <?php } ?>
            <?php if ($USER->IsAuthorized()) { ?>
                <form class="profile-add-form" method="post" action="" enctype="multipart/form-data" data-role="profile.form">
                    <?= bitrix_sessid_post() ?>
                    <div class="profile-add-form-field-group">
                        <div class="profile-add-form-field-title profile-add-form-field-title-required">
                            <?= Loc::getMessage('C_PROFILE_ADD_TEMPLATE_DEFAULT_PERSON_TYPE_TITLE') ?>
                        </div>
                        <div class="profile-add-form-block">
                            <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-8">
                                <?php foreach ($arResult['PERSON_TYPES'] as $arPersonType) { ?>
                                    <div class="intec-grid-item-auto intec-grid-item-550-1">
                                        <label class="intec-ui intec-ui-control-radiobox intec-ui-scheme-current intec-ui-size-3">
                                            <?= Html::input('radio', 'PERSON_ID', $arPersonType['ID'], [
                                                'checked' => $arPersonType['IS_SELECTED'] == 'Y' ? 'checked' : null,
                                                'required' => 'required',
                                                'data' => [
                                                    'role' => 'person.type.choice'
                                                ]
                                            ]) ?>
                                            <span class="intec-ui-part-selector"></span>
                                            <span class="intec-ui-part-content"><?= $arPersonType['NAME'] ?></span>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="profile-add-form-block">
                            <label class="profile-add-form-field-title profile-add-form-field-title-required" for="PROFILE_NAME">
                                <?= Loc::getMessage('C_PROFILE_ADD_TEMPLATE_DEFAULT_PROFILE_NAME_TITLE') ?>
                            </label>
                            <div class="profile-add-form-field">
                                <input class="profile-add-form-field-text intec-ui intec-ui-control-input" type="text" name="NAME" maxlength="50" value="" required="required" id="PROFILE_NAME" />
                            </div>
                        </div>
                    </div>
                    <?php foreach($arResult['ORDER_PROPS'] as $arGroup) { ?>
                        <?php if (!empty($arGroup['PROPS'])) { ?>
                            <div class="profile-add-form-field-group-title">
                                <?= $arGroup['NAME'] ?>
                            </div>
                            <div class="profile-add-form-field-group">
                                <?php foreach($arGroup['PROPS'] as $arProperty) { ?>
                                    <?php
                                    $sPropertyName = 'ORDER_PROP_'.$arProperty['ID'];
                                    $arCurrentValue = is_array($arResult['ORDER_PROPS_VALUES'][$sPropertyName]) ? $arResult['ORDER_PROPS_VALUES'][$sPropertyName] : [$arResult['ORDER_PROPS_VALUES'][$sPropertyName]];
                                    ?>
                                    <div class="profile-add-form-block">
                                        <label class="profile-add-form-field-title <?= $arProperty['REQUIED'] === 'Y' ? 'profile-add-form-field-title-required' : '' ?>" for="<?=$sPropertyName?>_PROFILE">
                                            <?= $arProperty['NAME'] ?>
                                        </label>
                                        <div class="profile-add-form-field" data-role="property.row">
                                            <?php if ($arProperty['TYPE'] == 'CHECKBOX') { ?>
                                                <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-4" data-role="parent.input">
                                                    <?php foreach ($arCurrentValue as $key => $sValue) { ?>
                                                        <div class="intec-grid-item-auto">
                                                            <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current intec-ui-size-2">
                                                                <input <?= $arProperty['REQUIED'] === 'Y' ? 'required="required"' : '' ?> type="checkbox" name="<?= $sPropertyName.'['.$key.']' ?>" <?= $sValue == 'Y' ? 'checked="checked"' : '' ?>>
                                                                <span class="intec-ui-part-selector"></span>
                                                            </label>
                                                        </div>
                                                    <?php } ?>
                                                    <?php unset($key, $sValue) ?>
                                                </div>
                                                <?php if ($arProperty['MULTIPLE'] === 'Y') { ?>
                                                    <?= Html::tag('div', Loc::getMessage('C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_ADD_FIELD'), [
                                                        'class' => [
                                                            'intec-ui' => [
                                                                '',
                                                                'control-button',
                                                                'scheme-current',
                                                                'size-1'
                                                            ]
                                                        ],
                                                        'data' => [
                                                            'role' => 'add.input',
                                                            'add-type' => $arProperty['TYPE'],
                                                            'add-name' => $sPropertyName.'[]'
                                                        ]
                                                    ]) ?>
                                                <?php } ?>
                                            <?php } else if ($arProperty['TYPE'] == 'TEXT') { ?>
                                                <?php foreach ($arCurrentValue as $key => $sValue) { ?>
                                                    <?= Html::input('text', $arProperty['MULTIPLE'] === 'Y' ? $sPropertyName.'['.$key.']' : $sPropertyName, $sValue, [
                                                        'class' => 'profile-add-form-field-text intec-ui intec-ui-control-input',
                                                        'required' => $arProperty['REQUIED'] === 'Y' ? 'required' : null,
                                                        'id' => $sPropertyName.'_PROFILE'
                                                    ]) ?>
                                                <?php } ?>
                                                <?php unset($key, $sValue) ?>
                                                <?php if ($arProperty['MULTIPLE'] === 'Y') { ?>
                                                    <?= Html::tag('div', Loc::getMessage('C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_ADD_FIELD'), [
                                                        'class' => [
                                                            'intec-ui' => [
                                                                '',
                                                                'control-button',
                                                                'scheme-current',
                                                                'size-1'
                                                            ]
                                                        ],
                                                        'data' => [
                                                            'role' => 'add.input',
                                                            'add-type' => $arProperty['TYPE'],
                                                            'add-name' => $sPropertyName.'[]'
                                                        ]
                                                    ]) ?>
                                                <?php } ?>
                                            <?php } else if ($arProperty['TYPE'] == 'SELECT') { ?>
                                                <?= Html::beginTag('select', [
                                                    'class' => 'profile-add-form-field-select',
                                                    'name' => $sPropertyName,
                                                    'size' => intval($arProperty['SIZE1']) > 0 ? $arProperty['SIZE1'] : 1,
                                                    'required' => $arProperty['REQUIED'] === 'Y' ? 'required' : null,
                                                    'id' => $sPropertyName."_PROFILE"
                                                ]) ?>
                                                    <?php $arCurrentValue = ArrayHelper::shift($arCurrentValue) ?>
                                                    <?php foreach ($arProperty['VALUES'] as $arValue) { ?>
                                                        <option value="<?= $arValue['VALUE'] ?>" <?= $arCurrentValue === $arValue['VALUE'] ? 'selected="selected"' : '' ?>>
                                                            <?= $arValue['NAME'] ?>
                                                        </option>
                                                    <?php } ?>
                                                    <?php unset($arValue) ?>
                                                <?= Html::endTag('select') ?>
                                            <?php } else if ($arProperty['TYPE'] == 'MULTISELECT') { ?>
                                                <?= Html::beginTag('select', [
                                                    'class' => 'profile-add-form-field-select',
                                                    'name' => $sPropertyName.'[]',
                                                    'multiple' => 'multiple',
                                                    'size' => intval($arProperty['SIZE1']) > 0 ? $arProperty['SIZE1'] : 5,
                                                    'required' => $arProperty['REQUIED'] === 'Y' ? 'required' : null
                                                ]) ?>
                                                    <?php foreach ($arProperty['VALUES'] as $arValue) { ?>
                                                        <option value="<?= $arValue['VALUE'] ?>" <?= ArrayHelper::isIn($arValue['VALUE'], $arCurrentValue) ? 'selected="selected"' : '' ?>>
                                                            <?= $arValue['NAME'] ?>
                                                        </option>
                                                    <?php } ?>
                                                    <?php unset($arValue) ?>
                                                <?= Html::endTag('select') ?>
                                            <?php } else if ($arProperty['TYPE'] == 'TEXTAREA') { ?>
                                                <?= Html::tag('textarea', implode(', ', $arCurrentValue), [
                                                    'class' => 'profile-add-form-field-textarea intec-ui intec-ui-control-input',
                                                    'name' => $arProperty['MULTIPLE'] === 'Y' ? $sPropertyName.'[]' : $sPropertyName,
                                                    'rows' => ((int)($arProperty['SIZE2']) > 0) ? $arProperty['SIZE2'] : 4,
                                                    'cols' => ((int)($arProperty['SIZE1']) > 0) ? $arProperty['SIZE1'] : 40,
                                                    'required' => $arProperty['REQUIED'] === 'Y' ? 'required' : null,
                                                    'id' => $sPropertyName."_PROFILE"
                                                ]) ?>
                                                <?php if ($arProperty['MULTIPLE'] === 'Y') { ?>
                                                    <?= Html::tag('div', Loc::getMessage('C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_ADD_FIELD'), [
                                                        'class' => [
                                                            'intec-ui' => [
                                                                '',
                                                                'control-button',
                                                                'scheme-current',
                                                                'size-1'
                                                            ]
                                                        ],
                                                        'data' => [
                                                            'role' => 'add.input',
                                                            'add-type' => $arProperty['TYPE'],
                                                            'add-name' => $sPropertyName.'[]'
                                                        ],
                                                        'id' => $sPropertyName."_PROFILE"
                                                    ]) ?>
                                                <?php } ?>
                                            <?php } else if ($arProperty['TYPE'] == 'LOCATION') { ?>
                                                <?php
                                                $sLocationTemplate = ($arParams['USE_AJAX_LOCATIONS'] === 'Y') ? 'popup' : '';
                                                $arLocationParams = [
                                                    'AJAX_CALL' => 'N',
                                                    'CITY_OUT_LOCATION' => 'Y',
                                                    'COUNTRY_INPUT_NAME' => $sPropertyName.'_COUNTRY',
                                                    'CITY_INPUT_NAME' => $arProperty['MULTIPLE'] === 'Y' ? $sPropertyName.'[0]' : $sPropertyName,
                                                    'LOCATION_VALUE' => ''
                                                ];

                                                if (!empty($arCurrentValue)) {
                                                    $arLocationParams['LOCATION_VALUE'] = ArrayHelper::shift($arCurrentValue);
                                                    $arLocationParams['ID'] = 'propertyLocation'.$sPropertyName."[$arCurrentValue]";
                                                }

                                                CSaleLocation::proxySaleAjaxLocationsComponent(
                                                    $arLocationParams,
                                                    [],
                                                    $sLocationTemplate,
                                                    true,
                                                    'profile-add-form-field-location'
                                                ) ?>
                                                <?php if ($arProperty['MULTIPLE'] === 'Y') { ?>
                                                    <?= Html::tag('div', Loc::getMessage('C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_ADD_FIELD'), [
                                                        'class' => [
                                                            'intec-ui' => [
                                                                '',
                                                                'control-button',
                                                                'scheme-current',
                                                                'size-1'
                                                            ]
                                                        ],
                                                        'data' => [
                                                            'role' => 'add.input',
                                                            'add-type' => $arProperty['TYPE'],
                                                            'add-name' => $sPropertyName,
                                                            'add-template' => $sLocationTemplate,
                                                            'add-last-key' => 0
                                                        ],
                                                        'id' => $sPropertyName."_PROFILE"
                                                    ]) ?>
                                                <?php } ?>
                                            <?php } else if ($arProperty['TYPE'] == 'RADIO') { ?>
                                                <?php foreach ($arProperty['VALUES'] as $arValue) { ?>
                                                    <label class="intec-ui intec-ui-control-radiobox intec-ui-scheme-current intec-ui-size-1">
                                                        <?= Html::input('radio', $sPropertyName, $arValue['VALUE'], []) ?>
                                                        <span class="intec-ui-part-selector"></span>
                                                        <span class="intec-ui-part-content"><?= $arValue['NAME'] ?></span>
                                                    </label>
                                                <?php } ?>
                                                <?php unset($arValue) ?>
                                            <?php } else if ($arProperty['TYPE'] == 'FILE') { ?>
                                                <div class="profile-add-form-field-file" data-role="file">
                                                    <?php $multiple = $arProperty['MULTIPLE'] === 'Y' ? 'multiple' : '' ?>
                                                    <?php if (!empty($arCurrentValue)) { ?>
                                                        <input type="hidden" name="<?= $sPropertyName ?>_default" data-role="file.default.delete.input" value="<?= implode(';', $arCurrentValue) ?>">
                                                        <div class="profile-add-form-field-default-files">
                                                            <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-end intec-grid-i-4">
                                                                <?php foreach ($arCurrentValue as $key => $sFileId) { ?>
                                                                    <?php if (!empty($sFileId)) { ?>
                                                                        <?php
                                                                        $rsFile = CFile::GetByID($sFileId);
                                                                        $arFile = $rsFile->Fetch();
                                                                        $arFile['SRC'] = CFile::GetPath($arFile['ID']);
                                                                        ?>
                                                                        <div class="intec-grid-item-auto">
                                                                            <div class="profile-add-form-field-default-file">
                                                                                <?php if (CFile::IsImage($arFile['FILE_NAME'])) { ?>
                                                                                    <div class="profile-add-form-field-default-file-picture intec-ui-picture">
                                                                                        <?= Html::img($arFile['SRC'], [
                                                                                            'alt' => $arFile['FILE_NAME'],
                                                                                            'title' => $arFile['FILE_NAME']
                                                                                        ]) ?>
                                                                                    </div>
                                                                                <?php } else { ?>
                                                                                    <?= Html::beginTag('a', [
                                                                                        'class' => 'profile-add-form-field-default-file-download',
                                                                                        'href' => $arFile['SRC'],
                                                                                        'download' => $arFile['ORIGINAL_NAME']
                                                                                    ]) ?>
                                                                                        <div class="profile-add-form-field-default-file-name">
                                                                                            <?= $arFile['ORIGINAL_NAME'] ?>
                                                                                        </div>
                                                                                        <div class="intec-ui intec-ui-control-button intec-ui-mod-round-2 intec-ui-scheme-current">
                                                                                            <?= Loc::getMessage('C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_DOWNLOAD') ?>
                                                                                        </div>
                                                                                    <?= Html::endTag('a') ?>
                                                                                <?php } ?>
                                                                                <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current intec-ui-size-1">
                                                                                    <input type="checkbox" value="<?= $arFile['ID'] ?>" data-role="file.default.delete">
                                                                                    <span class="intec-ui-part-selector"></span>
                                                                                    <span class="intec-ui-part-content">
                                                                                        <?= Loc::getMessage('C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_FILE_DELETE') ?>
                                                                                    </span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                                <?php unset($sFileId, $rsFile, $arFile) ?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-4">
                                                        <div class="intec-grid-item-auto intec-grid-item-550-1">
                                                            <label class="profile-add-form-field-file-choose">
                                                                <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-4">
                                                                    <div class="intec-grid-item-auto intec-grid-item-550-1">
                                                                        <div class="intec-ui intec-ui-control-button intec-ui-mod-round-2 intec-ui-scheme-current">
                                                                            <?= Loc::getMessage('C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_FILE_SELECT') ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="intec-grid-item-auto intec-grid-item-550-1">
                                                                        <div class="profile-add-form-field-file-load-info" data-role="file.load.info">
                                                                            <?= Loc::getMessage('C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_FILE_NOT_SELECTED') ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="intec-grid-item-auto intec-grid-item-550-1">
                                                                        <?= CFile::InputFile(
                                                                            $sPropertyName.'[]',
                                                                                20,
                                                                                null,
                                                                                false,
                                                                                0,
                                                                                'IMAGE',
                                                                                'class="profile-add-form-field-file-choose-input" data-role="file.load" '.$multiple
                                                                        ) ?>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                        <div class="intec-grid-item-auto intec-grid-item-550-1">
                                                            <div class="profile-add-form-field-file-load-cancel" data-role="file.load.cancel" data-active="false">
                                                                <i class="glyph-icon-cancel"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } else if ($arProperty['TYPE'] === 'DATE') { ?>
                                                <div class="profile-add-form-field-dates" data-role="parent.input">
                                                    <?php foreach ($arCurrentValue as $key => $sValue) { ?>
                                                        <div class="profile-add-form-field-text-date" onclick="BX.calendar({node: this, field: this, bTime: true});">
                                                            <?= Html::input('text', $arProperty['MULTIPLE'] === 'Y' ? $sPropertyName.'['.$key.']' : $sPropertyName, $sValue, [
                                                                'class' => 'profile-add-form-field-text intec-ui intec-ui-control-input',
                                                                'readonly' => 'readonly',
                                                                'id' => $sPropertyName."_PROFILE"
                                                            ]) ?>
                                                            <i class="profile-add-form-field-text-date-icon-calendar"></i>
                                                        </div>
                                                    <?php } ?>
                                                    <?php unset($key, $sValue) ?>
                                                </div>
                                                <?php if ($arProperty['MULTIPLE'] === 'Y') { ?>
                                                    <?= Html::tag('div', Loc::getMessage('C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_ADD_FIELD'), [
                                                        'class' => [
                                                            'intec-ui' => [
                                                                '',
                                                                'control-button',
                                                                'scheme-current',
                                                                'size-1'
                                                            ]
                                                        ],
                                                        'data' => [
                                                            'role' => 'add.input',
                                                            'add-type' => $arProperty['TYPE'],
                                                            'add-name' => $sPropertyName.'[]'
                                                        ]
                                                    ]) ?>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if (!empty($arProperty['DESCRIPTION'])) { ?>
                                                <br /><small><?= $arProperty['DESCRIPTION'] ?></small>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <div class="profile-add-form-buttons intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-12">
                        <div class="intec-grid-item-auto">
                            <?= Html::input('submit', 'save', Loc::getMessage('C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_SAVE'), [
                                'class' => [
                                    'profile-add-form-button',
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'scheme-current',
                                        'size-5',
                                        'mod-round-2'
                                    ]
                                ]
                            ]) ?>
                        </div>
                        <div class="intec-grid-item-auto">
                            <?= Html::tag('div', Loc::getMessage('C_PROFILE_ADD_TEMPLATE_DEFAULT_BUTTON_RESET'), [
                                'class' => [
                                    'profile-add-form-button',
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'scheme-current',
                                        'size-5',
                                        'mod-round-2',
                                        'mod-transparent'
                                    ]
                                ],
                                'data' => [
                                    'role' => 'clear'
                                ]
                            ]) ?>
                        </div>
                    </div>
                </form>
            <?php } ?>
        </div>
    </div>
</div>

<?php include(__DIR__.'/parts/script.php');
