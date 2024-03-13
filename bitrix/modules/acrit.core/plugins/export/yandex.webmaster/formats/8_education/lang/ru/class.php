<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'Яндекс - Образовательные онлайн-курсы и уроки';

// Fields: General
$MESS[$strHead.'HEADER_GENERAL'] = 'Общая информация';
$MESS[$strName.'@id'] = 'Идентификатор';
	$MESS[$strHint.'@id'] = 'Идентификатор курса. Должен быть уникальным для каждого элемента. Допустимо использовать латинские символы, цифры, символ подчеркивания.';
$MESS[$strName.'name'] = 'Название предложения';
	$MESS[$strHint.'name'] = 'Название предложения. Должно быть уникальным. Если курсы имеют одинаковые названия, то в name укажите разницу между ними, например:
	<ul>
		<li>Курс Java. Уровень 1</li>
		<li>Курс Java. Уровень 2.</li>
	</ul>';
$MESS[$strName.'url'] = 'URL-адрес предложения';
	$MESS[$strHint.'url'] = 'URL-адрес предложения. Должен быть уникальным среди всех предложений в пределах всех фидов одного и того же региона. Метки (например, UTM, Openstat, from) не делают URL уникальным.';
$MESS[$strName.'_category'] = 'Категория (служебный параметр!)';
	$MESS[$strHint.'_category'] = 'В данном поле Вы можете указать собственную категорию товара - это должно быть точное название или точный идентификатор в соответствии с <a href="https://yastatic.net/s3/doc-binary/src/support/products/education_rubricator.xml" target="_blank">рубрикатором</a>.<br/><br/>
	<b>Внимание!</b> По умолчанию данное поле не используется (оно пусто), в этом случае используется стандартный механизм сопоставления категорий (вкладка «Категории»).';
$MESS[$strName.'price'] = 'Стоимость за весь курс';
	$MESS[$strHint.'price'] = 'Стоимость за весь курс. Если фиксированная цена за курс отсутствует, то значение 0. Если значение 0 и не указана ежемесячная цена, то курс считается бесплатным.';
$MESS[$strName.'currencyId'] = 'Валюта';
	$MESS[$strHint.'currencyId'] = 'Идентификатор валюты. Например, RUR для рублей.';
$MESS[$strName.'picture'] = 'Логотип';
	$MESS[$strHint.'picture'] = 'Ссылка на логотип курса.';
$MESS[$strName.'description'] = 'Описание курса';
	$MESS[$strHint.'description'] = 'Описание курса.';


// Fields: Additional
$MESS[$strHead.'HEADER_ADDITIONAL'] = 'Дополнительная информация';

// Params
$MESS[$strLang.'PARAM_NAME_'.'additional_category'] = 'Дополнительная категория';
	$MESS[$strLang.'PARAM_HINT_'.'additional_category'] = 'Идентификатор дополнительной категории курса из общего рубрикатора курсов, если он нужен.';
$MESS[$strLang.'PARAM_NAME_'.'content_url'] = 'Ссылка на контент курса';
	$MESS[$strLang.'PARAM_HINT_'.'content_url'] = 'URL-адрес внутренней страницы курса, на которой идет обучение. Может совпадать с <code>url</code>. Ссылка нужна для оценки качества курсов при их ранжировании в результатах поиска.<br/><br/>
	Например, <code>url</code> курса равен https://example.com/courses/python-beginners. После покупки курса пользователь проходит обучение на странице https://example.com/education/python-beginners/home. Этот URL нужно указать в <code>param name="Ссылка на контент курса"</code>.<br/><br/>
	Если у курса много внутренних страниц, то можно указать множество значений <code>param name="Ссылка на контент курса"</code>.';
$MESS[$strLang.'PARAM_NAME_'.'discount_price'] = 'Цена по скидке';
	$MESS[$strLang.'PARAM_HINT_'.'discount_price'] = 'Число, указывающее актуальную цену курса с учётом скидки.';
$MESS[$strLang.'PARAM_NAME_'.'discount_last_date'] = 'Дата окончания скидки';
	$MESS[$strLang.'PARAM_HINT_'.'discount_last_date'] = 'Дата в формате <a href="https://ru.wikipedia.org/wiki/ISO_8601" target="_blank">ISO 8601</a>.';
$MESS[$strLang.'PARAM_NAME_'.'subscription_price'] = 'Цена за подписку';
	$MESS[$strLang.'PARAM_HINT_'.'subscription_price'] = '<code>true</code> или <code>false</code> (по умолчанию). Укажите <code>true</code>, если курс доступен по подписке. Цена подписка должна быть указана в элементе <code>price</code>.';
$MESS[$strLang.'PARAM_NAME_'.'installment_payment'] = 'Оплата в рассрочку';
	$MESS[$strLang.'PARAM_HINT_'.'installment_payment'] = 'Число. Указывает период рассрочки, если она есть. По умолчанию значение параметра указывается в месяцах. С помощью атрибута <code>unit</code> вы можете указать: день или месяц.';
$MESS[$strLang.'PARAM_NAME_'.'installment_payment@unit'] = 'Оплата в рассрочку (unit)';
	$MESS[$strLang.'PARAM_HINT_'.'installment_payment@unit'] = 'Укажите тип периода рассрочки: день или месяц.';
$MESS[$strLang.'PARAM_NAME_'.'monthly_price'] = 'Ежемесячная цена';
	$MESS[$strLang.'PARAM_HINT_'.'monthly_price'] = 'Число. Должно быть заполнено только для указания ежемесячной оплаты курса, и не допускается указание стоимости оплаты в месяц в рассрочку. Наличие рассрочки указывается через стоимость всего курса в элементе <code>price</code>, а периода рассрочки — в значении <code>param name="Оплата в рассрочку"</code>.';
$MESS[$strLang.'PARAM_NAME_'.'monthly_discount_price'] = 'Ежемесячная цена по скидке';
	$MESS[$strLang.'PARAM_HINT_'.'monthly_discount_price'] = 'Число. Укажите наличие скидки для ежемесячной оплаты курса. Также не допускается использование скидки на стоимость оплаты в месяц в рассрочку.';
$MESS[$strLang.'PARAM_NAME_'.'monthly_discount_last_date'] = 'Дата окончания ежемесячной скидки';
	$MESS[$strLang.'PARAM_HINT_'.'monthly_discount_last_date'] = 'Дата в формате <a href="https://ru.wikipedia.org/wiki/ISO_8601" target="_blank">ISO 8601</a>.';
$MESS[$strLang.'PARAM_NAME_'.'nearest_date'] = 'Ближайшая дата';
	$MESS[$strLang.'PARAM_HINT_'.'nearest_date'] = 'Дата в формате <a href="https://ru.wikipedia.org/wiki/ISO_8601" target="_blank">ISO 8601</a>. Значение обязательно для форматов обучения: самостоятельно с наставником и в группе с наставником.';
$MESS[$strLang.'PARAM_NAME_'.'duration'] = 'Продолжительность';
	$MESS[$strLang.'PARAM_HINT_'.'duration'] = 'Число. С помощью атрибута <code>unit="единица"</code> вы можете указать: час, день, месяц.';
$MESS[$strLang.'PARAM_NAME_'.'duration@unit'] = 'Продолжительность (unit)';
	$MESS[$strLang.'PARAM_HINT_'.'duration@unit'] = 'Укажите тип продолжительности - час, день, месяц.';
$MESS[$strLang.'PARAM_NAME_'.'plan'] = 'План';
	$MESS[$strLang.'PARAM_HINT_'.'plan'] = 'Строка. Описывает этапы программы обучения. <b>Элемент должен повторяться несколько раз, для курса должно быть указано 3 и больше элементов</b>. Если строк с <code>name="План"</code> меньше трех, то они будут игнорироваться.<br/><br/>
	Название этапа программы указывается в атрибуте <code>unit</code>. Длительность этапа в часах указывается в атрибуте <code>hours</code>. Значение <code>param</code> должно описывать содержание этапа программы.<br/><br/>
	Строки <code>param name="План"</code> обрабатываются в произвольном порядке, поэтому необходимо явно указать порядок этапов через атрибут <code>order</code>. Если текстовое значение содержимого этапа указывается через текстовые данные <code>CDATA</code>, то для отображения оно будет разбито на строки с применением функции <code>strip()</code>.';
$MESS[$strLang.'PARAM_NAME_'.'plan@unit'] = 'План (unit)';
$MESS[$strLang.'PARAM_NAME_'.'plan@hours'] = 'План (hours)';
$MESS[$strLang.'PARAM_NAME_'.'learning_format'] = 'Формат обучения';
$MESS[$strLang.'PARAM_HINT_'.'learning_format'] = 'Допустимые значения:
	<ul>
		<li>Самостоятельно (по умолчанию).</li>
		<li>Самостоятельно с наставником.</li>
		<li>В группе c наставником.</li>
		<li>С преподавателем.</li>
	</ul>';
$MESS[$strLang.'PARAM_NAME_'.'has_video_lessons'] = 'Есть видеоуроки';
	$MESS[$strLang.'PARAM_HINT_'.'has_video_lessons'] = '<code>true</code> или <code>false</code> (по умолчанию). Укажите список методов обучения.';
$MESS[$strLang.'PARAM_NAME_'.'has_text_lessons'] = 'Есть текстовые уроки';
	$MESS[$strLang.'PARAM_HINT_'.'has_text_lessons'] = '<code>true</code> или <code>false</code> (по умолчанию). Укажите список методов обучения.';
$MESS[$strLang.'PARAM_NAME_'.'has_webinars'] = 'Есть вебинары';
	$MESS[$strLang.'PARAM_HINT_'.'has_webinars'] = '<code>true</code> или <code>false</code> (по умолчанию). Укажите список методов обучения.';
$MESS[$strLang.'PARAM_NAME_'.'has_homework'] = 'Есть домашние работы';
	$MESS[$strLang.'PARAM_HINT_'.'has_homework'] = '<code>true</code> или <code>false</code> (по умолчанию). Укажите список методов обучения.';
$MESS[$strLang.'PARAM_NAME_'.'has_simulators'] = 'Есть тренажеры';
	$MESS[$strLang.'PARAM_HINT_'.'has_simulators'] = '<code>true</code> или <code>false</code> (по умолчанию). Укажите список методов обучения.';
$MESS[$strLang.'PARAM_NAME_'.'has_community'] = 'Есть сообщество';
	$MESS[$strLang.'PARAM_HINT_'.'has_community'] = '<code>true</code> или <code>false</code> (по умолчанию). Укажите список методов обучения.';
$MESS[$strLang.'PARAM_NAME_'.'difficulty'] = 'Сложность';
	$MESS[$strLang.'PARAM_HINT_'.'difficulty'] = 'Допустимые значения:
	<ul>
		<li>Для новичков (по умолчанию).</li>
		<li>Для опытных.</li>
	</ul>';
$MESS[$strLang.'PARAM_NAME_'.'training_type'] = 'Тип обучения';
	$MESS[$strLang.'PARAM_HINT_'.'training_type'] = 'Допустимые значения:
	<ul>
		<li>Курс (по умолчанию).</li>
		<li>Профессия.</li>
	</ul>';
$MESS[$strLang.'PARAM_NAME_'.'has_free_part'] = 'Есть бесплатная часть';
	$MESS[$strLang.'PARAM_HINT_'.'has_free_part'] = '<code>true</code> или <code>false</code> (по умолчанию).';
$MESS[$strLang.'PARAM_NAME_'.'has_employment'] = 'С трудоустройством';
	$MESS[$strLang.'PARAM_HINT_'.'has_employment'] = '<code>true</code> или <code>false</code> (по умолчанию). По результатам курса есть помощь от площадки по трудоустройству. Должно быть использовано только для типа обучения профессия.';
$MESS[$strLang.'PARAM_NAME_'.'learning_result'] = 'Результат обучения';
	$MESS[$strLang.'PARAM_HINT_'.'learning_result'] = 'Допустимые значения:
	<ul>
	<li>Сертификат.</li>
	<li>Диплом.</li>
	<li>Удостоверение.</li>
	</ul>';
$MESS[$strLang.'PARAM_NAME_'.'hours_per_week'] = 'Часы в неделю';
	$MESS[$strLang.'PARAM_HINT_'.'hours_per_week'] = 'Число. Ожидаемая интенсивность занятий.';
$MESS[$strLang.'PARAM_NAME_'.'classes'] = 'Классы';
	$MESS[$strLang.'PARAM_HINT_'.'classes'] = 'Строка. Список классов, для которых предназначен курс. Используется для курсов школьного образования. Можно указать классы через запятую и диапазоны классов. Например, 1,2,5-7,9.';
