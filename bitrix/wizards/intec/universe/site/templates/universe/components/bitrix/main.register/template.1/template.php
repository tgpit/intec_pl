<?
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2014 Bitrix
 */

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 */

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

if ($arResult["SHOW_SMS_FIELD"] == true)
    CJSCore::Init('phone_auth');

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this, true));
$sPrefix = "MAIN_REGISTER_TEMPLATE1_";
?>

<div class="ns-bitrix c-main-register c-main-register-template-1" id="<?= $sTemplateId ?>">
    <script>
        BX.message({
            phone_auth_resend: '<?=GetMessageJS($sPrefix.'phone_auth_resend')?>',
            phone_auth_resend_link: '<?=GetMessageJS($sPrefix.'phone_auth_resend_link')?>',
        });
    </script>

    <?if($USER->IsAuthorized()){?>
        <p>
            <?= Loc::getMessage($sPrefix.'AUTHORIZED') ?>
        </p>
    <? } else { ?>
        <?php
        if (count($arResult['ERRORS']) > 0) {
            foreach ($arResult['ERRORS'] as $key => $error) {
                if (intval($key) == 0 && $key !== 0) {
                    $arResult['ERRORS'][$key] = str_replace('#FIELD_NAME#',
                        '&quot;' . Loc::getMessage($sPrefix.'REGISTER_FIELD_' . $key) . '&quot;',
                        $error);
                }
            }
            ShowError(implode('<br />', $arResult['ERRORS']));

        } else if ($arResult['USE_EMAIL_CONFIRMATION'] === 'Y') { ?>
            <p><?echo Loc::getMessage($sPrefix.'REGISTER_EMAIL_WILL_BE_SENT')?></p>
        <?php } ?>

        <?php if ($arResult["SHOW_SMS_FIELD"]) { ?>
            <form class="main-register-form" method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform">
                <?php if($arResult["BACKURL"] <> '') { ?>
                    <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
                <?php } ?>
                <?=Html::hiddenInput('SIGNED_DATA', htmlspecialcharsbx($arResult["SIGNED_DATA"]));?>
                <div class="main-register-form-field">
                    <label class="main-register-field-caption" for="SMS_CODE_POPUP">
                        <?= Loc::getMessage($sPrefix.'SMS') ?>
                        <span class="main-register-starrequired">*</span>
                    </label>
                    <div class="main-register-field-value">
                        <?=Html::textInput('SMS_CODE', htmlspecialcharsbx($arResult["SMS_CODE"]), [
                            'class' => 'main-register-field-input',
                            'data-role' => 'input',
                            'size' => 30,
                            'autocomplete' => 'off',
                            'placeholder' => '',
                            'id' => 'SMS_CODE_POPUP'
                        ]);?>
                    </div>
                </div>
                <div class="main-register-form-field">
                    <div id="bx_main_register_error" style="display:none"><?ShowError("error")?></div>
                    <div id="bx_main_register_resend"></div>
                </div>
                <div class="main-register-button-wrap">
                    <?=Html::submitInput(Loc::getMessage($sPrefix."SMS_SEND"), [
                        'name' => 'code_submit_button',
                        'class' => [
                            'main-register-button',
                            'intec-ui' => [
                                '',
                                'control-button',
                                'scheme-current',
                                'size-2',
                            ]
                        ]
                    ])?>
                </div>
            </form>

            <script>
                new BX.PhoneAuth({
                    containerId: 'bx_main_register_resend',
                    errorContainerId: 'bx_main_register_error',
                    interval: <?=$arResult["PHONE_CODE_RESEND_INTERVAL"]?>,
                    data:
                    <?=CUtil::PhpToJSObject([
                        'signedData' => $arResult["SIGNED_DATA"],
                    ])?>,
                    onError:
                        function(response)
                        {
                            var errorDiv = BX('bx_main_register_error');
                            var errorNode = BX.findChildByClassName(errorDiv, 'errortext');
                            errorNode.innerHTML = '';
                            for(var i = 0; i < response.errors.length; i++)
                            {
                                errorNode.innerHTML = errorNode.innerHTML + BX.util.htmlspecialchars(response.errors[i].message) + '<br>';
                            }
                            errorDiv.style.display = '';
                        }
                });
            </script>
        <?php } else { ?>
            <form class="main-register-form" method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" enctype="multipart/form-data">
                <?php if($arResult["BACKURL"] <> '') { ?>
                    <?=Html::hiddenInput('backurl', $arResult["BACKURL"])?>
                <?php } ?>

                <div class="main-register-form-body">
                    <?php foreach ($arResult["SHOW_FIELDS"] as $FIELD) { ?>
                        <?php if ($FIELD == 'PERSONAL_PHOTO' ||
                                  $FIELD == 'WORK_LOGO') continue; ?>

                        <?php if ($FIELD == "AUTO_TIME_ZONE" && $arResult["TIME_ZONE_ENABLED"] == true) { ?>
                            <div class="">
                                <?= Loc::getMessage($sPrefix."main_profile_time_zones_auto") ?>
                                <?php if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y") { ?>
                                    <span class="main-register-starrequired">*</span>
                                <?php } ?>

                                <select name="REGISTER[AUTO_TIME_ZONE]"
                                        onchange="this.form.elements['REGISTER[TIME_ZONE]'].disabled=(this.value != 'N')"
                                        data-role="input"
                                        class="main-register-field-select">
                                    <option value="">
                                        <?= Loc::getMessage($sPrefix."main_profile_time_zones_auto_def")?>
                                    </option>
                                    <option value="Y"<?= $arResult["VALUES"][$FIELD] == "Y" ? " selected=\"selected\"" : ""?>>
                                        <?= Loc::getMessage($sPrefix."main_profile_time_zones_auto_yes")?>
                                    </option>
                                    <option value="N"<?= $arResult["VALUES"][$FIELD] == "N" ? " selected=\"selected\"" : ""?>>
                                        <?= Loc::getMessage($sPrefix."main_profile_time_zones_auto_no")?>
                                    </option>
                                </select>
                            </div>
                            <div class="">
                                <div>
                                    <?= Loc::getMessage($sPrefix."main_profile_time_zones_zones")?>
                                </div>
                                <div>
                                    <select name="REGISTER[TIME_ZONE]"
                                            <?if(!isset($_REQUEST["REGISTER"]["TIME_ZONE"])) echo 'disabled="disabled"'?>
                                            data-role="input"
                                            class="main-register-field-select">
                                        <?php foreach($arResult["TIME_ZONE_LIST"] as $tz=>$tz_name) { ?>
                                            <option value="<?=htmlspecialcharsbx($tz)?>"
                                                <?= $arResult["VALUES"]["TIME_ZONE"] == $tz ? " selected=\"selected\"" : ""?>>
                                                <?= htmlspecialcharsbx($tz_name)?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        <?php } else { ?>

                            <div class="main-register-form-field">
                                <label class="main-register-field-caption" for="REGISTER_<?=$FIELD?>">
                                    <?= Loc::getMessage($sPrefix."REGISTER_FIELD_".$FIELD) ?>
                                    <?php if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y") { ?>
                                        <span class="main-register-starrequired">*</span>
                                    <?php } ?>
                                </label>
                                <div class="main-register-field-value">
                                    <?php switch ($FIELD) {
                                        case "PASSWORD": ?>
                                            <?=Html::passwordInput(
                                                'REGISTER['.$FIELD.']',
                                                $arResult["VALUES"][$FIELD], [
                                                    'name' => 'REGISTER['.$FIELD.']',
                                                    'autocomplete' => 'off',
                                                    'data-role' => 'input',
                                                    'class' => [
                                                        'bx-auth-input',
                                                        'main-register-field-input'
                                                    ],
                                                    'id' => 'REGISTER_'.$FIELD
                                                ]
                                            );?>
                                            <?php if ($arResult["SECURE_AUTH"]) {?>
                                                <span class="bx-auth-secure" id="bx_auth_secure" title="<?echo Loc::getMessage($sPrefix."AUTH_SECURE_NOTE")?>" style="display:none">
                                                    <div class="bx-auth-secure-icon"></div>
                                                </span>
                                                <noscript>
                                                    <span class="bx-auth-secure" title="<?echo Loc::getMessage($sPrefix."AUTH_NONSECURE_NOTE")?>">
                                                        <div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
                                                    </span>
                                                </noscript>
                                                <script type="text/javascript">
                                                    document.getElementById('bx_auth_secure').style.display = 'inline-block';
                                                </script>
                                            <?php }?>
                                        <? break;
                                        case "CONFIRM_PASSWORD": ?>
                                            <?=Html::passwordInput('REGISTER['.$FIELD.']',
                                                $arResult["VALUES"][$FIELD], [
                                                'id' => 'REGISTER_'.$FIELD,
                                                'autocomplete' => 'off',
                                                'data-role' => 'input',
                                                'class' => 'main-register-field-input',
                                                'size' => 30
                                            ]);?>
                                        <? break;
                                        case "PERSONAL_GENDER": ?>
                                            <select name="REGISTER[<?=$FIELD?>]"
                                                    id="REGISTER_<?=$FIELD?>"
                                                    class="main-register-field-select"
                                                    data-role="input">
                                                <option value=""><?=Loc::getMessage($sPrefix."USER_DONT_KNOW")?></option>
                                                <option value="M"<?=$arResult["VALUES"][$FIELD] == "M" ? " selected=\"selected\"" : ""?>><?=Loc::getMessage("MAIN_REGISTER_TEMPLATE1_USER_MALE")?></option>
                                                <option value="F"<?=$arResult["VALUES"][$FIELD] == "F" ? " selected=\"selected\"" : ""?>><?=Loc::getMessage("MAIN_REGISTER_TEMPLATE1_USER_FEMALE")?></option>
                                            </select>
                                            <? break;
                                        case "PERSONAL_COUNTRY":
                                        case "WORK_COUNTRY": ?>
                                            <select name="REGISTER[<?=$FIELD?>]"
                                                    id="REGISTER_<?=$FIELD?>"
                                                    data-role="input"
                                                    class="main-register-field-select">
                                            <? foreach ($arResult["COUNTRIES"]["reference_id"] as $key => $value) { ?>
                                                <option value="<?=$value?>"<?if ($value == $arResult["VALUES"][$FIELD]):?> selected="selected"<?endif?>><?=$arResult["COUNTRIES"]["reference"][$key]?></option>
                                                <? } ?>
                                            </select>
                                            <? break;
                                        case "PERSONAL_PHOTO":
                                        case "WORK_LOGO":
                                            break;
                                        case "PERSONAL_NOTES":
                                        case "WORK_NOTES": ?>
                                            <?=Html::textarea('REGISTER_'.$FIELD, $arResult["VALUES"][$FIELD], [
                                                'class' => 'main-register-field-textarea',
                                                'data-role' => 'input',
                                                'row' => 5,
                                                'cols' => 5,
                                                'id' => 'REGISTER_'.$FIELD,
                                            ])?>
                                            <? break;
                                        default: ?>
                                            <?=Html::textInput('REGISTER['.$FIELD.']',
                                                $arResult["VALUES"][$FIELD], [
                                                    'class' => [
                                                        'main-register-field-input',
                                                        $FIELD == "PERSONAL_BIRTHDAY" ? $arResult["DATE_FORMAT"] : null
                                                    ],
                                                    'placeholder' => ($FIELD == "PERSONAL_BIRTHDAY") ? $arResult["DATE_FORMAT"] : null,
                                                    'data-role' => 'input',
                                                    'id' => 'REGISTER_'.$FIELD,
                                                ])?>
                                            <? if ($FIELD == "PERSONAL_BIRTHDAY") { ?>
                                                <div class="main-register-date-picker">
                                                    <? $APPLICATION->IncludeComponent(
                                                        'bitrix:main.calendar',
                                                        '',
                                                        array(
                                                            'SHOW_INPUT' => 'N',
                                                            'FORM_NAME' => 'regform',
                                                            'INPUT_NAME' => 'REGISTER[PERSONAL_BIRTHDAY]',
                                                            'SHOW_TIME' => 'N'
                                                        ),
                                                        null,
                                                        array("HIDE_ICONS"=>"Y")
                                                    ); ?>
                                                </div>
                                            <? } ?>
                                    <? } ?>
                                </div>
                            </div>

                        <?php } ?>
                    <?php } ?>

                    <?php
                    /* CAPTCHA */
                    if ($arResult["USE_CAPTCHA"] == "Y") {?>
                        <div class="main-register-form-field">
                            <div class="main-register-field-caption">
                                <?=Loc::getMessage($sPrefix.'REGISTER_CAPTCHA_TITLE')?>
                                <span class="main-register-starrequired">*</span>
                            </div>
                            <div class="main-register-field-value">
                                <div>
                                    <?=Html::hiddenInput('captcha_sid', $arResult['CAPTCHA_CODE'])?>
                                    <?=Html::img('/bitrix/tools/captcha.php?captcha_sid='.$arResult['CAPTCHA_CODE'], [
                                        'width' => 180,
                                        'height' => 40,
                                        'alt' => 'CAPTCHA',
                                    ]);?>
                                </div>
                                <div>
                                    <div>
                                        <label for="captcha_word_register">
                                            <?=Loc::getMessage($sPrefix.'REGISTER_CAPTCHA_PROMT')?>:<span class="starrequired">*</span>
                                        </label>
                                    </div>
                                    <div>
                                        <?=Html::textInput(
                                            'captcha_word', '', [
                                                'class' => 'main-register-field-input',
                                                'id' => 'captcha_word_register',
                                                'maxlength' => 50,
                                                'data-role' => 'input'
                                            ]
                                        );?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                    /* !CAPTCHA */
                    ?>
                </div>
                <?php if ($arResult['CONSENT']['SHOW']) { ?>
                    <div class="main-register-consent">
                        <label class="intec-ui intec-ui-control-switch intec-ui-scheme-current intec-ui-size-1">
                            <input type="checkbox" checked="checked" onchange="this.checked = !this.checked" />
                            <span class="intec-ui-part-selector"></span>
                            <span class="intec-ui-part-content"><?= Loc::getMessage('MAIN_REGISTER_TEMPLATE1_CONSENT', [
                                    '#URL#' => $arResult['CONSENT']['URL']
                                ]) ?></span>
                        </label>
                    </div>
                <?php } ?>
                <div class="main-register-button-wrap">
                    <?=Html::submitInput(Loc::getMessage($sPrefix.'AUTH_REGISTER'), [
                        'name' => 'register_submit_button',
                        'class' => [
                            'intec-ui' => [
                                '',
                                'control-button',
                                'scheme-current',
                                'size-2'
                            ],
                            'main-register-button'
                        ]
                    ]);?>
                </div>

                <p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p>

                <p>
                    <span class="main-register-starrequired">*</span><?=Loc::getMessage("MAIN_REGISTER_TEMPLATE1_AUTH_REQ")?>
                </p>

            </form>
        <?php } ?>
    <?php } ?>
</div>

<script>
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var inputs = $('[data-role="input"]', root);
        var update;

        update = function() {
            var self = $(this);

            if (self.val() != '') {
                self.addClass('completed');
            } else {
                self.removeClass('completed');
            }
        };

        inputs.each(function () {
            update.call(this);
        });

        inputs.on('change', function () {
            update.call(this);
        });
    }, {
        'name': '[Component] bitrix:main.register (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>