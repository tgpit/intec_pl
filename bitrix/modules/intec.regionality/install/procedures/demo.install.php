<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\db\ActiveRecords;
use intec\regionality\models\Region;
use intec\regionality\models\region\PriceType as RegionPriceType;
use intec\regionality\models\region\Store as RegionStore;
use intec\regionality\models\region\Site as RegionSite;
use intec\regionality\models\SiteSettings;

Loc::loadMessages(__FILE__);

$sites = Arrays::fromDBResult(CSite::GetList($by = 'sort', $order = 'asc'));
$regions = Region::find();

if ($regions->count() == 0) {
    $regions = new ActiveRecords();
    $regionDefault = null;

    $region = new Region();
    $region->loadDefaultValues();
    $region->code = 'Chelyabinsk';
    $region->name = Loc::getMessage('intec.regionality.install.procedures.demo.install.regions.Chelyabinsk');
    $region->sort = 100;

    if ($region->save()) {
        $regions->add($region);
        $regionDefault = $region;
    }

    $region = new Region();
    $region->loadDefaultValues();
    $region->code = 'Yekaterinburg';
    $region->name = Loc::getMessage('intec.regionality.install.procedures.demo.install.regions.Yekaterinburg');
    $region->sort = 200;

    if ($region->save())
        $regions->add($region);

    $region = new Region();
    $region->loadDefaultValues();
    $region->code = 'Moscow';
    $region->name = Loc::getMessage('intec.regionality.install.procedures.demo.install.regions.Moscow');
    $region->sort = 300;

    if ($region->save())
        $regions->add($region);

    unset($region);

    if (Loader::includeModule('catalog')) {
        $pricesTypes = Arrays::fromDBResult(CCatalogGroup::GetList([
            'SORT' => 'ASC'
        ], [
            'ACTIVE' => 'Y',
            'BASE' => 'Y'
        ]));

        $stores = Arrays::fromDBResult(CCatalogStore::GetList([
            'SORT' => 'ASC'
        ], [
            'ACTIVE' => 'Y',
            'ISSUING_CENTER' => 'Y'
        ]));

        if (!$pricesTypes->isEmpty())
            foreach ($regions as $region) {
                foreach ($pricesTypes as $priceType) {
                    $regionPriceType = new RegionPriceType();
                    $regionPriceType->regionId = $region->id;
                    $regionPriceType->priceTypeId = $priceType['ID'];
                    $regionPriceType->save();
                }
            }

        if (!$stores->isEmpty())
            foreach ($regions as $region) {
                foreach ($stores as $store) {
                    $regionStore = new RegionStore();
                    $regionStore->regionId = $region->id;
                    $regionStore->storeId = $store['ID'];
                    $regionStore->save();
                }
            }
    }

    foreach ($regions as $region) {
        foreach ($sites as $site) {
            $regionSite = new RegionSite();
            $regionSite->regionId = $region->id;
            $regionSite->siteId = $site['ID'];
            $regionSite->save();
        }
    }

    foreach ($sites as $site) {
        $siteSettings = SiteSettings::get($site['ID']);

        if (!empty($regionDefault))
            $siteSettings->regionId = $regionDefault->id;

        $siteSettings->save();
    }
}