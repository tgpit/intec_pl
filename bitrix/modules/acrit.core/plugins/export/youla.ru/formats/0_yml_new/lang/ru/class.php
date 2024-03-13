<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'Юла (формат YML, новый)';

//
$MESS[$strLang.'GROUP_COMMON'] = 'Общие элементы';
// $MESS[$strLang.'GROUP_XML_EXAMPLE'] = '<a href="#URL#" target="_blank">Скачать пример XML-файла</a>';
// $MESS[$strLang.'DESCRIPTION_FIELD_EXAMPLE'] = '<p><b>Пример:</b></p><p><code>#EXAMPLE#</code></p>';
// $MESS[$strLang.'DESCRIPTION_FIELD_ID']  = '<p><i><small style="color:gray;">ID свойства: #ID#</small></i></p>';
// $MESS[$strLang.'GUESS_AD_TYPE_FOR_SELL']  = 'продажу';

// Fields
$MESS[$strHead.'HEADER_COMMON'] = 'Общие элементы';
$MESS[$strName.'@id'] = 'Идентификатор';
	$MESS[$strHint.'@id'] = 'Идентификатор объявления';
$MESS[$strName.'url'] = 'Ссылка';
	$MESS[$strHint.'url'] = 'Ссылка на товарное предложение в источнике, например: https://youla.ru/sale/tovar.html.';
$MESS[$strName.'address'] = 'Адрес';
	$MESS[$strHint.'address'] = 'Адрес, где пользователь может забрать товар или получить услугу.<br/><br/>От содержимого данного тега зависит, по какому региону объявление будет показано пользователям.';
$MESS[$strName.'price'] = 'Цена';
	$MESS[$strHint.'price'] = 'Актуальная стоимость товара.<br/><br/>Целочисленное значение, дробные значения не допускаются, стоимость должна быть больше нуля.';
$MESS[$strName.'phone'] = 'Телефон';
	$MESS[$strHint.'phone'] = 'Контактный телефон.<br/><br/>В объявлении может быть указан только один телефон (11 цифр).';
$MESS[$strName.'name'] = 'Полное название предложения';
	$MESS[$strHint.'name'] = 'Полное название предложения, в которое входит: тип товара, производитель, модель и название товара, важные характеристики. Является заголовком объявления.<br/><br/>Допускается максимум 100 символов с учетом пробелов.';
$MESS[$strName.'picture'] = 'Изображения';
	$MESS[$strHint.'picture'] = 'URL-ссылка на изображение товара, если изображений несколько, необходимо создать несколько тегов <picture> на одном уровне.<br/><br/>Cсылки на изображения должны быть в формате полных путей, изображения должны открываться сразу по ссылке без редиректов. Допускаются протоколы http, https.';
$MESS[$strName.'description'] = 'Описание предложения';
	$MESS[$strHint.'description'] = 'Описание предложения.<br/><br/>Текст должен быть заключен в объект <code>&lt;![CDATA[]]&gt;</code>, перенос текста на новую строку осуществляется спецсимволами <code>\r\n</code>, допускается максимум 3000 символов с учетом пробелов.';
$MESS[$strName.'managerName'] = 'Имя менеджера';
	$MESS[$strHint.'managerName'] = 'Имя менеджера, отображается при нажатии на кнопку Показать телефон.';
$MESS[$strName.'сategoryId'] = 'Категория объявления на Юле';
	$MESS[$strHint.'сategoryId'] = 'Числовой идентификатор подкатегории на Юле (применяется только при альтернативном выборе категорий).';


# Errors
$MESS[$strLang.'ERROR_EMPTY_CATEGORY_ID'] = 'Для товара #ELEMENT_ID# не задана категория.';
$MESS[$strLang.'ERROR_WRONG_ATTRIBUTE_VALUE'] = '[Товар #ELEMENT_ID#] Некорректное значение "#VALUE#" для атрибута #ATTRIBUTE#.';
