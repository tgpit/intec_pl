if (!window.RegmarketsGeneralInitialized) {

	$(document).on('click', 'input[data-role="acrit_exp_regmarkets_email_send_now"]', function(e){
		let
			button = $(this),
			data = {
				email: $('input[data-role="acrit_exp_regmarkets_email_receiver"]').val().trim(),
				subject: $('input[data-role="acrit_exp_regmarkets_email_subject"]').val().trim(),
				sender: $('input[data-role="acrit_exp_regmarkets_email_sender"]').val().trim(),
				inn: $('input[data-role="acrit_exp_regmarkets_email_inn"]').val().trim(),
				fio: $('input[data-role="acrit_exp_regmarkets_email_fio"]').val().trim(),
				phone: $('input[data-role="acrit_exp_regmarkets_email_phone"]').val().trim()
			};
		button.attr('disabled', 'disabled');
		acritExpAjax(['plugin_ajax_action', 'send_email'], data, function (arJsonResult, textStatus, jqXHR) {
			if(arJsonResult.Success){
				alert(button.attr('data-success'));
			}
			else{
				alert(button.attr('data-error'));
			}
			button.removeAttr('disabled');
		}, function (jqXHR) {
			console.log(jqXHR);
			alert(button.attr('data-error'));
			button.removeAttr('disabled');
		}, true);
	});

	window.RegmarketsGeneralInitialized = true;
}

