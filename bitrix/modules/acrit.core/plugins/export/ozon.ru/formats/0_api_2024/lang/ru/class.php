<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'Ozon.Ru API 2024 (бета-версия)';

// Settings
$strSName = $strLang.'SETTINGS_NAME_';
$strSHint = $strLang.'SETTINGS_HINT_';
$MESS[$strSName.'CLIENT_ID'] = 'Клиентский идентификатор [Client ID]';
	$MESS[$strSHint.'CLIENT_ID'] = 'Укажите здесь клиентский идентификатор («<code><a href="https://seller.ozon.ru/settings/api-keys" target="_blank">Client Id</a></code>») вашей учетной записи продавца.';
$MESS[$strSName.'API_KEY'] = 'Ключ доступа [API Key]';
	$MESS[$strSHint.'API_KEY'] = 'Укажите здесь ключ доступа («<code><a href="https://seller.ozon.ru/settings/api-keys" target="_blank">API key</a></code>»).';
	$MESS[$strLang.'API_KEY_CHECK'] = 'Проверить доступ';
$MESS[$strSName.'LIMITS'] = 'Лимиты';
	$MESS[$strSHint.'LIMITS'] = 'Здесь отображается лимит на загрузку и обновление товаров.<br/><br/><a href="https://docs.ozon.ru/global/products/upload/upload-limit/?country=RU" target="_blank">Подробнее</a>';
	$MESS[$strLang.'LIMITS_REFRESH'] = 'Проверить лимит';
	$MESS[$strLang.'LIMITS_TEXT'] = '<span>#TYPE#: #REMAINING# / #VALUE#</span>';
	$MESS[$strLang.'LIMITS_TEXT_INFINITE'] = '<span>#TYPE#: неогранич.</span>';
	$MESS[$strLang.'LIMITS_TYPE_daily_create'] = 'Создание';
	$MESS[$strLang.'LIMITS_TYPE_daily_update'] = 'Обновление';
	$MESS[$strLang.'LIMITS_TYPE_total'] = 'Ассортимент';
	$MESS[$strLang.'LIMITS_ERROR'] = 'Проверьте данные для подключения - Client ID и API Key';
	$MESS[$strLang.'LIMITS_TEXT_ERROR'] = '<code>Данные по лимитам не получены, проверьте Client ID и API Key.</code>';
$MESS[$strLang.'EXPORT_STOCKS_CHECKBOX'] = 'Выгружать остатки по складам';
	$MESS[$strLang.'EXPORT_STOCKS_HINT'] = 'Выгрузка остатков по складам не связана с выгрузкой основного остатка в Ozon - т.к. выгрузка основного остатка и выгрузка остатка на выбранном складе - это не связанные между собой операции Ozon.<br/><br/>
	Для каждого склада необходимо указать ID склада (в системе Ozon) и произвольное название для удобства работы.<br/><br/>
	После указания складов и сохранения/применения настроек профиля в списке полей появляются новые поля - в каждом из них должно быть значение остатка на соответствующем складе.';
	$MESS[$strLang.'EXPORT_STOCKS_ADD'] = 'Добавить';
	$MESS[$strLang.'EXPORT_STOCKS_ADD_AUTO'] = 'Добавить все склады автоматически';
	$MESS[$strLang.'EXPORT_STOCKS_ADD_AUTO_EMPTY'] = 'В личном кабинете Озон склады отсутствуют.';
	$MESS[$strLang.'EXPORT_STOCKS_NOTE'] = '<b>Внимание!</b> Если в личном кабинете Ozon заданы склады, выгрузка общего остатка (поле <code>stock</code>) недопустима! В таком случае нужно выгружать только остатки по складам.<br/>В будущих обновлениях поле <code>stock</code> будет недоступно при выборе опции «Выгружать остатки по складам».<br/>В случае выгрузки общего остатка при наличии складов будет <a href="#" class="acrit-inline-link" onclick="if(!$(\'[data-role=acrit_ozon_stores_error_example]\').is(\':animated\'))$(\'[data-role=acrit_ozon_stores_error_example]\').slideToggle(); return false;">ошибка</a>. <div data-role="acrit_ozon_stores_error_example" style="display:none"><code>Request validation error: invalid ProductsStocksRequest.Stocks[0]: embedded message failed validation | caused by: invalid ProductsStocksRequest_Stock.WarehouseId: value must be greater than 0</code></div>';
	$MESS[$strLang.'EXPORT_STOCKS_DELETE'] = 'Удалить';
	$MESS[$strLang.'EXPORT_STOCKS_DELETE_CONFIRM'] = 'Действительно удалить выбранный склад?';
	$MESS[$strLang.'STOCK_ID'] = 'ID склада';
	$MESS[$strLang.'STOCK_NAME'] = 'Название склада';
$MESS[$strLang.'STOCK_AND_PRICE_CHECKBOX'] = 'Режим выгрузки только остатков и цен';
	$MESS[$strLang.'STOCK_AND_PRICE_HINT'] = '<b>Внимание!</b> Данный режим убирает из профиля все поля кроме необходимых для выгрузки остатков.<br/><br/>
	При этом offer_id является обязательным, т.к. это поле является идентификатором в Ozon.';
$MESS[$strLang.'ZERO_PRICE_OLD_CHECKBOX'] = 'Сброс <code>old_price</code> при отсутствии скидок';
$MESS[$strLang.'ZERO_PRICE_OLD_HINT'] = 'Опция управляет отправкой поля <code>old_price</code> только для случаев когда цена со скидкой не меньше обычной цены: при включённой опции <code>old_price</code> будет отправляться равным нулю, при отключённой опции <code>old_price</code> не будет передавать вовсе.';
$MESS[$strLang.'ZERO_PRICE_PREMIUM_CHECKBOX'] = 'Сброс <code>premium_price</code> при совпадении с price';
$MESS[$strLang.'ZERO_PRICE_PREMIUM_HINT'] = 'Опция управляет отправкой поля <code>premium_price</code> только для случаев когда premium-цена не меньше обычной цены: при включённой опции <code>premium_price</code> будет отправляться равным нулю, при отключённой опции <code>premium_price</code> не будет передавать вовсе.';

//
$MESS[$strSName.'HISTORY_SAVE'] = 'Сохранять историю';
	$MESS[$strSHint.'HISTORY_SAVE'] = 'Выберите, какие данные из историии выгрузок следует сохранять в базе данных.<br/><br/>
	Эта информации может быть использована только для информации. Без неё может быть очень сложно настроить выгрузку, но после настройки она может быть не нужна.<br/><br/>
	При больших объёмах выгрузки эта информация может приводить к переполнению базы данных, поэтому после полной настройки профиля желательно отключить сбор данной информации.';
$MESS[$strLang.'HISTORY_SAVE_TASK_PRODUCT_STOCK'] = 'Задачи, товары, остатки';
$MESS[$strLang.'HISTORY_SAVE_TASK_PRODUCT'] = 'Задачи, товары';
$MESS[$strLang.'HISTORY_SAVE_TASK'] = 'Задачи';
$MESS[$strLang.'HISTORY_SAVE_NOTHING'] = '-- не сохранять --';

//
$MESS[$strLang.'GUESS_BRAND'] = 'Бренд';
$MESS[$strLang.'GUESS_GROUP'] = 'Объединить на одной карточке';

//
$MESS[$strLang.'GENERAL_SETTINGS_HEADER_STOCK'] = 'Работа с остатками Ozon';
$MESS[$strLang.'GENERAL_SETTINGS_CONSIDER_RESERVED_STOCK'] = 'Учёт зарезервированного остатка в Ozon';
	$MESS[$strLang.'GENERAL_SETTINGS_HINT_CONSIDER_RESERVED_STOCK'] = 'Данная опция позволяет учитывать зарезервированный остаток в Ozon и выгружать с сайта остаток, уменьшенный на полученное значение.<br/><br/>
Данные товара из Ozon отбираются с фильтром по <code>offer_id</code> - т.е. это поле должно быть корректно настроено, иначе определение зарезервированного остатка в Ozon не будет работать.<br/><br/>
<b>Внимание!</b> Данная опция заставляет модуль для каждого товара получать его остатки в Ozon, соответственно это значительно увеличивает время выгрузки.<br/><br/>
<b>Внимание!</b> Данная опция влияет только на общий остаток (поле «stock»), на остатки по складам данная опция не влияет.';

// Fields
$MESS[$strHead.'HEADER_GENERAL'] = 'Основные данные о товарах';
$MESS[$strName.'offer_id'] = 'Идентификатор товара (артикул)';
	$MESS[$strHint.'offer_id'] = 'Идентификатор товара в системе продавца — артикул.<br/><br/>
	Артикул должен быть уникальным в рамках вашего ассортимента.<br/><br/>
	Максимальная длина значения - 50 символов, в случае превышения этого лимита будут наблюдаться проблемы отправки не только одного товара, но и тех, которые выгружаются одновременно с ним.';
$MESS[$strName.'name'] = 'Название товара';
	$MESS[$strHint.'name'] = 'Название товара. До 500 символов.';
$MESS[$strName.'images'] = 'Изображения';
	$MESS[$strHint.'images'] = 'Ссылки на изображения. Не больше 15 изображений в одном товаре (если используете primary_image - не более 14 изображений).';
$MESS[$strName.'primary_image'] = 'Главное изображение товара';
	$MESS[$strHint.'primary_image'] = 'Используйте для загрузки главного изображения товара. Если не передать значение primary_image, главным будет первое изображение в массиве images.';
$MESS[$strName.'image_group_id'] = 'Идентификатор пакетной загрузки изображений';
	$MESS[$strHint.'image_group_id'] = 'Идентификатор для последующей пакетной загрузки изображений.';
$MESS[$strName.'pdf_list'] = 'PDF-файлы';
	$MESS[$strHint.'pdf_list'] = 'Список pdf-файлов';
$MESS[$strName.'price'] = 'Цена (с учетом скидок)';
	$MESS[$strHint.'price'] = 'Цена товара с учетом скидок, отображается на карточке товара. Если на товар нет скидок — укажите значение old_price.';
$MESS[$strName.'old_price'] = 'Цена (без учета скидок)';
	$MESS[$strHint.'old_price'] = 'Цена до скидок (будет зачеркнута на карточке товара). Указывается в рублях. Разделитель дробной части — точка, до двух знаков после точки.';
$MESS[$strName.'min_price'] = 'Минимальная цена';
	$MESS[$strHint.'min_price'] = 'Минимальная цена товара после применения акций.';
$MESS[$strName.'premium_price'] = 'Цена Premium';
	$MESS[$strHint.'premium_price'] = 'Цена для клиентов с подпиской <a href="https://docs.ozon.ru/common/ozon-premium" target="_blank">Ozon Premium</a>.';
$MESS[$strName.'auto_action_enabled'] = 'Автоприменение акций';
	$MESS[$strHint.'auto_action_enabled'] = 'Атрибут для включения и выключения автоприменения акций:
	<ul>
		<li><code>ENABLED</code> — включить автоприменение акций</li>
		<li><code>DISABLED</code> — выключить автоприменение акций</li>
		<li><code>UNKNOWN</code> — ничего не менять, передаётся по умолчанию</li>
	</ul>
	<p>Например, если ранее вы включили автоприменение акций и не хотите выключать его, передавайте <code>UNKNOWN</<code>code>.</p>
	<p>Если вы передаёте <code>ENABLED</code> в этом параметре, установите значение минимальной цены в параметре <code>min_price</code>.</p>
	';
$MESS[$strName.'vat'] = 'Ставка НДС для товара';
	$MESS[$strHint.'vat'] = 'Ставка НДС для товара.<br/>
		<ul>
			<li>0 — не облагается НДС</li>
			<li>0.1 — 10%</li>
			<li>0.2 — 20%</li>
		</ul>';
$MESS[$strName.'stock'] = 'Остаток на складе (общий)';
	$MESS[$strHint.'stock'] = 'Общий остаток товара на складе.<br/><br/>
	Остаток обновляется только для товаров, у которых этап обработки прошёл статус <b>processed</b>. До этого попытки выгрузки остатков будут завершаться ошибкой «[NOT_FOUND] Product not found». Т.е. первичная выгрузка в любом случае не обновит остатки.';
$MESS[$strLang.'STOCK_X'] = 'Остаток на складе «#NAME#» [#ID#]';
$MESS[$strName.'warehouse_id'] = 'ID склада';
	$MESS[$strHint.'warehouse_id'] = 'Идентификатор склада.';
$MESS[$strName.'barcode'] = 'Штрихкод';
	$MESS[$strHint.'barcode'] = 'Введите штрихкод товара от производителя. Если у товара нет такого штрихкода, позже вы можете самостоятельно сгенерировать его в Озон.<br/><br/>
	Штрихкод нужен для продажи со склада Ozon, а также для продажи товара, подлежащего обязательной маркировке (обувь)';
$MESS[$strName.'depth'] = 'Длина упаковки';
	$MESS[$strHint.'depth'] = 'Длина — это наибольшая сторона упаковки товара. Перед измерением длины:
	<ul>
		<li>Одежда, текстиль, наборы для вышивания — сложите товар в упаковке пополам.</li>
		<li>Карты и интерьерные наклейки — скрутите в рулон. Длина рулона — самая большая величина.</li>
	</ul>
	Длина книжного комплекта — это длина всей стопки книг, которые входят в комплект.<br/><br/>
	Указывается в миллиметрах, сантиметрах, или дюймах - единицу измерения необходимо указывать в поле «dimension_unit».';
$MESS[$strName.'width'] = 'Ширина упаковки';
	$MESS[$strHint.'width'] = 'Сначала измерьте длину и высоту, оставшаяся сторона — это ширина. Перед измерением ширины:
	<ul>
		<li>Одежда, текстиль, наборы для вышивания — сложите товар в упаковке пополам.</li>
		<li>Карты и интерьерные наклейки — скрутите в рулон. Ширина рулона — это его диаметр.</li>
	</ul>	
	Ширина книжного комплекта — это ширина всей стопки книг, которые входят в комплект.<br/><br/>
	Указывается в миллиметрах, сантиметрах, или дюймах - единицу измерения необходимо указывать в поле «dimension_unit».';
$MESS[$strName.'height'] = 'Высота упаковки';
	$MESS[$strHint.'height'] = 'Высота — это наименьшая сторона упаковки товара. Перед измерением высоты:
	<ul>
		<li>Одежда, текстиль, наборы для вышивания — сложите товар в упаковке пополам.</li>
		<li>Карты и интерьерные наклейки — скрутите в рулон. Высота рулона — это его диаметр.</li>
	</ul>
	Высота книжного комплекта — это высота всей стопки книг, которые входят в комплект.<br/><br/>
	Указывается в миллиметрах, сантиметрах, или дюймах - единицу измерения необходимо указывать в поле «Единица измерения габаритов».';
$MESS[$strName.'dimension_unit'] = 'Единица измерения габаритов';
	$MESS[$strHint.'dimension_unit'] = 'Единица измерения габаритов
		<ul>
			<li>mm — миллиметры</li>
			<li>cm — сантиметры</li>
			<li>in — дюймы</li>
		</ul>';
$MESS[$strName.'weight'] = 'Вес товара в упаковке';
	$MESS[$strHint.'weight'] = 'Вес товара в упаковке. Предельное значение - 1000 килограмм или конвертированная величина в других единицах измерения.<br/><br/>
	Указывается в граммах, килограммах, или фунтах - единицу измерения необходимо указывать в поле «Единица измерения веса».';
$MESS[$strName.'weight_unit'] = 'Единица измерения веса';
	$MESS[$strHint.'weight_unit'] = 'Единица измерения веса:
		<ul>
			<li>g — граммы</li>
			<li>kg — килограммы</li>
			<li>lb — фунты</li>
		</ul>';
$MESS[$strName.'category_id'] = 'ID категории';
	$MESS[$strHint.'category_id'] = 'Укажите здесь ID категории.<br/><br/>
	Используется только при отмеченной галочке «Альтернативный режим выбора категорий»';
$MESS[$strName.'video_youtube'] = 'Видео для YouTube';
	$MESS[$strHint.'video_youtube'] = 'Укажите здесь видео для YouTube в любом из форматов:
	<ul>
		<li>https://youtu.be/ene4qDMdn6A</li>
		<li>https://www.youtube.com/watch?v=ene4qDMdn6A</li>
		<li>https://www.youtube.com/embed/ene4qDMdn6A</li>
		<li>ene4qDMdn6A (только код видео)</li>
	</ul>
	Данное поле не связано с полем «Код ролика на YouTube», которое может предлагаться к заполнению для отдельных категорий товаров.<br/><br/>
	Значение из данного поля преобразуется, определяется заголовок (автоматически) и результат подставляется в complex_attributes.';

$MESS[$strLang.'MESSAGE_CHECK_ACCESS_SUCCESS'] = 'Проверка успешна. Доступ разрешен.';
$MESS[$strLang.'MESSAGE_CHECK_ACCESS_DENIED'] = 'Указаны некорректные данные (ClientId и/или ApiKey).';
$MESS[$strLang.'MESSAGE_CHECK_ACCESS_COMPANY_BLOCKED'] = 'Аккаунт компании заблокирован. Обратитесь в техподдержку Ozon.';

$MESS[$strLang.'GROUPED_ATTRIBUTES_HEADER'] = 'ОБЩИЕ АТРИБУТЫ КАТЕГОРИЙ';

$MESS[$strLang.'NOTICE_SUPPORT'] = '<b>Внимание!</b> На данный формат выгрузки не распространяются условия бесплатной техподдержки. Помощь в настройке осуществляется <a href="/bitrix/admin/acrit_exportproplus_new_support.php?lang=ru&AcritExpSupport_active_tab=ask" target="_blank">на платной основе</a>.';

$MESS[$strLang.'HISTORY_OFFER_NOT_FOUND'] = 'Товар #OFFER_ID# не найден в истории выгрузок.';
$MESS[$strLang.'HISTORY_OFFER_EMPTY_ID'] = 'Пожалуйста, укажите непустой offer_id.';
$MESS[$strLang.'HISTORY_TASK_NOT_FOUND'] = 'Задача #TASK_ID# не найдена в истории выгрузок.';
$MESS[$strLang.'HISTORY_TASK_EMPTY_ID'] = 'Пожалуйста, укажите числовой ID задачи Ozon.';

$MESS[$strLang.'ERROR_WRONG_PRODUCT_SECTION'] = 'Для товара [ID=#ELEMENT_ID#] раздел инфоблока не определен.';
$MESS[$strLang.'ERROR_WRONG_PRODUCT_CATEGORY'] = 'Для товара [ID=#ELEMENT_ID#] категория не определена.';
$MESS[$strLang.'ERROR_EMPTY_REQUIRED_FIELDS'] = 'Для товара [ID=#ELEMENT_ID#] в категории «#CATEGORY#» не заполнены обязательные поля: #FIELDS#';
$MESS[$strLang.'ERROR_WRONG_DICTIONARY_VALUE'] = 'Для товара [ID=#ELEMENT_ID#] в атрибуте "#ATTRIBUTE#" указано некорректное значение #VALUE#. Проверьте значение по справочнику.';
$MESS[$strLang.'ERROR_CATEGORIES_EMPTY_ANSWER'] = 'Ошибка обновления категорий (#URL#). Попробуйте еще раз. #JSON#';
$MESS[$strLang.'ERROR_EXPORT_ITEMS_BY_API'] = 'Ошибка отправки товаров в OZON [#METHOD#]: #ERROR#.';
$MESS[$strLang.'ERROR_EXPORT_ITEMS_BY_API_POPUP'] = 'Ошибка отправки данных в OZON: #ERROR#.';
$MESS[$strLang.'ERROR_EXPORT_PRICES_BY_API'] = 'Ошибка отправки цен в OZON [#METHOD#]: #ERROR#.';
$MESS[$strLang.'ERROR_EXPORT_ITEMS_BY_API_TASK_0'] = 'Нулевое значение task_id';
$MESS[$strLang.'ERROR_JSON_NOT_FOUND'] = 'JSON-данные не найдены.';
$MESS[$strLang.'ERROR_PARSE_ATTRIBUTE'] = 'Ошибка работы с атрибутом «#ATTRIBUTE#». Проверьте настройки категорий, а также запустите обновление категорий.';
$MESS[$strLang.'ERROR_UPDATE_ATTRIBUTES'] = 'Ошибка обновления атрибутов для категории #CATEGORY_ID#: #ERROR#';
$MESS[$strLang.'ATTRIBUTE_VALUES_CHECK_SUCCESS'] = 'Все значения атрибута загружены (общее количество - #COUNT#)';
$MESS[$strLang.'ATTRIBUTE_VALUES_CHECK_ERROR'] = 'Ошибка загрузки значений атрибутов. Пожалуйста, попробуйте ещё раз.';
