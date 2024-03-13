<?
namespace Acrit\Core\Orders;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Page\Asset,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
$obTabControl->AddSection('HEADING_OTHER_CONSTANTS', Loc::getMessage('ACRIT_OTHER_CONSTANTS_HEADING'));
$obTabControl->BeginCustomField('PROFILE[OTHER][CONSTANTS]', Loc::getMessage('ACRIT_OTHER_CONSTANTS'), true);
$list = OrdersInfo::getConstants($arProfile['OTHER']['CONSTANTS']);
Asset::getInstance()->addString('<script>
$(document).delegate(\'input[data-role="acrit_other_constants_add"]\', \'click\', function(e){
	let
		items = $(\'div[data-role="acrit_other_constant_list"]\'),
		item = items.children().last(),
		id = +$(item).attr(\'data-id\').replace(\'constant_\', \'\') + 1;
		newId = \'constant_\'+ id;
		newItem = item.clone();
		newItem.attr(\'data-id\', `${newId}`);

	$(newItem).find(\'input[data-role="acrit_other_constant_name"]\').each(function() {
		$(this).attr(\'name\', `PROFILE[OTHER][CONSTANTS][LIST][${newId}][NAME]`);
		$(this).attr(\'value\', \'\');
	});
	$(newItem).find(\'input[data-role="acrit_other_constant_value"]\').each(function() {
		$(this).attr(\'name\', `PROFILE[OTHER][CONSTANTS][LIST][${newId}][VALUE]`);
		$(this).attr(\'value\', \'\');
	});

	newItem.appendTo(items);
	newItem.find(\'input[type="text"]\').val(\'\');
});

$(document).delegate(\'input[data-role="acrit_other_constant_delete"]\', \'click\', function(e, data){
	data = typeof data == \'object\' ? data : {};
	if(data.force || confirm($(this).attr(\'data-confirm\'))){
		$(this).closest(\'[data-role="acrit_other_constant"]\').remove();
	}
});
</script>'
);

//var_dump();
?>
<tr>
    <td>
        <div data-role="acrit_other_constant_wrapper">
            <div data-role="acrit_other_constants" style="padding-top:10px;">
<!--                --><?//$strStoreUrl = 'https://suppliers-portal.ozon.ru/marketplace-pass/warehouses';?>
                <div data-role="acrit_other_constant_list">
                    <?$i = 1;?>
                    <?foreach( $list as $id => $item):?>
                        <div data-role="acrit_other_constant" data-id="<?=$id?>" >
                            <?=Helper::ShowHint(Loc::getMessage('ACRIT_CONSTANTS_NAME_HINT'));?>
                            <label for="field_constant_name"><?=$obTabControl->GetCustomLabelHTML()?></label>
                            <input type="text" name="PROFILE[OTHER][CONSTANTS][LIST][<?=$id;?>][NAME]" size="30" maxlength="36"
                                   placeholder="<?=Loc::getMessage('CONSTANTS_NAME');?>"
                                   value="<?=htmlspecialcharsbx($item['NAME']);?>"
                                   data-role="acrit_other_constant_name" />
                            <?=Helper::ShowHint(Loc::getMessage('ACRIT_CONSTANTS_VALUE_HINT'));?>
                            <label for="field_constant_value"><?=$obTabControl->GetCustomLabelHTML()?></label>
                            <input type="text" name="PROFILE[OTHER][CONSTANTS][LIST][<?=$id;?>][VALUE]" size="40" maxlength="255"
                                   placeholder="<?=Loc::getMessage('CONSTANTS_VALUE');?>"
                                   value="<?=htmlspecialcharsbx($item['VALUE']);?>"
                                   data-role="acrit_other_constant_value" />
<!--                            --><?//=Helper::showHint(Loc::getMessage('CONSTANTS_HINT', ['#STORE_URL#' => $strStoreUrl]));?>
                            <input type="button" data-role="acrit_other_constant_delete"
                                   value="<?=Loc::getMessage('CONSTANTS_DELETE');?>"
                                   data-confirm="<?=Loc::getMessage('CONSTANTS_DELETE_CONFIRM');?>">
                        </div>
                    <?endforeach?>
                </div>
                <div data-role="acrit_other_constants_add_wrapper">
                    <input type="button" data-role="acrit_other_constants_add"
                           value="<?=Loc::getMessage('CONSTANTS_ADD');?>">
                    <!--                    <input type="button" data-role="acrit_exp_ozon_store_add_auto"-->
                    <!--                           value="--><?//=static::getMessage('EXPORT_STOCKS_ADD_AUTO');?><!--">-->
                </div>
            </div>
    </td>
</tr>
<?php
$obTabControl->EndCustomField('PROFILE[OTHER][CONSTANTS]');

$obTabControl->BeginCustomField('PROFILE[OTHER]', Loc::getMessage('ACRIT_CRM_TAB_SPECIAL_STOKS'));
?>
<!--    <tr id="tr_other_stocks">-->
<!--        <td>-->
            <?=$obPlugin->showSpecial();?>
<!--        </td>-->
<!--    </tr>-->
    <?
//file_put_contents(__DIR__.'/prof.txt', var_export($arProfile, true));
$obTabControl->EndCustomField('PROFILE[OTHER]');

$obTabControl->AddSection('HEADING_OTHER_DISCOUNT', Loc::getMessage('ACRIT_OTHER_DISCOUNT_HEADING'));
$obTabControl->BeginCustomField('PROFILE[OTHER][DISCOUNT][ON]', Loc::getMessage('ACRIT_OTHER_DISCOUNT_ON'), true);
?>
    <tr id="tr_other_discount_on">
        <td>
            <div>
                <input type="checkbox" name="PROFILE[OTHER][DISCOUNT][ON]" id="field_other_discount_on" value="Y"<?=$arProfile['OTHER']['DISCOUNT']['ON']=='Y'?' checked':'';?> />
                <label for="field_other_discount_on"><?=$obTabControl->GetCustomLabelHTML()?></label>
                <span><?=Loc::getMessage('ACRIT_OTHER_DISCOUNT_ON_HINT');?></span>
            </div>
        </td>
    </tr>
    <?
$obTabControl->EndCustomField('PROFILE[OTHER][DISCOUNT][ON]');
$obTabControl->BeginCustomField('PROFILE[OTHER][DISCOUNT][PERCENT]', Loc::getMessage('ACRIT_OTHER_DISCOUNT_PERCENT'), true);
?>
 <tr id="tr_other_discount_percent">
        <td>
            <div>
                <span class="adm-required-field"><?=Loc::getMessage('ACRIT_OTHER_DISCOUNT_PERCENT');?></span>:
                <input type="number" name="PROFILE[OTHER][DISCOUNT][PERCENT]" size="10" maxlength="3" style="width: 50px;"
                       value="<?=htmlspecialcharsbx($arProfile['OTHER']['DISCOUNT']['PERCENT']);?>" />
                <?=Helper::ShowHint(Loc::getMessage('ACRIT_OTHER_DISCOUNT_PERCENT_HINT'));?>
            </div>
        </td>
    </tr>
<?php
$obTabControl->EndCustomField('PROFILE[OTHER][DISCOUNT][PERCENT]');
