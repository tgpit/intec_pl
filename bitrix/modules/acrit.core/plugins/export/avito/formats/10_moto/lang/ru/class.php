<?
$strMessPrefix = 'ACRIT_EXP_AVITO_MOTO_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Авито (Мотоциклы и мототехника)';

// Fields
$MESS[$strMessPrefix.'FIELD_CATEGORY_NAME'] = 'Категория товара';
	$MESS[$strMessPrefix.'FIELD_CATEGORY_DESC'] = 'Категория товара — строка «Мотоциклы и мототехника»';
	$MESS[$strMessPrefix.'FIELD_CATEGORY_DEFAULT'] = 'Мотоциклы и мототехника';
$MESS[$strMessPrefix.'FIELD_CONDITION_NAME'] = 'Состояние';
	$MESS[$strMessPrefix.'FIELD_CONDITION_DESC'] = 'Состояние вещи — одно из значений списка:<br/>
<ul>
	<li>Новое</li>
	<li>Б/у</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_VEHICLE_TYPE_NAME'] = 'Вид техники';
	$MESS[$strMessPrefix.'FIELD_VEHICLE_TYPE_DESC'] = 'Вид техники — одно из значений списка:<br/>
<ul>
	<li>Багги</li>
	<li>Вездеходы</li>
	<li>Картинг</li>
	<li>Квадроциклы</li>
	<li>Мопеды и скутеры</li>
	<li>Мотоциклы</li>
	<li>Снегоходы</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_MOTO_TYPE_NAME'] = 'Тип мотоцикла';
	$MESS[$strMessPrefix.'FIELD_MOTO_TYPE_DESC'] = 'Тип мотоцикла — одно из значений списка:
<ul>
	<li>Дорожные</li>
	<li>Кастом-байки</li>
	<li>Кросс и эндуро</li>
	<li>Спортивные</li>
	<li>Чопперы</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_TITLE_NAME'] = 'Название объявления';
	$MESS[$strMessPrefix.'FIELD_TITLE_DESC'] = 'Название объявления — строка до 50 символов.<br/>
Примечание: не пишите в название цену и контактную информацию — для этого есть отдельные поля — и не используйте слово «продам».';
$MESS[$strMessPrefix.'FIELD_PRICE_NAME'] = 'Цена в рублях';
	$MESS[$strMessPrefix.'FIELD_PRICE_DESC'] = 'Цена в рублях — целое число.';
#
$MESS[$strMessPrefix.'FIELD_VIN_NAME'] = 'VIN-номер';
	$MESS[$strMessPrefix.'FIELD_VIN_DESC'] = '<br/><br/>Актуально для категорий: <p>VIN-номер&nbsp;(<a href="https://ru.wikipedia.org/wiki/%D0%98%D0%B4%D0%B5%D0%BD%D1%82%D0%B8%D1%84%D0%B8%D0%BA%D0%B0%D1%86%D0%B8%D0%BE%D0%BD%D0%BD%D1%8B%D0%B9_%D0%BD%D0%BE%D0%BC%D0%B5%D1%80_%D1%82%D1%80%D0%B0%D0%BD%D1%81%D0%BF%D0%BE%D1%80%D1%82%D0%BD%D0%BE%D0%B3%D0%BE_%D1%81%D1%80%D0%B5%D0%B4%D1%81%D1%82%D0%B2%D0%B0" target="_blank">vehicle identification number</a>) — строка до 20 символов.</p><p><br></p>';
$MESS[$strMessPrefix.'FIELD_YEAR_NAME'] = 'Год выпуска';
	$MESS[$strMessPrefix.'FIELD_YEAR_DESC'] = '<br/><br/>Актуально для категорий: <p>Год выпуска&nbsp;— целое число в диапазоне от 1905 до 2022</p>';
$MESS[$strMessPrefix.'FIELD_POWER_NAME'] = 'Мощность, л.с.';
	$MESS[$strMessPrefix.'FIELD_POWER_DESC'] = '<br/><br/>Актуально для категорий: <p>Мощность&nbsp;в л.с.&nbsp;— целое число в диапазоне от 1&nbsp;до 3000</p>';
$MESS[$strMessPrefix.'FIELD_ENGINE_CAPACITY_NAME'] = 'Объем двигателя, куб.см.';
	$MESS[$strMessPrefix.'FIELD_ENGINE_CAPACITY_DESC'] = '<br/><br/>Актуально для категорий: <p>Объем двигателя в см<sup>3</sup>&nbsp;— целое число в диапазоне от 1&nbsp;до&nbsp;100000</p>';
$MESS[$strMessPrefix.'FIELD_KILOMETRAGE_NAME'] = 'Пробег, км';
	$MESS[$strMessPrefix.'FIELD_KILOMETRAGE_DESC'] = '<br/><br/>Актуально для категорий: <p>Пробег транспортного средства в км - целое число, в диапазоне от 1 до&nbsp;1000000</p>';
#
$MESS[$strMessPrefix.'FIELD_AVAILABILITY_NAME'] = 'Доступность';
	$MESS[$strMessPrefix.'FIELD_AVAILABILITY_DESC'] = '<p>Доступность — одно из значений списка:</p><ul><li>В наличии</li><li>Под заказ</li></ul><br/><br/>Актуально для категорий: <br>';
$MESS[$strMessPrefix.'FIELD_TECHNICAL_PASSPORT_NAME'] = 'ПТС';
	$MESS[$strMessPrefix.'FIELD_TECHNICAL_PASSPORT_DESC'] = '<p>ПТС — одно из значений списка:</p><ul><li>Оригинал</li><li>Дубликат</li><li>Электронный</li><li>Нет</li></ul><br/><br/>Актуально для категорий: <p>Вид техники:&nbsp;</p><ul><li>Мотоциклы</li><li>Мопеды и скутеры</li></ul>';
$MESS[$strMessPrefix.'FIELD_OWNERS_NAME'] = 'Владельцев по ПТС';
	$MESS[$strMessPrefix.'FIELD_OWNERS_DESC'] = '<p>Владельцев по ПТС — одно из значений списка:</p><ul><li>1</li><li>2</li><li>3</li><li>4+</li></ul><br/><br/>Актуально для категорий: <p>Вид техники:&nbsp;</p><ul><li>Мотоциклы</li><li>Мопеды и скутеры</li></ul><p>Состояние:</p><ul><li>Б/у</li><li>На запчасти</li></ul>';
$MESS[$strMessPrefix.'FIELD_MAKE_NAME'] = 'Марка транспортного средства.';
	$MESS[$strMessPrefix.'FIELD_MAKE_DESC'] = '<p>Марка транспортного средства.</p><p><a href="https://autoload.avito.ru/format/motorcycles.xml" rel="nofollow" target="_blank">Каталог мотоциклов</a></p><p><a href="https://autoload.avito.ru/format/motorbikes_and_scooters.xml" rel="nofollow" target="_blank">Каталог мопедов и скутеров</a></p><br/><br/>Актуально для категорий: <p>Вид техники:&nbsp;</p><ul><li>Мотоциклы</li><li>Мопеды и скутеры</li></ul>';
$MESS[$strMessPrefix.'FIELD_MODEL_NAME'] = 'Модель транспортного средства.';
	$MESS[$strMessPrefix.'FIELD_MODEL_DESC'] = '<p>Модель транспортного средства.</p><p><a href="https://autoload.avito.ru/format/motorcycles.xml" rel="nofollow" target="_blank">Каталог мотоциклов</a></p><p><a href="https://autoload.avito.ru/format/motorbikes_and_scooters.xml" rel="nofollow" target="_blank">Каталог мопедов и скутеров</a></p><br/><br/>Актуально для категорий: <p>Вид техники:&nbsp;</p><ul><li>Мотоциклы</li><li>Мопеды и скутеры</li></ul>';
$MESS[$strMessPrefix.'FIELD_TYPE_NAME'] = 'Тип транспортного средства.';
	$MESS[$strMessPrefix.'FIELD_TYPE_DESC'] = '<p>Тип транспортного средства.&nbsp;</p><p>Для мотоциклов — одно из значений списка:</p><ul><li>Круизер или чоппер</li><li>Спортбайк</li><li>Туристический</li><li>Спорт-турист</li><li>Тур-эндуро</li><li>Трицикл</li><li>Naked bike</li><li>Мотард</li><li>Эндуро</li><li>Кроссовый</li><li>Питбайк</li><li>Триал</li><li>Детский</li><li>Кастом&nbsp;</li></ul><p>Для мопедов и скутеров — одно из значений списка:</p><ul><li>Скутер</li><li>Макси-скутер</li><li>Мопед</li><li>Минибайк</li></ul><br/><br/>Актуально для категорий: <p>Вид техники:&nbsp;</p><ul><li>Мотоциклы</li><li>Мопеды и скутеры</li></ul>';
$MESS[$strMessPrefix.'FIELD_ENGINE_TYPE_NAME'] = 'Тип двигателя';
	$MESS[$strMessPrefix.'FIELD_ENGINE_TYPE_DESC'] = '<p>Тип двигателя — одно из значений списка:</p><ul><li>Бензин</li><li>Электро</li></ul><br/><br/>Актуально для категорий: <br>';
$MESS[$strMessPrefix.'FIELD_FUEL_FEED_NAME'] = 'Подача топлива';
	$MESS[$strMessPrefix.'FIELD_FUEL_FEED_DESC'] = '<p>Подача топлива — одно из значений списка:</p><ul><li>Карбюратор</li><li>Инжектор</li></ul><br/><br/>Актуально для категорий: <p>Вид техники:&nbsp;</p><ul><li>Мотоциклы</li><li>Мопеды и скутеры</li></ul><p>Тип двигателя:</p><ul><li>Бензин</li></ul>';
$MESS[$strMessPrefix.'FIELD_DRIVE_TYPE_NAME'] = 'ЦепьРеменьКардан';
	$MESS[$strMessPrefix.'FIELD_DRIVE_TYPE_DESC'] = 'Тип привода&nbsp;— одно из значений списка:<ul><li>Цепь</li><li>Ремень</li><li>Кардан</li></ul><br/><br/>Актуально для категорий: <p>Вид техники:&nbsp;</p><ul><li>Мотоциклы</li></ul><p>Тип двигателя:</p><ul><li>Бензин</li></ul>';
$MESS[$strMessPrefix.'FIELD_STROKE_NAME'] = 'Число тактов';
	$MESS[$strMessPrefix.'FIELD_STROKE_DESC'] = 'Число тактов — одно из значений списка:<ul><li>2</li><li>4</li></ul><br/><br/>Актуально для категорий: <p>Вид техники:&nbsp;</p><ul><li>Мотоциклы</li><li>Мопеды и скутеры</li></ul><p>Тип двигателя:</p><ul><li>Бензин</li></ul>';
$MESS[$strMessPrefix.'FIELD_CYLINDERS_NAME'] = 'Количество цилиндров';
	$MESS[$strMessPrefix.'FIELD_CYLINDERS_DESC'] = 'Количество цилиндров — одно из значений списка:<ul><li>1</li><li>2</li><li>3</li><li>4</li><li>6</li></ul><br/><br/>Актуально для категорий: <p>Вид техники:&nbsp;</p><ul><li>Мотоциклы</li></ul><p>Тип двигателя:</p><ul><li>Бензин</li></ul>';
$MESS[$strMessPrefix.'FIELD_TRANSMISSION_NAME'] = 'МеханикаАвтоматРоботВариатор';
	$MESS[$strMessPrefix.'FIELD_TRANSMISSION_DESC'] = 'Коробка передач — одно из значений списка:<ul><li>Механика</li><li>Автомат</li><li>Робот</li><li>Вариатор</li></ul><br/><br/>Актуально для категорий: <p>Вид техники:&nbsp;</p><ul><li>Мотоциклы</li><li>Мопеды и скутеры</li></ul><p>Тип двигателя:</p><ul><li>Бензин</li></ul>';
$MESS[$strMessPrefix.'FIELD_NUMBER_OF_GEARS_NAME'] = 'Количество передач';
	$MESS[$strMessPrefix.'FIELD_NUMBER_OF_GEARS_DESC'] = 'Количество передач&nbsp;— одно из значений списка:<ul><li>3</li><li>4</li><li>5</li><li>6</li></ul><br/><br/>Актуально для категорий: <p>Коробка передач:&nbsp;</p><ul><li>Механика</li></ul>';
$MESS[$strMessPrefix.'FIELD_CYLINDERS_POSITION_NAME'] = 'Расположение цилиндров';
	$MESS[$strMessPrefix.'FIELD_CYLINDERS_POSITION_DESC'] = '<p>&nbsp;Расположение цилиндров&nbsp;— одно из значений списка:</p><ul><li>V-образное</li><li>Оппозитное</li><li>Рядное</li></ul><br/><br/>Актуально для категорий: <p>Вид техники:&nbsp;</p><ul><li>Мотоциклы</li></ul><p>Тип двигателя:</p><ul><li>Бензин</li></ul>';
$MESS[$strMessPrefix.'FIELD_ENGINE_COOLING_NAME'] = 'Охлаждение';
	$MESS[$strMessPrefix.'FIELD_ENGINE_COOLING_DESC'] = '<p>&nbsp;Охлаждение&nbsp;— одно из значений списка:</p><ul><li>Воздушное</li><li>Жидкостное</li></ul><br/><br/>Актуально для категорий: <p>Вид техники:&nbsp;</p><ul><li>Мотоциклы</li><li>Мопеды и скутеры</li></ul><p>Тип двигателя:</p><ul><li>Бензин</li></ul>';
$MESS[$strMessPrefix.'FIELD_TOP_SPEED_NAME'] = 'Максимальная скорость в км/ч';
	$MESS[$strMessPrefix.'FIELD_TOP_SPEED_DESC'] = '<p>Максимальная скорость в км/ч&nbsp;—&nbsp;целое число, в диапазоне от 1 до&nbsp;1000</p><br/><br/>Актуально для категорий: <p>Вид техники:&nbsp;</p><ul><li>Мотоциклы</li><li>Мопеды и скутеры</li></ul><p>Тип двигателя:</p><ul><li>Электро</li></ul>';
$MESS[$strMessPrefix.'FIELD_BATTERY_CAPACITY_NAME'] = 'Ёмкость аккумулятора в Ah';
	$MESS[$strMessPrefix.'FIELD_BATTERY_CAPACITY_DESC'] = '<p>Ёмкость аккумулятора в Ah&nbsp;—&nbsp;целое число</p><br/><br/>Актуально для категорий: <p>Вид техники:&nbsp;</p><ul><li>Мотоциклы</li><li>Мопеды и скутеры</li></ul><p>Тип двигателя:</p><ul><li>Электро</li></ul>';
$MESS[$strMessPrefix.'FIELD_ELECTRIC_RANGE_NAME'] = 'Запас хода в км';
	$MESS[$strMessPrefix.'FIELD_ELECTRIC_RANGE_DESC'] = '<p>Запас хода в км&nbsp;—&nbsp;целое число, в диапазоне от 1 до&nbsp;1000</p><br/><br/>Актуально для категорий: <p>Тип двигателя:</p><ul><li>Электро</li></ul>';
$MESS[$strMessPrefix.'FIELD_CHARGING_TIME_NAME'] = 'Время зарядки в часах (ч.)';
	$MESS[$strMessPrefix.'FIELD_CHARGING_TIME_DESC'] = '<p>Время зарядки в часах (ч.)&nbsp;—&nbsp;целое число, в диапазоне от 1 до&nbsp;1000</p><br/><br/>Актуально для категорий: <p>Вид техники:&nbsp;</p><ul><li>Мотоциклы</li><li>Мопеды и скутеры</li></ul><p>Тип двигателя:</p><ul><li>Электро</li></ul>';
$MESS[$strMessPrefix.'FIELD_ADDITIONAL_OPTIONS_NAME'] = 'Дополнительные опции';
	$MESS[$strMessPrefix.'FIELD_ADDITIONAL_OPTIONS_DESC'] = '<p>Дополнительные опции - мультивыбор значений из списка:</p><ul><li>Электростартер</li><li>Антиблокировочная система (ABS)</li><li>Трэкшн-контроль (TCS)</li><li>&nbsp;Система «старт-стоп»</li><li>Ветровое стекло</li><li>Кофр</li></ul><br/><br/>Актуально для категорий: <p>Вид техники:&nbsp;</p><ul><li>Мотоциклы</li><li>Мопеды и скутеры</li></ul>';
?>