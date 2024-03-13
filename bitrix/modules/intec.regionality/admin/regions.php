<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
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
use intec\regionality\models\region\Domain;
use intec\regionality\models\region\PriceType;
use intec\regionality\models\region\Site;
use intec\regionality\models\region\Store;
use intec\regionality\models\region\Value;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 * @global CUserTypeManager $USER_FIELD_MANAGER
 */

global $APPLICATION;
global $USER_FIELD_MANAGER;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('intec.regionality'))
    return;

include(Core::getAlias('@intec/regionality/module/admin/url.php'));

$request = Core::$app->request;
$site = $request->get('site');
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

$APPLICATION->SetTitle(Loc::getMessage('title'));

$list = 'regionality_regions';
$listSort = [];
$listSort['variable'] = $list.'_by';
$listSort['value'] = $request->get($listSort['variable'], 'sort');
$listOrder = [];
$listOrder['variable'] = $list.'_order';
$listOrder['value'] = $request->get($listOrder['variable'], 'asc');

$sort = new CAdminSorting(
    $list,
    $listSort['value'],
    $listSort['variable'],
    $listOrder['value'],
    $listOrder['variable']
);

$list = new CAdminList($list, $sort);
$regionProperties = Region::getProperties();

$filter = $list->InitFilter([
    'filterIdValue',
    'filterCodeValue',
    'filterActiveValue',
    'filterNameValue',
    'filterDomainValue',
    'filterSitesValue'
]);

$filter = new CAdminFilter(
    $list->table_id.'_filter',[
        Loc::getMessage('filter.fields.id'),
        Loc::getMessage('filter.fields.code'),
        Loc::getMessage('filter.fields.active'),
        Loc::getMessage('filter.fields.name'),
        Loc::getMessage('filter.fields.domain'),
        Loc::getMessage('filter.fields.sites')
    ]
);

if ($list->EditAction()) {
    /** @var array $FIELDS */

    if (!empty($FIELDS)) {
        $id = ArrayHelper::getKeys($FIELDS);
        /** @var ActiveRecords $regions */
        $regions = Region::find()
            ->with([
                'sites',
                'values'
            ])
            ->where(['id' => $id])
            ->indexBy('id')
            ->all();

        foreach ($FIELDS as $id => $data) {
            /** @var Region $region */
            $region = $regions->get($id);

            if (empty($region))
                continue;

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
            $region->load($data, '');

            if (isset($data['active']))
                $region->active = $region->active === 'Y';

            if ($region->save()) {
                if (isset($data['sites'])) {
                    if (!Type::isArray($data['sites']))
                        $data['sites'] = [];

                    foreach ($regionSites as $regionSite)
                        $regionSite->delete();

                    $sites->each(function ($index, $site) use (&$region, &$data) {
                        if (!ArrayHelper::isIn($site['ID'], $data['sites']))
                            return;

                        $regionSite = new Site();
                        $regionSite->regionId = $region->id;
                        $regionSite->siteId = $site['ID'];
                        $regionSite->save();
                    });
                }

                if ($pricesTypesUse && isset($data['pricesTypes'])) {
                    if (!Type::isArray($data['pricesTypes']))
                        $data['pricesTypes'] = [];

                    foreach ($regionPricesTypes as $regionPriceType)
                        $regionPriceType->delete();

                    $pricesTypes->each(function ($index, $priceType) use (&$region, &$data) {
                        if (!ArrayHelper::isIn($priceType['ID'], $data['pricesTypes']))
                            return;

                        $regionPriceType = new PriceType();
                        $regionPriceType->regionId = $region->id;
                        $regionPriceType->priceTypeId = $priceType['ID'];
                        $regionPriceType->save();
                    });
                }

                if ($storesUse && isset($data['stores'])) {
                    if (!Type::isArray($data['stores']))
                        $data['stores'] = [];

                    foreach ($regionStores as $regionStore)
                        $regionStore->delete();

                    $stores->each(function ($index, $store) use (&$region, &$data) {
                        if (!ArrayHelper::isIn($store['ID'], $data['stores']))
                            return;

                        $regionStore = new Store();
                        $regionStore->regionId = $region->id;
                        $regionStore->storeId = $store['ID'];
                        $regionStore->save();
                    });
                }

                foreach ($regionProperties as $regionProperty) {
                    if (!isset($data[$regionProperty['FIELD_NAME']]))
                        continue;

                    $regionProperty['VALUE'] = $data[$regionProperty['FIELD_NAME']];

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
            } else {
                $error = $region->getFirstErrors();
                $error = ArrayHelper::getFirstValue($error);
                $list->AddGroupError($error, $id);
            }
        }

        unset($error);
        unset($data);
        unset($id);
        unset($regionValue);
        unset($regionValues);
        unset($regionProperty);
    }
} else if ($id = $list->GroupAction()) {
    $action = $_REQUEST['action'];

    if (empty($id[0]) && count($id) === 1)
        $id = null;

    $regions = Region::find();

    if (!empty($id))
        $regions->where(['id' => $id]);

    /** @var Region[] $regions */
    $regions = $regions->all();

    foreach ($regions as $region) {
        if ($action === 'activate') {
            $region->active = true;
            $region->save();
        } else if ($action === 'deactivate') {
            $region->active = false;
            $region->save();
        } else if ($action === 'delete') {
            $region->delete();
        }
    }
}

unset($region);
unset($regions);
unset($id);

$regions = Region::find()
    ->with([
        'domains',
        'sites',
        'values'
    ]);

if (!empty($filterIdValue))
    $regions->andWhere(['=', 'id', $filterIdValue]);

if (!empty($filterCodeValue))
    $regions->andWhere(['like', 'code', $filterCodeValue]);

if (isset($filterActiveValue) && Type::isNumeric($filterActiveValue))
    $regions->andWhere(['=', 'active', $filterActiveValue > 0 ? 1 : 0]);

if (!empty($filterNameValue))
    $regions->andWhere(['like', 'name', $filterNameValue]);

if (!empty($filterDomainValue)) {
    $regions->joinWith('domains', false, 'RIGHT JOIN');
    $regions->andWhere(['like', '`'.Domain::tableName().'`.`value`', $filterDomainValue]);
}

if (!empty($filterSitesValue) && Type::isArray($filterSitesValue)) {
    $filterSitesValueTemporary = [];

    foreach ($filterSitesValue as $value)
        if (!empty($value))
            $filterSitesValueTemporary[] = $value;

    unset($value);

    $filterSitesValue = $filterSitesValueTemporary;

    if (!empty($filterSitesValue)) {
        $regions->joinWith('sites', false, 'RIGHT JOIN');
        $regions->andWhere(['in', '`' . Site::tableName() . '`.`siteId`', $filterSitesValue]);
    }

    unset($filterSitesValueTemporary);
}

$regions->distinct(true);

$navigation = new PageNavigation('nav');
$navigation->setRecordCount($regions->count());
$navigation->allowAllRecords(true);
$navigation->setPageSizes([
    10, 20, 50, 100, 200, 500
]);
$navigation->initFromUri();

$regions->offset($navigation->getOffset())
    ->limit($navigation->getPageSize())
    ->indexBy('id');

if (!empty($listSort['value']))
    $regions->orderBy([$listSort['value'] => $listOrder['value'] == 'asc' ? SORT_ASC : SORT_DESC]);

/** @var ActiveRecords $regions */
$regions = $regions->all();

$headers = [[
    'id' => 'id',
    'content' => Loc::getMessage('list.headers.id'),
    'sort' => 'id',
    'default' => true
], [
    'id' => 'code',
    'content' => Loc::getMessage('list.headers.code'),
    'sort' => 'code',
    'default' => true
], [
    'id' => 'active',
    'content' => Loc::getMessage('list.headers.active'),
    'sort' => 'active',
    'default' => true
], [
    'id' => 'name',
    'content' => Loc::getMessage('list.headers.name'),
    'sort' => 'name',
    'default' => true
], [
    'id' => 'description',
    'content' => Loc::getMessage('list.headers.description'),
    'sort' => 'description',
    'default' => false
], [
    'id' => 'domains',
    'content' => Loc::getMessage('list.headers.domains'),
    'default' => true
], [
    'id' => 'sites',
    'content' => Loc::getMessage('list.headers.sites'),
    'default' => true
]];

if ($pricesTypesUse) {
    $headers[] = [
        'id' => 'pricesTypes',
        'content' => Loc::getMessage('list.headers.pricesTypes'),
        'default' => true
    ];
}

if ($storesUse) {
    $headers[] = [
        'id' => 'stores',
        'content' => Loc::getMessage('list.headers.stores'),
        'default' => true
    ];
}

$headers[] = [
    'id' => 'sort',
    'content' => Loc::getMessage('list.headers.sort'),
    'sort' => 'sort',
    'default' => true
];

$USER_FIELD_MANAGER->AdminListAddHeaders(Region::ENTITY, $headers);
$list->AddHeaders($headers);

$list->AddAdminContextMenu([[
    'TEXT' => Loc::getMessage('list.actions.add'),
    'ICON' => 'btn_new',
    'LINK' => $arUrlTemplates['regions.add'],
    'TITLE' => Loc::getMessage('list.actions.add')
]]);

$list->AddGroupActionTable([
    'delete' => Loc::getMessage('list.actions.delete'),
    'activate' => Loc::getMessage('list.actions.activate'),
    'deactivate' => Loc::getMessage('list.actions.deactivate')
]);

$list->setNavigation($navigation, Loc::getMessage('list.navigation.title'));

foreach ($regions as $region) {
    /** @var Region $region */
    /** @var ActiveRecords $regionDomains */
    $regionDomains = $region
        ->getDomains(true)
        ->sortBy('sort', SORT_ASC);

    /** @var ActiveRecords $regionSites */
    $regionSites = $region->getSites(true);

    /** @var ActiveRecords $regionPricesTypes */
    $regionPricesTypes = $region->getPricesTypes(true);

    /** @var ActiveRecords $regionStores*/
    $regionStores = $region->getStores(true);

    /** @var ActiveRecords $regionValues */
    $regionValues = $region->getValues(true);
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

    $actions = [];
    $actions[] = [
        'ICON' => 'edit',
        'TEXT' => Loc::getMessage('list.rows.actions.edit'),
        'ACTION' => $list->ActionRedirect(StringHelper::replaceMacros($arUrlTemplates['regions.edit'], [
            'region' => $region->id
        ]))
    ];
    
    $actions[] = [
        'TEXT' => Loc::getMessage('list.rows.actions.domains'),
        'ACTION' => $list->ActionRedirect(StringHelper::replaceMacros($arUrlTemplates['regions.domains'], [
            'region' => $region->id
        ]))
    ];

    $actions[] = [
        'SEPARATOR' => 'Y'
    ];

    if ($region->active) {
        $actions[] = [
            'TEXT' => Loc::getMessage('list.rows.actions.deactivate'),
            'ACTION' => $list->ActionDoGroup($region->id, 'deactivate')
        ];
    } else {
        $actions[] = [
            'TEXT' => Loc::getMessage('list.rows.actions.activate'),
            'ACTION' => $list->ActionDoGroup($region->id, 'activate')
        ];
    }

    $actions[] = [
        'SEPARATOR' => 'Y'
    ];

    $actions[] = [
        'ICON' => 'delete',
        'TEXT' => Loc::getMessage('list.rows.actions.delete'),
        'ACTION' => 'if(confirm(\''.Loc::getMessage('list.rows.actions.delete.confirm').'\'))'.$list->ActionDoGroup(
            $region->id,
            'delete'
        )
    ];

    $row = $list->AddRow($region->id, [
        'id' => $region->id,
        'code' => $region->code,
        'active' => $region->active ? 'Y' : 'N',
        'name' => $region->name,
        'description' => $region->description,
        'sort' => $region->sort
    ]);

    $rowField = $regionDomains->asArray(function ($index, $domain) {
        /** @var Domain $domain */

        return [
            'value' => Html::a($domain->value, '//'.$domain->value, [
                    'target' => '_blank'
            ]).($domain->default ? ' ('.Loc::getMessage('list.rows.answers.default').')' : null)
        ];
    });

    if (!empty($rowField)) {
        $row->AddViewField('domains', implode('<br />', $rowField));
    } else {
        $row->AddField('domains', '('.Loc::getMessage('list.rows.answers.absent').')');
    }

    $rowField = $regionSites->asArray(function ($index, $site) use (&$sites) {
        /** @var Site $site */
        $site = $sites->get($site->siteId);

        if (empty($site))
            return ['skip' => true];

        return [
            'key' => $site['ID'],
            'value' => '['.$site['ID'].'] '.(!empty($site['SITE_NAME']) ? $site['SITE_NAME'] : $site['NAME'])
        ];
    });

    if (!empty($rowField)) {
        $row->AddViewField('sites', implode('<br />', $rowField));
    } else {
        $row->AddField('sites', '('.Loc::getMessage('list.rows.answers.unset').')');
    }

    if ($pricesTypesUse) {
        $rowField = $regionPricesTypes->asArray(function ($index, $priceType) use (&$pricesTypes) {
            /** @var PriceType $priceType */
            $priceType = $pricesTypes->get($priceType->priceTypeId);

            if (empty($priceType))
                return ['skip' => true];

            return [
                'key' => $priceType['ID'],
                'value' => '['.$priceType['NAME'].'] '.$priceType['NAME_LANG']
            ];
        });

        if (!empty($rowField)) {
            $row->AddViewField('pricesTypes', implode('<br />', $rowField));
        } else {
            $row->AddField('pricesTypes', '(' . Loc::getMessage('list.rows.answers.unset') . ')');
        }
    }

    if ($storesUse) {
        $rowField = $regionStores->asArray(function ($index, $store) use (&$stores) {
            /** @var Store $store */
            $store = $stores->get($store->storeId);

            if (empty($store))
                return ['skip' => true];

            return [
                'key' => $store['ID'],
                'value' => '['.$store['ID'].'] '.$store['TITLE']
            ];
        });

        if (!empty($rowField)) {
            $row->AddViewField('stores', implode('<br />', $rowField));
        } else {
            $row->AddField('stores', '(' . Loc::getMessage('list.rows.answers.unset') . ')');
        }
    }

    $row->AddInputField('code');
    $row->AddCheckField('active');
    $row->AddInputField('name');
    $row->AddEditField('sites', Html::hiddenInput(
            'FIELDS['.$region->id.'][sites]',
            null
        ).Html::dropDownList(
        'FIELDS['.$region->id.'][sites][]',
        $regionSites->asArray(function ($index, $site) {
            /** @var Site $site */

            return [
                'value' => $site->siteId
            ];
        }),
        ArrayHelper::merge([
            '' => '('.Loc::getMessage('list.rows.answers.unset').')'
        ],$sites->asArray(function ($index, $site) {
            return [
                'key' => $site['ID'],
                'value' => '['.$site['ID'].'] '.(!empty($site['SITE_NAME']) ? $site['SITE_NAME'] : $site['NAME'])
            ];
        })),
        [
            'multiple' => 'multiple'
        ]
    ));

    if ($pricesTypesUse) {
        $row->AddEditField('pricesTypes', Html::hiddenInput(
            'FIELDS['.$region->id.'][pricesTypes]',
            null
        ).Html::dropDownList(
            'FIELDS['.$region->id.'][pricesTypes][]',
            $regionPricesTypes->asArray(function ($index, $priceType) {
                /** @var PriceType $priceType */

                return [
                    'value' => $priceType->priceTypeId
                ];
            }),
            ArrayHelper::merge([
                '' => '('.Loc::getMessage('list.rows.answers.unset').')'
            ],$pricesTypes->asArray(function ($index, $priceType) {
                return [
                    'key' => $priceType['ID'],
                    'value' => '['.$priceType['NAME'].'] '.$priceType['NAME_LANG']
                ];
            })),
            [
                'multiple' => 'multiple'
            ]
        ));
    }

    if ($storesUse) {
        $row->AddEditField('stores', Html::hiddenInput(
            'FIELDS['.$region->id.'][stores]',
            null
        ).Html::dropDownList(
            'FIELDS['.$region->id.'][stores][]',
            $regionStores->asArray(function ($index, $store) {
                /** @var Store $store */

                return [
                    'value' => $store->storeId
                ];
            }),
            ArrayHelper::merge([
                '' => '('.Loc::getMessage('list.rows.answers.unset').')'
            ],$stores->asArray(function ($index, $store) {
                return [
                    'key' => $store['ID'],
                    'value' => '['.$store['ID'].'] '.$store['TITLE']
                ];
            })),
            [
                'multiple' => 'multiple'
            ]
        ));
    }

    $row->AddInputField('sort');

    $USER_FIELD_MANAGER->AddUserFields(Region::ENTITY, $regionFields, $row);
    $row->AddActions($actions);
}

$list->CheckListMode();

$url = new Url($request->getUrl());
$url->setHost(null);
$url->setFragment(null);
$url->getQuery()->removeAt('site');

?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php') ?>
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
<form name="find_form" method="get" action="<?= '/'.$request->getPathInfo() ?>">
    <?= Html::hiddenInput('site', $site) ?>
    <?php $filter->Begin(); ?>
        <tr>
            <td><?= Loc::getMessage('filter.fields.id') ?>:</td>
            <td><?= Html::textInput('filterIdValue', !empty($filterIdValue) ? $filterIdValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.code') ?>:</td>
            <td><?= Html::textInput('filterCodeValue', !empty($filterCodeValue) ? $filterCodeValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.active') ?>:</td>
            <td>
                <div style="margin-top: 6px;">
                    <div style="margin-bottom: 3px;">
                        <label>
                            <?= Html::radio('filterActiveValue', !isset($filterActiveValue) || !Type::isNumeric($filterActiveValue), [
                                'value' => null,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.active.any') ?></span>
                        </label>
                    </div>
                    <div style="margin-bottom: 3px;">
                        <label>
                            <?= Html::radio('filterActiveValue', isset($filterActiveValue) && Type::isNumeric($filterActiveValue) ? $filterActiveValue == 0 : false, [
                                'value' => 0,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.active.inactive') ?></span>
                        </label>
                    </div>
                    <div style="margin-bottom: 13px;">
                        <label>
                            <?= Html::radio('filterActiveValue', isset($filterActiveValue) && Type::isNumeric($filterActiveValue) ? $filterActiveValue > 0 : false, [
                                'value' => 1,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.active.active') ?></span>
                        </label>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.name') ?>:</td>
            <td><?= Html::textInput('filterNameValue', !empty($filterNameValue) ? $filterNameValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.domain') ?>:</td>
            <td><?= Html::textInput('filterDomainValue', !empty($filterDomainValue) ? $filterDomainValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.sites') ?>:</td>
            <td>
                <?= Html::hiddenInput(
                    'filterSitesValue',
                    null
                ).Html::dropDownList(
                    'filterSitesValue[]',
                    !empty($filterSitesValue) && Type::isArray($filterSitesValue) ? $filterSitesValue : null,
                    ArrayHelper::merge([
                        '' => '('.Loc::getMessage('filter.fields.sites.all').')'
                    ],$sites->asArray(function ($index, $site) {
                        return [
                            'key' => $site['ID'],
                            'value' => '['.$site['ID'].'] '.(!empty($site['SITE_NAME']) ? $site['SITE_NAME'] : $site['NAME'])
                        ];
                })), [
                    'multiple' => 'multiple'
                ]) ?>
            </td>
        </tr>
    <?php $filter->Buttons(['table_id' => $list->table_id, 'url' => '/'.$request->getPathInfo(), 'form' => 'find_form']) ?>
    <?php $filter->End() ?>
</form>
<?php $list->DisplayList() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>