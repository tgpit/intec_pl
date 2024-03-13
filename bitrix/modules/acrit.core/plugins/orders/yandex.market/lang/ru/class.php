<?

\Acrit\Core\Orders\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_PLUGIN_NAME'] = 'ЯндексМаркет';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_PRODUCTS_ID_FIELD_NAME'] = 'Идентификатор товара Мерчанта';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_SETTINGS_HEADING'] = 'Данные для подключения к площадке';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_SETTINGS_CLIENT_ID_HINT'] = '<a target="_blank" href="https://yandex.ru/dev/oauth/doc/dg/tasks/register-client-docpage/" >Зарегистрировать свое приложение.</a>Приложению присваивается уникальный идентификатор — параметр client_id на Яндекс.OAuth.После регистрации приложение сможет получить OAuth-токен для доступа к данным Маркета.';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_SETTINGS_CLIENT_ID'] = 'Идентификатор приложения Client ID';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_GET_OAUTH_TOKEN'] = 'Получить токен';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_SETTINGS_TOKEN'] = 'Токен OAuth <b>запросы к Маркету</b>';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_SETTINGS_TOKEN_HINT'] = '<a target="_blank" href="https://yandex.ru/dev/oauth/doc/dg/tasks/get-oauth-token.html" >Отладочный токен</a>';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_SETTINGS_CAMPAIGN_ID'] = 'Идентификатор кампании';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_SETTINGS_TOKEN_IN'] = 'Авторизационный токен <b>запросы от Маркета</b>';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_SETTINGS_CHECK_CONN'] = 'Проверить подключение';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_CHECK_ERROR'] = 'Ошибка: ';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_CHECK_SUCCESS'] = 'Успешно';

$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_SETTINGS_EXTERNAL_REQUEST_URL'] = 'Адрес внешнего запроса';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_SETTINGS_EXTERNAL_REQUEST'] = 'Разрешить внешний запрос';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_SETTINGS_HEADING'] = 'Данные для подключения к площадке';

$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_SETTINGS_API_KEY_HINT'] = 'API Key';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_SETTINGS_API_KEY'] = 'API Key';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_SETTINGS_CHECK_CONN'] = 'Проверить подключение';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_CONTACT_TITLE'] = 'Данные покупателя';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_CONTACT_CUSTOMER_FIO'] = 'ФИО';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_CONTACT_ADDRESS'] = 'Номер телефона';
// ORDER STATUS
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_UNPAID'] = 'Заказ оформлен, но еще не оплачен (если выбрана оплата при оформлении)';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_PENDING'] = 'По заказу требуются дополнительные действия со стороны Маркета';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_PENDING_ANTIFRAUD'] = 'Маркет проверяет, является ли заказ мошенническим.';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_PROCESSING'] = 'Заказ находится в обработке';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_PROCESSING_STARTED'] = 'Заказ подтвержден, его можно начать обрабатывать';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_PICKUP'] = 'Заказ доставлен в пункт самовывоза';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_PICKUP_PICKUP_SERVICE_RECEIVED'] = 'Заказ поступил в пункт выдачи';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_PICKUP_PICKUP_USER_RECEIVED'] = 'Покупатель получил заказ';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_DELIVERY'] = 'Заказ передан в службу доставки';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_DELIVERED'] = 'Заказ получен покупателем';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_CANCELLED'] = 'Заказ отменен';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_CANCELLED_DELIVERY_SERVICE_UNDELIVERED'] = 'Служба доставки не смогла доставить заказ';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_CANCELLED_PENDING_EXPIRED'] = 'Магазин не ответил на запрос POST /order/accept о новом заказе';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_CANCELLED_PROCESSING_EXPIRED'] = 'Магазин не обработал заказ в течение семи дней';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_CANCELLED_REPLACING_ORDER'] = 'Покупатель решил заменить товар другим по собственной инициативе';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_CANCELLED_RESERVATION_EXPIRED'] = 'Покупатель не завершил оформление зарезервированного заказа в течение 10 минут';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_CANCELLED_RESERVATION_FAILED'] = 'Маркет не может продолжить дальнейшую обработку заказа';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_CANCELLED_SHOP_FAILED'] = 'Магазин не может выполнить заказ';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_CANCELLED_SHOP_PENDING_CANCELLED'] = 'Магазин отклонил новый заказ в ответ на запрос POST /order/accept';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_CANCELLED_WAREHOUSE_FAILED_TO_SHIP'] = 'Вы не отгрузили товар со склада';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_CANCELLED_USER_CHANGED_MIND'] = 'Покупатель отменил заказ по собственным причинам';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_CANCELLED_USER_NOT_PAID'] = 'Покупатель не оплатил заказ (для типа оплаты PREPAID) в течение 30 минут';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_CANCELLED_USER_REFUSED_DELIVERY'] = 'Покупателя не устраивают условия доставки';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_CANCELLED_USER_REFUSED_PRODUCT'] = 'Покупателю не подошел товар';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_CANCELLED_USER_REFUSED_QUALITY'] = 'Покупателя не устраивает качество товара';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_STATUSES_CANCELLED_USER_UNREACHABLE'] = 'Не удалось связаться с покупателем';
// TAB Свойства Сопоставление со свойствами заказов магазина
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_id'] = 'Идентификатор заказа.';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_paymentType'] = 'Тип оплаты заказа.';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_paymentMethod'] = 'Способ оплаты заказа.';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_taxSystem'] = 'Система налогообложения (СНО) магазина на момент оформления заказа.';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_deliveryServiceId'] = 'Идентификатор службы доставки.';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_CUSTOMERADDRESS'] = 'Адрес покупателя';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_CONFIRMEDTIMELIMIT'] = 'Время до истечения срока подтверждения';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_PACKINGTIMELIMIT'] = 'Время до истечения срока комплектации';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_SHIPPINGTIMELIMIT'] = 'Время до истечения срока отгрузки';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_CREATIONDATE'] = 'Дата создания заказа';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_DELIVERYDATEFROM'] = 'Дата доставки покупателю (c)';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_DELIVERYDATETO'] = 'Дата доставки покупателю (до)';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_SHIPPMENTDATEFROM'] = 'Интервал отгрузки, отгрузить с';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_SHIPMENTDATETO'] = 'Интервал отгрузки, отгрузить до';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_status'] = 'Статус заказа status';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_status_PROCESSING'] = 'заказ находится в обработке';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_status_UNPAID'] = 'заказ оформлен, но еще не оплачен (если выбрана оплата при оформлении)';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_status_PENDING'] = 'по заказу требуются дополнительные действия со стороны Маркета.';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_status_PICKUP'] = 'заказ доставлен в пункт самовывоза';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_status_DELIVERY'] = 'заказ передан в службу доставки';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_status_DELIVERED'] = 'заказ получен покупателем';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_status_CANCELLED'] = 'заказ отменен';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_substatus'] = 'Статус заказа substatus';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_FIELDS_shipmentDate'] = 'День, в который нужно отгрузить заказ службе доставки.';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_PRODUCTS_MESSAGE'] = 'В данной версии плагина заказы создаются без товаров из-за ограничений со стороны Sbermegamarket.';

$MESS[$strName . 'id'] = 'Ваш SKU';
$MESS[$strHint . 'id'] = 'Ваш SKU. Уникальный код, который вы используете для идентификации товара (если не используете — придумайте). Ваш SKU может состоять из:
		<ul>
			<li>цифр;</li>
			<li>латинских букв;</li>
			<li>русских букв (кроме ё);</li>
			<li>символов . , \ / ( ) [ ] - =.</li>
		</ul>
		Максимальная длина — 80 символов. Должен быть уникальным для каждого товара.<br/>';
$MESS['ACRIT_ORDERS_PLUGIN_YANDEXMARKET_DESCRIPTION'] = 'Описание настройки профиля <a href="https://www.acrit-studio.ru/technical-support/configuring-the-module-export-on-trade-portals/nastroyka-plagina-obmena-zakazami-s-yandeks-market/" target="_blank">перейти</a>
    <br/>
    
    ';	
?>