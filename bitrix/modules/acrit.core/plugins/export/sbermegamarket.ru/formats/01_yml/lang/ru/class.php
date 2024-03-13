<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'СберМегаМаркет (YML)';

// Fields
$MESS[$strHead.'HEADER_GENERAL'] = 'Общая информация';
	$MESS[$strName.'@id'] = 'Идентификатор товара';
		$MESS[$strHint.'@id'] = 'Должен соответствовать артикулу оффера в системе учёта заказов мерчанта. Не должен содержать пробелов. Допустима латиница, цифры, знаки "-" и "_". Максимальная длина - 20 символов. Пробелы недопустимы.';
	$MESS[$strName.'@available'] = 'Доступность';
		$MESS[$strHint.'@available'] = 'Показывает доступность конкретного оффера. Может быть true или false.';
	$MESS[$strName.'url'] = 'Ссылка на страницу';
		$MESS[$strHint.'url'] = 'Ссылка на страницу оффера в магазине партнера. Не обязательно к заполнению, но рекомендуется заполнить.<br/><br/>
		Допустиые символы: латиница и цифры. Кириллические символы и пробелы недопустимы.';
	$MESS[$strName.'name'] = 'Название оффера';
		$MESS[$strHint.'name'] = 'Название оффера.<br/><br/>
		Допустимы символы: латиница, кириллица, цифры.';
	$MESS[$strName.'price'] = 'Цена';
		$MESS[$strHint.'price'] = 'Цена оффера.<br/><br/>
		Целочисленное значение больше нуля. Не целочисленное значение будет автоматически округлено в меньшую сторону, разделитель - точка.';
	$MESS[$strName.'oldprice'] = 'Старая цена';
		$MESS[$strHint.'oldprice'] = 'Старая цена оффера.<br/><br/>
		Целочисленное значение больше нуля. Главное требование к параметру oldprice - он должен быть минимум на 5% больше цены продажи. Например: если Вы продаете футболку за 1000 рублей, то старая цена должна быть минимум 1050 рублей.<br/><br/>
		Обязательно для мерчантов категории Fashion.';
	$MESS[$strName.'categoryId'] = 'Идентификатор категории';
		$MESS[$strHint.'categoryId'] = 'id категории продавца, в которую входит данный оффер.<br/><br/>
		Целочисленное значение. Должно быть больше нуля';
	$MESS[$strName.'picture'] = 'Изображения';
		$MESS[$strHint.'picture'] = 'Ссылка на изображение оффера. Можно передавать несколько изображений, основным на витрине будет первое изображение.<br/><br/>
		Допустимые символы: латиница и цифры. Кириллические символы и пробелы недопустимы';
	$MESS[$strName.'vat'] = 'Ставка НДС';
		$MESS[$strHint.'vat'] = 'Обязательно  для плательщиков НДС.<br/><br/>
		Допустимы только перечисленные возможные значения:<br/>
		<style>
			.sbermegamarket_hint_vat {border:1px solid silver; border-collapse:collapse; margin:10px 0;}
			.sbermegamarket_hint_vat th {background:#cbcbcb; border:1px solid silver; padding:8px 8px; width:50%;}
			.sbermegamarket_hint_vat td {border:1px solid silver; padding:4px 8px; width:50%;}
		</style>
		<table class="sbermegamarket_hint_vat">
			<thead>
				<tr>
					<th>Ставка</th>
					<th>Возможные значения</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>НДС не облагается</td>
					<td>NO_VAT (или 6)</td>
				</tr>
				<tr>
					<td>0%</td>
					<td>VAT_0 (или 5)</td>
				</tr>
				<tr>
					<td>10%</td>
					<td>VAT_10 (или 2)<br/>VAT_10_110 (или 4)</td>
				</tr>
				<tr>
					<td>20%</td>
					<td>VAT_20 (или 1)<br/> VAT_20_120 (или 3)</td>
				</tr>
			</tbody>
		</table>
		<a href="https://conf.goods.ru/merchant-api/1-vvedenie/1-1-tovarnyj-fid" target="_blank">Подробнее</a>';
	$MESS[$strName.'shipment-options.option@days'] = 'Срок отгрузки';
		$MESS[$strHint.'shipment-options.option@days'] = 'Число в днях, количество рабочих дней для отгрузки заказа (целочисленное значение больше или равное нулю).';
	$MESS[$strName.'shipment-options.option@order-before'] = 'Время суток для отгрузки';
		$MESS[$strHint.'shipment-options.option@order-before'] = 'Время окончания операционного дня (обязательный параметр, целочисленное значение от 0 до 24).';
	$MESS[$strName.'vendor'] = 'Производитель';
		$MESS[$strHint.'vendor'] = 'Допустимые символы: латиница, кириллица, цифры.';
	$MESS[$strName.'vendorCode'] = 'Код производителя';
		$MESS[$strHint.'vendorCode'] = 'Целочисленное значение больше нуля.';
	$MESS[$strName.'model'] = 'Модель';
		$MESS[$strHint.'model'] = 'Для объединения товаров разного размера и цвета/принта необходимо корректно указать модель (одинаковая числовая/буквенная часть артикула без указания размера/цвета/материала).<br/><br/>
		Обязательно для мерчантов категории Fashion.<br/><br/>
		Допустимые символы: латиница, кириллица, цифры.';
	$MESS[$strName.'description'] = 'Описание';
		$MESS[$strHint.'description'] = 'Используется при создании новых карточек товаров.<br/><br/>
		Допустимые символы: латиница, кириллица, цифры.';
	$MESS[$strName.'barcode'] = 'Штрих-код в формате EAN';
		$MESS[$strHint.'barcode'] = 'Штрих-код в формате <a href="https://ru.wikipedia.org/wiki/European_Article_Number" target="_blank">EAN</a>. Представлен с помощью цифр, имеет 8, 12 или 13 символов.  Должен находиться за пределами диапазона 20xxxxxxxxxxxx (первые 2 цифры не должны быть равны 20 в случае 13 цифрового баркода).<br/><br/>
		Целочисленное значение больше нуля.';
	$MESS[$strName.'outlets.outlet@id'] = 'Код склада';
		$MESS[$strHint.'outlets.outlet@id'] = 'Код склада (идентификатор), который вы указали в личном кабинете, раздел "Настройки" - "Доставка" / "Склад". Целочисленное значение.';
	$MESS[$strName.'outlets.outlet@instock'] = 'Количество товара на складе';
		$MESS[$strHint.'outlets.outlet@instock'] = 'количество товара на складе (шт.). Целочисленное значение большее или равное нулю.';

?>