<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'Kaspi.kz xml';

$strSName = $strLang.'SETTINGS_NAME_';
$strSHint = $strLang.'SETTINGS_HINT_';
$MESS[$strSName.'COMPANY_NAME'] = 'Название компании';
	$MESS[$strSHint.'COMPANY_NAME'] = 'Название вашей компании';
$MESS[$strSName.'COMPANY_ID'] = 'ID компании';
	$MESS[$strSHint.'COMPANY_ID'] = 'Идентификатор вашей компании в системе kaspi.kz';

// Fields: General

$MESS[$strName.'@sku'] = 'Идентификатор';
	$MESS[$strHint.'@sku'] = 'Идентификатор';	
$MESS[$strName.'model'] = 'Название';
	$MESS[$strHint.'model'] = 'Название товара';
$MESS[$strName.'brand'] = 'Бренд';
	$MESS[$strHint.'brand'] = 'Производитель товара';	
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
$MESS[$strName.'loanPeriod'] = 'срок кредита';
	$MESS[$strHint.'loanPeriod'] = 'срок кредита';
?>