<?
use Bitrix\Main\Page\Asset;
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/or/css/css.min.css", true); 
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/or/css/bootstrap.css", true); 
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/or/js/js.min.js"); 
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/or/js/bootstrap.js"); 
?>