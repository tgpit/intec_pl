<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var CMain $APPLICATION
 */

$last = substr($_SERVER['REQUEST_URI'], -1);
$url = $_SERVER['REQUEST_URI'];
if ($last == '/') {
	$url = substr($_SERVER['REQUEST_URI'],0,-1);
};
echo $url."<br>";
$url = explode('?', $url);
echo $url."<br>";
$url = $url[0];
echo $url."<br>";
$last = substr($url, -1);
if ($last == '/') {
	$url = substr($url,0,-1);
};
echo $url."<br>";
$arr = explode( '/', $url );
$cod = $arr[count($arr)-1];
echo $cod."<br>";
if ($arr[1]=='catalog'){
	if (is_numeric($cod)) {
		$ur = "";
		for ($i = 1; $i < count($arr)-2; $i++) {
		    $ur .= "/$arr[$i]";
		};
		$arFilter = Array(
			"IBLOCK_ID"=> "58", 
			"ACTIVE"=>"Y", 
			"PROPERTY_ID_OLD" => $cod,
		);
		$res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter);
		while($ar_fields = $res->GetNext())
		{
			$addr = $ar_fields["DETAIL_PAGE_URL"];
			LocalRedirect($addr);
		}
	} else {
		$arFilter = Array(
			"IBLOCK_ID"=> "58", 
			"ACTIVE"=>"Y", 
			"CODE" => $cod,
		);
		$res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter);
		while($ar_fields = $res->GetNext())
		{
			$addr = $ar_fields["DETAIL_PAGE_URL"];
			LocalRedirect($addr);
		}
    }
}

include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus('404 Not Found');

@define('ERROR_404', 'Y');

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$APPLICATION->SetTitle('Страница не найдена');

?>
<div class="intec-page intec-page-404 intec-content">
    <div class="intec-content-wrapper">
        <div class="intec-grid intec-grid-wrap intec-grid-a-h-center intec-grid-a-v-center intec-grid-i-20">
            <div class="intec-grid-item-2 intec-grid-item-768-1">
                <div class="intec-page-part-picture intec-ui-picture">
                    <img src="<?= SITE_DIR ?>images/404.png">
                </div>
            </div>
            <div class="intec-grid-item-2 intec-grid-item-768-1">
                <div class="intec-grid intec-grid-a-h-center">
                    <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                        <div class="intec-page-part-content">
                            <div class="intec-page-part-title">
                                Ошибка 404
                            </div>
                            <div class="intec-page-part-description">
                                Страница не найдена
                            </div>
                            <div class="intec-page-part-tip">
                                Неправильно набран адрес или такой страницы не существует
                            </div>
                            <div class="intec-page-part-search">
                                <?php $APPLICATION->IncludeComponent(
                                    "bitrix:search.title",
                                    "input.1",
                                    array(
                                        "NUM_CATEGORIES" => "1",
                                        "TOP_COUNT" => "5",
                                        "ORDER" => "date",
                                        "USE_LANGUAGE_GUESS" => "N",
                                        "CHECK_DATES" => "N",
                                        "SHOW_OTHERS" => "Y",
                                        "INPUT_ID" => "page-title-search-input",
                                        "TIPS_USE" => "Y",
                                        "PAGE" => "/search/index.php",
                                        "CATEGORY_0_TITLE" => "",
                                        "CATEGORY_0" => array(
                                            0 => "no",
                                        ),
                                        "CATEGORY_OTHERS_TITLE" => ""
                                    ),
                                    false
                                ) ?>
                            </div>
                            <div class="intec-page-part-buttons intec-grid intec-grid-wrap intec-grid-i-5 intec-grid-a-h-center intec-grid-a-v-center">
                                <div class="intec-grid-item-auto">
                                    <a href="<?= SITE_DIR ?>" class="intec-page-part-button intec-ui intec-ui-control-button intec-ui-scheme-current intec-ui-size-2 intec-ui-mod-round-3">
                                        Перейти на главную
                                    </a>
                                </div>
                                <div class="intec-grid-item-auto">
                                    <a href="<?= SITE_DIR ?>catalog/" class="intec-page-part-button intec-ui intec-ui-control-button intec-ui-scheme-current intec-ui-size-2 intec-ui-mod-transparent intec-ui-mod-round-3">
                                        Вернуться в каталог
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br><br>
    </div>
</div>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php') ?>