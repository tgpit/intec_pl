<?
$strMessPrefix = 'ACRIT_EXP_AVITO_PARTS_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Авито (Запчасти и аксессуары)';

// Headers
$MESS[$strMessPrefix.'HEADER_TIRES'] = 'Параметры шин, дисков и колёс';

// Fields
$MESS[$strMessPrefix.'FIELD_CATEGORY_NAME'] = 'Категория товара';
	$MESS[$strMessPrefix.'FIELD_CATEGORY_DESC'] = 'Категория объявлений — строка: «Запчасти и аксессуары».';
	$MESS[$strMessPrefix.'FIELD_CATEGORY_DEFAULT'] = 'Запчасти и аксессуары';
$MESS[$strMessPrefix.'FIELD_TYPE_ID_NAME'] = 'Подкатегория товара';
	$MESS[$strMessPrefix.'FIELD_TYPE_ID_DESC'] = 'Подкатегория товара — цифровой идентификатор из <a href="http://autoload.avito.ru/format/zapchasti_i_aksessuary/#TypeId" target="_blank">списка</a>';
$MESS[$strMessPrefix.'FIELD_AD_TYPE_NAME'] = 'Вид объявления';
	$MESS[$strMessPrefix.'FIELD_AD_TYPE_DESC'] = 'Вид объявления — одно из значений списка:<br/>
<ul>
	<li>Товар приобретен на продажу</li>
	<li>Товар от производителя</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_CONDITION_NAME'] = 'Состояние';
	$MESS[$strMessPrefix.'FIELD_CONDITION_DESC'] = 'Состояние — одно из значений списка:
<ul>
	<li>Новое</li>
	<li>Б/у</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_OEM_NAME'] = 'Номер детали OEM';
	$MESS[$strMessPrefix.'FIELD_OEM_DESC'] = 'Строка до 50 символов (разрешены цифры, латиница и знак дефиса).<br/>
Элемент может быть использован в подкатегориях:
<ul>
	<li>Запчасти / Для автомобилей</li>
	<li>Запчасти / Для мототехники</li>
	<li>Запчасти / Для спецтехники</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_OEM_NAME'] = 'Номер детали OEM';
	$MESS[$strMessPrefix.'FIELD_OEM_DESC'] = 'Номер детали OEM — строка до 50 символов (разрешены цифры, латиница и знак дефиса).<br/>
Элемент может быть использован в подкатегориях:
<ul>
	<li>Запчасти / Для автомобилей</li>
	<li>Запчасти / Для мототехники</li>
	<li>Запчасти / Для спецтехники</li>
</ul>';
	
$MESS[$strMessPrefix.'FIELD_RIM_DIAMETER_NAME'] = 'Диаметр, дюймы';
	$MESS[$strMessPrefix.'FIELD_RIM_DIAMETER_DESC'] = 'Диаметр, дюймы — десятичное число.';
$MESS[$strMessPrefix.'FIELD_TIRE_TYPE_NAME'] = 'Сезонность шин или колес';
	$MESS[$strMessPrefix.'FIELD_TIRE_TYPE_DESC'] = 'Сезонность шин или колес — одно из значений списка:
<ul>
	<li>Всесезонные</li>
	<li>Летние</li>
	<li>Зимние нешипованные</li>
	<li>Зимние шипованные</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_WHEEL_AXLE_NAME'] = 'Ось мотошины';
	$MESS[$strMessPrefix.'FIELD_WHEEL_AXLE_DESC'] = 'Ось мотошины — одно из значений списка:
<ul>
	<li>Задняя</li>
	<li>Любая</li>
	<li>Передняя</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_RIM_TYPE_NAME'] = 'Тип диска';
	$MESS[$strMessPrefix.'FIELD_RIM_TYPE_DESC'] = 'Тип диска — одно из значений списка (отдельно для каждой категории):
<ul>
	<li>Шины, диски и колёса / Диски:
		<ul>
			<li>Кованые</li>
			<li>Литые</li>
			<li>Штампованные</li>
			<li>Спицованные</li>
			<li>Сборные</li>
		</ul>
	</li>
	<li>Шины, диски и колёса / Колёса:
		<ul>
			<li>Кованые</li>
			<li>Литые</li>
			<li>Штампованные</li>
		</ul>
	</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_TIRE_SECTION_WIDTH_NAME'] = 'Ширина профиля шины';
	$MESS[$strMessPrefix.'FIELD_TIRE_SECTION_WIDTH_DESC'] = 'Ширина профиля шины — целое число.';
$MESS[$strMessPrefix.'FIELD_TIRE_ASPECT_RATIO_NAME'] = 'Высота профиля шины';
	$MESS[$strMessPrefix.'FIELD_TIRE_ASPECT_RATIO_DESC'] = 'Высота профиля шины — целое число.';
$MESS[$strMessPrefix.'FIELD_RIM_WIDTH_NAME'] = 'Ширина обода, дюймов';
	$MESS[$strMessPrefix.'FIELD_RIM_WIDTH_DESC'] = 'Ширина обода, дюймов — десятичное число.';
$MESS[$strMessPrefix.'FIELD_RIM_BOLTS_NAME'] = 'Количество отверстий под болты';
	$MESS[$strMessPrefix.'FIELD_RIM_BOLTS_DESC'] = 'Количество отверстий под болты — целое число.';
$MESS[$strMessPrefix.'FIELD_RIM_BOLTS_DIAMETER_NAME'] = 'Диаметр расположения отверстий под болты';
	$MESS[$strMessPrefix.'FIELD_RIM_BOLTS_DIAMETER_DESC'] = 'Диаметр расположения отверстий под болты — десятичное число.';
$MESS[$strMessPrefix.'FIELD_RIM_OFFSET_NAME'] = 'Вылет (ET)';
	$MESS[$strMessPrefix.'FIELD_RIM_OFFSET_DESC'] = 'Вылет (ET) — десятичное число.';
$MESS[$strMessPrefix.'FIELD_RUNFLAT_NAME'] = 'RunFlat - наличие технологии RunFlat';
	$MESS[$strMessPrefix.'FIELD_RUNFLAT_DESC'] = 'Наличие технологии RunFlat
	<ul>
		<li>Да</li>
		<li>Нет</li>
	</ul>';
$MESS[$strMessPrefix.'FIELD_HOMOLOGATION_NAME'] = 'Омологация';
	$MESS[$strMessPrefix.'FIELD_HOMOLOGATION_DESC'] = 'Омологация - бренд авто, под который омологирована шина';
$MESS[$strMessPrefix.'FIELD_MODEL_NAME'] = 'Название модели шины';
	$MESS[$strMessPrefix.'FIELD_MODEL_DESC'] = 'Название модели шины';
$MESS[$strMessPrefix.'FIELD_SPEED_INDEX_NAME'] = 'Индекс скорости шины';
	$MESS[$strMessPrefix.'FIELD_SPEED_INDEX_DESC'] = 'Индекс скорости шины - одно из значенией списка:
	<ul>
		<li>B</li>
		<li>C</li>
		<li>D</li>
		<li>E</li>
		<li>F</li>
		<li>G</li>
		<li>J</li>
		<li>K</li>
		<li>L</li>
		<li>M</li>
		<li>N</li>
		<li>P</li>
		<li>Q</li>
		<li>R</li>
		<li>S</li>
		<li>T</li>
		<li>U</li>
		<li>H</li>
		<li>VR</li>
		<li>V</li>
		<li>Z, ZR</li>
		<li>W</li>
		<li>Y</li>
	</ul>';
$MESS[$strMessPrefix.'FIELD_LOAD_INDEX_NAME'] = 'Индекс нагрузки шины';
	$MESS[$strMessPrefix.'FIELD_LOAD_INDEX_DESC'] = 'Индекс нагрузки шины. Может принимать значения от 19 до 204 включительно.';
$MESS[$strMessPrefix.'FIELD_BRAND_NAME'] = 'Производитель шин';
	$MESS[$strMessPrefix.'FIELD_BRAND_DESC'] = 'Производитель шин - строка из списка:
Aeolus, Aeolus Neo, Altenzo, Amtel, Antares, Aplus, Autogrip, Avatyre, Barum, Bfgoodrich, Bridgestone, Cachland, Compasal, Continental, Contyre, Cordiant, CrossLeader, Delinte, Dmack, DoubleStar, Dunlop, Effiplus, Falken, Firenza, Firestone, Forward, General Tire, Gislaved, Goodyear, GT Radial, Habilead, Hankook, Hifly, Imperial, Jinyu, Joyroad, Kama, Kleber, Kormoran, Kumho, Landsail, Laufenn, LingLong, Marshal, Matador, Maxxis, Michelin, Mickey Thompson, Minerva, Nankang, Nexen, Nitto, Nokian, Nordman, Nortec, Orium, Ovation, Pirelli, Pirelli Formula, Rapid, Roadstone, Rosava, Sailun, Satoya, Sava, Starmaxx, Sunfull, Sunny, Tigar, Toyo, Trayal, Triangle, Tunga, Tyrex, Uniroyal, Viatti, Vredestein, Windforce, Yokohama, Белшина, Волтайр.';
$MESS[$strMessPrefix.'FIELD_ORIGINALITY_NAME'] = 'Тип Запчасти';
	$MESS[$strMessPrefix.'FIELD_ORIGINALITY_DESC'] = 'Тип Запчасти — определяет оригинальность запчасти. Может принимать следующие значения:
<ul>
	<li>Оригинал</li>
	<li>Аналог</li>
	<li>Не знаю</li>
</ul>
Элемент может быть использован в подкатегориях:
<ul>
	<li>Запчасти / Для автомобилей</li>
	<li>Запчасти / Для мототехники</li>
	<li>Запчасти / Для спецтехники</li>
</ul>
Пример: Аналог';
$MESS[$strMessPrefix.'FIELD_ORIGINAL_OEM_NAME'] = 'Номер оригинальной детали OEM';
	$MESS[$strMessPrefix.'FIELD_ORIGINAL_OEM_DESC'] = 'Номер оригинальной детали OEM — строка до 50 символов (разрешены цифры, латиница и знак дефиса).<br/><br/>
	Используется для указания номера оригинальной запчасти, если он известен. Поле доступно для ввода при указании параметра "Тип Запчасти" со значением "Аналог". <br/><br/>
	Элемент может быть использован в подкатегориях:
	<ul>
		<li>Запчасти / Для автомобилей</li>
		<li>Запчасти / Для мототехники</li>
		<li>Запчасти / Для спецтехники</li>
	</ul>
	Пример: 03C121008F';
$MESS[$strMessPrefix.'FIELD_ORIGINAL_VENDOR_NAME'] = 'Производитель оригинальной запчасти';
	$MESS[$strMessPrefix.'FIELD_ORIGINAL_VENDOR_DESC'] = 'Используется для указания производителя оригинальной запчасти, если он известен. Поле доступно для ввода при указании параметра "Тип Запчасти" со значением "Аналог". <br/><br/>
	Элемент может быть использован в подкатегориях:
	<ul>
		<li>Запчасти / Для автомобилей</li>
		<li>Запчасти / Для мототехники</li>
		<li>Запчасти / Для спецтехники</li>
	</ul>
	Пример: BMW';
$MESS[$strMessPrefix.'FIELD_RESIDUAL_TREAD_NAME'] = 'Остаточная глубина протектора шины, мм';
	$MESS[$strMessPrefix.'FIELD_RESIDUAL_TREAD_DESC'] = 'Остаточная глубина протектора шины. Может принимать значения от 1 до 50 включительно, измеряется в миллиметрах (мм)';
$MESS[$strMessPrefix.'FIELD_DISPLAY_AREAS_NAME'] = 'Зоны показа объявления';
	$MESS[$strMessPrefix.'FIELD_DISPLAY_AREAS_DESC'] = 'Зоны показа объявления - значения из <a href="https://autoload.avito.ru/format/DisplayAreas.xml" target="_blank">справочника</a>.<br/><br/>
	Поле должно выгружаться как множественное!<br/><br/>
	<b>Внимание, данная функциональность Avito находится на стадии тестирования.</b>';
$MESS[$strMessPrefix.'FIELD_AVAILABILITY_NAME'] = 'Доступность';
	$MESS[$strMessPrefix.'FIELD_AVAILABILITY_DESC'] = 'Доступность — одно из значений списка:
	<ul>
		<li>В наличии</li>
		<li>Под заказ</li>
	</ul>';
	$MESS[$strMessPrefix.'FIELD_AVAILABILITY_IN'] = 'В наличии';
#
$MESS[$strMessPrefix.'FIELD_GOODS_TYPE_NAME'] = 'Вид товара';
	$MESS[$strMessPrefix.'FIELD_GOODS_TYPE_DESC'] = '<p>Вид товара — альтернатива <a href="https://autoload.avito.ru/format/zapchasti_i_aksessuary/#TypeId" rel="nofollow" target="_blank">TypeId</a></p><p>Одно из значений списка:</p><ul><li>Запчасти</li><li>Аксессуары</li><li>GPS-навигаторы</li><li>Автокосметика и автохимия</li><li>Аудио- и видеотехника</li><li>Багажники и фаркопы</li><li>Инструменты</li><li>Прицепы</li><li>Противоугонные устройства</li><li>Тюнинг</li><li>Шины, диски и колёса</li><li>Экипировка</li></ul><p><br></p><br/><br/>Актуально для категорий: <p>Обязательный, если не указан <a href="https://autoload.avito.ru/format/zapchasti_i_aksessuary/#TypeId" rel="nofollow" target="_blank">TypeId</a></p>';
$MESS[$strMessPrefix.'FIELD_PRODUCT_TYPE_NAME'] = 'Тип товара';
	$MESS[$strMessPrefix.'FIELD_PRODUCT_TYPE_DESC'] = '<p>Тип товара</p><p>Одно из значений списка.</p><p>Если <a href="https://autoload.avito.ru/format/zapchasti_i_aksessuary/#GoodsType" rel="nofollow" target="_blank">GoodsType</a> = «Запчасти»:</p><ul><li>Для автомобилей</li><li>Для мототехники</li><li>Для спецтехники</li><li>Для водного транспорта</li></ul><p>Если <a href="https://autoload.avito.ru/format/zapchasti_i_aksessuary/#GoodsType" rel="nofollow" target="_blank">GoodsType</a> = «Шины, диски и колёса»:</p><ul><li>Шины</li><li>Мотошины</li><li>Диски</li><li>Колёса</li><li>Колпаки</li></ul><p><br></p><br/><br/>Актуально для категорий: Обязательный, <br>если <a href="https://autoload.avito.ru/format/zapchasti_i_aksessuary/#GoodsType" rel="nofollow" target="_blank">GoodsType</a> = «Запчасти» или «Шины, диски и колёса»';
$MESS[$strMessPrefix.'FIELD_DEVICE_TYPE_NAME'] = 'Вид устройства';
	$MESS[$strMessPrefix.'FIELD_DEVICE_TYPE_DESC'] = '<p>Вид устройства</p><p>Одно из значений списка:</p><ul><li>Автосигнализации</li><li>Иммобилайзеры</li><li>Механические блокираторы</li><li>Спутниковые системы</li></ul><p><br></p><br/><br/>Актуально для категорий: Обязательный, <br>если <a href="https://autoload.avito.ru/format/zapchasti_i_aksessuary/#GoodsType" rel="nofollow" target="_blank">GoodsType</a> = «Противоугонные устройства»';
$MESS[$strMessPrefix.'FIELD_SPARE_PART_TYPE_NAME'] = 'Вид запчасти';
	$MESS[$strMessPrefix.'FIELD_SPARE_PART_TYPE_DESC'] = '<p>Вид запчасти</p><p>Элемент может быть использован в подкатегориях:</p><ul><li>Запчасти / Для спецтехники</li><li>Запчасти / Для автомобилей</li></ul><p>В подкатегории "Запчасти / Для автомобилей" может принимать следующие значения:</p><ul><li>Автосвет</li><li>Автомобиль на запчасти</li><li>Аккумуляторы</li><li>Двигатель</li><li>Запчасти для ТО</li><li>Кузов</li><li>Подвеска</li><li>Рулевое управление</li><li>Салон</li><li>Система охлаждения</li><li>Стекла</li><li>Топливная и выхлопная системы</li><li>Тормозная система</li><li>Трансмиссия и привод</li><li>Электрооборудование</li></ul><p>В подкатегории "Запчасти / Для спецтехники" может принимать следующие значения:</p><ul><li>Двигатели и комплектующие</li><li>Трансмиссия</li><li>Подвеска</li><li>Кабина</li><li>Будки, платформы, кузова</li><li>Автоэлектрика и автосвет</li><li>Гидравлические и пневмосистемы</li></ul><p>Если параметр не указан в файле, значение определяется автоматически по названию объявления.</p><br/><br/>Актуально для категорий: Обязательный, если <a href="https://autoload.avito.ru/format/zapchasti_i_aksessuary/#ProductType" rel="nofollow" target="_blank">ProductType</a> = «Для автомобилей»';
$MESS[$strMessPrefix.'FIELD_TECHNIC_SPARE_PART_TYPE_NAME'] = 'Запчасти / Для спецтехники';
	$MESS[$strMessPrefix.'FIELD_TECHNIC_SPARE_PART_TYPE_DESC'] = 'Тип&nbsp;детали спецтехники. Элемент может быть использован в подкатегориях:<ul><li>Запчасти / Для спецтехники</li></ul><p>Может принимать следующие значения в зависимости от подкатегории:</p><ul><li>Двигатели и комплектующие<ul><li>Двигатели в сборе</li><li>Двигатели в разборе</li><li>Навесное оборудование</li><li>Топливная система</li><li>Система смазки</li><li>Система охлаждения</li><li>Системы впуска и выпуска</li></ul></li><li>Трансмиссия<ul><li>КПП в сборе</li><li>КПП в разборе</li><li>Сцепление</li><li>Механизмы отбора мощности</li><li>Карданные валы</li><li>Редукторы и комплектующие</li></ul></li><li>Подвеска<ul><li>Рулевое управление</li><li>Балки в сборе</li><li>Балки в разборе</li><li>Мосты в сборе</li><li>Мосты в разборе</li><li>Амортизаторы и пружины</li><li>Рессоры и листы</li><li>Балансиры, буксы</li><li> Тормозная система</li><li>Пальцы, втулки, наконечники </li></ul></li><li>Кабина<ul><li>Кабины в сборе </li><li>Кабины в разборе</li><li>Экстерьер</li><li>Рамы и детали рам</li><li>Стекла</li></ul></li><li>Автоэлектрика и автосвет<ul><li>Аккумуляторы</li><li>Автосвет</li><li> Контроллеры, реле, датчики</li><li>Блоки управления</li><li>Электропроводка</li></ul></li><li>Гидравлические и пневмосистемы<ul><li>Пневматика тормозной системы</li><li>Пневматика сцепления</li><li>Пневмоподвеска</li><li>Шланги, трубки, патрубки</li><li>Компрессоры</li><li>Ресиверы, воздухоподготовка</li><li>Расширительные бачки</li><li>Система подъёма кузова</li></ul></li></ul><br/><br/>Актуально для категорий: <br>';
$MESS[$strMessPrefix.'FIELD_ENGINE_SPARE_PART_TYPE_NAME'] = 'Тип детали двигателя';
	$MESS[$strMessPrefix.'FIELD_ENGINE_SPARE_PART_TYPE_DESC'] = '<p>Тип детали двигателя</p><p>Одно из значений списка:</p><ul><li>Блок цилиндров, головка, картер</li><li>Вакуумная система</li><li>Генераторы, стартеры</li><li>Двигатель в сборе</li><li>Катушка зажигания, свечи, электрика</li><li>Клапанная крышка</li><li>Коленвал, маховик</li><li>Коллекторы</li><li>Крепление двигателя</li><li>Масляный насос, система смазки</li><li>Патрубки вентиляции</li><li>Поршни, шатуны, кольца</li><li>Приводные ремни, натяжители</li><li>Прокладки и ремкомплекты</li><li>Ремни, цепи, элементы ГРМ</li><li>Турбины, компрессоры</li><li>Электродвигатели и компоненты</li></ul><p>Если параметр не указан в файле, значение определяется автоматически по названию объявления.</p><br/><br/>Актуально для категорий: Обязательный, если <a href="https://autoload.avito.ru/format/zapchasti_i_aksessuary/#SparePartType" rel="nofollow" target="_blank">SparePartType</a> = «Двигатель»';
$MESS[$strMessPrefix.'FIELD_BODY_SPARE_PART_TYPE_NAME'] = 'Тип детали кузова';
	$MESS[$strMessPrefix.'FIELD_BODY_SPARE_PART_TYPE_DESC'] = '<p>Тип детали кузова</p><p>Одно из значений списка:</p><ul><li>??Балки, лонжероны</li><li>Бамперы</li><li>Брызговики</li><li>Двери</li><li>Заглушки</li><li>Замки</li><li>Защита</li><li>Зеркала</li><li>Кабина</li><li>Капот</li><li>Крепления</li><li>Крылья</li><li>Крыша</li><li>Крышка, дверь багажника</li><li>Кузов по частям</li><li>Кузов целиком</li><li>Лючок бензобака</li><li>Молдинги, накладки</li><li>Пороги</li><li>Рама</li><li>Решетка радиатора</li><li>Стойка кузова</li></ul><p>Если параметр не указан в файле, значение определяется автоматически по названию объявления.</p><br/><br/>Актуально для категорий: Обязательный, если&nbsp;<a href="https://autoload.avito.ru/format/zapchasti_i_aksessuary/#SparePartType" rel="nofollow" target="_blank">SparePartType</a> = «Кузов»';
$MESS[$strMessPrefix.'FIELD_TECHNIC_NAME'] = 'Тип спецтехники. Может принимать следующие значения';
	$MESS[$strMessPrefix.'FIELD_TECHNIC_DESC'] = '<p>Тип спецтехники. Может принимать следующие значения:</p><ul><li>Автобусы</li><li>Автодома</li><li>Автокраны</li><li>Бульдозеры</li><li>Грузовики</li><li>Погрузчики</li><li>Прицепы</li><li>Сельхозтехника</li><li>Строительная техника</li><li>Тягачи</li><li>Экскаваторы</li></ul><br/><br/>Актуально для категорий: <br>';
$MESS[$strMessPrefix.'FIELD_MAKE_NAME'] = 'Элемент может быть использован в подкатегориях';
	$MESS[$strMessPrefix.'FIELD_MAKE_DESC'] = '<p>Элемент может быть использован в подкатегориях:</p><ul><li>Запчасти / Для автомобилей</li></ul><p>Марка автомобиля — в соответствии со значениями из <a href="http://autoload.avito.ru/format/Autocatalog.xml" rel="nofollow" target="_blank">Справочника</a>.</p><ul><li>Запчасти / Для спецтехники</li></ul><p>Марка спецтехники — в соответствии со значениями из справочника:</p><ul><li><a href="https://autoload.avito.ru/format/bus.xml" rel="nofollow" target="_blank">Автобусы</a></li><li><a href="https://autoload.avito.ru/format/motorhome.xml" rel="nofollow" target="_blank">Автодома</a></li><li><a href="https://autoload.avito.ru/format/autocrane.xml" rel="nofollow" target="_blank">Автокраны</a></li><li><a href="https://autoload.avito.ru/format/bulldozer.xml" rel="nofollow" target="_blank">Бульдозеры</a></li><li><a href="https://autoload.avito.ru/format/truck_catalog.xml" rel="nofollow" target="_blank">Грузовики</a></li><li><a href="https://autoload.avito.ru/format/loader.xml" rel="nofollow" target="_blank">Погрузчики</a></li><li><a href="https://autoload.avito.ru/format/trailer_catalog.xml" rel="nofollow" target="_blank">Прицепы</a></li><li><a href="https://autoload.avito.ru/format/agricultural_machinery.xml" rel="nofollow" target="_blank">Сельхозтехника</a></li><li><a href="https://autoload.avito.ru/format/construction_machinery.xml" rel="nofollow" target="_blank">Строительная техника</a></li><li><a href="https://autoload.avito.ru/format/cab_catalog.xml" rel="nofollow" target="_blank">Тягачи</a></li><li><a href="https://autoload.avito.ru/format/excavator.xml" rel="nofollow" target="_blank">Экскаваторы</a></li></ul><br/><br/>Актуально для категорий: <br>';
$MESS[$strMessPrefix.'FIELD_GENERATION_NAME'] = 'Элемент может быть использован в подкатегориях';
	$MESS[$strMessPrefix.'FIELD_GENERATION_DESC'] = '<p>Элемент может быть использован в подкатегориях:</p><ul><li>Запчасти / Для автомобилей</li></ul><p>Поколение&nbsp;автомобиля — в соответствии со значениями из <a href="http://autoload.avito.ru/format/Autocatalog.xml" rel="nofollow" target="_blank">Справочника</a>.</p><br/><br/>Актуально для категорий: <br>';
$MESS[$strMessPrefix.'FIELD_MODIFICATION_NAME'] = 'Элемент может быть использован в подкатегориях';
	$MESS[$strMessPrefix.'FIELD_MODIFICATION_DESC'] = '<p>Элемент может быть использован в подкатегориях:</p><ul><li>Запчасти / Для автомобилей</li></ul><p>Модификация автомобиля — в соответствии со значениями из <a href="http://autoload.avito.ru/format/Autocatalog.xml" rel="nofollow" target="_blank">Справочника</a>.</p><br/><br/>Актуально для категорий: <br>';
$MESS[$strMessPrefix.'FIELD_BODY_TYPE_NAME'] = 'Элемент может быть использован в подкатегориях';
	$MESS[$strMessPrefix.'FIELD_BODY_TYPE_DESC'] = '<p>Элемент может быть использован в подкатегориях:</p><ul><li>Запчасти / Для автомобилей</li></ul><p>Тип кузова автомобиля — в соответствии со значениями из <a href="http://autoload.avito.ru/format/Autocatalog.xml" rel="nofollow" target="_blank">Справочника</a>.</p><br/><br/>Актуально для категорий: <br>';
$MESS[$strMessPrefix.'FIELD_DOORS_NAME'] = 'Элемент может быть использован в подкатегориях';
	$MESS[$strMessPrefix.'FIELD_DOORS_DESC'] = '<p>Элемент может быть использован в подкатегориях:</p><ul><li>Запчасти / Для автомобилей</li></ul><p>Количество дверей автомобиля — в соответствии со значениями из <a href="http://autoload.avito.ru/format/Autocatalog.xml" rel="nofollow" target="_blank">Справочника</a>.</p><br/><br/>Актуально для категорий: <br>';
$MESS[$strMessPrefix.'FIELD_DIFFERENT_WIDTH_TIRES_NAME'] = 'Разноширокий комплект шин. Возможные значения';
	$MESS[$strMessPrefix.'FIELD_DIFFERENT_WIDTH_TIRES_DESC'] = '<p>Разноширокий комплект шин. Возможные значения:</p><ul><li>Да</li><li>Нет</li></ul><br/><br/>Актуально для категорий: Шины, диски и колёса / Шины';
$MESS[$strMessPrefix.'FIELD_BACK_RIM_DIAMETER_NAME'] = 'Диаметр задней оси, дюймы';
	$MESS[$strMessPrefix.'FIELD_BACK_RIM_DIAMETER_DESC'] = '<p>Диаметр задней оси, дюймы — десятичное число. Обязательно при указании значения «Да» для&nbsp;DifferentWidthTires.</p><br/><br/>Актуально для категорий: Шины, диски и колёса / Шины**';
$MESS[$strMessPrefix.'FIELD_BACK_TIRE_SECTION_WIDTH_NAME'] = 'Ширина профиля шины задней оси. Список возможных значений такой же, как для TireSectionWidth. Обязательно при указании значения «Да» для DifferentWidthTires.';
	$MESS[$strMessPrefix.'FIELD_BACK_TIRE_SECTION_WIDTH_DESC'] = '<p>Ширина профиля шины задней оси. Список возможных значений такой же, как для&nbsp;TireSectionWidth.&nbsp;Обязательно при указании значения «Да» для&nbsp;DifferentWidthTires.</p><br/><br/>Актуально для категорий: Шины, диски и колёса / Шины**';
$MESS[$strMessPrefix.'FIELD_BACK_TIRE_ASPECT_RATIO_NAME'] = 'Высота профиля шины задней оси. Возможные значения такие же, как и для TireAspectRatio. Обязательно при указании значения «Да» для DifferentWidthTires.';
	$MESS[$strMessPrefix.'FIELD_BACK_TIRE_ASPECT_RATIO_DESC'] = '<p>Высота профиля шины задней оси. Возможные значения такие же, как и для&nbsp;TireAspectRatio.&nbsp;Обязательно при указании значения «Да» для&nbsp;DifferentWidthTires.</p><br/><br/>Актуально для категорий: Шины, диски и колёса / Шины**';
$MESS[$strMessPrefix.'FIELD_RIM_DIA_NAME'] = 'Диаметр центрального отверстия - десятичное число.';
	$MESS[$strMessPrefix.'FIELD_RIM_DIA_DESC'] = '<p>Диаметр центрального отверстия - десятичное число.</p><br/><br/>Актуально для категорий: Шины, диски и колёса / Диски';
$MESS[$strMessPrefix.'FIELD_QUANTITY_NAME'] = 'Количество шт. в комплекте';
	$MESS[$strMessPrefix.'FIELD_QUANTITY_DESC'] = '<p>Количество шт. в комплекте</p><ul><li>1</li><li>2</li><li>3</li><li>4</li><li>5</li><li>6</li><li>7</li><li>8</li></ul><p><br></p><br/><br/>Актуально для категорий: Шины, диски и колёса / Шины';

	
?>