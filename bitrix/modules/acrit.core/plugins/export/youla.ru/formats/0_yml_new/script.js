if (!window.avitoGeneralInitialized) {
	
	$(document).delegate('#checkbox_CATEGORIES_ALTERNATIVE', 'change', function(e){
		$(this).closest('tr').next().toggle($(this).is(':checked'));
		$(this).closest('tr').next().next().toggle($(this).is(':checked'));
		$('tr.adm-list-table-row[data-field="CategoryId"]').toggle($(this).is(':checked'));
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
	
	function acritExpavitoGeneralTriggers(){
		$('#checkbox_CATEGORIES_ALTERNATIVE').trigger('change');
	}

	window.avitoGeneralInitialized = true;
}

// On load
setTimeout(function(){
	acritExpavitoGeneralTriggers();
}, 500);
$(document).ready(function(){
	acritExpavitoGeneralTriggers();
});

// On current IBlock change
BX.addCustomEvent('onLoadStructureIBlock', function(a){
	acritExpavitoGeneralTriggers();
});