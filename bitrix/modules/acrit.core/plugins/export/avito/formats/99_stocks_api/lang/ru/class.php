<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'Авито (Выгрузка остатков по API)';

//
$MESS[$strLang.'CHECK_SUCCESS'] = 'Успешно! Доступ разрешён!';
$MESS[$strLang.'CHECK_ERROR'] = 'Ошибка! Проверьте поля Client_id и Client_secret.';

// Fields
$MESS[$strName.'external_id'] = 'Внешний идентификатор объявления';
	$MESS[$strHint.'external_id'] = 'Идентификатор объявления на сайте клиента.<br/><br/>
	Здесь должно быть то же значение, что и значения поля «Идентификатор объявления» при XML-выгрузках на Avito.';
$MESS[$strName.'item_id'] = 'Номер объявления на Avito';
	$MESS[$strHint.'item_id'] = 'Идентификатор объявления на Avito (номер объявления).';
$MESS[$strName.'quantity'] = 'Остаток';
	$MESS[$strHint.'quantity'] = 'Количество товара, шт.';


$MESS[$strLang.'ERROR_GET_TOKEN'] = 'Ошибка получения токена: #ERROR#.';
$MESS[$strLang.'ERROR_REQUEST_1'] = 'Ошибка выполнения запроса [#STATUS#]: #RESPONSE#.';
$MESS[$strLang.'ERROR_REQUEST_2'] = 'Некорректный код ответа на запрос: #STATUS#, #RESPONSE#.';
$MESS[$strLang.'ERROR_AUTH'] = 'Ошибка авторизации. Проверьте значения полей Client_id и Client_secret.';
$MESS[$strLang.'ERROR_ID_EMPTY'] = '[Элемент #ELEMENT_ID#] Для объявления не заполнен ни один из идентификаторов (external_id, item_id).';