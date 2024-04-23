<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Каталог товаров");

$IBLOCK_ID    = 58;
				// ----------==================----------------
				$ELEMENT_ID = 41396;  // код элемента
				$PROPERTY_CODE = "g_cat";  // код свойства

				$vprop = 'd_masl'
//				if ($sNam == 'Экипировка') {$vprop = 'd_ekip';};
//				if ($sNam == 'Запчасти') {$vprop = 'd_zapch';};
//				if ($sNam == 'Техника') {$vprop = 'd_tech';};
		    	echo  "$sNam--->---> ".$ob['ID']." ".$ob['NAME']." ".$PROPERTY_CODE." ".$vprop;
				$PROPERTY_VALUE = $vprop;  // значение свойства
//				$r = CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, $IBLOCK_ID, array($PROPERTY_CODE => $PROPERTY_VALUE));
//				var_dump($r); echo "-=-<br>";
//				CIBlockElement::SetPropertyValues($ELEMENT_ID,$IBLOCK_ID, $PROPERTY_VALUE, $PROPERTY_CODE);
				// -----------================-----------------
//			}
//		}
//	}
//}

//$arFilter = array('IBLOCK_ID' => IntVal($IBLOCK_ID),
//				  'ACTIVE' => 'Y',
//				  'DEPTH_LEVEL' => "1",
//    			); 
//$rsSect = CIBlockSection::GetTreeList(
//     $arFilter, //фильтр (выше объявили)
//     false, 	//выводить количество элементов - нет
//	);
//
//while ($arSect = $rsSect->GetNext()) {
//    echo  "<b>".$arSect['ID']." ".$arSect['NAME']."<b><br>";
//	getnod($arSect['ID'],$arSect['NAME']);
//}

?>  