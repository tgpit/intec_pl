<?php
$strLang = 'ACRIT_EXP_FIELDVALUE_PHP_';
// General
$MESS[$strLang.'NAME'] = 'PHP-код';
$MESS[$strLang.'PLACEHOLDER'] = 'PHP-код должен вернуть значение через return.
Открывающие <? и закрывающие ?> не требуются.
Вместо print_r() можно использовать Helper::P(). Весь отладочный вывод доступен только в предпросмотре по каждому товару!
Доступны переменные:
$intElementId - ID элемента
$intIBlockId - ID инфоблока
$intProfileId - ID профиля
$arElement - массив данных элемента
$arProfile - массив данных профиля
$obField - объект текущего поля
$obPlugin - объект текущего плагина
';
$MESS[$strLang.'URL_FAQ'] = 'Помощь по работе с PHP-кодом';
$MESS[$strLang.'PHP_ERROR'] = '[Товар ID=#ELEMENT_ID#] Ошибка выполнения PHP-кода (строка #LINE#) в поле #FIELD#: #ERROR#';

?>