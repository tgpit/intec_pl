<?
global $arTheme, $arRegion;
$arRegions = CNextRegionality::getRegions();
if ($arRegion)
    $bPhone = ($arRegion['PHONES'] ? true : false);
else
    $bPhone = ((int)$arTheme['HEADER_PHONES'] ? true : false);
$logoClass = ($arTheme['COLORED_LOGO']['VALUE'] !== 'Y' ? '' : ' colored');
?>
<div class="maxwidth-theme">
    <div class="logo-row v2 row margin0 menu-row">
        <div class="inner-table-block nopadding logo-block">
            <div class="logo<?= $logoClass ?>">
                <?= CNext::ShowLogo(); ?>
            </div>
        </div>
        <div class="inner-table-block menu-block">
            <div class="navs table-menu js-nav">
                <nav class="mega-menu sliced">
                    <? $APPLICATION->IncludeComponent("bitrix:main.include", ".default",
                        array(
                            "COMPONENT_TEMPLATE" => ".default",
                            "PATH" => SITE_DIR . "include/menu/menu.top_catalog_wide.php",
                            "AREA_FILE_SHOW" => "file",
                            "AREA_FILE_SUFFIX" => "",
                            "AREA_FILE_RECURSIVE" => "Y",
                            "EDIT_TEMPLATE" => "include_area.php"
                        ),
                        false, array("HIDE_ICONS" => "Y")
                    ); ?>
                </nav>
            </div>
        </div>
        <div class="pull-right">
            <? if ($arTheme['ORDER_BASKET_VIEW']['VALUE'] == 'NORMAL'): ?>
                <div class="top-block-item inner-table-block nopadding hide1562">
                    <div class="phone-block">
                        <? if ($bPhone): ?>
                            <div class="inline-block">
                                <? CNext::ShowHeaderPhones(); ?>
                            </div>
                        <? endif ?>
                    </div>
                </div>
            <? endif; ?>
            <div class="inner-table-block nopadding small-block">
                <div class="wrap_icon wrap_cabinet">
                    <?= CNext::ShowCabinetLink(true, false, 'big'); ?>
                </div>
            </div>
            <?= CNext::ShowBasketWithCompareLink('inner-table-block nopadding', 'big'); ?>
            <div class="inner-table-block small-block nopadding inline-search-show" data-type_search="fixed">
                <div class="search-block top-btn"><i class="svg svg-search lg"></i></div>
            </div>
        </div>
    </div>
</div>