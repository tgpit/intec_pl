<?
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', false);


// Implicitly flush the buffer(s)
ini_set('implicit_flush', true);
ob_implicit_flush(true);
header("Content-type: text/plain");
header('Cache-Control: no-cache');

set_time_limit(500000000);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$arTotal = array();

$str = file_get_contents("./muse.json");

$response = json_decode($str, True);

$arTotal['Получено пользователей'] = count($response);
$arTotal['Занесено'] = 0;

if(!CModule::IncludeModule("iblock")){die();}

$tableOfGroups = [
    '1' => 1,
    '2' => 2,
    '3' => 3,
    '4' => 4,
    '5' => 5,
    '6' => 6,
    '7' => 7,
    '8' => 8,
];

$connection = Bitrix\Main\Application::getConnection('default');

foreach ($response as $key => $value){

    $arFields = [];

    foreach ($value as $k => $v){

        switch ($k) {
            case 'PERSONAL_PHOTO':
//                if($v){
//                    $arIMAGE = CFile::MakeFileArray($value['PERSONAL_PHOTO']);
//                    $arIMAGE["MODULE_ID"] = "main";
//                    $arFields[$k] = $arIMAGE;
//                }
                break;
            case 'A':
                $groupIDS = [];
                //По таблице соответствия групп проставляем требуемые уровни доступа
                foreach ($v['GROUPS'] as $name => $val){
                    array_push($groupIDS,$tableOfGroups[$val]);
                }
                $arFields['GROUP_ID'] = $groupIDS;
                break;
            case 'LID':
                break;
            case 'IS_ONLINE':
                break;
            default:
                if($v){
                    $arFields[$k] = $v;
                }
                break;
        }
    }

//var_dump($arFields);
$pwd = $arFields['PASSWORD'];
$cpwd = $arFields['CHECKWORD'];
$arFields['PASSWORD'] = 'blabla';
$arFields['CONFIRM_PASSWORD'] = 'blabla';
$arFields['CHECKWORD'] = 'blabla';
unset($arFields["ID"]);
unset($arFields["TIMESTAMP_X"]);
unset($arFields["LAST_LOGIN"]);
unset($arFields["DATE_REGISTER"]);
unset($arFields["XML_ID"]);
unset($arFields["CHECKWORD_TIME"]);
//var_dump($arFields);


$n = 1;
$rr = '';

$str = trim($arFields["NAME"]);
if ((!preg_match('/^[a-zA-Z0-9а-яёА-ЯЁ_.\/\-]+$/ui',$str))&&($str!='')) {
 $rr .= 'Хуевое имя ';
 $n = 0;
};
$str = trim($arFields["LAST_NAME"]);
if ((!preg_match('/^[a-zA-Z0-9а-яёА-ЯЁ_.\/\-]+$/ui',$str))&&($str!='')) {
 $rr .= 'Хуевая фамилия ';
 $n = 0;
}
$str = trim($arFields["LOGIN"]);
if (!preg_match('/^[a-zA-Z0-9а-яёА-ЯЁ@_.\-]+$/ui',$str)) {
 $rr .= 'Хуевый логин ';
 $n = 0;
}
if ($str[0]=='@'){
 $rr .= 'Хуевый логин ';
 $n = 0;
}
$str = trim($arFields["EMAIL"]);
if (!preg_match('/^[a-zA-Z0-9@_.\-]+$/ui',$str)) {
 $rr .= 'Хуевое мыло ';
 $n = 0;
}
$massiv = explode("@", $str);
$mail = $massiv[1];
if (!preg_match('/^[a-zA-Z0-9.\-]+$/ui',$mail)) {
 $rr .= 'Хуевое мыло ';
 $n = 0;
}
$massiv = explode(".", $mail);
$mail = $massiv[1];
if (!preg_match('/^[a-zA-Z]+$/ui',$mail)) {
 $rr .= 'Хуевое мыло ';
 $n = 0;
}

if ($n == 0) {echo '<span style="color:red">';}
echo htmlspecialchars($arFields["LOGIN"]." | ".$arFields["NAME"]." | ".$arFields["LAST_NAME"]." | ".$arFields["EMAIL"]." ".$rr);
if ($n == 0) {echo '</span>';}
echo "<br>";
$arFields["NAME"] = trim($arFields["NAME"]);
$arFields["LAST_NAME"] = trim($arFields["LAST_NAME"]);
$arFields["LOGIN"] = trim($arFields["LOGIN"]);
$arFields["EMAIL"] = trim($arFields["EMAIL"]);
/*
ID
LOGIN
NAME
LAST_NAME
EMAIL
PERSONAL_PHONE
*/
if ($n == 1) {
    $user = new CUser;
    $ID = $user->Add($arFields);
    if (intval($ID) > 0){
        $arTotal['Занесено'] += 1;
        $connection->queryExecute("UPDATE b_user SET PASSWORD='".$pwd."', CHECKWORD='".$cpwd."' WHERE ID='".$ID."'");
    }else{
        $arTotal['Ошибки'] = $arTotal['Ошибки'].'
        '.$user->LAST_ERROR;
    } 
var_dump($arTotal);  echo "<br>";
$arTotal = array();
}
    ob_flush();
    flush();
}