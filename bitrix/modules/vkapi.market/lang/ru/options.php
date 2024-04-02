<?php

$MESS["AP_OPTION.URL_UTM.HELP"] = "Указанная строка параметров с utm метками будет добавлена в конец ссылки. \nПлейсхолдер {group_id} будет заменен на идентификатор группы. <br>\nПлейсхолдер {export_id} будет заменен на идентификатор настроек выгрузки. <br>\nПлейсхолдер {sku} будет заменен на артикул из настроек выгрузки. <br>\nПример заполнения: utm_source=vk&utm_medium=free&utm_campaign={group_id}_{export_id}_{sku}";
$MESS["AP_OPTION.URL_UTM"] = "Добавить UTM метки ";
$MESS["AP_OPTION.URL_HTTPS"] = "Использовать https";
$MESS["AP_OPTION.TIME_TO_START_EXEC"] = "Запускать экспорт в заданное время <nobr>(напр. 04:00)</nobr>";
$MESS["AP_OPTION.TIMEOUT"] = "Лимит времени на выполнение операций за 1 вызов (сек.), по умолчанию&nbsp;-&nbsp;45";
$MESS["AP_OPTION.STATUS_6"] = "Возвращен";
$MESS["AP_OPTION.STATUS_5"] = "Отменен";
$MESS["AP_OPTION.STATUS_4"] = "Выполнен";
$MESS["AP_OPTION.STATUS_3"] = "Доставляется";
$MESS["AP_OPTION.STATUS_2"] = "Собирается";
$MESS["AP_OPTION.STATUS_1"] = "Согласуется";
$MESS["AP_OPTION.STATUS_0"] = "Новый";
$MESS["AP_OPTION.SITE"] = "Адрес сайта (<span style=\"color:red;\">http://site.ru</span>)";
$MESS["AP_OPTION.SALE_PROPERTY_WIDTH"] = "Ширина заказа";
$MESS["AP_OPTION.SALE_PROPERTY_WEIGHT"] = "Вес заказа";
$MESS["AP_OPTION.SALE_PROPERTY_VKORDER"] = "Номер заказа в ВК";
$MESS["AP_OPTION.SALE_PROPERTY_PHONE"] = "Номер телефона";
$MESS["AP_OPTION.SALE_PROPERTY_LENGTH"] = "Длина заказа";
$MESS["AP_OPTION.SALE_PROPERTY_HEIGHT"] = "Высота заказа";
$MESS["AP_OPTION.SALE_PROPERTY_FIO"] = "ФИО";
$MESS["AP_OPTION.SALE_PROPERTY_COMMENT_FOR_USER"] = "Комментарий для пользователя в ВК";
$MESS["AP_OPTION.SALE_PROPERTY_ADDRESS"] = "Адреса доставки";
$MESS["AP_OPTION.PROXY_PORT"] = "Порт";
$MESS["AP_OPTION.PROXY_PASS"] = "Пароль";
$MESS["AP_OPTION.PROXY_LOGIN"] = "Логин";
$MESS["AP_OPTION.PROXY_HOST"] = "Хост";
$MESS["AP_OPTION.PERSONAL_TYPE"] = "Тип плательщика";
$MESS["AP_OPTION.PAYMENT_ID"] = "Способ оплаты";
$MESS["AP_OPTION.ITEM_EXPORT_TIME_LIMIT"] = "Длина шага в секундах при выгрузке товаров (сек.)\n <br> Не задавайте большое значение, так как агент\n<br> вызывается каждую минуту, если \n<br>он не завершится вовремя, следующий \n<br> вызов отложится на 10 минут \n<br> (по умолчанию: 45)";
$MESS["AP_OPTION.ITEM_COUNT_PREPIRE"] = "При подготовке обрабатывать за раз элементов<br>(по умолчанию: 1000)";
$MESS["AP_OPTION.ITEM_COUNT"] = "При выгрузке обрабатывать за раз элементов<br>(по умолчанию: 25)";
$MESS["AP_OPTION.GROUP.VK_LINK"] = "Формирование ссылки на товар";
$MESS["AP_OPTION.GROUP.VK"] = "Данные приложения ВКонтакте";
$MESS["AP_OPTION.GROUP.SALE_STATUS"] = "Соответствие статусов";
$MESS["AP_OPTION.GROUP.SALE_OTHER"] = "Прочее";
$MESS["AP_OPTION.GROUP.PROXY"] = "Прокси (с серверов Украины заблокирован доступ к ВК)";
$MESS["AP_OPTION.GROUP.ANTIGATE"] = "Антикапча";
$MESS["AP_OPTION.ENABLE_PROXY"] = "Использовать прокси";
$MESS["AP_OPTION.DISABLE_UPDATE_PICTURE"] = "Запретить обновлять картинки <br>Рекомендуется использовать на сайта на которых 1С каждый день обновляет картинки";
$MESS["AP_OPTION.DESCRIPTION_LENGTH_LIMIT"] = "Максимальная длина описания товара в вконтакте,<br> по умолчанию 5000 (символов)";
$MESS["AP_OPTION.DELIVERY_ID"] = "Способ доставки";
$MESS["AP_OPTION.DEBUG_CONNECT"] = "Логирование запросов и ответов ВКонтакте<br><small>Используется модуль логирования</small>";
$MESS["AP_OPTION.DEBUG"] = "Заполнять журнал операций <br><small>Использовать только при тестировании и отладке</small>";
$MESS["AP_OPTION.DEBUG.EXPAND"] = "<div class='vkapi-market-admin-option-log-clear vkapi-market-admin-btn vkapi-market-admin-btn--button vkapi-market-admin-btn--success'>Очистить</div>";
$MESS["AP_OPTION.CRON"] = "Файл автоэкспорта на cron'e <br><a href='https://bxmaker.ru/doc/vk/avtoeksport-tovarov-na-cron/' target='_blank'>Подробнее</a>";
$MESS["AP_OPTION.CONNECT_INTERVAL"] = "Интервал между запросами к API ВКонтакте, сек.";
$MESS["AP_OPTION.APP_SECRET"] = "Защищённый ключ";
$MESS["AP_OPTION.APP_ID"] = "ID приложения";
$MESS["AP_OPTION.ANTIGATE_KEY"] = "Antigate Key <br> <a href='https://BXmaker.ru/anticaptcha' target='_blank'>Регистрация в сервисе автораспознавания captcha</a>";
$MESS["AP_OPTION.ALBUM_COUNT_PREPIRE"] = "При подготовке обрабатывать за раз разделов<br>(по умолчанию: 200)";
$MESS["AP_OPTION.ALBUM_COUNT"] = "При выгрузке обрабатывать за раз разделов<br>(по умолчанию: 30)";
$MESS["AP_OPTION.ADD_TO_VK_PACK_LENGTH"] = "Ограничить количество товаров в пачке для выгрузки в 1 итерации,<br> (1 - 25) по умолчанию 1. На более мощных серверах можно постепенно увеличить значение максимум до 25, если не будет возникать большого количества ошибок в журнале операций";
$MESS["AP_EDIT_TAB.SITE"] = "Сайт - #SITE#";
$MESS["AP_EDIT_TAB.SALE"] = "Магазин - #SITE_NAME#";
$MESS["AP_EDIT_TAB.GEN"] = "Главное";
$MESS["AP_EDIT_TAB.ACCESS"] = "Управление доступом";
$MESS["AP_OPTION.GROUP.DELIVERY"] = "Доставка";
$MESS["AP_OPTION.DELIVERY_ID_COURIER"] = "Курьером";
$MESS["AP_OPTION.DELIVERY_ID_POCHTA"] = "Доставка почтой";
$MESS["AP_OPTION.DELIVERY_ID_POINT"] = "Пункт выдачи";
$MESS["AP_OPTION.DELIVERY_ID"] = "Самовывоз (по умолчанию)";