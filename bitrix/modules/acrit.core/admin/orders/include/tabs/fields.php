<?
namespace Acrit\Core\Orders;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$obTabControl->AddSection('HEADING_FIELDS_TBLCMPR', Loc::getMessage('ACRIT_CRM_TAB_FIELDS_HEADING'));

// Block for tags management
$obTabControl->BeginCustomField('PROFILE[FIELDS][table_compare]', Loc::getMessage('ACRIT_CRM_FIELDS_TBLCMPR'));
$store_fields = $obPlugin->getFields();
$constant_fields = OrdersInfo::getConstantsList($arProfile['OTHER']['CONSTANTS']);
$store_fields = array_merge($store_fields, $constant_fields);
$order_fields = OrdersInfo::getProps();
$order_pt = (int)$arProfile['CONNECT_DATA']['pay_type'];
$order_pt_fields = $order_fields[$order_pt];
?>
    <tr id="tr_fields_table_compare">
        <td>
            <table class="adm-list-table" id="acrit_imp_agents_list">
                <thead>
                <tr class="adm-list-table-header">
                    <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=Loc::getMessage('ACRIT_CRM_TAB_FIELDS_ORDER', ['#PAY_NUM#' => $order_pt]);?></div></td>
                    <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=Loc::getMessage('ACRIT_CRM_TAB_FIELDS_STORE');?></div></td>
                    <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=Loc::getMessage('ACRIT_CRM_TAB_FIELDS_DATE');?></div></td>
                </tr>
                </thead>
                <tbody>
                    <?foreach ($order_pt_fields as $order_field):?>
                    <tr class="adm-list-table-row action-add-row" id="acrit_imp_agents_add">
                        <td class="adm-list-table-cell"><?=$order_field['NAME'];?> [<?=$order_field['ID'];?>]</td>
                        <td class="adm-list-table-cell">
                            <select class="custom-select" name="PROFILE[FIELDS][table_compare][<?=$order_field['ID'];?>][value]">">
                                <option value=""><?=Loc::getMessage('ACRIT_CRM_TAB_FIELDS_NO');?></option>
	                            <?foreach ($store_fields as $store_field):?>
                                <option value="<?=$store_field['id'];?>"<?=$arProfile['FIELDS']['table_compare'][$order_field['ID']]['value']==$store_field['id']?' selected':'';?>><?=$store_field['name'];?> (<?=$store_field['id'];?>)</option>
                                <?endforeach;?>
                            </select>
                            <input type="hidden" name="PROFILE[FIELDS][table_compare][<?=$order_field['ID'];?>][direction]" value="<?=$order_field['SYNC_DIR'];?>">
                        </td>
                        <td class="adm-list-table-cell">
                        <input type="checkbox"  name="PROFILE[OTHER][FIELDS_DATE][]" value="<?=$order_field['ID'];?>"<?=in_array($order_field['ID'], $arProfile['OTHER']['FIELDS_DATE'] ? : [])?' checked':'';?>></div>
                        </td>
                    </tr>
                    <?endforeach;?>
                </tbody>
            </table>
        </td>
    </tr>
<?
$obTabControl->EndCustomField('PROFILE[FIELDS]');

?>