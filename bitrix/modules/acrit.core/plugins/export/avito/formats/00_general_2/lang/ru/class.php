<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'Авито XML v.3 (все типы объявлений)';

//
$MESS[$strLang.'GROUP_COMMON'] = 'Общие элементы';
$MESS[$strLang.'GROUP_XML_EXAMPLE'] = '<a href="#URL#" target="_blank">Скачать пример XML-файла</a>';
$MESS[$strLang.'DESCRIPTION_FIELD_DEPENDENCY'] = '<p><b><i>#TEXT#</i></b></p>';
$MESS[$strLang.'DESCRIPTION_FIELD_EXAMPLE'] = '<p><b>Пример:</b></p><p><code>#EXAMPLE#</code></p>';
$MESS[$strLang.'DESCRIPTION_FIELD_ID']  = '<p><i><small style="color:gray;">ID свойства: #ID#</small></i></p>';
$MESS[$strLang.'GUESS_AD_TYPE_FOR_SELL']  = 'продажу';

// Fields
$MESS[$strName.'Id'] = 'Уникальный идентификатор объявления';
$MESS[$strName.'DateBegin'] = 'Дата и время начала размещения';
$MESS[$strName.'DateEnd'] = 'Дата и время окончания размещения';
$MESS[$strName.'ListingFee'] = 'Вариант платного размещения';
$MESS[$strName.'AdStatus'] = 'Услуга продвижения';
$MESS[$strName.'AvitoId'] = 'Номер объявления на Авито';
$MESS[$strName.'ManagerName'] = 'Имя менеджера';
$MESS[$strName.'ContactPhone'] = 'Контактный телефон';
// Fields (in some categories)
$MESS[$strName.'Description'] = 'Описание';
$MESS[$strName.'Images'] = 'Изображения';
$MESS[$strName.'VideoURL'] = 'Видео';
// Alternative category
$MESS[$strName.'CategoryId'] = 'Категория (альтернатив. выбор)';
	$MESS[$strHint.'CategoryId'] = 'Служебный параметр, для определения категории товара (от категории зависят дополнительные атрибуты).<br/><br/>
	В данном поле для каждого товара должна выгружаться категория Avito. Возможна выгрузка в полной записи, например:<br/><br/>
	<code>[1202095] Личные вещи / Одежда, обувь, аксессуары / Мужская одежда</code>)<br/><br/>
	Также возможна выгрузка только идентификатора, например:<br/><br/>
	<code>1202095</code><br/><br/>
	<b>Внимание!</b> Корректность указания данного параметра является критически важной. Если для товара не будет указана категория (либо будет указана некорректная), дополнительные атрибуты не будут выгружены, либо будут выгружены некорректно.';

# Errors
$MESS[$strLang.'ERROR_EMPTY_CATEGORY_ID'] = 'Для товара #ELEMENT_ID# не задана Avito-категория.';
