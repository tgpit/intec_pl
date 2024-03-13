<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'Авито (Выгрузка остатков)';

// Fields
$MESS[$strName.'id'] = 'Идентификатор товара';
	$MESS[$strHint.'id'] = 'Укажите здесь идентификатор товара.';
$MESS[$strName.'avitoId'] = 'Идентификатор объявления на Avito';
	$MESS[$strHint.'avitoId'] = 'Укажите здесь числовой код avitoId (<a href="https://support.avito.ru/articles/2209" target="_blank">идентификатор объявления на Avito</a>).<br/><br/>
	Если вы хотя бы раз пользовались автозагрузкой, можно вместо AvitoID заполнить колонку ID и внести в неё ID из вашей системы учёта.';
$MESS[$strName.'stock'] = 'Остаток';
	$MESS[$strHint.'stock'] = 'Укажите здесь остаток товара (число). Максимальное значение - 999999.<br/><br/>
	Если товар постоянно пополняется — поставьте символ звёздочки <code><b>*</b></code>';
