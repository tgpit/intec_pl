<?php

use Ninja\Helper\SeoBot;
use Ninja\Project\Regionality\Regionality;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var object $APPLICATION */

global $arTheme, $arRegion;

/**
 * Регионы
 */
$arRegions = Regionality::getRegions();

/**
 *
 */
$bPhone = $arRegion ? (bool)$arRegion['PHONES'] : (bool)((int)$arTheme['HEADER_PHONES']);

/**
 * Определяет поискового бота по user agent
 */
$isBot = SeoBot::check();
?>
<div class="top-block top-block-v1">
  <div class="maxwidth-theme">
    <div class="row">
      <div class="col-md-6">
          <?php $APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
              "COMPONENT_TEMPLATE" => ".default",
              "PATH" => SITE_DIR."include/menu/menu.topest.php",
              "AREA_FILE_SHOW" => "file",
              "AREA_FILE_SUFFIX" => "",
              "AREA_FILE_RECURSIVE" => "Y",
              "EDIT_TEMPLATE" => "include_area.php"
          ), false);?>
      </div>

      <div class="top-block-item pull-right show-fixed top-ctrl">
        <div class="personal_wrap">
          <div class="personal top login twosmallfont">
              <?=CNext::ShowCabinetLink(true, true);?>
          </div>
        </div>
      </div>
        <?php if ($arTheme['ORDER_BASKET_VIEW']['VALUE'] === 'NORMAL'): ?>
            <?php $APPLICATION->IncludeComponent('ninja:layout-contacts', 'header', [], false); ?>
        <?php endif ?>
    </div>
  </div>
</div>
<div class="header-v3 header-wrapper">
  <div class="logo_and_menu-row">
    <div class="logo-row" style="background:#d4d4d4">
      <div class="maxwidth-theme" style="background:#d4d4d4">
        <div class="row">
          <div class="logo-block col-md-2 col-sm-3">
            <div class="logo">
                <?=CNext::ShowLogo()?>
            </div>
          </div>
            <?php if($arRegions): ?>
              <div class="inline-block pull-left">
                <div class="top-description">
                    <?php $APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
                        "COMPONENT_TEMPLATE" => ".default",
                        "PATH" => SITE_DIR."include/top_page/regionality.list.php",
                        "AREA_FILE_SHOW" => "file",
                        "AREA_FILE_SUFFIX" => "",
                        "AREA_FILE_RECURSIVE" => "Y",
                        "EDIT_TEMPLATE" => "include_area.php"
                    ), false);?>
                </div>
              </div>
            <?php endif ?>
          <div class="pull-left search_wrap wide_search">
            <div class="search-block inner-table-block">
                <?php $APPLICATION->IncludeComponent("bitrix:main.include", "", Array(
                    "AREA_FILE_SHOW" => "file",
                    "PATH" => SITE_DIR."include/top_page/search.title.catalog.php",
                    "EDIT_TEMPLATE" => "include_area.php"
                )); ?>
            </div>
          </div>
            <?php if($arTheme['ORDER_BASKET_VIEW']['VALUE'] !== 'NORMAL'): ?>
              <div class="pull-right block-link">
                <div class="phone-block with_btn">
                    <?php if($bPhone): ?>
                      <div class="inner-table-block">
                          <?php CNext::ShowHeaderPhones(); ?>
                        <div class="schedule">
                            <?php $APPLICATION->IncludeFile(SITE_DIR."include/header-schedule.php", array(), array("MODE" => "html","NAME" => GetMessage('HEADER_SCHEDULE'),"TEMPLATE" => "include_area.php")); ?>
                        </div>
                      </div>
                    <?php endif ?>
                    <?php if($arTheme['SHOW_CALLBACK']['VALUE'] === 'Y'): ?>
                      <div class="inner-table-block">
                        <span class="callback-block animate-load twosmallfont colored  white btn-default btn" data-event="jqm" data-param-form_id="CALLBACK" data-name="callback"><?=GetMessage("CALLBACK")?></span>
                      </div>
                    <?php endif ?>
                </div>
              </div>
            <?php endif ?>
          <div class="pull-right block-link">
              <?=CNext::ShowBasketWithCompareLink('with_price', 'big', true, 'wrap_icon inner-table-block baskets big-padding')?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="menu-row middle-block bg<?=strtolower($arTheme["MENU_COLOR"]["VALUE"]);?>">
    <div class="maxwidth-theme">
      <div class="row">
        <div class="col-md-12">
          <div class="menu-only">
            <nav class="mega-menu sliced">
                <?php $APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
                    "COMPONENT_TEMPLATE" => ".default",
                    "PATH" => SITE_DIR."include/menu/menu.top_catalog_wide.php",
                    "AREA_FILE_SHOW" => "file",
                    "AREA_FILE_SUFFIX" => "",
                    "AREA_FILE_RECURSIVE" => "Y",
                    "EDIT_TEMPLATE" => "include_area.php"
                ), false, array("HIDE_ICONS" => "Y"));?>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="line-row visible-xs"></div>
</div>
