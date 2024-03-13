<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'Kaspi.kz api';

$strSName = $strLang.'SETTINGS_NAME_';
$strSHint = $strLang.'SETTINGS_HINT_';
$MESS[$strSName.'AUTH_TOKEN'] = 'Токен авторизации';
	$MESS[$strSHint.'AUTH_TOKEN'] = 'Ваш токен авторизации';
$MESS[$strSName.'COMPANY_NAME'] = 'Название компании';
	$MESS[$strSHint.'COMPANY_NAME'] = 'Название вашей компании';
$MESS[$strSName.'COMPANY_ID'] = 'ID компании';
	$MESS[$strSHint.'COMPANY_ID'] = 'Идентификатор вашей компании в системе kaspi.kz';
$MESS[$strSName.'USE_CACHE'] = 'Использовать кеш';
	$MESS[$strSHint.'USE_CACHE'] = 'Использовать кеш обращений по kaspi.kz api';	

// Fields: General

$MESS[$strName.'sku'] = 'Идентификатор';
	$MESS[$strHint.'sku'] = 'Идентификатор';	
$MESS[$strName.'model'] = 'Название';
	$MESS[$strHint.'model'] = 'Название товара';
$MESS[$strName.'title'] = 'Название';
	$MESS[$strHint.'title'] = 'Название товара';	
$MESS[$strName.'brand'] = 'Бренд';
	$MESS[$strHint.'brand'] = 'Производитель товара';
$MESS[$strName.'category'] = 'Категория';
	$MESS[$strHint.'category'] = 'Категория товара';
$MESS[$strName.'attributes'] = 'Характеристики';
	$MESS[$strHint.'attributes'] = 'Характеристики товара';
$MESS[$strName.'images'] = 'Изображения';
	$MESS[$strHint.'images'] = 'Изображения товара';	
$MESS[$strName.'description'] = 'Описание';
	$MESS[$strHint.'description'] = 'Описание товара';	
$MESS[$strName.'availabilities.availability@available'] = 'Доступность товара на пункте самовывоза';
	$MESS[$strHint.'availabilities.availability@available'] = 'Доступность товара на пункте самовывоза';
$MESS[$strName.'availabilities.availability@storeId'] = 'Идентификатор пункта самовывоза';
	$MESS[$strHint.'availabilities.availability@storeId'] = 'Идентификатор пункта самовывоза';
$MESS[$strName.'cityprices'] = 'Цена, включающая НДС по городу';
	$MESS[$strHint.'cityprices'] = 'Цена, включающая НДС по городу';
$MESS[$strName.'cityprices.cityprice@cityId'] = 'Идентификатор города';
	$MESS[$strHint.'cityprices.cityprice@cityId'] = 'Идентификатор города';
$MESS[$strName.'cityprices.cityprice'] = 'Цена, включающая НДС в городе';
	$MESS[$strHint.'cityprices.cityprice'] = 'Цена, включающая НДС в городе';	
$MESS[$strName.'price'] = 'Цена, включающая НДС';
	$MESS[$strHint.'price'] = 'Цена, включающая НДС';
?>