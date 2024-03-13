if (!window.YandexApiInitialized) {

	// Get oauth token
	$(document).on('click', 'input[data-role="acrit_exp_yandex_market_api_oauth_token_get"]', function(e) {
		let
			button = $(this),
			inputs = button.closest('td').find('input'),
			form = $('<form>'),
			params = {
				response_type: 'code',
				client_id: $.trim($('input[data-role="acrit_exp_yandex_market_api_oauth_client_id"]').val()),
				display: 'popup',
			},
			secretId = $.trim($('input[data-role="acrit_exp_yandex_market_api_oauth_client_secret_id"]').val()),
			allowed = true,
			data = {};
		if(!params.client_id.length){
			alert(button.attr('data-message-no-client-id'));
			allowed = false;
		}
		if(!secretId.length){
			alert(button.attr('data-message-no-client-secret-id'));
			allowed = false;
		}
		if(allowed){
			if(confirm(button.attr('data-message-need-auth'))){
				inputs.attr('disabled', 'disabled');
				form.attr({
					action: 'https://oauth.yandex.ru/authorize',
					method: 'get',
					target: '_blank',
				});
				for(let i in params){
					if(params.hasOwnProperty(i)){
						form.append($('<input>').attr({type:'hidden', name:i, value:params[i]}));
					}
				}
				form.appendTo($('body')).submit();
				setTimeout(function(){
					form.remove();
					$('<div data-role="acrit_exp_yandex_market_api_oauth_token_prompt" />')
						.append(
							$('<span />').text(button.attr('data-message-label-confirm-code'))
						)
						.append(' ')
						.append(
							$('<input type="text" size="10" maxlength="10" />')
								.attr('placeholder', button.attr('data-message-placeholder-confirm-code'))
						)
						.append(' ')
						.append(
							$('<input type="button" />').val(button.attr('data-message-button-confirm-code')).bind('click', function(e){
								let
									confirmCode = $('div[data-role="acrit_exp_yandex_market_api_oauth_token_prompt"]')
										.children('input[type="text"]').val().trim();
								if(confirmCode.length){
									data = {
										client_id: params.client_id,
										client_secret_id: secretId,
										confirm_code: confirmCode,
									};
									acritExpAjax(['plugin_ajax_action', 'get_oauth_token'], data, function(json, textStatus, jqXHR){
										if(json.Success){
											$('input[data-role="acrit_exp_yandex_market_api_oauth_token"]').val(json.AccessToken);
											$('input[data-role="acrit_exp_yandex_market_api_oauth_refresh_token"]').val(json.RefreshToken);
											$('input[data-role="acrit_exp_yandex_market_api_oauth_token_type"]').val(json.TokenType);
											$('input[data-role="acrit_exp_yandex_market_api_oauth_expires_in"]').val(json.ExpiresIn);
											$('input[data-role="acrit_exp_yandex_market_api_oauth_expire_timestamp"]').val(json.ExpireTimestamp);
										}
										else{
											alert(button.attr('data-message-error-get-token'));
										}
										inputs.removeAttr('disabled');
										$('div[data-role="acrit_exp_yandex_market_api_oauth_token_prompt"]').remove();
										acritExpHandleAjaxError(jqXHR, false);
									}, function(jqXHR){
										inputs.removeAttr('disabled');
										$('div[data-role="acrit_exp_yandex_market_api_oauth_token_prompt"]').remove();
										acritExpHandleAjaxError(jqXHR, true);
										spanStatus.removeClass(classL).removeClass(classY).addClass(classN);
									}, false);
								}
							})
						)
						.insertAfter(button);
					$('div[data-role="acrit_exp_yandex_market_api_oauth_token_prompt"] > input[type="text"]').focus();
				}, 500);
			}
		}
	});

	// Check campaign_id
	$(document).on('click', 'input[data-role="acrit_exp_yandex_market_api_campaign_id_check"]', function(e) {
		let
			clientId = $('input[data-role="acrit_exp_yandex_market_api_oauth_client_id"]').val(),
			oauthToken = $('input[data-role="acrit_exp_yandex_market_api_oauth_token"]').val(),
			campaignId = $('input[data-role="acrit_exp_yandex_market_api_campaign_id"]').val(),
			spanStatus = $('span.acrit_exp_yandex_market_api_status[data-role="acrit_exp_yandex_market_api_campaign_id_status"]'),
			btn = $(this),
			classY = 'yandex_market_api_status_y',
			classN = 'yandex_market_api_status_n',
			classL = 'yandex_market_api_status_loading',
			data = {
				client_id: clientId,
				oauth_token: oauthToken,
				campaign_id: campaignId,
			}
		spanStatus.removeClass(classY).removeClass(classN);
		if(!campaignId.length){
			return;
		}
		spanStatus.addClass(classL);
		btn.attr('disabled', 'disabled');
		acritExpAjax(['plugin_ajax_action', 'check_campaign_id'], data, function(json, textStatus, jqXHR){
			btn.removeAttr('disabled');
			spanStatus.removeClass(classL).removeClass(classY).removeClass(classN);
			if(json.Success){
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

	// Check business_id
	$(document).on('click', 'input[data-role="acrit_exp_yandex_market_api_business_id_check"]', function(e) {
		let
			clientId = $('input[data-role="acrit_exp_yandex_market_api_oauth_client_id"]').val(),
			oauthToken = $('input[data-role="acrit_exp_yandex_market_api_oauth_token"]').val(),
			campaignId = $('input[data-role="acrit_exp_yandex_market_api_campaign_id"]').val(),
			businessId = $('input[data-role="acrit_exp_yandex_market_api_business_id"]').val(),
			spanStatus = $('span.acrit_exp_yandex_market_api_status[data-role="acrit_exp_yandex_market_api_business_id_status"]'),
			btn = $(this),
			classY = 'yandex_market_api_status_y',
			classN = 'yandex_market_api_status_n',
			classL = 'yandex_market_api_status_loading',
			data = {
				client_id: clientId,
				oauth_token: oauthToken,
				campaign_id: campaignId,
				business_id: businessId,
			}
		spanStatus.removeClass(classY).removeClass(classN);
		if(!businessId.length){
			return;
		}
		spanStatus.addClass(classL);
		btn.attr('disabled', 'disabled');
		acritExpAjax(['plugin_ajax_action', 'check_business_id'], data, function(json, textStatus, jqXHR){
			btn.removeAttr('disabled');
			spanStatus.removeClass(classL).removeClass(classY).removeClass(classN);
			if(json.Success){
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

	// View businesses
	$(document).on('click', 'input[data-role="acrit_exp_yandex_market_api_business_id_view"]', function(e) {
		acritExpPopupYandexApi.Open();
	});

	acritExpPopupYandexApi = new BX.CDialog({
		ID: 'acritExpPopupYandexApi',
		title: 'business_id',
		content: '',
		resizable: true,
		draggable: true,
		height: 220,
		width: 500
	 });
	 acritExpPopupYandexApi.Open = function () {
		this.Show();
		this.LoadContent();
	 }
	 acritExpPopupYandexApi.LoadContent = function () {
		var thisPopup = this;
		thisPopup.SetTitle(phpVars.messLoading);
		thisPopup.SetContent(phpVars.messLoading);
		acritExpAjax(['plugin_ajax_action', 'load_businesses'], '', function (JsonResult, textStatus, jqXHR) {
			thisPopup.SetTitle(JsonResult.Title);
			thisPopup.SetContent(JsonResult.Html);
			$('.bx-core-adm-dialog-content-wrap-inner', thisPopup.DIV).css({
				'height': '100%',
				'-webkit-box-sizing': 'border-box',
				'-moz-box-sizing': 'border-box',
				'box-sizing': 'border-box'
			}).children().css({
				'height': '100%'
			});
		}, function (jqXHR) {
			thisPopup.SetContent('Error loading popup');
			console.error(jqXHR);
		}, true);
	 }

	// View businesses
	$(document).on('click', 'ul.acrit_exp_yandex_market_api_businesses span[data-business-id]', function(e) {
		$('input[data-role="acrit_exp_yandex_market_api_business_id"]').val($(this).attr('data-business-id'));
		acritExpPopupYandexApi.Close();
	});

	// Export stocks
	$(document).delegate('input[data-role="acrit_exp_yandex_market_api_export_stocks"]', 'change', function(e){
		$('div[data-role="acrit_exp_yandex_market_api_stores_wrapper"]').toggle($(this).prop('checked'));
	});

	// Add store
	$(document).delegate('input[data-role="acrit_exp_yandex_market_api_store_add"]', 'click', function(e){
		let
			items = $('div[data-role="acrit_exp_yandex_market_api_stores_list"]'),
			item = items.children().first(),
			newItem = item.clone();
		newItem.appendTo(items);
		newItem.find('input[type="text"]').val('');
	});

	// Delete store
	$(document).delegate('input[data-role="acrit_exp_yandex_market_api_store_delete"]', 'click', function(e, data){
		data = typeof data == 'object' ? data : {};
		if(data.force || confirm($(this).attr('data-confirm'))){
			$(this).closest('[data-role="acrit_exp_yandex_market_api_store"]').remove();
		}
	});

	// External request
	$(document).delegate('input[data-role="acrit_exp_yandex_market_api_external_request"]', 'change', function(e){
		$('div[data-role="acrit_exp_yandex_market_api_external_request_wrapper"]').toggle($(this).prop('checked'));
	});
	
	function acritExpYandexApiTriggers(){
		$('input[data-role="acrit_exp_yandex_market_api_export_stocks"]').trigger('change');
		$('input[data-role="acrit_exp_yandex_market_api_external_request"]').trigger('change');
	}

	window.YandexApiInitialized = true;
}

// On load
setTimeout(function(){
	acritExpYandexApiTriggers();
}, 500);
$(document).ready(function(){
	acritExpYandexApiTriggers();
});

// On current IBlock change
BX.addCustomEvent('onLoadStructureIBlock', function(a){
	acritExpYandexApiTriggers();
});
