<?
namespace Acrit\Core\Orders;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$obTabControl->AddSection('HEADING_BASIC', Loc::getMessage('ACRIT_CRM_TAB_BASIC_HEADING'));
// Site ID field
$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][LID]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_LID'));
$list = OrdersInfo::getSites();
?>
    <tr id="tr_connect_data_lid">
        <td>
            <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_LID_HINT'));?>
            <label for="field_connect_data_lid"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <select name="PROFILE[CONNECT_DATA][LID]">
                <?foreach ($list as $item):?>
                    <option value="<?=$item['ID'];?>"<?=$arProfile['CONNECT_DATA']['LID']==$item['ID']?' selected':'';?>><?=$item['NAME'];?><?=$item['ID']?(' [' . $item['ID'] . ']'):'';?></option>
                <?endforeach;?>
            </select>
        </td>
    </tr>
    <?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][LID]');
// Order ID field
$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][orderid_field]', Loc::getMessage('ACRIT_CRM_GENERAL_ORDERID_FIELD'), true);
$orderid_fields = OrdersInfo::getOrderExtIDFields($arProfile);
?>
    <tr id="tr_ORDERID_FIELD">
        <td>
	        <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_GENERAL_ORDERID_FIELD_HINT'));?>
            <label for="field_CONNECT_DATA_ORDERID_FIELD"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <select name="PROFILE[CONNECT_DATA][orderid_field]" id="field_CONNECT_DATA_ORDERID_FIELD">
				<?foreach ($orderid_fields as $orderid_field):?>
                <option value="<?=$orderid_field['id'];?>"<?=$arProfile['CONNECT_DATA']['orderid_field'] == $orderid_field['id']?' selected':'';?>><?=$orderid_field['name'];?> [<?=$orderid_field['id'];?>]</option>
				<?endforeach;?>
            </select>
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][orderid_field]');

$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][user_control]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_USER_CONTROL'), true);
?>
    <tr id="tr_connect_data_user_control">
        <td>
            <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_USER_CONTROL_HINT'));?>
            <label for="field_connect_data_user_control"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <input type="checkbox" name="PROFILE[CONNECT_DATA][user_control]" id="field_connect_data_user_control" value="Y"<?=$arProfile['CONNECT_DATA']['user_control']=='Y'?' checked':'';?> />
        </td>
    </tr>
    <?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][user_control]');

// Default buyer
$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][buyer]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_BUYER'), true);
//$list = OrdersInfo::getUsers();
$ajax_link = '/bitrix/admin/'.str_replace('.', '_', $strModuleId).'_orders_ajax.php';
$user_sel = false;
if ($arProfile['CONNECT_DATA']['buyer']) {
	$user_sel = OrdersInfo::getUser((int)$arProfile['CONNECT_DATA']['buyer']);
}
?>
    <tr id="tr_connect_data_buyer">
        <td>
	        <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_BUYER_HINT'));?>
            <label for="field_connect_data_buyer"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <select class="connect-data-user-search" name="PROFILE[CONNECT_DATA][buyer]">
	            <?if($user_sel):?>
                <option value="<?=$user_sel['id'];?>"><?=$user_sel['name'];?>, <?=$user_sel['code'];?> [<?=$user_sel['id'];?>]</option>
	            <?endif;?>
            </select>
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][buyer]');

if ( \Bitrix\Main\Loader::IncludeModule('crm') ) {
// Default company
    $obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][company]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_COMPANY'), true);
    $ajax_link = '/bitrix/admin/' . str_replace('.', '_', $strModuleId) . '_orders_ajax.php';
    $company_sel = false;
    if ($arProfile['CONNECT_DATA']['company']) {
        $company_sel = OrdersInfo::getCompany((int)$arProfile['CONNECT_DATA']['company']);
    }
    ?>
    <tr id="tr_connect_data_company">
        <td>
            <?= Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_COMPANY_HINT')); ?>
            <label for="field_connect_data_company"><?= $obTabControl->GetCustomLabelHTML() ?><label>
        </td>
        <td>
            <select class="connect-data-company-search" name="PROFILE[CONNECT_DATA][company]">
                <?
                if ($company_sel): ?>
                    <option value="<?= $company_sel['id']; ?>"><?= $company_sel['name']; ?>, [<?= $company_sel['id']; ?>
                        ]
                    </option>
                <?endif; ?>
            </select>
        </td>
    </tr>
    <?
    $obTabControl->EndCustomField('PROFILE[CONNECT_DATA][company]');

// Default contact
    $obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][contact]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_CONTACT'), true);
    $ajax_link = '/bitrix/admin/' . str_replace('.', '_', $strModuleId) . '_orders_ajax.php';
    $contact_sel = false;
    if ($arProfile['CONNECT_DATA']['contact']) {
        $contact_sel = OrdersInfo::getContact((int)$arProfile['CONNECT_DATA']['contact']);
    }
    ?>
    <tr id="tr_connect_data_contact">
        <td>
            <?= Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_CONTACT_HINT')); ?>
            <label for="field_connect_data_contact"><?= $obTabControl->GetCustomLabelHTML() ?><label>
        </td>
        <td>
            <select class="connect-data-contact-search" name="PROFILE[CONNECT_DATA][contact]">
                <?
                if ($contact_sel): ?>
                    <option value="<?= $contact_sel['id']; ?>"><?= $contact_sel['name']; ?>
                        , <?= $contact_sel['code']; ?> [<?= $contact_sel['id']; ?>]
                    </option>
                <?endif; ?>
            </select>
        </td>
    </tr>
    <?
    $obTabControl->EndCustomField('PROFILE[CONNECT_DATA][contact]');
}
// Default responsible user
$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][responsible]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_RESPONSIBLE'));
//$list = OrdersInfo::getUsers('СОК');
$user_sel = false;
if ($arProfile['CONNECT_DATA']['responsible']) {
	$user_sel = OrdersInfo::getUser((int)$arProfile['CONNECT_DATA']['responsible']);
}
?>
    <tr id="tr_connect_data_responsible">
        <td>
	        <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_RESPONSIBLE_HINT'));?>
            <label for="field_connect_data_responsible"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <select class="connect-data-user-search" name="PROFILE[CONNECT_DATA][responsible]">
                <?if($user_sel):?>
                <option value="<?=$user_sel['id'];?>"><?=$user_sel['name'];?>, <?=$user_sel['code'];?> [<?=$user_sel['id'];?>]</option>
                <?endif;?>
            </select>

            <script>
                $(document).ready(function() {
                    $('.connect-data-user-search').select2({
                        minimumInputLength: 3,
                        width: '390',
                        placeholder: '<?=Loc::getMessage('ACRIT_CRM_TAB_BASIC_USER_SEARCH_PLACEHOLDER');?>',
                        language: 'ru',
                        ajax: {
                            url: "<?=$ajax_link;?>",
                            delay: 250,
                            dataType: 'json',
                            data: function (params) {
                                return {
                                    action: 'find_users',
                                    q: params.term,
                                };
                            },
                            processResults: function (data) {
                                var arr = []
                                $.each(data, function (index, value) {
                                    arr.push({
                                        id: index,
                                        text: value
                                    })
                                })
                                return {
                                    results: arr
                                };
                            },
                        }
                    });
                    $('.connect-data-company-search').select2({
                        minimumInputLength: 3,
                        width: '390',
                        placeholder: '<?=Loc::getMessage('ACRIT_CRM_TAB_BASIC_COMPANY_SEARCH_PLACEHOLDER');?>',
                        language: 'ru',
                        ajax: {
                            url: "<?=$ajax_link;?>",
                            delay: 250,
                            dataType: 'json',
                            data: function (params) {
                                return {
                                    action: 'find_companies',
                                    q: params.term,
                                };
                            },
                            processResults: function (data) {
                                var arr = []
                                $.each(data, function (index, value) {
                                    arr.push({
                                        id: index,
                                        text: value
                                    })
                                })
                                return {
                                    results: arr
                                };
                            },
                        }
                    });
                    $('.connect-data-contact-search').select2({
                        minimumInputLength: 3,
                        width: '390',
                        placeholder: '<?=Loc::getMessage('ACRIT_CRM_TAB_BASIC_CONTACT_SEARCH_PLACEHOLDER');?>',
                        language: 'ru',
                        ajax: {
                            url: "<?=$ajax_link;?>",
                            delay: 250,
                            dataType: 'json',
                            data: function (params) {
                                return {
                                    action: 'find_contacts',
                                    q: params.term,
                                };
                            },
                            processResults: function (data) {
                                var arr = []
                                $.each(data, function (index, value) {
                                    arr.push({
                                        id: index,
                                        text: value
                                    })
                                })
                                return {
                                    results: arr
                                };
                            },
                        }
                    });
                });
            </script>
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][responsible]');

// Default payment type
$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][pay_type]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_PAY_TYPE'), true);
$list = OrdersInfo::getPersonTypes();
?>
    <tr id="tr_connect_data_pay_type">
        <td>
	        <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_PAY_TYPE_HINT'));?>
            <label for="field_connect_data_pay_type"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <select name="PROFILE[CONNECT_DATA][pay_type]">
				<?foreach ($list as $item):?>
                <option value="<?=$item['id'];?>"<?=$arProfile['CONNECT_DATA']['pay_type']==$item['id']?' selected':'';?>><?=$item['name'];?><?=$item['id']?(' [' . $item['id'] . ']'):'';?></option>
				<?endforeach;?>
            </select>
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][pay_type]');

// Default payment method
$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][pay_method]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_PAY_METHOD'));
$list = OrdersInfo::getPayMethods();
?>
    <tr id="tr_connect_data_pay_method">
        <td>
	        <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_PAY_METHOD_HINT'));?>
            <label for="field_connect_data_pay_method"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <select name="PROFILE[CONNECT_DATA][pay_method]">
				<?foreach ($list as $item):?>
                <option value="<?=$item['id'];?>"<?=$arProfile['CONNECT_DATA']['pay_method']==$item['id']?' selected':'';?>><?=$item['name'];?><?=$item['id']?(' [' . $item['id'] . ']'):'';?></option>
				<?endforeach;?>
            </select>
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][pay_method]');

// Default delivery method
$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][deliv_method]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_DELIV_METHOD'));
$list = OrdersInfo::getDeliveryMethods();
?>
    <tr id="tr_connect_data_deliv_method">
        <td>
	        <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_DELIV_METHOD_HINT'));?>
            <label for="field_connect_data_deliv_method"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <select name="PROFILE[CONNECT_DATA][deliv_method]">
				<?foreach ($list as $item):?>
                <option value="<?=$item['id'];?>"<?=$arProfile['CONNECT_DATA']['deliv_method']==$item['id']?' selected':'';?>><?=$item['name'];?><?=$item['id']?(' [' . $item['id'] . ']'):'';?></option>
				<?endforeach;?>
            </select>
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][deliv_method]');


$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][is_paid]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_IS_PAID'));
?>
    <tr id="tr_connect_data_is_paid">
        <td>
            <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_IS_PAID_HINT'));?>
            <label for="field_connect_data_is_paid"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <input type="checkbox" name="PROFILE[CONNECT_DATA][is_paid]" id="field_connect_data_is_paid" value="Y"<?=$arProfile['CONNECT_DATA']['is_paid']=='Y'?' checked':'';?> />
        </td>
    </tr>
    <?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][is_paid]');

$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][ratio]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_RATIO'));
?>
    <tr id="tr_connect_data_ratio">
        <td>
            <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_RATIO_HINT'));?>
            <label for="field_connect_data_ratio"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <input type="checkbox" name="PROFILE[CONNECT_DATA][ratio]" id="field_connect_data_ratio" value="Y"<?=$arProfile['CONNECT_DATA']['ratio']=='Y'?' checked':'';?> />
        </td>
    </tr>
    <?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][ratio]');

$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][convert]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_CONVERT'));
?>
    <tr id="tr_connect_data_convert">
        <td>
            <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_CONVERT_HINT'));?>
            <label for="field_connect_data_convert"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <input type="checkbox" name="PROFILE[CONNECT_DATA][convert]" id="field_connect_data_convert" value="Y"<?=$arProfile['CONNECT_DATA']['convert']=='Y'?' checked':'';?> />
        </td>
    </tr>
    <?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][convert]');

$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][cartblock]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_CARTBLOCK'));
?>
    <tr id="tr_connect_data_cartblock">
        <td>
            <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_CARTBLOCK_HINT'));?>
            <label for="field_connect_data_cartblock"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <input type="checkbox" name="PROFILE[CONNECT_DATA][cartblock]" id="field_connect_data_cartblock" value="Y"<?=$arProfile['CONNECT_DATA']['cartblock']=='Y'?' checked':'';?> />
        </td>
    </tr>
    <?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][cartblock]');

$obTabControl->AddSection('HEADING_BASIC_NUMBERS', Loc::getMessage('ACRIT_CRM_TAB_BASIC_NUMBERS'));

$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][number][on]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_NUMBER_ON'));
?>
    <tr id="tr_connect_data_number_on">
        <td>
            <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_NUMBER_ON_HINT'));?>
            <label for="field_connect_data_number_on"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <input type="checkbox" name="PROFILE[CONNECT_DATA][number][on]" id="field_connect_data_number_on" value="Y"<?=$arProfile['CONNECT_DATA']['number']['on']=='Y'?' checked':'';?> />
        </td>
    </tr>
    <?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][number][on]');

$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][number][prefix]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_NUMBER_PREFIX'));
?>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_NUMBER_PREFIX_HINT'));?>
            <span class="adm-required-field"><?=Loc::getMessage('ACRIT_CRM_TAB_BASIC_NUMBER_PREFIX');?></span>:
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" name="PROFILE[CONNECT_DATA][number][prefix]" size="50" maxlength="255" data-role="connect-cred-client_id"
                   value="<?=htmlspecialcharsbx($arProfile['CONNECT_DATA']['number']['prefix']);?>" />
        </td>
    </tr>
    <?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][number][prefix]');

$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][number][prefix]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_NUMBER_PREFIX'));
?>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_NUMBER_PREFIX_HINT'));?>
            <span class="adm-required-field"><?=Loc::getMessage('ACRIT_CRM_TAB_BASIC_NUMBER_PREFIX');?></span>:
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" name="PROFILE[CONNECT_DATA][number][prefix]" size="50" maxlength="255" data-role="connect-cred-client_id"
                   value="<?=htmlspecialcharsbx($arProfile['CONNECT_DATA']['number']['prefix']);?>" />
        </td>
    </tr>
    <?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][number][prefix]');

$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][number][scheme]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_NUMBER_SCHEME'));
$list = OrdersInfo::getNumberScheme();
?>
    <tr id="tr_connect_data_scheme">
        <td>
            <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_NUMBER_SCHEME_HINT'));?>
            <label for="field_connect_data_scheme"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <select name="PROFILE[CONNECT_DATA][number][scheme]">
                <?foreach ($list as $item):?>
                    <option value="<?=$item['ID'];?>"<?=$arProfile['CONNECT_DATA']['number']['scheme']==$item['ID']?' selected':'';?>><?=$item['NAME'];?><?=$item['ID']?(' [' . $item['ID'] . ']'):'';?></option>
                <?endforeach;?>
            </select>
        </td>
    </tr>
    <?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][number][scheme]');

$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][number][separator]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_NUMBER_SEPARATOR'));
$list = OrdersInfo::getSeparator();
?>
    <tr id="tr_connect_data_separator">
        <td>
            <?=Helper::ShowHint(Loc::getMessage('ACRIT_CRM_TAB_BASIC_NUMBER_SEPARATOR_HINT'));?>
            <label for="field_connect_data_separator"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <select name="PROFILE[CONNECT_DATA][number][separator]">
                <?foreach ($list as $item):?>
                    <option value="<?=$item['ID'];?>"<?=$arProfile['CONNECT_DATA']['number']['separator']==$item['ID']?' selected':'';?>><?=$item['NAME'];?><?=$item['ID']?(' [' . $item['ID'] . ']'):'';?></option>
                <?endforeach;?>
            </select>
        </td>
    </tr>
    <?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][number][separator]');