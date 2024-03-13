if(typeof BX == 'function'){
	BX.ready(function(){
		setTimeout(function(){
			BX.ajax({
				url: '/bitrix/admin/acrit_core_check_updates.php?regular=Y&lang='+phpVars.LANGUAGE_ID,
				method: 'GET',
				dataType: 'json',
				cache: false,
				async: true,
				start: true
			});
		}, 1000);
	});
}
