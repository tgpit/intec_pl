<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}


class CatalogElements extends CBitrixComponent
{
    public function executeComponent()
    {
        $elements = \Ninja\Project\Catalog\CatalogElements::getList(['SECTION_CODE' => $this->arParams['SECTION_CODE']]);
        $this->arResult['city'] = \Ninja\Project\Regionality\Cities::getCityByHost();
        $this->arResult['list'] = $elements['list'];
        $this->includeComponentTemplate();
    }
}
