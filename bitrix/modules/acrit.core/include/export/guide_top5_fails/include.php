<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper;

Helper::loadMessages(__FILE__);
$strTop5Lang = 'ACRIT_CORE_GUIDE_TOP5_FAILS_';

if($_POST['top5_fails_hide'] == 'Y'){
	Helper::obRestart();
	header('Content-Type: application/json');
	\CUserOptions::setOption($strModuleId, 'hide_guide_top5_fails', $_POST['top5_fails_checked'] == 'Y' ? 'Y' : 'N');
	print \Acrit\Core\Json::encode(['Success' => true]);
	die();
}

if(\CModule::includeModuleEx($strModuleId) != MODULE_DEMO){
	return;
}

if(\CUserOptions::getOption($strModuleId, 'hide_guide_top5_fails') == 'Y'){
	return;
}

?>
<?ob_start();?>
	<form action="https://b24-575jtz.bitrix24.site/crm_form_jjk27/" method="get" target="_blank">
		<div style="text-align:center;">
			<img src="data:image/png;base64,<?=base64_encode(file_get_contents(__DIR__.'/img.png'));?>" /><br/>
		</div>
		<div style="text-align:center;">
			<input type="submit" class="adm-btn-green" id="acrit_core_popup_top5_fails_submit" value="<?=Helper::getMessage($strTop5Lang.'POPUP_BUTTON');?>" />
		</div>
		<div style="margin-top:30px; text-align:left;">
			<label>
				<input type="checkbox" value="Y" id="acrit_core_popup_top5_fails_hide" />
				<span><?=Helper::getMessage($strTop5Lang.'POPUP_HIDE');?></span>
			</label>
		</div>
	</form>
<?$strPopupContent = ob_get_clean();?>
<style>
.acrit_export_popup_top5_fails, .acrit_export_popup_top5_fails > .bx-core-adm-dialog-buttons {
	padding:0!important;
}
.acrit_export_popup_top5_fails .bx-core-adm-dialog-content {
	background:#fff!important;
}
.acrit_export_popup_top5_fails .bx-core-adm-dialog-content-wrap-inner {
	padding-bottom:0!important;
}
.acrit_export_popup_top5_fails input[type="submit"] {
	font-size:130%;
	height:50px!important;
	padding:0 40px;
}
</style>
<script>
// POPUP: top-5 fails
var acritCorePopupTop5fails = new BX.CDialog({
	ID: 'acritCorePopupTop5fails',
	title: '<?=Helper::getMessage($strTop5Lang.'POPUP_TITLE');?>',
	content: `<?=$strPopupContent;?>`,
	draggable: true,
	resizable: false,
	height: 710,
	width: 816
});
acritCorePopupTop5fails.Open = function(error){
	this.Show();
	BX.addClass(this.PARTS.CONTENT, 'acrit_export_popup_top5_fails');
	BX.adminFormTools.modifyCheckbox(BX('acrit_core_popup_top5_fails_hide'));
	BX.bind(BX('acrit_core_popup_top5_fails_submit'), 'click', function(){
		setTimeout(function(){
			acritCorePopupTop5fails.Close();
		}, 500);
	});
	BX.bind(BX('acrit_core_popup_top5_fails_hide'), 'change', function(){
		BX.showWait();
		return BX.ajax({
			url: location.href,
			method: 'POST',
			data: {
				top5_fails_hide: 'Y',
				top5_fails_checked: this.checked ? 'Y' : 'N'
			},
			dataType: 'json',
			timeout: 30,
			async: true,
			processData: true,
			scriptsRunFirst: false,
			emulateOnload: false,
			start: true,
			cache: false,
			onsuccess: function(json){
				BX.closeWait();
			},
			onfailure: function(status, error, config){
				console.error(status, error, config);
				BX.closeWait();
			}
		});
	});
}
BX.ready(function(){
	acritCorePopupTop5fails.Open();
});
</script>