<?php
$strEventName = 'Экспорт «АКРИТ»: отправка файла';
$strEventDescription = '
#FROM# - От кого
#TO# - Кому
#SUBJECT# - Тема сообщения

#FILE_SIZE# - размер файла

#PROFILE_ID# - ID профиля
#PROFILE_NAME# - Название профиля
#PROFILE_URL# - Ссылка на профиль

#MODULE_ID# - Код модуля
#MODULE_NAME# - Название модуля

#DATE_END# - Дата окончания
#TIME_TOTAL# - Общее время выгрузки

#ELEMENTS_COUNT# - Всего товаров и предложений
#ELEMENTS_Y# - Товаров успешно
#ELEMENTS_N# - Товаров с ошибкой
#OFFERS_N# - Предложений успешно
#OFFERS_Y# - Предложений с ошибкой';
$strMessage = '
Сгенерирован новый файл выгрузки (размер: #FILE_SIZE#).<br>
<br>
Профиль: [<a href="#PROFILE_URL#">#PROFILE_ID#</a>] #PROFILE_NAME#<br>
Модуль: [#MODULE_ID#] #MODULE_NAME#<br>
<br>
Дата окончания: #DATE_END#<br>
Общее время выгрузки: #TIME_TOTAL#<br><br>
<br>
Всего товаров и предложений: #ELEMENTS_COUNT#<br>
Товаров успешно: #ELEMENTS_Y#<br>
Товаров с ошибкой: #ELEMENTS_N#<br>
Предложений успешно: #OFFERS_N#<br>
Предложений с ошибкой: #OFFERS_Y#<br>';
if(!defined('BX_UTF') || BX_UTF !== true){
	$strEventName = $GLOBALS['APPLICATION']->convertCharset($strEventName, 'UTF-8', 'CP1251');
	$strEventDescription = $GLOBALS['APPLICATION']->convertCharset($strEventDescription, 'UTF-8', 'CP1251');
	$strMessage = $GLOBALS['APPLICATION']->convertCharset($strMessage, 'UTF-8', 'CP1251');
}
$strEventType = 'ACRIT_EXPORT_FILE';
$strLanguageId = 'ru';
$arEventType = [
	'LID' => $strLanguageId,
	'EVENT_NAME' => $strEventType,
	'NAME' => $strEventName,
	'DESCRIPTION' => ltrim($strEventDescription),
	'SORT' => 150,
	'EVENT_TYPE' => 'email',
];
$arEventMessage = [
	'LANGUAGE_ID' => $strLanguageId,
	'EVENT_NAME' => $strEventType,
	'ACTIVE' => 'Y',
	'EMAIL_FROM' => '#FROM#',
	'EMAIL_TO' => '#TO#',
	'SUBJECT' => '#SUBJECT#',
	'MESSAGE' => ltrim($strMessage),
	'BODY_TYPE' => 'html',
];
$bEventTypeExists = true;
if(!\Bitrix\Main\Mail\Internal\EventTypeTable::getList(['filter' => ['EVENT_NAME' => $strEventType, 'LID' => $strLanguageId]])->fetch()){
	if(!($obResult = \Bitrix\Main\Mail\Internal\EventTypeTable::add($arEventType))->isSuccess()){
		$bEventTypeExists = false;
	}
}
$bEventMessageExists = false;
if($bEventTypeExists){
	$bEventMessageExists = true;
	if(!\Bitrix\Main\Mail\Internal\EventMessageTable::getList(['filter' => ['EVENT_NAME' => $strEventType, 'LANGUAGE_ID' => $strLanguageId]])->fetch()){
		if(($obResult = \Bitrix\Main\Mail\Internal\EventMessageTable::add($arEventMessage))->isSuccess()){
			$intMessageId = $obResult->getId();
			$resSites = \CSite::getList($by = 'SORT', $order =' ASC', ['ACTIVE' => 'Y']);
			while($arSite = $resSites->getNext()) {
				\Bitrix\Main\Mail\Internal\EventMessageSiteTable::add(['EVENT_MESSAGE_ID' => $intMessageId, 'SITE_ID' => $arSite['ID']]);
			}
		}
		else{
			$bEventMessageExists = false;
		}
	}
}
return $bEventMessageExists;
