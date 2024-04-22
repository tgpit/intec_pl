<?
use \Bitrix\Main\EventManager;
use \Bitrix\Main\Loader;

include_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/val_phone.php");

/* send calltouch */
Bitrix\Main\EventManager::getInstance()->addEventHandler('sale','OnSaleOrderSaved','sendOrderToCalltouch');
  
function sendOrderToCalltouch(Bitrix\Main\Event $event){
    $isNew = $event->getParameter("IS_NEW"); if (!$isNew) return;
    $order = $event->getParameter("ENTITY");
    $propertyCollection = $order->getPropertyCollection();
    $name = !empty($propertyCollection->getPayerName()) ? $propertyCollection->getPayerName()->getValue() : '';
    $phone = !empty($propertyCollection->getPhone()) ? $propertyCollection->getPhone()->getValue() : '';
    $email = !empty($propertyCollection->getUserEmail()) ? $propertyCollection->getUserEmail()->getValue()  : '';
    $order_fields = $propertyCollection->getArray();
    foreach ($order_fields['properties'] as $fieldKey => $fieldValue) {
        if (empty($name) && $fieldValue['CODE']=='NAME'){ $name = $propertyCollection->getItemByOrderPropertyId($fieldValue['ID'])->getValue(); }
        if (empty($last_name) && $fieldValue['CODE']=='LAST_NAME'){ $last_name = $propertyCollection->getItemByOrderPropertyId($fieldValue['ID'])->getValue(); }
        if (empty($phone) && $fieldValue['CODE']=='PHONE'){ $phone = $propertyCollection->getItemByOrderPropertyId($fieldValue['ID'])->getValue(); }
        if (empty($email) && $fieldValue['CODE']=='EMAIL'){ $email = $propertyCollection->getItemByOrderPropertyId($fieldValue['ID'])->getValue(); }
    }
    $fio = ''; if (!empty($name) && !empty($last_name)){ $fio = $name . ' ' . $last_name; } else { if (!empty($name)){ $fio = $name; }  if (!empty($last_name)){ $fio = $last_name; } }
 
    $call_value = $_COOKIE['_ct_session_id']; if (isset($_POST['call_value'])){$call_value = $_POST['call_value'];}
    $ct_data = array(
        'subject'       =>  'Оформление заказа',
        'fio'           =>   isset($fio) ? $fio : '',
        'phoneNumber'   =>   isset($phone) ? $phone : '',
        'email'         =>   isset($email) ? $email : '',
        'requestUrl'    =>   $_SERVER['HTTP_REFERER'],
        'sessionId'     =>   $call_value
    );
    $ct_data_str = http_build_query($ct_data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.calltouch.ru/calls-service/RestAPI/requests/64230/register/");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded;charset=utf-8"));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $ct_data_str);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $calltouch = curl_exec ($ch);
    curl_close ($ch);
}

AddEventHandler('form', 'onAfterResultAdd', 'sendRequestToCalltouch');
  
function sendRequestToCalltouch($WEB_FORM_ID, $resultId){  

	// Получаем данные о результате формы
	/*
	$arResult = array();
	$arAnswers = array();
	$res = CFormResult::GetDataByID($resultId, array(), $arResult, $arAnswers);
	 
	// Проверяем содержимое селекторов
	$log_content = "\n \n" . "request " . date("Y.m.d H:i") . "\n";
	$log_content .= "Result ID: " . $resultId . "\n";
	$log_content .= "Web form ID: " . $WEB_FORM_ID . "\n";
	$log_content .= "Result data: " . print_r($arResult, true) . "\n";
	$log_content .= "Answers data: " . print_r($arAnswers, true) . "\n";
	file_put_contents(__DIR__ . '/calltouch_log_selector.txt', $log_content, FILE_APPEND | LOCK_EX);
	 
	if (defined("ADMIN_SECTION")) {
		return;
	}
	*/

    if (defined("ADMIN_SECTION")){ return; }
    $arResult = array(); $arAnswers = array();
    $res = CFormResult::GetDataByID($resultId, array(), $arResult, $arAnswers);
    foreach ($arAnswers as $fieldKey => $fieldValue) {
        foreach ($fieldValue as $fKey => $fValue) {
            $com_code = $fValue['VARNAME'];
            if ($com_code=="NAME" || $com_code=="FIO" || $com_code=="CLIENT_NAME" || $com_code=="new_field_25963" || $com_code=="INITIALS" || $com_code=="SIMPLE_QUESTION_907"){ $name = $fValue["USER_TEXT"] ? $fValue["USER_TEXT"] : $fValue["VALUE"]; }
            if ($com_code=="PHONE" || $com_code=="new_field_14911" || $com_code=="SIMPLE_QUESTION_396"){ $phone = $fValue["USER_TEXT"] ? $fValue["USER_TEXT"] : $fValue["VALUE"]; }
            if ($com_code=="EMAIL"|| $com_code=="E_MAIL" || $com_code=="new_field_62627" || $com_code=="SIMPLE_QUESTION_132"){ $email = $fValue["USER_TEXT"] ? $fValue["USER_TEXT"] : $fValue["VALUE"]; }
            if ($com_code=="QUESTION"){ $comment = $fValue["USER_TEXT"] ? $fValue["USER_TEXT"] : $fValue["VALUE"]; }
        }
    }
    
    $ct_site_id = '64230'; // ID сайта Calltouch
    if ($phone || $name || $mail){
        $call_value = $_COOKIE['_ct_session_id']; if (isset($_POST['call_value'])){$call_value = $_POST['call_value'];}
        $ct_data = array(
            'subject'       =>   isset($arResult["NAME"]) ? $arResult["NAME"] : 'Заявка',
            'fio'           =>   isset($name) ? $name : '',
            'phoneNumber'   =>   isset($phone) ? $phone : '',
            'email'         =>   isset($email) ? $email : '',
            'requestUrl'    =>   $_SERVER['HTTP_REFERER'],
            'comment'       =>   isset($comment) ? $comment : '',
            'sessionId'     =>   $call_value
        );
        $ct_data_str = http_build_query($ct_data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.calltouch.ru/calls-service/RestAPI/requests/$ct_site_id/register/");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded;charset=utf-8"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $ct_data_str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $calltouch = curl_exec ($ch);
        curl_close ($ch);
    }
}
/* send calltouch */