<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;
$aMenuLinksExt = array();

if(CModule::IncludeModule('iblock'))
{

    $res = CIBlock::GetList(
        Array(),
        Array(
            'TYPE'=>'aspro_next_content',
            'SITE_ID'=> 's1',
            'ACTIVE'=>'Y',
            "CNT_ACTIVE"=>"Y",
            "=CODE"=>'aspro_next_tablicu_razmerov'
        ), true
    );
    while($ar_res = $res->Fetch())
    {
        $iblock_id = $ar_res['ID'];
    }

    $arFilter = array(
            "TYPE" => "aspro_next_content",
            "SITE_ID" => "s1",
            "ID" => $iblock_id
        );


        $dbIBlock = CIBlock::GetList(array('SORT' => 'ASC', 'ID' => 'ASC'), $arFilter);
        $dbIBlock = new CIBlockResult($dbIBlock);

        if ($arIBlock = $dbIBlock->GetNext())
        {
            if(defined("BX_COMP_MANAGED_CACHE"))
                $GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arIBlock["ID"]);

            if($arIBlock["ACTIVE"] == "Y")
            {
                $aMenuLinksExt = $APPLICATION->IncludeComponent("bitrix:menu.sections", "", array(
                    "IS_SEF" => "Y",
                    "SEF_BASE_URL" => "",
                    "SECTION_PAGE_URL" => $arIBlock['SECTION_PAGE_URL'],
                    "DETAIL_PAGE_URL" => $arIBlock['DETAIL_PAGE_URL'],
                    "IBLOCK_TYPE" => $arIBlock['IBLOCK_TYPE_ID'],
                    "IBLOCK_ID" => $iblock_id,
                    "DEPTH_LEVEL" => "3",
                    "CACHE_TYPE" => "N",
                ), false, Array('HIDE_ICONS' => 'Y'));
            }
        }

        if(defined("BX_COMP_MANAGED_CACHE"))
            $GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arIBlock["ID"]);

}

$aMenuLinks = array_merge($aMenuLinksExt, $aMenuLinks);

?>
