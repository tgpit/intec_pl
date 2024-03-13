<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

if(!Helper::strlen($this->arParams['EMAIL']['RECEIVER'])){
	$this->arParams['EMAIL']['RECEIVER'] = static::EMAIL_DEFAULT;
}
if(!Helper::strlen($this->arParams['EMAIL']['SUBJECT'])){
	$this->arParams['EMAIL']['SUBJECT'] = static::getMessage('SETTINGS_NAME_SEND_TO_EMAIL_SUBJECT_DEFAULT');
}

?>

<div style="margin-bottom:8px;">
	<input type="text" name="PROFILE[PARAMS][EMAIL][RECEIVER]" data-role="acrit_exp_regmarkets_email_receiver"
		value="<?=htmlspecialcharsbx($this->arParams['EMAIL']['RECEIVER']);?>" size="50" maxlength="255"
		placeholder="<?=static::getMessage('SETTINGS_NAME_SEND_TO_EMAIL_RECEIVER');?>"/>
</div>

<div style="margin-bottom:8px;">
	<input type="text" name="PROFILE[PARAMS][EMAIL][SUBJECT]" data-role="acrit_exp_regmarkets_email_subject"
		value="<?=htmlspecialcharsbx($this->arParams['EMAIL']['SUBJECT']);?>" size="50" maxlength="255"
		placeholder="<?=static::getMessage('SETTINGS_NAME_SEND_TO_EMAIL_SUBJECT');?>"/>
</div>

<div style="margin-bottom:8px;">
	<input type="text" name="PROFILE[PARAMS][EMAIL][SENDER]" data-role="acrit_exp_regmarkets_email_sender"
		value="<?=htmlspecialcharsbx($this->arParams['EMAIL']['SENDER']);?>" size="50" maxlength="255"
		placeholder="<?=static::getMessage('SETTINGS_NAME_SEND_TO_EMAIL_SENDER');?>"/>
</div>

<div style="margin-bottom:8px;">
	<input type="text" name="PROFILE[PARAMS][EMAIL][INN]" data-role="acrit_exp_regmarkets_email_inn"
		value="<?=htmlspecialcharsbx($this->arParams['EMAIL']['INN']);?>" size="50" maxlength="255"
		placeholder="<?=static::getMessage('SETTINGS_NAME_SEND_TO_EMAIL_INN');?>"/>
</div>

<div style="margin-bottom:8px;">
	<input type="text" name="PROFILE[PARAMS][EMAIL][FIO]" data-role="acrit_exp_regmarkets_email_fio"
		value="<?=htmlspecialcharsbx($this->arParams['EMAIL']['FIO']);?>" size="50" maxlength="255"
		placeholder="<?=static::getMessage('SETTINGS_NAME_SEND_TO_EMAIL_FIO');?>"/>
</div>

<div style="margin-bottom:8px;">
	<input type="text" name="PROFILE[PARAMS][EMAIL][PHONE]" data-role="acrit_exp_regmarkets_email_phone"
		value="<?=htmlspecialcharsbx($this->arParams['EMAIL']['PHONE']);?>" size="50" maxlength="255"
		placeholder="<?=static::getMessage('SETTINGS_NAME_SEND_TO_EMAIL_PHONE');?>"/>
</div>

<div style="margin-bottom:8px;">
	<input type="button" data-role="acrit_exp_regmarkets_email_send_now"
		value="<?=static::getMessage('SETTINGS_NAME_SEND_TO_EMAIL_SEND_NOW');?>"
		data-success="<?=static::getMessage('SETTINGS_NAME_SEND_TO_EMAIL_SUCCESS');?>"
		data-error="<?=static::getMessage('SETTINGS_NAME_SEND_TO_EMAIL_ERROR');?>" />
</div>

<div style="margin-bottom:8px;">
	<label>
		<input type="hidden" name="PROFILE[PARAMS][EMAIL][AUTO]" value="N" />
		<input type="checkbox" name="PROFILE[PARAMS][EMAIL][AUTO]" data-role="acrit_exp_regmarkets_email_auto"
			value="Y"<?if($this->arParams['EMAIL']['AUTO'] == 'Y'):?> checked<?endif?>/>
		<span><?=static::getMessage('SETTINGS_NAME_SEND_TO_EMAIL_AUTO');?></span>
	</label>
</div>
