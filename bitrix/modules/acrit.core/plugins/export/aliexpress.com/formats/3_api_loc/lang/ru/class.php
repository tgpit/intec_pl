<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

$strSName = $strLang.'SETTINGS_NAME_';
$strSHint = $strLang.'SETTINGS_HINT_';
$strSValues = $strLang.'SETTINGS_VALUES_';

// General
$MESS[$strLang.'NAME'] = 'Aliexpress.com NEW (обмен через Локальный API)';

# Steps
$MESS[$strLang.'STEP_CHECK_TASKS'] = 'Проверка выгрузки товаров';
$MESS[$strLang.'ERROR_NOT_SET_CATEG_ID'] = 'Ошибка: не выбран раздел каталога';
$MESS[$strLang.'ERROR_NOT_LOADED_CATEG_ATTRIBS'] = 'Ошибка: не загружена информация об атрибутах раздела каталога';
$MESS[$strLang.'ERRORS_FOR_SENT_ITEM'] = 'Ошибки товара #ITEM_ID#: ';
$MESS[$strLang.'ERRORS_FOR_ADD_ITEM'] = 'Ошибки добавления товара #ITEM_ID#: ';
$MESS[$strLang.'ERRORS_FOR_EDIT_ITEM'] = 'Ошибки изменения товара #ITEM_ID#: ';
$MESS[$strLang.'SUCCESS_SENT_ITEMS'] = 'Успешно загруженные товары: ';
$MESS[$strLang.'NOT_CHECKED_ITEMS'] = 'Отправленные, но не проверенные товары: ';
$MESS[$strLang.'CHECKED_COUNT'] = 'Обработано площадкой товаров: ';

// Settings
$MESS[$strSName.'WAIT_TIME'] = 'Время проверки выгрузки';
$MESS[$strSHint.'WAIT_TIME'] = 'Максимальное время проверки отправленных товаров при ручном запуске (сек). По умолчанию - 180 сек.';
$MESS[$strSName.'TOKEN'] = 'Токен для подключения';
$MESS[$strSHint.'TOKEN'] = 'Укажите здесь токен для обмена данными модуля с вашим кабинетом продавца';
$MESS[$strLang.'TOKEN_CHECK'] = 'Проверить доступ';
$MESS[$strLang.'TOKEN_HELP'] = 'Инструкция по получению токена расположена в блоке "Описание выгрузки"';
$MESS[$strLang.'GET_TOKEN'] = 'Получить токен авторизации';
$MESS[$strLang.'CHECK_ERROR'] = 'Ошибка: ';
$MESS[$strLang.'CHECK_SUCCESS'] = 'Успешно';
$MESS[$strSName.'SECTIONS'] = 'Раздел каталога';
$MESS[$strSHint.'SECTIONS'] = 'Выберите раздел товарного каталога AliExpress, в который будут выгружаться товары данного профиля. От этого выбора будет зависеть набор полей, доступных для выгрузки.';
$MESS[$strLang.'SECTION_EMPTY'] = 'Выбрать вариант';

// Fields
$MESS[$strHead.'HEADER_GENERAL'] = 'Общая информация';
$MESS[$strLang.'LOAD_ERROR'] = 'Ошибка';
$MESS[$strLang.'LOAD_WARNING'] = 'Внимание! Справочная информация не загрузилась с сервера AliExpress. Попробуйте обновить страницу через несколько минут.';

$MESS[$strName.'title'] = 'Название товара';
$MESS[$strHint.'title'] = 'Название - это категория, бренд, модель и дополнительные атрибуты. Например, «Кроссовки летние AliExpress женские для города и прогулок TMALL W TM55555».';
$MESS[$strName.'description'] = 'Описание товара';
$MESS[$strHint.'description'] = 'Текст описания товара может содержать HTML-теги.';
$MESS[$strName.'sku_code'] = 'Артикул';
$MESS[$strHint.'sku_code'] = 'Артикул или штрихкод товара';
$MESS[$strName.'main_image_urls_list'] = 'Изображения';
$MESS[$strHint.'main_image_urls_list'] = 'Будет загружено до шести изображений';
$MESS[$strName.'price'] = 'Цена';
$MESS[$strHint.'price'] = 'Розничная цена товара';
$MESS[$strName.'discount_price'] = 'Цена со скидкой';
$MESS[$strHint.'discount_price'] = 'Цена с учетом скидки';
$MESS[$strName.'inventory'] = 'Остаток';
$MESS[$strHint.'inventory'] = 'Остаток товара на складе';
$MESS[$strName.'language'] = 'Локализация';
$MESS[$strHint.'language'] = 'Языковой код';
$MESS[$strName.'weight'] = 'Вес в упаковке, кг';
$MESS[$strHint.'weight'] = 'Используется для расчета стоимости и возможности доставки';
$MESS[$strName.'package_length'] = 'Размер в упаковке (Д), см';
$MESS[$strHint.'package_length'] = 'Используется для расчета стоимости и определения возможности доставки.';
$MESS[$strName.'package_height'] = 'Размер в упаковке (В), см';
$MESS[$strHint.'package_height'] = 'Используется для расчета стоимости и определения возможности доставки.';
$MESS[$strName.'package_width'] = 'Размер в упаковке (Ш), см';
$MESS[$strHint.'package_width'] = 'Используется для расчета стоимости и определения возможности доставки.';
$MESS[$strName.'package_type'] = 'Метод продаж';
$MESS[$strHint.'package_type'] = 'Лотами или поштучно';
$MESS[$strSValues.'package_type_values_pieces'] = 'Поштучно';
$MESS[$strSValues.'package_type_values_lots'] = 'Лотами';
$MESS[$strName.'lot_num'] = 'Количество товара в лоте';
$MESS[$strHint.'lot_num'] = 'Обязательный параметр, если выбран метод продаж лотами';
$MESS[$strName.'product_unit'] = 'Единица измерения товара';
$MESS[$strHint.'product_unit'] = '';
$MESS[$strSValues.'product_unit_values_13'] = 'пара';
$MESS[$strSValues.'product_unit_values_14'] = 'упаковка/упаковки';
$MESS[$strSValues.'product_unit_values_15'] = 'штука/штуки';
$MESS[$strSValues.'product_unit_values_17'] = 'комплект/комплекты';
$MESS[$strSValues.'product_unit_values_19'] = 'квадратный метр';
$MESS[$strName.'shipping_lead_time'] = 'Время на отправку заказа';
$MESS[$strHint.'shipping_lead_time'] = 'Время, за которое вы обязуетесь ввести трек-номер, если у вас своя логистика. Значение от 1 до 30.';
$MESS[$strName.'size_chart_id'] = 'Идентификатор таблицы размеров';
$MESS[$strHint.'size_chart_id'] = 'Нужен для одежды, обуви, аксессуаров и белья.';
$MESS[$strName.'bulk_discount'] = 'Процент скидки для оптовой покупки';
$MESS[$strHint.'bulk_discount'] = 'Значение от 1 до 99';
$MESS[$strName.'bulk_order'] = 'Минимальное количество заказов для оптовой покупки';
$MESS[$strHint.'bulk_order'] = 'Значение от 2 до 100 000';
$MESS[$strName.'tnved_codes'] = 'Коды ВЭД';
$MESS[$strHint.'tnved_codes'] = 'Классификатор товаров из товарной номенклатуры внешнеэкономической деятельности, обязательный для некоторых категорий товаров';
$MESS[$strName.'gtin'] = 'Глобальный номер в GS1';
$MESS[$strHint.'gtin'] = 'Глобальный номер товарной продукции в единой международной базе товаров GS1, для автоматического получения ТНВЭД или ОКПД2';
$MESS[$strName.'okpd2_codes'] = 'Общероссийский классификатор продукции по ВЭД';
$MESS[$strHint.'okpd2_codes'] = 'Общероссийский классификатор продукции по видам экономической деятельности. Для товаров, производимых в РФ.';
$MESS[$strName.'freight_template_id'] = 'Шаблон доставки';
$MESS[$strHint.'shipping_template_id'] = 'Шаблон доставки – это сценарий доставки товара, включающий в себя все ваши курьерские службы. Вариант по-умолчанию: Почта России';
?>