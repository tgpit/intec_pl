<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

# General
$MESS[$strLang.'NAME'] = 'СберМегаМаркет (цены и остатки, стандартная схема)';

# Fields
$MESS[$strHead.'HEADER_GENERAL'] = 'Общая информация';
	$MESS[$strName.'offerId'] = 'Идентификатор';
		$MESS[$strHint.'offerId'] = 'Идентификатор оффера продавца';
$MESS[$strHead.'HEADER_PRICES'] = 'Передача цен';
	$MESS[$strName.'price'] = 'Цена';
		$MESS[$strHint.'price'] = 'Цена оффера продавца (число).';
	$MESS[$strName.'isDeleted'] = 'Статус цены';
		$MESS[$strHint.'isDeleted'] = 'Статус цены оффера: затираем или записываем новое значение.<br/><br/>
		Логика работы обновления цен по api:
		<ul>
			<li>Если в isDeleted установлено значение false, то старое значение не удаляется, а дополнительно записывается новое значение. В такой ситуации будет показано две цены.</li>
			<li>Если в параметре установлено значение true, то старое удаляется и показывается только новое значение.</li>
			<li>Первое обновление цен по апи должно быть с параметром isDeleted false всегда. Зачеркнутая цена берётся из тега oldprice в фиде, если он есть.</li>
			<li>Если передать isDeleted = true, то установленная цена будет сброшена до той, которая передавалась в товарном фиде.</li>
		</ul>';
$MESS[$strHead.'HEADER_STOCKS'] = 'Передача остатков';
$MESS[$strName.'quantity'] = 'Остаток';
	$MESS[$strHint.'quantity'] = 'Остаток (число).';
#
$MESS[$strLang.'STEP_PROCESS_PRICES'] = 'Выгрузка цен';
$MESS[$strLang.'STEP_PROCESS_STOCKS'] = 'Выгрузка остатков';

$MESS[$strLang.'LOG_PRICES_SUCCESS'] = 'Цены успешно выгружены (/manualPrice/save), всего: #COUNT#. Предупреждения: #WARNINGS#.';
$MESS[$strLang.'LOG_PRICES_ERROR_TITLE'] = 'Ошибка выгрузки цен (/manualPrice/save) - подробности в логе';
$MESS[$strLang.'LOG_PRICES_ERROR'] = 'Ошибка выгрузки цен (/manualPrice/save) [ответ #RESPONSE_CODE#]: #RESPONSE#. Запрос: #REQUEST_URL#, #REQUEST#.';

$MESS[$strLang.'LOG_STOCKS_SUCCESS'] = 'Остатки успешно выгружены (/stock/update), всего: #COUNT#. Предупреждения: #WARNINGS#.';
$MESS[$strLang.'LOG_STOCKS_ERROR_TITLE'] = 'Ошибка выгрузки остатков (/stock/update) - подробности в логе';
$MESS[$strLang.'LOG_STOCKS_ERROR'] = 'Ошибка выгрузки остатков (/stock/update) [ответ #RESPONSE_CODE#]: #RESPONSE#. Запрос: #REQUEST_URL#, #REQUEST#.';

$MESS[$strLang.'LOG_ENVIRONMENT'] = 'Среда: #TYPE#';
	$MESS[$strLang.'LOG_ENVIRONMENT_TEST'] = 'тестовая';
	$MESS[$strLang.'LOG_ENVIRONMENT_PROD'] = 'продуктовая';
