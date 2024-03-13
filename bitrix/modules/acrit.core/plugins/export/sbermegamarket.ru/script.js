var AcritWbPopupCategory;

function acritExpSbermegamarketInitialize(){
	if(!window.sbermegamarketInitialized){

		// Check token main
		$(document).on('click', 'input[data-role="acrit_exp_sbermegamarket_auth_token_check"]', function(e) {
			let
				authToken = $('input[data-role="acrit_exp_sbermegamarket_auth_token"]').val(),
				spanStatus = $('span[data-role="acrit_exp_sbermegamarket_auth_token_status"]'),
				btn = $(this),
				classY = 'sbermegamarket_auth_token_status_y',
				classN = 'sbermegamarket_auth_token_status_n',
				classL = 'sbermegamarket_auth_token_status_loading',
				environment = $('select[data-role="acrit_exp_sbermegamarket_environment"]').val(),
				data = {
					auth_token: authToken,
					environment: environment,
				}
			spanStatus.removeClass(classY).removeClass(classN);
			if(!authToken.length){
				return;
			}
			spanStatus.addClass(classL);
			btn.attr('disabled', 'disabled');
			acritExpAjax(['plugin_ajax_action', 'token_check'], data, function(JsonResult, textStatus, jqXHR){
				btn.removeAttr('disabled');
				spanStatus.removeClass(classL).removeClass(classY).removeClass(classN);
				if(JsonResult.Success){
					spanStatus.addClass(classY);
				}
				else{
					spanStatus.addClass(classN);
				}
				acritExpHandleAjaxError(jqXHR, false);
			}, function(jqXHR){
				acritExpHandleAjaxError(jqXHR, true);
				btn.removeAttr('disabled');
				spanStatus.removeClass(classL).removeClass(classY).addClass(classN);
			}, false);
		});
		
	}
	window.sbermegamarketInitialized = true;
}

// On load
setTimeout(function(){
	acritExpSbermegamarketInitialize();
}, 500);
$(document).ready(function(){
	acritExpSbermegamarketInitialize();
});

// On current IBlock change
BX.addCustomEvent('onLoadStructureIBlock', function(a){
	acritExpSbermegamarketInitialize();
});