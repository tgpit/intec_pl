function acritExecJQuery(callback){
	var head = document.getElementsByTagName('head')[0];
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js';
	script.onreadystatechange = callback;
	script.onload = callback;
	head.appendChild(script);
}
function acritPrepareInputHtml(html){
	let
		div = $('<div/>').html(html);
	$('*', div).removeAttr('class');
	$('a', div).attr('target', '_blank');
	return div.html().trim();
}
function acritPrepareInputTitle(example){
	let
		title = $('<div/>').html(example).children().first().text().trim().replace(/:$/, '').replace(/\s*—.*?$/g, '');
	return title;
}
function acritPrepareOutput(nameSnake, nameUpper, title, category, description, example, isMultiple){
	let
		multiple = isMultiple ? `
				'MULTIPLE' => true,
				'PARAMS' => ['MULTIPLE' => 'multiple'],` : ``,
		resultDeclare = `
			$arResult[] = new Field(array(
				'CODE' => '${nameUpper}',
				'DISPLAY_CODE' => '${nameSnake}',
				'NAME' => static::getMessage('FIELD_${nameUpper}_NAME'),
				'SORT' => 5000,
				'DESCRIPTION' => static::getMessage('FIELD_${nameUpper}_DESC'),${multiple}
			));
			`,
		resultOutput = isMultiple ? `
			if(!Helper::isEmpty($arFields['${nameUpper}']))
				$arXmlTags['${nameSnake}'] = Xml::addTagWithSubtags($arFields['${nameUpper}'], 'option');
			` : `
			if(!Helper::isEmpty($arFields['${nameUpper}']))
				$arXmlTags['${nameSnake}'] = Xml::addTag($arFields['${nameUpper}']);
			`,
		categoryNote = category.length ? `<br/><br/>Актуально для категорий: ${category}` : '',
		resultLang = `
$MESS[$strMessPrefix.'FIELD_${nameUpper}_NAME'] = '${title}';
	$MESS[$strMessPrefix.'FIELD_${nameUpper}_DESC'] = '${description}${categoryNote}';
		`;
		
		window.acritAvitoDeclare.push(resultDeclare.replace(/^\n/g, '').replace(/\n\s+$/g, '\n'));
		window.acritAvitoOutput.push(resultOutput.replace(/^\n/g, '').replace(/\n\s+$/g, '\n'));
		window.acritAvitoLang.push(resultLang.replace(/^\n/g, '').replace(/\n\s+$/g, '\n'));

}
acritExecJQuery(function(){
	let
		fields = 'Place, ClientGender, SpecialistGender, Specialty, WorkWithContract, Accommodation, TeamSize, ContactDays, WorkDays, UrgencyFee, MinimumOrderAmount, MaterialPurchase, ContactTimeFrom, ContactTimeTo, WorkTimeFrom, WorkTimeTo, GoodsType, Make, Model, BodyType, TypeOfVehicle, SubTypeOfVehicle, TypeOfTrailer, TypeOfVehicleSemiTrailerCoupling, MakeSemiTrailerCoupling, ModelSemiTrailerCoupling, TypeSemiTrailerCoupling, MakeKmu, ModelKmu, Brand, Body, PremiumAppliances, HighAltitudeWork, LoadingType, EmbeddingType, Device, Display',
		fieldsArray = fields.split(', ');
	window.acritAvitoDeclare = [];
	window.acritAvitoOutput = [];
	window.acritAvitoLang = [];
	$(fieldsArray).each(function(){
		let
			selector = `a[href="#${this}"][name="${this}"]`,
			a = $(selector).first();
			if(a.length){
				let
				nameSnake = this,
				nameUpper = nameSnake.replace(/([a-z])([A-Z])/g, '$1_$2').toUpperCase(),
				tdLink = a.closest('td'),
				tdCategory = tdLink.next('td'),
				category = acritPrepareInputHtml(tdCategory.html()),
				tdDescription = tdCategory.next('td'),
				description = acritPrepareInputHtml(tdDescription.html()),
				title = acritPrepareInputTitle(description),
				trThis = tdLink.closest('tr'),
				trNext = trThis.next('tr'),
				tdExample = trNext.children('td').children('pre'),
				example = tdExample.text().trim(),
				isMultiple = example.indexOf('<Option>') != -1;
			acritPrepareOutput(nameSnake, nameUpper, title, category, description, example, isMultiple);
			}
	});
	console.log(window.acritAvitoDeclare.join(''));
	console.log(window.acritAvitoOutput.join(''));
	console.log(window.acritAvitoLang.join(''));
});
