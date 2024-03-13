<?php
/** @var string $REQUEST_METHOD */
/** @var string $RestoreDefaults */
/** @var string $Apply */
/** @var string $Update */
/** @var string $mid */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use \Pec\Delivery\Request;
use \Pec\Delivery\Tools;
use Bitrix\Sale\Location\Admin\LocationHelper as Helper;

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
Loc::loadMessages(__FILE__);

if (!function_exists('curl_init'))
{
    CAdminMessage::ShowMessage(Loc::getMessage('PEC_DELIVERY_OPT_ERROR_CURL'));
    return;
}
if (!function_exists('json_decode'))
{
    CAdminMessage::ShowMessage(Loc::getMessage('PEC_DELIVERY_OPT_ERROR_JSON'));
    return;
}

require_once('lib/pec-api/pecom_kabinet.php');

$module_id = "pecom.ecomm";

Loader::includeModule($module_id);
Loader::includeModule('sale');

$RIGHT = $APPLICATION->GetGroupRight($module_id);
$RIGHT_W = ($RIGHT>="W");
$RIGHT_R = ($RIGHT>="R");
if ($RIGHT_R)
{
	if (
		$REQUEST_METHOD=="POST"
		&& $RIGHT_W
		&& check_bitrix_sessid()
	)
	{
		$_REQUEST['PEC_API_AGENT_ORDER_EXPIRED'] = (int)$_REQUEST['PEC_API_AGENT_ORDER_EXPIRED'];
		if ($_REQUEST['PEC_API_AGENT_ORDER_EXPIRED'] > 60) {
			$_REQUEST['PEC_API_AGENT_ORDER_EXPIRED'] = 60;
		}

		$arPecSettingsString = ['PEC_GET_USER_ADDRESS', 'PEC_STORE_ADDRESS', 'PEC_STORE_PZZ', 'PEC_WEIGHT', 'PEC_LENGTH_D', 'PEC_WIDTH_D', 'PEC_HEIGHT_D',
            'PEC_STORE_TITLE', 'PEC_STORE_INN', 'PEC_STORE_KPP', 'PEC_STORE_PERSON', 'PEC_STORE_PHONE',
			'PEC_API_TYPE_DELIVERY', 'PEC_SHOW_TYPE_WIDGET', 'PEC_API_LOGIN', 'PEC_API_KEY', 'PEC_API_AGENT_ORDER_EXPIRED',
			'PEC_INN', 'PEC_DOCUMENT_TYPE', 'PEC_DOCUMENT_SERIES', 'PEC_DOCUMENT_NUMBER', 'PEC_DOCUMENT_DATE',
			'PEC_DELIVERY_ADDRESS', 'PEC_ORDER_SEND', 'PEC_ORDER_CREATE', 'PEC_STORE_TYPE', 'PEC_PRINT_LABEL',
            'PEC_API_URL', 'PEC_WIDGET_API_URL', 'PEC_WIDGET_URL', 'PEC_WIDGET_MAP_PICKER_URL',
            'PEC_API_LOGIN_BASE', 'PEC_API_LOGIN_TEST', 'PEC_API_LOGIN_CUSTOM',
            'PEC_API_KEY_BASE', 'PEC_API_KEY_TEST', 'PEC_API_KEY_CUSTOM',
			'PEC_SAVE_PDF_URL', 'PEC_API_AGENT_PERIOD', 'PEC_SELF_PACK_INPUT', 'PEC_DELIVERY_APARTMENT', 'ID_FOR_INSERT_AFTER_PEC_PICKUP_BLOCK',
            'PEC_FROM_MAP_PICKER_DATA', 'PEC_STORE_DEPARTMENT_UID',
        ];

		foreach ($arPecSettingsString as $key) {
			if($_REQUEST[$key])
			{
				if ($key == 'PEC_ORDER_SEND' && $_REQUEST[$key] != 'U')
					COption::RemoveOption($module_id, "PEC_ORDER_CREATE");
				if(in_array($key, ['PEC_WEIGHT', 'PEC_LENGTH_D', 'PEC_WIDTH_D', 'PEC_HEIGHT_D'])) {
					$_REQUEST[$key] = str_replace(',', '.', $_REQUEST[$key]);
				}
				Option::set($module_id, $key, trim($_REQUEST[$key]));
			}
			else
			{
				$value = '';

				if ($key == 'PEC_INN') {
                    $value = CSaleOrderProps::GetList(array(), array("NAME" => Loc::getMessage("PEC_DELIVERY_INN")))->Fetch()['CODE'];
                }
                if ($key == 'PEC_DOCUMENT_TYPE') {
                    $value = CSaleOrderProps::GetList(array(), array("CODE" => "PEC_DOCUMENT_TYPE"))->Fetch()['CODE'];
                }
                if ($key == 'PEC_DELIVERY_ADDRESS') {
                    $value = 'PEC_DELIVERY_ADDRESS';
                }
                if ($key == 'PEC_DELIVERY_APARTMENT') {
                    $value = 'FLAT_NUM';
                }
                if ($key == 'ID_FOR_INSERT_AFTER_PEC_PICKUP_BLOCK') {
                    $value = 'bx-soa-delivery';
                }
                if ($key == 'PEC_API_URL') {
                    $value = PecomKabinet::API_BASE_URL;
                }
                if ($key == 'PEC_WIDGET_URL') {
                    $value = 'https://calc.pecom.ru/iframe/e-store-calculator';
                }
                if ($key == 'PEC_WIDGET_API_URL') {
                    $value = 'https://calc.pecom.ru/api/e-store-calculate';
                }
                if ($key == 'PEC_WIDGET_MAP_PICKER_URL') {
                    $value = 'https://calc.pecom.ru/map-picker';
                }
				Option::set($module_id, $key, trim($value));
			}
		}

		$arPecSettingArray = ['PEC_API_ALLOW_DELIVERY', 'PEC_API_SHIPPED', 'PEC_API_STATUS_TABLE', 'PEC_API_START_AGENT'];
		foreach ($arPecSettingArray as $key) {
			if($_REQUEST[$key])
			{
				Option::set($module_id, $key, serialize($_REQUEST[$key]));
			}
            elseif ($_REQUEST['PEC_API_SHIPPED']) // If setup table given (no login - no table)
			{
				Option::set($module_id, $key, '');
			}
		}
		$arPecSettingsBool = ['PEC_SAFE_PRICE', 'PEC_SELF_PACK', 'PEC_COST_OUT', 'PEC_SHOW_WIDGET', 'PEC_SAVE_PDF', 'PEC_API_AGENT_ACTIVE'];
		foreach ($arPecSettingsBool as $key) {
			if($_REQUEST[$key])
			{
				Option::set($module_id, $key, 1);
			}
			else
			{
				Option::set($module_id, $key, 0);
			}
		}
		foreach ($_REQUEST['PEC_STORE_DOP'] as $sklad) {
			if($sklad['parent_id'] && $sklad['address']) {
				$dop_sklads[] = $sklad;
			}
		}
		if (!empty($dop_sklads)) {
			Option::set($module_id, 'PEC_STORE_DOP', serialize($dop_sklads));
		} else {
			Option::set($module_id, 'PEC_STORE_DOP', '');
		}

		// agent params
		$interval = $_REQUEST['PEC_API_AGENT_PERIOD'];
		if (!$interval || (int)$interval < 5) $interval = 120;
		$arField = ['AGENT_INTERVAL' => $interval * 60];
		$arField['ACTIVE'] = Option::get($module_id, 'PEC_API_AGENT_ACTIVE', '') ? 'Y' : 'N';
		$arOldAgent = CAgent::GetList([], ["NAME" => '\Pec\Delivery\Tools::agentUpdateOrdersPecStatus();'])->Fetch();
		$objDateTime = new DateTime("+10 seconds");
		$date = $objDateTime->format("d.m.Y H:i:s");
		$arField['NEXT_EXEC'] = $date;
		if ($arOldAgent)
			CAgent::Update($arOldAgent["ID"], $arField);
		else
			Tools::RegisterAgent($arField['AGENT_INTERVAL']);
	}

	$optionPecApiAllowDelivery = array_filter(unserialize(Option::get($module_id, 'PEC_API_ALLOW_DELIVERY', '')));
	$optionPecApiShipped = array_filter(unserialize(Option::get($module_id, 'PEC_API_SHIPPED', '')));
	$optionPecApiStatusTable = array_filter(unserialize(Option::get($module_id, 'PEC_API_STATUS_TABLE', '')));
	$optionPecApiStartAgent = array_filter(unserialize(Option::get($module_id, 'PEC_API_START_AGENT', '')));

	$arAgent = CAgent::GetList([], ["NAME" => '\Pec\Delivery\Tools::agentUpdateOrdersPecStatus();'])->Fetch();
	if ($arAgent) {
		$optionAgentActive = $arAgent['ACTIVE'] == 'Y' ? 1 : 0;
	} else {
		Tools::RegisterAgent();
		$optionAgentActive = 0;
	}


	$request = new Request;
	$pecStatus = $request->getAllStatus();

	$aTabs = array(
		array("DIV" => "edit1", "TAB" => Loc::getMessage("PEC_DELIVERY_SETTING"), "ICON" => "", "TITLE" => Loc::getMessage("PEC_DELIVERY_SETTING_TITLE")),
		array("DIV" => "edit2", "TAB" => Loc::getMessage("PEC_DELIVERY_SETTING_STORE"), "ICON" => "", "TITLE" => Loc::getMessage("PEC_DELIVERY_SETTING_STORE")),
		array("DIV" => "edit3", "TAB" => Loc::getMessage("PEC_DELIVERY_API"), "ICON" => "", "TITLE" => Loc::getMessage("PEC_DELIVERY_API_TITLE")),
		array("DIV" => "edit4", "TAB" => Loc::getMessage("PEC_DELIVERY_FAQ"), "ICON" => "", "TITLE" => Loc::getMessage("PEC_DELIVERY_FAQ")),
        array("DIV" => "edit5", "TAB" => Loc::getMessage("PEC_DELIVERY_INTEGRATION"), "ICON" => "", "TITLE" => Loc::getMessage("PEC_DELIVERY_INTEGRATION")),
	);
    if (Option::get($module_id, 'TAB_ACTIVE', 'N') === 'Y') {
        $arr = array("DIV" => "edit6", "TAB" => Loc::getMessage("PEC_WIDGET_URL"), "ICON" => "", "TITLE" => Loc::getMessage("PEC_WIDGET_URL_TITLE"));
        $aTabs[] = $arr;
    }
	$tabControl = new CAdminTabControl("tabControl", $aTabs);
	$tabControl->Begin();

	$storeTypes = array(
		["id" => 0, "name" => Loc::getMessage("PEC_SETUP_OWNER")],
		["id" => 1, "name" => Loc::getMessage("PEC_SETUP_OWNER_ANO")],
		["id" => 2, "name" => Loc::getMessage("PEC_SETUP_OWNER_AO")],
		["id" => 3, "name" => Loc::getMessage("PEC_SETUP_OWNER_GAUK")],
		["id" => 4, "name" => Loc::getMessage("PEC_SETUP_OWNER_GAUK")],
		["id" => 5, "name" => Loc::getMessage("PEC_SETUP_OWNER_GAUSO")],
		["id" => 6, "name" => Loc::getMessage("PEC_SETUP_OWNER_GBUZ")],
		["id" => 7, "name" => Loc::getMessage("PEC_SETUP_OWNER_GOBU")],
		["id" => 8, "name" => Loc::getMessage("PEC_SETUP_OWNER_GOUVPO")],
		["id" => 9, "name" => Loc::getMessage("PEC_SETUP_OWNER_GP")],
		["id" => 10, "name" => Loc::getMessage("PEC_SETUP_OWNER_GUZ")],
		["id" => 11, "name" => Loc::getMessage("PEC_SETUP_OWNER_GUP")],
		["id" => 13, "name" => Loc::getMessage("PEC_SETUP_OWNER_DOAO")],
		["id" => 14, "name" => Loc::getMessage("PEC_SETUP_OWNER_ZAO")],
		["id" => 15, "name" => Loc::getMessage("PEC_SETUP_OWNER_IP")],
		["id" => 16, "name" => Loc::getMessage("PEC_SETUP_OWNER_KGBUZ")],
		["id" => 17, "name" => Loc::getMessage("PEC_SETUP_OWNER_KGOY")],
		["id" => 18, "name" => Loc::getMessage("PEC_SETUP_OWNER_KT")],
		["id" => 19, "name" => Loc::getMessage("PEC_SETUP_OWNER_KFX")],
		["id" => 20, "name" => Loc::getMessage("PEC_SETUP_OWNER_MAOY")],
		["id" => 21, "name" => Loc::getMessage("PEC_SETUP_OWNER_MAY")],
		["id" => 22, "name" => Loc::getMessage("PEC_SETUP_OWNER_MKP")],
		["id" => 23, "name" => Loc::getMessage("PEC_SETUP_OWNER_MC")],
		["id" => 24, "name" => Loc::getMessage("PEC_SETUP_OWNER_MUP")],
		["id" => 25, "name" => Loc::getMessage("PEC_SETUP_OWNER_NAO")],
		["id" => 26, "name" => Loc::getMessage("PEC_SETUP_OWNER_NOYVPO")],
		["id" => 27, "name" => Loc::getMessage("PEC_SETUP_OWNER_NP")],
		["id" => 28, "name" => Loc::getMessage("PEC_SETUP_OWNER_OAO")],
		["id" => 29, "name" => Loc::getMessage("PEC_SETUP_OWNER_ODO")],
		["id" => 30, "name" => Loc::getMessage("PEC_SETUP_OWNER_OOO")],
		["id" => 31, "name" => Loc::getMessage("PEC_SETUP_OWNER_OC")],
		["id" => 32, "name" => Loc::getMessage("PEC_SETUP_OWNER_PAO")],
		["id" => 33, "name" => Loc::getMessage("PEC_SETUP_OWNER_PK")],
		["id" => 34, "name" => Loc::getMessage("PEC_SETUP_OWNER_PT")],
		["id" => 35, "name" => Loc::getMessage("PEC_SETUP_OWNER_PO")],
		["id" => 36, "name" => Loc::getMessage("PEC_SETUP_OWNER_ROPP")],
		["id" => 37, "name" => Loc::getMessage("PEC_SETUP_OWNER_CNGOC")],
		["id" => 38, "name" => Loc::getMessage("PEC_SETUP_OWNER_COOO")],
		["id" => 39, "name" => Loc::getMessage("PEC_SETUP_OWNER_CPK")],
		["id" => 40, "name" => Loc::getMessage("PEC_SETUP_OWNER_TDO")],
		["id" => 41, "name" => Loc::getMessage("PEC_SETUP_OWNER_TOO")],
		["id" => 42, "name" => Loc::getMessage("PEC_SETUP_OWNER_UP")],
		["id" => 43, "name" => Loc::getMessage("PEC_SETUP_OWNER_FBU")],
		["id" => 44, "name" => Loc::getMessage("PEC_SETUP_OWNER_FBUZ")],
		["id" => 45, "name" => Loc::getMessage("PEC_SETUP_OWNER_FGAOYVPO")],
		["id" => 46, "name" => Loc::getMessage("PEC_SETUP_OWNER_FGAY")],
		["id" => 47, "name" => Loc::getMessage("PEC_SETUP_OWNER_FGBOYVPO")],
		["id" => 48, "name" => Loc::getMessage("PEC_SETUP_OWNER_FGBU")],
		["id" => 49, "name" => Loc::getMessage("PEC_SETUP_OWNER_FGU")],
		["id" => 50, "name" => Loc::getMessage("PEC_SETUP_OWNER_FGUP")],
		["id" => 51, "name" => Loc::getMessage("PEC_SETUP_OWNER_FKU")],
		["id" => 52, "name" => Loc::getMessage("PEC_SETUP_OWNER_FOND")],
		["id" => 53, "name" => Loc::getMessage("PEC_SETUP_OWNER_FCGAOY")],
		["id" => 54, "name" => Loc::getMessage("PEC_SETUP_OWNER_CHNOYVPO")],
		["id" => 55, "name" => Loc::getMessage("PEC_SETUP_OWNER_CHP")]
	);
	?>
    <form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>">
		<?=bitrix_sessid_post()?>
		<?php $tabControl->BeginNextTab();?>
        <div class="adm-detail-content-item-block">
            <div style="display: flex;">
                <div style="width: 45%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_ADDRESS_RECEPTION'))?>
					<?=Loc::getMessage("PEC_DELIVERY_ADDRESS_RECEPTION")?>
                </div>
                <div>
					<?php
                    $check_personal = (Option::get($module_id, "PEC_GET_USER_ADDRESS") == 'personal') ? 'checked' : '';
					$check_work = (Option::get($module_id, "PEC_GET_USER_ADDRESS") == 'work') ? 'checked' : '';
					$check_no = (Option::get($module_id, "PEC_GET_USER_ADDRESS") == 'no') ? 'checked' : '';
					if ($check_personal == '' && $check_work == '') {
						$check_no = 'checked';
					}
					?>
                    <input type="radio" value="personal" name="PEC_GET_USER_ADDRESS" <?=$check_personal?>><?=Loc::getMessage("PEC_DELIVERY_USER_ADDRESS1")?>
                    <input type="radio" value="work" name="PEC_GET_USER_ADDRESS" <?=$check_work?>><?=Loc::getMessage("PEC_DELIVERY_USER_ADDRESS2")?>
                    <input type="radio" value="no" name="PEC_GET_USER_ADDRESS" <?=$check_no?>><?=Loc::getMessage("PEC_DELIVERY_USER_ADDRESS3")?>
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 45%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_ADDRESS'))?>
					<?=Loc::getMessage("PEC_DELIVERY_ADDRESS")?>:
                </div>
                <div style="width: 100%;">
                    <?php
                    $baseSrc = Option::get('pecom.ecomm', 'PEC_WIDGET_MAP_PICKER_URL');
                    if($baseSrc !== 'https://calc.pecom.ru/map-picker'){
                        $baseSrc = 'https://calc.pecom.ru/map-picker';
                    }
                        $attrs = [
                            'direction-type' => (Option::get('pecom.ecomm', 'PEC_STORE_PZZ', 'pzz') == 'store') ? 'address' : 'department',
                            'address' => Option::get('pecom.ecomm', "PEC_STORE_ADDRESS", Loc::getMessage('PEC_SETUP_STORE_CITY_DEFAULT')),
                            'department-uid' => Option::get('pecom.ecomm', "PEC_STORE_DEPARTMENT_UID", ''),
                        ];
                        $attrs = array_map(function($key, $value) {
                            return sprintf('%s=%s', $key, $value);
                        }, array_keys($attrs), array_values($attrs));
                        $attrs = implode('&', $attrs);
                        $src = sprintf('%s?%s', $baseSrc, $attrs);
                    ?>
                    <button class="adm-btn" type="button" id="map_picker_show_buttom" onclick="
                        document.getElementById('map_picker_show_buttom').style.display = 'none';
                        document.getElementById('map_picker_hide_buttom').style.display = 'block';
                        document.getElementById('map_picker_iframe').style.display = 'block';
                    " style="margin: 0 10px 10px 0;"><?=Loc::getMessage('PEC_DELIVERY_BTN_MAP_CHOOSE')?></button>
                    <button class="adm-btn" type="button" id="map_picker_hide_buttom" onclick="
                        document.getElementById('map_picker_show_buttom').style.display = 'block';
                        document.getElementById('map_picker_hide_buttom').style.display = 'none';
                        document.getElementById('map_picker_iframe').style.display = 'none';
                    " style="display: none; margin: 0 10px 10px 0;"><?=Loc::getMessage('PEC_DELIVERY_BTN_MAP_CLOSE')?></button>
                    <iframe id="map_picker_iframe" src="<?=$src?>" width="100%" height="552" frameborder="0" style="border: 1px solid #e0e8ea; display: none;"></iframe>
                    <div style="display: flex; margin-bottom: 5px; margin-top: 5px;">
                        <div style="width: 45%;"><?=Loc::getMessage('PEC_DELIVERY_PICKUP_TYPE')?></div>
                        <div>
                            <input id="pec_from_type" type="text" disabled style="width: 376px;">
                        </div>
                    </div>
                    <div style="display: flex; margin-bottom: 5px;">
                        <div style="width: 45%;"><?=Loc::getMessage('PEC_DELIVERY_FROM_ADDRESS')?></div>
                        <div>
                                <textarea id="pec_from_address" type="text" disabled style="width: 376px; resize: none; height: 45px;"></textarea>
                        </div>
                    </div>
                    <div style="display: flex; margin-bottom: 5px;">
                        <div style="width: 45%;"><?=Loc::getMessage('PEC_DELIVERY_FROM_UID')?></div>
                        <div>
                            <input id="pec_from_department_uid" type="text" disabled style="width: 376px;">
                        </div>
                    </div>
                    <?php
                    $mapPickerOptions = [
                        'pec_from_latitude' => Loc::getMessage('PEC_DELIVERY_FROM_LAT'),
                        'pec_from_longitude' => Loc::getMessage('PEC_DELIVERY_FROM_LON'),
                        'pec_from_max_weight' => Loc::getMessage('PEC_DELIVERY_FROM_WEIGHT'),
                        'pec_from_max_volume' => Loc::getMessage('PEC_DELIVERY_FROM_VOLUME'),
                        'pec_from_weight_per_place' => Loc::getMessage('PEC_DELIVERY_FROM_WEIGHT_PP'),
                        'pec_from_dimension' => Loc::getMessage('PEC_DELIVERY_FROM_DIM'),
                        'pec_from_avia' => Loc::getMessage('PEC_DELIVERY_FROM_AVIA'),
                    ];
                    foreach ($mapPickerOptions as $optionId => $optionName) {?>
                        <div style="display: flex; margin-bottom: 5px;">
                            <div style="width: 45%;">
                                <?=$optionName?>
                            </div>
                            <div>
                                <input id="<?=$optionId?>" type="text" disabled>
                            </div>
                        </div>
                    <?php }?>
                    <div><?=Loc::getMessage('PEC_DELIVERY_FROM_EASYWAY')?></div>
                    <input type="hidden" id="PEC_STORE_PZZ" name="PEC_STORE_PZZ"
                           value="<?=Option::get('pecom.ecomm', "PEC_STORE_PZZ", 'pzz')?>">
                    <input type="hidden" id="PEC_FROM_MAP_PICKER_DATA" name="PEC_FROM_MAP_PICKER_DATA"
                           value="<?=Option::get('pecom.ecomm', "PEC_FROM_MAP_PICKER_DATA", '')?>">
                    <input type="hidden" id="PEC_STORE_ADDRESS" name="PEC_STORE_ADDRESS"
                           value="<?=Option::get('pecom.ecomm', "PEC_STORE_ADDRESS", '')?>">
                    <input type="hidden" id="PEC_STORE_DEPARTMENT_UID" name="PEC_STORE_DEPARTMENT_UID"
                           value="<?=Option::get('pecom.ecomm', "PEC_STORE_DEPARTMENT_UID", '')?>">
                    <script type="text/javascript">
                        let widgetListener = window.addEventListener('message', (event) => {
                            let pecDelivery = event.data.pecDelivery;
                            console.log(event.data);
                            document.getElementById('pec_from_address').value = pecDelivery.result.address;
                            document.getElementById('PEC_STORE_ADDRESS').value = pecDelivery.result.address;
                            document.getElementById('pec_from_latitude').value = pecDelivery.result.coordinates.latitude;
                            document.getElementById('pec_from_longitude').value = pecDelivery.result.coordinates.longitude;
                            document.getElementById('pec_from_longitude').value = pecDelivery.result.coordinates.longitude;
                            if (event.data.pecDelivery.result.type == 'address') {
                                document.getElementById('PEC_STORE_PZZ').value = 'store';
                                document.getElementById('pec_from_type').value = '<?=Loc::getMessage('PEC_DELIVERY_FROM_TYPE_STORE')?>';

                                document.getElementById('pec_from_department_uid').parentNode.parentNode.style.display = 'none';
                                document.getElementById('pec_from_max_weight').parentNode.parentNode.style.display = 'none';
                                document.getElementById('pec_from_max_volume').parentNode.parentNode.style.display = 'none';
                                document.getElementById('pec_from_weight_per_place').parentNode.parentNode.style.display = 'none';
                                document.getElementById('pec_from_dimension').parentNode.parentNode.style.display = 'none';
                                document.getElementById('pec_from_avia').parentNode.parentNode.style.display = 'none';
                            } else {
                                document.getElementById('PEC_STORE_PZZ').value = 'pzz';
                                document.getElementById('pec_from_type').value = '<?=Loc::getMessage('PEC_DELIVERY_FROM_TYPE_PZZ')?>';
                                document.getElementById('PEC_FROM_MAP_PICKER_DATA').value = JSON.stringify(pecDelivery.result.departmentData);

                                document.getElementById('pec_from_department_uid').value = pecDelivery.result.departmentUid;
                                document.getElementById('PEC_STORE_DEPARTMENT_UID').value = pecDelivery.result.departmentUid;
                                if (pecDelivery.result.departmentData.Warehouses[0].MaxWeight) {
                                    document.getElementById('pec_from_max_weight').value = pecDelivery.result.departmentData.Warehouses[0].MaxWeight;
                                } else {
                                    document.getElementById('pec_from_max_weight').value = '<?=Loc::getMessage('PEC_DELIVERY_FROM_NO_LIMIT')?>';
                                }
                                if (pecDelivery.result.departmentData.Warehouses[0].MaxVolume) {
                                    document.getElementById('pec_from_max_volume').value = pecDelivery.result.departmentData.Warehouses[0].MaxVolume;
                                } else {
                                    document.getElementById('pec_from_max_volume').value = '<?=Loc::getMessage('PEC_DELIVERY_FROM_NO_LIMIT')?>';
                                }
                                if (pecDelivery.result.departmentData.Warehouses[0].MaxWeightOnePlace) {
                                    document.getElementById('pec_from_weight_per_place').value = pecDelivery.result.departmentData.Warehouses[0].MaxWeightOnePlace;
                                } else {
                                    document.getElementById('pec_from_weight_per_place').value = '<?=Loc::getMessage('PEC_DELIVERY_FROM_NO_LIMIT')?>';
                                }
                                if (pecDelivery.result.departmentData.Warehouses[0].MaxDimension) {
                                    document.getElementById('pec_from_dimension').value = pecDelivery.result.departmentData.Warehouses[0].MaxDimension;
                                } else {
                                    document.getElementById('pec_from_dimension').value = '<?=Loc::getMessage('PEC_DELIVERY_FROM_NO_LIMIT')?>';
                                }
                                let avia = false;
                                event.data.pecDelivery.result.departmentData.OperationsTransportation.forEach(element => {
                                    console.log(element.transport.KindOfTransportationTypeGuid);
                                    if (element.transport.KindOfTransportationTypeGuid == '7e952741-5791-41e4-a853-6d3e0e40527b') {
                                        avia = true;
                                    }
                                });
                                document.getElementById('pec_from_avia').value = avia ? '<?=Loc::getMessage('PEC_DELIVERY_Y')?>' : '<?=Loc::getMessage('PEC_DELIVERY_N')?>';

                                document.getElementById('pec_from_department_uid').parentNode.parentNode.style.display = 'flex';
                                document.getElementById('pec_from_max_weight').parentNode.parentNode.style.display = 'flex';
                                document.getElementById('pec_from_max_volume').parentNode.parentNode.style.display = 'flex';
                                document.getElementById('pec_from_weight_per_place').parentNode.parentNode.style.display = 'flex';
                                document.getElementById('pec_from_dimension').parentNode.parentNode.style.display = 'flex';
                                document.getElementById('pec_from_avia').parentNode.parentNode.style.display = 'flex';
                            }
                        });
                    </script>
                </div>
            </div>
            <?php
            $sklads = is_array(unserialize(Option::get($module_id, "PEC_STORE_DOP"))) ?
				unserialize(Option::get($module_id, "PEC_STORE_DOP")) : [];
			if(empty($sklads)) {
				?><div style="display: none"><?php
                $APPLICATION->IncludeComponent("bitrix:sale.location.selector.".Helper::getWidgetAppearance(), "", array(
					"ID" => "",
					"CODE" => "",
					"INPUT_NAME" => "",
					"PROVIDE_LINK_BY" => "id",
					"SHOW_ADMIN_CONTROLS" => 'Y',
					"SELECT_WHEN_SINGLE" => 'N',
					"FILTER_BY_SITE" => 'N',
					"SHOW_DEFAULT_LOCATIONS" => 'N',
					"SEARCH_BY_PRIMARY" => 'Y',
				),
					false
				);?></div><?php
            }
			foreach ($sklads as $key => $sklad) {
				?>
                <div style="display: flex; border-top: 1px solid grey; margin-top: 15px; padding-top: 15px" class="dop_sklad"
                     id="div-dop-sklad-<?=$key?>">
                    <div style="width: 45%;"><?=Loc::getMessage("PEC_SETUP_OTHER_ADDRESS_LABEL")?><br><br>
                        <div style="padding: 0 20px 20px 0">
							<?php $APPLICATION->IncludeComponent("bitrix:sale.location.selector.".Helper::getWidgetAppearance(), "", array(
								"ID" => $sklad['parent_id'],
								"CODE" => "",
								"INPUT_NAME" => "PEC_STORE_DOP[$key][parent_id]", //element[PARENT_ID]
								"PROVIDE_LINK_BY" => "id",
								"SHOW_ADMIN_CONTROLS" => 'Y',
								"SELECT_WHEN_SINGLE" => 'N',
								"FILTER_BY_SITE" => 'N',
								"SHOW_DEFAULT_LOCATIONS" => 'N',
								"SEARCH_BY_PRIMARY" => 'Y',
								//"EXCLUDE_SUBTREE" => $nodeId,
							),
								false
							);?>
                        </div>
                    </div>
                    <div>
                        <br><br>
                        <textarea  rows="2" cols="45" name="PEC_STORE_DOP[<?=$key?>][address]"><?=$sklad['address'];?></textarea>
                        <div><?=Loc::getMessage("PEC_SETUP_OTHER_ADDRESS_LABEL2")?></div>
                        <br>
                        <input type="radio" value="1" name="PEC_STORE_DOP[<?=$key?>][intake]" <?php if($sklad['intake'] == 1) echo 'checked'?>><?=Loc::getMessage("PEC_DELIVERY_BY_ADDRESS")?>
                        <input type="radio" value="0" name="PEC_STORE_DOP[<?=$key?>][intake]" <?php if($sklad['intake'] == 0) echo 'checked'?>><?=Loc::getMessage("PEC_DELIVERY_IN_DEPARTMENT")?>
                    </div>
                    <div style="padding: 30px">
                        <button type="button" class="adm-btn" id="but-dop-sklad-<?=$key?>"><?=Loc::getMessage("PEC_SETUP_OTHER_ADDRESS_LABEL3")?></button>
                    </div>
                    <script>
                        BX.ready(function (){
                            BX('but-dop-sklad-<?=$key?>').addEventListener('click',function (){
                                BX.remove(BX('div-dop-sklad-<?=$key?>'));
                            });
                        });
                    </script>
                </div>
				<?php
            }
			?>
            <div id="but-dop-sklad-add" style="display: flex;"><button type="button" class="adm-btn"><?=Loc::getMessage("PEC_SETUP_OTHER_ADDRESS_LABEL4")?></button></div>
            <script>
                BX.ready(function (){
                    var but=BX('but-dop-sklad-add');
                    but.addEventListener('click',function (){
                        BX.ajax({
                            url: '/bitrix/js/pecom.ecomm/ajax.php',
                            data: {
                                method:'dopSklad',
                            },
                            method: 'POST',
                            timeout: 30,
                            async: false,
                            onsuccess: function(data){
                                var div=document.createElement("div");
                                div.innerHTML=data;
                                BX.insertBefore(div,but);
                            }
                        });
                    });
                });
            </script>
        </div>
        <div class="adm-detail-content-item-block">
            <div style="display: flex;">
                <div style="width: 45%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_WEIGHT'))?>
					<?=Loc::getMessage("PEC_DELIVERY_WEIGHT")?>
                </div>
                <div>
                    <input type="text" value="<?=Option::get($module_id, "PEC_WEIGHT", '0.05');?>" name="PEC_WEIGHT">
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 45%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_LENGTH_D'))?>
                    <?=Loc::getMessage("PEC_DELIVERY_LENGTH_D")?>
                </div>
                <div>
                    <input type="text" value="<?=Option::get($module_id, "PEC_LENGTH_D", '200');?>" name="PEC_LENGTH_D">
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 45%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_WIDTH_D'))?>
                    <?=Loc::getMessage("PEC_DELIVERY_WIDTH_D")?>
                </div>
                <div>
                    <input type="text" value="<?=Option::get($module_id, "PEC_WIDTH_D", '200');?>" name="PEC_WIDTH_D">
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 45%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_HEIGHT_D'))?>
                    <?=Loc::getMessage("PEC_DELIVERY_HEIGHT_D")?>
                </div>
                <div>
                    <input type="text" value="<?=Option::get($module_id, "PEC_HEIGHT_D", '200');?>" name="PEC_HEIGHT_D">
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 45%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_PRICE'))?>
					<?=Loc::getMessage("PEC_DELIVERY_PRICE")?>
                </div>
                <div>
                    <input type="checkbox" value="Y" name="PEC_SAFE_PRICE" <?php if(Option::get($module_id, "PEC_SAFE_PRICE", '0')) echo 'checked';?>>
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 45%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_ZTU'))?>
					<?=Loc::getMessage("PEC_DELIVERY_ZTU")?>
                </div>
                <div>
                    <input type="checkbox" value="Y" id="PEC_SELF_PACK" name="PEC_SELF_PACK" <?php if(Option::get($module_id, "PEC_SELF_PACK", '0')) echo 'checked';?>>
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 45%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_ZTU_INPUT'))?>
					<?=Loc::getMessage("PEC_DELIVERY_ZTU_INPUT")?>
                </div>
                <div>
                    <input type="text" id="PEC_SELF_PACK_INPUT" value="<?=Option::get($module_id, "PEC_SELF_PACK_INPUT");?>" name="PEC_SELF_PACK_INPUT">
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 45%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_PAYMENT_DELIVERY'))?>
					<?=Loc::getMessage("PEC_DELIVERY_PAYMENT_DELIVERY")?>
                </div>
                <div>
                    <input type="checkbox" name="PEC_COST_OUT" <?php if(Option::get($module_id, "PEC_COST_OUT", '1')) echo 'checked';?>>
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 45%;">
					<?=Loc::getMessage("PEC_DELIVERY_SHOW_TYPE_WIDGET")?>
                </div>
                <div>
                    <select name="PEC_SHOW_TYPE_WIDGET">
                        <option value="show" <?php if(Option::get($module_id, "PEC_SHOW_TYPE_WIDGET", '0') == 'show') echo 'selected';?>><?=Loc::getMessage("PEC_DELIVERY_SHOW_TYPE_VERSION1")?></option>
                        <option value="hide" <?php if(Option::get($module_id, "PEC_SHOW_TYPE_WIDGET", '0') == 'hide') echo 'selected';?>><?=Loc::getMessage("PEC_DELIVERY_SHOW_TYPE_VERSION2")?></option>
                        <option value="modal" <?php if(Option::get($module_id, "PEC_SHOW_TYPE_WIDGET", '0') == 'modal') echo 'selected';?>><?=Loc::getMessage("PEC_DELIVERY_SHOW_TYPE_VERSION3")?></option>
                    </select>
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 45%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_TYPE'))?>
					<?=Loc::getMessage("PEC_DELIVERY_TYPE")?>
                </div>
                <div>
                    <select name="PEC_API_TYPE_DELIVERY">
                        <option value="auto" <?php if(Option::get($module_id, "PEC_API_TYPE_DELIVERY", '0') == 'auto') echo 'selected';?>><?=Loc::getMessage("PEC_DELIVERY_TYPE_AVTO")?></option>
                        <option value="avia" <?php if(Option::get($module_id, "PEC_API_TYPE_DELIVERY", '0') == 'avia') echo 'selected';?>><?=Loc::getMessage("PEC_DELIVERY_TYPE_AVIA")?></option>
                        <option value="easyway" <?php if(Option::get($module_id, "PEC_API_TYPE_DELIVERY", '0') == 'easyway') echo 'selected';?>><?=Loc::getMessage("PEC_DELIVERY_TYPE_EASY_WAY")?></option>
                    </select>
                </div>
            </div>
        </div>

		<?php $tabControl->BeginNextTab();?>
        <div class="adm-detail-content-item-block">

            <div style="display: flex;">
                <div style="width: 45%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_INN_TEXT'))?>
					<?=Loc::getMessage("PEC_INN_TEXT")?>
                </div>
                <div>
					<?php $inn = CSaleOrderProps::GetList(array(), array("NAME" => Loc::getMessage("PEC_DELIVERY_INN")))->Fetch()['CODE']; ?>
                    <input type="text" value="<?=Option::get($module_id, "PEC_INN", $inn, 's1');?>" name="PEC_INN">
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 45%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_DOCUMENT_TYPE_TEXT'))?>
					<?=Loc::getMessage("PEC_DOCUMENT_TYPE_TEXT")?>
                </div>
                <div><?php
                    $type = CSaleOrderProps::GetList(array(), array("CODE" => "PEC_DOCUMENT_TYPE"))->Fetch()['CODE'];
					?>
                    <div>
                        <input type="text" value="<?=Option::get($module_id, "PEC_DOCUMENT_TYPE", $type);?>" name="PEC_DOCUMENT_TYPE">
                    </div>
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 45%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_DOCUMENT_SERIES_TEXT'))?>
					<?=Loc::getMessage("PEC_DOCUMENT_SERIES_TEXT")?>
                </div>
                <div>
                    <input type="text" value="<?=Option::get($module_id, "PEC_DOCUMENT_SERIES", '');?>" name="PEC_DOCUMENT_SERIES">
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 45%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_DOCUMENT_NUMBER_TEXT'))?>
					<?=Loc::getMessage("PEC_DOCUMENT_NUMBER_TEXT")?>
                </div>
                <div>
                    <input type="text" value="<?=Option::get($module_id, "PEC_DOCUMENT_NUMBER", '');?>" name="PEC_DOCUMENT_NUMBER">
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 45%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_DOCUMENT_DATE_TEXT'))?>
					<?=Loc::getMessage("PEC_DOCUMENT_DATE_TEXT")?>
                </div>
                <div>
                    <input type="text" value="<?=Option::get($module_id, "PEC_DOCUMENT_DATE", '');?>" name="PEC_DOCUMENT_DATE">
                </div>
            </div>
            <br>
            <div style="display: flex; ">
                <div style="width: 45%; font-weight: bold;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_DELIVERY_ADDRESS_TEXT'))?>
					<?=Loc::getMessage("PEC_DELIVERY_ADDRESS_TEXT")?>
                </div>
                <div>
                    <input type="text" value="<?=Option::get($module_id, "PEC_DELIVERY_ADDRESS", 'PEC_DELIVERY_ADDRESS');?>" required name="PEC_DELIVERY_ADDRESS">
                </div>
            </div>
            <br>
            <div style="display: flex; ">
                <div style="width: 45%; font-weight: bold;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_DELIVERY_APARTMENT_TEXT'))?>
                    <?=Loc::getMessage("PEC_DELIVERY_APARTMENT_TEXT")?>
                </div>
                <div>
                    <input type="text" value="<?=Option::get($module_id, "PEC_DELIVERY_APARTMENT", 'FLAT_NUM');?>" required name="PEC_DELIVERY_APARTMENT">
                </div>
            </div>
            <br>
            <div style="display: flex; ">
                <div style="width: 45%; font-weight: bold;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_ORDER_ADDRESS'))?>
                    <?=Loc::getMessage('PEC_DELIVERY_ORDER_ADDRESS')?>
                </div>
                <div>
                    <input type="text" value="<?=Option::get($module_id, "PEC_ORDER_ADDRESS", 'ADDRESS');?>" required name="PEC_ORDER_ADDRESS">
                </div>
            </div>
            <br>
            <div style="display: flex; ">
                <div style="width: 45%; font-weight: bold;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_ORDER_PHONE'))?>
                    <?=Loc::getMessage('PEC_DELIVERY_ORDER_PHONE')?>
                </div>
                <div>
                    <input type="text" value="<?=Option::get($module_id, "PEC_ORDER_PHONE", 'PHONE');?>" required name="PEC_ORDER_PHONE">
                </div>
            </div>
            <br>
            <div style="display: flex; ">
                <div style="width: 45%; font-weight: bold;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_ORDER_EMAIL'))?>
                    <?=Loc::getMessage('PEC_DELIVERY_ORDER_EMAIL')?>
                </div>
                <div>
                    <input type="text" value="<?=Option::get($module_id, "PEC_ORDER_EMAIL", 'EMAIL');?>" name="PEC_ORDER_EMAIL">
                </div>
            </div>
            <br>
        </div>
        <div class="adm-detail-content-item-block">
            <div style="display: flex;">
                <div style="width: 45%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_ORDER_SEND'))?>
					<?=Loc::getMessage("PEC_DELIVERY_ORDER_SEND")?>
                </div>
                <div>
                    <select name="PEC_ORDER_SEND" id="PEC_ORDER_SEND" onchange="pecomOptions.getStatus(this);">
                        <option value="M" <?php if(Option::get($module_id, "PEC_ORDER_SEND", '0') == 'M') echo 'selected';?>><?=Loc::getMessage("PEC_DELIVERY_ORDER_SEND_TEXT1")?></option>
                        <option value="C" <?php if(Option::get($module_id, "PEC_ORDER_SEND", '0') == 'C') echo 'selected';?>><?=Loc::getMessage("PEC_DELIVERY_ORDER_SEND_TEXT2")?></option>
                        <option value="U" <?php if(Option::get($module_id, "PEC_ORDER_SEND", '0') == 'U') echo 'selected';?>><?=Loc::getMessage("PEC_DELIVERY_ORDER_SEND_TEXT3")?></option>
                    </select>
                </div>
            </div>
            <br>
            <div style="display: <?=Option::get($module_id, "PEC_ORDER_SEND", '0') == 'status_update' ? 'flex' : 'none';?>" id="PEC_ORDER_CREATE_DIV">
                <div style="width: 45%;">
					<?=Loc::getMessage("PEC_DELIVERY_STATUS_ORDER_SEND")?>
                </div>
                <div>
					<?php $orderStatus = [];
					$db_lang = \CSaleStatus::GetList(array('SORT'=>'ASC'), array("LID" => LANGUAGE_ID));
					while ($arLang = $db_lang->Fetch()) {
						$orderStatus[] = ['ID' => $arLang['ID'], 'VALUE' => $arLang['NAME'] . ' [' . $arLang['ID'].']'];
					}?>
                    <select name="PEC_ORDER_CREATE" id="PEC_ORDER_CREATE">
                        <option value=""></option>
						<?php foreach ($orderStatus as $item) {?>
                            <option value="<?=$item['ID']?>" <?php if(Option::get($module_id, "PEC_ORDER_CREATE") == $item['ID']) echo 'selected';?>><?=$item['VALUE']?></option><?php
                        }?>
                    </select>
                </div>
            </div>
            <div class="adm-info-message-wrap" id="infoBlock">
                <div class="adm-info-message">
					<?=Loc::getMessage("PEC_DELIVERY_STATUS_WARNING_TEXT")?>
                </div>
            </div>
            <br>
            <hr>
            <br>
            <div style="display: flex;">
                <div style="width: 45%;">
					<?=Loc::getMessage("PEC_DELIVERY_PRINT_LABEL")?>
                </div>
                <div>
                    <select name="PEC_PRINT_LABEL">
                        <option value="1" <?php if(Option::get($module_id, "PEC_PRINT_LABEL", '0') == '1') echo 'selected';?>><?=Loc::getMessage("PEC_DELIVERY_PRINT_LABEL_OPTIONS1")?></option>
                        <option value="2" <?php if(Option::get($module_id, "PEC_PRINT_LABEL", '0') == '2') echo 'selected';?>><?=Loc::getMessage("PEC_DELIVERY_PRINT_LABEL_OPTIONS2")?></option>
                        <option value="3" <?php if(Option::get($module_id, "PEC_PRINT_LABEL", '0') == '3') echo 'selected';?>><?=Loc::getMessage("PEC_DELIVERY_PRINT_LABEL_OPTIONS3")?></option>
                    </select>
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 45%;">
					<?=Loc::getMessage("PEC_DELIVERY_SAVE_PDF")?>
                </div>
                <div>
                    <input type="checkbox" value="Y" onclick="pecomOptions.getUrl();" id="PEC_SAVE_PDF" name="PEC_SAVE_PDF"
						<?php if(Option::get($module_id, "PEC_SAVE_PDF", 0)) echo 'checked';?>>
                </div>
            </div>
            <br>
            <div style="display: none;" id="PEC_SAVE_PDF_URL_DIV">
                <div style="width: 45%;">
					<?=Loc::getMessage("PEC_DELIVERY_SAVE_PDF_URL")?>
                </div>
                <div>
                    <div class="adm-workarea" style="padding: 0">
                        <input type="text" value="<?=Option::get($module_id, "PEC_SAVE_PDF_URL");?>"
                               name="PEC_SAVE_PDF_URL" id="PEC_SAVE_PDF_URL">
                        <input type="button" onclick="OpenImage();" value="...">
                    </div>
                </div>
            </div>
        </div>

		<?php $tabControl->BeginNextTab();?>
        <div class="adm-detail-content-item-block">

            <div style="display: flex;">
                <div style="width: 38%; font-weight: bold;"><?=Loc::getMessage('PEC_DELIVERY_API_MODE')?></div>
                <div style="width: 60%;">
                    <select name="PEC_API_MODE" id="PEC_API_MODE" oninput="
                        if (document.getElementById('PEC_API_MODE').value !== '') {
                            document.getElementById('PEC_API_URL').disabled = true;
                            document.getElementById('PEC_API_URL').value = document.getElementById('PEC_API_MODE').value;
                            document.getElementById('PEC_API_URL_2').value = document.getElementById('PEC_API_URL').value;
                        } else {
                            document.getElementById('PEC_API_URL').disabled = false;
                        }

                        if (document.getElementById('PEC_API_MODE').value === '<?=PecomKabinet::API_BASE_URL?>') {
                            document.getElementById('PEC_API_LOGIN').value = document.getElementById('PEC_API_LOGIN_BASE').value;
                        }
                        if (document.getElementById('PEC_API_MODE').value === '<?=PecomKabinet::API_TEST_URL?>') {
                            document.getElementById('PEC_API_LOGIN').value = document.getElementById('PEC_API_LOGIN_TEST').value;
                        }
                        if (
                            document.getElementById('PEC_API_MODE').value !== '<?=PecomKabinet::API_BASE_URL?>'
                            && document.getElementById('PEC_API_MODE').value !== '<?=PecomKabinet::API_TEST_URL?>'
                        ) {
                            document.getElementById('PEC_API_LOGIN').value = document.getElementById('PEC_API_LOGIN_CUSTOM').value;
                        }

                        if (document.getElementById('PEC_API_MODE').value === '<?=PecomKabinet::API_BASE_URL?>') {
                            document.getElementById('PEC_API_KEY').value = document.getElementById('PEC_API_KEY_BASE').value;
                        }
                        if (document.getElementById('PEC_API_MODE').value === '<?=PecomKabinet::API_TEST_URL?>') {
                            document.getElementById('PEC_API_KEY').value = document.getElementById('PEC_API_KEY_TEST').value;
                        }
                        if (
                            document.getElementById('PEC_API_MODE').value !== '<?=PecomKabinet::API_BASE_URL?>'
                            && document.getElementById('PEC_API_MODE').value !== '<?=PecomKabinet::API_TEST_URL?>'
                        ) {
                            document.getElementById('PEC_API_KEY').value = document.getElementById('PEC_API_KEY_CUSTOM').value;
                        }

                        if (document.getElementById('PEC_API_MODE').value === '<?=PecomKabinet::API_TEST_URL?>') {
                            document.getElementById('SET_API_TEST_LOGIN_PASS').style.display = 'inline-block';
                        } else {
                            document.getElementById('SET_API_TEST_LOGIN_PASS').style.display = 'none';
                        }
                    ">
                        <option value="<?=PecomKabinet::API_BASE_URL?>"
                            <?=(Option::get($module_id, 'PEC_API_URL', PecomKabinet::API_BASE_URL) == PecomKabinet::API_BASE_URL) ? 'selected' : ''?>>
                            <?=Loc::getMessage('PEC_DELIVERY_API_MODE_BASE')?>
                        </option>
                        <option value="<?=PecomKabinet::API_TEST_URL?>"
                            <?=(Option::get($module_id, 'PEC_API_URL', PecomKabinet::API_BASE_URL) == PecomKabinet::API_TEST_URL) ? 'selected': ''?>>
                            <?=Loc::getMessage('PEC_DELIVERY_API_MODE_TEST')?>
                        </option>
                        <option value=""
                            <?=in_array(Option::get($module_id, 'PEC_API_URL', PecomKabinet::API_BASE_URL), [
                                PecomKabinet::API_BASE_URL,
                                PecomKabinet::API_TEST_URL
                            ]) ? '' : 'selected'?>>
                            <?=Loc::getMessage('PEC_DELIVERY_API_MODE_CUSTOM')?>
                        </option>
                    </select>
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 38%; font-weight: bold;">
                    <?=Loc::getMessage('PEC_DELIVERY_API_URL')?>
                </div>
                <div style="width: 60%;">
                    <input type="hidden" id="PEC_API_URL_2" name="PEC_API_URL" required
                        value="<?=Option::get($module_id, 'PEC_API_URL', PecomKabinet::API_BASE_URL)?>">
                    <input type="text" id="PEC_API_URL" name="PEC_API_URL" required style="width: 100%;"
                        <?=in_array(Option::get($module_id, 'PEC_API_URL', PecomKabinet::API_BASE_URL), [
                            PecomKabinet::API_BASE_URL,
                            PecomKabinet::API_TEST_URL
                        ]) ? 'disabled' : ''?>
                        value="<?=Option::get($module_id, 'PEC_API_URL', PecomKabinet::API_BASE_URL)?>">
                </div>
            </div>
            <br>

            <div style="display: flex;">
                <div style="width: 38%; font-weight: bold;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_LOGIN'))?>
					<?=Loc::getMessage("PEC_DELIVERY_LOGIN")?>
                </div>
                <div style="width: 60%;">
                    <input type="text" id="PEC_API_LOGIN" name="PEC_API_LOGIN" oninput="
                        if (document.getElementById('PEC_API_MODE').value === '<?=PecomKabinet::API_BASE_URL?>') {
                            document.getElementById('PEC_API_LOGIN_BASE').value = document.getElementById('PEC_API_LOGIN').value;
                        }
                        if (document.getElementById('PEC_API_MODE').value === '<?=PecomKabinet::API_TEST_URL?>') {
                            document.getElementById('PEC_API_LOGIN_TEST').value = document.getElementById('PEC_API_LOGIN').value;
                        }
                        if (
                            document.getElementById('PEC_API_MODE').value !== '<?=PecomKabinet::API_BASE_URL?>'
                            && document.getElementById('PEC_API_MODE').value !== '<?=PecomKabinet::API_TEST_URL?>'
                        ) {
                            document.getElementById('PEC_API_LOGIN_CUSTOM').value = document.getElementById('PEC_API_LOGIN').value;
                        }
                    " value="<?=Option::get($module_id, "PEC_API_LOGIN", '')?>" required style="width: 100%;">
                    <input type="hidden" id="PEC_API_LOGIN_BASE" name="PEC_API_LOGIN_BASE" value="<?=Option::get($module_id, "PEC_API_LOGIN_BASE", Option::get($module_id, "PEC_API_LOGIN", ''))?>">
                    <input type="hidden" id="PEC_API_LOGIN_TEST" name="PEC_API_LOGIN_TEST" value="<?=Option::get($module_id, "PEC_API_LOGIN_TEST", 'test')?>">
                    <input type="hidden" id="PEC_API_LOGIN_CUSTOM" name="PEC_API_LOGIN_CUSTOM" value="<?=Option::get($module_id, "PEC_API_LOGIN_CUSTOM", 'test')?>">
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 38%; font-weight: bold;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_API_KEY'))?>
					<?=Loc::getMessage("PEC_DELIVERY_API_KEY")?>
                </div>
                <div  style="width: 60%;">
                    <input type="text" id="PEC_API_KEY" name="PEC_API_KEY" oninput="
                    if (document.getElementById('PEC_API_MODE').value === '<?=PecomKabinet::API_BASE_URL?>') {
                        document.getElementById('PEC_API_KEY_BASE').value = document.getElementById('PEC_API_KEY').value;
                    }
                    if (document.getElementById('PEC_API_MODE').value === '<?=PecomKabinet::API_TEST_URL?>') {
                        document.getElementById('PEC_API_KEY_TEST').value = document.getElementById('PEC_API_KEY').value;
                    }
                    if (
                        document.getElementById('PEC_API_MODE').value !== '<?=PecomKabinet::API_BASE_URL?>'
                        && document.getElementById('PEC_API_MODE').value !== '<?=PecomKabinet::API_TEST_URL?>'
                    ) {
                        document.getElementById('PEC_API_KEY_CUSTOM').value = document.getElementById('PEC_API_KEY').value;
                    }
                    " value="<?=Option::get($module_id, "PEC_API_KEY", '')?>" required style="width: 100%;">
                    <input type="hidden" id="PEC_API_KEY_BASE" name="PEC_API_KEY_BASE" value="<?=Option::get($module_id, "PEC_API_KEY_BASE", Option::get($module_id, "PEC_API_KEY", ''))?>">
                    <input type="hidden" id="PEC_API_KEY_TEST" name="PEC_API_KEY_TEST" value="<?=Option::get($module_id, "PEC_API_KEY_TEST", '782ED429C365439B05909319CD08A0C3CB9C15A4')?>">
                    <input type="hidden" id="PEC_API_KEY_CUSTOM" name="PEC_API_KEY_CUSTOM" value="<?=Option::get($module_id, "PEC_API_KEY_CUSTOM", '782ED429C365439B05909319CD08A0C3CB9C15A4')?>">
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 38%;"></div>
                <div>
                    <button type="button" class="adm-btn" onclick="
                    const data = new FormData();
                    data.append('method', 'checkApi');
                    data.append('sessid', BX.bitrix_sessid());
                    data.append('login', document.querySelector('[name=PEC_API_LOGIN]').value);
                    data.append('password', document.querySelector('[name=PEC_API_KEY]').value);
                    data.append('apiUrl', document.querySelector('[name=PEC_API_URL]').value);
                    fetch('/bitrix/js/pecom.ecomm/ajax.php',
                    {
                        method: 'POST',
                        body: data,
                    })
                    .then(function(res) {
                        return res.json();
                    })
                    .then(function(data) {
                        if (data) {
                            alert('<?=Loc::getMessage('PEC_DELIVERY_MSG_API_OK')?>');
                        } else {
                            alert('<?=Loc::getMessage('PEC_DELIVERY_MSG_API_ERROR')?>');
                        }
                    })
                    "><?=Loc::getMessage('PEC_DELIVERY_BTN_API_TEST')?></button>
                    <button type="button" id="SET_API_TEST_LOGIN_PASS" class="adm-btn" onclick="
                        document.getElementById('PEC_API_LOGIN').value = 'test';
                        document.getElementById('PEC_API_LOGIN_TEST').value = 'test';
                        document.getElementById('PEC_API_KEY').value = '782ED429C365439B05909319CD08A0C3CB9C15A4';
                        document.getElementById('PEC_API_KEY_TEST').value = '782ED429C365439B05909319CD08A0C3CB9C15A4';
                    " style="margin-left: 5px;
                    <?=(Option::get($module_id, 'PEC_API_URL', PecomKabinet::API_BASE_URL) !== PecomKabinet::API_TEST_URL) ? 'display: none;': ''?>>
                    "><?=Loc::getMessage('PEC_DELIVERY_BTN_TEST_KEY')?></button>
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 38%;">
					<?=Loc::getMessage("PEC_DELIVERY_STORE_TYPE")?>
                </div>
                <div>
                    <select name="PEC_STORE_TYPE">

                        <option value=""></option>
						<?php foreach ($storeTypes as $item) {?>
                        <option value="<?=$item['name']?>" <?php if(Option::get($module_id, "PEC_STORE_TYPE") === $item['name']) echo 'selected';?>>
							<?=$item['name']?>
                            </option><?php
                        }?>
                    </select>
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 38%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_STORE_NAME'))?>
					<?=Loc::getMessage("PEC_DELIVERY_STORE_NAME")?>
                </div>
                <div>
                    <input type="text" value="<?=Option::get($module_id, "PEC_STORE_TITLE", COption::GetOptionString("eshop", "shopOfName", Loc::getMessage("PEC_SETUP_STORE_TYPE"), 's1'));?>" name="PEC_STORE_TITLE">
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 38%; font-weight: bold;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_INN'))?>
					<?=Loc::getMessage("PEC_DELIVERY_INN")?>
                </div>
                <div>
                    <input type="text" value="<?=Option::get($module_id, "PEC_STORE_INN", COption::GetOptionString("eshop", "shopINN", "", 's1'));?>" required pattern="\d{8,}" name="PEC_STORE_INN">
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 38%;">
                    <?=Loc::getMessage('PEC_DELIVERY_KPP')?>
                </div>
                <div>
                    <input type="text" value="<?=Option::get($module_id, "PEC_STORE_KPP", COption::GetOptionString(Tools::$MODULE_ID, 'PEC_STORE_KPP', '', 's1'));?>" name="PEC_STORE_KPP">
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 38%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_CONTACTS'))?>
					<?=Loc::getMessage("PEC_DELIVERY_CONTACTS")?>
                </div>
                <div>
                    <input type="text" value="<?=Option::get($module_id, "PEC_STORE_PERSON", Loc::getMessage("PEC_DELIVERY_PEC_STORE_PERSON"));?>" name="PEC_STORE_PERSON">
                </div>
            </div>
            <br>
            <div style="display: flex;">
                <div style="width: 38%;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_PHONE'))?>
					<?=Loc::getMessage("PEC_DELIVERY_PHONE")?>
                </div>
                <div>
                    <input type="text" value="<?=Option::get($module_id, "PEC_STORE_PHONE", COption::GetOptionString("eshop", "siteTelephone", "", 's1'));?>" name="PEC_STORE_PHONE">
                </div>
            </div>
        </div>
		<?php if ($pecStatus && !$pecStatus['error']): ?>
            <div class="adm-detail-content-item-block----">
				<?php
                $selectAllowDelivery = function ($pecStatus) use ($optionPecApiAllowDelivery) {
					return '
                    <select name="PEC_API_ALLOW_DELIVERY[' . $pecStatus . ']">
                        <option value="">---</option>
                        <option value="1" ' . ($optionPecApiAllowDelivery[$pecStatus] == 1 ? 'selected' : ' ') . '>'.Loc::getMessage("PEC_DELIVERY_TRUE").'</option>
                        <option value="2" ' . ($optionPecApiAllowDelivery[$pecStatus] == 2 ? 'selected' : ' ') . '>'.Loc::getMessage("PEC_DELIVERY_FALSE").'</option>
                    </select>
                ';
				};
				$selectIsShipped = function ($pecStatus) use ($optionPecApiShipped) {
					return '
                    <select name="PEC_API_SHIPPED[' . $pecStatus . ']">
                        <option value="">---</option>
                        <option value="1" ' . ($optionPecApiShipped[$pecStatus] == 1 ? 'selected' : ' ') . '>'.Loc::getMessage("PEC_DELIVERY_SHIPPED").'</option>
                        <option value="2" ' . ($optionPecApiShipped[$pecStatus] == 2 ? 'selected' : ' ') . '>'.Loc::getMessage("PEC_DELIVERY_NO_SHIPPED").'</option>
                    </select>
                ';
				};

				\Bitrix\Main\Loader::IncludeModule("sale");
				$statusResult = \Bitrix\Sale\Internals\StatusLangTable::getList(array(
					'order' => array('STATUS.SORT'=>'ASC'),
					'filter' => array('STATUS.TYPE'=>'D','LID'=>LANGUAGE_ID),
					'select' => array('STATUS_ID','NAME','DESCRIPTION'),
				));
				$bxShipStatus = [];
				while($status = $statusResult->fetch()) {
					$bxShipStatus[] = $status;
				}
				$selectShipStatuses = function ($pecStatus) use ($bxShipStatus, $optionPecApiStatusTable) {
					$result = '
                    <select name="PEC_API_STATUS_TABLE[' . $pecStatus . ']">
                        <option value="">---</option>';
					foreach ($bxShipStatus as $status) {
						$selected = '';
						if ($optionPecApiStatusTable[$pecStatus] == $status['STATUS_ID']) {
							$selected = 'selected';
						}
						$result .= '<option value="' . $status['STATUS_ID'] . '"' . $selected . '>' . $status['NAME'] . '</option>';
					}
					$result .= '</select>';

					return $result;
				}
				?>
                <h3 style="text-align: center;">
                    <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_STATUS_UPDATE'))?>
                    <?=Loc::getMessage("PEC_DELIVERY_STATUS_UPDATE")?>
                </h3>
                <td>
                    <tr>
                        <th><?=Loc::getMessage("PEC_DELIVERY_STATUS_ORDER")?></th>
                        <th><?=Loc::getMessage("PEC_DELIVERY_ALLOWED")?></th>
                        <th><?=Loc::getMessage("PEC_DELIVERY_SHIPMENT_STATUS")?></th>
                        <th><?=Loc::getMessage("PEC_DELIVERY_STATUS")?></th>
                        <th><?=Loc::getMessage("PEC_DELIVERY_STATUS_AGENT_UPDATE")?></th>
                    </tr>
					<?php foreach ($pecStatus as $status): ?>
                        <tr>
                            <td><?=mb_convert_encoding($status->name, ini_get('default_charset') == 'cp1251' ? 'windows-1251' : ini_get('default_charset'), 'utf-8');?></td>
                            <td><?=$selectAllowDelivery($status->id)?></td>
                            <td><?=$selectIsShipped($status->id)?></td>
                            <td><?=$selectShipStatuses($status->id)?></td>
                            <td style="text-align: center;">
                                <input type="checkbox" name="PEC_API_START_AGENT[<?=$status->id?>]" <?php if ($optionPecApiStartAgent[$status->id]) echo 'checked'?>>
                            </td>
                        </tr>
					<?php endforeach; ?>
                    <tr><td style="padding-top: 20px;" </tr>
                    <tr>
                        <td>
                            <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_STATUS_AGENT_RUN'))?>
							<?=Loc::getMessage("PEC_DELIVERY_STATUS_AGENT_RUN")?>
                        </td>
                        <td>
                            <input type="checkbox" name="PEC_API_AGENT_ACTIVE" <?php if (Option::get($module_id, "PEC_API_AGENT_ACTIVE", 0)) echo 'checked'?>>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_AGENT_PRESCRIPTION'))?>
							<?=Loc::getMessage("PEC_DELIVERY_AGENT_PRESCRIPTION")?>
                        </td>
                        <td>
                            <input type="text" name="PEC_API_AGENT_ORDER_EXPIRED" value="<?=Option::get($module_id, "PEC_API_AGENT_ORDER_EXPIRED", '30');?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_AGENT_RUN_PERIOD'))?>
							<?=Loc::getMessage("PEC_DELIVERY_AGENT_RUN_PERIOD")?>
                        </td>
                        <td>
                            <input type="text" name="PEC_API_AGENT_PERIOD"  value="<?=Option::get($module_id, "PEC_API_AGENT_PERIOD", '120');?>">
                        </td>
                    </tr>
                </td>
            </div>
		<?php else: ?>
            <div>
                <p style="color: red"><?php
                    if (strpos($pecStatus['error']['message'], 'Connection timed out') === 0) {?>
						<?=Loc::getMessage("PEC_DELIVERY_API_TIMEOUT")?>
					<?php } else {?>
						<?=Loc::getMessage("PEC_DELIVERY_CHECK_LOGIN")?>
					<?php }?>
                </p>
            </div>
		<?php endif; ?>
		<?php $tabControl->BeginNextTab();?>
        <div>
            <a href="https://disk.yandex.ru/i/bj_s_RdXUO-HgQ" target="_blank"
               download="<?=Loc::getMessage('PEC_DELIVERY_DOC_INSTALL')?>"
               style="font-size: 16px; cursor: pointer;"><?=Loc::getMessage('PEC_DELIVERY_DOC_INSTALL_2')?></a>
            <br>
            <a href="https://disk.yandex.ru/i/A-UNhxhJA7IAXA"
               target="_blank"
               style="font-size: 16px; cursor: pointer;"><?=Loc::getMessage('PEC_DELIVERY_DOC_USER')?></a>
            <div style="color: tomato; font-size: 16px;"><?=Loc::getMessage("PEC_DELIVERY_ALARM")?></div>
            <br>
            <h2><?=Loc::getMessage("PEC_DELIVERY_CALC_SETUP")?></h2>
			<?=Loc::getMessage("PEC_DELIVERY_CALC_SETUP_TEXT")?>
            <br>
            <img src="/bitrix/js/<?=$module_id?>/faq1.png" title="faq" style="max-width: 100%;">
			<?=Loc::getMessage("PEC_DELIVERY_CALC_SETUP_TEXT2")?>
            <br>
            <h2><?=Loc::getMessage("PEC_DELIVERY_STORE_SETUP");?></h2>
			<?=Loc::getMessage("PEC_DELIVERY_STORE_SETUP_TEXT");?>
            <br>
            <img src="/bitrix/js/<?=$module_id?>/faq3.png" title="faq" style="max-width: 80%;">
            <br>
            <h2><?=Loc::getMessage("PEC_DELIVERY_API_SETUP");?></h2>
			<?=Loc::getMessage("PEC_DELIVERY_API_SETUP_TEXT");?>
            <br>
            <img src="/bitrix/js/<?=$module_id?>/fag1.jpg" title="faq" style="max-width: 100%;">
            <br>
			<?=Loc::getMessage("PEC_DELIVERY_API_SETUP_TEXT2");?>
            <br>
            <img src="/bitrix/js/<?=$module_id?>/fag2.jpg" title="faq" style="max-width: 80%;">
            </p>
			<?=Loc::getMessage("PEC_DELIVERY_API_SETUP_TEXT3");?>
            <br>
            <p style="font-size: 16px;">
                <?=Loc::getMessage('PEC_DELIVERY_API_SETUP_TEXT4');?>
                <a href="mailto:admin-extintegration@pecom.ru">admin-extintegration@pecom.ru</a>
            </p>
        </div>
        <?php $tabControl->BeginNextTab();?>
        <div style="font-size: 16px; color:red;">
            <?=Loc::getMessage('PEC_DELIVERY_INTEGRATION_WARNING')?>
        </div>
        <div>
            <td>
                <tr>
                    <td>
                        <?php Tools::ShowLabel(Loc::getMessage('PEC_DELIVERY_LBL_ID_FOR_INSERT'))?>
                        <?=Loc::getMessage("ID_FOR_INSERT_AFTER_PEC_PICKUP_BLOCK")?>
                    </td>
                    <td>
                        <input type="text" name="ID_FOR_INSERT_AFTER_PEC_PICKUP_BLOCK"  value="<?=Option::get($module_id, "ID_FOR_INSERT_AFTER_PEC_PICKUP_BLOCK", 'bx-soa-delivery');?>">
                    </td>
                </tr>
            </td>
        </div>
        <?php if (Option::get($module_id, 'TAB_ACTIVE', 'N') === 'Y') { ?>
            <?php $tabControl->BeginNextTab();?>
            <div class="adm-detail-content-item-block">
                <div style="display: flex;">
                    <div style="width: 38%; font-weight: bold;">
                        <?=Loc::getMessage("PEC_WIDGET_FIELD")?>
                    </div>
                    <div style="width: 60%;">
                        <input type="text" name="PEC_WIDGET_URL" value="<?=Option::get($module_id, 'PEC_WIDGET_URL', 'https://calc.pecom.ru/iframe/e-store-calculator');?>" required style="width: 100%;">
                    </div>
                </div>
                <br>
                <div style="display: flex;">
                    <div style="width: 38%; font-weight: bold;">
                        <?=Loc::getMessage("PEC_API_FIELD")?>
                    </div>
                    <div style="width: 60%;">
                        <input type="text" name="PEC_WIDGET_API_URL" value="<?=Option::get($module_id, 'PEC_WIDGET_API_URL', 'https://calc.pecom.ru/api/e-store-calculate');?>" required style="width: 100%;">
                    </div>
                </div>
                <br>
                <div style="display: flex;">
                    <?php
                    $baseSrc = Option::get('pecom.ecomm', 'PEC_WIDGET_MAP_PICKER_URL');
                    if($baseSrc !== 'https://calc.pecom.ru/map-picker'){
                        $baseSrc = 'https://calc.pecom.ru/map-picker';
                    }
                    ?>
                    <div style="width: 38%; font-weight: bold;">
                        <?=Loc::getMessage('PEC_DELIVERY_WIDGET_MAP_PICKER_URL')?>
                    </div>
                    <div style="width: 60%;">
                        <input type="text" name="PEC_WIDGET_MAP_PICKER_URL" value="<?=$baseSrc?>" required style="width: 100%;">
                    </div>
                </div>
            </div>
        <?php }?>
		<?php //require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
		<?php $tabControl->Buttons();?>
        <input <?php if(!$RIGHT_W) echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
        <input <?php if(!$RIGHT_W) echo "disabled" ?> type="submit" name="RestoreDefaults" title="<?=GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="confirm('<?=AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?=GetMessage("MAIN_RESTORE_DEFAULTS")?>">
		<?php $tabControl->End();?>
    </form>
	<?php
	CAdminFileDialog::ShowScript(Array
	(
		"event" => "OpenImage",
		"arResultDest" => Array("FUNCTION_NAME" => "pecomOptions.SetImageUrl"),
		"arPath" => Array(),
		"select" => 'D',
		"operation" => 'O',
		"showUploadTab" => false,
		"showAddToMenuTab" => false,
		"fileFilter" => '',
		"saveConfig" => true
	));
	?>
    <script>
        var pecomOptions = {
            init: function () {
                this.getUrl();
                this.getStatus();
            },
            getStatus: function () {
                let val = document.getElementById('PEC_ORDER_SEND').value;
                let div = document.getElementById('PEC_ORDER_CREATE_DIV');
                let info = document.getElementById('infoBlock');
                if (val === 'U') {
                    div.style.display = "flex";
                    info.style.display = "flex";
                } else {
                    document.getElementById('PEC_ORDER_CREATE').value = '';
                    div.style.display = "none";
                    info.style.display = "none";
                }
            },
            getUrl: function () {
                let val = document.getElementById('PEC_SAVE_PDF');
                let div = document.getElementById('PEC_SAVE_PDF_URL_DIV');
                if (val.checked)
                    div.style.display = "flex";
                else {
                    div.style.display = "none";
                }
            },
            SetImageUrl: function(filename,path) {
                document.getElementById('PEC_SAVE_PDF_URL').value = path;
            }
        }
        pecomOptions.init();
    </script>
	<?php
}
?>
