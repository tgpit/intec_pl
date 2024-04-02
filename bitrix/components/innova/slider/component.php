<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	CModule::IncludeModule("iblock");

	$componentPath = $this->GetPath();
	
	
	CJSCore::RegisterExt('INNOVA_SLIDER', array(
			'js' => $componentPath.'/lib/min/tiny-slider.js',
			'css' => $componentPath."/lib/tiny-slider.css",
			'rel' => array()
	)); 
	CUtil::InitJSCore(array('INNOVA_SLIDER'));
	
	CJSCore::RegisterExt('INNOVA_SLIDER_RESIZESENSOR', array(
			'js' => array($componentPath.'/lib/ElementQueries.js', $componentPath.'/lib/ResizeSensor.js'),
			'rel' => array()
	)); 
	CUtil::InitJSCore(array('INNOVA_SLIDER_RESIZESENSOR'));
	
	if(!empty($arParams["IBLOCK_ID"])) {
		
		$arResult['ITEMS'] = array();
		
		$arFilter = Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
		$res = CIBlockElement::GetList(array("SORT"=>"ASC"), $arFilter);
		while($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();
			$arProps = $ob->GetProperties();
			$arResult['ITEMS'][$arFields['ID']] = $arFields;
			$arResult['ITEMS'][$arFields['ID']]['PROPERTIES'] = $arProps;
		}
	}
	
	$this->IncludeComponentTemplate();
?>