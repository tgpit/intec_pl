<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);
$strSName = $strLang.'SETTINGS_NAME_';
$strSHint = $strLang.'SETTINGS_HINT_';

# General
$MESS[$strLang.'NAME'] = 'Wildberries.ru (API) - Карточка товара NEW';
$MESS[$strLang.'TEST_VERSION'] = '<b>Внимание!</b> Данная выгрузка находится в тестовом режиме. Некоторые нюансы API Wildberries разработчики могут изменить в любое время без дополнительных уведомлений. При возникновении проблем обращайтесь в нашу <a href="/bitrix/admin/acrit_exportproplus_new_support.php?lang=ru&AcritExpSupport_active_tab=ask" target="_blank">техподдержку</a>.';
$MESS[$strLang.'NOTICE_SUPPORT'] = '<b>Внимание!</b> На данный формат выгрузки не распространяются условия бесплатной техподдержки. Помощь в настройке осуществляется <a href="/bitrix/admin/acrit_exportproplus_new_support.php?lang=ru&AcritExpSupport_active_tab=ask" target="_blank">на платной основе</a>.';
$MESS[$strLang.'NOTICE_PREVIEW_STOCK_AND_PRICE'] = '<b>Внимание!</b> Предпросмотр носит служебный характер, данные по карточке не будут отправлены на Wildberries, т.к. в профиле включен «Режим выгрузки только цен и остатков».';

# Steps
$MESS[$strLang.'STEP_EXPORT_PRICES'] = 'Выгрузка цен';
$MESS[$strLang.'STEP_EXPORT_STOCKS'] = 'Выгрузка остатков';

# Settings
$MESS[$strSName.'AUTHORIZATION'] = 'Токен авторизации';
	$MESS[$strSHint.'AUTHORIZATION'] = 'Укажите здесь единый токен авторизации, получить его можно на странице <a href="https://seller.wildberries.ru/supplier-settings/access-to-new-api" target="_blank">личного кабинета</a>.';
		$MESS[$strLang.'TOKEN_CHECK'] = 'Проверить токен';

# Settings: stocks
$MESS[$strLang.'EXPORT_STOCKS_CHECKBOX'] = 'Выгружать остатки';
	$MESS[$strLang.'EXPORT_STOCKS_HINT'] = 'Отметьте опцию, чтобы выгружать остатки на складах для карточек.';
	$MESS[$strLang.'STOCK_ID'] = 'ID склада';
	$MESS[$strLang.'STOCK_NAME'] = 'Название склада';
	$MESS[$strLang.'STOCK_HINT'] = 'ID склада можно узнать на странице <a href="#STORE_URL#" target="_blank">Склад поставщика</a> (в настройках личного кабинета).<br/><br/>
	Название склада - произвольное название для удобства управления в списке полей.';
	$MESS[$strLang.'FIELD_STOCK'] = 'Остаток «#STORE_NAME#» [#STORE_ID#]';
	$MESS[$strLang.'FIELD_STOCK_DESCRIPTION'] = 'Укажите здесь значение остатка для выбранного склада.';

# Settings: export prices by 1
$MESS[$strLang.'EXPORT_PRICES_BY_1_CHECKBOX'] = 'Выгружать цены по одной (вместо 100)';
	$MESS[$strLang.'EXPORT_PRICES_BY_1_HINT'] = 'Опция позволяет выгружать цены на каждую карточку отдельно.<br/><br/>
	По умолчанию опция не включена: модуль выгружает по 100 цен за 1 запрос. Но это может создавать ситуации, когда из-за ошибки выгрузки одной цены остальные также не выгружаются.<br/><br/>
	При этом важно понимать, что включение опции замедлит общую выгрузку цен примерно в 100 раз.';

# Settings: just stocks and prices
$MESS[$strLang.'STOCK_AND_PRICE_CHECKBOX'] = 'Режим выгрузки только цен и остатков';
	$MESS[$strLang.'STOCK_AND_PRICE_HINT'] = 'Данный режим позволяет выгружать только цены и остатки. Данные по карточкам (характеристики, размеры, фотографии) не выгружаются.<br/><br/>
	При этом <b>поле Баркод (barcode) является обязательным</b>, т.к. по нему обновляются остатки, а <b>поле Артикул (vendorCode) является обязательным</b> для обновления цен (модуль по артикулу выполняет поиск номенклатур и собственно запрос на обновление цен происходит по найденным ID номенклатур).<br/><br/>
	Включение данной опции не приводит к скрытию полей (ни стандартных полей, ни атрибутов), благодаря чему не сбиваются настройки, поэтому можно сначала настроить профиль на полную выгрузку, затем создать копию и настроить на обновление только цен и остатков (либо включить данный режим в настроенном профиле в любое время).';

# Settings: SKIP_APPEND_SIZES
$MESS[$strLang.'SKIP_APPEND_SIZES_CHECKBOX'] = 'Не дополнять массив sizes из ранее выгруженного товара';
	$MESS[$strLang.'SKIP_APPEND_SIZES_HINT'] = 'Опция позволяет отменить стандартный функционал дополения массива <code>sizes</code> размерами уже имеющегося товара на Wildberries (теми, которые отсутствуют в выгружаемых данных при обновлении товара - на создание нового товара это не влияет).<br/><br/>
	Это работает так: по умолчанию, если модуль выгружает товар, который уже есть на Wildberries, он выгружает в массиве sizes не только то, что сгенерировано для товара, но и то, что уже имеется для данного товара на Wildberries (поиск уже имеющихся выполняется по баркоду).<br/><br/>
	При этом предпросмотр показывает оригинальные данные, без добавленных элементов (добавление происходит в момент выгрузки).<br/><br/>
	Поставьте галочку, если Вам нужно, чтобы массив sizes выгружался без дополнительных данных (так, как в предпросмотре).<br/><br/>
	<i><b>Опция носит экспериментальный характер</b></i>.';

# Settings: SKIP_UPDATE_CARDS
$MESS[$strLang.'SKIP_UPDATE_CARDS_CHECKBOX'] = 'Запретить обновление существующих на WB карточек';
	$MESS[$strLang.'SKIP_UPDATE_CARDS_HINT'] = 'Опция позволяет запретить обновление имеющихся на WB карточек товаров.<br/><br/>
	Это предотвращает:
	<ol>
		<li>выполнение любых запросов на обновление карточек товаров - <code>/content/v1/cards/update</code></li>
		<li>обновление фотографий для существующих карточек - <code>/content/v1/media/save</code></li>
	</ol>
	Имейте в виду, из-за особенностей WB фотографии могут не выгрузиться с первого раза, в таком случае, если данную опцию снимать нельзя, фотографии Вам придётся загружать вручную в личном кабинете WB (т.к. по существующим карточкам модуль не будет выполнять повторные запросы на обновление фотографий).<br/><br/>
	<b>Внимание!</b> Пожалуйста, проверяйте работу опции на небольшой выгрузке.';
	$MESS[$strLang.'SKIP_UPDATE_CARDS_LOG'] = 'Фото для товара #VENDOR_CODE# исключены из выгрузки (действует запрет на обновление карточек).';

# Settings: SKIP_CREATE_UNAVAILABLE
$MESS[$strLang.'SKIP_CREATE_UNAVAILABLE_CHECKBOX'] = 'Не создавать новые карточки с нулевым остатком';
	$MESS[$strLang.'SKIP_CREATE_UNAVAILABLE_HINT'] = 'Опция позволяет отменить создание новых карточек, если не задан остаток.<br/><br/>
	Для существующих карточек эта галочка не учитывается.';
	$MESS[$strLang.'SKIP_CREATE_UNAVAILABLE_LOG'] = 'Товар ##ID# исключён из выгрузки, т.к. в его данных содержится нулевой остаток.';

# Settings: group by colors
$MESS[$strLang.'GROUP_BY_COLORS_CHECKBOX'] = 'Разобрать по цветам и размерам';
	$MESS[$strLang.'GROUP_BY_COLORS_HINT'] = 'Опция перегруппирует отправляемые данные таким образом, что все товары каждого цвета объединяются в один, т.е. в каждом цвете будет по нескольку размеров.<br/><br/>
	<b>Внимание!</b> Опция применяется для режима работы «Только ТП».';

# Settings: history
$MESS[$strSName.'HISTORY_SAVE'] = 'Сохранять историю';
	$MESS[$strSHint.'HISTORY_SAVE'] = 'Выберите, какие данные из историии выгрузок следует сохранять в базе данных.<br/><br/>
	Эта информации может быть использована только для информации. Без неё может быть очень сложно настроить выгрузку, но после настройки она может быть не нужна.<br/><br/>
	При больших объёмах выгрузки эта информация может приводить к переполнению базы данных, поэтому после полной настройки профиля можно отключить сбор данной информации (либо оставить логирование только задач, без товаров).';
$MESS[$strLang.'HISTORY_SAVE_TASK_PRODUCT'] = 'Задачи, товары';
$MESS[$strLang.'HISTORY_SAVE_TASK'] = 'Задачи';
$MESS[$strLang.'HISTORY_SAVE_NOTHING'] = '-- не сохранять --';

# Headers
$MESS[$strHead.'HEADER_N'] = 'Поля для номенклатуры';
	$MESS[$strLang.'HEADER_N_ATTRIBUTES'] = '«#NAME#» (атрибуты для номенклатуры)';
$MESS[$strHead.'HEADER_STOCKS'] = 'Остатки';

# Fields
$MESS[$strName.'object'] = 'Предмет';
	$MESS[$strHint.'object'] = 'Укажите здесь тип предмета (категорию).<br/><br/>
	Название категории должно быть указано в точности так же, как на Wildberries. Искать категории можно, нажав кнопку «Добавить категорию» на вкладке «Категории».';
$MESS[$strName.'techSize'] = 'Размер поставщика';
	$MESS[$strHint.'techSize'] = 'Размер поставщика (пример S, M, L, XL, 42, 42-43).';
$MESS[$strName.'wbSize'] = 'Российский размер';
	$MESS[$strHint.'wbSize'] = 'Российский размер.';
$MESS[$strName.'vendorCode'] = 'Артикул';
	$MESS[$strHint.'vendorCode'] = 'Укажите здесь артикул товара. По артикулу товара выполняются все сопоставления товара.<br/><br/>
		Поэтому артикул можно считать идентификатором товара. Соответственно, у разных товаров не должно быть одинаковых артикулов, а сам артикул должен состоять только из символов латиницы, чисел и знаков подчёркивания.';
$MESS[$strName.'price'] = 'Цена, руб';
	$MESS[$strHint.'price'] = 'Укажите цену в рублях, без копеек.';
$MESS[$strName.'skus'] = 'Баркод';
	$MESS[$strHint.'skus'] = 'Укажите здесь штрихкод товара. Можно указать более одного штрихкода.';
$MESS[$strName.'photos'] = 'Фото (размер не менее 450х450)';
	$MESS[$strHint.'photos'] = 'Укажите здесь изображения товара (можно выгружать несколько изображений), каждое из которых должно иметь размер не менее 450х450 пикселей.<br/><br/>
	Данное поле выгружается не в общем массиве данных, а отдельным запросом (в соответствии с API Wildberries), поэтому после в некоторых случаях после выгрузки товара может понадобиться очередная выгрузка, чтобы загрузить товару фотографии.<br/><br/>
	<b>Внимание!</b> Если для товара выгружается несколько изображений, и хотя бы одно из них размером меньше 450х450, то все изображения не будут выгружены (при этом такая ситуация не приводит к ошибке, поэтому будьте внимательны: в лог эта информация не добавляется).';

$MESS[$strLang.'REF'] = ' (из справочника)';

# Hints for types
$MESS[$strLang.'DESCRIPTION_TYPE_STRING'] = 'Тип данных: Строка.';
$MESS[$strLang.'DESCRIPTION_TYPE_STRING_ARRAY'] = 'Тип данных: Массив строк.';
$MESS[$strLang.'DESCRIPTION_TYPE_NUMBER'] = 'Тип данных: Число.';
$MESS[$strLang.'DESCRIPTION_TYPE_NUMBER_ARRAY'] = 'Тип данных: Массив чисел.';

# Dictionaries
$MESS[$strLang.'DICTIONARY_colors'] = 'Цвет';
$MESS[$strLang.'DICTIONARY_kinds'] = 'Пол';
$MESS[$strLang.'DICTIONARY_countries'] = 'Страна производства';
$MESS[$strLang.'DICTIONARY_collections'] = 'Коллекция';
$MESS[$strLang.'DICTIONARY_seasons'] = 'Сезон';
$MESS[$strLang.'DICTIONARY_contents'] = 'Комплектация';
$MESS[$strLang.'DICTIONARY_consists'] = 'Состав';
$MESS[$strLang.'DICTIONARY_brands'] = 'Бренд';
$MESS[$strLang.'DICTIONARY_tnved'] = 'ТНВЭД';

# Custom tabs
$MESS[$strLang.'TAB_CARDS_NAME'] = 'Проверка API';
	$MESS[$strLang.'TAB_CARDS_DESC'] = 'Проверка методов API Wildberries.ru';

$MESS[$strLang.'ERROR_EMPTY_PRODUCT_CATEGORY'] = 'Для товара #ELEMENT_ID# не указана категория.';

# Log messages
$MESS[$strLang.'LOG_PHOTOS_EXPORTED'] = '[Товар ##ELEMENT_ID#] Изображения выгружены (всего: #COUNT#). JSON: #PHOTOS#.';
$MESS[$strLang.'LOG_PHOTOS_ERROR'] = '[Товар ##ELEMENT_ID#] Ошибка выгрузки изображений для #VENDOR_CODE#: #TEXT# (#RESPONSE_CODE#). JSON: #PHOTOS#.';

$MESS[$strLang.'LOG_PRICES_PREPARE'] = 'Подготовка выгрузки цен..';
$MESS[$strLang.'LOG_PRICES_COUNT'] = 'Найдено цен для выгрузки: #COUNT#.';
$MESS[$strLang.'LOG_PRICES_VENDOR_CODES'] = 'Найдены цены для артикулов: #VENDOR_CODES#.';
$MESS[$strLang.'LOG_PRICES_NOT_FOUND'] = 'Все цены выгружены.';
$MESS[$strLang.'LOG_PRICES_EXPORTED'] = '[Товар ##ELEMENT_ID#] Цены выгружены (всего: #COUNT#). JSON: #PRICES#.';
$MESS[$strLang.'LOG_PRICES_ERROR'] = '[Товар ##ELEMENT_ID#] Цены не выгружены из-за ошибки: #ERROR#. [код ответа: #RESPONSE_CODE#]. JSON: #PRICES#.';
$MESS[$strLang.'LOG_PRICES_PERCENT_LIMIT'] = '[Товар ##ELEMENT_ID#] Цена выгружена ниже заданной (#PRICE_EXPORTED# вместо #PRICE_TARGET#), т.к. выгрузка новых цен выше на 20% старых невозможна (старая цена: #PRICE_REMOTE#, процент превышения: #PERCENT#, артикул: #VENDOR_CODE#, номенклатура: #NM_ID#). Через некоторое время (обычно до 1 минуты) требуется очередная выгрузка для корректировки цены.';
$MESS[$strLang.'LOG_PRICES_FILTER_ERROR'] = '[Товар ##ELEMENT_ID#] Цены не выгружены из-за ошибки при поиске по артикулу: #ERROR#. [код ответа: #RESPONSE_CODE#]. JSON: #PRICES#.';

$MESS[$strLang.'LOG_STOCKS_PREPARE'] = 'Подготовка выгрузки остатков..';
$MESS[$strLang.'LOG_STOCKS_COUNT'] = 'Найдено остатков для выгрузки: #COUNT#.';
$MESS[$strLang.'LOG_STOCKS_BARCODES'] = 'Найдены остатки для баркодов: #BARCODES#.';
$MESS[$strLang.'LOG_STOCKS_NOT_FOUND'] = 'Все остатки выгружены.';
$MESS[$strLang.'LOG_STOCKS_EXPORTED'] = '[Товар ##ELEMENT_ID#] Остатки выгружены (всего: #COUNT#): JSON: #STOCKS#.';
$MESS[$strLang.'LOG_STOCKS_ERROR'] = '[Товар ##ELEMENT_ID#] Остатки не выгружены из-за ошибки: #ERROR#. [код ответа: #RESPONSE_CODE#]. JSON: #STOCKS#.';

$MESS[$strLang.'LOG_UPDATE_ATTRIBUTES_ERROR'] = 'Ошибка обновления атрибутов категории #CATEGORY#: #ERROR#.';

$MESS[$strLang.'LOG_PRODUCT_SKIPPED_EMPTY_STOCK_FOR_NEW_CARD'] = 'Выгрузка товара #ELEMENT_ID# (артикул #VENDOR_CODE#) отменена: в профиле запрещено создавать новые товары с пустым остатком.';

$MESS[$strLang.'ITEM_HAS_NO_VENDOR_CODES'] = 'Для товара #ELEMENT_ID# не выгружается артикул vendorCode.';




