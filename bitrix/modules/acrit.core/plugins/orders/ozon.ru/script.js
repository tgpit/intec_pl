
$(document).delegate('[data-role="connection-check"]', 'click', function(e) {
	var btn = $(this);
	var client_id = $('[data-role="connect-cred-client_id"]').val();
	var api_key = $('[data-role="connect-cred-api_key"]').val();
	if (!btn.hasClass('adm-btn-disabled')) {
		data = {
			'client_id': client_id,
			'api_key': api_key,
		};
		btn.addClass('adm-btn-disabled');
		$('#check_msg').html('');
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

// Export stocks
$(document).delegate('input[data-role="acrit_exp_ozon_export_stocks"]', 'change', function(e){
	$('div[data-role="acrit_exp_ozon_stores_wrapper"]').toggle($(this).prop('checked'));
});

$(document).delegate('input[data-role="acrit_exp_ozon_store_add"]', 'click', function(e){
	let
		items = $('div[data-role="acrit_exp_ozon_stores_list"]'),
		item = items.children().first(),
		newItem = item.clone();
	newItem.appendTo(items);
	newItem.find('input[type="text"]').val('');
});

$(document).delegate('input[data-role="acrit_exp_ozon_store_delete"]', 'click', function(e, data){
	data = typeof data == 'object' ? data : {};
	if(data.force || confirm($(this).attr('data-confirm'))){
		$(this).closest('[data-role="acrit_exp_ozon_store"]').remove();
	}
});

$(document).ready(function(){

});
