<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);
$strSName = $strLang.'SETTINGS_NAME_';
$strSHint = $strLang.'SETTINGS_HINT_';

// General
$MESS[$strLang.'NAME'] = 'Wildberries.ru (API) v.3 - НЕ ПОДДЕРЖИВАЕТСЯ!';

// Steps
$MESS[$strLang.'STEP_PAUSE_BEFORE_STOCKS'] = 'Подготовка к выгрузке остатков';
$MESS[$strLang.'STEP_EXPORT_STOCKS'] = 'Выгрузка остатков';

// Settings
$MESS[$strSName.'AUTHORIZATION'] = 'Единый токен авторизации';
	$MESS[$strSHint.'AUTHORIZATION'] = 'Укажите здесь единый токен авторизации, получить его можно на странице <a href="https://suppliers-portal.wildberries.ru/supplier-settings/access-to-new-api" target="_blank">личного кабинета</a>.';
		$MESS[$strLang.'TOKEN_CHECK'] = 'Проверить токен';

$MESS[$strSName.'OFFERS_STRUCTURE'] = 'Структура выгрузки ТП';
	$MESS[$strSHint.'OFFERS_STRUCTURE'] = 'Укажите здесь структуру ТП при выгрузке.<br/><br/>
	Данная опция включает режим выгрузки, при котором каждое торговое предложение выгружается не как вариация, а как отдельный товар.<br/><br/>
	Это полезно в случаях, когда ТП представляют собой не простейшие вариации, различающиеся ценой, цветом, размером, и т.д., но и дополнительными характеристиками, в т.ч. если у каждого ТП могут быть свои фотографии.<br/><br/>
	На одном и том же сайте данная опция может быть включена для выгрузки одних категорий товаров и отключена для других - в зависимости от характера данных в ТП.<br/><br/>
	<b>Внимание!</b> Не рекомедуем изменять режим после выгрузки, теоретически это может стать причиной проблем с загрузкой.<br/><br/>
	<b>Внимание!</b> Данная опция не заменяет параметр профиля «Режим работы ТП» и не конфликтует с ним: если общий параметр «Режим работы ТП» влияет только на отбор сущностей к выгрузке, то частный параметр «Компоновка ТП» влияет на то, как из отобранных сущностей будет построена структура данных для последующей выгрузки.';
	$MESS[$strLang.'OFFERS_STRUCTURE_C'] = 'Каждое ТП - отдельная карточка';
	$MESS[$strLang.'OFFERS_STRUCTURE_N'] = 'Каждое ТП - отдельная номенклатура';
	$MESS[$strLang.'OFFERS_STRUCTURE_V'] = 'Каждое ТП - отдельная вариация';

$MESS[$strLang.'EXPORT_STOCKS_CHECKBOX'] = 'Выгружать остатки';
	$MESS[$strLang.'EXPORT_STOCKS_HINT'] = 'Отметьте опцию, если необходимо отправлять остатки этим же профилем.
	<br/><br/>Для выгрузки необходимо указать <a href="https://suppliers-portal.wildberries.ru/marketplace-pass/api-access" target="_blank">токен</a>, ID склада (он указан в ЛК Wildberries) и название склада - любой текст для удобства.';
	$MESS[$strLang.'STOCK_ID'] = 'ID склада';
	$MESS[$strLang.'STOCK_NAME'] = 'Название склада';
	$MESS[$strLang.'STOCK_HINT'] = 'ID склада можно узнать на странице <a href="#STORE_URL#" target="_blank">Склад поставщика</a> (в настройках личного кабинета).<br/><br/>
	Название склада - произвольное название для удобства управления в списке полей.';
	$MESS[$strLang.'FIELD_STOCK'] = 'Остаток «#STORE_NAME#» [#STORE_ID#]';
	$MESS[$strLang.'FIELD_STOCK_DESCRIPTION'] = 'Укажите здесь значение остатка для выбранного склада.';
$MESS[$strLang.'STOCK_AND_PRICE_CHECKBOX'] = 'Режим выгрузки только остатков ';
	$MESS[$strLang.'STOCK_AND_PRICE_HINT'] = '<b>Внимание!</b> Данный режим убирает из профиля все поля кроме необходимых для выгрузки остатков.<br/><br/>
	При этом <b>поле barcode является обязательными</b>, т.к. по нему обновляются остатки, а поля supplierVendorCode и vendorCode являются обязательными в связи с необходимостью построения правильной структуры выгрузки.';
$MESS[$strLang.'CONTINUE_ON_ERROR_CHECKBOX'] = 'Продолжать выгрузку при ошибках';
	$MESS[$strLang.'CONTINUE_ON_ERROR_HINT'] = 'Опция позволяет продолжить выгрузку при возникновении серьёзных ошибок. Это может быть полезно при выгрузке большого объёма товаров, когда ошибка выгрузка отдельных товаров не должна останавливать весь процесс.';

//
$MESS[$strSName.'HISTORY_SAVE'] = 'Сохранять историю';
	$MESS[$strSHint.'HISTORY_SAVE'] = 'Выберите, какие данные из историии выгрузок следует сохранять в базе данных.<br/><br/>
	Эта информации может быть использована только для информации. Без неё может быть очень сложно настроить выгрузку, но после настройки она может быть не нужна.<br/><br/>
	При больших объёмах выгрузки эта информация может приводить к переполнению базы данных, поэтому после полной настройки профиля желательно отключить сбор данной информации.';
$MESS[$strLang.'HISTORY_SAVE_TASK_PRODUCT'] = 'Задачи, товары';
$MESS[$strLang.'HISTORY_SAVE_TASK'] = 'Задачи';
$MESS[$strLang.'HISTORY_SAVE_NOTHING'] = '-- не сохранять --';

// Headers
$MESS[$strHead.'HEADER_C'] = 'Поля для карточки';
	$MESS[$strLang.'HEADER_C_ATTRIBUTES'] = '«#NAME#» (атрибуты для карточки)';
$MESS[$strHead.'HEADER_N'] = 'Поля для номенклатуры';
	$MESS[$strLang.'HEADER_N_ATTRIBUTES'] = '«#NAME#» (атрибуты для номенклатуры)';
$MESS[$strHead.'HEADER_V'] = 'Поля для вариации';
	$MESS[$strLang.'HEADER_V_ATTRIBUTES'] = '«#NAME#» (атрибуты для вариации)';
$MESS[$strHead.'HEADER_STOCKS'] = 'Остатки';

// Fields
$MESS[$strName.'object'] = 'Категория товара Wildberries';
	$MESS[$strHint.'object'] = 'Укажите здесь название категории.<br/><br/>
	Название категории должно быть указано в точности так же, как на Wildberries. Искать категории можно, нажав кнопку «Добавить категорию» на вкладке «Категории».';
$MESS[$strName.'supplierVendorCode'] = 'Артикул поставщика';
	$MESS[$strHint.'supplierVendorCode'] = 'Артикул поставщика. Допускаются только латинские символы, цифры, символ подчеркивания.<br/><br/>
	<b>Внимание!</b> Данный код критически важен для корректной выгрузки (именно поэтому ранее модуль самостоятельно управлял данным свойством). По данному полю привязываются товары, также данный код используется в отчётах.<br/><br/>
	Если поле не заполнено - модуль сгенерирует его сам, в целях обеспечения корректной выгрузки новых товаров.';
$MESS[$strName.'countryProduction'] = 'Страна-производитель';
	$MESS[$strHint.'countryProduction'] = 'Укажите здесь страну проиводства.';
$MESS[$strName.'price'] = 'Цена, руб';
	$MESS[$strHint.'price'] = 'Укажите цену в рублях, без копеек.';
$MESS[$strName.'vendorCode'] = 'Артикул';
	$MESS[$strHint.'vendorCode'] = 'Артикул товара.';
$MESS[$strName.'barcode'] = 'Штрихкод';
	$MESS[$strHint.'barcode'] = 'Штрихкод товара/предложения.<br/><br/>
Штрихкод должен быть уникальным в пределах всего портала Wildberries, иначе успешная выгрузка невозможна.';

$MESS[$strLang.'GUESS_BRAND'] = 'Бренд';
$MESS[$strLang.'GUESS_DESCRIPTION'] = 'Описание';
$MESS[$strLang.'GUESS_TNVED'] = 'Тнвэд';

$MESS[$strLang.'CUSTOM_ATTR_PRICE'] = 'Розничная цена';
	$MESS[$strLang.'CUSTOM_ATTR_PRICE_UNIT'] = 'рублей';
$MESS[$strLang.'CUSTOM_ATTR_PHOTO'] = 'Фото';
	$MESS[$strLang.'DESC_CUSTOM_ATTR_PHOTO'] = 'Фотографии товара (указывается ссылка на видео с текущего сайта).<br/><br/>
Минимальное разрешение - <code><b>450х600</b></code><br/>
Максимальное количество - <code><b>10 фото</b></code>.';
$MESS[$strLang.'CUSTOM_ATTR_PHOTO360'] = 'Фото 360';
	$MESS[$strLang.'DESC_CUSTOM_ATTR_PHOTO360'] = 'Фотографии 360 (указывается ссылка на видео с текущего сайта).';
$MESS[$strLang.'CUSTOM_ATTR_VIDEO'] = 'Видео';
	$MESS[$strLang.'DESC_CUSTOM_ATTR_VIDEO'] = 'Видео (указывается ссылка на видео с текущего сайта).';
$MESS[$strLang.'CUSTOM_ATTR_INGREDIENTS'] = 'Состав';
	$MESS[$strLang.'DESC_CUSTOM_ATTR_INGREDIENTS'] = 'Каждый элемент из состава указывается в соотвествии со значениями из справочника. Значение указывается в процентах.<br/><br/>
Сумма значений процентного состава должна быть равна 100%.';
$MESS[$strLang.'CUSTOM_ATTR_NAME'] = 'Наименование';
	$MESS[$strLang.'DESC_CUSTOM_ATTR_NAME'] = 'Данное поле носит служебный характер, его работа <b>не гарантируется</b>!';
$MESS[$strLang.'CUSTOM_ATTR_ADDITIONAL_COLORS'] = 'Доп. цвета';
$MESS[$strLang.'CUSTOM_ATTR_KEYWORDS'] = 'Ключевые слова';
	$MESS[$strLang.'DESC_CUSTOM_ATTR_KEYWORDS'] = 'Ключевые слова - это слова и фразы описывающие ваш продукт и предоставляют дополнительный контекст, чтобы помочь людям найти ваш товар.<br/><br/>
Примеры: Деревянный, 100% хлопок, длинный рукав.<br/><br/>
Допускается не более 16ти слов.';
$MESS[$strLang.'CUSTOM_ATTR_DESCRIPTION'] = 'Описание';
	$MESS[$strLang.'DESC_CUSTOM_ATTR_DESCRIPTION'] = 'Опишите основные характеристики продукта, его особенности и преимущества.<br/><br/>
Максимальное количество символов - 1000.<br/><br/>
Разрешённые символы: <code><b>а-яА-ЯёЁ0-9a-zA-Z @!?,.|/:;\'"*&@#$№%[]{}()+-$</b></code>';

$MESS[$strLang.'REF'] = ' (из справочника)';

$MESS[$strLang.'ERROR_EMPTY_PRODUCT_CATEGORY'] = 'Для товара #ELEMENT_ID# не указана категория.';

$MESS[$strLang.'TEST_VERSION'] = '<b>Внимание!</b> Данная выгрузка находится в тестовом режиме. При возникновении проблем обращайтесь в нашу <a href="/bitrix/admin/acrit_exportproplus_new_support.php?lang=ru&AcritExpSupport_active_tab=ask" target="_blank">техподдержку</a>.';

$MESS[$strLang.'NOTICE_SUPPORT'] = '<b>Внимание!</b> На данный формат выгрузки не распространяются условия бесплатной техподдержки. Помощь в настройке осуществляется <a href="/bitrix/admin/acrit_exportproplus_new_support.php?lang=ru&AcritExpSupport_active_tab=ask" target="_blank">на платной основе</a>.';

$MESS[$strLang.'LOG_ELEMENT_DEBUG'] = 'Товар ##ELEMENT_ID# [#VENDOR_CODE#]: ошибка загрузки данных (#METHOD#) #METHOD# #JSON# #RESULT#';
$MESS[$strLang.'LOG_IMAGE_UPLOAD_ERROR'] = 'Товар ##ELEMENT_ID#: Ошибка загрузки изображения #UUID# (#URL#): код ответа #RESPONSE_CODE#: #RESPONSE#';
$MESS[$strLang.'LOG_STOCKS_EXPORTED'] = 'Остатки выгружены (всего: #COUNT#).';
$MESS[$strLang.'LOG_STOCKS_ERROR_TITLE'] = 'Ошибка выгрузки остатков.';
$MESS[$strLang.'LOG_STOCKS_ERROR'] = 'Остатки не выгружены из-за ошибки: #ERROR#: <pre>#CONTENT#</pre> [код ответа: #RESPONSE_CODE#]';
$MESS[$strLang.'LOG_PRICES_EXPORTED'] = 'Цены выгружены (всего: #COUNT#).';
$MESS[$strLang.'LOG_PRICES_ERROR_TITLE'] = 'Ошибка выгрузки цен.';
$MESS[$strLang.'LOG_PRICES_ERROR'] = 'Цены не выгружены из-за ошибки: #ERROR#: <pre>#CONTENT#</pre> [код ответа: #RESPONSE_CODE#]';
$MESS[$strLang.'LOG_UPDATE_ATTRIBUTES_ERROR'] = 'Ошибка обновления атрибутов: #ERROR#.';
$MESS[$strLang.'LOG_EXPORT_ITEM'] = 'Выгрузка товара ##ELEMENT_ID# (supplierVendorCode: #SUPPLIER_VENDOR_CODE#, метод #METHOD#): #PRODUCT#, результат: #RESULT#.';
$MESS[$strLang.'LOG_EXPORT_ITEM_NOT_FOUND'] = 'товар не найден в Wildberries';

$MESS[$strLang.'ERROR_LOGIN_TOO_MANY_ATTEMPLS'] = 'Пожалуйста, повторите запрос примерно через #TIME# с.';
$MESS[$strLang.'ERROR_WRONG_CONFIRM_CODE'] = 'Указан неверный SMS-код.';
$MESS[$strLang.'ERROR_RECEIVED_EMPTY_TOKEN'] = 'Ошибка авторизации. Попробуйте ещё раз.';
$MESS[$strLang.'ERROR_EMPTY_REQUIRED_FIELDS'] = 'Для категории «#CATEGORY#» не заполнены обязательные поля: #FIELDS#';
$MESS[$strLang.'ERROR_INGREDIENTS_SUMM'] = 'Сумма значений процентного состава должна быть равна 100% [элемент ID = #ELEMENT_ID#].';
$MESS[$strLang.'ERROR_EXPORT_ITEMS_BY_API'] = 'Ошибка отправки товаров в Wildberries (метод: #METHOD#).';
