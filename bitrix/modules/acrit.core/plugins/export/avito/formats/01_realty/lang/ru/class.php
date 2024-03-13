<?
$strMessPrefix = 'ACRIT_EXP_AVITO_REALTY_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Авито (Недвижимость)';

// Headers
$MESS[$strMessPrefix.'HEADER_LEASE'] = 'Характеристики для аренды';
$MESS[$strMessPrefix.'HEADER_ADDITIONAL'] = 'Дополнительные характеристики';

// Fields
$MESS[$strMessPrefix.'FIELD_STREET_NAME'] = 'Адрес объекта объявления';
	$MESS[$strMessPrefix.'FIELD_STREET_DESC'] = 'Адрес объекта объявления — строка до 256 символов, содержащая:<br/>
Место осмотра — строка до 256 символов, содержащая:
<ul>
	<li>название улицы и номер дома — если задан точный населенный пункт из <a href="http://autoload.avito.ru/format/Locations.xml" target="_blank">справочника</a>;</li>
	<li>если нужного населенного пункта нет в справочнике, то в этом элементе нужно указать:
		<ul>
			<li>район региона (если есть),</li>
			<li>населенный пункт (обязательно),</li>
			<li>улицу и номер дома, например для Тамбовской обл.: "Моршанский р-н, с. Устьи, ул. Лесная, д. 7".</li>
		</ul>
	</li>
</ul>
Примечания:<br/>
<ul>
	<li>элемент является устаревшим, рекомендуется использовать элемент "Address";</li>
	<li>для квартир-новостроек при указании NewDevelopmentId поле Street не обязательно, т. к. значение берется из внутреннего справочника Авито и не может быть переопределено;</li>
	<li>для того, чтобы ваш объект мог полноценно отображаться в поиске на карте, необходимо:
		<ul>
			<li>указать его точный адрес, известный <a href="https://yandex.ru/maps/" target="_blank">Яндекс.Картам</a>,</li>
			<li>или задать географические координаты (см. ниже).</li>
		</ul>
	</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LATITUDE_NAME'] = 'Географическая широта';
	$MESS[$strMessPrefix.'FIELD_LATITUDE_DESC'] = 'Географическая широта объекта (для указания точки на карте), <a href="https://ru.wikipedia.org/wiki/%D0%93%D0%B5%D0%BE%D0%B3%D1%80%D0%B0%D1%84%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%B5_%D0%BA%D0%BE%D0%BE%D1%80%D0%B4%D0%B8%D0%BD%D0%B0%D1%82%D1%8B#.D0.A4.D0.BE.D1.80.D0.BC.D0.B0.D1.82.D1.8B_.D0.B7.D0.B0.D0.BF.D0.B8.D1.81.D0.B8_.D0.B3.D0.B5.D0.BE.D0.B3.D1.80.D0.B0.D1.84.D0.B8.D1.87.D0.B5.D1.81.D0.BA.D0.B8.D1.85_.D0.BA.D0.BE.D0.BE.D1.80.D0.B4.D0.B8.D0.BD.D0.B0.D1.82" target="_blank">в градусах — десятичные дроби</a>.<br/><br/>
Примечания:<br/>
<ul>
	<li>если координаты указаны, определение геопозиции будет осуществлено по ним, а элемент Address будет проигнорирован.</li>
	<li>если координаты не заданы, то Авито попытается поставить точку на карту автоматически, определив местоположение объекта по значениям полей "City" и "Street";</li>
	<li>для квартир-новостроек с NewDevelopmentId координаты берутся из внутреннего справочника Авито и не могут быть переопределены,</li>
	<li>элементы Latitude и Longitude являются необязательными, но если они указаны, определение геопозиции будет осуществлено по ним, а элемент Address будет проигнорирован.</li>
</ul>
<b>Внимание!</b> С 28.10.2019 не будет осуществляться определение геопозиции по элементам Region, City, District, Subway, Street. Для определения геопозиции используйте обязательный элемент Address. С 25.11.2019 элементы Region, City, District, Subway, Street перестанут поддерживаться в XML-файле.';
$MESS[$strMessPrefix.'FIELD_LONGITUDE_NAME'] = 'Географическая долгота';
	$MESS[$strMessPrefix.'FIELD_LONGITUDE_DESC'] = 'Географическая долгота объекта (для указания точки на карте), <a href="https://ru.wikipedia.org/wiki/%D0%93%D0%B5%D0%BE%D0%B3%D1%80%D0%B0%D1%84%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%B5_%D0%BA%D0%BE%D0%BE%D1%80%D0%B4%D0%B8%D0%BD%D0%B0%D1%82%D1%8B#.D0.A4.D0.BE.D1.80.D0.BC.D0.B0.D1.82.D1.8B_.D0.B7.D0.B0.D0.BF.D0.B8.D1.81.D0.B8_.D0.B3.D0.B5.D0.BE.D0.B3.D1.80.D0.B0.D1.84.D0.B8.D1.87.D0.B5.D1.81.D0.BA.D0.B8.D1.85_.D0.BA.D0.BE.D0.BE.D1.80.D0.B4.D0.B8.D0.BD.D0.B0.D1.82" target="_blank">в градусах — десятичные дроби</a>.<br/><br/>
Примечания:<br/>
<ul>
	<li>если координаты указаны, определение геопозиции будет осуществлено по ним, а элемент Address будет проигнорирован.</li>
	<li>если координаты не заданы, то Авито попытается поставить точку на карту автоматически, определив местоположение объекта по значениям полей "City" и "Street";</li>
	<li>для квартир-новостроек с NewDevelopmentId координаты берутся из внутреннего справочника Авито и не могут быть переопределены,</li>
	<li>элементы Latitude и Longitude являются необязательными, но если они указаны, определение геопозиции будет осуществлено по ним, а элемент Address будет проигнорирован.</li>
</ul>
<b>Внимание!</b> С 28.10.2019 не будет осуществляться определение геопозиции по элементам Region, City, District, Subway, Street. Для определения геопозиции используйте обязательный элемент Address. С 25.11.2019 элементы Region, City, District, Subway, Street перестанут поддерживаться в XML-файле.';
$MESS[$strMessPrefix.'FIELD_DISTANCE_TO_CITY_NAME'] = 'Расстояние до города, в км';
	$MESS[$strMessPrefix.'FIELD_DISTANCE_TO_CITY_DESC'] = 'Расстояние до города, в км — целое число.<br/><br/>
Примечание: если объект находится в черте города, то:
<ul>
	<li>нужно указывать значение "0";</li>
	<li>если в городе есть метро, то нужно обязательно указать ближайшую станцию метро (поле Subway);</li>
	<li>если по <a href="http://autoload.avito.ru/format/Locations.xml" target="_blank">справочнику</a> локаций в городе есть районы, то нужно указать район в соответствии со значениями справочника (поле District).</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_DIRECTION_ROAD_NAME'] = 'Направление от города';
	$MESS[$strMessPrefix.'FIELD_DIRECTION_ROAD_DESC'] = 'Направление от города — в соответствии со значениями <a href="http://autoload.avito.ru/format/Locations.xml" target="_blank">справочника</a>.<br/><br/>
<b>Обязательно, если в справочнике для города указаны направления</b>.<br/><br>
<b>Обязательно для объектов не в черте города</b>.';
#
$MESS[$strMessPrefix.'FIELD_CATEGORY_NAME'] = 'Категория объекта недвижимости';
	$MESS[$strMessPrefix.'FIELD_CATEGORY_DESC'] = 'Категория объекта недвижимости — одно из значений списка:<br/>
<ul>
	<li>Квартиры,</li>
	<li>Комнаты,</li>
	<li>Дома, дачи, коттеджи,</li>
	<li>Земельные участки,</li>
	<li>Гаражи и машиноместа,</li>
	<li>Коммерческая недвижимость,</li>
	<li>Недвижимость за рубежом.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_OPERATION_TYPE_NAME'] = 'Тип объявления';
	$MESS[$strMessPrefix.'FIELD_OPERATION_TYPE_DESC'] = 'Тип объявления — одно из значений списка:
<ul>
	<li>Продам,</li>
	<li>Сдам.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_COUNTRY_NAME'] = 'Страна';
	$MESS[$strMessPrefix.'FIELD_COUNTRY_DESC'] = 'Страна, в которой находится объект объявления — в соответствии со значениями из <a href="http://autoload.avito.ru/format/Countries.xml" target="_blank">справочника</a>.';
$MESS[$strMessPrefix.'FIELD_TITLE_NAME'] = 'Название объявления (только для коммерческой недвижимости)';
	$MESS[$strMessPrefix.'FIELD_TITLE_DESC'] = 'Названия объявлений формируются автоматически, исходя из выбранных параметров объекта.<br/><br/>
Только в категории «Коммерческая недвижимость» заголовок можно задать самостоятельно. В заголовке необходимо указывать только вид объекта и основные параметры. Указание цены, слов, привлекающих внимание, контактной информации, адреса сайта или только слова «продам / куплю» нарушает <a href="https://support.avito.ru/hc/ru/articles/200026888" target="_blank">правила Авито</a>.';
$MESS[$strMessPrefix.'FIELD_PRICE_NAME'] = 'Цена в рублях';
	$MESS[$strMessPrefix.'FIELD_PRICE_DESC'] = 'Цена в рублях в зависимости от типа объявления — целое число:
<ul>
	<li>Продам — руб. за всё;</li>
	<li>Сдам — в зависимости от срока аренды:
		<ul>
			<li>На длительный срок — руб. в месяц за весь объект;</li>
			<li>Посуточно — руб. за сутки.</li>
		</ul>
</ul>';
$MESS[$strMessPrefix.'FIELD_PRICE_TYPE_NAME'] = 'Вариант задания цены';
	$MESS[$strMessPrefix.'FIELD_PRICE_TYPE_DESC'] = 'Вариант задания цены — одно из значений списка:<br/>
<ul>
<li>Продам — руб.;
	<ul>
		<li>за всё — значение по умолчанию,</li>
		<li>за м<sup>2</sup></li>
	</ul>
<li>Сдам — руб.:
	<ul>
		<li>в месяц — значение по умолчанию,</li>
		<li>в месяц за м<sup>2</sup>,</li>
		<li>в год,</li>
		<li>в год за м<sup>2</sup>.</li>
	</ul>
</ul>
';
$MESS[$strMessPrefix.'FIELD_ROOMS_NAME'] = 'Количество комнат в квартире';
	$MESS[$strMessPrefix.'FIELD_ROOMS_DESC'] = 'Количество комнат в квартире — целое число или текст "Студия".';
$MESS[$strMessPrefix.'FIELD_SQUARE_NAME'] = 'Общая площадь объекта';
	$MESS[$strMessPrefix.'FIELD_SQUARE_DESC'] = 'Общая площадь объекта недвижимости, выставленная на продажу, в кв. метрах — десятичное число.<br/><br/>
Примечание: для категории "Дома, дачи, коттеджи" здесь нужно указывать площадь дома, площадь участка указывается в поле LandArea.';
$MESS[$strMessPrefix.'FIELD_KITCHEN_SPACE_NAME'] = 'Площадь кухни';
	$MESS[$strMessPrefix.'FIELD_KITCHEN_SPACE_DESC'] = 'Площадь кухни, в кв. метрах — десятичное число.';
$MESS[$strMessPrefix.'FIELD_LIVING_SPACE_NAME'] = 'Жилая площадь';
	$MESS[$strMessPrefix.'FIELD_LIVING_SPACE_DESC'] = 'Жилая площадь, в кв. метрах — десятичное число.';
$MESS[$strMessPrefix.'FIELD_LAND_AREA_NAME'] = 'Площадь участка';
	$MESS[$strMessPrefix.'FIELD_LAND_AREA_DESC'] = 'Площадь участка, в сотках — десятичное число.';
$MESS[$strMessPrefix.'FIELD_FLOOR_NAME'] = 'Этаж';
	$MESS[$strMessPrefix.'FIELD_FLOOR_DESC'] = 'Этаж, на котором находится объект — целое число.';
$MESS[$strMessPrefix.'FIELD_FLOORS_NAME'] = 'Количество этажей в доме';
	$MESS[$strMessPrefix.'FIELD_FLOORS_DESC'] = 'Количество этажей в доме — целое число.';
$MESS[$strMessPrefix.'FIELD_HOUSE_TYPE_NAME'] = 'Тип дома';
	$MESS[$strMessPrefix.'FIELD_HOUSE_TYPE_DESC'] = 'Тип дома — одно из значений списка:
<ul>
	<li>Кирпичный,</li>
	<li>Панельный,</li>
	<li>Блочный,</li>
	<li>Монолитный,</li>
	<li>Деревянный.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_WALLS_TYPE_NAME'] = 'Материал стен';
	$MESS[$strMessPrefix.'FIELD_WALLS_TYPE_DESC'] = 'Материал стен — одно из значений списка:
<ul>
	<li>Кирпич,</li>
	<li>Брус,</li>
	<li>Бревно,</li>
	<li>Газоблоки,</li>
	<li>Металл,</li>
	<li>Пеноблоки,</li>
	<li>Сэндвич-панели,</li>
	<li>Ж/б панели,</li>
	<li>Экспериментальные материалы.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_MARKET_TYPE_NAME'] = 'Вторичка/новостройка';
	$MESS[$strMessPrefix.'FIELD_MARKET_TYPE_DESC'] = 'Принадлежность квартиры к рынку — одно из значений списка:
<ul>
	<li>Вторичка,</li>
	<li>Новостройка.</li>
</ul>
<b>Обязательно для типа (OperationType) "Продам"</b>.';
$MESS[$strMessPrefix.'FIELD_NEW_DEVELOPMENT_ID_NAME'] = 'Объект новостройки';
	$MESS[$strMessPrefix.'FIELD_NEW_DEVELOPMENT_ID_DESC'] = 'Объект новостройки — ID объекта из <a href="https://autoload.avito.ru/format/New_developments.xml" target="_blank">XML-справочника</a>:
<ul>
	<li>если в жилом комплексе новостроек есть корпуса, то обязательно ID корпуса (элементы Housing);</li>
	<li>если корпусов нет, то ID жилого комплекса (элементы Object).</li>
</ul>
Если задан элемент NewDevelopmentId, то значения поля Street и географических координат берутся из внутреннего справочника Авито.<br/><br/>
Важно: Если в нашем справочнике нет нужного вам объекта или вы нашли в нем ошибку, то сообщайте по адресу <a href="mailto:newdevelopments@avito.ru">newdevelopments@avito.ru</a> с указанием ссылки на сайт, где есть проектная документация:<br/>
<ul>
	<li>для ДДУ: разрешение на строительство дома, заключение строительного надзора о соответствии застройщика и проектной декларации 214-ФЗ (для проектов начатых после 01.01.2017), проектная декларация;</li>
	<li>для ЖСК: разрешение на строительство дома и устав кооператива).</li>
</ul>
<b>Обязательно для типа "Новостройка"</b>.';
$MESS[$strMessPrefix.'FIELD_PROPERTY_RIGHTS_NAME'] = 'Право собственности';
	$MESS[$strMessPrefix.'FIELD_PROPERTY_RIGHTS_DESC'] = 'Право собственности — одно из значений списка:
<ul>
	<li>Собственник;</li>
	<li>Посредник;</li>
	<li>Застройщик (доступно только в категории "Квартиры. Продам. Новостройка").</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_OBJECT_TYPE_NAME'] = 'Вид объекта';
	$MESS[$strMessPrefix.'FIELD_OBJECT_TYPE_DESC'] = 'Вид объекта — одно из значений списка (отдельно для каждой категории):<br/>
<ul>
	<li>
		<b>Дома, дачи, коттеджи:</b>
		<ul>
			<li>Дом,</li>
			<li>Дача,</li>
			<li>Коттедж,</li>
			<li>Таунхаус;</li>
		</ul>
	</li>
	<li>
		<b>Земельные участки:</b>
		<ul>
			<li>Поселений (ИЖС),</li>
			<li>Сельхозназначения (СНТ, ДНП),</li>
			<li>Промназначения;</li>
		</ul>
	</li>
	<li>
		<b>Гаражи и машиноместа:</b>
		<ul>
			<li>Гараж,</li>
			<li>Машиноместо;</li>
		</ul>
	</li>
	<li>
		<b>Коммерческая недвижимость:</b>
		<ul>
			<li>Гостиница,</li>
			<li>Офисное помещение,</li>
			<li>Помещение общественного питания,</li>
			<li>Помещение свободного назначения,</li>
			<li>Производственное помещение,</li>
			<li>Складское помещение</li>
			<li>Торговое помещение;</li>
		</ul>
	</li>
	<li>
		<b>Недвижимость за рубежом:</b>
		<ul>
			<li>Квартира, апартаменты,</li>
			<li>Дом, вилла,</li>
			<li>Земельный участок,</li>
			<li>Гараж, машиноместо,</li>
			<li>Коммерческая недвижимость.</li>
		</ul>
	</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_ADDITIONAL_OBJECT_TYPES_NAME'] = 'Вид объекта - дополнительные варианты';
	$MESS[$strMessPrefix.'FIELD_ADDITIONAL_OBJECT_TYPES_DESC'] = 'Вид объекта - дополнительные варианты. Возможные значения (не более 2) в зависимости от основного:
<ul>
	<li>
		Помещение свободного назначения
		<ul>
			<li>Офис</li>
			<li>Торговое помещение</li>
			<li>Производство</li>
			<li>Склад</li>
			<li>Общепит</li>
			<li>Гостиница</li>
		</ul>
	</li>
	<li>
		Торговое помещение
		<ul>
			<li>Офис</li>
			<li>Общепит</li>
		</ul>
	</li>
	<li>
		Офисное помещение
		<ul>
			<li>Торговое помещение</li>
		</ul>
	</li>
	<li>
		Складское помещение
		<ul>
			<li>Производство</li>
		</ul>
	</li>
	<li>
		Производственное помещение
		<ul>
			<li>Склад</li>
		</ul>
	</li>
</ul>
<b>3й и последующие указанные значения будут проигнорированы.</b>';
$MESS[$strMessPrefix.'FIELD_OBJECT_SUBTYPE_NAME'] = 'Подвид объекта';
	$MESS[$strMessPrefix.'FIELD_OBJECT_SUBTYPE_DESC'] = 'Подвид объекта — одно из значений списка (отдельно для каждого типа):<br/>
<ul>
	<li>
		<b>Гараж:</b>
		<ul>
			<li>Железобетонный,</li>
			<li>Кирпичный,</li>
			<li>Металлический;</li>
		</ul>
	</li>
	<li>
		<b>Машиноместо:</b>
		<ul>
			<li>Многоуровневый паркинг,</li>
			<li>Подземный паркинг,</li>
			<li>Крытая стоянка,</li>
			<li>Открытая стоянка.</li>
		</ul>
	</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_SECURED_NAME'] = 'Охрана объекта';
	$MESS[$strMessPrefix.'FIELD_SECURED_DESC'] = 'Охрана объекта — одно из значений списка:<br/>
<ul>
	<li>Да,</li>
	<li>Нет.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_BUILDING_CLASS_NAME'] = 'Класс здания';
	$MESS[$strMessPrefix.'FIELD_BUILDING_CLASS_DESC'] = 'Класс здания (только для видов объекта "Офисное помещение" и "Складское помещение") — одно из значений списка:
<ul>
	<li>A,</li>
	<li>B,</li>
	<li>C,</li>
	<li>D.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_CADASTRAL_NUMBER_NAME'] = 'Кадастровый номер';
	$MESS[$strMessPrefix.'FIELD_CADASTRAL_NUMBER_DESC'] = 'Кадастровый номер — строка.<br/><br/>
Примечание: не показывается в объявлении полностью.';
$MESS[$strMessPrefix.'FIELD_DECORATION_NAME'] = 'Отделка помещения';
	$MESS[$strMessPrefix.'FIELD_DECORATION_DESC'] = 'Отделка помещения (только для типов объекта (MarketType) "Новостройка"). Возможные значения параметра:
<ul>
	<li>"Без отделки"</li>
	<li>"Черновая"</li>
	<li>"Чистовая"</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_SAFE_DEMONSTRATION_NAME'] = 'Онлайн показ';
	$MESS[$strMessPrefix.'FIELD_SAFE_DEMONSTRATION_DESC'] = 'Онлайн показ — одно из значений списка:
<ul>
	<li>Могу провести</li>
	<li>Не хочу</li>
</ul>
<b>Важно</b>: Данный элемент не поддерживается в микрокатегориях Аренда/Посуточно (для всех указанных категорий) и Продажа/Новостройка (для категории Квартиры).';
$MESS[$strMessPrefix.'FIELD_APARTMENT_NUMBER_NAME'] = 'Номер квартиры';
	$MESS[$strMessPrefix.'FIELD_APARTMENT_NUMBER_DESC'] = 'Номер квартиры - строка, содержащая от 1 до 10 символов.';
$MESS[$strMessPrefix.'FIELD_STATUS_NAME'] = 'Статус недвижимости';
	$MESS[$strMessPrefix.'FIELD_STATUS_DESC'] = 'Статус недвижимости — одно из значений списка:
<ul>
	<li>Квартира</li>
	<li>Апартаменты</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_BALCONY_OR_LOGGIA_NAME'] = 'Балкон или лоджия';
	$MESS[$strMessPrefix.'FIELD_BALCONY_OR_LOGGIA_DESC'] = 'Балкон или лоджия — одно из значений списка:
<ul>
	<li>Балкон</li>
	<li>Лоджия</li>
	<li>Нет</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_BALCONY_OR_LOGGIA_MULTI_NAME'] = 'Балкон или лоджия';
	$MESS[$strMessPrefix.'FIELD_BALCONY_OR_LOGGIA_MULTI_DESC'] = 'Балкон или лоджия — одно из значений списка:
<ul>
	<li>Балкон</li>
	<li>Лоджия</li>
	<li>Нет</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_VIEW_FROM_WINDOWS_NAME'] = 'Вид из окон';
	$MESS[$strMessPrefix.'FIELD_VIEW_FROM_WINDOWS_DESC'] = 'Вид из окон — одно или более значения из списка:
<ul>
	<li>На улицу</li>
	<li>Во двор</li>
</ul>
Поле должно выгружаться как множественное!';
$MESS[$strMessPrefix.'FIELD_BUILT_YEAR_NAME'] = 'Год постройки';
	$MESS[$strMessPrefix.'FIELD_BUILT_YEAR_DESC'] = 'Год постройки (только для типов объекта (MarketType) "Вторичка") -  целое число.';
$MESS[$strMessPrefix.'FIELD_PASSENGER_ELEVATOR_NAME'] = 'Пассажирский лифт';
	$MESS[$strMessPrefix.'FIELD_PASSENGER_ELEVATOR_DESC'] = 'Пассажирский лифт — одно из значений списка:
<ul>
	<li>Нет</li>
	<li>1</li>
	<li>2</li>
	<li>3</li>
	<li>4</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_FREIGHT_ELEVATOR_NAME'] = 'Грузовой лифт';
	$MESS[$strMessPrefix.'FIELD_FREIGHT_ELEVATOR_DESC'] = 'Пассажирский лифт — одно из значений списка:
<ul>
	<li>Нет</li>
	<li>1</li>
	<li>2</li>
	<li>3</li>
	<li>4</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_IN_HOUSE_NAME'] = 'В доме';
	$MESS[$strMessPrefix.'FIELD_IN_HOUSE_DESC'] = 'В доме (только для типов объекта (MarketType) "Вторичка") - вложенные элементы с возможными значениями из списка:
<ul>
	<li>Консьерж</li>
	<li>Мусоропровод</li>
	<li>Газоснабжение</li>
</ul>
Поле должно выгружаться как множественное!';
$MESS[$strMessPrefix.'FIELD_COURTYARD_NAME'] = 'Двор';
	$MESS[$strMessPrefix.'FIELD_COURTYARD_DESC'] = 'Двор - вложенные элементы с возможными значениями из списка:
<ul>
	<li>Закрытая территория</li>
	<li>Детская площадка</li>
	<li>Спортивная площадка</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_PARKING_NAME'] = 'Парковка';
	$MESS[$strMessPrefix.'FIELD_PARKING_DESC'] = 'Парковка - вложенные элементы с возможными значениями из списка:
<ul>
	<li>Подземная</li>
	<li>Наземная многоуровневая</li>
	<li>За шлагбаумом во дворе</li>
	<li>Открытая во дворе</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_CEILING_HEIGHT_NAME'] = 'Высота потолков, м';
	$MESS[$strMessPrefix.'FIELD_CEILING_HEIGHT_DESC'] = 'Высота потолков, в метрах  — десятичное число.';
$MESS[$strMessPrefix.'FIELD_RENOVATION_NAME'] = 'Ремонт';
	$MESS[$strMessPrefix.'FIELD_RENOVATION_DESC'] = 'Ремонт (только для типов объекта (MarketType) "Вторичка")  — одно из значений списка:
<ul>
	<li>Косметический</li>
	<li>Евро</li>
	<li>Дизайнерский</li>
	<li>Требует ремонта</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_BATHROOM_NAME'] = 'Санузел';
	$MESS[$strMessPrefix.'FIELD_BATHROOM_DESC'] = 'Санузел — одно из значений списка:
<ul>
	<li>Совмещенный</li>
	<li>Раздельный</li>
	<li>Несколько</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_BATHROOM_MULTI_NAME'] = 'Санузел';
	$MESS[$strMessPrefix.'FIELD_BATHROOM_MULTI_DESC'] = 'Санузел - элемент с возможными значениями из списка:
<ul>
	<li>
		для объектов в категории "Квартиры" со сроком аренды (LeaseType) "На длительный срок",
		для объектов в категории "Квартиры" с типом объявления (OperationType) "Продам":
		<ul>
			<li>Совмещённый</li>
			<li>Раздельный</li>
		</ul>
		для объектов в категории "Дома, дачи, коттеджи":
		<ul>
			<li>В доме</li>
			<li>На улице</li>
		</ul>
	</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_SSADDITIONALLY_NAME'] = 'Дополнительно (для вторички)';
	$MESS[$strMessPrefix.'FIELD_SSADDITIONALLY_DESC'] = 'Дополнительно (только для типов объекта (MarketType) "Вторичка") - вложенные элементы с возможными значениями из списка:
<ul>
	<li>Мебель</li>
	<li>Бытовая техника</li>
	<li>Кондиционер</li>
	<li>Гардеробная</li>
	<li>Панорамные окна</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_NDADDITIONALLY_NAME'] = 'Дополнительно (для новостроек)';
	$MESS[$strMessPrefix.'FIELD_NDADDITIONALLY_DESC'] = 'Дополнительно (только для типов объекта (MarketType) "Новостройка") - вложенные элементы с возможными значениями из списка:
<ul>
	<li>Гардеробная</li>
	<li>Панорамные окна</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_DEALTYPE_NAME'] = 'Тип сделки';
	$MESS[$strMessPrefix.'FIELD_DEALTYPE_DESC'] = 'Тип сделки — одно из значений списка:
<ul>
	<li>Прямая продажа</li>
	<li>Альтернативная</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_ROOMTYPE_NAME'] = 'Тип комнат';
	$MESS[$strMessPrefix.'FIELD_ROOMTYPE_DESC'] = 'Тип комнат - вложенные элементы с возможными значениями из списка:
<ul>
	<li>Смежные</li>
	<li>Изолированные</li>
</ul>';


$MESS[$strMessPrefix.'FIELD_LEASE_TYPE_NAME'] = 'Тип аренды';
	$MESS[$strMessPrefix.'FIELD_LEASE_TYPE_DESC'] = 'Тип аренды — одно из значений списка:<br/>
<ul>
	<li>На длительный срок,</li>
	<li>Посуточно.</li>
</ul>
<b>Обязательно для типа "Сдам"</b>.';
$MESS[$strMessPrefix.'FIELD_LEASE_BEDS_NAME'] = 'Количество кроватей.';
	$MESS[$strMessPrefix.'FIELD_LEASE_BEDS_DESC'] = 'Количество кроватей (только для аренды) — целое число.';
$MESS[$strMessPrefix.'FIELD_LEASE_SLEEPING_PLACES_NAME'] = 'Количество спальных мест';
	$MESS[$strMessPrefix.'FIELD_LEASE_SLEEPING_PLACES_DESC'] = 'Количество спальных мест (только для аренды) — целое число.';
$MESS[$strMessPrefix.'FIELD_LEASE_MULTIMEDIA_NAME'] = 'Опции "Мультимедиа"';
	$MESS[$strMessPrefix.'FIELD_LEASE_MULTIMEDIA_DESC'] = 'Опции "Мультимедиа" (только для аренды) — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Wi-Fi,</li>
	<li>Телевизор,</li>
	<li>Кабельное / цифровое ТВ.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LEASE_APPLIANCES_NAME'] = 'Опции "Бытовая техника"';
	$MESS[$strMessPrefix.'FIELD_LEASE_APPLIANCES_DESC'] = 'Опции "Бытовая техника" (только для аренды) — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Плита,</li>
	<li>Микроволновка,</li>
	<li>Холодильник,</li>
	<li>Стиральная машина,</li>
	<li>Фен,</li>
	<li>Утюг.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LEASE_COMFORT_NAME'] = 'Опции "Комфорт"';
	$MESS[$strMessPrefix.'FIELD_LEASE_COMFORT_DESC'] = 'Опции "Комфорт" (только для аренды) — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Кондиционер,</li>
	<li>Камин,</li>
	<li>только в категориях "Квартиры" и "Комнаты":<br/>
		<ul>
			<li>Балкон / лоджия,</li>
			<li>Парковочное место;</li>
		</ul>
	</li>
	<li>только в категории "Дома, дачи, коттеджи":<br/>
		<ul>
			<li>Бассейн,</li>
			<li>Баня / сауна.</li>
		</ul>
	</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LEASE_ADDITIONALLY_NAME'] = 'Опции "Дополнительно"';
	$MESS[$strMessPrefix.'FIELD_LEASE_ADDITIONALLY_DESC'] = 'Опции "Дополнительно" (только для аренды) — вложенные элементы &lt;Option&gt; с возможными значениями из списка:<br/>
<ul>
	<li>Можно с питомцами,</li>
	<li>Можно с детьми,</li>
	<li>Можно для мероприятий,</li>
	<li>Можно курить.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LEASE_COMMISSION_SIZE_NAME'] = 'Размер комиссии в %';
	$MESS[$strMessPrefix.'FIELD_LEASE_COMMISSION_SIZE_DESC'] = 'Размер комиссии в % — целое число.<br/><br/>
	<b>Обязательно для долгосрочной аренды в случае права собственности "Посредник"</b>.';
$MESS[$strMessPrefix.'FIELD_LEASE_DEPOSIT_NAME'] = 'Залог';
	$MESS[$strMessPrefix.'FIELD_LEASE_DEPOSIT_DESC'] = 'Залог — одно из значений списка:
<ul>
	<li>Без залога,</li>
	<li>0,5 месяца,</li>
	<li>1 месяц,</li>
	<li>1,5 месяца,</li>
	<li>2 месяца,</li>
	<li>2,5 месяца,</li>
	<li>3 месяца.</li>
</ul>
<b>Обязательно для долгосрочной аренды</b>.';

#
$MESS[$strMessPrefix.'FIELD_LAND_ADDITIONALLY_NAME'] = 'Дополнительно (на участке)';
	$MESS[$strMessPrefix.'FIELD_LAND_ADDITIONALLY_DESC'] = '<p>Дополнительно (на участке)&nbsp;- элемент с возможными значениями из&nbsp;списка:<br></p><ul><li>Баня или сауна<br></li><li>Бассейн</li></ul><br/><br/>Актуально для категорий: <p>Дома, дачи, коттеджи</p>';
$MESS[$strMessPrefix.'FIELD_LAND_STATUS_NAME'] = 'Статус участка';
	$MESS[$strMessPrefix.'FIELD_LAND_STATUS_DESC'] = '<p>Статус участка&nbsp;— одно из значений списка:<br></p><ul><li>Индивидуальное жилищное строительство (ИЖС)</li><li>Садовое некоммерческое товарищество (СНТ)</li><li>Дачное некоммерческое партнёрство (ДНП)</li><li>Фермерское хозяйство</li></ul><br/><br/>Актуально для категорий: Дома, дачи, коттеджи/Продам*';
$MESS[$strMessPrefix.'FIELD_REPAIR_ADDITIONALLY_NAME'] = 'Ремонт дополнительные опции';
	$MESS[$strMessPrefix.'FIELD_REPAIR_ADDITIONALLY_DESC'] = '<p>Ремонт дополнительные опции&nbsp;(только для типов объекта (MarketType) "Вторичка") &nbsp;— элемент с возможными значениями из&nbsp;списка:<br></p><ul><li>Тёплый пол</li></ul><br/><br/>Актуально для категорий: Квартиры/Продам';
$MESS[$strMessPrefix.'FIELD_FURNITURE_NAME'] = 'Мебель';
	$MESS[$strMessPrefix.'FIELD_FURNITURE_DESC'] = '<p>Мебель&nbsp; — элемент с возможными значениями из&nbsp;списка</p><p>для типов объекта (MarketType) "Вторичка" в категории "Квартиры",<br>для типов объекта со сроком аренды (LeaseType)&nbsp;"На длительный срок" в категории "Квартиры",<br>для типов объекта со сроком аренды (LeaseType)&nbsp;"На длительный срок" в категории "Дома, дачи, коттеджи":<br></p><ul><li>Кухня</li><li>Шкафы</li><li>Спальные места</li></ul><p>Для категории "Недвижимость за рубежом" объектов с типом объекта ("ObjectType") "Квартира, апартаменты", "Дом, вилла":</p><ul><li>Кухня</li><li>Шкафы</li><li>Спальные места</li><li>Ванная</li></ul><br/><br/>Актуально для категорий: <ul><li>Квартиры</li><li>Дома, дачи, коттеджи/Сдам</li><li>Недвижимость за рубежом</li></ul>';
$MESS[$strMessPrefix.'FIELD_RENOVATION_PROGRAM_NAME'] = 'Реновация';
	$MESS[$strMessPrefix.'FIELD_RENOVATION_PROGRAM_DESC'] = '<p>Реновация&nbsp;(только для типов объекта (MarketType) "Вторичка") &nbsp;— элемент с возможными значениями из&nbsp;списка:<br></p><ul><li>Запланирован снос</li></ul><br/><br/>Актуально для категорий: <p>Квартиры/Продам</p>';
$MESS[$strMessPrefix.'FIELD_HOUSE_ADDITIONALLY_NAME'] = 'Дополнительно (в доме)';
	$MESS[$strMessPrefix.'FIELD_HOUSE_ADDITIONALLY_DESC'] = '<p>Дополнительно (в доме)&nbsp;— элемент с возможными значениями из&nbsp;списка:<br></p><ul><li>Терраса или веранда</li></ul><br/><br/>Актуально для категорий: Дома, дачи, коттеджи';
$MESS[$strMessPrefix.'FIELD_HOUSE_SERVICES_NAME'] = 'Коммуникации';
	$MESS[$strMessPrefix.'FIELD_HOUSE_SERVICES_DESC'] = '<p>Коммуникации — элемент с возможными значениями из&nbsp;списка:<br></p><ul><li>Электричество</li><li>Газ</li><li>Отопление</li><li>Канализация</li></ul><br/><br/>Актуально для категорий: Дома, дачи, коттеджи';
$MESS[$strMessPrefix.'FIELD_PARKING_TYPE_NAME'] = 'Тип парковки';
	$MESS[$strMessPrefix.'FIELD_PARKING_TYPE_DESC'] = '<p><strong>Внимание</strong>: рекомендуем заполнить этот элемент (ParkingType), поскольку&nbsp;<strong>c 31.05.22&nbsp;</strong><strong>он станет обязательным&nbsp;</strong>в&nbsp;категории&nbsp;"Коммерческая недвижимость" с видом объекта (ObjectType) "Офисное помещение", "Помещение свободного назначения", "Торговое помещение", "Помещение общественного питания", "Гостиница", "Здание".</p><p>Тип парковки -&nbsp;одно из значений списка:</p><p>для типов объекта со сроком аренды (LeaseType) "Посуточно" в категории "Квартиры", <br>для объектов в категории "Коммерческая недвижимость":</p><ul><li>Нет</li><li>На улице</li><li>В здании</li></ul><p>для объектов в категории "Дома, дачи, коттеджи":</p><ul><li>Нет</li><li>Гараж</li><li>Парковочное место</li></ul><br/><br/>Актуально для категорий: <ul><li>Квартиры/Сдам</li><li>Дома, дачи, коттеджи</li><li>Коммерческая недвижимость</li></ul>';
$MESS[$strMessPrefix.'FIELD_PARKING_ADDITIONALLY_NAME'] = 'Дополнительно о парковке';
	$MESS[$strMessPrefix.'FIELD_PARKING_ADDITIONALLY_DESC'] = '<p>Дополнительно о парковке (только для объектов с типом парковки (ParkingType) "На улице" или "В здании") - элемент с возможными значениями из списка: </p><p>для объектов со сроком аренды (LeaseType) "Посуточно" в категории "Квартиры":</p><ul><li>Бесплатная </li></ul><p>для объектов в категории "Коммерческая недвижимость":</p><ul><li>Бесплатная</li><li>Подходит для грузового транспорта</li></ul><br/><br/>Актуально для категорий: <ul><li>Квартиры/Сдам</li><li>Коммерческая недвижимость</li></ul>';
$MESS[$strMessPrefix.'FIELD_TRANSPORT_ACCESSIBILITY_NAME'] = 'Транспортная доступность';
	$MESS[$strMessPrefix.'FIELD_TRANSPORT_ACCESSIBILITY_DESC'] = '<p>Транспортная доступность&nbsp; — элемент с возможными значениями из&nbsp;списка:<br></p><ul><li>Асфальтированная дорога</li><li>Остановка общественного транспорта</li><li>Железнодорожная станция</li></ul><br/><br/>Актуально для категорий: Дома, дачи, коттеджи';
$MESS[$strMessPrefix.'FIELD_INFRASTRUCTURE_NAME'] = 'Инфраструктура';
	$MESS[$strMessPrefix.'FIELD_INFRASTRUCTURE_DESC'] = '<p>Инфраструктура&nbsp;— элемент с возможными значениями из&nbsp;списка:<br></p><p>для объектов со сроком аренды (LeaseType)&nbsp;"Посуточно" в категории "Дома, дачи, коттеджи"</p><ul><li>Магазин</li><li>Аптека</li></ul><p>для остальных объектов в&nbsp;категории "Дома, дачи, коттеджи"&nbsp;и в категории "Недвижимость за рубежом" (с типом объекта ("ObjectType") "Квартира, апартаменты", "Дом, вилла"):</p><ul><li>Магазин</li><li>Аптека</li><li>Детский сад</li><li>Школа</li></ul><br/><br/>Актуально для категорий: <ul><li>Дома, дачи, коттеджи</li><li>Недвижимость за рубежом</li></ul>';
$MESS[$strMessPrefix.'FIELD_SALE_METHOD_NAME'] = 'Способ продажи';
	$MESS[$strMessPrefix.'FIELD_SALE_METHOD_DESC'] = '<p><em>Важно:</em>&nbsp;Список возможных значений данного элемента зависит от параметра "Право собственности" (PropertyRights).<br><br>Способ продажи (для объектов в категории "Новостройки")&nbsp;— элемент с возможными значениями из&nbsp;списка:<br></p><p>для объектов с правом собственности (PropertyRights) "Собственник":</p><ul><li>Договор уступки права требования</li></ul><p>для объектов с правом собственности (PropertyRights) "Посредник":</p><ul><li>Договор уступки права требования</li><li>Договор долевого участия</li><li>Договор ЖСК</li></ul><p>для объектов с правом собственности (PropertyRights) "Застройщик":</p><ul><li>Договор долевого участия</li><li>Договор ЖСК</li></ul><p><br></p><br/><br/>Актуально для категорий: <p>Квартиры/Продам</p>';
$MESS[$strMessPrefix.'FIELD_SALE_OPTIONS_NAME'] = 'Способ продажи дополнительно';
	$MESS[$strMessPrefix.'FIELD_SALE_OPTIONS_DESC'] = '<p>Способ продажи дополнительно — элемент с возможными значениями из списка: </p><p>для объектов в категории "Квартиры" с типом сделки (DealType) "Прямая продажа" и типом объекта (MarketType) "Вторичка": </p><ul><li>Реализация на торгах </li><li>Возможна ипотека </li><li>Продажа доли </li></ul><p>для объектов в категории "Квартиры" с типом сделки (DealType) "Альтернативная" и типом объекта (MarketType) "Вторичка": </p><ul><li>Возможна ипотека </li><li>Продажа доли </li></ul><p>для объектов в категории "Квартиры" с типом объекта (MarketType) "Новостройки": </p><ul><li>Возможна ипотека </li></ul><p>для объектов в категории "Дома, дачи, коттеджи": </p><ul><li>Продажа доли</li></ul><br/><br/>Актуально для категорий: <ul><li>Квартиры/Продам</li><li>Дома, дачи, коттеджи/Продам</li></ul>';
$MESS[$strMessPrefix.'FIELD_PREMISES_TYPE_NAME'] = 'Тип помещения';
	$MESS[$strMessPrefix.'FIELD_PREMISES_TYPE_DESC'] = '<p>Тип помещения (только для объявлений с видом объекта (ObjectType) "Торговое помещение") — одно из значений списка: </p><ul><li>Для уличной торговли </li><li>В торговом комплексе</li></ul><br/><br/>Актуально для категорий: Коммерческая недвижимость';
$MESS[$strMessPrefix.'FIELD_ENTRANCE_NAME'] = 'Вход';
	$MESS[$strMessPrefix.'FIELD_ENTRANCE_DESC'] = '<p><strong>Внимание</strong>: рекомендуем заполнить этот элемент (Entrance), поскольку&nbsp;<strong>c 31.05.22&nbsp;</strong><strong>он станет обязательным&nbsp;</strong>в&nbsp;категории&nbsp;"Коммерческая недвижимость" с видом объекта (ObjectType) "Помещение свободного назначения", "Торговое помещение", "Помещение общественного питания".</p><p>Вход (только для объявлений с видом объекта (ObjectType) "Гостиница", "Помещение общественного питания", "Помещение свободного назначения", "Производственное помещение", "Складское помещение", "Торговое помещение", "Автосервис", "Здание") — одно из значений списка: </p><ul><li>С улицы </li><li>Со двора&nbsp;</li></ul><br/><br/>Актуально для категорий: Коммерческая недвижимость';
$MESS[$strMessPrefix.'FIELD_ENTRANCE_ADDITIONALLY_NAME'] = 'Вход дополнительно';
	$MESS[$strMessPrefix.'FIELD_ENTRANCE_ADDITIONALLY_DESC'] = '<p>Вход дополнительно (только для объявлений с видом объекта (ObjectType) "Гостиница", "Помещение общественного питания", "Помещение свободного назначения", "Производственное помещение", "Складское помещение", "Торговое помещение", "Автосервис", "Здание") — элемент с возможными значениями из списка:</p><ul><li>Отдельный вход&nbsp;</li></ul><br/><br/>Актуально для категорий: Коммерческая недвижимость';
$MESS[$strMessPrefix.'FIELD_FLOOR_ADDITIONALLY_NAME'] = 'Этаж дополнительно';
	$MESS[$strMessPrefix.'FIELD_FLOOR_ADDITIONALLY_DESC'] = '<p>Этаж дополнительно (только для объявлений с видом объекта (ObjectType) "Гостиница", "Офисное помещение", "Помещение общественного питания", "Помещение свободного назначения", "Производственное помещение", "Складское помещение", "Торговое помещение", "Автосервис") — элемент с возможными значениями из списка:</p><ul><li>Несколько этажей</li></ul><br/><br/>Актуально для категорий: Коммерческая недвижимость';
$MESS[$strMessPrefix.'FIELD_LAYOUT_NAME'] = 'Планировка';
	$MESS[$strMessPrefix.'FIELD_LAYOUT_DESC'] = '<p>Планировка (только для объявлений с видом объекта (ObjectType) "Складское помещение") — элемент с возможными значениями из списка:</p><ul><li>Кабинетная</li><li>Открытая </li></ul><br/><br/>Актуально для категорий: Коммерческая недвижимость';
$MESS[$strMessPrefix.'FIELD_POWER_GRID_CAPACITY_NAME'] = 'Мощность электросети, в кВт';
	$MESS[$strMessPrefix.'FIELD_POWER_GRID_CAPACITY_DESC'] = '<p>Мощность электросети, в кВт (только для объявлений с видом объекта (ObjectType) "Гостиница", "Помещение общественного питания", "Помещение свободного назначения", "Производственное помещение", "Складское помещение", "Торговое помещение", "Автосервис", "Здание") — целое число. </p><br/><br/>Актуально для категорий: Коммерческая недвижимость/Продам';
$MESS[$strMessPrefix.'FIELD_POWER_GRID_ADDITIONALLY_NAME'] = 'Электросеть дополнительно';
	$MESS[$strMessPrefix.'FIELD_POWER_GRID_ADDITIONALLY_DESC'] = '<p>Электросеть дополнительно (только для объявлений с видом объекта (ObjectType) "Гостиница", "Помещение общественного питания", "Помещение свободного назначения", "Производственное помещение", "Складское помещение", "Торговое помещение", "Автосервис", "Здание") — элемент с возможными значениями из списка: </p><ul><li>Возможно увеличение мощности </li></ul><br/><br/>Актуально для категорий: Коммерческая недвижимость/Продам';
$MESS[$strMessPrefix.'FIELD_HEATING_NAME'] = 'Отопление';
	$MESS[$strMessPrefix.'FIELD_HEATING_DESC'] = '<p>Отопление (только для объявлений в категории "Коммерческая недвижимость" с видом объекта (ObjectType) "Гостиница", "Помещение общественного питания", "Помещение свободного назначения", "Производственное помещение", "Складское помещение", "Торговое помещение", "Автосервис", "Здание") и для категории "Недвижимость за рубежом" с типом объекта ("ObjectType") "Квартира, апартаменты", "Дом, вилла", "Коммерческая недвижимость" — одно из значений списка:</p><ul><li>Нет</li><li>Центральное</li><li>Автономное </li></ul><br/><br/>Актуально для категорий: <ul><li>Коммерческая недвижимость</li><li>Недвижимость за рубежом</li></ul>';
$MESS[$strMessPrefix.'FIELD_READINESS_STATUS_NAME'] = 'Статус готовности';
	$MESS[$strMessPrefix.'FIELD_READINESS_STATUS_DESC'] = '<p>Статус готовности — одно из значений списка: </p><ul><li>Проект </li><li>Строится </li><li>В эксплуатации</li></ul><br/><br/>Актуально для категорий: Коммерческая недвижимость/Продам';
$MESS[$strMessPrefix.'FIELD_BUILDING_TYPE_NAME'] = 'Тип здания';
	$MESS[$strMessPrefix.'FIELD_BUILDING_TYPE_DESC'] = '<p>Тип здания — одно из значений списка: </p><ul><li>Бизнес-центр </li><li>Торговый центр </li><li>Административное здание </li><li>Жилой дом </li><li>Другой </li></ul><br/><br/>Актуально для категорий: Коммерческая недвижимость*';
$MESS[$strMessPrefix.'FIELD_DISTANCE_FROM_ROAD_NAME'] = 'Удаленность от дороги';
	$MESS[$strMessPrefix.'FIELD_DISTANCE_FROM_ROAD_DESC'] = '<p>Удаленность от дороги — одно из значений списка:</p><ul><li>Первая линия</li><li>Вторая линия и дальше</li></ul><br/><br/>Актуально для категорий: Коммерческая недвижимость';
$MESS[$strMessPrefix.'FIELD_PARKING_SPACES_NAME'] = 'Количество мест на парковке';
	$MESS[$strMessPrefix.'FIELD_PARKING_SPACES_DESC'] = '<p>Количество мест на парковке — целое число. </p><br/><br/>Актуально для категорий: Коммерческая недвижимость';
$MESS[$strMessPrefix.'FIELD_TRANSACTION_TYPE_NAME'] = 'Тип сделки';
	$MESS[$strMessPrefix.'FIELD_TRANSACTION_TYPE_DESC'] = '<p><strong>Внимание</strong>: рекомендуем заполнить этот элемент (TransactionType), поскольку&nbsp;<strong>c 31.05.22&nbsp;</strong><strong>он станет обязательным&nbsp;</strong>в&nbsp;категории&nbsp;"Коммерческая недвижимость".</p><p> — одно из значений списка: </p><ul><li>Продажа</li><li>Переуступка права аренды</li></ul><br/><br/>Актуально для категорий: Коммерческая недвижимость/Продам';
$MESS[$strMessPrefix.'FIELD_CURRENT_TENANTS_NAME'] = 'Текущие арендаторы';
	$MESS[$strMessPrefix.'FIELD_CURRENT_TENANTS_DESC'] = '<p>Текущие арендаторы — элемент с возможными значениями из списка: </p><ul><li>Помещение занято</li></ul><br/><br/>Актуально для категорий: Коммерческая недвижимость/Продам';
$MESS[$strMessPrefix.'FIELD_FOREIGN_REALTY_COMPLETION_YEAR_NAME'] = 'Год сдачи новостройки';
	$MESS[$strMessPrefix.'FIELD_FOREIGN_REALTY_COMPLETION_YEAR_DESC'] = '<p>Год сдачи новостройки. Доступен для объектов типа ("ObjectType") "Квартира, апартаменты" с типом квартиры ("MaketType") "Новостройка".&nbsp;</p><p>Одно из значений списка:</p><ul><li>Дом сдан</li><li>2022</li><li>2023</li><li>2024</li><li>2025</li><li>2026</li><li>2027</li><li>2028</li><li>2029</li><li>2030</li></ul><br/><br/>Актуально для категорий: Недвижимость за рубежом';
$MESS[$strMessPrefix.'FIELD_BATHROOM_COUNT_NAME'] = 'Количество санузлов';
	$MESS[$strMessPrefix.'FIELD_BATHROOM_COUNT_DESC'] = '<p>Количество санузлов. Для объектов типа ("ObjectType") "Квартира, апартаменты", "Дом, вилла"</p><ul><li>1</li><li>2</li><li>3</li><li>4 и больше</li></ul><br/><br/>Актуально для категорий: Недвижимость за рубежом';
$MESS[$strMessPrefix.'FIELD_ELEVATOR_NAME'] = 'Наличие лифта';
	$MESS[$strMessPrefix.'FIELD_ELEVATOR_DESC'] = '<p>Наличие лифта.&nbsp;Для объектов типа ("ObjectType") "Квартира, апартаменты", "Дом, вилла".</p><p>Одно из значений списка:</p><ul><li>Да</li><li>Нет</li></ul><br/><br/>Актуально для категорий: Недвижимость за рубежом';
$MESS[$strMessPrefix.'FIELD_FOREIGN_REALTY_ADDITIONALLY_NAME'] = 'Удобства';
	$MESS[$strMessPrefix.'FIELD_FOREIGN_REALTY_ADDITIONALLY_DESC'] = '<p>Удобства – только для объектов с типом ("ObjectType") "Квартира, апартаменты", "Дом, вилла". Элемент с возможными значениями из списка:</p><ul><li>Бытовая техника</li><li>Кондиционер</li><li>Гардеробная</li><li>Панорамные окна</li><li>Спортзал</li><li>Консьерж</li></ul><br/><br/>Актуально для категорий: Недвижимость за рубежом';
$MESS[$strMessPrefix.'FIELD_AIRPORT_DISTANCE_NAME'] = 'Расстояние до аэропорта в километрах';
	$MESS[$strMessPrefix.'FIELD_AIRPORT_DISTANCE_DESC'] = '<p>Расстояние до аэропорта в километрах. Для объектов типа ("ObjectType") "Квартира, апартаменты", "Дом, вилла", "Коммерческая недвижимость", "Земельный участок".</p><p>Целое число.&nbsp;</p><br/><br/>Актуально для категорий: Недвижимость за рубежом';
$MESS[$strMessPrefix.'FIELD_WATER_DISTANCE_NAME'] = 'Расстояние до воды в метрах';
	$MESS[$strMessPrefix.'FIELD_WATER_DISTANCE_DESC'] = '<p>Расстояние до воды в метрах. Для объектов типа ("ObjectType") "Квартира, апартаменты", "Дом, вилла":</p><p>Целое число.&nbsp;</p><br/><br/>Актуально для категорий: Недвижимость за рубежом';
$MESS[$strMessPrefix.'FIELD_RESIDENCE_AFTER_DEAL_NAME'] = 'ВНЖ при покупке.';
	$MESS[$strMessPrefix.'FIELD_RESIDENCE_AFTER_DEAL_DESC'] = '<p>ВНЖ при покупке.</p><p>Одно из значений списка:</p><ul><li>Да</li><li>Нет</li></ul><br/><br/>Актуально для категорий: Недвижимость за рубежом';
$MESS[$strMessPrefix.'FIELD_FOREIGN_REALTY_SALE_OPTIONS_NAME'] = 'Условия покупки';
	$MESS[$strMessPrefix.'FIELD_FOREIGN_REALTY_SALE_OPTIONS_DESC'] = '<p>Условия покупки.&nbsp;Элемент с возможными значениями из списка:</p><ul><li>Возможен кредит</li><li>Возможна рассрочка</li></ul><br/><br/>Актуально для категорий: Недвижимость за рубежом';
$MESS[$strMessPrefix.'FIELD_INVESTMENT_NAME'] = 'Возможность для инвестиций';
	$MESS[$strMessPrefix.'FIELD_INVESTMENT_DESC'] = '<p>Возможность для инвестиций.&nbsp;Элемент с возможными значениями из списка:</p><ul><li>Обратный выкуп</li><li>Услуги сдачи в аренду</li></ul><br/><br/>Актуально для категорий: Недвижимость за рубежом';
$MESS[$strMessPrefix.'FIELD_FOREIGN_REALTY_COMMERCIAL_OBJECT_TYPE_NAME'] = 'Тип коммерческой зарубежной недвижимости';
	$MESS[$strMessPrefix.'FIELD_FOREIGN_REALTY_COMMERCIAL_OBJECT_TYPE_DESC'] = '<p>Тип коммерческой зарубежной недвижимости.&nbsp;Для объектов типа ("ObjectType") "Коммерческая недвижимость".</p><p>Одно из значений списка:</p><ul><li>Офис</li><li>Свободного назначения</li><li>Магазин/Торговая площадь</li><li>Склад</li><li>Производство</li><li>Общепит</li><li>Гостиница</li><li>Автосервис</li></ul><br/><br/>Актуально для категорий: Недвижимость за рубежом';
$MESS[$strMessPrefix.'FIELD_FOREIGN_REALTY_LAND_STATUS_NAME'] = 'Назначение земельного участка за рубежом';
	$MESS[$strMessPrefix.'FIELD_FOREIGN_REALTY_LAND_STATUS_DESC'] = '<p>Назначение земельного участка за рубежом.&nbsp;Для объектов типа ("ObjectType") "Земельный участок".</p><p>Элемент с возможными значениями из списка:</p><ul><li>Жилое строительство</li><li>Коммерческое строительство</li><li>Сельское хозяйство</li><li>Другое</li></ul><br/><br/>Актуально для категорий: Недвижимость за рубежом';
$MESS[$strMessPrefix.'FIELD_FOREIGN_CITY_NAME'] = 'Город, в котором находится объект объявления';
	$MESS[$strMessPrefix.'FIELD_FOREIGN_CITY_DESC'] = '<p>Город, в котором находится объект объявления — в соответствии со значениями из <a href="https://autoload.avito.ru/format/foreign_countries.xml" rel="nofollow" target="_blank">справочника</a>. Если нужного города нет в справочнике, следует выбрать ближайший или написать на&nbsp;<a href="mailto:ForeignRealty@avito.ru." rel="nofollow" target="_blank">ForeignRealty@avito.ru</a>&nbsp;с просьбой добавить нужный вам город.&nbsp;&nbsp;&nbsp;</p><br/><br/>Актуально для категорий: Недвижимость за рубежом';
$MESS[$strMessPrefix.'FIELD_SHAREHOLDER_FIRST_NAME_NAME'] = 'Имя дольщика';
	$MESS[$strMessPrefix.'FIELD_SHAREHOLDER_FIRST_NAME_DESC'] = '<p>Для типов объекта (MarketType) "Новостройка" в категории "Квартиры": </p><p>Имя дольщика - строка, содержащая буквы русского алфавита. Необходимо указать при продаже квартиры с переуступкой права по договору долевого участия (ДДУ). </p><p>При продаже от частного лица указываются данные из ДДУ: </p><ul><li>ShareholderFirstName </li><li>ShareholderLastName </li><li>ShareholderPatronymic </li></ul><p>При продаже от юридического лица указываются данные из ДДУ: </p><ul><li>ShareholderINN</li></ul><br/><br/>Актуально для категорий: Квартиры/Продам/Новостройка';
$MESS[$strMessPrefix.'FIELD_SHAREHOLDER_LAST_NAME_NAME'] = 'Фамилия дольщика';
	$MESS[$strMessPrefix.'FIELD_SHAREHOLDER_LAST_NAME_DESC'] = '<p>Для типов объекта (MarketType) "Новостройка" в категории "Квартиры": </p><p>Фамилия дольщика - строка, содержащая буквы русского алфавита. &nbsp;Необходимо указать при продаже квартиры с переуступкой права по договору долевого участия (ДДУ). </p><p>При продаже от частного лица указываются данные из ДДУ: </p><ul><li>ShareholderFirstName </li><li>ShareholderLastName </li><li>ShareholderPatronymic </li></ul><p>При продаже от юридического лица указываются данные из ДДУ: </p><ul><li>ShareholderINN</li></ul><br/><br/>Актуально для категорий: Квартиры/Продам/Новостройка';
$MESS[$strMessPrefix.'FIELD_SHAREHOLDER_PATRONYMIC_NAME'] = 'Отчество дольщика';
	$MESS[$strMessPrefix.'FIELD_SHAREHOLDER_PATRONYMIC_DESC'] = '<p>Для типов объекта (MarketType) "Новостройка" в категории "Квартиры": </p><p>Отчество дольщика - строка, содержащая буквы русского алфавита. Необходимо указать при продаже квартиры с переуступкой права по договору долевого участия (ДДУ). </p><p>При продаже от частного лица указываются данные из ДДУ: </p><ul><li>ShareholderFirstName </li><li>ShareholderLastName </li><li>ShareholderPatronymic </li></ul><p>При продаже от юридического лица указываются данные из ДДУ: </p><ul><li>ShareholderINN</li></ul><br/><br/>Актуально для категорий: Квартиры/Продам/Новостройка';
$MESS[$strMessPrefix.'FIELD_SHAREHOLDER_INN_NAME'] = 'ИНН дольщика';
	$MESS[$strMessPrefix.'FIELD_SHAREHOLDER_INN_DESC'] = '<p>Для типов объекта (MarketType) "Новостройка" в категории "Квартиры": </p><p>ИНН дольщика - 10 цифр для российских компаний и 9 для иностранных. Необходимо указать при продаже квартиры с переуступкой права по договору долевого участия (ДДУ). </p><p>При продаже от частного лица указываются данные из ДДУ: </p><ul><li>ShareholderFirstName </li><li>ShareholderLastName </li><li>ShareholderPatronymic </li></ul><p>При продаже от юридического лица указываются данные из ДДУ: </p><ul><li>ShareholderINN</li></ul><br/><br/>Актуально для категорий: Квартиры/Продам/Новостройка';
$MESS[$strMessPrefix.'FIELD_CURRENCY_PRICE_NAME'] = 'Цена в валюте';
	$MESS[$strMessPrefix.'FIELD_CURRENCY_PRICE_DESC'] = '<p>Цена в валюте. Если заполнен этот параметр, то параметр Price игнорируется.</p><ul><li>"Продам" — цена в валюте за всё;</li><li>"Сдам" — в зависимости от срока аренды:<ul><li>"На длительный срок" —&nbsp; цена в валюте в месяц за весь объект;</li><li>"Посуточно" — цена в валюте за сутки.</li></ul></li></ul><p><br></p><br/><br/>Актуально для категорий: Недвижимость за рубежом';
$MESS[$strMessPrefix.'FIELD_CURRENCY_NAME'] = 'Валюта цены';
	$MESS[$strMessPrefix.'FIELD_CURRENCY_DESC'] = '<p>Валюта цены. Становится обязательным, если заполнен атрибут CurrencyPrice.</p><p>Одно из значений&nbsp;списка:</p><ul><li>RUB</li><li>USD</li><li>EUR</li><li>CNY</li></ul><br/><br/>Актуально для категорий: Недвижимость за рубежом';
$MESS[$strMessPrefix.'FIELD_RESIDENCE_TYPE_NAME'] = 'Тип жилья';
	$MESS[$strMessPrefix.'FIELD_RESIDENCE_TYPE_DESC'] = 'Тип жилья.<br><p>Одно из значений&nbsp;списка:</p><ul><li>Комната</li><li>Койко-мест<br></li></ul><br/><br/>Актуально для категорий: Комнаты/Сдам*';
$MESS[$strMessPrefix.'FIELD_ROOM_LOCATION_TYPE_NAME'] = 'ResidenceType';
	$MESS[$strMessPrefix.'FIELD_ROOM_LOCATION_TYPE_DESC'] = 'Расположение. Для объектов где в&nbsp;<strong>ResidenceType</strong>&nbsp;выбрано&nbsp; Комната<br><p>Одно из значений&nbsp;списка:</p><ul><li>Квартира</li><li>Хостел</li><li>Гостиница<br><br></li></ul><br/><br/>Актуально для категорий: Комнаты/Сдам**';
$MESS[$strMessPrefix.'FIELD_BED_LOCATION_TYPE_NAME'] = 'ResidenceType';
	$MESS[$strMessPrefix.'FIELD_BED_LOCATION_TYPE_DESC'] = 'Расположение.&nbsp;Для объектов где в&nbsp;<strong>ResidenceType</strong>&nbsp;выбрано&nbsp;&nbsp;Койко-место<br><p>Одно из значений&nbsp;списка:</p><ul><li>Квартира</li><li>Хостел<br><br></li></ul><br/><br/>Актуально для категорий: Комнаты/Сдам**';
$MESS[$strMessPrefix.'FIELD_LEASE_COMFORT_MULTI_NAME'] = 'Комфорт';
	$MESS[$strMessPrefix.'FIELD_LEASE_COMFORT_MULTI_DESC'] = '<p>Комфорт (только для объектов со сроком аренды (LeaseType)&nbsp;"Посуточно")&nbsp;— элемент с возможными значениями из&nbsp;списка:<br></p><ul><li>Постельное белье</li><li>Полотенца</li><li>Средства гигиены</li></ul><br/><br/>Актуально для категорий: <ul><li>Квартиры</li><li>Дома, дачи, коттеджи</li></ul>';
$MESS[$strMessPrefix.'FIELD_LEASE_PRICE_OPTIONS_NAME'] = 'Цена дополнительно';
	$MESS[$strMessPrefix.'FIELD_LEASE_PRICE_OPTIONS_DESC'] = '<p>Цена дополнительно — элемент с возможными значениями из списка: </p><p>для объектов в категории "Квартиры" со сроком аренды (LeaseType) "На длительный срок", <br>для объектов в категории "Дома, дачи, коттеджи" со сроком аренды (LeaseType) "На длительный срок": </p><ul><li>Оплата по счётчикам включена </li></ul><p>для объектов в категории "Коммерческая недвижимость", </p><ul><li>Коммунальные услуги включены</li><li>Эксплуатационные расходы включены </li></ul><br/><br/>Актуально для категорий: <ul><li>Квартиры</li><li>Дома, дачи, коттеджи</li><li>Коммерческая недвижимость</li></ul>';
$MESS[$strMessPrefix.'FIELD_SQUARE_ADDITIONALLY_NAME'] = 'Площадь дополнительно';
	$MESS[$strMessPrefix.'FIELD_SQUARE_ADDITIONALLY_DESC'] = '<p>Площадь дополнительно — элемент с возможными значениями из списка: </p><ul><li>Возможная нарезка </li></ul><br/><br/>Актуально для категорий: <p>Коммерческая недвижимость</p>';
$MESS[$strMessPrefix.'FIELD_RENTAL_TYPE_NAME'] = 'Тип аренды';
	$MESS[$strMessPrefix.'FIELD_RENTAL_TYPE_DESC'] = '<p><strong>Внимание</strong>: рекомендуем заполнить этот элемент (RentalType), поскольку&nbsp;<strong>c 31.05.22&nbsp;</strong><strong>он станет обязательным&nbsp;</strong>в&nbsp;категории&nbsp;"Коммерческая недвижимость".</p><p>Тип аренды — одно из значений списка: </p><ul><li>Прямая</li><li>Субаренда </li></ul><br/><br/>Актуально для категорий: Коммерческая недвижимость';
$MESS[$strMessPrefix.'FIELD_RENTAL_HOLIDAYS_NAME'] = 'Арендные каникулы';
	$MESS[$strMessPrefix.'FIELD_RENTAL_HOLIDAYS_DESC'] = '<p>Арендные каникулы —&nbsp;одно из значений списка:</p><ul><li>Арендные каникулы</li></ul><br/><br/>Актуально для категорий: Коммерческая недвижимость';
$MESS[$strMessPrefix.'FIELD_RENTAL_MINIMUM_PERIOD_NAME'] = 'Минимальный срок аренды, в мес';
	$MESS[$strMessPrefix.'FIELD_RENTAL_MINIMUM_PERIOD_DESC'] = '<p>Минимальный срок аренды, в мес — целое число.</p><br/><br/>Актуально для категорий: Коммерческая недвижимость';
$MESS[$strMessPrefix.'FIELD_LEASE_DEPOSIT_PRICE_NAME'] = 'Сумма залога, в руб';
	$MESS[$strMessPrefix.'FIELD_LEASE_DEPOSIT_PRICE_DESC'] = '<p>Сумма залога, в руб&nbsp;(только для объектов со сроком аренды (LeaseType)&nbsp;"Посуточно") -&nbsp;целое число.</p><br/><br/>Актуально для категорий: <ul><li>Квартиры</li><li>Дома, дачи, коттеджи</li></ul>';
$MESS[$strMessPrefix.'FIELD_CHILDREN_ALLOWED_NAME'] = 'Можно с детьми';
	$MESS[$strMessPrefix.'FIELD_CHILDREN_ALLOWED_DESC'] = '<p><em>Важно:</em>&nbsp;данный атрибут (SmokingAllowed)&nbsp;<strong>является</strong><strong>&nbsp;обязательным&nbsp;</strong>в&nbsp;категории "Квартиры/Сдам" и в категории "Дома, дачи, коттеджи" со сроком аренды "Посуточно".</p><p>Можно с детьми&nbsp;— одно из значений списка:</p><ul><li>Да</li><li>Нет</li></ul><br/><br/>Актуально для категорий: <ul><li>Квартиры*</li><li>Дома, дачи, коттеджи**</li><li>Недвижимость за рубежом</li></ul>';
$MESS[$strMessPrefix.'FIELD_PETS_ALLOWED_NAME'] = 'Можно с животными';
	$MESS[$strMessPrefix.'FIELD_PETS_ALLOWED_DESC'] = '<p><em>Важно:</em>&nbsp;данный атрибут (PetsAllowed)&nbsp;<strong>является</strong><strong>&nbsp;обязательным </strong>в категории "Квартиры/Сдам" и в категории "Дома, дачи, коттеджи" со сроком аренды "Посуточно".</p><p>Можно с животными&nbsp;— одно из значений списка:</p><ul><li>Да</li><li>Нет</li></ul><br/><br/>Актуально для категорий: <ul><li>Квартиры*</li><li>Дома, дачи, коттеджи**</li><li>Недвижимость за рубежом</li></ul>';
$MESS[$strMessPrefix.'FIELD_SMOKING_ALLOWED_NAME'] = 'Разрешено курить';
	$MESS[$strMessPrefix.'FIELD_SMOKING_ALLOWED_DESC'] = '<p><em>Важно:</em>&nbsp;данный атрибут (SmokingAllowed)&nbsp;<strong>является</strong><strong>&nbsp;обязательным&nbsp;</strong>в категории "Дома, дачи, коттеджи" со сроком аренды "Посуточно".</p><p>Разрешено курить&nbsp;— одно из значений списка:</p><ul><li>Да</li><li>Нет</li></ul><br/><br/>Актуально для категорий: <ul><li>Квартиры</li><li>Дома, дачи, коттеджи**</li></ul>';
$MESS[$strMessPrefix.'FIELD_PARTIES_ALLOWED_NAME'] = 'Разрешены вечеринки';
	$MESS[$strMessPrefix.'FIELD_PARTIES_ALLOWED_DESC'] = '<p><em>Важно:</em>&nbsp;данный атрибут (PartiesAllowed)&nbsp;<strong>является</strong><strong>&nbsp;обязательным&nbsp;</strong>в категории "Дома, дачи, коттеджи" со сроком аренды "Посуточно".</p><p>Разрешены вечеринки&nbsp;(только для объектов со сроком аренды (LeaseType)&nbsp;"Посуточно") — одно из значений списка:</p><ul><li>Да</li><li>Нет</li></ul><br/><br/>Актуально для категорий: <ul><li>Квартиры</li><li>Дома, дачи, коттеджи**</li><li>Недвижимость за рубежом</li></ul>';
$MESS[$strMessPrefix.'FIELD_DOCUMENTS_NAME'] = 'Есть отчётные документы';
	$MESS[$strMessPrefix.'FIELD_DOCUMENTS_DESC'] = '<p><em>Важно:</em>&nbsp;данный атрибут (Documents)&nbsp;<strong>является</strong><strong>&nbsp;обязательным&nbsp;</strong>в категории "Дома, дачи, коттеджи" со сроком аренды "Посуточно".</p><p>Есть отчётные документы&nbsp;(только для объектов со сроком аренды (LeaseType)&nbsp;"Посуточно") — одно из значений списка:</p><ul><li>Да</li><li>Нет</li></ul><br/><br/>Актуально для категорий: <ul><li>Квартиры</li><li>Дома, дачи, коттеджи**</li></ul>';



?>