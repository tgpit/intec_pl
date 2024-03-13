function acritExpAvitoStocksApiInitialize(){
	if(!window.avito_stocks_apiInitialized){
		
		// Check OAuth
		$(document).on('click', 'input[data-role="acrit_exp_avito_api_oauth_client_check"]', function(e) {
			let
				btn = $(this),
				clientId = $('input[data-role="acrit_exp_avito_api_oauth_client_id"]').val().trim(),
				clientSecretId = $('input[data-role="acrit_exp_avito_api_oauth_client_secret_id"]').val().trim(),
				data = {
					client_id: clientId,
					client_secret_id: clientSecretId,
				};
			btn.attr('disabled', 'disabled');
			acritExpAjax(['plugin_ajax_action', 'oauth_check'], data, function(JsonResult, textStatus, jqXHR){
				btn.removeAttr('disabled');
				if(JsonResult.Success){
					alert(JsonResult.Message);
				}
				else{
					alert(JsonResult.Message);
				}
				acritExpHandleAjaxError(jqXHR, false);
			}, function(jqXHR){
				acritExpHandleAjaxError(jqXHR, true);
				btn.removeAttr('disabled');
			}, false);
		});
		
	}
	window.avito_stocks_apiInitialized = true;
}

// On load
setTimeout(function(){
	acritExpAvitoStocksApiInitialize();
}, 500);
$(document).ready(function(){
	acritExpAvitoStocksApiInitialize();
});

// On current IBlock change
BX.addCustomEvent('onLoadStructureIBlock', function(a){
	acritExpAvitoStocksApiInitialize();
});