
$(document).delegate('[data-role="connection-check"]', 'click', function(e) {
	var btn = $(this);
	var username = $('[data-role="connect-cred-username"]').val();
	var password = $('[data-role="connect-cred-password"]').val();
	var client_id = $('[data-role="connect-cred-client_id"]').val();
	var client_secret = $('[data-role="connect-cred-client_secret"]').val();
	var api_key = $('[data-role="connect-cred-api_key"]').val();
	if (!btn.hasClass('adm-btn-disabled')) {
		data = {
			'api_key': api_key,
			'username': username,
			'password': password,
			'client_id': client_id,
			'client_secret': client_secret,
		};
		btn.addClass('adm-btn-disabled');
		acritExpAjax(['plugin_ajax_action', 'connection_check'], data, function (JsonResult, textStatus, jqXHR) {
			if (JsonResult.result == 'ok') {
				btn.removeClass('adm-btn-disabled');
				if (JsonResult.check == 'success') {
					$('#check_msg').html(JsonResult.message);
				}
				else {
					$('#check_msg').html('<span class="required">' + JsonResult.message + '</span>');
				}
			}
		}, function (jqXHR) {
			console.log(jqXHR);
		}, true);
	}
	return false;
});


$(document).ready(function(){

});
