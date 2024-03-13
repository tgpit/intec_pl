<?php

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use intec\core\helpers\Type;
use intec\regionality\models\Region;

class IntecRegionalityRegionsSelectComponent extends CBitrixComponent
{
    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams)
    {
        if (!Type::isArray($arParams))
            $arParams = [];

        return $arParams;
    }

    /**
     * @inheritdoc
     */
    public function executeComponent()
    {
        if (
            !Loader::includeModule('intec.core') &&
            !Loader::includeModule('intec.regionality')
        ) return;

        $oContext = Context::getCurrent();
        $arParams = $this->arParams;
        $arResult = [];

        $arResult['ACTION'] = $this->getPath().'/ajax.php';
        $arResult['SELECTED'] = Region::isCurrentSet();
        $arResult['REGION'] = null;
        $arResult['REGIONS'] = Region::find()
            ->where([
                'active' => 1
            ])
            ->forSites($oContext->getSite())
            ->orderBy(['sort' => SORT_ASC])
            ->all(null, false);

        $oRegionCurrent = Region::getCurrent();

        /** @var Region $oRegion */
        foreach ($arResult['REGIONS'] as $oRegion)
            if ($oRegion->id == $oRegionCurrent->id) {
                $arResult['REGION'] = $oRegion;
                break;
            }

        $this->arResult = $arResult;

        unset($arParams);
        unset($arResult);

        $this->includeComponentTemplate();
    }
}