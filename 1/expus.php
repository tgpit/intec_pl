<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

//Получаем список пользователей
$rsUsers = CUser::GetList($by="", $order="",[],array("SELECT"=>array("UF_*")));
while ($user = $rsUsers->Fetch()){

    $arUsers[$user['ID']] = $user;
	$trsUser = CUser::GetByID($user['ID'])->Fetch();

    if($user['PERSONAL_PHOTO']){
        $arFileTmp = CFile::ResizeImageGet(
            $user['PERSONAL_PHOTO'],
            array("width" => 1000, "height" => 1000),
            BX_RESIZE_IMAGE_PROPORTIONAL,
            true
        );

        $arUsers[$user['ID']]['PERSONAL_PHOTO'] = 'http://' . $_SERVER['SERVER_NAME'] . $arFileTmp["src"];
    }
	$arUsers[$user['ID']]['PASSWORD'] = $trsUser['PASSWORD'];
	$arUsers[$user['ID']]['CHECKWORD'] = $trsUser['CHECKWORD'];
//$r$rsUser->Fetch();
//var_dump($trsUser);//  $rsUser['CHECKWORD']";
    $userGroups = CUser::GetUserGroup($user['ID']);

    $arUsers[$user['ID']]['A']['GROUPS'] = $userGroups;
}
$fd = fopen("muse.json", 'w');
$jsdat = json_encode($arUsers);
echo $jsdat;
fwrite($fd, $jsdat);
fclose($fd);

die();

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_after.php');