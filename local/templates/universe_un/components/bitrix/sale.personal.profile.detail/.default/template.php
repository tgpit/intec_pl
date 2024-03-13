<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Json;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

?>
<div id="<?= $sTemplateId?>" class="ns-bitrix c-sale-personal-profile-detail c-sale-personal-profile-detail-default">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <?php
            if(strlen($arResult["ID"])>0) {
                ShowError($arResult["ERROR_MESSAGE"]);
            ?>
                <div class="sale-personal-profile-detail-link-list">
                    <a href="<?=$arParams["PATH_TO_LIST"]?>"><?=Loc::getMessage('SALE_PERSONAL_PROFILE_DETAIL_DEFAULT_RECORDS_LIST')?></a>
                </div>
                <form method="post" class="sale-personal-profile-detail-form" action="<?=POST_FORM_ACTION_URI?>" enctype="multipart/form-data">
                    <?=bitrix_sessid_post()?>
                    <input type="hidden" name="ID" value="<?=$arResult["ID"]?>">
                    <div class="sale-personal-profile-detail-block">
                        <div class="sale-personal-profile-detail-title">
                            <?= Loc::getMessage('SALE_PERSONAL_PROFILE_DETAIL_DEFAULT_PROFILE')?>
                        </div>
                        <div class="sale-personal-profile-detail-properties">
                            <div class="sale-personal-profile-detail-property">
                                <label class="sale-personal-profile-detail-property-label">
                                    <div class="sale-personal-profile-detail-property-title">
                                        <?=Loc::getMessage('SALE_PERSONAL_PROFILE_DETAIL_DEFAULT_PERSON_TYPE')?>
                                    </div>
                                    <?= Html::beginTag('input', [
                                        'type' => 'text',
                                        'value' => $arResult["PERSON_TYPE"]["NAME"],
                                        'readonly' => 'readonly',
                                        'disabled' => 'disabled',
                                        'class' => Html::cssClassFromArray([
                                            'sale-personal-profile-detail-property-input' => true,
                                            'intec-ui' => true,
                                            'intec-ui-control-input' => true,
                                        ], true)
                                    ])?>
                                    </label>
                            </div>
                            <div class="sale-personal-profile-detail-property">
                                <label class="sale-personal-profile-detail-property-label">
                                    <div class="sale-personal-profile-detail-property-title">
                                        <?=Loc::getMessage('SALE_PERSONAL_PROFILE_DETAIL_DEFAULT_PNAME')?>: <span class="property-required">*</span>
                                    </div>
                                    <?= Html::beginTag('input', [
                                        'type' => 'text',
                                        'name' => 'NAME',
                                        'maxlength' => 100,
                                        'value' => $arResult["NAME"],
                                        'placeholder' => Loc::getMessage('SALE_PERSONAL_PROFILE_DETAIL_DEFAULT_PNAME'),
                                        'class' => Html::cssClassFromArray([
                                            'sale-personal-profile-detail-property-input' => true,
                                            'intec-ui' => true,
                                            'intec-ui-control-input' => true,
                                        ], true)
                                    ])?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php foreach($arResult["ORDER_PROPS"] as $block) {

                        if (!empty($block["PROPS"])) { ?>
                            <div class="sale-personal-profile-detail-block">
                                <div class="sale-personal-profile-detail-title">
                                    <?= $block["NAME"]?>
                                </div>
                                <div class="sale-personal-profile-detail-properties">
                                    <?php foreach($block["PROPS"] as $property) {
                                        $key = (int)$property["ID"];
                                        $name = "ORDER_PROP_".$key;
                                        $currentValue = $arResult["ORDER_PROPS_VALUES"][$name];
                                        ?>
                                        <div class="sale-personal-profile-detail-property sale-personal-profile-detail-property-<?=strtolower($property["TYPE"])?>">
                                            <label class="sale-personal-profile-detail-property-title" for="<?=$name?>_1">
                                                <?= $property["NAME"]?>: <?= ($property["REQUIED"] == "Y")?'<span class="property-required">*</span>':''?>
                                            </label>
                                            <?php if ($property["TYPE"] == "CHECKBOX") { ?>
                                                <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current">
                                                    <input
                                                        type="checkbox"
                                                        checked="checked"
                                                        name="<?=$name?>"
                                                        id="<?=$name?>_1"
                                                        value="Y"
                                                        <?=($currentValue == "Y" || !isset($currentValue) && $property["DEFAULT_VALUE"] == "Y")?'checked':''?>
                                                    />
                                                    <span class="intec-ui-part-selector"></span>
                                                </label>

                                            <?php } elseif (in_array($property['TYPE'], ['TEXT', 'DATE'])) {

                                                $arFieldsParam = [
                                                    'type' => 'text',
                                                    'name' => $name,
                                                    'maxlength' => 100,
                                                    'value' => $currentValue,
                                                    'id' => $name.'_1',
                                                    'class' => [
                                                        'sale-personal-profile-detail-property-input',
                                                        'property-input-text-multiple',
                                                        'intec-ui',
                                                        'intec-ui-control-input'
                                                    ]
                                                ];

                                                if ($property['TYPE'] == 'DATE')
                                                    $arFieldsParam = ArrayHelper::merge(
                                                        $arFieldsParam,
                                                        [
                                                            'autocomplete' => 'off',
                                                            'readonly' => 'readonly',
                                                            'onclick' => 'BX.calendar({node: this, field: this, bTime: true});'
                                                        ]
                                                    );

                                                if ($property["MULTIPLE"] === 'Y') {
                                                    if (empty($currentValue) || !is_array($currentValue))
                                                        $currentValue = [''];

                                                    $arFieldsParam['name'].= '[]';

                                                    foreach ($currentValue as $elementValue) { ?>
                                                        <?php $arFieldsParam['value'] = $elementValue;?>
                                                        <?= Html::beginTag('input', $arFieldsParam)?>
                                                    <?php } ?>
                                                    <?= Html::beginTag('span', [
                                                        'data-add-type' => $property["TYPE"],
                                                        'data-add-name' => $name.'[]',
                                                        'class' => Html::cssClassFromArray([
                                                            'input-add-multiple' => true,
                                                            'intec-ui' => [
                                                                '' => true,
                                                                'control-button' => true,
                                                                'scheme-current' => true,
                                                                'size-2' => false,
                                                                'mod-round-2' => true
                                                            ]
                                                        ], true)
                                                    ])?>
                                                        <?=Loc::getMessage('SALE_PERSONAL_PROFILE_DETAIL_DEFAULT_ADD')?>
                                                    <?= Html::endTag('span'); ?>
                                                <?php } else {
                                                    $arFieldsParam['placeholder'] = $property["NAME"];
                                                    echo Html::beginTag('input', $arFieldsParam);?>
                                                <?php }
                                            } elseif ($property["TYPE"] == "SELECT") { ?>
                                                <select
                                                    class="intec-ui intec-ui-control-input sale-personal-profile-detail-property-input"
                                                    name="<?=$name?>"
                                                    size="<?echo (intval($property["SIZE1"])>0)?$property["SIZE1"]:1; ?>">
                                                        <?php foreach ($property["VALUES"] as $value) { ?>
                                                            <option value="<?= $value["VALUE"]?>" <?if ($value["VALUE"] == $currentValue || !isset($currentValue) && $value["VALUE"]==$property["DEFAULT_VALUE"]) echo " selected"?>>
                                                                <?= $value["NAME"]?>
                                                            </option>
                                                        <?php } ?>
                                                </select>
                                            <?php } elseif ($property["TYPE"] == "MULTISELECT") { ?>
                                                <select
                                                    class="intec-ui intec-ui-control-input sale-personal-profile-detail-property-input"
                                                    multiple name="<?=$name?>[]"
                                                    size="<?echo (intval($property["SIZE1"])>0)?$property["SIZE1"]:5; ?>">
                                                        <?php
                                                        $arCurVal = $currentValue;
                                                        $arDefVal = unserialize($property["~DEFAULT_VALUE"]);

                                                        foreach($property["VALUES"] as $value) { ?>
                                                            <option value="<?= $value["VALUE"]?>"<?if (in_array($value["VALUE"], $arCurVal) || !isset($currentValue) && in_array($value["VALUE"], $arDefVal)) echo" selected"?>>
                                                                <?= $value["NAME"]?>
                                                            </option>
                                                        <?php } ?>
                                                </select>
                                            <?php } elseif ($property["TYPE"] == "TEXTAREA") { ?>
                                                <?= Html::tag(
                                                    'textarea',
                                                    (isset($currentValue)) ? $currentValue : $property["DEFAULT_VALUE"],
                                                    [
                                                        'name' => $name,
                                                        'id' => $name.'_1',
                                                        'value' => $elementValue,
                                                        'rows' => ((int)($property["SIZE2"])>0)?$property["SIZE2"]:4,
                                                        'cols' => ((int)($property["SIZE1"])>0)?$property["SIZE1"]:40,
                                                        'class' => 'sale-personal-profile-detail-property-input intec-ui intec-ui-control-input'
                                                    ]
                                                );?>
                                            <?php } elseif ($property["TYPE"] == "LOCATION") {
                                                $locationTemplate = ($arParams['USE_AJAX_LOCATIONS'] !== 'Y') ? "popup" : "";
                                                $locationClassName = 'location-block-wrapper';

                                                if ($arParams['USE_AJAX_LOCATIONS'] === 'Y')
                                                    $locationClassName .= ' location-block-wrapper-delimeter';

                                                if ($property["MULTIPLE"] === 'Y') {
                                                    if (empty($currentValue) || !is_array($currentValue))
                                                        $currentValue = array($property["DEFAULT_VALUE"]);

                                                    foreach ($currentValue as $code => $elementValue) {
                                                        $locationValue = intval($elementValue) ? $elementValue : $property["DEFAULT_VALUE"];
                                                        CSaleLocation::proxySaleAjaxLocationsComponent(
                                                            array(
                                                                "ID" => "propertyLocation".$name."[$code]",
                                                                "AJAX_CALL" => "N",
                                                                'CITY_OUT_LOCATION' => 'Y',
                                                                'COUNTRY_INPUT_NAME' => $name.'_COUNTRY',
                                                                'CITY_INPUT_NAME' => $name."[$code]",
                                                                'LOCATION_VALUE' => $locationValue,
                                                            ),
                                                            array(
                                                            ),
                                                            $locationTemplate,
                                                            true,
                                                            $locationClassName
                                                        );
                                                    } ?>
                                                    <?= Html::beginTag('span', [
                                                        'data-add-type' => $property["TYPE"],
                                                        'data-add-name' => $name,
                                                        'data-add-last-key' => $code,
                                                        'data-add-template' => $locationTemplate,
                                                        'class' => Html::cssClassFromArray([
                                                            'input-add-multiple' => true,
                                                            'intec-ui' => [
                                                                '' => true,
                                                                'control-button' => true,
                                                                'scheme-current' => true,
                                                                'size-2' => false,
                                                                'mod-round-2' => true
                                                            ]
                                                        ], true)
                                                    ])?>
                                                        <?=Loc::getMessage('SALE_PERSONAL_PROFILE_DETAIL_DEFAULT_ADD')?>
                                                    <?= Html::endTag('span'); ?>
                                                <?php } else {
                                                    $locationValue = (int)($currentValue) ? (int)$currentValue : $property["DEFAULT_VALUE"];

                                                    CSaleLocation::proxySaleAjaxLocationsComponent(
                                                        array(
                                                            "AJAX_CALL" => "N",
                                                            'CITY_OUT_LOCATION' => 'Y',
                                                            'COUNTRY_INPUT_NAME' => $name.'_COUNTRY',
                                                            'CITY_INPUT_NAME' => $name,
                                                            'LOCATION_VALUE' => $locationValue,
                                                        ),
                                                        array(
                                                        ),
                                                        $locationTemplate,
                                                        true,
                                                        'location-block-wrapper'
                                                    );
                                                }
                                            } elseif ($property["TYPE"] == "RADIO") {
                                                foreach($property["VALUES"] as $value) { ?>
                                                    <label class="intec-ui intec-ui-control-radiobox intec-ui-scheme-current">
                                                        <input
                                                            type="radio"
                                                            name="<?=$name?>"
                                                            value="<?=$value["VALUE"]?>"
                                                            <?=($value["VALUE"] == $currentValue || !isset($currentValue) && $value["VALUE"] == $property["DEFAULT_VALUE"])?'checked':''?>
                                                        />
                                                        <span class="intec-ui-part-selector"></span>
                                                        <span class="intec-ui-part-content"><?= $value["NAME"]?></span>
                                                    </label><br>
                                                <?php }
                                            } elseif ($property["TYPE"] == "FILE") {
                                                $multiple = ($property["MULTIPLE"] === "Y") ? "multiple" : '';
                                                $profileFiles = is_array($currentValue) ? $currentValue : [$currentValue];
                                                if (count($currentValue) > 0) { ?>
                                                    <input type="hidden" name="<?=$name?>_del" class="profile-property-input-delete-file">
                                                    <?php foreach ($profileFiles as $file) { ?>
                                                        <div class="sale-personal-profile-detail-form-file">
                                                            <?
                                                            $fileId = $file['ID'];
                                                            if (CFile::IsImage($file['FILE_NAME'])) { ?>
                                                                <div class="sale-personal-profile-detail-prop-img">
                                                                    <?=CFile::ShowImage($fileId, 150, 150, "border=0", "", true)?>
                                                                </div>
                                                            <?php } else { ?>
                                                                <div>
                                                                    <a download="<?=$file["ORIGINAL_NAME"]?>" href="<?=CFile::GetFileSRC($file)?>">
                                                                        <?=Loc::getMessage('SALE_PERSONAL_PROFILE_DETAIL_DEFAULT_DOWNLOAD_FILE', array("#FILE_NAME#" => $file["ORIGINAL_NAME"]))?>
                                                                    </a>
                                                                </div>
                                                            <?php } ?>
                                                            <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current">
                                                                <input type="checkbox" value="<?=$fileId?>" class="profile-property-check-file" id="profile-property-check-file-<?=$fileId?>">
                                                                <span class="intec-ui-part-selector"></span>
                                                                <span class="intec-ui-part-content"><?=Loc::getMessage('SALE_PERSONAL_PROFILE_DETAIL_DEFAULT_DELETE_FILE')?></span>
                                                            </label>
                                                        </div>
                                                    <?php }
                                                } ?>
                                                <label class="sale-personal-profile-detail-file-choose-wrap">
                                                    <?= Html::beginTag('span', [
                                                        'class' => Html::cssClassFromArray([
                                                            'intec-ui' => [
                                                                '' => true,
                                                                'control-button' => true,
                                                                'scheme-current' => true,
                                                                'size-2' => false,
                                                                'mod-round-2' => true
                                                            ]
                                                        ], true)
                                                    ])?>
                                                        <?=Loc::getMessage('SALE_PERSONAL_PROFILE_DETAIL_DEFAULT_SELECT')?>
                                                    <?= Html::endTag('span'); ?>
                                                    <span class="sale-personal-profile-detail-load-file-info">
                                                        <?=Loc::getMessage('SALE_PERSONAL_PROFILE_DETAIL_DEFAULT_FILE_NOT_SELECTED')?>
                                                    </span>
                                                    <?=CFile::InputFile(
                                                    $name.'[]',
                                                    20,
                                                    null,
                                                    false,
                                                    0,
                                                    "IMAGE",
                                                    "class='sale-personal-profile-detail-input-file sale-personal-profile-detail-input-file-hidden' ".$multiple
                                                    )?>
                                                </label>
                                                <span class="sale-personal-profile-detail-load-file-cancel sale-personal-profile-hide"></span>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php }
                    } ?>
                    <div class="sale-personal-profile-detail-buttons intec-grid intec-grid-wrap intec-grid-i-h-12 intec-grid-i-v-6">
                        <div class="intec-grid-item-auto">
                            <?= Html::beginTag('input', [
                                'type' => 'submit',
                                'name' => 'save',
                                'value' => Loc::getMessage('SALE_PERSONAL_PROFILE_DETAIL_DEFAULT_SAVE'),
                                'class' => Html::cssClassFromArray([
                                    'sale-personal-profile-detail-button' => true,
                                    'intec-ui' => [
                                        '' => true,
                                        'control-button' => true,
                                        'scheme-current' => true,
                                        'size-5' => false,
                                        'mod-round-2' => true
                                    ]
                                ], true)
                            ])?>
                        </div>
                        <div class="intec-grid-item-auto">
                            <?= Html::beginTag('input', [
                                'type' => 'submit',
                                'name' => 'apply',
                                'value' => Loc::getMessage('SALE_PERSONAL_PROFILE_DETAIL_DEFAULT_APPLY'),
                                'class' => Html::cssClassFromArray([
                                    'sale-personal-profile-detail-button' => true,
                                    'intec-ui' => [
                                        '' => true,
                                        'control-button' => true,
                                        'mod-transparent' => true,
                                        'scheme-current' => true,
                                        'size-5' => false,
                                        'mod-round-2' => true
                                    ]
                                ], true)
                            ])?>
                        </div>
                        <div class="intec-grid-item-auto sale-personal-profile-detail-button-right">
                            <?= Html::beginTag('input', [
                                'type' => 'submit',
                                'name' => 'reset',
                                'value' => Loc::getMessage('SALE_PERSONAL_PROFILE_DETAIL_DEFAULT_RESET'),
                                'class' => Html::cssClassFromArray([
                                    'sale-personal-profile-detail-button' => true,
                                    'intec-ui' => [
                                        '' => true,
                                        'control-button' => true,
                                        'mod-round-2' => true
                                    ]
                                ], true)
                            ])?>
                        </div>
                    </div>
                </form>
                <?php
                $javascriptParams = array(
                    "ajaxUrl" => CUtil::JSEscape($this->__component->GetPath().'/ajax.php'),
                );
                $javascriptParams = CUtil::PhpToJSObject($javascriptParams);
                ?>
                <script>
                    BX.message({
                        SPPD_FILE_COUNT: '<?=Loc::getMessage('SALE_PERSONAL_PROFILE_DETAIL_DEFAULT_FILE_COUNT')?>',
                        SPPD_FILE_NOT_SELECTED: '<?=Loc::getMessage('SALE_PERSONAL_PROFILE_DETAIL_DEFAULT_FILE_NOT_SELECTED')?>'
                    });
                    BX.Sale.PersonalProfileComponent.PersonalProfileDetail.init(<?=$javascriptParams?>);
                </script>
            <?php } else
                ShowError($arResult["ERROR_MESSAGE"]);
            ?>
        </div>
    </div>
</div>

