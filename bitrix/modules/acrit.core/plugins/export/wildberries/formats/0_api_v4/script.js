var AcritWbPopupCategory;

function acritExpWildberriesInitialize(){
	if(!window.wildberriesV4Initialized){
		
		// UUID mask
		window.wildberriesUuidMask = '********-****-****-****-************';
		$('input[data-role="acrit_exp_wildberries_supplier_id"]').inputmask({'mask': window.wildberriesUuidMask});

		// Check token main
		$(document).on('click', 'input[data-role="acrit_exp_wildberries_token_check"]', function(e) {
			let
				supplierId = $('input[data-role="acrit_exp_wildberries_supplier_id"]').val(),
				authToken = $('input[data-role="acrit_exp_wildberries_auth_token"]').val(),
				spanStatus = $('span[data-role="acrit_exp_wildberries_auth_token_status"]'),
				btn = $(this),
				classY = 'wildberries_token_status_y',
				classN = 'wildberries_token_status_n',
				classL = 'wildberries_token_status_loading',
				data = {
					supplier_id: supplierId,
					auth_token: authToken
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

		// Token get main
		$(document).on('input', 'input[data-role="acrit_exp_wildberries_auth_token"]', function(e) {
			$('input[data-role="acrit_exp_wildberries_token_check"]').toggle(!!$(this).val().trim().length);
			$('div[data-role="acrit_exp_wildberries_token_get"]').toggle(!$(this).val().trim().length);
			$('span[data-role="acrit_exp_wildberries_auth_token_status"]').removeClass('wildberries_token_status_y')
				.removeClass('wildberries_token_status_n');
		});
		$('input[data-role="acrit_exp_wildberries_auth_token"]').trigger('input');

		// Categories: add button to add (show popup)
		$(document).delegate('input[data-role="acrit_wb_categories_select"]', 'click', function(e){
			AcritWbPopupCategory.Open($(this).attr('data-popup-title'));
		});
		
		// Categories: remove selected category
		$(document).delegate('div[data-role="acrit_wb_categories_item_delete"] a', 'click', function(e){
			e.preventDefault();
			$(this).closest('[data-role="acrit_wb_categories_item"]').remove();
		});
		
		// Export stocks
		$(document).delegate('input[data-role="acrit_exp_wildberries_export_stocks"]', 'change', function(e){
			$('div[data-role="acrit_exp_wildberries_stores_wrapper"]').toggle($(this).prop('checked'));
		});
		$('input[data-role="acrit_exp_wildberries_export_stocks"]').trigger('change');

		function acritExpWildberriesCatAttrUpdateEnableControls(enabled){
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
		
		function acritExpWildberriesCatAttrUpdateExecute(start, force, justAttr){
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
			acritExpWildberriesCatAttrUpdateEnableControls(false);
			window.acritExpWildberriesAjaxUpdateAttr = acritExpAjax(action, data, function (arJsonResult, textStatus, jqXHR) {
				if(arJsonResult.Continue){
					acritExpWildberriesCatAttrUpdateExecute(false);
				}
				else{
					acritExpWildberriesCatAttrUpdateEnableControls(true);
				}
				if(arJsonResult.Html){
					$('div[data-role="categories-update-attributes-result"]').html(arJsonResult.Html).closest('tr').show();
				}
			}, function (jqXHR) {
				console.log(jqXHR);
				acritExpWildberriesCatAttrUpdateEnableControls(true);
			}, true);
		}
		
		function acritExpWildberriesCatAttrUpdateStop(){
			acritExpWildberriesCatAttrUpdateEnableControls(true);
			if(window.acritExpWildberriesAjaxUpdateAttr){
				window.acritExpWildberriesAjaxUpdateAttr.abort();
			}
		}

		// Attributes update: start
		$(document).delegate('input[data-role="categories-update-attributes-start"]', 'click', function(e) {
			acritExpWildberriesCatAttrUpdateExecute(true, e.ctrlKey, e.shiftKey);
		});
		
		// Attributes update: stop
		$(document).delegate('input[data-role="categories-update-attributes-stop"]', 'click', function(e) {
			acritExpWildberriesCatAttrUpdateStop();
		});

		/**
		 * Popup for select category
		 */
		AcritWbPopupCategory = new BX.CDialog({
			ID: 'AcritWbPopupCategory',
			title: '',
			content: '',
			resizable: true,
			draggable: true,
			height: 310,
			width: 600
		});
		AcritWbPopupCategory.Open = function(title){
			this.SetTitle(title);
			this.LoadContent();
			this.Show();
		}
		AcritWbPopupCategory.LoadContent = function(){
			var thisPopup = this;
			//
			thisPopup.SetContent(BX.message('ACRIT_EXP_POPUP_LOADING'));
			// Set popup buttons
			thisPopup.SetNavButtons();
			//
			acritExpAjax(['plugin_ajax_action', 'popup_categories_add'], {}, function(JsonResult, textStatus, jqXHR){
				thisPopup.SetHtml(JsonResult.HTML);
				thisPopup.SetAutoSize();
				//
				acritExpHandleAjaxError(jqXHR, false);
			}, function(jqXHR){
				acritExpHandleAjaxError(jqXHR, true);
			}, true);
		}
		AcritWbPopupCategory.SetAutoSize = function(){
			$('.bx-core-adm-dialog-content-wrap-inner', this.DIV).css({
				'height': '100%',
				'-webkit-box-sizing': 'border-box',
						'-moz-box-sizing': 'border-box',
								'box-sizing': 'border-box'
			}).children().css({
				'height': '100%'
			});
		}
		AcritWbPopupCategory.SetHtml = function(html){
			$('.bx-core-adm-dialog-content-wrap-inner', this.PARTS.CONTENT_DATA).first().html(html);
		}
		AcritWbPopupCategory.SetNavButtons = function(){
			$(this.PARTS.BUTTONS_CONTAINER).html('');
			this.SetButtons(
				[{
					'name': BX.message('ACRIT_EXP_POPUP_SAVE'),
					'className': 'adm-btn-green',
					'id': this.PARAMS.ID + '_btnSave',
					'action': function(){
						let 
							list = $('div[data-role="acrit_wb_categories_list"]'),
							sample = $('div[data-role="acrit_wb_categories_list"] > div[data-role="acrit_wb_categories_item"]:first-child'),
							categoryName = $('select[data-role="acrit_wb_popup_category_select"]').val(),
							newItem = sample.clone();
						if(categoryName.length){
							newItem.find('input[type="hidden"]').val(categoryName);
							newItem.find('[data-role="acrit_wb_categories_item_name"]').text(categoryName);
							newItem.appendTo(list);
							this.parentWindow.Close();
						}
					}
				}, {
					'name': BX.message('ACRIT_EXP_POPUP_CANCEL'),
					'id': this.PARAMS.ID + '_btnCancel',
					'action': function(){
						this.parentWindow.Close();
					}
				}]
			)
		}

		/* Log */
		$(document).delegate('input[data-role="log-tasks-refresh"]', 'click', function(e, params) {
			acritExpAjax(['plugin_ajax_action', 'refresh_tasks_list'], params, function(JsonResult, textStatus, jqXHR){
				$('#tr_LOG_CUSTOM > td').html(JsonResult.HTML);
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
	
		$(document).delegate('a[data-role="log-tasks-preview"]', 'click', function(e){
			let data = {
				task_id: $(this).closest('tr[data-task-id]').attr('data-task-id')
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
	
		$(document).delegate('a[data-role="log-tasks-status-preview"]', 'click', function(e){
			let data = {
				task_id: $(this).closest('tr[data-task-id]').attr('data-task-id'),
				history_item_id: $(this).attr('data-id'),
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

		/* CARDS BROWSER */
		$(document).delegate('input[data-role="acrit_exp_wildberries_cards_browser_execute"]', 'click', function(e){
			let
				div = $(this).closest('.acrit_wb_cards_explorer_wrapper'),
				type = $(this).attr('data-type'),
				countPerPage = $('input[data-role="acrit_exp_wildberries_cards_browser_count_per_page"]', div).val(),
				sortField = $('select[data-role="acrit_exp_wildberries_cards_browser_sort_field"]', div).val(),
				sortOrder = $('select[data-role="acrit_exp_wildberries_cards_browser_sort_order"]', div).val(),
				withPhoto = $('select[data-role="acrit_exp_wildberries_cards_browser_with_photo"]', div).val(),
				textSearch = $('input[data-role="acrit_exp_wildberries_cards_browser_text_search"]', div).val(),
				navUpdatedAt = $('input[data-role="acrit_exp_wildberries_cards_browser_updated_at"]', div).val(),
				navNmId = $('input[data-role="acrit_exp_wildberries_cards_browser_nm_id"]', div).val(),
				filterVendorCode = $('textarea[data-role="acrit_exp_wildberries_cards_browser_filter_vendor_code"]', div).val(),
				categoryName = $('input[data-role="acrit_exp_wildberries_cards_browser_attributes_category_name"]', div).val(),
				pricesNmId = $('input[data-role="acrit_exp_wildberries_cards_browser_prices_nm_id"]', div).val(),
				setPriceNmId = $('input[data-role="acrit_exp_wildberries_cards_browser_set_price_nm_id"]', div).val(),
				setPriceValue = $('input[data-role="acrit_exp_wildberries_cards_browser_set_price_value"]', div).val(),
				data = {
					type: type,
					count_per_page: countPerPage,
					sort_field: sortField,
					sort_order: sortOrder,
					with_photo: withPhoto,
					text_search: textSearch,
					nav_updated_at: navUpdatedAt,
					nav_nm_id: navNmId,
					filter_vendor_code: filterVendorCode,
					category_name: categoryName,
					prices_nm_id: pricesNmId,
					set_price_nm_id: setPriceNmId,
					set_price_value: setPriceValue,
				};
			acritExpWildberriesCardsBrowserDisable(div, true, true);
			acritExpAjax(['plugin_ajax_action', 'cards_browser'], data, function(JsonResult, textStatus, jqXHR){
				acritExpHandleAjaxError(jqXHR, false);
				acritExpWildberriesCardsBrowserDisable(div, false);
				div.parent().find('div[data-role="acrit_exp_wildberries_cards_browser_ajax_result"]').html(JsonResult.HTML);
			}, function(jqXHR){
				acritExpHandleAjaxError(jqXHR, true);
				acritExpWildberriesCardsBrowserDisable(div, false);
			}, true);
		});

		function acritExpWildberriesCardsBrowserDisable(div, flag, clear){
			let
				buttons = $('input[data-role="acrit_exp_wildberries_cards_browser_execute"]', div),
				divAjaxResult = div.parent().find('div[data-role="acrit_exp_wildberries_cards_browser_ajax_result"]');
			if(clear){
				divAjaxResult.html('');
			}
			if(flag){
				buttons.attr('disabled', 'disabled');
			}
			else{
				buttons.removeAttr('disabled');
			}
			divAjaxResult.toggleClass('acrit-core-loader', flag);
		}

		$('input[data-role="acrit_exp_wildberries_cards_browser_page"]')
			.add($('input[data-role="acrit_exp_wildberries_cards_browser_count_per_page"]'))
			.add($('select[data-role="acrit_exp_wildberries_cards_browser_sort_field"]'))
			.add($('select[data-role="acrit_exp_wildberries_cards_browser_sort_order"]'))
			.bind('keydown', function(e){
				if(e.keyCode == 13){
					e.preventDefault();
					$('input[data-role="acrit_exp_wildberries_cards_browser_execute"][data-type="cards"]').trigger('click');
				}
			});
		
			$('[data-role="acrit_wb_card_browser_wrapper"] :input').bind('keydown', function(e){
				if(e.keyCode == 13){
					e.preventDefault();
					$(this).closest('.adm-detail-content-item-block-view-tab').find('input[type=button][data-type]').trigger('click');
				}
			})
		
	}
	window.wildberriesV4Initialized = true;
}

// On load
setTimeout(function(){
	acritExpWildberriesInitialize();
}, 500);
$(document).ready(function(){
	acritExpWildberriesInitialize();
});

// On current IBlock change
BX.addCustomEvent('onLoadStructureIBlock', function(a){
	acritExpWildberriesInitialize();
});