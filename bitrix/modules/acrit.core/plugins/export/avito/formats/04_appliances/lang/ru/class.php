<?
$strMessPrefix = 'ACRIT_EXP_AVITO_APPLIANCES_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Авито (Бытовая электроника)';

// Fields
$MESS[$strMessPrefix.'FIELD_CATEGORY_NAME'] = 'Категория';
	$MESS[$strMessPrefix.'FIELD_CATEGORY_DESC'] = 'Категория товара — одно из значений списка:<br/>
<ul>
	<li>Телефоны</li>
	<li>Аудио и видео</li>
	<li>Товары для компьютера</li>
	<li>Фототехника</li>
	<li>Игры, приставки и программы</li>
	<li>Оргтехника и расходники</li>
	<li>Планшеты и электронные книги</li>
	<li>Ноутбуки</li>
	<li>Настольные компьютеры</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_GOODS_TYPE_NAME'] = 'Вид товара';
	$MESS[$strMessPrefix.'FIELD_GOODS_TYPE_DESC'] = 'Вид товара — одно из значений списка (отдельно для каждой категории):
<ul>
<li>Для категории «Телефоны»:<br/>
Acer, Alcatel, ASUS, BlackBerry, BQ, DEXP, Explay, Fly, Highscreen, HTC, Huawei, iPhone, Lenovo, LG, Meizu, Micromax, Microsoft, Motorola, MTS, Nokia, Panasonic, Philips, Prestigio, Samsung, Siemens, SkyLink, Sony, teXet, Vertu, Xiaomi, ZTE, Другие марки, Рации, Стационарные телефоны, Аккумуляторы, Гарнитуры и наушники, Зарядные устройства, Кабели и адаптеры, Модемы и роутеры, Запчасти, Чехлы и плёнки</li>
<li>Для категории «Аудио и видео»:
MP3-плееры, Акустика, колонки, сабвуферы, Видео, DVD и Blu-ray плееры, Видеокамеры, Кабели и адаптеры, Микрофоны, Музыка и фильмы, Музыкальные центры, магнитолы, Наушники, Телевизоры и проекторы, Усилители и ресиверы, Аксессуары</li>
<li>Для категории «Товары для компьютера»:
Акустика, Веб-камеры, Джойстики и рули, Клавиатуры и мыши, CD, DVD и Blu-ray приводы, Блоки питания, Видеокарты, Жёсткие диски, Звуковые карты, Контроллеры, Корпусы, Материнские платы, Оперативная память, Процессоры, Системы охлаждения, Мониторы, Переносные жёсткие диски, Сетевое оборудование, ТВ-тюнеры, Флэшки и карты памяти, Аксессуары</li>
<li>Для категории «Фототехника»:
Компактные фотоаппараты, Зеркальные фотоаппараты, Плёночные фотоаппараты, Бинокли и телескопы, Объективы, Оборудование и аксессуары</li>
<li>Для категории «Игры, приставки и программы»:
Игровые приставки, Игры для приставок, Программы, Компьютерные игры</li>
<li>Для категории «Оргтехника и расходники»:
МФУ, копиры и сканеры, Принтеры, Телефония, ИБП, сетевые фильтры, Уничтожители бумаг, Блоки питания и батареи, Болванки, Бумага, Кабели и адаптеры, Картриджи, Канцелярия</li>
<li>Для категории «Планшеты и электронные книги»:
Планшеты, Электронные книги, Аккумуляторы, Гарнитуры и наушники, Док-станции, Зарядные устройства, Кабели и адаптеры, Модемы и роутеры, Стилусы, Чехлы и плёнки, Другое</li>
<li>Для категории «Ноутбуки»:
Acer, Apple, ASUS, Compaq, Dell, Fujitsu, HP, Lenovo, MSI, Microsoft, Samsung, Sony, Toshiba, Packard Bell, Другой</li>
</ul>
Примечание: для категории «Настольные компьютеры» вид товара указывать не нужно.';
$MESS[$strMessPrefix.'FIELD_PRODUCTS_TYPE_NAME'] = 'Тип товара';
	$MESS[$strMessPrefix.'FIELD_PRODUCTS_TYPE_DESC'] = 'Тип товара - одно из значений списка:
<ul>
	<li>Телевизоры</li>
	<li>Проекторы</li>
	<li>Другое</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_PRODUCTS_TYPE_MFD_NAME'] = 'Тип товара (Оргтехника)';
	$MESS[$strMessPrefix.'FIELD_PRODUCTS_TYPE_MFD_DESC'] = 'Тип товара (Оргтехника) - одно из значений списка:
<ul>
	<li>МФУ</li>
	<li>Копиры</li>
	<li>Сканеры</li>
	<li>Другое</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_TITLE_NAME'] = 'Название объявления';
	$MESS[$strMessPrefix.'FIELD_TITLE_DESC'] = 'Название объявления — строка до 50 символов.<br/><br/>
Примечание: не пишите в название цену и контактную информацию — для этого есть отдельные поля — и не используйте слово «продам».';
$MESS[$strMessPrefix.'FIELD_PRICE_NAME'] = 'Цена в рублях';
	$MESS[$strMessPrefix.'FIELD_PRICE_DESC'] = 'Цена в рублях — целое число. ';

$MESS[$strMessPrefix.'HEADER_MOBILE'] = 'Характеристики телефонов';

$strPhoneRef = '<br/><br/><a href="http://autoload.avito.ru/format/phone_catalog.xml" target="_blank">Справочник «Мобильные телефоны»</a>';
$MESS[$strMessPrefix.'FIELD_PHONE_VENDOR_NAME'] = 'Производитель';
	$MESS[$strMessPrefix.'FIELD_PHONE_VENDOR_DESC'] = 'Производитель телефона.'.$strPhoneRef;
$MESS[$strMessPrefix.'FIELD_PHONE_MODEL_NAME'] = 'Модель';
	$MESS[$strMessPrefix.'FIELD_PHONE_MODEL_DESC'] = 'Модель телефона конкретного бренда.'.$strPhoneRef;
$MESS[$strMessPrefix.'FIELD_PHONE_COLOR_NAME'] = 'Цвет';
	$MESS[$strMessPrefix.'FIELD_PHONE_COLOR_DESC'] = 'Цвет телефона.'.$strPhoneRef;
$MESS[$strMessPrefix.'FIELD_MEMORY_SIZE_NAME'] = 'Встроенная память';
	$MESS[$strMessPrefix.'FIELD_MEMORY_SIZE_DESC'] = 'Встроенная память телефона (размер встроенного хранилища).'.$strPhoneRef;
$MESS[$strMessPrefix.'FIELD_RAM_SIZE_NAME'] = 'Оперативная память.';
	$MESS[$strMessPrefix.'FIELD_RAM_SIZE_DESC'] = 'Оперативная память телефона.'.$strPhoneRef;
$MESS[$strMessPrefix.'FIELD_IMEI_NAME'] = 'IMEI';
	$MESS[$strMessPrefix.'FIELD_IMEI_DESC'] = 'Международный идентификатор мобильного оборудования (International Mobile Equipment Identity).<br/><br/>
	Значение – 15 цифр. Если на вашем устройстве два номера, укажите IMEI1.';
$MESS[$strMessPrefix.'FIELD_IMEI1_NAME'] = 'IMEI (дополнительный)';
	$MESS[$strMessPrefix.'FIELD_IMEI1_DESC'] = 'Международный идентификатор мобильного оборудования (International Mobile Equipment Identity).<br/><br/>
	Укажите здесь второй IMEI (если имеется).	Значение – 15 цифр.';
#
$MESS[$strMessPrefix.'FIELD_BRAND_NAME'] = 'Бренд';
	$MESS[$strMessPrefix.'FIELD_BRAND_DESC'] = '<br/><br/>Актуально для категорий: <p>Бренд.</p><p>Значение – текст внутри &lt;Brand&gt; из соответствующего набора характеристик, заданного в &lt;Gpus&gt; (см. справочник "Видеокарты").</p>';
$MESS[$strMessPrefix.'FIELD_PRODUCER_CODE_NAME'] = 'Код производителя';
	$MESS[$strMessPrefix.'FIELD_PRODUCER_CODE_DESC'] = '<br/><br/>Актуально для категорий: Код производителя<br><p>Значение – текст внутри &lt;ProducerCode&gt; из соответствующего набора характеристик, заданного в &lt;Gpus&gt; (см. справочник "Видеокарты").</p>';
$MESS[$strMessPrefix.'FIELD_SIM_SLOT_NAME'] = 'Слот для сим-карты';
	$MESS[$strMessPrefix.'FIELD_SIM_SLOT_DESC'] = '<br/><br/>Актуально для категорий: Слот для сим-карты.<br><p>Значение – текст внутри &lt;SimSlot&gt; из соответствующего набора характеристик, заданного в &lt;Tablets&gt; (см. справочник "Планшеты").</p>';
?>