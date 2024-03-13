if (!window.onlinerbyNewApiInitialized) {
	
	function acritExpOnlinerbyNewApiCatAttrUpdateEnableControls(enabled){
		let
			btnStart = $('input[data-role="categories-update-attributes-start"]'),
			btnStop = $('input[data-role="categories-update-attributes-stop"]'),
			loader = $('div[data-role="categories-update-attributes-loader"]');
		if(enabled){
			btnStart.removeClass('hidden');
			btnStop.addClass('hidden');
			loader.addClass('hidden');
		}
		else{
			btnStart.addClass('hidden');
			btnStop.removeClass('hidden');
			loader.removeClass('hidden');
		}
	}
	
	function acritExpOnlinerbyNewApiCatAttrUpdateExecute(start, force, justAttr){
		let
			action = ['plugin_ajax_action', 'category_attributes_update'],
			data = {
				iblock_id: $('#field_IBLOCK').val(),
				start: start ? 'Y' : 'N'
			};
		if(force){
			data.force = 'Y';
		}
		if(justAttr){
			data.just_attr = 'Y';
		}
		acritExpOnlinerbyNewApiCatAttrUpdateEnableControls(false);
		window.acritExpOnlinerbyNewApiAjaxUpdateAttr = acritExpAjax(action, data, function (arJsonResult, textStatus, jqXHR) {
			if(arJsonResult.Continue){
				acritExpOnlinerbyNewApiCatAttrUpdateExecute(false);
			}
			else{
				acritExpOnlinerbyNewApiCatAttrUpdateEnableControls(true);
			}
			if(arJsonResult.Html){
				$('div[data-role="categories-update-attributes-result"]').html(arJsonResult.Html).closest('tr').show();
			}
		}, function (jqXHR) {
			console.log(jqXHR);
			acritExpOnlinerbyNewApiCatAttrUpdateEnableControls(true);
		}, true);
	}
	
	function acritExpOnlinerbyNewApiCatAttrUpdateStop(){
		acritExpOnlinerbyNewApiCatAttrUpdateEnableControls(true);
		if(window.acritExpOnlinerbyNewApiAjaxUpdateAttr){
			window.acritExpOnlinerbyNewApiAjaxUpdateAttr.abort();
		}
	}
	
	$(document).delegate('input[data-role="categories-update-attributes-start"]', 'click', function(e) {
		acritExpOnlinerbyNewApiCatAttrUpdateExecute(true, e.ctrlKey, e.shiftKey);
	});
	
	$(document).delegate('input[data-role="categories-update-attributes-stop"]', 'click', function(e) {
		acritExpOnlinerbyNewApiCatAttrUpdateStop();
	});
	
	$(document).delegate('input[data-role="acrit_exp_onlinerby_access_check"]', 'click', function(e){
		e.preventDefault();
		let
			clientId = $('input[data-role="acrit_exp_onlinerby_company_id"]').val(),
			apiKey = $('input[data-role="acrit_exp_onlinerby_api_key"]').val(),
			data = {client_id: clientId, api_key: apiKey};
		if(clientId.length && apiKey){
			acritExpAjax(['plugin_ajax_action', 'check_access'], data, function(JsonResult, textStatus, jqXHR){
				if(JsonResult.Message){
					alert(JsonResult.Message);
				}
				acritExpHandleAjaxError(jqXHR, false);
			}, function(jqXHR){
				acritExpHandleAjaxError(jqXHR, true);
			}, false);
		}
	});
	
	$(document).delegate('input[data-role="log-tasks-refresh"]', 'click', function(e, params) {
		acritExpAjax(['plugin_ajax_action', 'refresh_tasks_list'], params, function(JsonResult, textStatus, jqXHR){
			$('#tr_LOG_CUSTOM > td').html(JsonResult.HTML);
			acritExpHandleAjaxError(jqXHR, false);
		}, function(jqXHR){
			acritExpHandleAjaxError(jqXHR, true);
		}, false);
	});
	
	$(document).delegate('a[data-role="log-tasks-item-update-status"]', 'click', function(e) {
		let
			row = $(this).closest('[data-task-id]'),
			taskId = row.attr('data-task-id'),
			data = {task_id: taskId},
			detailsShown = $('div[data-role="log-tasks-status-details-table"]', row).is(':visible');
		acritExpAjax(['plugin_ajax_action', 'update_task_status'], data, function(JsonResult, textStatus, jqXHR){
			row.find('[data-role="log-tasks-item-status"]').html(JsonResult.HTML);
			if(JsonResult.StatusUpdateDatetime){
				row.find('[data-role="log-tasks-item-status-datetime"]').html(JsonResult.StatusUpdateDatetime);
			}
			if(detailsShown){
				$('div[data-role="log-tasks-status-details-table"]', row).show();
			}
			acritExpHandleAjaxError(jqXHR, false);
		}, function(jqXHR){
			acritExpHandleAjaxError(jqXHR, true);
		}, false);
	});
	
	$(document).delegate('a[data-role="log-tasks-status-toggle"]', 'click', function(e) {
		let
			div = $(this).next();
		e.preventDefault();
		if(!div.is(':animated')){
			div.slideToggle(150);
		}
	});
	
	let timeout;
	$(document).delegate('input[data-role="allowed-values-filter-text"]', 'input', function(e) {
		let 
			input = $(this),
			data = {
				field: $('input[data-role="allowed-values-current-field"]').val(),
				query: $.trim(input.val())
			};
		clearTimeout(timeout);
		timeout = setTimeout(function(){
			input.addClass('loading');
			acritExpAjax(['plugin_ajax_action', 'allowed_values_filter'], data, function(arJsonResult){
				input.removeClass('loading');
				$('div[data-role="allowed-values-filter-results"]').html(arJsonResult.HTML);	
			}, function(){
				input.removeClass('loading');
			});
		}, 500);
	});
	
	$(document).delegate('div[data-role="allowed-values-found-items"] span', 'click', function(e) {
		let
			span = this,
			colorClass = 'colored';
		acritCoreCopyToClipboard(span);
		$(span).addClass(colorClass);
		setTimeout(function(){
			$(span).removeClass(colorClass);
		}, 300);
	});
	
	$(document).delegate('a[data-role="log-tasks-status-preview"]', 'click', function(e){
		let data = {
			history_item_id: $(this).attr('data-id')
		};
		e.preventDefault();
		AcritPopupHint.SetSize({width:1000, height:400});
		AcritPopupHint.Open();
		AcritPopupHint.SetTitle('Json preview');
		AcritPopupHint.SetHtml(BX.message('ACRIT_EXP_POPUP_LOADING'));
		acritExpAjax(['plugin_ajax_action', 'history_item_json_preview'], data, function(arJsonResult){
			AcritPopupHint.SetHtml(arJsonResult.HTML);
			$('pre > code', AcritPopupHint.PARTS.CONTENT_DATA).each(function(){
				highlighElement(this);
			});
		});
	});
	
	$(document).delegate('a[data-role="log-task-json-preview"]', 'click', function(e){
		let data = {
			item_id: $(this).attr('data-id'),
			task_id: $(this).attr('data-task-id')
		};
		e.preventDefault();
		AcritPopupHint.SetSize({width:1000, height:400});
		AcritPopupHint.Open();
		AcritPopupHint.SetTitle('Json preview');
		AcritPopupHint.SetHtml(BX.message('ACRIT_EXP_POPUP_LOADING'));
		acritExpAjax(['plugin_ajax_action', 'task_json_preview'], data, function(arJsonResult){
			AcritPopupHint.SetHtml(arJsonResult.HTML);
			$('pre > code', AcritPopupHint.PARTS.CONTENT_DATA).each(function(){
				highlighElement(this);
			});
		});
	});
	
	$(document).delegate('#checkbox_CATEGORIES_ALTERNATIVE', 'change', function(e){
		$(this).closest('tr').next().toggle($(this).is(':checked'));
		$(this).closest('tr').next().next().toggle($(this).is(':checked'));
		$('tr.adm-list-table-row[data-field="category_id"]').toggle($(this).is(':checked'));
	});
	
	$(document).delegate('input[data-role="categories-alternative-select"]', 'click', function(e){
		AcritExpPopupCategoriesRedefinitionSelect.Open(this, $('#field_IBLOCK').val(), false, false);
	});
	
	$(document).delegate('input[data-role="categories-alternative-select"]', 'acrit:categoryselect', function(e, params){
		let
			list = $('div[data-role="categories-alternative-list"]'),
			sample = $('div[data-role="categories-alternative-list"] > div[data-role="categories-alternative-item"]:first-child'),
			categoryName = params.category,
			categoryId = categoryName.replace(/^\[(\d+)\].*?$/, '$1'),
			newItem = sample.clone();
		newItem.find('input[type="hidden"]').val(categoryId);
		newItem.find('[data-role="categories-alternative-item-name"]').text(categoryName);
		newItem.appendTo(list);
	});
	
	$(document).delegate('div[data-role="categories-alternative-item-delete"] a', 'click', function(e){
		e.preventDefault();
		$(this).closest('[data-role="categories-alternative-item"]').remove();
	});
	
	$(document).delegate('input[data-role="acrit_exp_onlinerby_new_access_check"]', 'click', function(e){
		e.preventDefault();
		let
			clientId = $('input[data-role="acrit_exp_onlinerby_new_client_id"]').val(),
			apiKey = $('input[data-role="acrit_exp_onlinerby_new_api_key"]').val(),
			data = {client_id: clientId, api_key: apiKey};
		if(clientId.length && apiKey){
			acritExpAjax(['plugin_ajax_action', 'check_access'], data, function(JsonResult, textStatus, jqXHR){
				if(JsonResult.Message){
					alert(JsonResult.Message);
				}
				acritExpHandleAjaxError(jqXHR, false);
			}, function(jqXHR){
				acritExpHandleAjaxError(jqXHR, true);
			}, false);
		}
	});

	// Export stocks
	$(document).delegate('input[data-role="acrit_exp_onlinerby_export_stocks"]', 'change', function(e){
		$('div[data-role="acrit_exp_onlinerby_stores_wrapper"]').toggle($(this).prop('checked'));
	});

	// Add store
	$(document).delegate('input[data-role="acrit_exp_onlinerby_store_add"]', 'click', function(e){
		let
			items = $('div[data-role="acrit_exp_onlinerby_stores_list"]'),
			item = items.children().first(),
			newItem = item.clone();
		newItem.appendTo(items);
		newItem.find('input[type="text"]').val('');
	});

	// Add stores (auto)
	$(document).delegate('input[data-role="acrit_exp_onlinerby_store_add_auto"]', 'click', function(e){
		acritExpAjax(['plugin_ajax_action', 'load_stores'], {}, function(JsonResult, textStatus, jqXHR){
			if(typeof JsonResult.Stores == 'object' && Object.keys(JsonResult.Stores).length){
				// Remove exist data
				$('input[data-role="acrit_exp_onlinerby_store_delete"]:visible').trigger('click', {force:true});
				$('div[data-role="acrit_exp_onlinerby_store"] input[type="text"]').val('');
				// Add new
				var first = true;
				for(var id in JsonResult.Stores){
					if(!first){
						$('input[data-role="acrit_exp_onlinerby_store_add"]').trigger('click');
					}
					$('div[data-role="acrit_exp_onlinerby_store"]').last().each(function(){
						$('input[data-role="acrit_exp_onlinerby_store_id"]', this).val(id);
						$('input[data-role="acrit_exp_onlinerby_store_name"]', this).val(JsonResult.Stores[id]);
					});
					first = false;
				}
			}
			else if(typeof JsonResult.Message == 'string' && JsonResult.Message.length){
				alert(JsonResult.Message);
			}
			acritExpHandleAjaxError(jqXHR, false);
		}, function(jqXHR){
			acritExpHandleAjaxError(jqXHR, true);
		}, false);
	});

	// Delete store
	$(document).delegate('input[data-role="acrit_exp_onlinerby_store_delete"]', 'click', function(e, data){
		data = typeof data == 'object' ? data : {};
		if(data.force || confirm($(this).attr('data-confirm'))){
			$(this).closest('[data-role="acrit_exp_onlinerby_store"]').remove();
		}
	});
	
	function acritExpOnlinerbyNewApiTriggers(){
		$('#checkbox_CATEGORIES_ALTERNATIVE').trigger('change');
		$('input[data-role="acrit_exp_onlinerby_export_stocks"]').trigger('change');		
	}

	window.onlinerbyNewApiInitialized = true;
}

// On load
setTimeout(function(){
	acritExpOnlinerbyNewApiTriggers();
}, 500);
$(document).ready(function(){
	acritExpOnlinerbyNewApiTriggers();	
});

// On current IBlock change
BX.addCustomEvent('onLoadStructureIBlock', function(a){
	acritExpOnlinerbyNewApiTriggers();
});