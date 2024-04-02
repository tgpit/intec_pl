<?
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

    if (!\Bitrix\Main\Loader::includeModule('vkapi.market')){
        $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
    }
	
    \Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

	$oManager     = \VKapi\Market\Manager::getInstance();
    $oMessage = new \VKapi\Market\Message($oManager->getModuleId(), 'ALBUM_LIST_PAGE');
	$oAdmin = new \VKapi\Market\Admin($oManager->getModuleId());
	$oAlbumTable = new \VKapi\Market\Album\ItemTable();


    $oAdmin->setTableId('vkapi_market_album_list');

	$app          = \Bitrix\Main\Application::getInstance();
	$req          = $app->getContext()->getRequest();

	// проверка доступа
    $oManager->base()->checkLevelAccess();


	$oSort  = new CAdminSorting($oAdmin->getTableId(), "SORT", "ASC");
	$oAdminList = new CAdminList($oAdmin->getTableId(), $oSort);

	// передача по ссылке объекта для работы со списком
	$oAdmin->setAdminList($oAdminList);
	//языковые сообщения
	$oAdmin->setMessage($oMessage);


	// строка меню над списком --
	$oMenu    = new CAdminContextMenu(array(
        array(
            "TEXT"  => $oMessage->get('BTN_NEW'),
            "LINK"  => $oAdmin->getPageUrl('album_edit'),
            "TITLE" => $oMessage->get('BTN_NEW'),
            "ICON"  => "btn_new",
        ),
    ));



	//  активация, деактивания, удаление массове
	if ($oManager->base()->canActionRight('W') && $arID = $oAdminList->GroupAction()) {
		switch ($req->getPost('action_button')) {
			case "delete":
				foreach ($arID as $id) {
					$res = $oAlbumTable->delete($id);
				}
				break;
		}
	}

	$arSite = $oAdmin->getSiteList();

	// фильтрация
    $oAdmin->addFilterField('ID');
    $oAdmin->addFilterField('NAME');
    $oAdmin->addFilterField('VK_NAME');
    $oAdmin->checkFilter();

	// поля достпные для сортировки
	$oAdmin->setSortFields(array(
	    'ID', 'NAME', 'VK_NAME'
    ), 'ID');


	$dbResultList = $oAlbumTable->getList($oAdmin->getListQuery());

	$dbResultList = new CAdminResult($dbResultList, $oAdmin->getTableId());

	$dbResultList->NavStart();

	$oAdminList->NavText($dbResultList->GetNavPrint($oMessage->get( 'NAV_PAGE')));

	$oAdminList->AddHeaders(array(
		array(
			"id"      => 'ID',
			"content" => $oMessage->get('HEAD.ID'),
			"sort"    => 'ID',
			"default" => true
		),
        array(
            "id"      => 'NAME',
            "content" => $oMessage->get( 'HEAD.NAME'),
            "sort"    => 'NAME',
            "default" => true
        ),
        array(
            "id"      => 'VK_NAME',
            "content" => $oMessage->get( 'HEAD.VK_NAME'),
            "sort"    => 'VK_NAME',
            "default" => true
        ),
        array(
            "id"      => 'PICTURE',
            "content" => $oMessage->get( 'HEAD.PICTURE'),
            "sort"    => '',
            "default" => true
        ),

	));


	while ($arItem = $dbResultList->NavNext(false)) {


		$row = &$oAdminList->AddRow($arItem['ID'], $arItem);

		$row->AddField('NAME', $arItem['NAME']);
		$row->AddField('VK_NAME', $arItem['VK_NAME']);
		$row->AddField('ID', $arItem['ID']);

		$img  ='';
		if($arItem['PICTURE'])
        {
            $src = \CFile::ResizeImageGet($arItem['PICTURE'], array(
                'width' => 200,
                'height' => 200
            ));
            
            $img .= '<img src="'.$src['src'].'" style="max-width:70px;max-height:39px;" />';
        }
		
		$row->AddField('PICTURE', $img);


		$arActions   = Array();
        $arActions[] = array(
            "ICON"    => "edit",
            "TEXT"    => $oMessage->get('MENU_EDIT'),
            "ACTION"  => $oAdminList->ActionRedirect($oAdmin->getPageUrl('album_edit', array(
                'ID' =>  $arItem['ID']
            ))),
            "DEFAULT" => true
        );
        $arActions[] = array(
            "ICON"    => "copy",
            "TEXT"    => $oMessage->get('MENU_COPY'),
            "ACTION"  => $oAdminList->ActionRedirect($oAdmin->getPageUrl('album_edit', array(
                'COPY_ID' =>  $arItem['ID']
            ))),
            "DEFAULT" => true
        );

		$row->AddActions($arActions);
	}


	$oAdminList->AddFooter(
		array(
			array(
				"title" => $oMessage->get('LIST_SELECTED'),
				"value" => $dbResultList->SelectedRowsCount()
			),
			array(
				"counter" => true,
				"title"   => $oMessage->get( 'LIST_CHECKED'),
				"value"   => "0"
			),
		)
	);

	if ($oManager->base()->canActionRight('W')) {
		$oAdminList->AddGroupActionTable(
			array(
				"delete"     => $oMessage->get('LIST_DELETE'),
			)
		);
	}


	$oAdminList->CheckListMode();
	$APPLICATION->SetTitle($oMessage->get('TITLE'));

	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

    \VKapi\Market\Manager::getInstance()->showAdminPageCssJs();
	\VKapi\Market\Manager::getInstance()->showAdminPageMessages();


	$oAdmin->showFilter();

	$oMenu->Show();
	$oAdminList->DisplayList();

	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>