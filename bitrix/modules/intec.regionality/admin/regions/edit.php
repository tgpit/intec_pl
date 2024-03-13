<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\db\ActiveRecords;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\net\Url;
use intec\regionality\models\Region;
use intec\regionality\models\region\PriceType;
use intec\regionality\models\region\Site;
use intec\regionality\models\region\Store;
use intec\regionality\models\region\Value;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 */

global $APPLICATION;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('intec.regionality'))
    return;

include(Core::getAlias('@intec/regionality/module/admin/url.php'));

$request = Core::$app->request;
$site = $request->get('site');
$error = null;
$sites = Arrays::fromDBResult(CSite::GetList($by = 'order', $sort = 'asc'))->indexBy('ID');

$pricesTypesUse = Loader::includeModule('catalog') || Loader::includeModule('intec.startshop');
$pricesTypes = Arrays::from([]);

if ($pricesTypesUse) {
    if (Loader::includeModule('catalog')) {
        $pricesTypes = Arrays::fromDBResult(CCatalogGroup::GetList([
            'SORT' => 'ASC'
        ], [
            'ACTIVE' => 'Y'
        ]))->indexBy('ID');
    } else {
        $pricesTypesTemporary = Arrays::fromDBResult(CStartShopPrice::GetList([
            'SORT' => 'ASC'
        ], [
            'ACTIVE' => 'Y'
        ]))->asArray();

        foreach ($pricesTypesTemporary as $priceType) {
            $pricesTypes->set($priceType['ID'], [
                'ID' => $priceType['ID'],
                'NAME' => $priceType['CODE'],
                'NAME_LANG' => ArrayHelper::getValue($priceType, ['LANG', LANGUAGE_ID, 'NAME'])
            ]);
        }

        unset($priceType);
        unset($pricesTypesTemporary);
    }
}

$storesUse = Loader::includeModule('catalog');
$stores = Arrays::from([]);

if ($storesUse) {
    $stores = Arrays::fromDBResult(CCatalogStore::GetList([
        'SORT' => 'ASC'
    ], [
        'ACTIVE' => 'Y',
        'ISSUING_CENTER' => 'Y'
    ]))->indexBy('ID');
}

if (!empty($site))
    $site = $sites->get($site);

if (!empty($site)) {
    $site = $site['ID'];
} else {
    $site = null;
}

/** @var Region $region */
$region = $request->get('region');

if (!empty($region)) {
    $region = Region::findOne($region);

    if (empty($region))
        LocalRedirect($arUrlTemplates['regions']);
} else {
    $region = new Region();
    $region->loadDefaultValues();
}

$regionProperties = Region::getProperties();

/** @var ActiveRecords $regionSites */
$regionSites = $region->getSites(true)
    ->indexBy('siteId');

/** @var ActiveRecords $regionPricesTypes */
$regionPricesTypes = $region->getPricesTypes(true)
    ->indexBy('priceTypeId');

/** @var ActiveRecords $regionStores */
$regionStores = $region->getStores(true)
    ->indexBy('storeId');

/** @var ActiveRecords $regionValues */
$regionValues = $region->getValues(true);

if ($region->getIsNewRecord()) {
    $APPLICATION->SetTitle(Loc::getMessage('title.add'));
} else {
    $APPLICATION->SetTitle(Loc::getMessage('title.edit'));
}

if ($request->getIsPost()) {
    $post = $request->post();
    $return = $request->post('apply');
    $return = empty($return);
    $region->load($post);

    if ($region->save()) {
        if (isset($post['sites'])) {
            if (!Type::isArray($post['sites']))
                $post['sites'] = [];

            foreach ($regionSites as $regionSite)
                $regionSite->delete();

            $sites->each(function ($index, $site) use (&$region, &$post) {
                if (!ArrayHelper::isIn($site['ID'], $post['sites']))
                    return;

                $regionSite = new Site();
                $regionSite->regionId = $region->id;
                $regionSite->siteId = $site['ID'];
                $regionSite->save();
            });
        }

        if ($pricesTypesUse && isset($post['pricesTypes'])) {
            if (!Type::isArray($post['pricesTypes']))
                $post['pricesTypes'] = [];

            foreach ($regionPricesTypes as $regionPriceType)
                $regionPriceType->delete();

            $pricesTypes->each(function ($index, $priceType) use (&$region, &$post) {
                if (!ArrayHelper::isIn($priceType['ID'], $post['pricesTypes']))
                    return;

                $regionPriceType = new PriceType();
                $regionPriceType->regionId = $region->id;
                $regionPriceType->priceTypeId = $priceType['ID'];
                $regionPriceType->save();
            });
        }

        if ($storesUse && isset($post['stores'])) {
            if (!Type::isArray($post['stores']))
                $post['stores'] = [];

            foreach ($regionStores as $regionStore)
                $regionStore->delete();

            $stores->each(function ($index, $store) use (&$region, &$post) {
                if (!ArrayHelper::isIn($store['ID'], $post['stores']))
                    return;

                $regionStore = new Store();
                $regionStore->regionId = $region->id;
                $regionStore->storeId = $store['ID'];
                $regionStore->save();
            });
        }

        foreach ($regionProperties as $regionProperty) {
            if (!isset($post[$regionProperty['FIELD_NAME']]))
                continue;

            $regionProperty['VALUE'] = $post[$regionProperty['FIELD_NAME']];

            /** @var Value $regionValue */
            $regionValue = null;

            foreach ($regionValues as $regionValue) {
                if (
                    $regionValue->siteId === $site &&
                    $regionValue->propertyCode === $regionProperty['FIELD_CODE']
                ) break;

                $regionValue = null;
            }

            if (empty($regionValue)) {
                $regionValue = new Value();
                $regionValue->propertyCode = $regionProperty['FIELD_CODE'];
                $regionValue->regionId = $region->id;
                $regionValue->siteId = $site;
            }

            $regionValue->value = $regionProperty['VALUE'];

            if ($regionValue->getIsEmpty()) {
                if (!$regionValue->getIsNewRecord())
                    $regionValue->delete();
            } else {
                $regionValue->save();
            }
        }

        if ($return)
            LocalRedirect($arUrlTemplates['regions']);

        LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['regions.edit'], [
            'region' => $region->id
        ]).(!empty($site) ? '&site='.$site : null));
    } else {
        $error = $region->getFirstErrors();
        $error = ArrayHelper::getFirstValue($error);
    }
}

$regionFields = $regionValues->asArray(function ($index, $value) use (&$site) {
    /** @var Value $value */

    if ($value->siteId !== $site)
        return ['skip' => true];

    return [
        'key' => Region::PROPERTY_PREFIX_SYSTEM.$value->propertyCode,
        'value' => $value->value
    ];
});

foreach ($regionProperties as $regionProperty)
    if (!isset($regionFields[$regionProperty['FIELD_NAME']]))
        $regionFields[$regionProperty['FIELD_NAME']] = null;

$form = new CAdminForm('regionsEditForm', [[
    'DIV' => 'common',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.common'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.common'))
], [
    'DIV' => 'properties',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.properties'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.properties'))
]]);

$form->BeginPrologContent();
$form->EndPrologContent();
$form->BeginEpilogContent();
$form->EndEpilogContent();

$sections = include(__DIR__.'/sections.php');
$panel = include(__DIR__.'/panel.php');

$url = new Url($request->getUrl());
$url->setHost(null);
$url->setFragment(null);
$url->getQuery()->removeAt('site');

?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php') ?>
<?php if (!$region->getIsNewRecord()) { ?>
    <div style="margin-bottom: 10px;">
        <span><?= Loc::getMessage('sites.title') ?>:</span>
        <?= Html::dropDownList(null, $site, ArrayHelper::merge([
            '' => '('.Loc::getMessage('sites.all').')'
        ], $sites->asArray(function ($id, $site) {
            return [
                'key' => $site['ID'],
                'value' => '['.$site['ID'].'] '.$site['NAME']
            ];
        })), [
            'onchange' => '(function (control) {
                var url = '.JavaScript::toObject($url->build()).';
                var value = control[control.selectedIndex].value;
                            
                if (value != null && value.length > 0) {
                    if (url.indexOf(\'?\') >= 0) {
                        url += \'&\';
                    } else {
                        url += \'?\';
                    }
                    
                    window.location = url + \'site=\' + value;
                } else {
                    window.location = url;
                }
            })(this)'
        ]) ?>
    </div>
    <?php $sections($region, 'edit', ['margin-bottom' => '10px']) ?>
<?php } ?>
<?php $panel->Show() ?>
<?php if (!empty($error)) { ?>
    <?php CAdminMessage::ShowMessage($error) ?>
<?php } ?>
<?php $form->Begin([
    'FORM_ACTION' => $request->getUrl()
]) ?>
<?php $form->BeginNextFormTab() ?>
    <?php if (!$region->getIsNewRecord()) { ?>
        <?php $form->BeginCustomField('id', $region->getAttributeLabel('id').':', true) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td><?= $region->id ?></td>
            </tr>
        <?php $form->EndCustomField('id') ?>
    <?php } ?>
    <?php $form->BeginCustomField('code', $region->getAttributeLabel('code').':', true) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($region->formName().'[code]', $region->code) ?></td>
        </tr>
    <?php $form->EndCustomField('code') ?>
    <?php $form->BeginCustomField('active', $region->getAttributeLabel('active').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput($region->formName().'[active]', 0) ?>
                <?= Html::checkbox($region->formName().'[active]', $region->active) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('active') ?>
    <?php $form->BeginCustomField('name', $region->getAttributeLabel('name').':', true) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($region->formName().'[name]', $region->name) ?></td>
        </tr>
    <?php $form->EndCustomField('name') ?>
    <?php $form->BeginCustomField('description', $region->getAttributeLabel('description').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textarea($region->formName().'[description]', $region->description, [
                'style' => [
                    'min-width' => '100%',
                    'min-height' => '150px',
                    'resize' => 'vertical'
                ]
            ]) ?></td>
        </tr>
    <?php $form->EndCustomField('description') ?>
    <?php $form->BeginCustomField('sites', Loc::getMessage('fields.sites.title').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput('sites', null) ?>
                <?= Html::dropDownList(
                    'sites[]',
                    $regionSites->asArray(function ($index, $site) {
                        /** @var Site $site */

                        return [
                            'value' => $site->siteId
                        ];
                    }),
                    ArrayHelper::merge([
                        '' => '('.Loc::getMessage('answers.unset').')'
                    ], $sites->asArray(function ($index, $site) {
                        return [
                            'key' => $site['ID'],
                            'value' => '['.$site['ID'].'] '.(!empty($site['SITE_NAME']) ? $site['SITE_NAME'] : $site['NAME'])
                        ];
                    })),
                    [
                        'multiple' => 'multiple'
                    ]
                ) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('sites') ?>
    <?php if ($pricesTypesUse) { ?>
        <?php $form->BeginCustomField('pricesTypes', Loc::getMessage('fields.pricesTypes.title').':', false) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td>
                    <?= Html::hiddenInput('pricesTypes', null) ?>
                    <?= Html::dropDownList(
                        'pricesTypes[]',
                        $regionPricesTypes->asArray(function ($index, $priceType) {
                            /** @var PriceType $priceType */

                            return [
                                'value' => $priceType->priceTypeId
                            ];
                        }),
                        ArrayHelper::merge([
                            '' => '('.Loc::getMessage('answers.unset').')'
                        ], $pricesTypes->asArray(function ($index, $priceType) {
                            return [
                                'key' => $priceType['ID'],
                                'value' => '['.$priceType['NAME'].'] '.$priceType['NAME_LANG']
                            ];
                        })),
                        [
                            'multiple' => 'multiple'
                        ]
                    ) ?>
                </td>
            </tr>
        <?php $form->EndCustomField('pricesTypes') ?>
    <?php } ?>
    <?php if ($storesUse) { ?>
        <?php $form->BeginCustomField('stores', Loc::getMessage('fields.stores.title').':', false) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td>
                    <?= Html::hiddenInput('stores', null) ?>
                    <?= Html::dropDownList(
                        'stores[]',
                        $regionStores->asArray(function ($index, $store) {
                            /** @var Store $store */

                            return [
                                'value' => $store->storeId
                            ];
                        }),
                        ArrayHelper::merge([
                            '' => '('.Loc::getMessage('answers.unset').')'
                        ], $stores->asArray(function ($index, $store) {
                            return [
                                'key' => $store['ID'],
                                'value' => '['.$store['ID'].'] '.$store['TITLE']
                            ];
                        })),
                        [
                            'multiple' => 'multiple'
                        ]
                    ) ?>
                </td>
            </tr>
        <?php $form->EndCustomField('stores') ?>
    <?php } ?>
    <?php $form->BeginCustomField('sort', $region->getAttributeLabel('sort').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($region->formName().'[sort]', $region->sort) ?></td>
        </tr>
    <?php $form->EndCustomField('sort') ?>
<?php $form->BeginNextFormTab() ?>
    <?php if (!$region->getIsNewRecord()) { ?>
        <?php $form->ShowUserFieldsWithReadyData(Region::ENTITY, $regionFields, false) ?>
    <?php } ?>
<?php $form->Buttons([
    'disabled' => false,
    'btnSaveAndAdd' => false,
    'btnApply' => true,
    'btnCancel' => true,
    'back_url' => $arUrlTemplates['regions']
]) ?>
<?php $form->Show() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>