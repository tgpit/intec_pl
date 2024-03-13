
/**
 * Token check
 */

$(document).delegate('[data-role="acrit_exp_settings_token_check"]', 'click', function(e) {
	var btn = $(this);
	if (!btn.hasClass('adm-btn-disabled')) {
		btn.addClass('adm-btn-disabled');
		acritExpAjax(['plugin_ajax_action', 'check_connection'], {
			'APP_ID': $('[name="PROFILE[PARAMS][APP_ID]"]').val(),
			'APP_PUB_KEY': $('[name="PROFILE[PARAMS][APP_PUB_KEY]"]').val(),
			'APP_SEC_KEY': $('[name="PROFILE[PARAMS][APP_SEC_KEY]"]').val(),
			'ACCESS_TOKEN': $('[name="PROFILE[PARAMS][ACCESS_TOKEN]"]').val(),
			'GROUP_ID': $('[name="PROFILE[PARAMS][GROUP_ID]"]').val(),
		}, function (JsonResult, textStatus, jqXHR) {
			if (JsonResult.result == 'ok') {
				btn.removeClass('adm-btn-disabled');
				if (JsonResult.check == 'success') {
					$('#acrit_exp_settings_token_check_msg').html(JsonResult.message);
				}
				else {
					$('#acrit_exp_settings_token_check_msg').html('<span class="required">' + JsonResult.message + '</span>');
				}
			}
		}, function (jqXHR) {
			console.log(jqXHR);
		}, true);
	}
	return false;
});


if (!window.okPluginInitialized) {
	function okGoodsAccessTokenHandleChange(){
		$('#acrit_exp_plugin_access_token').unbind('textchange').bind('textchange', function(e) {
			var accessToken = $(this).val().match(/access_token=(\w+)/i);
			if(accessToken != null){
				$(this).val(accessToken[1]);
			}
		});
	}
	//
	window.okPluginInitialized = true;

	// Reset PROCESS_NEXT_POS param
	$(document).delegate('#acrit_exp_plugin_ok_process_next_pos_reset', 'click', function(e) {
		var btn = $(this);
		if (!btn.hasClass('adm-btn-disabled')) {
			if (confirm(BX.message('SETTINGS_PROCESS_NEXT_POS_RESET_ALERT'))) {
				data = {};
				btn.addClass('adm-btn-disabled');
				acritExpAjax(['plugin_ajax_action', 'params_next_pos_reset'], data, function (JsonResult, textStatus, jqXHR) {
						//console.log(JsonResult);
						btn.removeClass('adm-btn-disabled');
						if (JsonResult.result == 'ok') {
							$('#acrit_exp_plugin_ok_process_next_pos_view').text('0');
						}
					}, function (jqXHR) {
						console.log(jqXHR);
					}, true
				);
			}
		}
		return false;
	});
}


// On page load
$(document).ready(function(){
	okGoodsAccessTokenHandleChange();
});

// On change plugin
setTimeout(function(){
	okGoodsAccessTokenHandleChange();
}, 500);
